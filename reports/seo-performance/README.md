# SEO Performance Reports — carlifebydani.com

Committed, dated site-wide snapshots, one per month, so month-over-month SEO
progress can be tracked. These are **generated artifacts**, not documentation —
each file is the output of a single monthly capture.

## How these are generated

Reports are produced by the **`seo-performance-report` skill**
([`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)).

Each month, ask Claude to **"generate the SEO performance report"** (or run
`/seo-performance-report`). The skill:
1. Pulls CrUX real-user **field data** from the PageSpeed Insights REST API
   (mobile + desktop) — the actual ranking signal,
2. captures Lighthouse **lab** scores via the PSI MCP,
3. runs one fresh GTmetrix test via the GTmetrix MCP,
4. pulls Search Console query→page performance and **mines it into prioritized
   on-site action items** (striking-distance, low-CTR winners, cannibalisation),
5. reviews the 10 keywords tracked in Semrush's free-tier rank tracker and
   flags any that have gone stale (see **Tracked-keyword review** below),
6. writes a new `YYYY-MM-DD-snapshot.md` here,
7. appends one machine-readable row to `history.csv`, and
8. compares against the previous snapshot, re-scoring carried-forward action
   items as ✅ done / ↔ flat / ⬆️⬇️ moved.

## Files

- `YYYY-MM-DD-snapshot.md` — one dated snapshot per capture. Newest = latest month.
- `history.csv` — one row per snapshot; the fast machine-readable diff across all
  of them. See its header for the column list.
- `tracked-keywords.csv` — append-only, one row per (keyword, review_date). See
  **Tracked-keyword review** below.

## Tracked-keyword review

Semrush's free plan caps rank tracking at 10 keywords and can't be read or
written via MCP, so `tracked-keywords.csv` is the only record of which
keywords are tracked and how they've moved. Written and read by
[`tools/keyword_tracking.py`](../../tools/keyword_tracking.py) — never edit
the file by hand.

**Columns:**

| Column | Notes |
|---|---|
| `review_date` | the date this row's reading was taken |
| `keyword`, `category` | the tracked phrase and a free-text grouping (e.g. `renault`, `charging`) |
| `months_tracked` | count of real reviews for this keyword so far — `0` on the bootstrap placeholder row |
| `signal_source` | `` (bootstrap placeholder) \| `gsc` \| `semrush_manual` — trend only compares rows sharing the same source |
| `position`, `impressions` | this reading's raw values |
| `trend` | `new` \| `flat` \| `rising` \| `falling` \| `no-footprint` — see `docs/seo-performance/README.md` for the exact thresholds |
| `status` | `tracking` \| `candidate-for-swap` |
| `note` | free text |

**Who writes what:** `seo-performance-report`'s Step 4c appends one row per
tracked keyword every run, via `python3 tools/keyword_tracking.py append`.
The very first run seeds the roster with `python3 tools/keyword_tracking.py
bootstrap` instead, since no history exists yet. Nothing else writes to this
file, and nothing in this repo writes to Semrush itself — every suggested
swap is a manual action the user applies in Semrush's web UI.

## Related

- **Methodology, decision rules, and shipped-change log:**
  [`docs/seo-performance/README.md`](../../docs/seo-performance/README.md)
- **The generator skill:**
  [`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)
- **Per-article optimization** (this skill finds the pages; that one fixes them):
  [`reports/seo-metatags/`](../seo-metatags/)
- **Competitive landscape:** [`reports/competitor-gap/`](../competitor-gap/)
- **Action backlog:** [`docs/SEO_EV_NEWS_TODO.md`](../../docs/SEO_EV_NEWS_TODO.md)
