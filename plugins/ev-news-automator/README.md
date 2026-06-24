# EV News Automator

WordPress plugin for **Car Life by Dani**. Collects English-language EV news from configured RSS feeds and web sources, translates and summarises each article into Bulgarian using AI, stores the results in a Google Sheet, and drafts a Bulgarian podcast script on recording day.

---

## What it does

1. **Collects** articles from RSS feeds and HTML pages on a configurable schedule (cron or manual trigger).
2. **Summarises** each new article — sends the title and excerpt to an OpenRouter AI model, receives a Bulgarian headline and 2–3 sentence summary.
3. **Stores** results in a Google Sheet (one tab per recording session). Deduplicates against existing URLs. Trims the sheet to a configurable maximum.
4. **Tracks clicks** — syncs `ev_news_click` GA4 event counts back into the sheet's clicks column on every collection run.
5. **Syncs to live** — pushes the current sheet tab's articles into a WordPress option (`ev_news_live_articles`), sorted by engagement (new-today first, then by click count). The theme reads this option to render the **EV News Feed** page (`/ev-news-feed/`) — a standalone public page with an Instagram-style mobile feed and a desktop grid layout.
6. **Generates a podcast script** on recording day — scrapes the full body of each article, passes them through the AI model, and appends the resulting Bulgarian script to a configured Google Doc.

---

## Requirements

- WordPress 6.4+, PHP 8.1+
- WP-CLI (for running smoke tests)
- **OpenRouter** account and API key
- **Google Cloud project** with these APIs enabled:
  - Google Sheets API
  - Google Drive API
  - Google Docs API
  - Google Analytics Data API
- A **Google Service Account** with a downloaded JSON key file
- A **GA4 property** with `ev_news_click` events and `article_url` registered as an event-scoped custom dimension

---

## Local development setup

This repo is a monorepo. The plugin lives at `plugins/ev-news-automator/` and is symlinked into a Local (by Flywheel) WordPress installation:

```bash
# From the Local site's wp-content directory
ln -s /path/to/repo/plugins/ev-news-automator ev-news-automator
```

The theme follows the same pattern:

```bash
# From the Local site's wp-content/themes directory
ln -s /path/to/repo/theme carlifebydani
```

---

## Installation (production)

1. Copy (or symlink) `plugins/ev-news-automator/` into `wp-content/plugins/`.
2. Activate the plugin in **WordPress → Plugins**.
3. Go to **EV News → Settings** and fill in all fields (see below).
4. Run the smoke test suite to verify every integration before going live.

---

## Settings

| Field | Description |
|---|---|
| OpenRouter API Key | Secret key from openrouter.ai. Leave blank to keep the existing value. |
| OpenRouter Model | Model identifier, e.g. `anthropic/claude-opus-4-8`. |
| Google Spreadsheet ID | ID from the Sheet URL: `.../spreadsheets/d/{ID}/edit`. |
| Drive Folder ID | Google Drive folder where podcast docs are created. |
| Podcast Script Document ID | ID of the Google Doc the plugin appends podcast scripts to. Create the doc, share it with the service account as Editor, paste the ID here. |
| Service Account JSON Path | Absolute server path to the service account key file. Must be outside webroot. |
| GA4 Property ID | Numeric GA4 property ID (e.g. `427729375`). Leave blank to disable click sync. |
| Upcoming Session Page ID | WordPress page ID for the live news placeholder page. Leave `0` to disable. |
| Max Articles | Maximum rows kept in the active sheet tab. Oldest rows are trimmed automatically. |
| Collection Interval | How often the cron job runs (15 min / 30 min / 1 hr / 6 hr / 12 hr / daily). |
| Podcast Recording | Day and time the podcast script generation cron fires. |
| News Sources | One source per line: `https://example.com/feed rss` or `https://example.com html`. Method defaults to `rss`. |

### Google service account permissions

The same service account JSON is used for all Google integrations. It needs:

- **Google Sheet** — Editor (share the sheet with the service account email)
- **Google Drive folder** — Editor (share the folder with the service account email)
- **Google Docs** — Editor on the podcast script doc (share the doc with the service account email)
- **GA4 property** — Viewer (GA4 → Admin → Property Access Management → add the service account email)

### GA4 custom dimension

Before click tracking will work, register `article_url` as a custom dimension in GA4:

**GA4 → Admin → Custom definitions → Custom dimensions → Create**
- Dimension name: `article_url`
- Scope: Event
- Event parameter: `article_url`

---

## Smoke tests

All tests use WP-CLI and must be run from the WordPress root directory.

### Run the full suite

```bash
bash wp-content/plugins/ev-news-automator/tests/run-all.sh
```

Flags:

| Flag | Effect |
|---|---|
| `--skip-docs` | Skip test-03. Use this for routine checks — test-03 appends content to the real podcast Google Doc. |
| `--continue` | Run all tests even if one fails (default: stop on first failure). |

### Run individual tests

```bash
# Test 01 — Scraper + OpenRouter
# Fetches up to 3 articles per source, runs each through OpenRouter,
# saves results to tests/output/test-01-*.json. Nothing written to Sheets.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-01-scraper-openrouter.php

# Test 02 — Google Sheets
# Verifies auth, lists tabs, detects the active session sheet,
# appends a test row, updates its click count, then deletes it.
# Leaves the sheet in its original state.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-02-google-sheets.php

# Test 03 — Google Docs + Drive
# Creates a file in the configured Drive folder, appends a test podcast
# section to the configured podcast doc, and generates an AI podcast script.
# The appended content must be removed manually from the Google Doc afterwards.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-03-google-docs.php

# Test 04 — Google Analytics
# Verifies the service account can query the GA4 property and fetch
# ev_news_click events. Zero results is expected on a fresh install.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-04-google-analytics.php
```

Test output (JSON logs) is written to `tests/output/` and is git-ignored.

### Deployment checklist

Run the suite after every new deployment or configuration change:

```
[ ] test-01 passes  — scraper and AI summarisation working
[ ] test-02 passes  — Google Sheets read/write working
[ ] test-03 passes  — Google Docs append and Drive folder working
[ ] test-04 passes  — GA4 property access granted, custom dimension registered
```

---

## Theme integration: EV News Feed page

The plugin writes `ev_news_live_articles` (a JSON-encoded array) to `wp_options` after every collection and sync run. The theme reads this option to power the **EV News Feed** page.

**Theme files:**

| File | Role |
|---|---|
| `page-ev-news-feed.php` | WordPress page template (`Template Name: EV News Feed`). Reads `ev_news_live_articles`, renders mobile feed + desktop grid. |
| `template-parts/ev-news-feed/card.php` | Single article card. Mobile: 70 vh full-bleed image with gradient overlay and title at bottom. Desktop: horizontal flex with thumbnail left, content right. |

**Article fields rendered** (from `ev_news_live_articles`):

| Field | Used for |
|---|---|
| `title` | Card headline |
| `link` | External link (opens in new tab with `rel="nofollow"`) |
| `source` | Source domain label (red, uppercase) |
| `description` | 2-line summary clamp (desktop only) |
| `clicks` | Green engagement badge |
| `date` | Session date label (desktop only) |

**GA4 click tracking** is wired via `data-ev-news-article` attributes on all article links, which `ev-news-tracking.js` picks up and pushes an `ev_news_click` event to `dataLayer`. OG images are loaded asynchronously by `ogimageloader.init.js` via the server-side proxy (`admin-ajax.php?action=fetch_og_image`).

**To create or replace the page in WordPress:**

1. Create a new Page, set the title to `EV News Feed`.
2. Under Page → Template, choose **EV News Feed**.
3. Publish. The page is available at `/ev-news-feed/` (or whichever slug WordPress assigns).
4. Add the page to the desired navigation menu in **Appearance → Menus**.

---

## Dashboard

**EV News → Dashboard** shows:

- **Status cards** for the last Collection, Sync, and Podcast runs
- **Action buttons** to trigger any run manually
- **AI Usage Stats** — token counts broken down by summaries vs podcast scripts, estimated words generated, OpenRouter account credit balance (click Refresh to fetch live from the API)
- **Run log** and last run transcript

---

## Architecture

```
ENA_Plugin (singleton)
├── ENA_Settings        — wp_options wrapper
├── ENA_Logger          — run log + per-step transcript stored in wp_options
├── ENA_HTTP            — wp_remote_* wrapper with SSRF protection
├── ENA_Google_Auth     — service account → JWT → OAuth2 token (cached in wp_options)
├── ENA_Sheets          — Google Sheets API (read, append, update, delete, trim)
├── ENA_Analytics       — GA4 Data API (ev_news_click event counts)
├── ENA_Docs            — Google Docs + Drive API (create, append sections, move)
├── ENA_OpenRouter      — chat completions + local token usage tracking
├── ENA_Scraper         — RSS and HTML article fetching
├── ENA_Collector       — orchestrates scrape → summarise → append → trim
├── ENA_Sync            — pushes sheet rows to wp_options for the live theme feed
├── ENA_Podcast         — orchestrates scrape → AI script → append to Google Doc
├── ENA_Cron            — WP-Cron schedule registration and hook handlers
└── ENA_Ajax            — admin-ajax.php handlers for dashboard buttons
```
