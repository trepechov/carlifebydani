# SEO skills — index

Quick orientation for the `carlifebydani.com` SEO skill set. For the *why* behind this
structure and the full history of how it got here, see
[`docs/SEO_SKILLS_REFACTOR.md`](../../docs/SEO_SKILLS_REFACTOR.md) — that document is now a
closed decision record (all 12 items landed 2026-08-14), not a live reference; start here
instead for day-to-day use.

## The article-optimization pipeline

One post, one entry point:

```
seo-article-optimize            ← say "optimize post 5240" and start here
    │
    ├─ seo-keyphrase-research    Phase A — research, keyphrase, tags. No writes.
    ├─ ev-news-transcript-content Phase B — EV News only, grounded body content.
    └─ seo-article-apply         Phase C — drafts + applies metatags/tags/alt/links.
```

`seo-article-optimize` detects the post's category and sequences the phases — you don't need
to invoke the phase skills directly or think about ordering. All three phases read and append
to one shared report per post: `reports/seo-metatags/<date>-<id>-<slug>.md`
([template](_shared/report-template.md)).

## Shared files (not skills — no frontmatter, never invoked directly)

- [`_shared/constants.md`](_shared/constants.md) — site constants (WP alias, category ids,
  Yoast meta keys…) and traps that apply across every phase.
- [`_shared/approval-gate.md`](_shared/approval-gate.md) — the manifest/multiSelect/revise-loop
  procedure every write goes through. No skill embeds its own copy of this.
- [`_shared/report-template.md`](_shared/report-template.md) — the one report template all
  three phases append to.

## The other skill

- [`seo-performance-report`](seo-performance-report/SKILL.md) — the monthly site-wide
  snapshot. Finds pages worth optimizing (feeds `docs/SEO_EV_NEWS_TODO.md`) and verifies
  whether past optimizations worked (reads `reports/seo-optimizations/`). Independent
  cadence from the per-article pipeline above; run it monthly regardless of how much
  per-article work happened that month.

## Reports this pipeline produces

| Directory | What | README |
|---|---|---|
| `reports/seo-metatags/` | Per-post optimization reports (research → content → applied) | [README](../../reports/seo-metatags/README.md) |
| `reports/seo-optimizations/` | The ledger: what was applied, and whether it worked | [README](../../reports/seo-optimizations/README.md) |
| `reports/yoast-meta-backup/` | Pre-write CSV snapshots of Yoast fields and media alt text (no WP revisions cover these) | — |
| `reports/seo-performance/` | Monthly site-wide snapshots | [README](../../docs/seo-performance/README.md) |

## Background docs

- [`docs/EV_NEWS_CONTENT_METHOD.md`](../../docs/EV_NEWS_CONTENT_METHOD.md) — the measured
  facts behind Phase B (corpus stats, the cross-episode finding, the `post_content` decision).
- [`docs/SEO_TRANSCRIPT_MCP_PROPOSALS.md`](../../docs/SEO_TRANSCRIPT_MCP_PROPOSALS.md) — what's
  proposed but **not** built yet (hosts'-claims block, chapters/FAQ schema, evergreen hubs).
- [`docs/SEO_EV_NEWS_TODO.md`](../../docs/SEO_EV_NEWS_TODO.md) — the live backlog.
- [`docs/MCP_SERVERS.md`](../../docs/MCP_SERVERS.md) — the MCP toolchain this all runs on.
