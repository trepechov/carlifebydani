#!/usr/bin/env python3
"""Local cache for paid/slow SEO data — check here before spending API units.

Two stores, both under data/seo-cache/:

  keywords.csv   Cumulative keyword ledger: one upserted row per (keyword,
                 database). Search volume is a 12-month average and barely moves,
                 so a hit here is as good as a fresh call. Committed to git, so
                 `git log -p` gives you volume history for free.

  raw/<ns>/      Verbatim API responses keyed by a caller-supplied cache key,
                 with a per-namespace TTL. For anything not shaped like a keyword
                 row: SERPs, autocomplete, GSC pulls, Labs responses.

Typical use, from the seo-article-optimize skill:

    # 1. what do we already know?
    python3 tools/seo_cache.py kw get "тесла цена" "зареждане на електромобил"
    #    -> prints hits; exits 2 and prints MISSING= if a call is still needed

    # 2. spend units only on the misses, then bank the result
    python3 tools/seo_cache.py kw put --source semrush <<'TSV'
    тесла цена	1300	0.14	0.05	42
    TSV

    # raw responses
    python3 tools/seo_cache.py raw get serp "зареждане|bg" > serp.json
    python3 tools/seo_cache.py raw put serp "зареждане|bg" --file resp.json

Stdlib only. Exit codes: 0 = full hit, 2 = miss/stale (go fetch), 1 = error.
"""

import argparse
import csv
import hashlib
import json
import os
import re
import sys
from datetime import date, datetime, timezone

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CACHE_DIR = os.path.join(ROOT, "data", "seo-cache")
LEDGER = os.path.join(CACHE_DIR, "keywords.csv")
RAW_DIR = os.path.join(CACHE_DIR, "raw")

FIELDS = ["keyword", "database", "volume", "cpc", "competition", "kd", "source", "fetched"]

# Days after which a cached entry is considered stale. Search volume is a
# 12-month rolling average; SERPs churn much faster.
TTL_DAYS = {
    "keywords": 90,
    "serp": 14,
    "autocomplete": 30,
    "gsc": 7,
    "ga4": 7,
    "labs": 30,
    "_default": 14,
}


def today():
    return datetime.now(timezone.utc).date().isoformat()


def age_days(stamp):
    """Age in days of an ISO date string; None if unparseable."""
    try:
        return (date.fromisoformat(stamp[:10]) - date.today()).days * -1
    except (ValueError, TypeError):
        return None


def norm(kw):
    """Normalize a keyword for comparison: lowercase, collapse whitespace."""
    return re.sub(r"\s+", " ", (kw or "").strip()).lower()


# ---------------------------------------------------------------- keyword ledger


def load_ledger():
    """Return {(keyword, database): row}. Missing file is an empty ledger."""
    rows = {}
    if not os.path.exists(LEDGER):
        return rows
    with open(LEDGER, newline="", encoding="utf-8") as fh:
        for row in csv.DictReader(fh):
            rows[(norm(row.get("keyword")), row.get("database", ""))] = row
    return rows


def save_ledger(rows):
    os.makedirs(CACHE_DIR, exist_ok=True)
    ordered = sorted(rows.values(), key=lambda r: (r.get("database", ""), norm(r.get("keyword"))))
    tmp = LEDGER + ".tmp"
    with open(tmp, "w", newline="", encoding="utf-8") as fh:
        writer = csv.DictWriter(fh, fieldnames=FIELDS)
        writer.writeheader()
        for row in ordered:
            writer.writerow({k: row.get(k, "") for k in FIELDS})
    os.replace(tmp, LEDGER)


def cmd_kw_get(args):
    ledger = load_ledger()
    max_age = args.max_age if args.max_age is not None else TTL_DAYS["keywords"]
    hits, missing, stale = [], [], []

    for kw in args.keywords:
        row = ledger.get((norm(kw), args.database))
        if not row:
            missing.append(kw)
            continue
        age = age_days(row.get("fetched", ""))
        if age is None or age > max_age:
            stale.append(kw)
            (hits if args.allow_stale else missing).append(row if args.allow_stale else kw)
            continue
        hits.append(row)

    if args.json:
        print(json.dumps({"hits": hits, "missing": missing, "stale": stale},
                         ensure_ascii=False, indent=2))
    else:
        rows = hits
        if rows:
            print(f"{'keyword':<45} {'vol':>7} {'kd':>5} {'cpc':>6} {'src':<12} {'age':>5}")
            print("-" * 85)
            for r in rows:
                age = age_days(r.get("fetched", ""))
                print(
                    f"{r.get('keyword','')[:44]:<45} {r.get('volume',''):>7} "
                    f"{r.get('kd',''):>5} {r.get('cpc',''):>6} "
                    f"{r.get('source','')[:11]:<12} {str(age) + 'd' if age is not None else '?':>5}"
                )
        if stale:
            print(f"\nSTALE (>{max_age}d): {'; '.join(stale)}", file=sys.stderr)
        if missing:
            # Semicolon-joined: paste straight into Semrush phrase_these `phrase`.
            print(f"\nMISSING={';'.join(missing)}", file=sys.stderr)

    return 2 if missing else 0


def cmd_kw_put(args):
    """Upsert rows read from stdin as TSV or CSV.

    Columns, in order: keyword, volume, cpc, competition, kd. Trailing columns
    may be omitted. A header line is detected and skipped.
    """
    ledger = load_ledger()
    text = sys.stdin.read()
    if not text.strip():
        print("nothing on stdin", file=sys.stderr)
        return 1

    delim = "\t" if "\t" in text.splitlines()[0] else ","
    written = 0
    for line in csv.reader(text.splitlines(), delimiter=delim):
        if not line or not line[0].strip():
            continue
        if norm(line[0]) in ("keyword", "phrase", "ph"):
            continue
        cells = (line + [""] * 5)[:5]
        row = {
            "keyword": cells[0].strip(),
            "database": args.database,
            "volume": cells[1].strip(),
            "cpc": cells[2].strip(),
            "competition": cells[3].strip(),
            "kd": cells[4].strip(),
            "source": args.source,
            "fetched": args.date or today(),
        }
        ledger[(norm(row["keyword"]), args.database)] = row
        written += 1

    save_ledger(ledger)
    print(f"upserted {written} row(s) into {os.path.relpath(LEDGER, ROOT)} "
          f"({len(ledger)} total)")
    return 0


def cmd_kw_search(args):
    """Substring search across cached keywords — 'what do we know about X'."""
    ledger = load_ledger()
    needle = norm(args.substring)
    found = [r for (kw, _db), r in ledger.items() if needle in kw]
    found.sort(key=lambda r: -int(r.get("volume") or 0))
    if not found:
        print(f"no cached keyword contains {args.substring!r}", file=sys.stderr)
        return 2
    for r in found[: args.limit]:
        print(f"{r.get('volume',''):>7}  {r.get('keyword','')}  "
              f"[{r.get('source','')} {r.get('fetched','')}]")
    return 0


# ------------------------------------------------------------------- raw cache


def raw_path(namespace, key):
    digest = hashlib.sha1(f"{namespace}|{norm(key)}".encode("utf-8")).hexdigest()[:12]
    # Keep Cyrillic in the filename so cached files stay identifiable at a glance.
    label = re.sub(r"[^\w]+", "-", norm(key), flags=re.UNICODE)[:40].strip("-") or "key"
    return os.path.join(RAW_DIR, namespace, f"{label}-{digest}.json")


def cmd_raw_get(args):
    path = raw_path(args.namespace, args.key)
    if not os.path.exists(path):
        print(f"MISS {args.namespace}:{args.key}", file=sys.stderr)
        return 2

    with open(path, encoding="utf-8") as fh:
        blob = json.load(fh)

    ttl = args.max_age if args.max_age is not None else TTL_DAYS.get(
        args.namespace, TTL_DAYS["_default"])
    age = age_days(blob.get("fetched", ""))
    if age is None or age > ttl:
        print(f"STALE {args.namespace}:{args.key} ({age}d > {ttl}d)", file=sys.stderr)
        return 2

    print(f"HIT {args.namespace}:{args.key} ({age}d old)", file=sys.stderr)
    json.dump(blob["payload"], sys.stdout, ensure_ascii=False, indent=2)
    print()
    return 0


def cmd_raw_put(args):
    text = open(args.file, encoding="utf-8").read() if args.file else sys.stdin.read()
    if not text.strip():
        print("nothing to store", file=sys.stderr)
        return 1
    try:
        payload = json.loads(text)
    except json.JSONDecodeError:
        payload = text  # store non-JSON (e.g. raw HTML/XML) verbatim

    path = raw_path(args.namespace, args.key)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, "w", encoding="utf-8") as fh:
        json.dump(
            {"namespace": args.namespace, "key": args.key, "fetched": today(),
             "note": args.note or "", "payload": payload},
            fh, ensure_ascii=False, indent=2,
        )
    print(f"stored {os.path.relpath(path, ROOT)}")
    return 0


def cmd_stats(args):
    ledger = load_ledger()
    print(f"keyword ledger: {len(ledger)} row(s) -> {os.path.relpath(LEDGER, ROOT)}")
    by_source = {}
    for r in ledger.values():
        by_source[r.get("source", "?")] = by_source.get(r.get("source", "?"), 0) + 1
    for src, n in sorted(by_source.items(), key=lambda kv: -kv[1]):
        print(f"  {src:<14} {n}")

    if os.path.isdir(RAW_DIR):
        print(f"\nraw cache -> {os.path.relpath(RAW_DIR, ROOT)}")
        for ns in sorted(os.listdir(RAW_DIR)):
            nsdir = os.path.join(RAW_DIR, ns)
            if not os.path.isdir(nsdir):
                continue
            files = [f for f in os.listdir(nsdir) if f.endswith(".json")]
            ttl = TTL_DAYS.get(ns, TTL_DAYS["_default"])
            fresh = 0
            for f in files:
                try:
                    with open(os.path.join(nsdir, f), encoding="utf-8") as fh:
                        age = age_days(json.load(fh).get("fetched", ""))
                    if age is not None and age <= ttl:
                        fresh += 1
                except (OSError, json.JSONDecodeError):
                    pass
            print(f"  {ns:<14} {len(files)} file(s), {fresh} fresh (ttl {ttl}d)")
    return 0


def main():
    parser = argparse.ArgumentParser(
        description="Local cache for paid SEO data — check before spending API units.")
    sub = parser.add_subparsers(dest="group", required=True)

    kw = sub.add_parser("kw", help="keyword metrics ledger").add_subparsers(
        dest="cmd", required=True)

    g = kw.add_parser("get", help="look keywords up in the ledger")
    g.add_argument("keywords", nargs="+")
    g.add_argument("--database", default="bg")
    g.add_argument("--max-age", type=int, default=None, help="days (default 90)")
    g.add_argument("--allow-stale", action="store_true",
                   help="return stale rows instead of listing them as missing")
    g.add_argument("--json", action="store_true")
    g.set_defaults(func=cmd_kw_get)

    p = kw.add_parser("put", help="upsert rows from stdin (TSV/CSV: keyword,volume,cpc,competition,kd)")
    p.add_argument("--source", required=True, help="semrush | dataforseo | gsc | manual")
    p.add_argument("--database", default="bg")
    p.add_argument("--date", default=None, help="override fetch date (YYYY-MM-DD)")
    p.set_defaults(func=cmd_kw_put)

    s = kw.add_parser("search", help="substring search across cached keywords")
    s.add_argument("substring")
    s.add_argument("--limit", type=int, default=40)
    s.set_defaults(func=cmd_kw_search)

    raw = sub.add_parser("raw", help="verbatim response cache").add_subparsers(
        dest="cmd", required=True)

    rg = raw.add_parser("get", help="print cached payload; exit 2 on miss/stale")
    rg.add_argument("namespace", help="serp | autocomplete | gsc | ga4 | labs | ...")
    rg.add_argument("key", help="stable cache key, e.g. 'зареждане|bg|desktop'")
    rg.add_argument("--max-age", type=int, default=None, help="days (per-namespace default)")
    rg.set_defaults(func=cmd_raw_get)

    rp = raw.add_parser("put", help="store a response from --file or stdin")
    rp.add_argument("namespace")
    rp.add_argument("key")
    rp.add_argument("--file", default=None)
    rp.add_argument("--note", default=None, help="how it was fetched, for provenance")
    rp.set_defaults(func=cmd_raw_put)

    st = sub.add_parser("stats", help="what is cached right now")
    st.set_defaults(func=cmd_stats)

    args = parser.parse_args()
    sys.exit(args.func(args))


if __name__ == "__main__":
    main()
