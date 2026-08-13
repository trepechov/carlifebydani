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
5. writes a new `YYYY-MM-DD-snapshot.md` here,
6. appends one machine-readable row to `history.csv`, and
7. compares against the previous snapshot, re-scoring carried-forward action
   items as ✅ done / ↔ flat / ⬆️⬇️ moved.

## Files

- `YYYY-MM-DD-snapshot.md` — one dated snapshot per capture. Newest = latest month.
- `history.csv` — one row per snapshot; the fast machine-readable diff across all
  of them. See its header for the column list.

## Related

- **Methodology, decision rules, and shipped-change log:**
  [`docs/seo-performance/README.md`](../../docs/seo-performance/README.md)
- **The generator skill:**
  [`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)
- **Per-article optimization** (this skill finds the pages; that one fixes them):
  [`reports/seo-metatags/`](../seo-metatags/)
- **Competitive landscape:** [`reports/competitor-gap/`](../competitor-gap/)
- **Action backlog:** [`docs/SEO_EV_NEWS_TODO.md`](../../docs/SEO_EV_NEWS_TODO.md)
