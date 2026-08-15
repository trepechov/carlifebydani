#!/usr/bin/env python3
"""Tests for keyword_tracking.py. Stdlib unittest, no test runner wired up
in this repo yet — run directly:

    python3 -m unittest tools.test_keyword_tracking -v
"""

import io
import os
import sys
import tempfile
import unittest
from contextlib import redirect_stderr, redirect_stdout
from datetime import date, timedelta

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import keyword_tracking as kt  # noqa: E402


def d(offset_days):
    return (date(2026, 1, 1) + timedelta(days=offset_days)).isoformat()


class KeywordTrackingTest(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.NamedTemporaryFile(suffix=".csv", delete=False)
        self.tmp.close()
        os.unlink(self.tmp.name)  # start missing, like a fresh repo
        self._orig_store = kt.STORE
        kt.STORE = self.tmp.name

    def tearDown(self):
        kt.STORE = self._orig_store
        if os.path.exists(self.tmp.name):
            os.unlink(self.tmp.name)

    # -------------------------------------------------------------- happy path

    def test_missing_store_bootstraps_without_error(self):
        self.assertFalse(os.path.exists(kt.STORE))
        rc = run_bootstrap("рено 5\trenault\n", on_date=d(0))
        self.assertEqual(rc, 0)
        self.assertTrue(os.path.exists(kt.STORE))
        roster = kt.current_roster(kt.load_rows())
        self.assertEqual(len(roster), 1)
        self.assertEqual(roster[0]["trend"], "new")
        self.assertEqual(roster[0]["status"], "tracking")
        self.assertEqual(roster[0]["months_tracked"], "0")

    def test_first_real_row_is_new(self):
        run_bootstrap("рено 5\trenault\n", on_date=d(0))
        run_append("рено 5\trenault\tgsc\t9.2\t1198\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "рено 5")
        self.assertEqual(row["trend"], "new")
        self.assertEqual(row["months_tracked"], "1")
        self.assertEqual(row["status"], "tracking")

    def test_position_improved_by_full_point_is_rising(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t7.5\t500\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "rising")

    def test_position_worsened_by_full_point_is_falling(self):
        run_append("keyword a\tcat\tgsc\t4.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t6.0\t500\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "falling")

    def test_three_flat_reviews_spanning_75_days_is_candidate(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t9.2\t500\n", on_date=d(40))
        _, stderr = run_append_capture("keyword a\tcat\tgsc\t8.9\t500\n", on_date=d(80))
        self.assertIn("CANDIDATES=keyword a", stderr)
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "candidate-for-swap")

    # ---------------------------------------------------------------- edges

    def test_low_impressions_is_no_footprint_not_falling(self):
        run_append("keyword a\tcat\tgsc\t4.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t\t12\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "no-footprint")

    def test_first_row_low_impressions_is_new_not_no_footprint(self):
        # A keyword's very first real reading has nothing to compare
        # against yet — that's true regardless of how weak this run's
        # impressions are, so it must read "new", not "no-footprint".
        run_append("keyword a\tcat\tgsc\t9.0\t8\n", on_date=d(0))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "new")

    def test_fewer_than_three_reviews_stays_tracking_even_if_flat(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t9.1\t500\n", on_date=d(80))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "tracking")

    def test_flat_streak_under_day_floor_does_not_flag(self):
        # Same three flat readings as the candidate test, but compressed
        # into a 20-day span instead of 75+ — an irregular/rapid cadence
        # should not trigger a swap suggestion.
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t9.2\t500\n", on_date=d(10))
        run_append("keyword a\tcat\tgsc\t8.9\t500\n", on_date=d(20))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "tracking")

    def test_signal_source_switch_resets_trend_to_new(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tsemrush_manual\t9.1\t500\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "new")

    def test_signal_source_switch_breaks_a_flat_streak(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tsemrush_manual\t9.1\t500\n", on_date=d(40))
        run_append("keyword a\tcat\tsemrush_manual\t9.0\t500\n", on_date=d(120))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        # Only two consecutive same-source (semrush_manual) flat rows exist
        # after the source switch reset the streak — not yet a candidate.
        self.assertEqual(row["status"], "tracking")

    def test_category_drift_warns_but_keeps_new_value(self):
        run_append("keyword a\tev\tgsc\t9.0\t500\n", on_date=d(0))
        _, stderr = run_append_capture("keyword a\tcharging\tgsc\t9.1\t500\n", on_date=d(30))
        self.assertIn("category drift", stderr)
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["category"], "charging")

    def test_blank_category_carries_forward_prior_value(self):
        run_append("keyword a\tev\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\t\tgsc\t9.1\t500\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["category"], "ev")

    def test_flat_threshold_exact_boundary_is_rising_not_flat(self):
        # abs(delta) < FLAT_THRESHOLD is a strict "<" — a delta of exactly
        # 1.0 must NOT read as flat.
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t8.0\t500\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "rising")

    def test_already_tracked_keyword_is_skipped_on_bootstrap(self):
        run_bootstrap("keyword a\tev\n", on_date=d(0))
        _, stderr = run_bootstrap_capture("keyword a\tev\n", on_date=d(1))
        self.assertIn("already tracked", stderr)
        self.assertEqual(len(kt.current_roster(kt.load_rows())), 1)

    def test_malformed_position_falls_back_to_new_and_warns(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        _, stderr = run_append_capture("keyword a\tcat\tgsc\tn/a\t500\n", on_date=d(30))
        self.assertIn("non-numeric", stderr)
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "new")
        self.assertEqual(row["position"], "n/a")  # still recorded verbatim

    def test_malformed_review_date_is_stale_returns_false(self):
        bad_row = {"review_date": "not-a-date"}
        self.assertFalse(kt.is_stale([bad_row, bad_row], {"review_date": d(0), "trend": "flat"}))

    def test_duplicate_append_same_day_is_skipped_and_warns(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        _, stderr = run_append_capture("keyword a\tcat\tgsc\t8.0\t500\n", on_date=d(0))
        self.assertIn("already has a row for", stderr)
        rows = kt.rows_for(kt.load_rows(), "keyword a")
        self.assertEqual(len(rows), 1)
        self.assertEqual(rows[0]["position"], "9.0")  # the duplicate did not overwrite it

    def test_nfc_and_nfd_keyword_normalize_to_same_identity(self):
        import unicodedata
        # Cyrillic й (U+0439) has a real canonical decomposition — и (U+0438)
        # + combining breve (U+0306) — unlike most Cyrillic letters, which
        # have no precomposed/decomposed pair at all. Use it to force a
        # genuine byte-level difference between the two encodings of "one
        # visual character" pasted in from different sources.
        composed = "й"
        decomposed = unicodedata.normalize("NFD", composed)
        self.assertNotEqual(composed, decomposed)  # sanity: the raw bytes really do differ
        self.assertEqual(kt.norm("keyword " + composed), kt.norm("keyword " + decomposed))

    def test_malformed_tsv_line_missing_tabs_is_skipped_and_warns(self):
        # A line with no real tabs at all (e.g. an LLM re-serializing the
        # heredoc as spaces) must not silently become a garbage row.
        _, stderr = run_append_capture("keyword a cat gsc 9.0 500\n", on_date=d(0))
        self.assertIn("malformed line", stderr)
        self.assertEqual(kt.current_roster(kt.load_rows()), [])

    def test_deep_history_real_jump_two_reviews_ago_blocks_candidate(self):
        # r1=new, r2=flat, r3=a real jump (rising), r4=flat, r5=flat.
        # The most recent two (r4, r5) are flat, but r3 — two reviews back —
        # was a genuine rank change, not noise. The staleness window must
        # still catch that even though it's no longer the very first row.
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))    # new
        run_append("keyword a\tcat\tgsc\t9.1\t500\n", on_date=d(30))   # flat
        run_append("keyword a\tcat\tgsc\t5.0\t500\n", on_date=d(60))   # rising (real jump)
        run_append("keyword a\tcat\tgsc\t5.1\t500\n", on_date=d(130))  # flat
        run_append("keyword a\tcat\tgsc\t4.9\t500\n", on_date=d(200))  # flat
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "tracking")  # NOT candidate-for-swap

    def test_deep_history_four_consecutive_flat_is_candidate(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))    # new
        run_append("keyword a\tcat\tgsc\t9.1\t500\n", on_date=d(30))   # flat
        run_append("keyword a\tcat\tgsc\t9.2\t500\n", on_date=d(110))  # flat
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(190))  # flat
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "candidate-for-swap")

    def test_cmd_latest_on_empty_roster_returns_2(self):
        stdout, stderr = io.StringIO(), io.StringIO()
        with redirect_stdout(stdout), redirect_stderr(stderr):
            rc = kt.cmd_latest(_Args())
        self.assertEqual(rc, 2)
        self.assertIn("no tracked keywords yet", stderr.getvalue())

    def test_cmd_latest_happy_path_lists_tracked_keyword(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        stdout, stderr = io.StringIO(), io.StringIO()
        with redirect_stdout(stdout), redirect_stderr(stderr):
            rc = kt.cmd_latest(_Args())
        self.assertEqual(rc, 0)
        self.assertIn("keyword a", stdout.getvalue())
        self.assertIn("new", stdout.getvalue())

    def test_retire_marks_status_and_leaves_history(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        rc = run_retire("keyword a", replacement="keyword b", on_date=d(30))
        self.assertEqual(rc, 0)
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["status"], "retired")
        self.assertIn("keyword b", row["note"])
        self.assertEqual(len(kt.rows_for(kt.load_rows(), "keyword a")), 2)  # history kept

    def test_retire_unknown_keyword_errors(self):
        rc, stderr = run_retire_capture("never tracked", on_date=d(0))
        self.assertEqual(rc, 1)
        self.assertIn("not tracked", stderr)

    def test_retire_already_retired_errors(self):
        run_append("keyword a\tcat\tgsc\t9.0\t500\n", on_date=d(0))
        run_retire("keyword a", on_date=d(30))
        rc, stderr = run_retire_capture("keyword a", on_date=d(60))
        self.assertEqual(rc, 1)
        self.assertIn("already retired", stderr)


# ---------------------------------------------------------------- CLI shims


class _Args:
    def __init__(self, **kw):
        self.__dict__.update(kw)
        self.__dict__.setdefault("date", None)
        self.__dict__.setdefault("replacement", None)


def run_bootstrap(tsv, on_date):
    stdin = sys.stdin
    sys.stdin = io.StringIO(tsv)
    try:
        return kt.cmd_bootstrap(_Args(date=on_date))
    finally:
        sys.stdin = stdin


def run_bootstrap_capture(tsv, on_date):
    stdin = sys.stdin
    sys.stdin = io.StringIO(tsv)
    stdout, stderr = io.StringIO(), io.StringIO()
    try:
        with redirect_stdout(stdout), redirect_stderr(stderr):
            kt.cmd_bootstrap(_Args(date=on_date))
    finally:
        sys.stdin = stdin
    return stdout.getvalue(), stderr.getvalue()


def run_append(tsv, on_date):
    stdin = sys.stdin
    sys.stdin = io.StringIO(tsv)
    try:
        return kt.cmd_append(_Args(date=on_date))
    finally:
        sys.stdin = stdin


def run_append_capture(tsv, on_date):
    stdin = sys.stdin
    sys.stdin = io.StringIO(tsv)
    stdout, stderr = io.StringIO(), io.StringIO()
    try:
        with redirect_stdout(stdout), redirect_stderr(stderr):
            kt.cmd_append(_Args(date=on_date))
    finally:
        sys.stdin = stdin
    return stdout.getvalue(), stderr.getvalue()


def run_retire(keyword, on_date, replacement=None):
    return kt.cmd_retire(_Args(keyword=keyword, date=on_date, replacement=replacement))


def run_retire_capture(keyword, on_date, replacement=None):
    stdout, stderr = io.StringIO(), io.StringIO()
    with redirect_stdout(stdout), redirect_stderr(stderr):
        rc = kt.cmd_retire(_Args(keyword=keyword, date=on_date, replacement=replacement))
    return rc, stderr.getvalue()


if __name__ == "__main__":
    unittest.main()
