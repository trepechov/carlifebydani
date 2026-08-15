# SEO Performance Monitoring — Methodology & Reference

**Purpose:** capture a consistent monthly snapshot for `carlifebydani.com` so
**change is tracked over time and turned into on-site SEO actions**. Two layers:
**technical/off-site** (Core Web Vitals + page speed — Google ranks on the **CrUX
field data**, not the Lighthouse lab score or GTmetrix grade, so field is primary
and lab/GTmetrix are secondary diagnostics) and **on-site/content** (Search
Console query→page performance, mined each run into a prioritized action list).
Each run also appends a machine-readable trend row to
[`reports/seo-performance/history.csv`](../../reports/seo-performance/history.csv).

This file is the **methodology and decision-rules reference**. The step-by-step
*procedure* for producing a snapshot now lives in a skill, and the generated
snapshots live in a separate reports folder:

- **Generator skill:** [`.claude/skills/seo-performance-report/SKILL.md`](../../.claude/skills/seo-performance-report/SKILL.md)
  — ask Claude to **"generate the SEO performance report"** (or `/seo-performance-report`).
- **Generated reports:** [`reports/seo-performance/`](../../reports/seo-performance/)
  — one dated `YYYY-MM-DD-snapshot.md` per month, committed.

---

## Data sources (summary)

| Source | What it gives | How | Notes |
|---|---|---|---|
| **PageSpeed Insights (REST)** | CrUX real-user **field data** (LCP, INP, CLS, FCP, TTFB) — the ranking signal | `runPagespeed` v5 → `.loadingExperience`, per form factor | **Primary.** ⚠️ Do **not** WebFetch a `pagespeed.web.dev/analysis/<id>` URL — it serves the *stored* analysis for that id, so reusing a prior snapshot's id returns stale field data (hit 2026-08-07). CLS percentile is ×100 |
| **PageSpeed Insights (PSI MCP)** | Lighthouse **lab** scores (Perf/A11y/BP/SEO + TBT/SI/lab-LCP/CLS) | `mcp__psi__psi_lighthouse` per form factor | **Live — keyed & verified 2026-07-29.** Mobile lab is throttled — sanity-check against field data, never a ranking verdict |
| **GTmetrix (MCP)** | Grade, Performance/Structure, per-resource weight, top Lighthouse issues | `mcp__gtmetrix__*` tools | **Live — confirmed 2026-07-29.** Report page is Cloudflare-403 to WebFetch, so MCP is the path |
| **Google Search Console (MCP)** | Actual search **outcome** — clicks, impressions, CTR, avg position (per query/page), indexing & sitemap status | `mcp__google-search-console__*` tools | **Live — confirmed 2026-07-29.** The ranking-outcome signal the other sources only predict |
| **Optimization ledger** | Whether a past `seo-article-optimize` change actually shipped and worked | `reports/seo-optimizations/ledger.csv` + `checks.csv` (read-only, no MCP) | **Live — added 2026-08-14.** Closes the loop: this report finds pages worth fixing, the pipeline fixes them, this report's Step 4a verifies whether it worked. See `reports/seo-optimizations/README.md` |
| **Tracked-keyword store** | Trend/status for the 10 keywords tracked in Semrush's free-tier rank tracker — Semrush itself can't be read or written via MCP on this plan | `reports/seo-performance/tracked-keywords.csv` + `tools/keyword_tracking.py` (no MCP, no live Semrush call) | **Live — added 2026-08-15.** GSC-derived by default, since it's the only live position signal this report can reach. See `reports/seo-performance/README.md` |

### MCP server configuration

Setup, auth, scope and cost for every server above — plus the ones this file does
not use (Semrush, DataForSEO, GA4, WordPress) — live in a single inventory:
**[`docs/MCP_SERVERS.md`](../MCP_SERVERS.md)**.

None are committed: all are registered at local or user scope in `~/.claude.json`,
with the local stdio runtimes in the gitignored `.mcp-local/`. A **session
restart** is required after registering any of them before their tools appear.

---

## Comparison schema (keep identical across snapshots)

- **PageSpeed field data (mobile, then desktop):** `Assessment | LCP | INP | CLS |
  FCP | TTFB` — each value + rating + % distribution.
- **PageSpeed lab (mobile, then desktop):** `Performance | Accessibility |
  Best-Practices | SEO | TBT | Speed Index` (+ lab LCP/CLS).
- **GTmetrix:** `Score | Grade | Performance % | Structure % | LCP | TBT | CLS |
  FCP | Speed Index | TTI | Page Size | Requests | Fully Loaded`.
- **Search Console:** `Clicks | Impressions | CTR | Avg position` (headline) +
  top queries/pages + indexing/sitemap status.
- **On-site action items:** carried forward each run and re-scored (position/CTR)
  → ✅ done / ↔ flat / ⬆️⬇️ moved.
- **Trend log:** one row per snapshot in `history.csv` (see its header for the
  exact column list) — the fast machine-readable diff across all snapshots.

Always note the **CrUX 28-day date range** and the **GTmetrix test location +
connection** — comparisons are only valid across the same window/config.

---

## Decision rules (what the numbers mean for SEO)

- **Core Web Vitals thresholds (Google ranking):** LCP ≤2.5s good / ≤4s NI /
  >4s poor · INP ≤200ms / ≤500ms / >500ms · CLS ≤0.1 / ≤0.25 / >0.25. The page
  "passes" only when **all three (LCP, INP, CLS) are Good at the 75th percentile.**
- Field data moving the wrong way = investigate before it costs ranking; lab +
  GTmetrix diagnostics tell you *why* (render-blocking, image/font weight, TBT).
- Field data lags deploys by up to ~28 days (rolling window), so a change shipped
  mid-month only fully shows in the following month's snapshot — record deploy
  dates alongside snapshots.

### On-site opportunity thresholds (Search Console → action items)
- **Striking distance:** avg position **5–20** + **≥300 impressions**, non-brand
  → optimise the target page (title/H1/coverage) to push into top-3. Top lever.
- **Low-CTR winner:** position **≤5** but CTR below the rank norm (pos 1 ≈25–30%,
  2–3 ≈10–15%, 4–5 ≈5–8%) → rewrite title/meta to earn the click.
- **Cannibalisation:** one query split across ≥2 URLs → consolidate/canonicalise.
- Exclude **brand** queries (clbd, carlife by dani, carlifebydani, clbd parts).
- Enforce the impression floor — 28-day CTR/position on low-impression rows is noise.

### Tracked-keyword staleness thresholds (Step 4c)
- **Trend** compares a tracked keyword's position only against its own prior
  reading **from the same signal source** (`gsc` vs `semrush_manual`) — GSC's
  blended position and a pasted-in Semrush number aren't the same measurement,
  so a source switch resets to `new` instead of reading as movement.
- **Flat:** position moves less than **1.0** since the last same-source
  reading. **No footprint:** impressions below the **~50** floor (same floor
  Step 4a uses) — position is noise below it regardless of the number.
- **Swap candidate:** the flat/no-footprint streak must be **3 consecutive
  real reviews** *and* span **≥75 days** — the day floor stops a skipped or
  doubled-up report run from mis-triggering the count. Every review in that
  window is checked, not just the most recent one — a real rank change two
  reviews ago still counts as "not flat" even if the two reviews since have
  been flat again.
- Replacement candidates come from **this same run's Step 4b opportunity
  list** — no separate GSC pull for them.
- Keyword-to-query matching is **exact string** (case-insensitive,
  whitespace- and Unicode-normalised). Traffic under a near-variant phrasing
  (word order, spelling) won't be picked up — a known limitation, not a bug.
- Once a suggested swap is applied in Semrush, the old keyword is **retired**
  (`python3 tools/keyword_tracking.py retire`) rather than deleted — its rows
  stay in `tracked-keywords.csv` with `status=retired` for history.

## Change log of what was shipped (context for reading the trend)

- **2026-07-10** — deferred render-blocking JS, lazy-loaded below-fold images,
  fixed font-loading chain + preconnect hints, fixed oversized mobile image
  `sizes`, added homepage H1. (Expect LCP/FCP improvement to land in field data
  through late July / August.)
- See `docs/SEO_PROPOSALS.md` for the still-pending items (title-tag fix, Review /
  VideoObject / CollectionPage schema).
- **2026-07-10 → 2026-08-07 — no on-page SEO changes shipped.** Commits in this
  window were EV-News-plugin and reporting-tooling work only. The 2026-08-07
  snapshot's flat action-item positions confirm it; read that snapshot's movements
  as organic, not as the result of any intervention.
