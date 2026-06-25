# EV News Automator — Implementation Plan

**Status:** Ready for implementation  
**Plugin directory:** `plugins/ev-news-automator/`  
**Brainstorm source:** `docs/brainstorms/2026-06-17-ev-news-automation-requirements.md`

---

## Context

The carlifebydani team currently curates EV news manually — editing a CSV file per podcast episode. This is slow, produces a static snapshot, and gives site visitors no live feed. This plugin automates:

1. **Daily collection** — before scraping, fetch GA4 click counts for existing articles and update the Sheet; then scrape EV news sources, translate + summarize to Bulgarian via OpenRouter, store in Google Sheets.
2. **Team curation** — team edits/deletes rows directly in Google Sheets (no separate UI).
3. **Website display** — the upcoming-session episode page carries a `news_csv` post meta pointing to the active Sheet tab's public CSV export URL, set once manually by the team when the page is created. `single.php` fetches that URL at request time and renders fresh article data — identically to past episode pages. The plugin does not update `news_csv` meta; the Sheet is always fresh because the plugin continuously appends new articles to it.
4. **Podcast script generation** — Tuesday night, generate a Google Doc with extended Bulgarian scripts per article.

The plugin must not touch the existing `news_csv` post meta path used by all existing episode posts.

**Session turnover flow (Tuesday after recording):**
1. The team publishes the recorded episode as a WP post (e.g. "EV News #155").
2. The team creates a new Sheet tab named `DD.MM.YYYY` for the upcoming session (e.g. `02.07.2026`) with the 8-column header row.
3. The team triggers "Run collection now" from the plugin dashboard.

The plugin auto-detects the newest `DD.MM.YYYY` tab as the active session. After the collection run, `ENA_Sync` writes the fresh articles to `ev_news_live_articles` in `wp_options`, and the static `/ev-news-feed/` page (WP ID 8851) immediately reflects the new session's content.

**No new WP page is created per session.** The static `/ev-news-feed/` page serves all sessions — what it shows is determined entirely by which Sheet tab is active (the most recently dated `DD.MM.YYYY` tab).

---

## Design Principle: Data Layer Abstraction

> **Google Sheets is the first backend, not the only one.**

The team plans to migrate away from Google Sheets as the article database in a future phase. To make that migration straightforward:

- **`class-ena-sheets.php` is a storage adapter, not the source of truth.** All collection, sync, and podcast code calls it through a thin interface (`read_data_rows`, `append_rows`, `delete_rows`). Those callers do not know or care what sits behind the interface.
- When migrating (e.g. to a WordPress custom post type, a MySQL table, or an external API), implement a new adapter class (`class-ena-db.php`, `class-ena-cpt.php`, etc.) with the same method signatures, update the single binding in `ENA_Plugin::__construct()`, and remove Sheets.
- **The interface contract** (used throughout the plugin):
  - `read_data_rows(): array` — returns rows as assoc arrays with keys: `title`, `description`, `link`, `author`, `upvote`, `downvote`, `clicks`, `added_date`, `session_date`. `session_date` (Y-m-d) is derived from the sheet tab name, not a real column. `clicks` is the GA4-sourced integer click count (column G). `added_date` (Y-m-d) is the calendar date the row was appended, stored in column H — used by `ENA_Sync` to distinguish new-today articles from older ones.
  - `append_rows(array $rows): bool|WP_Error` — each row is an assoc array with the same keys (except `session_date` and `added_date`). The adapter writes today's date into column H automatically. `clicks` defaults to `0`.
  - `delete_rows(array $row_indices): bool|WP_Error` — delete by storage-internal indices (adapter translates to whatever the backend needs).
  - `update_clicks(array $url_to_clicks): bool|WP_Error` — given a map of `[url => int]`, update column G for every matching row in the active sheet. Rows whose URL is not in the map are left unchanged.
  - `sort_by_clicks(): bool|WP_Error` — reorder all data rows in the active sheet by column G (clicks) descending using the Sheets v4 `sortRange` batchUpdate. Header row is preserved. Called immediately after `update_clicks()` so the public CSV export reflects engagement order before new articles are appended.
  - `existing_urls(): array` — returns `[link => true]` for dedupe.
  - `row_count(): int`
- Keep `class-ena-sheets.php` self-contained (no business logic) so it can be deleted cleanly.

---

## Weekly Rhythm

| When | What |
|---|---|
| Daily 09:00 (configurable, site local time) | WP-Cron: **fetch GA4 clicks → update Sheet column G → sort rows by clicks DESC** → scrape → summarize → append → trim |
| All week | Team curates directly in Google Sheets |
| Tuesday morning (recording day) | By 09:00 the final collection of the week runs automatically — 7 full days of articles from Wednesday through Tuesday |
| Tuesday (before recording) | **Manual**: team creates Google Doc → pastes ID in settings → clicks "Generate Podcast Script" in dashboard |

**7-day cycle:** Wednesday 09:00 (first collection after recording) → Tuesday 09:00 (last collection before recording). Visitors can read and click articles throughout the week; GA4 click data accumulates so the most-engaged articles rise to the top of the podcast script.

### Engagement Sort Order (applied during sync)

Every sync writes articles to `wp_options` in three groups, so the live page always surfaces the most relevant content first without ever deleting anything:

```
Group 1 — New today        (added_date = today):  shown first, unsorted
           → articles just scraped haven't had time to earn clicks; no penalty
Group 2 — Engaged          (added_date < today, clicks > 0):  sorted by clicks DESC
           → proven audience interest; highest-clicked rises to top
Group 3 — Zero-click older (added_date < today, clicks = 0):  shown last, unsorted
           → had at least one day to attract clicks; users weren't interested
```

A new article enters at the top of Group 1. After the next daily run updates GA4 clicks it either moves to Group 2 (any clicks → sorted by count) or sinks to Group 3 (still zero). Nothing is ever deleted by the plugin — the team manages deletions manually via the Sheet. Zero-click articles remain visible at the bottom, giving the editorial team full visibility of what exists.

---

## File Tree

```
plugins/ev-news-automator/
├── ev-news-automator.php              # Bootstrap: constants, requires, activation/deactivation hooks
├── uninstall.php                      # Remove all wp_options entries on uninstall
├── includes/
│   ├── class-ena-plugin.php           # Singleton: wires all hooks; the one place that binds the storage adapter
│   ├── class-ena-settings.php         # get/update/defaults for the ena_settings option
│   ├── class-ena-logger.php           # Ring-buffer run log (20 entries) + named status options for dashboard
│   ├── class-ena-http.php             # Safe HTTP wrapper: is_safe_url (re-impl of theme SSRF guard), get, post_json
│   ├── class-ena-google-auth.php      # Service Account JWT (RS256, openssl_sign) → access token cached in wp_options
│   ├── class-ena-sheets.php           # STORAGE ADAPTER — Google Sheets v4: read, append, delete, update_clicks, existing_urls
│   ├── class-ena-analytics.php        # GA4 Data API v1: fetch ev_news_click event counts keyed by article_url
│   ├── class-ena-docs.php             # Google Docs v1 + Drive v3: create doc, move to folder, append sections
│   ├── class-ena-openrouter.php       # OpenRouter chat completions: summarize (→ bg_title, bg_summary), podcast_script
│   ├── class-ena-scraper.php          # fetch_source (RSS / HTML fallback), extract_body (for podcast), clean_text
│   ├── class-ena-collector.php        # Phase 1 orchestrator: scrape → dedupe → summarize → store → trim
│   ├── class-ena-sync.php             # Phase 2 orchestrator: read storage → engagement-sort → write ev_news_live_articles to wp_options
│   ├── class-ena-podcast.php          # Phase 4 orchestrator: read storage → fetch bodies → scripts → Google Doc
│   ├── class-ena-cron.php             # Schedule registration, custom intervals, hook callbacks, cleanup
│   └── class-ena-ajax.php             # wp_ajax_* handlers for manual trigger buttons in admin dashboard
├── admin/
│   ├── class-ena-admin.php            # Menu pages, settings form POST handler, asset enqueue
│   └── views/
│       ├── settings-page.php          # Settings form HTML (nonce, all fields, dynamic source rows)
│       └── dashboard-page.php         # Status panel, manual trigger buttons, run log table
└── assets/
    ├── admin.css                      # Minimal admin styling
    └── admin.js                       # Dynamic source-row repeater + fetch() AJAX trigger buttons
```

---

## File-by-File Specification

### `ev-news-automator.php`

Plugin header. Defines constants: `ENA_VERSION`, `ENA_PLUGIN_FILE`, `ENA_PLUGIN_DIR`, `ENA_PLUGIN_URL`. Requires all `includes/` and `admin/` files. Registers activation/deactivation hooks, then `add_action('plugins_loaded', ['ENA_Plugin','instance'])`.

```
// Agent navigation:
// - To change the storage backend, look in ENA_Plugin::__construct() where ENA_Sheets is instantiated
// - Constants for wp_options keys are defined here to avoid string literals scattered through the code
```

Option key constants (define here, reference everywhere else):
- `ENA_OPT_SETTINGS` = `ena_settings`
- `ENA_OPT_GOOGLE_TOKEN` = `ena_google_token`
- `ENA_OPT_SHEET_META` = `ena_sheet_meta`
- `ENA_OPT_RUN_LOG` = `ena_run_log`
- `ENA_OPT_STATUS_COLLECTION` = `ena_status_last_collection`
- `ENA_OPT_STATUS_SYNC` = `ena_status_last_sync`
- `ENA_OPT_STATUS_PODCAST` = `ena_status_last_podcast`

---

### `includes/class-ena-plugin.php`

Singleton. `instance(): ENA_Plugin`. Constructor:
1. Instantiates and stores all service classes.
2. **Binds the storage adapter** — this is the one place to swap backends:
   ```php
   // STORAGE ADAPTER BINDING — swap ENA_Sheets for a different class to change the backend
   $this->storage = new ENA_Sheets( $this->auth, $this->settings );
   ```
3. Passes `$this->storage` into `ENA_Collector`, `ENA_Sync`, `ENA_Podcast`.
4. Calls `ENA_Cron::register_hooks()` and `ENA_Ajax::register()`.

---

### `includes/class-ena-settings.php`

```php
// Wraps the ena_settings option. All plugin code calls this instead of get_option() directly.
get( string $key, $default = null )
all(): array
update( array $values ): void
defaults(): array
// defaults: openrouter_model='anthropic/claude-opus-4-8', max_articles=50, max_script_articles=10,
//           collection_interval='daily', collection_time='09:00',
//           ga4_property_id='', podcast_doc_id='', sources=''
sources(): array    // parses the sources textarea into [['url'=>..., 'method'=>'rss'|'html'], ...]
service_account_path(): string
ga4_property_id(): string   // GA4 numeric property ID (e.g. '123456789'); empty string disables click sync
```

**Sources field format** — a plain `<textarea>` in the settings page, one source per line:
```
https://electrek.co/feed rss
https://insideevs.com/feed rss
https://ev-database.org html
https://thedriven.io/feed
```
Each line: URL (required) + optional method (`rss` or `html`). Method defaults to `rss` if omitted. `sources()` parses, validates with `ENA_HTTP::is_safe_url()`, and returns the normalized list. Invalid or non-HTTPS lines are skipped and flagged as admin notices. This format is easy to bulk-edit, copy-paste, and version-control.

> **Future:** automatic site health review — see [Future Considerations](#future-considerations).

---

### `includes/class-ena-logger.php`

```php
// Structured run logger. Two storage layers:
//
//   1. Ring buffer (ENA_OPT_RUN_LOG) — last 20 summary events shown in the dashboard log table.
//      Each entry: { time (ISO 8601), trigger ('cron'|'manual'), phase ('collection'|'sync'|'podcast'), level ('info'|'warning'|'error'), message, context[] }
//
//   2. Full cron transcript (ENA_OPT_CRON_TRANSCRIPT) — complete step-by-step log of the most
//      recent cron run. Overwritten on each new cron trigger. Shown in the dashboard "Last Run Detail"
//      expandable section so agents and developers can read exactly what happened during any cron run.
//      Each entry: { time, step (e.g. 'scrape_source', 'openrouter_call', 'sheets_append'), status ('ok'|'skip'|'error'), detail string }

log( string $phase, string $level, string $message, array $context = [] ): void  // appends to ring buffer
step( string $step, string $status, string $detail = '' ): void                   // appends to current cron transcript
begin_run( string $trigger, string $phase ): void  // clears transcript, records trigger='cron'|'manual' + timestamp
end_run( array $summary ): void                    // writes final summary entry to both ring buffer and transcript

all(): array                    // ring buffer entries (newest first)
transcript(): array             // current cron transcript entries
clear_log(): void
clear_transcript(): void

set_status( string $key, array $data ): void  // named status options for dashboard quick-stats
get_status( string $key ): array
```

Example transcript for a collection run:
```
[18:00:01] begin_run  trigger=cron  phase=collection
[18:00:01] scrape_source  ok       "electrek.co RSS — 12 articles found"
[18:00:01] scrape_source  ok       "insideevs.com RSS — 8 articles found"
[18:00:02] scrape_source  error    "ev-database.org HTML — connection timeout, skipped"
[18:00:02] dedupe          ok      "17 new after deduplication (3 already in sheet)"
[18:00:02] openrouter_call ok      "Electrek article 1/17 — bg_title generated"
...
[18:00:18] sheets_append   ok      "17 rows appended"
[18:00:18] sheets_trim     ok      "3 oldest rows removed (max=50)"
[18:00:19] sync             ok      "47 articles written to ev_news_live_articles"
[18:00:19] end_run          ok      "added=17 removed=3 duration=18s"
```

---

### `includes/class-ena-http.php`

```php
// Re-implementation of carlifebydani_is_safe_url() from theme/functions.php:117-134.
// Plugin must not depend on the theme being active.
static is_safe_url( string $url ): bool        // HTTPS-only, public IPv4 only, max 3 redirects
static get( string $url, array $args = [] ): array|WP_Error
static post_json( string $url, array $body, array $headers = [] ): array|WP_Error
static retrieve_json( array|WP_Error $response ): array|WP_Error
```

Google token endpoint (`accounts.google.com`) and OpenRouter (`openrouter.ai`) are allowlisted constants — they bypass `is_safe_url` since they are fixed trusted endpoints, not user-supplied URLs.

---

### `includes/class-ena-google-auth.php`

```php
// Issues Google API access tokens using a Service Account JSON (RS256 JWT, no OAuth flow).
// Token cached in wp_options with expiry check; refreshed automatically when near expiry.
get_access_token( array $scopes ): string|WP_Error
private build_jwt( array $sa, array $scopes ): string
private base64url( string $data ): string
private cached_token( string $cache_key ): ?string
private store_token( string $cache_key, string $token, int $expires_in ): void
private load_service_account(): array|WP_Error   // reads + validates the SA JSON from configured server path
```

SA file lives at the server path configured in settings (default: `.credentials/` dir already in the repo, gitignored). Must be outside webroot; `load_service_account()` rejects paths under `ABSPATH`.

Scopes required:
- `https://www.googleapis.com/auth/spreadsheets`
- `https://www.googleapis.com/auth/documents`
- `https://www.googleapis.com/auth/drive.file`
- `https://www.googleapis.com/auth/analytics.readonly`

Each API call requests only the scopes it needs (token cache is keyed per scope set). The analytics scope is only requested by `ENA_Analytics`; Sheets/Docs classes continue to request their own subset.

---

### `includes/class-ena-sheets.php` — STORAGE ADAPTER

```php
// STORAGE ADAPTER for Google Sheets v4 REST API.
// All callers (ENA_Collector, ENA_Sync, ENA_Podcast) use this through the interface contract:
//   read_data_rows(), append_rows(), delete_rows(), existing_urls(), row_count()
// To replace Google Sheets, implement a new class with those same method signatures
// and update the binding in ENA_Plugin::__construct().

read_data_rows(): array|WP_Error    // rows from the active session sheet, mapped to assoc (see columns below); injects session_date from tab name
existing_urls(): array              // [link => true] for O(1) dedupe within the active session sheet
append_rows( array $rows ): bool|WP_Error
delete_rows( array $row_indices ): bool|WP_Error   // batch deleteDimension; indices are 0-based data rows (adapter adds 1 for header offset)
update_clicks( array $url_to_clicks ): bool|WP_Error  // given [url => int], update column G for each matching row; uses batchUpdate valueInputOption=RAW
sort_by_clicks(): bool|WP_Error                       // reorder data rows by column G DESC, then column H (added_date) DESC as tiebreaker; uses sortRange batchUpdate; header row preserved
row_count(): int

// Session management — read-only; tab creation is a manual team step:
list_sheets(): array|WP_Error           // all tabs: [['title'=>'DD.MM.YYYY', 'id'=>int], ...]  cached 5 min
active_sheet_name(): string|WP_Error    // title of the most recently dated DD.MM.YYYY tab
```

**Spreadsheet structure (actual):**
- One Google Spreadsheet, one tab per podcast session.
- Tab names: `DD.MM.YYYY` format (e.g. `16.06.2026`). The date lives here, not in a column.
- "Active sheet" = the tab whose name is the most recent valid date.
- Columns per tab: `title | description | link | author | upvote | downvote | clicks | added_date`
  - `title` — Bulgarian article headline (generated by OpenRouter)
  - `description` — Bulgarian 2–3 sentence summary (generated by OpenRouter)
  - `link` — original article URL
  - `author` — source outlet name (Electrek, InsideEVs, etc.)
  - `upvote` / `downvote` — deprecated, always empty (kept for backward compatibility with existing sheets)
  - `clicks` — integer GA4 click count for this article URL; written as `0` on append, updated daily before collection
  - `added_date` — Y-m-d date the row was appended by the plugin; written once on insert, never changed

**Row assoc keys returned by `read_data_rows()`:**
`title`, `description`, `link`, `author`, `upvote`, `downvote`, `clicks`, `added_date`, `session_date` (Y-m-d, parsed from tab name)

Sheets v4 endpoints used:
- Read: `GET /v4/spreadsheets/{id}/values/{sheet}!A:H`
- Append: `POST /v4/spreadsheets/{id}/values/{sheet}!A:H:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS`
- Delete: `POST /v4/spreadsheets/{id}:batchUpdate` with `deleteDimension` requests (sorted descending to avoid index shifting)
- Update clicks: `POST /v4/spreadsheets/{id}/values:batchUpdate` with `valueInputOption=RAW` and one `ValueRange` per updated row targeting `G{row}`
- List sheets / resolve sheetId: `GET /v4/spreadsheets/{id}?fields=sheets.properties(sheetId,title)`

**Existing sheets migration:** tabs created before the clicks/added_date columns (A:F only) are handled by `read_data_rows()` — missing G treated as `clicks=0`, missing H as `added_date=session_date` (best-effort fallback). New tabs must be created manually by the team with the 8-column header row.

---

### `includes/class-ena-analytics.php`

```php
// Reads GA4 ev_news_click event counts via the Analytics Data API v1.
// The site fires a GTM/dataLayer event named 'ev_news_click' with custom parameter 'article_url'
// whenever a visitor clicks an EV news article card (theme/js/ev-news-tracking.js).
// This class translates that into a [url => click_count] map for the given date range.

fetch_clicks( array $urls, int $days_back = 7 ): array|WP_Error
// Returns [url => int]. URLs in $urls that had zero events are included with value 0.
// date range: startDate = "{days_back}daysAgo", endDate = "today"
// GA4 report: dimension=customEvent:article_url, metric=eventCount, filter: eventName=ev_news_click
// Requires $settings->ga4_property_id() to be set; returns WP_Error if empty.

private run_report( array $body ): array|WP_Error
// POST https://analyticsdata.googleapis.com/v1beta/properties/{property_id}:runReport
// Uses ENA_Google_Auth::get_access_token(['https://www.googleapis.com/auth/analytics.readonly'])
```

**GA4 report request body:**
```json
{
  "dimensions": [{ "name": "customEvent:article_url" }],
  "metrics":    [{ "name": "eventCount" }],
  "dateRanges": [{ "startDate": "7daysAgo", "endDate": "today" }],
  "dimensionFilter": {
    "filter": {
      "fieldName": "eventName",
      "stringFilter": { "matchType": "EXACT", "value": "ev_news_click" }
    }
  },
  "limit": 10000
}
```

`fetch_clicks()` sends the report, builds `[article_url => eventCount]`, then merges the passed `$urls` list so every requested URL appears in the result (default 0 for URLs not in the report). URLs not present in the GA4 response had zero events in the window — they are included with count `0`, not omitted.

> **days_back:** defaults to 7 so the daily click sync always covers the full current session week, even on the first run after a session sheet is created.

---

### `includes/class-ena-docs.php`

```php
// Google Docs v1 + Drive v3. Creates the Tuesday podcast script document.
// Not part of the storage adapter contract — Docs are output, not the article database.
create_doc( string $title ): string|WP_Error     // returns documentId
move_to_folder( string $doc_id, string $folder_id ): bool|WP_Error
append_sections( string $doc_id, array $sections ): bool|WP_Error
doc_url( string $doc_id ): string
```

Each section in `append_sections` is `['bg_title'=>, 'url'=>, 'script'=>]`. Appended via `insertText` with `endOfSegmentLocation: {}` (appends to end of body). Format: `"{bg_title}\n{url}\n\n{script}\n\n———\n\n"`.

---

### `includes/class-ena-openrouter.php`

```php
// Calls OpenRouter chat completions for two tasks: article summarization and podcast scripting.
// Model is configurable in settings (default: anthropic/claude-opus-4-8).
summarize( string $original_title, string $excerpt_or_body ): array|WP_Error  // returns ['bg_title'=>, 'bg_summary'=>]
podcast_script( string $bg_title, string $body_text ): string|WP_Error
private chat( string $system, string $user, array $opts = [] ): string|WP_Error
```

**Summarize prompt:**
- System: `"Bulgarian automotive news editor. Reply ONLY with JSON: {\"title\":\"...\",\"summary\":\"...\"}. Title = concise BG headline. Summary = 2–3 BG sentences. No markdown."`
- User: `"Original title: {title}\n\nArticle excerpt: {excerpt}\n\nProduce JSON."`
- Temperature: 0.4. On JSON parse failure → fallback to `{original_title, raw_content}`.

**Podcast prompt:**
- System: `"Scriptwriter for Bulgarian EV podcast Car Life by Dani. Spoken-style Bulgarian, 1–2 paragraphs, no markdown."`
- User: `"Заглавие: {bg_title}\n\nПълен текст:\n{body_text}\n\nНапиши разширен подкаст скрипт."`
- Body text truncated to ~6000 chars before sending.

---

### `includes/class-ena-scraper.php`

```php
// Fetches article lists from news sources (RSS-first, HTML fallback).
// Also extracts full article body text for podcast script generation.
fetch_source( array $source ): array           // dispatch to fetch_rss or fetch_html; returns [['title','url','excerpt','source'],...]
fetch_rss( string $url ): array                // wp_remote_get → DOMDocument::loadXML → iterate item/entry
fetch_html( string $url, string $base ): array // load HTML → DOMXPath → extract headline anchors
extract_body( string $url ): string|WP_Error   // fetch article page, strip scripts/styles/nav, return clean text
private clean_text( DOMDocument $dom ): string
```

All external URLs validated with `ENA_HTTP::is_safe_url()` before fetching. Source failures are caught, logged, and skipped — the rest of the run continues.

---

### `includes/class-ena-collector.php`

```php
// Phase 1 orchestrator. Drives the full daily collection pipeline.
// Depends on the storage adapter and ENA_Analytics (both injected in ENA_Plugin).
run(): array   // returns ['added'=>int, 'removed'=>int]
private trim_to_max( int $max ): int
```

**Daily cron invocation** calls `ENA_Analytics::fetch_clicks()` first (before `run()`), then `$this->storage->update_clicks()`, then `$this->storage->sort_by_clicks()` to reorder the sheet rows by engagement, then `run()`. This is orchestrated in `ENA_Cron::run_daily_collection()` (and mirrored in `ENA_Ajax::handle_run_collection()`) rather than inside `run()` so that `run()` remains reusable standalone.

Pipeline: load sources → for each: `ENA_Scraper::fetch_source` → flatten → **age filter** (drop articles where `published_at < time() - DAY_IN_SECONDS`; articles with `published_at === 0` always pass) → dedupe via `$this->storage->existing_urls()` (also dedupes within batch) → **sort by `published_at` DESC** (newest first within the batch) → for each new: `ENA_OpenRouter::summarize` → build row `[title=bg_title, description=bg_summary, link=url, author=source, upvote='', downvote='', clicks=0]` → `$this->storage->append_rows($rows)` → `trim_to_max($max)` → `ENA_Logger::set_status`.

Note: the session date is embedded in the active sheet tab name; it is not written as a column value.

---

### `includes/class-ena-sync.php`

```php
// Optional orchestrator: reads the active sheet, engagement-sorts articles, and writes a
// JSON snapshot to ev_news_live_articles in wp_options.
// Not part of the automatic daily cron — the episode page reads the Sheet CSV directly.
// Can be triggered manually from the admin dashboard if a live news page is built in future.
// Depends on the storage adapter only (no ENA_Settings needed).

run(): array   // returns ['count' => int]
```

Pipeline:
```
$today   = date('Y-m-d')
$rows    = $this->storage->read_data_rows()

// — Engagement sort —
$new_today   = array_filter($rows, fn($r) => $r['added_date'] === $today)
$with_clicks = array_filter($rows, fn($r) => $r['added_date'] < $today && (int)$r['clicks'] > 0)
$zero_clicks = array_filter($rows, fn($r) => $r['added_date'] < $today && (int)$r['clicks'] === 0)
usort($with_clicks, fn($a,$b) => (int)$b['clicks'] <=> (int)$a['clicks'])
$sorted = array_merge(array_values($new_today), array_values($with_clicks), array_values($zero_clicks))

// — Write live articles cache (future use) —
$articles = array_map(fn($r) => [...], $sorted)
update_option(ENA_OPT_LIVE_ARTICLES, wp_json_encode($articles))

log + set_status(ENA_OPT_STATUS_SYNC, ['timestamp', 'count', 'new_today', 'with_clicks', 'zero_clicks'])
```

---

### `includes/class-ena-podcast.php`

```php
// Phase 4 orchestrator. Generates the Tuesday podcast script Google Doc.
// Depends on the storage adapter via $this->storage (injected in ENA_Plugin).
run(): array   // returns ['doc_url'=>string, 'count'=>int]
```

Pipeline: `$this->storage->read_data_rows()` → for each: `ENA_Scraper::extract_body($row['link'])` → `ENA_OpenRouter::podcast_script($row['title'], $body)` → accumulate sections → `ENA_Docs::create_doc('EV News Podcast Script – '.date('Y-m-d'))` → `move_to_folder` → `append_sections` → `ENA_Logger::set_status(ENA_OPT_STATUS_PODCAST, ...)`.

---

### `includes/class-ena-cron.php`

```php
// Manages WP-Cron schedules and runs orchestrators on schedule.
static activate(): void       // called on plugin activation
static deactivate(): void     // wp_clear_scheduled_hook for ena_daily_collection
static reschedule(): void     // called after settings save; re-schedules collection at next collection_time
static register_hooks(): void // add_action for ena_daily_collection
static run_daily_collection(): void  // fetch GA4 clicks → update_clicks → sort_by_clicks → ENA_Collector::run() → ENA_Sync::run()
static add_intervals( array $schedules ): array  // adds ena_15min, ena_30min, ena_6hours
```

Hook name: `ena_daily_collection` (configurable interval; for `daily`, fires at `collection_time` in site local timezone).

Podcast script generation is **manual only** — triggered via the admin dashboard "Generate Podcast Script" button. There is no `ena_weekly_podcast` cron hook.

**`run_daily_collection()` detail:**
```php
// 1. Fetch click counts for all URLs currently in the active sheet
$rows  = $plugin->storage->read_data_rows();
$urls  = array_column($rows, 'link');
$clicks = $plugin->analytics->fetch_clicks($urls);   // WP_Error → log warning, skip update
if (!is_wp_error($clicks)) {
    $plugin->storage->update_clicks($clicks);
    // 1b. Reorder rows by clicks DESC so the public CSV export reflects engagement order
    //     before new articles are appended at the bottom.
    $plugin->storage->sort_by_clicks();              // WP_Error → logged, non-fatal
}
// 2. Standard collection
$plugin->collector->run();
$plugin->sync->run();
```

**Configurable collection interval** (`collection_interval` setting):

| Setting value | WP-Cron interval | Use case |
|---|---|---|
| `15min` | `ena_15min` (900s) | Development / rapid testing |
| `30min` | `ena_30min` (1800s) | Development |
| `1hour` | `hourly` (built-in) | Development |
| `6hours` | `ena_6hours` (21600s) | Staging |
| `12hours` | `twicedaily` (built-in) | Pre-production |
| `daily` | `daily` (built-in) | **Production default** |

`add_intervals()` registers the four custom intervals (`ena_15min`, `ena_30min`, `ena_6hours`). The interval is stored in settings. On save, `reschedule()` clears and re-adds `ena_daily_collection` with the new interval. The 18:00 start time still applies at `daily`; for shorter intervals the first run fires as soon as possible after activation/save.

> Dev workflow: set interval to `15min` to iterate quickly; flip to `daily` before deploying to production. The setting is in the admin UI so no code changes are needed.

Timestamps on scheduling use `wp_timezone()` so configured times are in the site's local timezone.

> Production note: set `DISABLE_WP_CRON = true` in `wp-config.php` and add a real server crontab hitting `wp-cron.php` for reliable timing.

---

### `includes/class-ena-ajax.php`

```php
// Registers admin-ajax handlers for the three manual trigger buttons.
// All handlers: check_ajax_referer('ena_admin','nonce') + current_user_can('manage_options').
static register(): void
static handle_run_collection(): void   // action: ena_run_collection (includes GA4 click sync before collecting)
static handle_run_sync(): void         // action: ena_run_sync (re-sorts and re-writes ev_news_live_articles)
static handle_run_podcast(): void      // action: ena_run_podcast
```

Each returns `wp_send_json_success($result)` or `wp_send_json_error($message, 403/500)`.

---

### `admin/class-ena-admin.php`

```php
// Registers the admin menu, enqueues assets on plugin pages only, and handles the settings form POST.
add_menu(): void           // add_menu_page + two add_submenu_page (Dashboard, Settings)
enqueue( string $hook ): void  // load admin.css + admin.js only on plugin pages
                               // wp_localize_script('ena-admin', 'enaAjax', ['url'=>..., 'nonce'=>...])
handle_settings_save(): void  // action: admin_post_ena_save_settings
render_settings(): void       // include views/settings-page.php
render_dashboard(): void      // include views/dashboard-page.php
```

Settings save: `check_admin_referer('ena_save_settings','ena_settings_nonce')` + `current_user_can('manage_options')` → sanitize all fields → `ENA_Settings::update()` → `ENA_Cron::reschedule()` → `wp_safe_redirect(add_query_arg('updated','1', wp_get_referer()))`.

Sanitization rules:
- API key: `sanitize_text_field`; rendered as `type="password"` with empty value (never echoed back as plaintext)
- Sources textarea: parse each non-empty line → split on whitespace → `esc_url_raw` for URL, method whitelisted to `rss`|`html` (default `rss` if omitted); invalid lines are silently dropped
- `max_articles`: `absint`
- `collection_interval`: whitelisted to allowed values — `15min`, `30min`, `1hour`, `6hours`, `12hours`, `daily`
- `collection_time`: validated `HH:MM` pattern (default `09:00`); used only when interval is `daily`
- All other text fields: `sanitize_text_field`

---

### `templates/template-live-news.php` — REMOVED

Not needed. The upcoming-session placeholder page is a standard WP page using the existing `single.php` flow. The `news_csv` post meta on that page points to the active Sheet tab's CSV export URL; `single.php` fetches and renders it via `card-article-external.php`, identical to past episode pages. `ENA_Plugin::register_page_template()` and the `theme_page_templates` / `template_include` filters are also removed.

---

### `templates/parts/card-article-live.php` — REMOVED

Not needed. The existing `theme/template-parts/single/card-article-external.php` handles rendering. The `upvote` and `downvote` Sheet columns are empty, so the vote circles display `0` — the same default the template already shows for missing values.

---

### `assets/admin.js`

- Three trigger buttons call their respective AJAX action via `fetch()` with `URLSearchParams` body, display results or errors inline without page reload.
- Shows a live spinner and elapsed time while a manual run is in progress.

---

## Option Keys Reference

| Constant | Key | Content |
|---|---|---|
| `ENA_OPT_SETTINGS` | `ena_settings` | Serialized settings array |
| `ENA_OPT_GOOGLE_TOKEN` | `ena_google_token` | Cached access tokens keyed by scope md5 hash |
| `ENA_OPT_SHEET_META` | `ena_sheet_meta` | Cached numeric Sheets sheetId |
| `ENA_OPT_RUN_LOG` | `ena_run_log` | Last 20 summary events (ring buffer) |
| `ENA_OPT_CRON_TRANSCRIPT` | `ena_cron_transcript` | Full step-by-step log of the most recent cron run |
| `ENA_OPT_STATUS_COLLECTION` | `ena_status_last_collection` | `{timestamp, added, removed}` |
| `ENA_OPT_STATUS_SYNC` | `ena_status_last_sync` | `{timestamp, count, new_today, with_clicks, zero_clicks}` |
| `ENA_OPT_STATUS_PODCAST` | `ena_status_last_podcast` | `{timestamp, doc_url, count}` |

---

## Files to Reference (do not modify)

| File | Why |
|---|---|
| [theme/functions.php:117–134](../theme/functions.php) | Copy `carlifebydani_is_safe_url()` into `class-ena-http.php` — plugin must not depend on the theme |
| [theme/template-parts/single/card-article-external.php](../theme/template-parts/single/card-article-external.php) | Renders article cards for the placeholder page — used as-is, no fork needed |
| [theme/single.php:108–142](../theme/single.php) | Must remain untouched — both past episodes and the upcoming-session placeholder use this CSV path |

---

## Prerequisites (before first run)

1. Google Cloud project with **Sheets API v4**, **Docs API v1**, and **Google Analytics Data API v1** enabled.
2. Service account with:
   - Editor access to the target Google Sheet and Drive folder.
   - **Viewer role** on the GA4 property (Google Analytics → Admin → Property → Property Access Management → add service account email as Viewer).
3. Service account credentials JSON at a non-web-accessible server path (e.g. `.credentials/ev-news-sa.json`).
4. OpenRouter account with a funded balance and API key.
5. GA4 **numeric property ID** (found in Analytics → Admin → Property Settings, e.g. `123456789`). Enter in plugin settings as `ga4_property_id`. Leave empty to disable click sync (filter will still run but won't drop zero-click articles).
6. Google Spreadsheet with at least one tab named `DD.MM.YYYY` (e.g. `16.06.2026`) and columns `title | description | link | author | upvote | downvote | clicks` in that order. Spreadsheet ID noted for plugin settings. Use "New Session" (future admin button) or create tabs manually. For existing tabs without column G, see migration note in the Sheets adapter spec above. The spreadsheet must be shared as **"Anyone with the link can view"** so the CSV export URL is accessible by `single.php` without authentication.
7. The static **EV News Feed** page at `/ev-news-feed/` (WP ID 8851) must exist in WordPress with the **EV News Feed** page template (`page-ev-news-feed.php`). This page is created once and never replaced — it serves all future sessions automatically.

---

## Verification Plan

| Phase | How to verify |
|---|---|
| Activation | `wp cron event list` shows `ena_daily_collection` scheduled at the next occurrence of `collection_time` (default ~09:00 site local time); no `ena_weekly_podcast` event |
| Settings | Save → reload → all fields persist; API key field shows empty/masked; GA4 property ID persists |
| GA4 click sync | After at least one day of article clicks on the live site, "Run collection now" → open the active sheet tab → column G shows non-zero integers for clicked articles; dashboard transcript shows `analytics_fetch ok "N URLs, M with clicks"` |
| Phase 1 — Collection | "Run collection now" → new rows appear with columns title/description/link/author/clicks=0/added_date=today; run twice → no duplicates; set max=5 → oldest rows removed; dashboard shows counts |
| Phase 2 — Sync + Engagement Sort | After a day has passed: manually set G column values to simulate clicks; "Sync now" → visit the live news page → today's new articles appear first, then previous-day articles sorted by clicks descending, then zero-click older articles at the bottom; page TTFB fast (no external calls on render) |
| Phase 3 — Podcast | "Generate podcast script now" → Google Doc created in Drive folder, one section per article; dashboard shows working Doc link |
| GA4 not configured | Leave ga4_property_id empty → "Run collection now" logs `analytics_fetch skip "ga4_property_id not set"`, sync still runs (sort treats all clicks as 0, so only new-today vs older grouping applies) |
| Backward compat | Open existing episode post with `news_csv` meta → `card-article-external.php` still renders with vote circles, unchanged |
| Security | Invalid nonce → 403; non-admin AJAX → 403; SA JSON path under webroot → plugin rejects at load time |

---

## Future Considerations

These are not in scope for the initial build but should be kept in mind so architectural choices don't block them.

### Automatic Source Health Review

Currently the team manages the sources textarea manually. At a later stage, add an automated review layer that runs separately from collection (e.g. weekly):

- **Stale source detection** — if a source has produced zero new articles for N consecutive days, flag it in the dashboard ("No new articles in 14 days") so the team can decide to remove it.
- **High-volume source throttling** — if a single source dominates the Sheet (e.g. >40% of all articles), flag it and optionally cap its contribution per run.
- **Source discovery (AI-assisted)** — optionally call OpenRouter with a prompt asking for Bulgarian EV news sites given the existing source list; present suggestions to the team rather than auto-adding.
- **Per-source article count tracking** — add a `source_stats` option key (updated each run) with `{source_url: {articles_added_total, last_article_date, runs_with_zero}}`.

This all lives in a future `class-ena-source-health.php` that reads from the logger and storage adapter. No changes to the core collection pipeline.

### Database Migration Away from Google Sheets

The storage adapter contract (`read_data_rows`, `append_rows`, `delete_rows`, `existing_urls`, `row_count`) is the migration path. When ready:

1. Implement a new adapter (e.g. `class-ena-cpt.php` using WordPress custom post types, or `class-ena-db.php` using a custom table).
2. Write a one-time migration script that reads from `ENA_Sheets` and writes to the new adapter.
3. Change the single binding in `ENA_Plugin::__construct()`.
4. Remove `class-ena-sheets.php` and the Google Sheets settings fields.

---

## Out of Scope (this phase)

- X / Twitter integration (API cost; deferred)
- Automatic WP post publishing
- Public voting or reactions
- AI-generated featured images
- Email / Slack notifications on cron runs

> **Note:** engagement-based article ordering (GA4 click counts drive the sync sort order) is now **in scope** — see [Weekly Rhythm](#weekly-rhythm), `class-ena-analytics.php`, and `class-ena-sync.php`. Articles are never deleted by the plugin; zero-click older articles sink to the bottom of the feed rather than being removed.
