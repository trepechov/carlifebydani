# SEO Performance Reports — carlifebydani.com

Committed, dated snapshots of Core Web Vitals + page-speed data, one per month, so
month-over-month SEO progress can be tracked. These are **generated artifacts**,
not documentation — each file is the output of a single monthly capture.

## How these are generated

Reports are produced by the **`seo-performance-report` skill**
([`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)).

Each month, ask Claude to **"generate the SEO performance report"** (or run
`/seo-performance-report`). The skill:
1. Pulls CrUX real-user field data from PageSpeed Insights (mobile + desktop),
2. runs a fresh GTmetrix test via the GTmetrix MCP server,
3. optionally captures Lighthouse lab scores (PSI API),
4. writes a new `YYYY-MM-DD-snapshot.md` here, and
5. compares it against the previous snapshot and gives a historical read.

## Files

- `YYYY-MM-DD-snapshot.md` — one dated snapshot per capture. Newest = latest month.

## Related

- **Methodology, decision rules, and shipped-change log:**
  [`docs/seo-performance/README.md`](../../docs/seo-performance/README.md)
- **The generator skill:**
  [`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)
