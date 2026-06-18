# EV News Automation — Requirements

**Date:** 2026-06-17  
**Status:** Ready for planning  
**Category:** New feature — WordPress plugin

---

## Problem

EV news articles are currently collected manually, formatted into a CSV file, and linked to individual weekly WordPress posts. This is time-consuming, produces a static snapshot per episode, and gives site visitors no live or continuously-updated view of current EV news. The team also has no automated podcast script — hosts prepare ad-hoc before recording.

---

## Goal

Replace the manual CSV workflow with an automated pipeline:

1. A daily AI agent collects EV news, translates headlines and writes short Bulgarian summaries, and writes articles to Google Sheets.
2. The team curates the live list directly in Google Sheets (edit, reorder, delete).
3. The website always shows the current state of the Sheet — a live rolling feed of 20–50 articles visible to all visitors.
4. On recording day, a script generation run takes a snapshot of the current live articles and produces a Google Doc with extended Bulgarian summaries for the podcast hosts.

---

## Users

**Team (editorial):** 2–3 people who curate the live news list in Google Sheets. They can delete irrelevant articles, edit Bulgarian titles/summaries, and reorder rows. No separate approval step — whatever is in the Sheet is on the site.

**Site visitors:** Can see all current news articles on the live news page, click through to the source, and expand the Bulgarian summary. No interaction or voting in this phase.

**Future (deferred):** Engagement data from Google Analytics or a similar source will be used to surface which articles resonate with the audience, informing curation.

---

## Core Flow

### Phase 1 — Daily Collection (automated, WP-Cron)

- WP-Cron fires once daily (configurable time, e.g. 6:00 AM)
- Plugin scrapes a configured list of EV news websites
  - RSS feeds used where available; HTML scraping (wp_remote_get + DOMDocument) as fallback
  - Deduplicates by URL against all existing articles in the Sheet
- For each new article, calls OpenRouter:
  - **Output:** Bulgarian title (translated) + short Bulgarian summary (2–3 sentences)
- Appends new rows to the Google Sheet
  - Columns: `Date Collected`, `Source`, `Original Title`, `Bulgarian Title`, `URL`, `Short Summary (BG)`
- If total row count exceeds the configured maximum (default: 50), the oldest rows are removed automatically
- X (Twitter) accounts: **deferred** — skip until API access is resolved

### Phase 2 — Live Team Curation (Google Sheets, ongoing)

- The Google Sheet is the single source of truth for what appears on the website
- Team can at any time:
  - Delete rows (removes article from site)
  - Edit Bulgarian Title or Short Summary (edits appear on site after next sync)
  - Reorder rows (row order = display order on site)
- No approve/reject status column — if a row exists in the Sheet, it is live on the site
- Target range: 20–50 articles in the Sheet at any time

### Phase 3 — Website Sync (automated, same WP-Cron run)

- After each daily collection run, the plugin syncs the full Sheet content to WordPress
  - Stores the current article list as a JSON payload in `wp_options` (key: `ev_news_live_articles`)
  - This is what the website template reads — no API calls at page-render time
- A dedicated WordPress page uses a custom template to display the live articles
  - Reads from the `wp_options` JSON (fast, no external calls)
  - Renders each article using the existing `card-article-external` template part
  - Shows source, Bulgarian title (linked to original article), and expandable Bulgarian summary
- The live news page URL is configurable (e.g. `/ev-news-live/` or replaces the category page)
- Existing weekly episode posts (with `news_csv` meta) continue to work unchanged — no migration needed

### Phase 4 — Podcast Script Generation (scheduled, recording day)

- WP-Cron fires on the configured recording day of the week at a configured time (e.g. Tuesday 23:00)
- Plugin reads the current article list from the Google Sheet (live snapshot at that moment)
- For each article, fetches the full article content via wp_remote_get
- Calls OpenRouter with an extended podcast-script prompt per article
  - **Output:** 1–2 paragraph Bulgarian script the host reads on air
- Creates or updates a Google Doc in a configured Google Drive folder
  - Doc title: `EV News Podcast Script – YYYY-MM-DD`
  - One section per article: Bulgarian title, source URL, extended Bulgarian script
- Can also be triggered manually from the WP admin dashboard at any time

---

## WordPress Admin Plugin Page

A dedicated admin menu item "EV News Automator" with two sub-pages:

**Settings:**
- OpenRouter API key
- Google Cloud service account credentials (path to JSON file on server)
- Google Sheet ID
- Google Drive folder ID (for podcast scripts)
- List of EV news sources to scrape (URL + method: `rss` or `html`)
- Maximum articles to keep in Sheet (default: 50)
- Recording day and time (for Phase 4 schedule)

**Dashboard:**
- Last collection run: timestamp + articles added + articles removed
- Current article count in Sheet
- Last website sync: timestamp
- Last podcast script: timestamp + Google Doc link
- Manual trigger buttons: "Run collection now", "Sync Sheet to site now", "Generate podcast script now"
- Log of last 20 run events with status

---

## Website Display

The live news page displays the current article list to visitors:

- Each article card shows: Bulgarian title (external link to source), source name, date collected, expandable Bulgarian summary
- No voting or engagement interaction in this phase (upvote/downvote columns from the old CSV model are removed)
- Future: engagement data (e.g. click-through rates from Google Analytics) can be added as a sort/rank signal without changing this architecture
- The page is a standard WordPress page with a custom page template — no new URL structure required beyond what WP already handles

---

## Non-Functional Requirements

- **No breaking changes to existing content:** The `news_csv` post meta and `single.php` template (lines 108–135) continue to work for all existing episode posts unchanged.
- **No API calls at render time:** All Google Sheets reads and OpenRouter calls happen in WP-Cron, never during a visitor page load.
- **Fail gracefully on scraping errors:** If a source is unreachable or its structure has changed, that source is skipped and logged. The rest of the run continues.
- **Google credentials stored outside webroot:** The service account JSON must not be accessible via HTTP (e.g. stored above `public_html` or in a non-served directory).
- **Sheet is authoritative:** If the Sheet is edited between two cron runs, the next sync picks up all changes. There is no local cache that can diverge permanently.

---

## Decisions & Constraints

| Decision | Choice | Reason |
|---|---|---|
| Data model | Rolling live feed (20–50 items) | Not weekly episode snapshots; continuously updated |
| Source of truth | Google Sheets | Team curates there directly; familiar and shareable |
| Website data store | `wp_options` JSON synced from Sheet | Fast reads at render time; no extra DB schema |
| Curation model | Edit/delete in Sheet directly | No approval gate; Sheet content = live site content |
| Podcast script output | Google Doc (per recording day) | For host use only, separate from website |
| AI provider | OpenRouter | Team preference; flexible model selection |
| X / Twitter | Deferred | Official API $100+/month; revisit when budget allows |
| Execution platform | WordPress plugin + WP-Cron | Same server as site; no extra infrastructure |
| Scraping approach | RSS-first, HTML fallback | RSS is structured and reliable; HTML scraping for sources without feeds |
| Upvotes / downvotes | Removed from new system | No live voting; future engagement signals will come from analytics |

---

## Prerequisites (before development starts)

1. **Google Cloud project** with Sheets API v4 and Docs API enabled
2. **Service account** with Editor access to the target Sheet and Drive folder
3. **Service account credentials JSON** stored in a non-web-accessible path on the server
4. **OpenRouter API key** with a funded account
5. **Google Sheet** created with the column structure above; Sheet ID noted for plugin settings
6. **WordPress page** created as the live news destination; page template assigned in plugin settings

---

## Out of Scope

- X / Twitter integration (deferred, pending API access decision)
- Engagement-based sorting or ranking (deferred, pending analytics integration)
- Automatic publishing or scheduling of WordPress posts
- Public-facing voting or reactions on articles
- AI-generated featured images
- Email or Slack notifications on cron runs

---

## Open Questions

- Which specific EV news source URLs should be in the initial scrape list? (Configuration, not code — the settings page holds the list.)
- Should the live news page replace the `/ev-news/` category URL, or live at a separate URL (e.g. `/ev-strim/`, `/ev-live/`)?
- One Google Doc per recording session, or a single running master Doc updated each week?
