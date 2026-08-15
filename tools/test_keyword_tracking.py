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
        run_append("keyword a\tcat\tgsc\t8.9\t500\n", on_date=d(80))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "flat")
        self.assertEqual(row["status"], "candidate-for-swap")

    # ---------------------------------------------------------------- edges

    def test_low_impressions_is_no_footprint_not_falling(self):
        run_append("keyword a\tcat\tgsc\t4.0\t500\n", on_date=d(0))
        run_append("keyword a\tcat\tgsc\t\t12\n", on_date=d(30))
        row = kt.latest_row(kt.load_rows(), "keyword a")
        self.assertEqual(row["trend"], "no-footprint")

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

    def test_already_tracked_keyword_is_skipped_on_bootstrap(self):
        run_bootstrap("keyword a\tev\n", on_date=d(0))
        _, stderr = run_bootstrap_capture("keyword a\tev\n", on_date=d(1))
        self.assertIn("already tracked", stderr)
        self.assertEqual(len(kt.current_roster(kt.load_rows())), 1)


# ---------------------------------------------------------------- CLI shims


class _Args:
    def __init__(self, **kw):
        self.__dict__.update(kw)


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


if __name__ == "__main__":
    unittest.main()
