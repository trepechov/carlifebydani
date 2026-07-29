# SEO Performance Monitoring — Methodology & Reference

**Purpose:** capture a consistent monthly snapshot of Core Web Vitals + page-speed
data for `carlifebydani.com` so month-over-month progress can be compared and
optimization decisions can be made. **Angle: off-site/technical SEO — maximize
Google search ranking.** Google ranks on the **CrUX field data** (real-user Core
Web Vitals), not on the Lighthouse lab score or the GTmetrix grade, so field data
is the primary signal; lab/GTmetrix are secondary diagnostics.

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
| **PageSpeed Insights (WebFetch)** | CrUX real-user **field data** (LCP, INP, CLS, FCP, TTFB) — the ranking signal | WebFetch the `pagespeed.web.dev` analysis URL per form factor | **Primary.** Works reliably; mobile & desktop are separate `?form_factor=` URLs |
| **PageSpeed Insights API** | Lighthouse **lab** scores + field JSON | `curl` PSI v5 with a Google API key | Optional. Keyless calls 429 on a shared quota — use a key |
| **GTmetrix (MCP)** | Grade, Performance/Structure, per-resource weight, top Lighthouse issues | `mcp__gtmetrix__*` tools | **Live — confirmed 2026-07-29.** Report page is Cloudflare-403 to WebFetch, so MCP is the path |
| **Google Search Console (MCP)** | Actual search **outcome** — clicks, impressions, CTR, avg position (per query/page), indexing & sitemap status | `mcp__google-search-console__*` tools | **Live — confirmed 2026-07-29.** The ranking-outcome signal the other sources only predict |

### GTmetrix MCP — configuration

Remote HTTP MCP server `https://gtmetrix.com/mcp`, registered in Claude Code at
**local scope** (project: carlifebydani). Auth = HTTP Basic with the account API
key (`Authorization: Basic base64(APIKEY:)`); the key is stored only in
`~/.claude.json`, never committed. Server identifies as `groupone-gtmetrix` and
exposes MCP tools, resources, and prompts (run tests, read reports, history,
recommendations).

Account is GTmetrix **Basic = 5 API credits** (auto-refill to 5). Each new test
spends a credit; reading an existing report id is free — the skill runs **one
deliberate test per monthly snapshot**.

To re-add on a new machine / after key rotation:
```bash
HDR="Authorization: Basic $(printf '%s:' '<GTMETRIX_API_KEY>' | base64)"
claude mcp add --scope local --transport http gtmetrix https://gtmetrix.com/mcp --header "$HDR"
claude mcp list   # expect: gtmetrix ... ✔ Connected
```
> A **session restart** is required after adding before the `mcp__gtmetrix__*`
> tools appear in-session.

### Google Search Console MCP — configuration

Local stdio MCP server. **Not committed** — registered at **local scope** (in
`~/.claude.json`, per-user), and its entire runtime lives in the gitignored
`.mcp-local/` folder: launcher `.mcp-local/gsc-mcp.sh` + the vendored
`.mcp-local/gsc_mcp/` Python package, run in place via `uv run`. Deps are pulled
on demand (`mcp<2`, `google-auth`, `requests`, `jinja2`). Copied 2026-07-29 from
the `CLBD-Marketing` workspace.

> **`mcp<2` is deliberate:** `gsc_mcp/server.py` imports `mcp.server.fastmcp`,
> which mcp 2.0 removed. The launcher pins `mcp<2` so FastMCP stays where the
> vendored package expects it.

Auth uses the shared **`carlifebydani` service account**
(`.credentials/google-service-account.json`, gitignored) — verified to have
`siteFullUser` read access to the property. **Property is the www URL-prefix
`https://www.carlifebydani.com/`** (note: not the bare apex used by PSI/GTmetrix).
No OAuth consent screen, no token expiry — access is governed purely by the SA
email being a user on the Search Console property.

To re-register on another machine (after copying `.mcp-local/` + the credential):
```bash
claude mcp add --scope local google-search-console \
  "$(pwd)/.mcp-local/gsc-mcp.sh"
claude mcp list   # expect: google-search-console ... ✔ Connected
```
> Requires `uv` on PATH and a **session restart** before `mcp__google-search-console__*`
> tools appear. Tools: `gsc_sites`, `gsc_site_details`, `gsc_query`,
> `gsc_performance_overview`, `gsc_indexing_issues`, `gsc_inspect_url`,
> `gsc_sitemaps`, `gsc_audit`.

---

## Comparison schema (keep identical across snapshots)

- **PageSpeed field data (mobile, then desktop):** `Assessment | LCP | INP | CLS |
  FCP | TTFB` — each value + rating + % distribution.
- **PageSpeed lab (mobile, then desktop) — if captured:** `Performance |
  Accessibility | Best-Practices | SEO | TBT | Speed Index`.
- **GTmetrix:** `Score | Grade | Performance % | Structure % | LCP | TBT | CLS |
  FCP | Speed Index | TTI | Page Size | Requests | Fully Loaded`.

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

## Change log of what was shipped (context for reading the trend)

- **2026-07-10** — deferred render-blocking JS, lazy-loaded below-fold images,
  fixed font-loading chain + preconnect hints, fixed oversized mobile image
  `sizes`, added homepage H1. (Expect LCP/FCP improvement to land in field data
  through late July / August.)
- See `docs/SEO_PROPOSALS.md` for the still-pending items (title-tag fix, Review /
  VideoObject / CollectionPage schema).
