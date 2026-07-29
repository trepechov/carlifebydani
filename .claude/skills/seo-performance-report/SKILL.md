---
name: seo-performance-report
description: Generate the monthly SEO performance snapshot for carlifebydani.com — pulls Core Web Vitals field data (PageSpeed Insights), lab scores, and GTmetrix metrics, writes a dated report to reports/seo-performance/, and compares it against the previous snapshot. Use when the user asks to "generate the SEO report", "run the monthly SEO snapshot", "capture Core Web Vitals", or compare SEO performance month-over-month.
---

# SEO Performance Report Generator

Produce one consistent monthly snapshot of Core Web Vitals + page-speed data for
`carlifebydani.com`, save it as a dated report, and compare it against the prior
month. **Angle: off-site/technical SEO — maximize Google search ranking.**

Google ranks on **CrUX real-user field data** (real Core Web Vitals), not the
Lighthouse lab score or GTmetrix grade — so **field data is the primary signal;
lab + GTmetrix are secondary "why" diagnostics.**

- **Reports live in:** `reports/seo-performance/YYYY-MM-DD-snapshot.md` (committed).
- **Methodology / decision rules / change log:** `docs/seo-performance/README.md`.
- Always compare a new snapshot against the previous one over the same metric
  schema and the same 28-day CrUX window.

---

## Procedure

### Step 0 — Locate the previous snapshot
List `reports/seo-performance/` and read the most recent `*-snapshot.md`. You will
diff against it at the end. If none exists, this run is the baseline.

### Step 1 — PageSpeed Insights field data (PRIMARY) — via WebFetch
The `pagespeed.web.dev` analysis page exposes the **CrUX real-user field data** in
fetchable HTML — the ranking-relevant signal, reads reliably. If the user gave a
fresh analysis URL, use it; otherwise use
`https://pagespeed.web.dev/analysis/https-carlifebydani-com/<id>`.

Run **WebFetch on each form factor** (mobile and desktop are separate — note the
`?form_factor=` param):

```
WebFetch(url = "<pagespeed analysis URL>?form_factor=mobile",
  prompt = "Extract from this PageSpeed Insights report: (1) Core Web Vitals
  Assessment Passed/Failed; (2) the 28-day real-user FIELD DATA for LCP, INP, CLS,
  FCP, TTFB — each with value, Good/Needs-Improvement/Poor rating, and % Good/NI/
  Poor distribution; (3) the exact 28-day date range shown; (4) whether it is
  URL-level or origin-level (falls back to origin) data. Report exact numbers.")
```
Repeat with `?form_factor=desktop`. Record: Assessment result, all five metrics +
distributions, the date range, and origin-vs-URL scope.

### Step 2 — PageSpeed Insights lab scores (SECONDARY, optional) — needs API key
Lab scores (Performance/Accessibility/Best-Practices/SEO 0–100, TBT, Speed Index)
render client-side and do **not** come through WebFetch. Capture via the PSI API
(needs a free Google API key; keyless calls hit a shared quota and 429). If no key
is available, leave lab blank — do not block the snapshot or guess.

```bash
KEY=<PAGESPEED_API_KEY>
for STRAT in mobile desktop; do
  curl -s "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://carlifebydani.com/&strategy=$STRAT&category=performance&category=accessibility&category=best-practices&category=seo&key=$KEY" \
    -o "psi_$STRAT.json" --max-time 120
done
```
Extract: `.lighthouseResult.categories.{performance,seo,accessibility,best-practices}.score` (×100);
`.lighthouseResult.audits.{"largest-contentful-paint","total-blocking-time","cumulative-layout-shift","speed-index","first-contentful-paint"}.displayValue`;
field data under `.loadingExperience.metrics` / `.originLoadingExperience.metrics`.

### Step 3 — GTmetrix — via GTmetrix MCP (live, confirmed 2026-07-29)
The GTmetrix report page is Cloudflare-protected (403 to WebFetch/curl); use the
**GTmetrix MCP server** — the native, reusable path. Tools appear as
`mcp__gtmetrix__*` (a session restart is required after first configuring the
server before they show up in-session).

Flow:
1. `mcp__gtmetrix__gtmetrix_get_account_status` — confirm connectivity and check
   **credits remaining** before spending one.
2. `mcp__gtmetrix__gtmetrix_start_test` with `url = "https://carlifebydani.com"`.
   Note the returned `test_id`. **This spends 1 credit.**
3. `mcp__gtmetrix__gtmetrix_get_test` with the `test_id` and `wait_seconds = 25`
   (long-polls server-side). If it returns "still running", re-issue immediately —
   no shell sleep. When `state = completed`, take the `report_id`.
4. `mcp__gtmetrix__gtmetrix_get_report` with the `report_id`.

Record: GTmetrix score + Grade, Performance %, Structure %, LCP, TBT, CLS, FCP,
Speed Index, TTI, Total Page Size, Requests, Fully Loaded time, TTFB, the
Resource Summary (per-type request count + transferred bytes — JS and fonts are
the usual offenders), the top Lighthouse issues, and the test
location/browser/date + `report_url`.

> ⚠️ **Credits.** Account is GTmetrix **Basic = 5 credits** (auto-refill to 5).
> Each new test spends one; reading an existing report id is free. Run **one
> deliberate test per monthly snapshot** — don't burn credits on reruns. If the
> user supplies a `report_id`, skip straight to `get_report`.

**Raw REST fallback** (same API key, if MCP is unavailable):
```bash
curl -s --user "<GTMETRIX_API_KEY>:" "https://gtmetrix.com/api/2.0/tests/<TEST_ID>" | jq .data.attributes
```

### Step 4 — Google Search Console — search-outcome data (via GSC MCP)
This is the **ranking-outcome** signal — the actual search performance PSI and
GTmetrix can only predict. Tools appear as `mcp__google-search-console__*` (a
session restart is required after first configuring the server).

> **Property:** the GSC property is the **www** URL-prefix
> `https://www.carlifebydani.com/` — not `https://carlifebydani.com/`. Pass that
> exact `site_url`. (Confirm with `gsc_sites` if unsure.) Auth is the shared
> `carlifebydani` service account (`.credentials/google-service-account.json`),
> read-only; no credits, no quota concerns for normal use.

Use a **28-day window** ending yesterday, to line up with the CrUX window. Call:
1. `gsc_performance_overview(site_url, date_from, date_to)` — headline clicks,
   impressions, CTR, average position for the period.
2. `gsc_query(...)` for the **top queries** and **top pages** (clicks,
   impressions, CTR, position) — the money view for tracking movement.
3. `gsc_sitemaps(site_url)` — submitted/indexed counts and any errors.
4. `gsc_indexing_issues(site_url, [key pages])` / `gsc_inspect_url(...)` — spot
   coverage problems, especially on the EV-news feed pages (thin content / lang
   misconfig are known risks — see `docs/SEO_EV_NEWS_PROPOSALS.md`).

Alternatively `gsc_audit(...)` generates a full HTML audit in one call — use it
when the user wants the standalone report artifact rather than table rows.

Record: the overview totals, top ~10 queries and top ~10 pages, sitemap/indexing
status, and the exact date range.

### Step 5 — Write the report
Create `reports/seo-performance/<today>-snapshot.md` (date = today, `YYYY-MM-DD`)
using the template below. Fill every section you captured; for any source you
could not capture, leave the table blank with a one-line reason — never guess.

### Step 6 — Historical comparison
Read the previous snapshot and append a **"Compared to <prev date>"** section to
the new report: for each field-data metric (mobile + desktop) **and each Search
Console headline metric** (clicks, impressions, CTR, avg position) show prev → new
and the delta; flag any field metric that crossed a Good/NI/Poor threshold or
moved >10%, and any material swing in search clicks/impressions/position; then
relate movements to deploy dates in the change log
(`docs/seo-performance/README.md`). Field data lags deploys by up to ~28 days
(rolling window), so a mid-month ship only fully lands in the following snapshot.
Then give the user a short verbal read: what improved, what regressed, what to
watch, and any recommended action.

---

## Report template

```markdown
# SEO Performance Snapshot — <YYYY-MM-DD>

**Captured:** <date> · **Method:** PageSpeed WebFetch (field) + GTmetrix MCP + PSI API (lab) + Search Console MCP (search outcome)

Source URLs used this run:
- PageSpeed: `<analysis URL>` (`?form_factor=mobile` / `=desktop`)
- GTmetrix: `<report_url>` (report id `<id>`)
- Search Console property: `https://www.carlifebydani.com/` · window `<date_from>`–`<date_to>`

## PageSpeed Insights — CrUX real-user field data
**CrUX 28-day window: <range>.** Data scope: <origin-level | URL-level>.

### Mobile — Core Web Vitals Assessment: <PASSED | FAILED>
| Metric | Value | Rating | Distribution (Good / NI / Poor) |
|--------|-------|--------|----------------------------------|
| LCP | | | |
| INP | | | |
| CLS | | | |
| FCP | | | |
| TTFB | | | |

### Desktop — Core Web Vitals Assessment: <PASSED | FAILED>
(same table shape)

## PageSpeed Insights — Lighthouse lab scores
| | Performance | Accessibility | Best-Practices | SEO | TBT | Speed Index |
|---|---|---|---|---|---|---|
| Mobile | | | | | | |
| Desktop | | | | | | |

## GTmetrix
| Score | Grade | Performance % | Structure % | LCP | TBT | CLS | FCP | Speed Index | TTI | Page Size | Requests | Fully Loaded |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| | | | | | | | | | | | | |

Test config (location / browser / connection / date): <…>
Resource summary + top Lighthouse issues: <…>

## Search Console — search performance (28-day, <date_from>–<date_to>)
**Overview:** Clicks <…> · Impressions <…> · CTR <…>% · Avg position <…>

**Top queries:**
| Query | Clicks | Impressions | CTR | Position |
|---|---|---|---|---|

**Top pages:**
| Page | Clicks | Impressions | CTR | Position |
|---|---|---|---|---|

**Indexing / sitemaps:** <submitted / indexed counts, coverage issues, EV-news feed status>

## Compared to <prev date>
<per-metric deltas, threshold crossings, links to deploys, watch-items>
```

---

## Decision rules (what the numbers mean for SEO)
- **Core Web Vitals thresholds (Google ranking):** LCP ≤2.5s good / ≤4s NI / >4s
  poor · INP ≤200ms / ≤500ms / >500ms · CLS ≤0.1 / ≤0.25 / >0.25. The page
  "passes" only when **all three (LCP, INP, CLS) are Good at the 75th percentile.**
- Field data moving the wrong way = investigate before it costs ranking; lab +
  GTmetrix diagnostics tell you *why* (render-blocking JS, image/font weight, TBT,
  slow TTFB).
- Comparisons are only valid across the same CrUX window and same GTmetrix
  location/connection — always record both.
