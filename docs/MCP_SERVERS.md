# MCP Servers — carlifebydani

Every MCP server this project depends on: what it gives, how it authenticates,
what it costs, and how to re-register it on a new machine.

**None of these are committed.** All are registered at **local scope** (project:
`carlifebydani`) or **user scope** in `~/.claude.json`, and the three local stdio
servers keep their runtime in the gitignored `.mcp-local/`. Cloning this repo
gives you the docs, not the credentials — follow the per-server setup below.

> **A session restart is required after registering any server** before its
> `mcp__<name>__*` tools appear in-session. `claude mcp list` reporting
> ✔ Connected is *not* enough — that health-checks the process, it does not load
> the tools into a session that already started.

---

## Inventory

Status column verified **2026-08-13** (`claude mcp list` — all 9 ✔ Connected);
`youtube-rag` added **2026-08-14**.

| Server | Scope | What it's for | Auth | Cost |
|---|---|---|---|---|
| [`youtube-rag`](#youtube-rag) | **project** | Grounded quotes and summaries from the podcast transcript archive, deep-linked to the second | **None** — loopback only | Free |
| [`google-search-console`](#google-search-console) | local | Actual search outcome: clicks, impressions, CTR, position per query/page; indexing + sitemaps | Service account | Free |
| [`psi`](#pagespeed-insights-psi) | local | Lighthouse **lab** scores + metrics | Google API key | Free |
| [`gtmetrix`](#gtmetrix) | local | Grade, per-resource weight, top Lighthouse issues | HTTP Basic (API key) | **5 credits**, auto-refill |
| [`semrush`](#semrush) | user | BG keyword volume/CPC/KD, SERP, competitor + keyword gap | Remote account session | **Per returned line** |
| [`dataforseo`](#dataforseo) | local | Google Ads Keyword Planner volumes, live google.bg SERPs, OnPage crawl | Basic (user/pass) | **Per request** — $1 balance |
| [`ga4`](#google-analytics-4) | local | Landing-page engagement: which pages deserve the work | Service account | Free |
| [`wordpress`](#wordpress) | local | Read/write posts on production | App password via Keychain | Free |
| `github` | user | Repo/PR/issue operations | Token | Free |
| `claude.ai Google Drive` | managed | Drive file access | Managed OAuth | Free |

### How they divide up

- **Demand** (what do people search?) — `semrush`, `dataforseo`
- **Outcome** (what do *we* get?) — `google-search-console`, `ga4`
- **Diagnosis** (why?) — `psi`, `gtmetrix`
- **Supply** (what do we actually have to say?) — `youtube-rag`
- **Action** (change it) — `wordpress`

The two SEO skills consume them: [`seo-performance-report`](../.claude/skills/seo-performance-report/SKILL.md)
finds the pages worth optimizing (site-wide, monthly), and
[`seo-article-optimize`](../.claude/skills/seo-article-optimize/SKILL.md) optimizes
one of them (per-URL, on demand).

---

## Cost discipline

Two of these bill real money, and both are hit by the per-article skill:

- **Semrush** bills **per returned line** — a 50-row `phrase_related` pull costs
  50× the unit price of that report.
- **DataForSEO** bills **per request**, drawn from a prepaid balance. **Current
  balance: $1** (verified 2026-08-13). That is a pilot budget: enough for
  Keyword Planner volumes and SERPs across a dozen pages, not enough for a
  site-wide OnPage crawl.

**Always check [`data/seo-cache/`](../data/seo-cache/) before issuing a paid
call, and write the result back.** Most articles on this site share the same
entity vocabulary (Tesla, зареждане, BYD, обхват), so cross-run overlap is large.
See [`tools/seo_cache.py`](../tools/seo_cache.py).

---

## YouTube-RAG

Registered 2026-08-14 at **project scope** — the only server in this repo's own
[`.mcp.json`](../.mcp.json) rather than user/local config, because it is useless
outside this workspace:

```bash
claude mcp add --transport http --scope project youtube-rag http://localhost:8000/mcp
```

Served by the **`chat-api` container in `~/Projects/youtube-rag-n8n`**, which must be
running (`docker compose up -d`) for the tools to answer. Producer-side docs:
[`docs/mcp-server.md`](../../youtube-rag-n8n/docs/mcp-server.md).

**Why it matters here.** Every other server on this page tells us what the market wants
or what the site is getting. This one is the only source of **what we actually have to
say** — 99 ingested episodes, 12,669 chunks, ~14.9 M characters of original Bulgarian
commentary that exists nowhere else in text. See
[`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md) for what's built on it, and
[`SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md) for what isn't yet.

**Auth: none, by design.** The port is bound to `127.0.0.1` and `MCP_ENABLED=false` in
the producer's `docker-compose.prod.yml`. There is no credential to rotate and nothing to
tear down at the end of a push — unlike `wordpress`. Do not expose it on a server.

### Tools

`list_collections` · `capabilities` · `ask` · `search_transcripts` · `summarize_topics` ·
`trending_topics` · `summarize_episode` · `generate_chapters` · `get_transcript`

Slash commands ship with the server: `/mcp__youtube-rag__discuss_topic`,
`hottest_topics`, `episode_chapters`, `quote_hunt`.

**The routing rule that matters:** `ask` retrieves the nearest *k* chunks and can never
count. Anything phrased as *most / hottest / biggest / trending / how often* needs
`trending_topics`. An `ask` answer to a ranking question is fluent, sourced, and wrong.

### Verified 2026-08-14

Handshake and 6 of 9 tools exercised directly over the wire. Working: `list_collections`,
`capabilities`, `search_transcripts` (returns `timestamp_url` per chunk), `ask` (coherent
Bulgarian answers with sources), `generate_chapters` (~5 s for an 86-chunk episode).

**Two live defects — both producer-side, neither fixed here:**

1. **Any tool call with `date_from` / `date_to` fails with a 422.**
   [`chat-api/services/qdrant.py`](../../youtube-rag-n8n/chat-api/services/qdrant.py)
   `date_filter()` emits `{"key": "published_at", "datetime_range": {...}}`. Qdrant
   **1.18.0 has no `datetime_range` condition** — it rejects the body with
   *"At least one field condition must be specified"*. The correct shape is the ordinary
   `range` key with RFC-3339 strings against the existing `datetime` payload index;
   verified working and correctly bounded. Fix is one word: `datetime_range` → `range`
   (and the docstring claiming `range` is numeric-only is wrong for 1.18).
   Affects `trending_topics` and every dated `search_transcripts` call.
2. **`trending_topics` returns nothing useful regardless** — no chunk carries a `topics`
   payload, so the one-time backfill (`scripts/backfill_topics.py`) has not been run.
   Proposal F depends on it.

**Quality caveat for Proposal C:** `generate_chapters` runs on the free
`GENERATION_MODEL`, and the Bulgarian chapter titles come back generic ("Истории за
събития и електромобили"). Not publishable as H2s without a paid generation model or a
client-side rewrite.

---

## Google Search Console

Local stdio server: launcher `.mcp-local/gsc-mcp.sh` + the vendored
`.mcp-local/gsc_mcp/` Python package, run in place via `uv run`. Deps pulled on
demand (`mcp<2`, `google-auth`, `requests`, `jinja2`). Copied 2026-07-29 from the
`CLBD-Marketing` workspace.

> **`mcp<2` is deliberate:** `gsc_mcp/server.py` imports `mcp.server.fastmcp`,
> which mcp 2.0 removed. The launcher pins `mcp<2` so FastMCP stays where the
> vendored package expects it.

Auth uses the shared **`carlifebydani` service account**
(`.credentials/google-service-account.json`, gitignored) — verified to have
`siteFullUser` read access. **Property is the www URL-prefix
`https://www.carlifebydani.com/`** — not the bare apex used by PSI/GTmetrix. No
OAuth consent screen and no token expiry; access is governed purely by the SA
email being a user on the property.

```bash
claude mcp add --scope local google-search-console "$(pwd)/.mcp-local/gsc-mcp.sh"
claude mcp list   # expect: google-search-console ... ✔ Connected
```
Requires `uv` on PATH. Tools: `gsc_sites`, `gsc_site_details`, `gsc_query`,
`gsc_performance_overview`, `gsc_indexing_issues`, `gsc_inspect_url`,
`gsc_sitemaps`, `gsc_audit`.

> `gsc_query` caps at 250 rows per pull and returns rows **ordered by clicks
> descending**, so pages with impressions but zero clicks fall off the end. Page
> inventories built from one unfiltered call are not exhaustive — this bit the
> 2026-08-13 baseline in [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md).

## PageSpeed Insights (PSI)

Local stdio server: launcher `.mcp-local/psi-mcp.sh` + single-file
`.mcp-local/psi_mcp.py`, run via `uv run` (deps `mcp<2` + `requests`). Mirrors the
GSC pattern; `mcp<2` pinned for the same FastMCP reason. Built 2026-07-29.

One tool, **`psi_lighthouse(url, strategy)`** → `scores.{performance,
accessibility, best_practices, seo}` (0–100) + `lab_metrics.{fcp, lcp, tbt, cls,
speed_index}`. It wraps the PSI v5 API (Lighthouse in Google's cloud) — a single
stateless GET, which is why a shell/curl call is an equally valid fallback.

Auth = a free Google API key in `.credentials/pagespeed-api-key` (gitignored;
`#`-comments and blank lines ignored, first real line = key) or
`$PAGESPEED_API_KEY`. Without a key the tool returns `{"error":"PSI API 429"}`
(shared keyless quota). New key: Google Cloud Console → enable **PageSpeed
Insights API** → API key.

```bash
claude mcp add --scope local psi "$(pwd)/.mcp-local/psi-mcp.sh"
```

> **Field data does not come from this server.** CrUX field metrics are read from
> the `runPagespeed` v5 REST response (`.loadingExperience`). Do **not** WebFetch
> a `pagespeed.web.dev/analysis/<id>` URL — it serves the *stored* analysis for
> that id, so reusing a prior snapshot's id returns stale data (hit 2026-08-07).

## GTmetrix

Remote HTTP server `https://gtmetrix.com/mcp`. Auth = HTTP Basic with the account
API key (`Authorization: Basic base64(APIKEY:)`), stored only in `~/.claude.json`.
Server identifies as `groupone-gtmetrix`.

Account is GTmetrix **Basic = 5 API credits** (auto-refill to 5). Each new test
spends a credit; reading an existing report id is free — the monthly skill runs
**one deliberate test per snapshot**.

```bash
HDR="Authorization: Basic $(printf '%s:' '<GTMETRIX_API_KEY>' | base64)"
claude mcp add --scope local --transport http gtmetrix https://gtmetrix.com/mcp --header "$HDR"
```

## Semrush

Remote HTTP server `https://mcp.semrush.com/v1/mcp`, registered at **user scope**
— it is available in every project on this machine, not just this one. No
credential is stored in `~/.claude.json`; authorization is held by the remote
server against the Semrush account.

```bash
claude mcp add --scope user --transport http semrush https://mcp.semrush.com/v1/mcp
```

**Workflow is always three calls:** a discovery tool (`keyword_research`,
`organic_research`, …) → `get_report_schema(report=...)` → `execute_report(...)`.
Discovery and schema are free; only `execute_report` bills.

**Database is `bg`** (desktop — there is no `mobile-bg`). Coverage is real, not
empty: verified 2026-08-13, `зарядни станции` 1,000/mo, `електрически автомобили`
880/mo, `тесла цена` 720/mo. Long-tail Bulgarian phrases still come back thin —
that is what DataForSEO is for.

Reports used so far: `phrase_these` (batch keyword metrics), `phrase_this`,
`phrase_related`, `phrase_questions`, `phrase_kdi`, `phrase_organic` (SERP),
`domain_organic_organic` (competitor discovery), `domain_organic` (keyword
footprint), `domain_domains` (keyword gap). See
[`reports/competitor-gap/`](../reports/competitor-gap/).

## DataForSEO

Local stdio server via `npx dataforseo-mcp-server`. Auth = Basic with the account
username/password in the server's env block in `~/.claude.json`.

```bash
claude mcp add dataforseo \
  --env DATAFORSEO_USERNAME=<user> --env DATAFORSEO_PASSWORD=<pass> \
  -- npx -y dataforseo-mcp-server --mode stdio
```

Added 2026-08-13 to cover two gaps nothing else fills:

1. **Google Ads Keyword Planner volumes** — Google's own numbers for
   `location_name: Bulgaria`, `language_code: bg`, rather than Semrush's modeled
   estimates. Raw Keyword Planner returns bucketed ranges (100–1K) for accounts
   without ad spend; DataForSEO resolves these to point estimates.
2. **Live google.bg SERPs** including People-Also-Ask, featured snippets and
   related searches. The built-in WebSearch tool is **US-only** and cannot verify
   a Bulgarian SERP at all; Semrush's `phrase_organic` is index-based, not live.

Also exposes an **OnPage** API for crawling our own pages.

**Account status:** `trepechov@gmail.com`, activated 2026-08-13. It returned
`40104` (not activated) earlier that day and now returns `20000 Ok` — activation
is not instant. **Balance $1.** Probe with the free
`/v3/appendix/user_data` endpoint before planning a run around it.

## Google Analytics 4

Local stdio server via `pipx run --spec google-analytics-mcp ga4-mcp-server`.

```bash
claude mcp add ga4 --scope local \
  --env GOOGLE_APPLICATION_CREDENTIALS="$(pwd)/.credentials/ga-service-account.json" \
  --env GA4_PROPERTY_ID=427729375 \
  -- pipx run --spec google-analytics-mcp ga4-mcp-server
```

> **Both env vars are required.** The server starts without `GA4_PROPERTY_ID` but
> has no property to query — this was the original mis-registration on 2026-08-13.

Auth = the `ga4-service-account@carlifebydani.iam.gserviceaccount.com` service
account (`.credentials/ga-service-account.json`, gitignored), which must be added
as a user on the GA4 property. **Property `427729375`** — verified 2026-08-13
returning 2,763 sessions over 28 days.

**Why it's here:** GSC tells you impressions and position; GA4 tells you what
happens *after* the click. It is the filter that separates "page with 2,000
impressions worth optimizing" from "page nobody reads once they land."

## WordPress

Local stdio server through the wrapper `.mcp-local/wp-mcp.sh`, which vendors
`server-wp-mcp@1.0.1` under `.mcp-local/wp_mcp/`. Site alias: **`carlifebydani`**
(passed as the `site` param on every tool call).

**Vendored, not `npx`'d, for two reasons:** `server-wp-mcp` declares no `bin` in
its `package.json`, so `npx server-wp-mcp` fails with "could not determine
executable to run"; and `npx` would re-resolve the package on every spawn, whereas
a pinned local install means the reviewed bytes are the bytes that run. The
package was last published 2024-12-24 and lists no repository — the tarball is the
only source of truth, so it was reviewed in full on 2026-08-13 (166 lines; no
telemetry, no `eval`, no disk writes, no egress beyond the configured site).
**Re-review on any version bump.**

### Credential handling

The writer is a **dedicated `seo-bot` user with the Editor role** (id 28), not an
admin. Application passwords inherit the user's full role — there is no
per-capability scoping — so an app password on an admin account *is* an admin
credential. Editor can edit posts and cannot touch plugins, themes, users, or
settings.

The password lives in the **macOS Keychain** (service `carlifebydani-wp-mcp`) and
is pulled at spawn time, so it is never in a file, never in git, and never in a
Claude Code transcript. `server-wp-mcp` only reads credentials from a plaintext
`WP_SITES_PATH` JSON file, so the wrapper writes one to a `0700` temp dir, and
shreds it 15 seconds after startup with an `EXIT` trap as backstop.

```bash
security add-generic-password -a "$USER" -s carlifebydani-wp-mcp -w   # prompts, no echo
claude mcp add --scope local wordpress "$(pwd)/.mcp-local/wp-mcp.sh"
```

Wordfence is active on this site (`wordfence/v1` + `wordfence-login-security/v1`
are live in `wp-json`). It can disable application passwords outright, or block
them when 2FA is enforced — check **Wordfence → Login Security → Settings** first,
or registration fails with a 401 that is not a config error.

**Teardown when the SEO push is done** — a standing write credential to production
is not worth the convenience:
```bash
claude mcp remove wordpress
security delete-generic-password -a "$USER" -s carlifebydani-wp-mcp
```
then revoke `claude-mcp` under Users → Profile on the site.

### Known limitation — Yoast fields are not REST-writable

**Yoast's SEO fields are not exposed over REST on this site.** Verified 2026-08-13
against post 9248: the `meta` object contains only `footnotes` — no
`_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, or `_yoast_wpseo_focuskw`. What
*is* exposed is `yoast_head_json`, which is Yoast's rendered output and read-only.

So this server can write post content, title and excerpt, but **not the Yoast
fields the P0 items actually need** (missing meta descriptions, thin content).
Closing that gap requires an mu-plugin calling `register_post_meta()` with
`show_in_rest => true` for those keys — which also makes them reachable by
anything else with REST access, so it is a real (if small) production surface
change.

> **WordPress revisions do not cover postmeta.** If a meta description is
> overwritten, revision history will not restore it. Existing Yoast values are
> exported to [`reports/yoast-meta-backup/`](../reports/yoast-meta-backup/) as
> `<post-id>-<YYYY-MM-DD>.csv` before any write — that CSV is the only way back.

---

## Related

- **Monitoring methodology and decision rules:** [`docs/seo-performance/README.md`](seo-performance/README.md)
- **Root-cause diagnosis:** [`docs/SEO_EV_NEWS_PROPOSALS.md`](SEO_EV_NEWS_PROPOSALS.md)
- **EV News content method (built):** [`docs/EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md)
- **Transcript-archive proposals (not yet built):** [`docs/SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md)
- **Action backlog:** [`docs/SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md)
- **Paid-data cache:** [`data/seo-cache/README.md`](../data/seo-cache/README.md)
