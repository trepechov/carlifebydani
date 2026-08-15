#!/usr/bin/env python3
"""Monthly review helper for reports/seo-performance/tracked-keywords.csv.

Semrush's free-tier rank tracker caps at 10 keywords and can't be read or
written via MCP on this plan, so this file is the only record of which
keywords are currently tracked and how they've moved. Append-only: one row
per (keyword, review_date). The current roster is the most recent row per
distinct keyword.

    review_date,keyword,category,months_tracked,signal_source,position,
    impressions,trend,status,note

  signal_source  "" (bootstrap placeholder) | gsc | semrush_manual
  trend          new | flat | rising | falling | no-footprint
  status         tracking | candidate-for-swap

Typical use, from seo-performance-report's Step 4c:

    # 1. what's currently tracked?
    python3 tools/keyword_tracking.py latest

    # 2. very first run only — seed the roster with no signal data yet
    python3 tools/keyword_tracking.py bootstrap <<'TSV'
    рено 5	renault
    зарядна станция	charging
    TSV

    # 3. every other run — record this month's reading per keyword
    python3 tools/keyword_tracking.py append <<'TSV'
    рено 5	renault	gsc	6.1	1198
    зарядна станция	charging	gsc	4.2	310
    TSV

Trend is computed against the keyword's own prior rows, comparing only
rows that share the same signal_source (GSC's blended position and a
pasted-in Semrush number aren't the same measurement — mixing them across
a comparison would read as movement that never happened). A signal-source
switch resets the trend to "new" for that row, which also breaks any
flat/no-footprint streak in progress. "Impressions below ~50" always reads
as no-footprint regardless of the position number, mirroring the
impression floor seo-performance-report's own Step 4a already uses.

A keyword becomes a swap candidate only once it has 3 consecutive
flat/no-footprint reviews *and* those three reviews span at least 75 days
— the day floor stops an irregular reporting cadence (a skipped month, or
two runs in one month) from mis-triggering the count.

Stdlib only. Exit codes: 0 = ok, 1 = error, 2 = nothing to report (roster
empty on `latest`).
"""

import argparse
import csv
import os
import sys
from datetime import date, timedelta

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
STORE = os.path.join(ROOT, "reports", "seo-performance", "tracked-keywords.csv")

FIELDS = [
    "review_date", "keyword", "category", "months_tracked", "signal_source",
    "position", "impressions", "trend", "status", "note",
]

IMPRESSION_FLOOR = 50          # same floor seo-performance-report's Step 4a uses
FLAT_THRESHOLD = 1.0           # position delta under this counts as flat
STALE_REVIEW_COUNT = 3         # consecutive flat/no-footprint reviews to flag
STALE_MIN_SPAN_DAYS = 75       # and they must span at least this many days


def today():
    return date.today().isoformat()


def norm(kw):
    return " ".join((kw or "").strip().split()).lower()


# --------------------------------------------------------------------- store


def load_rows():
    """Return all rows, oldest first. Missing file is an empty list."""
    if not os.path.exists(STORE):
        return []
    with open(STORE, newline="", encoding="utf-8") as fh:
        return list(csv.DictReader(fh))


def save_rows(rows):
    os.makedirs(os.path.dirname(STORE), exist_ok=True)
    ordered = sorted(rows, key=lambda r: (norm(r.get("keyword")), r.get("review_date", "")))
    tmp = STORE + ".tmp"
    with open(tmp, "w", newline="", encoding="utf-8") as fh:
        writer = csv.DictWriter(fh, fieldnames=FIELDS)
        writer.writeheader()
        for row in ordered:
            writer.writerow({k: row.get(k, "") for k in FIELDS})
    os.replace(tmp, STORE)


def rows_for(rows, keyword):
    """This keyword's rows, oldest first."""
    key = norm(keyword)
    matches = [r for r in rows if norm(r.get("keyword")) == key]
    matches.sort(key=lambda r: r.get("review_date", ""))
    return matches


def latest_row(rows, keyword):
    matches = rows_for(rows, keyword)
    return matches[-1] if matches else None


def current_roster(rows):
    """Most recent row per distinct keyword, in first-seen order."""
    seen, roster = [], {}
    for r in rows:
        key = norm(r.get("keyword"))
        if key not in roster:
            seen.append(key)
        roster[key] = r  # rows are oldest-first, so last write wins
    return [roster[k] for k in seen]


# ------------------------------------------------------------------ classify


def classify(prior_real_rows, signal_source, position, impressions):
    """Return (trend, months_tracked) for a new row given this keyword's
    prior *real* rows (signal_source set, i.e. not a bootstrap placeholder),
    oldest first."""
    months_tracked = len(prior_real_rows) + 1

    try:
        impr = float(impressions)
    except (TypeError, ValueError):
        impr = 0.0
    if impr < IMPRESSION_FLOOR:
        return "no-footprint", months_tracked

    if not prior_real_rows:
        return "new", months_tracked

    prev = prior_real_rows[-1]
    if prev.get("signal_source") != signal_source:
        # Different measurement methodology — not comparable. Treat as a
        # fresh baseline rather than guessing at a delta.
        return "new", months_tracked

    try:
        prev_pos = float(prev.get("position"))
        new_pos = float(position)
    except (TypeError, ValueError):
        return "new", months_tracked

    delta = prev_pos - new_pos  # positive = moved toward #1
    if abs(delta) < FLAT_THRESHOLD:
        return "flat", months_tracked
    return "rising" if delta > 0 else "falling", months_tracked


def is_stale(prior_real_rows, this_row):
    """Would this row, plus the STALE_REVIEW_COUNT-1 real rows before it,
    span a qualifying flat/no-footprint streak over enough days?

    The earliest row in that span only anchors the date floor — its own
    trend is always "new" (nothing preceded it to compare against), so
    only the *later* STALE_REVIEW_COUNT-1 trend values are required to
    read flat/no-footprint.
    """
    span = prior_real_rows[-(STALE_REVIEW_COUNT - 1):] + [this_row]
    if len(span) < STALE_REVIEW_COUNT:
        return False
    if any(r.get("trend") not in ("flat", "no-footprint") for r in span[1:]):
        return False
    try:
        start = date.fromisoformat(span[0]["review_date"])
        end = date.fromisoformat(span[-1]["review_date"])
    except (KeyError, ValueError):
        return False
    return (end - start) >= timedelta(days=STALE_MIN_SPAN_DAYS)


# -------------------------------------------------------------------- verbs


def cmd_latest(args):
    roster = current_roster(load_rows())
    if not roster:
        print("no tracked keywords yet — run `bootstrap` first", file=sys.stderr)
        return 2
    print(f"{'keyword':<32} {'category':<14} {'mo':>3} {'src':<13} "
          f"{'pos':>6} {'impr':>7} {'trend':<12} status")
    print("-" * 100)
    for r in roster:
        print(
            f"{r.get('keyword','')[:31]:<32} {r.get('category','')[:13]:<14} "
            f"{r.get('months_tracked','0'):>3} {r.get('signal_source','')[:12]:<13} "
            f"{r.get('position',''):>6} {r.get('impressions',''):>7} "
            f"{r.get('trend',''):<12} {r.get('status','')}"
        )
    return 0


def cmd_bootstrap(args):
    """Seed the roster from stdin TSV: keyword, category. No signal data —
    this run hasn't queried GSC for these keywords yet."""
    rows = load_rows()
    text = sys.stdin.read()
    if not text.strip():
        print("nothing on stdin", file=sys.stderr)
        return 1

    added = 0
    for line in csv.reader(text.splitlines(), delimiter="\t"):
        if not line or not line[0].strip():
            continue
        keyword = line[0].strip()
        if norm(keyword) == "keyword":
            continue
        category = line[1].strip() if len(line) > 1 else ""
        if latest_row(rows, keyword) is not None:
            print(f"skip (already tracked): {keyword}", file=sys.stderr)
            continue
        rows.append({
            "review_date": args.date or today(),
            "keyword": keyword,
            "category": category,
            "months_tracked": "0",
            "signal_source": "",
            "position": "",
            "impressions": "",
            "trend": "new",
            "status": "tracking",
            "note": "bootstrap — no signal data yet",
        })
        added += 1

    save_rows(rows)
    print(f"seeded {added} keyword(s) -> {os.path.relpath(STORE, ROOT)}")
    return 0


def cmd_append(args):
    """Record this run's reading from stdin TSV: keyword, category,
    signal_source, position, impressions."""
    rows = load_rows()
    text = sys.stdin.read()
    if not text.strip():
        print("nothing on stdin", file=sys.stderr)
        return 1

    review_date = args.date or today()
    written, candidates = 0, []
    for line in csv.reader(text.splitlines(), delimiter="\t"):
        if not line or not line[0].strip():
            continue
        keyword = line[0].strip()
        if norm(keyword) == "keyword":
            continue
        cells = (line + [""] * 5)[:5]
        category, signal_source, position, impressions = cells[1:5]
        category, signal_source = category.strip(), signal_source.strip()

        prior = rows_for(rows, keyword)
        prior_real = [r for r in prior if r.get("signal_source")]

        prior_category = prior[-1].get("category") if prior else ""
        if prior_category and category and prior_category != category:
            print(
                f"WARN category drift for {keyword!r}: "
                f"{prior_category!r} -> {category!r} (kept new value)",
                file=sys.stderr,
            )

        trend, months_tracked = classify(prior_real, signal_source, position, impressions)
        row = {
            "review_date": review_date,
            "keyword": keyword,
            "category": category or prior_category,
            "months_tracked": str(months_tracked),
            "signal_source": signal_source,
            "position": position.strip(),
            "impressions": impressions.strip(),
            "trend": trend,
            "status": "tracking",
            "note": "",
        }
        if is_stale(prior_real, row):
            row["status"] = "candidate-for-swap"
            candidates.append(keyword)

        rows.append(row)
        written += 1

    save_rows(rows)
    print(f"appended {written} row(s) -> {os.path.relpath(STORE, ROOT)}")
    if candidates:
        print(f"CANDIDATES={';'.join(candidates)}", file=sys.stderr)
    return 0


def main():
    parser = argparse.ArgumentParser(
        description="Monthly review helper for tracked-keywords.csv.")
    sub = parser.add_subparsers(dest="cmd", required=True)

    lp = sub.add_parser("latest", help="print the current roster")
    lp.set_defaults(func=cmd_latest)

    bp = sub.add_parser("bootstrap", help="seed the roster (stdin TSV: keyword, category)")
    bp.add_argument("--date", default=None, help="override review_date (YYYY-MM-DD)")
    bp.set_defaults(func=cmd_bootstrap)

    ap = sub.add_parser(
        "append",
        help="record this run's reading (stdin TSV: keyword, category, "
             "signal_source, position, impressions)",
    )
    ap.add_argument("--date", default=None, help="override review_date (YYYY-MM-DD)")
    ap.set_defaults(func=cmd_append)

    args = parser.parse_args()
    sys.exit(args.func(args))


if __name__ == "__main__":
    main()
