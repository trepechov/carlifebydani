# EV News Automator — WordPress Best Practices Audit

Audit date: 2026-06-21

---

## Data storage overview

The plugin uses **no custom database tables**. All data is stored in the standard WordPress `wp_options` table.

| `wp_options` key | What's stored |
|---|---|
| `ena_settings` | All plugin config (API keys, schedule, sources) |
| `ev_news_live_articles` | JSON snapshot of all articles (written by `ENA_Sync`) |
| `ena_google_token` | Google OAuth2 token |
| `ena_sheet_meta` | Google Sheets metadata cache |
| `ena_run_log` | Last 20 run log entries |
| `ena_cron_transcript` | Step-by-step transcript of the current/last run |
| `ena_status_last_collection` | Timestamp + stats of last collection run |
| `ena_status_last_sync` | Timestamp + stats of last sync run |
| `ena_status_last_podcast` | Timestamp + stats of last podcast run |

Primary article storage is **Google Sheets** via the Sheets API. `ev_news_live_articles` is a local JSON snapshot written by `ENA_Sync` so the frontend doesn't need to call the API on every request.

The activation hook (`register_activation_hook` → `ENA_Cron::activate()`) creates no `wp_options` rows — all options are created lazily the first time `update_option()` is called.

---

## What is already correct

- `ABSPATH` guard in every file
- `register_deactivation_hook` + `uninstall.php` (correct approach over `register_uninstall_hook`)
- `uninstall.php` cleans up all 9 option keys
- Nonce on every AJAX handler (`check_ajax_referer`)
- `manage_options` capability check on all admin actions and AJAX handlers
- `check_admin_referer` on the settings form POST
- All user input sanitized (`sanitize_text_field`, `absint`, `sanitize_textarea_field`, allowlisted intervals)
- Assets enqueued conditionally — only on plugin admin pages
- `wp_safe_redirect` after settings form save
- All option key constants defined centrally in the main plugin file

---

## Issues to fix

### 1. Plugin file header is incomplete (High)

`ev-news-automator.php` is missing several standard header fields. `Requires PHP` is the most important — the plugin uses PHP 8 syntax (nullable return types, arrow functions) and will throw a fatal error on PHP 7 without it.

```php
Plugin URI: https://carlifebydani.com
Author URI: https://carlifebydani.com
Requires at least: 6.0
Requires PHP: 8.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
```

---

### 2. Large options are autoloaded unnecessarily (Medium)

WordPress autoloads all options on every page load by default. Three options will grow to non-trivial sizes:

- `ev_news_live_articles` — full JSON of up to 50 articles
- `ena_run_log` — 20 serialized log entries
- `ena_cron_transcript` — step transcript array

Fix: pass `false` as the third argument to every `update_option()` call for these three keys. `ena_settings`, token, and status options are small and fine to autoload.

```php
// ENA_Sync::run()
update_option( ENA_OPT_LIVE_ARTICLES, wp_json_encode( $articles ), false );

// ENA_Logger::log()
update_option( ENA_OPT_RUN_LOG, $log, false );

// ENA_Logger::step()
update_option( ENA_OPT_CRON_TRANSCRIPT, $transcript, false );
```

Files to update: `includes/class-ena-logger.php`, `includes/class-ena-sync.php`.

---

### 3. Dead `add_filter` call in `ENA_Cron::activate()` (Low)

`ENA_Cron::activate()` calls `add_filter('cron_schedules', ...)` but this has no effect during activation — the activation request never fires `cron_schedules`. The same filter is correctly registered in `register_hooks()`, which runs on every normal page load.

Remove the `add_filter` line from `activate()`. File: `includes/class-ena-cron.php`.

---

### 4. No plugin version stored on activation (Low/Optional)

There is no `ena_version` option written on activation. If a future version needs a data migration (e.g. restructuring `ena_settings` keys), there is no stored version to compare against.

Add to `ENA_Cron::activate()`:

```php
update_option( 'ena_version', ENA_VERSION );
```

And add `'ena_version'` to the cleanup list in `uninstall.php`.
