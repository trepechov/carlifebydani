# EV News Automator — Implementation Plan

**Status:** Ready for implementation  
**Plugin directory:** `plugins/ev-news-automator/`  
**Brainstorm source:** `docs/brainstorms/2026-06-17-ev-news-automation-requirements.md`

---

## Context

The carlifebydani team currently curates EV news manually — editing a CSV file per podcast episode. This is slow, produces a static snapshot, and gives site visitors no live feed. This plugin automates:

1. **Daily collection** — before scraping, fetch GA4 click counts for existing articles and update the Sheet; then scrape EV news sources, translate + summarize to Bulgarian via OpenRouter, store in Google Sheets.
2. **Team curation** — team edits/deletes rows directly in Google Sheets (no separate UI).
3. **Website display** — two surfaces:
   - **Episode page** (`single.php`): the upcoming-session episode page carries a `news_csv` post meta pointing to the active Sheet tab's public CSV export URL. The plugin does not touch this meta; the Sheet stays fresh because collection runs continuously.
   - **EV News Feed page** (`/ev-news-feed/`): a standalone public page powered by the `ev_news_live_articles` wp_option that `ENA_Sync` writes after every collection. Template `page-ev-news-feed.php` renders an Instagram-style mobile feed (70 vh snap cards) and a desktop grid with sidebar. GA4 click tracking and OG image loading reuse the existing `ev-news-tracking.js` / `ogimageloader.init.js` scripts.
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
  - `update_clicks(array $url_to_clicks): bool|WP_Error` — given a map of `[url => int]`, update column G for every matching row. Rows not in the map are left unchanged.
  - `update_upvotes(array $url_to_count): bool|WP_Error` — same contract, updates column E.
  - `update_downvotes(array $url_to_count): bool|WP_Error` — same contract, updates column F.
  - `sort_by_upvotes(): bool|WP_Error` — reorder all data rows by upvote (col E) DESC → pub_date (col I) DESC → added_date (col H) DESC using the Sheets v4 `sortRange` batchUpdate. Header row preserved. Called after every engagement sync so the Sheet always reflects engagement order.
  - `trim_to_max(int $max): int` — delete bottom rows until the data row count ≤ max. Must be called after `sort_by_upvotes()` so the bottom rows are the zero-upvote oldest articles.
  - `existing_urls(): array` — returns `[link => true]` for dedupe.
  - `row_count(): int`
- Keep `class-ena-sheets.php` self-contained (no business logic) so it can be deleted cleanly.

---

## Weekly Rhythm

| When | What |
|---|---|
| Daily 09:00 (configurable, site local time) | WP-Cron: **fetch GA4 clicks + upvotes + downvotes → update Sheet columns E/F/G → scrape → summarize → append → sort by upvote DESC → trim** → sync live articles → push notification |
| All week | Team curates directly in Google Sheets |
| Tuesday morning (recording day) | By 09:00 the final collection of the week runs automatically — 7 full days of articles from Wednesday through Tuesday |
| Tuesday (before recording) | **Manual**: team creates Google Doc → pastes ID in settings → clicks "Generate Podcast Script" in dashboard |

**7-day cycle:** Wednesday 09:00 (first collection after recording) → Tuesday 09:00 (last collection before recording). Visitors can read, click, and upvote/downvote articles throughout the week; GA4 upvote data accumulates and drives the sort (clicks are also tracked but are not the sort key), so the most-liked articles rise to the top of both the sheet and the podcast script.

### Two-Layer Ordering

The system maintains two distinct orderings that serve different purposes.

**Sheet order** — physical row order in the active tab, managed by `sort_by_upvotes()` after every collection run:

| Priority | Field | Direction |
|---|---|---|
| 1 | `upvote` | DESC — most-upvoted articles at the top |
| 2 | `pub_date` | DESC — newer articles win among equal upvote counts |
| 3 | `added_date` | DESC — tiebreaker when upvote and pub_date are equal |

This is what the Google Sheet reflects visually and what the podcast script uses. After sort, the bottom rows (zero-upvote, oldest pub_date) are trimmed to respect `max_articles`.

**Display order** — applied by `ENA_Sync` when writing `ev_news_live_articles` to `wp_options`. `ENA_Sync` does **not** trust the sheet's physical order for this — it independently re-sorts each group in memory with the same upvote → pub_date → added_date criteria, since the Sheets sort is a separate API call earlier in the pipeline and display order shouldn't depend on it having landed:

```
Group 1 — Added today (added_date === today, exact calendar-day match on UTC date —
           must stay in sync with the is_new badge cutoff in
           template-parts/ev-news-feed/card.php, or a non-badged article can end up
           in this group and outrank a badged one):
           shown first, sorted by upvote DESC → pub_date DESC → added_date DESC

Group 2 — Everything older:
           shown after Group 1, same sort
```

`added_date` (the date the row was scraped into the Sheet, stored as Y-m-d UTC) is used for group assignment.

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
│   ├── class-ena-docs.php             # Google Docs v1 + Drive v3: append sections to a user-supplied doc ID
│   ├── class-ena-openrouter.php       # OpenRouter chat completions: summarize (→ bg_title, bg_summary), podcast_summary (extended); rate-limit/usage tracking
│   ├── class-ena-scraper.php          # fetch_source (RSS / HTML fallback), extract_body (unused by current podcast flow), clean_text
│   ├── class-ena-collector.php        # Phase 1 orchestrator: scrape → dedupe → summarize → store; stops early on OpenRouter 429/401
│   ├── class-ena-sync.php             # Phase 2 orchestrator: read storage → engagement-sort → write ev_news_live_articles to wp_options
│   ├── class-ena-podcast.php          # Phase 4 orchestrator: GA4 refresh + sheet sort (shared with collection) → title/description/extended-summary → Google Doc
│   ├── class-ena-cron.php             # Schedule registration, custom intervals, run_pipeline() orchestration, refresh_analytics()/sort_sheet() (shared with podcast)
│   ├── class-ena-ajax.php             # wp_ajax_* handlers for manual trigger buttons in admin dashboard
│   ├── class-ena-background.php       # Background/async run support for long-running manual triggers
│   ├── class-ena-push.php             # Web Push notifications on new-article publish — see docs/PWA_PUSH_BADGES.md
│   └── class-ena-pwa.php              # PWA manifest/service-worker registration — see docs/PWA_PUSH_BADGES.md
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
//           ga4_property_id='', google_doc_id='', sources=''
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
log_error( string $phase, string $message, array $context = [] ): void  // convenience: writes to both ring buffer (level='error') and transcript in one call
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
delete_rows( array $row_indices ): bool|WP_Error    // batch deleteDimension; indices are 0-based data rows (adapter adds 1 for header offset)
update_clicks( array $url_to_clicks ): bool|WP_Error    // given [url => int], update column G for each matching row; uses batchUpdate valueInputOption=RAW
update_upvotes( array $url_to_count ): bool|WP_Error    // same contract, updates column E (upvote)
update_downvotes( array $url_to_count ): bool|WP_Error  // same contract, updates column F (downvote)
sort_by_upvotes(): bool|WP_Error                        // reorder Sheet rows: col E (upvote) DESC → col I (pub_date) DESC → col H (added_date) DESC; uses sortRange batchUpdate; header row preserved
trim_to_max( int $max ): int                            // delete bottom rows until row count ≤ max; must be called after sort_by_upvotes() so bottom rows are oldest zero-upvote articles
row_count(): int

// Session management — read-only; tab creation is a manual team step:
list_sheets(): array|WP_Error           // all tabs: [['title'=>'DD.MM.YYYY', 'id'=>int], ...]  cached 5 min
active_sheet_name(): string|WP_Error    // title of the most recently dated DD.MM.YYYY tab
active_sheet_url(): string|WP_Error     // full edit URL of the active tab (https://docs.google.com/spreadsheets/d/{id}/edit#gid={sheetId}); used by ENA_Sync to store the sheet link in sync status
```

**Spreadsheet structure (actual):**
- One Google Spreadsheet, one tab per podcast session.
- Tab names: `DD.MM.YYYY` format (e.g. `16.06.2026`). The date lives here, not in a column.
- "Active sheet" = the tab whose name is the most recent valid date.
- Columns per tab (9 columns, A–I): `title | description | link | author | upvote | downvote | clicks | added_date | pub_date`
  - `title` — Bulgarian article headline (generated by OpenRouter)
  - `description` — Bulgarian 2–3 sentence summary (generated by OpenRouter)
  - `link` — original article URL
  - `author` — source outlet name (Electrek, InsideEVs, etc.)
  - `upvote` — integer GA4 upvote count (`ev_news_upvote` events); written as `0` on append, synced daily
  - `downvote` — integer GA4 downvote count (`ev_news_downvote` events); written as `0` on append, synced daily
  - `clicks` — integer GA4 click count (`ev_news_click` events); written as `0` on append, synced daily
  - `added_date` — Y-m-d UTC date the row was appended; written once on insert, never changed
  - `pub_date` — Y-m-d publish date from the RSS `<pubDate>` field; used as the primary sort tiebreaker

**Row assoc keys returned by `read_data_rows()`:**
`title`, `description`, `link`, `author`, `upvote`, `downvote`, `clicks`, `added_date`, `pub_date`, `session_date` (Y-m-d, parsed from tab name)

Sheets v4 endpoints used:
- Read: `GET /v4/spreadsheets/{id}/values/{sheet}!A:I`
- Append: `POST /v4/spreadsheets/{id}/values/{sheet}!A:I:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS`
- Delete: `POST /v4/spreadsheets/{id}:batchUpdate` with `deleteDimension` requests (sorted descending to avoid index shifting)
- Update clicks: `POST /v4/spreadsheets/{id}/values:batchUpdate` with `valueInputOption=RAW` and one `ValueRange` per updated row targeting `G{row}`
- List sheets / resolve sheetId: `GET /v4/spreadsheets/{id}?fields=sheets.properties(sheetId,title)`

**Existing sheets migration:** tabs created before the clicks/added_date columns (A:F only) are handled by `read_data_rows()` — missing G treated as `clicks=0`, missing H as `added_date=session_date` (best-effort fallback). New tabs must be created manually by the team with the 8-column header row.

---

### `includes/class-ena-analytics.php`

```php
// Reads GA4 event counts via the Analytics Data API v1.
// The site fires three dataLayer events with custom parameter 'article_url':
//   'ev_news_click'    — when a visitor clicks an article card (theme/js/ev-news-tracking.js)
//   'ev_news_upvote'   — when a visitor upvotes a card (theme/js/ev-news-voting.js)
//   'ev_news_downvote' — when a visitor downvotes a card (theme/js/ev-news-voting.js)
// Each event fires at most once per direction per article per user (cookie-gated).

fetch_clicks( array $urls, int $days_back = 7 ): array|WP_Error
// Returns [url => int]. Every URL in $urls appears; missing URLs default to 0.
fetch_upvotes( array $urls, int $days_back = 7 ): array|WP_Error   // same contract for ev_news_upvote
fetch_downvotes( array $urls, int $days_back = 7 ): array|WP_Error  // same contract for ev_news_downvote

private fetch_event_counts( string $event_name, array $urls, int $days_back ): array|WP_Error
// Shared implementation: GA4 report dimension=customEvent:article_url, metric=eventCount,
// filter: eventName=$event_name. GA4 truncates article_url at 100 chars; a prefix→full-url
// index handles long URLs that get truncated in GA4 but stored in full in the Sheet.

private run_report( string $token, string $property_id, string $event_name, int $days_back ): array|WP_Error
// POST https://analyticsdata.googleapis.com/v1beta/properties/{property_id}:runReport
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
// Google Docs v1 + Drive v3. Appends podcast script sections to a user-supplied Google Doc.
// The team creates the doc manually and pastes its ID into plugin settings (google_doc_id).
// Not part of the storage adapter contract — Docs are output, not the article database.
append_sections( string $doc_id, array $sections ): bool|WP_Error
doc_url( string $doc_id ): string

// Also implemented; not called by the current podcast flow:
// create_doc( string $title ): string|WP_Error
// move_to_folder( string $doc_id, string $folder_id ): bool|WP_Error
```

Each section passed to `append_sections` is `['bg_title'=>, 'url'=>, 'description'=>, 'summary'=>]`.

The method fetches the document first to determine the current end position, then builds a single `batchUpdate` request with absolute cursor indices. Content is inserted in this order per call:

```
Episode header  — "EV News Roundup — Month Day, Year"   → HEADING_1
                — blank line

Per article (numbered):
  "{n}. {bg_title}"                                      → HEADING_2
  "Read the original article"                            → italic, blue hyperlink to {url}
  blank line
  "Описание: {description}"                              → italic (original short description, copied verbatim from the sheet)
  "Резюме: {summary}"                                     → "Резюме:" bold prefix (AI-generated extended summary, 8-10 sentences)
  "────────────────────────────────────────────────"     → gray Unicode separator
```

Private helpers used internally: `utf16_len()` (Google Docs counts characters in UTF-16 code units), `req_insert()`, `req_para_style()`, `req_text_style()`.

---

### `includes/class-ena-openrouter.php`

```php
// Calls OpenRouter chat completions for summarization and podcast scripting.
// Model is configurable in settings (default: anthropic/claude-opus-4-8).
// Token usage is persisted in the ena_openrouter_usage wp_option.
summarize( string $original_title, string $excerpt_or_body ): array|WP_Error  // returns ['bg_title'=>, 'bg_summary'=>]
podcast_summary( string $bg_title, string $description ): string|WP_Error      // used by podcast run; extended 8-10 sentence summary from Sheet title + description, no scraping
podcast_script( string $bg_title, string $body_text ): string|WP_Error         // exists; not called by current podcast flow (would need a scraped article body)
get_key_info(): array|WP_Error            // fetches live key/credit info from OpenRouter API
static get_local_stats(): array           // returns locally tracked token usage from wp_options (ena_openrouter_usage)
static reset_local_stats(): void          // resets ena_openrouter_usage in wp_options
static get_daily_request_count(): array   // {date, count} — requests made today (UTC) by this plugin, regardless of outcome; stored in ena_openrouter_daily_requests
static get_last_rate_limit(): ?array      // {remaining, limit, reset_at_utc, observed_at} snapshot from the most recent 429's X-RateLimit-* headers; stored in ena_openrouter_rate_limit; null until the first 429
private record_usage( array $usage, string $type ): void
private chat( string $system, string $user, array $opts = [], string $type = 'general' ): string|WP_Error
```

**Summarize prompt:**
- System: `"Bulgarian automotive news editor. Reply ONLY with JSON: {\"title\":\"...\",\"summary\":\"...\"}. Title = concise BG headline. Summary = 2–3 BG sentences. No markdown."`
- User: `"Original title: {title}\n\nArticle excerpt: {excerpt}\n\nProduce JSON."`
- Temperature: 0.4. On JSON parse failure → fallback to `{original_title, raw_content}`.

**Podcast summary prompt** (`podcast_summary()` — the one actually used by `ENA_Podcast::run()`):
- System: `"Пишеш разширено фактическо резюме на новинарска статия на български, което да разгъне и допълни кратко описание с повече детайли и контекст. Съдържай само фактите от статията — без упоменаване на подкаст, водещи, слушатели или епизоди. Без markdown."`
- User: `"Заглавие: {bg_title}\n\nКратко описание: {description}\n\nНапиши разширено резюме от 8-10 изречения с повече детайли и контекст, без да повтаряш описанието дословно."`
- Temperature: 0.3. Deliberately longer than the sheet's 2-3 sentence description so the Google Doc adds material instead of just restating it.

**Podcast script prompt** (`podcast_script()` — exists but unused; would need a scraped full article body):
- System: `"Scriptwriter for Bulgarian EV podcast Car Life by Dani. Spoken-style Bulgarian, 1–2 paragraphs, no markdown."`
- User: `"Заглавие: {bg_title}\n\nПълен текст:\n{body_text}\n\nНапиши разширен подкаст скрипт."`
- Body text truncated to ~6000 chars before sending.

**Rate limiting** (no retry-on-429 — added after the previous sleep-and-retry approach just re-hit the same wall repeatedly and blocked the run):
- `chat()` makes exactly one request per call; a `429`/`401` response is returned to the caller immediately, not retried.
- Every request (success or failure) increments the daily counter in `ena_openrouter_daily_requests`.
- A `429` response's `X-RateLimit-*` headers are parsed and persisted to `ena_openrouter_rate_limit`, and the upstream error message body is merged into the returned `WP_Error` message — so logs and the dashboard show OpenRouter's actual reason and quota, not a generic "Rate limited".
- The OpenRouter Account card on the dashboard (`ENA_Ajax::handle_openrouter_usage()` → `renderAccountCard()` in `assets/admin.js`) shows `get_key_info()` (live credit balance), `get_daily_request_count()`, and `get_last_rate_limit()` together.

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
// Phase 1 orchestrator. Depends on the storage adapter, ENA_Scraper, ENA_OpenRouter,
// ENA_Logger and ENA_Settings (all injected in ENA_Plugin).
run(): array   // returns ['added'=>int, 'skipped'=>int, 'skip_summary'=>string]
private static describe_error_code( string $code ): string
```

`run()` does **not** call GA4 refresh, sort, or trim itself — those are orchestrated around it by `ENA_Cron::run_pipeline()` (shared by the scheduled cron, "Run collection now", and the background worker): GA4 refresh happens *before* `run()`, sort + trim happen *after* it returns, on the full sheet (existing + newly appended rows). See the `class-ena-cron.php` section below for the exact order.

Pipeline: load sources → for each source: `ENA_Scraper::fetch_source` → **age filter, method-dependent**:
  - RSS sources: drop items where `published_at === 0 || published_at < $cutoff`, where `$cutoff = ENA_Settings::article_age_cutoff()` (previous run's timestamp minus a 1-hour buffer, or the configured Article Age Limit on the very first run).
  - HTML sources: no reliable publish dates, so instead the fetched list is simply capped to the first 5 items (`$html_cap`), assumed most-recent-first.

→ dedupe via `$this->storage->existing_urls()` (also dedupes within the batch across sources) → **sort by `published_at` DESC** (newest first within the batch) → for each new article: sleep 4s between calls (`REQUEST_DELAY_SECONDS`, a safety margin under OpenRouter's free-tier ~20 req/min cap) → `ENA_OpenRouter::summarize()` → on success, build row `[title=bg_title, description=bg_summary, link=url, author=source, upvote=0, downvote=0, clicks=0, pub_date]` → `$this->storage->append_rows($rows)` once at the end.

**Stops early on 429/401** instead of retrying: if `summarize()` fails with `http_429` (rate limited) or `http_401` (bad/expired key), the loop `break`s immediately — the remaining not-yet-attempted articles are left for the next scheduled run rather than burning the run on repeated failures against the same wall. Other error codes (parse failures, empty responses, etc.) are logged and skipped per-article, and the loop continues to the next one. `$result['skip_summary']` aggregates a human-readable count per error code (via `describe_error_code()`) plus a "N× not attempted (run stopped early)" note when applicable; the dashboard surfaces this with a link to the OpenRouter Account card.

Note: the session date is embedded in the active sheet tab name; it is not written as a column value.

---

### `includes/class-ena-sync.php`

```php
// Orchestrator: reads the active sheet and writes a JSON snapshot to ev_news_live_articles.
// Runs automatically after every collection — both the daily cron and the manual AJAX trigger.
// Depends on the storage adapter only (no ENA_Settings needed).

run(): array   // returns ['count' => int, 'published_today' => int]
```

Pipeline:
```
$today  = gmdate('Y-m-d')                              // today, UTC calendar date
$rows   = $this->storage->read_data_rows()

// — Display grouping —
// Deliberately does NOT trust the sheet's physical row order (that's a separate,
// earlier Sheets API call via sort_by_upvotes() and may not have landed reliably by
// the time this reads the rows back). Each group is independently re-sorted here.
$recent = array_filter($rows, fn($r) => ($r['added_date'] ?? '') === $today)   // added today (exact match — must
$older  = array_filter($rows, fn($r) => ($r['added_date'] ?? '') !== $today)   // stay in sync with card.php's is_new cutoff)

$by_engagement = fn($a, $b) => upvote DESC, then pub_date DESC, then added_date DESC
usort($recent, $by_engagement)
usort($older, $by_engagement)
$sorted = array_merge($recent, $older)

// — Write live articles cache —
// Each article in the JSON array has these keys:
//   id          → md5($row['link'])          — stable unique identifier
//   title       → $row['title']              — Bulgarian headline
//   link        → $row['link']               — original article URL
//   description → $row['description']        — Bulgarian 2–3 sentence summary
//   source      → $row['author']             — source outlet name
//   pub_date    → $row['pub_date']           — Y-m-d publish date from RSS
//   date        → $row['session_date']       — Y-m-d session date from the sheet tab name
//   clicks      → (int) $row['clicks']       — GA4 click count
//   upvote      → (int) $row['upvote']       — GA4 upvote count
//   downvote    → (int) $row['downvote']     — GA4 downvote count
//   added_date  → $row['added_date']         — Y-m-d date the row was first appended
$articles = array_map(fn($r) => [...], $sorted)
update_option(ENA_OPT_LIVE_ARTICLES, wp_json_encode($articles))

// published_today = count of $recent (rows where added_date === today) — also drives the push-notification badge count in ENA_Cron::run_pipeline()
log + set_status(ENA_OPT_STATUS_SYNC, ['timestamp', 'count', 'published_today', 'recent_24h' (= count of $recent), 'older', 'sheet_name', 'sheet_url'])
```

---

### `includes/class-ena-podcast.php`

```php
// Phase 4 orchestrator. Appends podcast summaries to a user-supplied Google Doc.
// Constructor: (ENA_Sheets, ENA_Analytics, ENA_OpenRouter, ENA_Docs, ENA_Logger, ENA_Settings)
run(): array   // returns ['doc_url'=>string, 'count'=>int]
```

Pipeline:
1. `ENA_Cron::refresh_analytics()` — refreshes GA4 clicks/upvotes/downvotes on the sheet's existing rows.
2. `ENA_Cron::sort_sheet()` — physically re-sorts the sheet: upvote DESC → pub_date DESC → added_date DESC.

   Steps 1–2 are the exact same static helpers `ENA_Cron::run_pipeline()` uses for the daily collection — reused here (rather than the script doing its own click-only in-memory sort) specifically so the generated script's article order always matches what the sheet physically shows.
3. `$this->storage->read_data_rows()` — reads the now-sorted sheet.
4. `array_slice()` to the top `max_script_articles` rows (sheet order = final order, no further sorting).
5. For each of the top N rows (sleeping `REQUEST_DELAY_SECONDS` = 2s between calls): `ENA_OpenRouter::podcast_summary($row['title'], $row['description'])` to generate an extended 8-10 sentence summary. On failure, logs the error and falls back to an empty summary (the section still gets the description).
6. Build each section as `['bg_title'=>$row['title'], 'url'=>$row['link'], 'description'=>$row['description'], 'summary'=>$generated_or_empty]` — `description` is copied verbatim from the sheet, `summary` is the newly generated extended write-up. Both get written to the doc (see `class-ena-docs.php` section above for the exact layout).
7. `ENA_Docs::append_sections($doc_id, $sections)` → `ENA_Logger::set_status(ENA_OPT_STATUS_PODCAST, ...)`.

If `sort_sheet()` fails (WP_Error), the run aborts early and returns `['doc_url'=>'', 'count'=>0]` without calling OpenRouter or touching the doc.

No body scraping — uses the Bulgarian title and description already stored in the Sheet. `podcast_script()` (which would scrape the full article body) exists in `ENA_OpenRouter` but is not called.

**Note:** The team creates the Google Doc manually before clicking "Generate Podcast Script", then pastes the doc ID into plugin settings. The plugin does not create or move docs.

---

### `includes/class-ena-cron.php`

```php
// Manages WP-Cron schedules and runs orchestrators on schedule.
static activate(): void       // called on plugin activation
static deactivate(): void     // wp_clear_scheduled_hook for ena_daily_collection
static reschedule(): void     // called after settings save; re-schedules collection at next collection_time
static register_hooks(): void // add_action for ena_daily_collection
static run_daily_collection(): void  // wraps run_pipeline() in begin_run()/end_run() logging + a top-level try/catch
static run_pipeline( ENA_Plugin $plugin ): array   // the shared pipeline itself — see detail below
static refresh_analytics( ENA_Sheets, ENA_Analytics, ENA_Logger ): void  // GA4 clicks+upvotes+downvotes → sheet columns G/E/F; shared with ENA_Podcast::run()
static sort_sheet( ENA_Sheets, ENA_Logger ): bool|WP_Error               // physical sheet sort; shared with ENA_Podcast::run()
static add_intervals( array $schedules ): array  // adds ena_15min, ena_30min, ena_6hours
```

Hook name: `ena_daily_collection` (configurable interval; for `daily`, fires at `collection_time` in site local timezone).

Podcast script generation is **manual only** — triggered via the admin dashboard "Generate Podcast Script" button. There is no `ena_weekly_podcast` cron hook.

**`run_pipeline()` detail (shared by the scheduled cron, "Run collection now", and the background worker):**
```php
// 0. Flush the 5-minute sheets-list cache, then log which tab resolved as "active" —
//    the single most useful line for diagnosing a run that targets the wrong sheet.
$plugin->storage->flush_sheets_cache();
$active_name = $plugin->storage->active_sheet_name();

// 1. Refresh clicks + upvotes + downvotes on EXISTING rows from GA4 (each fetch logged
//    independently; failures non-fatal). Shared helper — also called by ENA_Podcast::run().
self::refresh_analytics($plugin->storage, $plugin->analytics, $plugin->logger);

// 2. Collect & append newly scraped articles at the bottom.
$result = $plugin->collector->run();   // ['added', 'skipped', 'skip_summary']

// 3. Sort the FULL sheet: upvote DESC → pub_date DESC → added_date DESC.
//    Always runs, independent of whether the GA4 fetch in step 1 succeeded — a failed
//    fetch must never skip the sort, or freshly appended articles are stranded at the
//    bottom unsorted. Shared helper — also called by ENA_Podcast::run().
self::sort_sheet($plugin->storage, $plugin->logger);

// 4. Trim bottom rows to max_articles (oldest zero-upvote articles removed).
$removed = $plugin->storage->trim_to_max($max_articles);
$plugin->logger->set_status(ENA_OPT_STATUS_COLLECTION, [
    'timestamp', 'added' => $result['added'], 'removed', 'skipped' => $result['skipped'], 'skip_summary',
]);

// 5. Rebuild the live JSON snapshot for the feed page.
$sync_result = $plugin->sync->run();

// 6. Push a badge notification to subscribed PWA users if any articles were added today.
//    See docs/PWA_PUSH_BADGES.md for the push subscription/delivery details.
if (($sync_result['published_today'] ?? 0) > 0) {
    ENA_Push::send_all($sync_result['published_today']);
}

// 7. Persist this run's start time so the next run's age-cutoff (ENA_Settings::article_age_cutoff())
//    starts from here, minus a 1-hour buffer.
update_option('ena_last_collection_at', $run_started_at);
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

`add_intervals()` registers the three custom intervals (`ena_15min`, `ena_30min`, `ena_6hours`). The interval is stored in settings. On save, `reschedule()` clears and re-adds `ena_daily_collection` with the new interval, anchored to the configured `collection_time` (default `09:00`, site local timezone) via `next_collection_timestamp()` — e.g. a 6-hour interval anchored at `08:00` fires at `02:00`, `08:00`, `14:00`, `20:00`.

> Dev workflow: set interval to `15min` to iterate quickly; flip to `daily` before deploying to production. The setting is in the admin UI so no code changes are needed.

Timestamps on scheduling use `wp_timezone()` so configured times are in the site's local timezone.

> Production note: set `DISABLE_WP_CRON = true` in `wp-config.php` and add a real server crontab hitting `wp-cron.php` for reliable timing.

---

### `includes/class-ena-ajax.php`

```php
// Registers admin-ajax handlers for manual trigger buttons and OpenRouter usage.
// All handlers: check_ajax_referer('ena_admin','nonce') + current_user_can('manage_options').
static register(): void
static handle_run_collection(): void    // action: ena_run_collection — calls ENA_Cron::run_pipeline() directly (same code path as the scheduled cron)
static handle_run_sync(): void          // action: ena_run_sync (re-sorts and re-writes ev_news_live_articles)
static handle_run_podcast(): void       // action: ena_run_podcast — calls $plugin->podcast->run()
static handle_openrouter_usage(): void  // action: ena_openrouter_usage — returns {key_info, local, daily_requests, last_rate_limit}
static handle_reset_usage_stats(): void // action: ena_reset_usage_stats — calls reset_local_stats()
```

Each returns `wp_send_json_success($result)` or `wp_send_json_error($message, 403/500)`.

Also registers (not admin-gated the same way; documented in `docs/PWA_PUSH_BADGES.md`, out of scope for this doc): `ena_dispatch_job` / `ena_job_status` / `wp_ajax_nopriv_ena_bg_worker` for `class-ena-background.php`'s async job runner, and `wp_ajax(_nopriv)_ena_save_push_sub` for PWA push subscriptions.

---

### `admin/class-ena-admin.php`

```php
// Constructor: ( ENA_Settings $settings, ENA_Logger $logger )
// Registers the admin menu, enqueues assets on plugin pages only, and handles the settings form POST.
add_menu(): void           // add_menu_page + three add_submenu_page (Dashboard, Settings, Работен процес)
enqueue( string $hook ): void  // load admin.css + admin.js only on plugin pages
                               // wp_localize_script('ena-admin', 'enaAjax', ['url'=>..., 'nonce'=>...])
handle_settings_save(): void  // action: admin_post_ena_save_settings
render_settings(): void       // include views/settings-page.php
render_dashboard(): void      // include views/dashboard-page.php
render_how_it_works(): void   // include views/how-it-works-page.php (slug: ev-news-automator-plan)
```

Settings save: `check_admin_referer('ena_save_settings','ena_settings_nonce')` + `current_user_can('manage_options')` → sanitize all fields → `ENA_Settings::update()` → `ENA_Cron::reschedule()` → `wp_safe_redirect(add_query_arg('updated','1', wp_get_referer()))`.

Sanitization rules:
- API key: `sanitize_text_field`; rendered as `type="password"` with empty value (never echoed back as plaintext)
- Sources textarea: parse each non-empty line → split on whitespace → `esc_url_raw` for URL, method whitelisted to `rss`|`html` (default `rss` if omitted); invalid lines are silently dropped
- `max_articles`: `absint`
- `collection_interval`: whitelisted to allowed values — `15min`, `30min`, `1hour`, `6hours`, `12hours`, `daily`
- `collection_time`: validated `HH:MM` pattern (default `09:00`); used as the anchor slot for every interval, not just `daily` (see `next_collection_timestamp()` in the cron section above)
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
| `ENA_OPT_STATUS_COLLECTION` | `ena_status_last_collection` | `{timestamp, added, removed, skipped, skip_summary}` |
| `ENA_OPT_STATUS_SYNC` | `ena_status_last_sync` | `{timestamp, count, published_today, recent_24h, older, sheet_name, sheet_url}` |
| `ENA_OPT_STATUS_PODCAST` | `ena_status_last_podcast` | `{timestamp, doc_url, count, top_clicks}` |
| `ENA_OPT_LIVE_ARTICLES` | `ev_news_live_articles` | JSON-encoded array of all articles from the active sheet, grouped+sorted (added-today group first, then older — each group independently sorted upvote DESC → pub_date DESC → added_date DESC); each item has keys: `id` (md5 of link), `title`, `link`, `description`, `source`, `pub_date`, `date` (session date), `clicks`, `upvote`, `downvote`, `added_date`; written by `ENA_Sync` after every collection run; read by the live news page template at request time |
| *(not an `ENA_OPT_*` bootstrap constant — private consts on `ENA_OpenRouter`)* | `ena_openrouter_usage` | Accumulated token usage / call counts by type — see `class-ena-openrouter.php` section |
| | `ena_openrouter_daily_requests` | `{date, count}` — total OpenRouter requests made today (UTC), any outcome |
| | `ena_openrouter_rate_limit` | `{remaining, limit, reset_at_utc, observed_at}` snapshot from the most recent 429 |

---

## Files to Reference (do not modify)

| File | Why |
|---|---|
| [theme/functions.php:117–134](../../../theme/functions.php) | Copy `carlifebydani_is_safe_url()` into `class-ena-http.php` — plugin must not depend on the theme |
| [theme/template-parts/single/card-article-external.php](../../../theme/template-parts/single/card-article-external.php) | Renders article cards for the placeholder page — used as-is, no fork needed |
| [theme/single.php:108–142](../../../theme/single.php) | Must remain untouched — both past episodes and the upcoming-session placeholder use this CSV path |

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
| Phase 2 — Sync + Engagement Sort | After a day has passed: manually set E column (upvote) values to simulate votes; "Sync now" → visit the live news page → today's new articles appear first, then previous-day articles sorted by upvote descending, then zero-vote older articles at the bottom; page TTFB fast (no external calls on render) |
| Phase 3 — Podcast | Team creates Google Doc manually → pastes doc ID into settings → "Generate podcast script now" → sheet is re-sorted first (upvote → pub_date → added_date) → sections appended to that doc in that same order, each with title, link, an italic "Описание" line copied verbatim from the sheet, and a bold-labeled "Резюме" line with a new AI-generated 8-10 sentence summary; dashboard shows working Doc link |
| GA4 not configured | Leave ga4_property_id empty → "Run collection now" logs `analytics_fetch skip "ga4_property_id not set"`, sync still runs (sort treats all upvotes as 0, so only added-today vs older grouping applies) |
| OpenRouter 429/401 | Force a 429 or 401 from OpenRouter (e.g. invalid key, or exhaust free-tier quota) mid-collection → run stops immediately instead of retrying with sleep(); dashboard shows a single "N article(s) skipped" notice linking to the OpenRouter Account card, which shows the real upstream error message, today's request count, and the last known quota/reset snapshot |
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
- AI-generated featured images
- Email / Slack notifications on cron runs

> **Note:** upvote/downvote voting is **in scope and implemented** — see `theme/js/ev-news-voting.js`, `class-ena-analytics.php` (`fetch_upvotes` / `fetch_downvotes`), and `class-ena-sheets.php` (`update_upvotes` / `update_downvotes` / `sort_by_upvotes`). The sheet sort and feed display order both reflect upvote engagement.
