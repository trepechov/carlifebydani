---
name: seo-performance-report
description: Generate the monthly SEO performance snapshot for carlifebydani.com — pulls Core Web Vitals field data (PageSpeed Insights), Lighthouse lab scores (PSI MCP), GTmetrix metrics, and Search Console search performance; writes a dated report to reports/seo-performance/, appends the trend log, compares against the previous snapshot, verifies any seo-article-optimize changes that came due (reports/seo-optimizations/), derives on-site SEO action items, and reviews the 10 keywords tracked in Semrush's free-tier rank tracker for staleness (reports/seo-performance/tracked-keywords.csv). Use when the user asks to "generate the SEO report", "run the monthly SEO snapshot", "capture Core Web Vitals", "check if the SEO changes worked", "review tracked keywords", "check the Semrush tracker", or compare SEO performance month-over-month.
---

# SEO Performance Report Generator

**Goal: track how carlifebydani.com's SEO changes over time and turn each
snapshot into concrete on-site actions that lift Google ranking.** Every run
produces one consistent dated snapshot, appends a machine-readable trend row,
diffs against the prior snapshot, and writes an **action-items** list the next
run checks off.

Two layers, both captured every run:
- **Technical / off-site** — Core Web Vitals + page speed. Google ranks on
  **CrUX real-user field data**, not the Lighthouse lab score or GTmetrix grade,
  so **field data is the primary signal; lab + GTmetrix are secondary "why"
  diagnostics.**
- **On-site / content** — Search Console query→page performance. This is where
  the actionable ranking upside lives (striking-distance keywords, low-CTR
  pages, thin content). Turn it into a prioritized action list each run.

- **Reports live in:** `reports/seo-performance/YYYY-MM-DD-snapshot.md` (committed).
- **Trend log (machine-readable):** `reports/seo-performance/history.csv` — one
  row per snapshot; append every run so trends are greppable without diffing prose.
- **Methodology / decision rules / change log:** `docs/seo-performance/README.md`.
- Always compare a new snapshot against the previous one over the same metric
  schema and the same 28-day CrUX window.

---

## Procedure

### Step 0 — Locate the previous snapshot
List `reports/seo-performance/` and read the most recent `*-snapshot.md`. You will
diff against it at the end. If none exists, this run is the baseline.

### Step 1 — PageSpeed Insights field data (PRIMARY) — via the PSI REST API
The CrUX real-user field data is the ranking-relevant signal. Take it from the
**PSI REST API's `loadingExperience` block**, which always returns the *current*
28-day CrUX window:

```bash
KEY=$(grep -v '^#' .credentials/pagespeed-api-key | grep -v '^$' | head -1)
for STRAT in mobile desktop; do
  curl -s "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://carlifebydani.com/&strategy=$STRAT&category=performance&key=$KEY" \
    -o "$SCRATCH/psi_$STRAT.json" --max-time 180
done
```
Then read each file with `jq`. Per form factor record:
- `.loadingExperience.overall_category` — `FAST` = Core Web Vitals **PASSED**.
- `.loadingExperience.metrics.<M>.percentile` + `.category` + the three
  `.distributions[].proportion` values, for `LARGEST_CONTENTFUL_PAINT_MS`,
  `INTERACTION_TO_NEXT_PAINT`, `CUMULATIVE_LAYOUT_SHIFT_SCORE`,
  `FIRST_CONTENTFUL_PAINT_MS`, `EXPERIMENTAL_TIME_TO_FIRST_BYTE`.
- `.loadingExperience.origin_fallback` — `true` = **origin-level** data (too
  little URL-level traffic); `.loadingExperience.id` shows which origin.
- `.analysisUTCTimestamp` — the 28-day window ends on this date. The API does not
  return explicit window start/end, so record it as "28-day window ending <date>".

> ⚠️ **Do not use WebFetch on `pagespeed.web.dev/analysis/<id>` for field data.**
> That URL serves the **stored** analysis for that id, not a fresh one — reusing a
> previous snapshot's id silently returns month-old field data that looks current.
> (Hit on 2026-08-07: the July id still reported the Jun 30–Jul 27 window.) The
> REST call above is the reliable path. Percentile units: CLS is returned ×100
> (`6` → `0.06`); LCP/FCP/TTFB in ms; INP in ms.

### Step 2 — PageSpeed Insights lab scores (SECONDARY, optional) — via PSI MCP
Lab scores (Performance/Accessibility/Best-Practices/SEO 0–100, TBT, Speed Index)
render client-side and do **not** come through WebFetch. Capture via the **PSI
MCP** — tool `mcp__psi__psi_lighthouse` (local stdio server
`.mcp-local/psi-mcp.sh`, mirrors the GSC MCP). Call it once per form factor:

```
mcp__psi__psi_lighthouse(url = "https://carlifebydani.com/", strategy = "mobile")
mcp__psi__psi_lighthouse(url = "https://carlifebydani.com/", strategy = "desktop")
```
Each returns `scores.{performance,accessibility,best_practices,seo}` (0–100) and
`lab_metrics.{fcp,lcp,tbt,cls,speed_index}` (display strings) for the lab table.
Key is live and verified (2026-07-29) — the lab section should normally be filled.

> **Key.** The MCP reads `$PAGESPEED_API_KEY` or `.credentials/pagespeed-api-key`
> (gitignored; `#`-comment and blank lines ignored, first real line is the key).
> Without a key the tool returns `{"error":"PSI API 429","key_used":false}` — if
> that happens, **leave the lab section blank** (don't guess) and note the key is
> missing. New key: Google Cloud Console → enable "PageSpeed Insights API" → API
> key → paste into `.credentials/pagespeed-api-key`.
>
> **Session nuance.** `mcp__psi__psi_lighthouse` only appears after a Claude
> session restart following registration. If the native tool isn't loaded yet
> this session, call the same server via the wrapper instead — identical output:
> ```bash
> cd .mcp-local && uv run --with 'mcp<2' --with requests python -c \
>   "import psi_mcp,json;print(json.dumps(psi_mcp.psi_lighthouse('https://carlifebydani.com/','mobile')))"
> ```
> (swap `desktop` for the second call).

**Raw REST fallback** (bypasses the MCP entirely; same key):
```bash
KEY=$(grep -v '^#' .credentials/pagespeed-api-key | head -1)
for STRAT in mobile desktop; do
  curl -s "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://carlifebydani.com/&strategy=$STRAT&category=performance&category=accessibility&category=best-practices&category=seo&key=$KEY" \
    -o "psi_$STRAT.json" --max-time 120
done
```

> **Read lab in context, not in isolation.** The mobile lab run is throttled
> (emulated slow-4G, cold cache, single sample) and routinely shows an alarming
> LCP/FCP that does **not** reflect real users — always sanity-check it against
> the CrUX field LCP from Step 1 before reporting it as a problem. Lab is a
> "why/diagnostic" signal for the field data, never a ranking verdict.

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

### Step 4a — Verify optimizations that came due (read-only, no approval gate needed)

The other half of the loop this report closes: `seo-article-optimize` finds and fixes pages
(via `seo-keyphrase-research` / `ev-news-transcript-content` / `seo-article-apply`), this step
checks whether the fix actually worked. Full design:
[`docs/SEO_SKILLS_REFACTOR.md` §13.3](../../../docs/SEO_SKILLS_REFACTOR.md#133-the-verification-run--a-new-step-in-seo-performance-report).
This step is **entirely read-only** — no WordPress writes, so it needs no approval gate and is
safe in an otherwise-unattended monthly run.

1. **Select.** Read `reports/seo-optimizations/ledger.csv`. Rows where `verify_due <= today`
   **and** no `checks.csv` row exists yet for that `ledger_id` + `checkpoint` are due. If none,
   write one line in the snapshot — *"no optimizations came due this month"* — and skip to
   Step 4b.
2. **Did it actually ship?** For each due row: `gsc_inspect_url` for last-crawl time, and fetch
   the live page to compare the served `<title>`/`<meta name="description">` against what
   `changed` says was written. Google rewrites meta descriptions freely — *applied* ≠ *served*.
   If not re-crawled since `date_applied`, or the served snippet doesn't match, the verdict is
   **`not-shipped`**: record it, push `verify_due` out (+14 days), and **do not** compute a
   delta. Measuring CTR against a snippet users never saw is the easiest way to draw a false
   conclusion here.
3. **Pull the after window.** GSC page-level for `date_applied+1 … date_applied+28` (or `+56`
   for `phase=B` rows), plus the `keyphrase` row filtered the same way. Only query rows whose
   window has fully closed — `today >= win_end + 3` (GSC finalises with a lag); a row that
   hasn't closed yet stays pending, not `not-shipped`.
4. **Control for the tide.** Two controls, both required, so the verdict has a counterfactual:
   - **site-wide** — the same two windows from this run's `gsc_performance_overview` pull
     (already trended in `history.csv` — no second API call needed).
   - **cohort** — median delta across un-optimized posts in the same `category` published
     within ~6 months of the row's post. EV News episodes decay naturally, so a falling
     year-old episode is baseline behaviour, not a failure; the cohort is what tells the
     difference apart from the site-wide number alone. If the median cohort member is itself
     below the impression floor (next step), the cohort control is noise — say so in `note`
     and fall back to the site-wide control alone.
5. **Impression floor.** Below ~50 impressions in *either* window, average position is noise
   and CTR is a coin flip — verdict is **`inconclusive`**, never dressed up as `flat`.
6. **Verdict and write.** Match the metric to `changed` (`title`/`metadesc` → page CTR at
   stable position; `focuskw`/`tags` → keyphrase position; `content`/`inbound` → impressions
   and query count). **If `changed` is `alt` and/or `media_title` only, skip the metric pull
   entirely** — image alt/media title have no web-search ranking or CTR signal this ledger can
   attribute (see `reports/seo-optimizations/README.md`). Confirm the value is still live as
   written (still a "did it ship" check — a later run could have overwritten it) and write
   verdict `not-applicable`, not a forced `flat`/`inconclusive`. For every other `changed`
   value, a starting band: the change must clear the control by **≥0.5 positions** or
   **≥0.5pp CTR** to count as `improved` rather than `flat` — but until enough checks have run
   to see the cohort's real spread, treat that number as a calibration guess and say so in
   `note`, not as a settled threshold. Write:
   - one `checks.csv` row per due item (append-only — never overwrite a prior checkpoint's row);
   - a dated **Verification** section appended to the post's own report in
     `reports/seo-metatags/` (never a new file — see `_shared/report-template.md` §Verification);
   - a line in this snapshot's own **Verification roll-up** section (Step 5's template) — one
     row per post verified this run, plus a standing *"optimized to date: N posts · verdicts
     x improved / y flat / z regressed / w not-shipped / v inconclusive / u not-applicable"*
     count.

### Step 4b — Mine on-site SEO opportunities from GSC (the actionable layer)
This is where the report earns its keep. From the GSC query and page rows, pull
**two extra dimension cuts** and derive concrete on-page actions:
- `gsc_query(..., dimensions="query,page", row_limit=1000)` — which page ranks
  for which query. Needed to attribute an opportunity to a specific URL to edit.

Then classify (thresholds in Decision rules below):
1. **Striking-distance queries** — position **5–20** with **≥300 impressions**
   and non-brand intent. These are one on-page push from page-1 top-3. Action:
   improve the target page's title tag / H1 / on-page coverage for that query.
2. **Low-CTR winners** — position **≤5** but CTR well below the SERP norm for
   that rank (e.g. <3% at pos ≤5) with high impressions. Action: rewrite
   title/meta-description to earn the click; the ranking is already there.
3. **Cannibalisation** — one query whose clicks/impressions are split across
   ≥2 URLs. Action: consolidate/canonicalise to the stronger page.
4. **Thin / misconfigured content** — cross-check EV-news feed pages and known
   risks in `docs/SEO_EV_NEWS_PROPOSALS.md` / `docs/SEO_PROPOSALS.md`.
5. **Regressed or not-shipped from Step 4a** — a `regressed` verdict already has more evidence
   behind it than anything mined fresh here (a real before/after, control-adjusted); surface it
   as a top-priority action with the `backup` path from `ledger.csv` for the rollback decision.
   A `not-shipped` row repeating across two checkpoints is a finding about the *page* (crawl
   budget, noindex, a caching layer) rather than about the change — flag it as such, not as
   "redo the optimization."

Exclude brand queries (clbd, carlife by dani, carlifebydani, clbd parts) from
opportunity mining — they already convert and aren't the growth lever.

### Step 4c — Keyword tracking review (Semrush's free-tier rank tracker)
Semrush's free plan tracks 10 keywords and can't be read or written via MCP —
`reports/seo-performance/tracked-keywords.csv` is the only place that record
exists. Runs after Step 4b so it can reuse this run's opportunity list for
replacement candidates instead of a second GSC pull.

1. **Load the roster.**
   ```bash
   python3 tools/keyword_tracking.py latest
   ```
   If it prints "no tracked keywords yet", this is the very first run — ask
   the user for the 10 keywords currently live in Semrush's tracker (with a
   category each), then:
   ```bash
   python3 tools/keyword_tracking.py bootstrap <<'TSV'
   <keyword>	<category>
   TSV
   ```
   and skip straight to Step 5 — no keyword has enough history yet to flag a
   candidate. `bootstrap` also works mid-stream, not just on the very first
   run — use it any time a replacement keyword needs adding to the roster
   (e.g. right after retiring one, per step 4 below).

   Composing the tab-delimited heredocs below as a tool call, not typing them
   at a real terminal, is the one place this step is easy to get subtly
   wrong — a lost tab silently turns a whole line into one garbled keyword.
   `latest` printing the row you just wrote back out is the cheap way to
   confirm the fields landed correctly; `append`/`bootstrap` also warn to
   stderr and skip the line outright if it doesn't have the expected number
   of tab-separated fields, so a lost tab fails loud rather than quietly
   corrupting the roster.

2. **Pull each tracked keyword's own position.** Do **not** reuse Step 4's
   top-query rows for this — that pull is clicks-sorted and keeps only the
   highest-traffic rows, so a keyword that's actually gone stale (by
   definition, low-click) is exactly the one most likely to be missing from
   it. Instead issue one filtered call per tracked keyword (still free, no
   quota concern):
   ```
   gsc_query(site_url, date_from, date_to, dimensions="query",
             dimensionFilterGroups=[{"filters": [{"dimension": "query",
               "operator": "equals", "expression": "<tracked keyword>"}]}])
   ```
   Match is **exact string** (case-insensitive, whitespace-normalised) —
   near-variant phrasing (word order, spelling) won't be picked up; that's a
   known limitation, not a bug, and is documented as one.
   If the user has pasted this month's actual Semrush position for a keyword
   into the conversation, use that number instead and record it as
   `semrush_manual` — it's the more precise signal when available.

3. **Record this run's reading.**
   ```bash
   python3 tools/keyword_tracking.py append <<'TSV'
   <keyword>	<category>	<gsc|semrush_manual>	<position>	<impressions>
   TSV
   ```
   Watch stderr for two things: `CANDIDATES=...` — the keywords the script
   just flagged `candidate-for-swap` this run — and any `WARN` line
   (category drift, a non-numeric position/impressions value, or a
   duplicate row for a keyword already recorded today). A `WARN` isn't
   fatal, but surface it to the user rather than silently continuing.

4. **Pair each candidate with one replacement.** Pick a single replacement
   from this run's Step 4b striking-distance list — prefer a same-category
   opportunity when one exists — excluding anything already tracked. If
   nothing in this run's list qualifies, say so explicitly rather than
   forcing a weak pick. Once the user later confirms a suggested swap was
   actually applied in Semrush (a later run, not this one), retire the old
   keyword rather than leaving it silently accumulating no further readings:
   ```bash
   python3 tools/keyword_tracking.py retire "<old keyword>" --replacement "<new keyword>"
   ```
   then `bootstrap` the replacement. History is never deleted — a retired
   keyword's rows stay in the file with `status=retired`.

5. **Remind, don't assume.** This file has no way to detect drift from the
   live Semrush tracker (a keyword swapped there but not here, or vice
   versa). Close the step with a one-line reminder to confirm the roster
   still matches Semrush before trusting the trend.

### Step 5 — Write the report
Create `reports/seo-performance/<today>-snapshot.md` (date = today, `YYYY-MM-DD`)
using the template below. Fill every section you captured; for any source you
could not capture, leave the table blank with a one-line reason — never guess.
Include the **On-site SEO — action items** section: the prioritized list from
Step 4b, each item as a checkbox with the target URL, the query, current
position/CTR/impressions, and the specific edit.

### Step 6 — Historical comparison
Read the previous snapshot and append a **"Compared to <prev date>"** section to
the new report. Cover **all three metric families**:
- **Field data** (mobile + desktop): each metric prev → new + delta; flag any
  Good/NI/Poor threshold crossing or >10% move.
- **Lab + GTmetrix**: prev → new for lab category scores and the GTmetrix row;
  remember single-sample lab/GTmetrix variance — corroborate against field data
  before calling a real regression.
- **Search Console**: clicks, impressions, CTR, avg position prev → new + delta;
  flag material swings. **Also carry the previous run's action items forward** —
  mark each ✅ done / ↔ no change / ⬆️⬇️ moved, using the new GSC positions, so
  the report shows whether last month's actions worked.

Relate movements to deploy dates in the change log
(`docs/seo-performance/README.md`). Field data lags deploys by up to ~28 days
(rolling window), so a mid-month ship only fully lands in the following snapshot.

### Step 7 — Append the trend log
Append one row to `reports/seo-performance/history.csv` (create with the header
if missing) so trends are machine-readable across snapshots. Columns:
```
date,crux_window,m_lcp_s,m_inp_ms,m_cls,m_ttfb_s,d_lcp_s,d_inp_ms,d_cls,d_ttfb_s,cwv_pass,lab_perf_m,lab_perf_d,gt_score,gt_grade,gt_page_mb,gsc_clicks,gsc_impr,gsc_ctr,gsc_pos
```
Use the same values you put in the report. Leave a field blank (not 0) if a
source wasn't captured. Then give the user a short verbal read: what improved,
what regressed, what to watch, the **top 1–3 on-site actions** to take now,
and — when Step 4c flagged one — the keyword swap suggestion alongside them.

---

## Report template

```markdown
# SEO Performance Snapshot — <YYYY-MM-DD>

**Captured:** <date> · **Method:** PSI REST (field) + PSI MCP (lab) + GTmetrix MCP + Search Console MCP (search outcome + on-site opportunities)

Source URLs used this run:
- PageSpeed field data: PSI API v5 `runPagespeed` (`loadingExperience`), mobile + desktop, analysed `<analysisUTCTimestamp>`
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

## Optimization verification (from `reports/seo-optimizations/`)
<"No optimizations came due this month" if Step 4a's Select found nothing.>
| Post | Phase | Checkpoint | Verdict | Page metric Δ | vs. control | Note |
|---|---|---|---|---|---|---|

**Optimized to date:** <N> posts · verdicts <x improved / y flat / z regressed / w not-shipped / v inconclusive / u not-applicable>

## On-site SEO — action items (from GSC opportunity mining)
Prioritized; carried forward and re-scored each run. `[ ]` open · `[x]` done.
| # | Priority | Target page | Query | Pos | CTR | Impr | Action |
|---|---|---|---|---|---|---|---|
| 1 | | | | | | | |

## Keyword tracking review (Semrush's 10-keyword free-tier tracker)
| Keyword | Category | Months tracked | Signal | Pos | Trend | Status |
|---|---|---|---|---|---|---|

**Suggested swaps (manual — apply in Semrush):** <one line per candidate: "swap
<keyword> for <replacement>, pos <n>/<impr> impr this run" — or "no qualifying
replacement this run" — omit this subsection entirely when no keyword is
`candidate-for-swap`.>

Reminder: confirm this roster still matches what's live in Semrush's tracker —
this file has no way to detect drift on its own.

## Compared to <prev date>
**Field data:** <mobile + desktop per-metric deltas, threshold crossings>
**Lab + GTmetrix:** <deltas; note lab/GTmetrix single-sample variance vs field>
**Search Console:** <clicks / impressions / CTR / position deltas>
**Action-item follow-up:** <last run's items → ✅ done / ↔ flat / ⬆️⬇️ moved, with new positions>
<links to deploys, watch-items>
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

### On-site opportunity thresholds (Search Console)
- **Striking distance:** avg position **5–20** + **≥300 impressions**, non-brand
  → on-page optimise (title/H1/coverage) to push into top-3. Highest-leverage bucket.
- **Low-CTR winner:** position **≤5** but CTR below the rank norm (rough page-1
  norms: pos 1 ≈25–30%, pos 2–3 ≈10–15%, pos 4–5 ≈5–8%) → rewrite title/meta.
- **Cannibalisation:** one query split across ≥2 URLs → consolidate/canonicalise.
- Always exclude **brand** queries from opportunity ranking (they already convert).
- CTR/position from a 28-day window is noisy for low-impression rows; require the
  impression floor before actioning.
