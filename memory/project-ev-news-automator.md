---
name: project-ev-news-automator
description: EV News Automator plugin — status, architecture decisions, and outstanding blockers
metadata:
  type: project
---

Plugin implemented and ready for activation. RSS scraping verified (Electrek: 100 articles). Awaiting Google SA key + OpenRouter key to go live.

**Session turnover (Tuesday after recording) — deprecated old flow:**
- ~~Create a new WP page per session~~ — this is no longer done.
- **New flow:** publish the episode post → create a new `DD.MM.YYYY` Sheet tab → trigger "Run collection now". The static `/ev-news-feed/` page (WP ID 8851) auto-updates. No new WP page is created per session.

**Architecture decisions made:**
- Google Sheets is the storage adapter (swappable via ENA_Plugin::__construct binding)
- One tab per session, named DD.MM.YYYY; active tab = most recently dated
- Columns A–G: title | description | link | author | upvote | downvote | clicks
- `clicks` (column G) is the GA4-sourced click count, written as 0 on append and updated daily before collection

**GA4 integration (added 2026-06-19):**
- Event tracked by site: `ev_news_click` with custom param `article_url` (theme/js/ev-news-tracking.js)
- New class: `class-ena-analytics.php` — queries GA4 Data API v1 for eventCount grouped by customEvent:article_url
- Scope needed: `analytics.readonly` on the service account
- SA needs Viewer role on the GA4 property
- GA4 numeric property ID configured in plugin settings as `ga4_property_id`

**Engagement sort (added 2026-06-19, revised same day):**
- No mid-week deletion. Instead, ENA_Sync sorts articles into 3 groups every sync:
  1. New today (added_date = today) — always at the top, no click penalty
  2. Older with clicks > 0 — sorted by click count descending
  3. Older with clicks = 0 — at the bottom
- Requires column H (`added_date`, Y-m-d) written by the adapter on append
- The plugin never deletes rows; team handles deletions manually in the Sheet

**Blockers before going live:**
- Google Service Account JSON key (path configured in plugin settings)
- OpenRouter API key
- GA4 numeric property ID (for click sync and engagement sort)
- Service account needs: Editor on Sheet + Drive folder, Viewer on GA4 property

**Why:** no-deletion approach keeps full editorial visibility (zero-click articles stay in Sheet for team review). Natural self-organising feed: interesting content floats up each day.
**How to apply:** when suggesting plugin features or config steps, include GA4 property ID and service account GA4 Viewer role in the setup checklist.

**Superseded (2026-07-18): trim_to_max() now deletes rows.** The "plugin never deletes rows" decision above no longer holds — `ENA_Cron::run_pipeline()` now sorts the full sheet (upvote DESC → pub_date DESC → added_date DESC) and calls `ENA_Sheets::trim_to_max($max_articles)`, which deletes the bottom rows every run. `max_articles` was 100 as of this date.

**Vote/click sync reconciliation finding (2026-07-18):** GA4 vs. active-tab Sheet vs. `ev_news_live_articles` were pulled for a full audit (staging DB `local`, table prefix `cvrvu_`, via the `WP Local` install). Sheet↔feed sync (`ENA_Sync::run()`) was perfect (0/100 mismatches) and the GA4 fetch steps all logged `ok` — so the visible undercount is **not** a fetch failure or a stale `collection_interval`. It's **orphaning**: of 277 GA4 (event, url) combinations over 7 days, 152 belonged to articles no longer in the active tab at all (373 of ~570 total GA4 events). Split two ways:
- **33 URLs** stranded on the previous week's tab (mostly `14.07.2026`, one on `07.07.2026`) — tab rotation orphaning, as expected: only the newest tab gets `refresh_analytics()` + re-sync.
- **119 URLs** weren't on *any* tab — deleted outright by `trim_to_max()`. Because the sort key is upvote-first, articles with real downvotes/clicks but zero upvotes rank to the bottom and get trimmed, silently discarding real GA4 engagement history for them.
**Why:** confirms the "no-deletion" architecture note above is stale and that trimming (not just tab rotation) is now a real source of engagement loss — 119 vs 33 URLs, so trimming is the larger contributor.
**How to apply:** if asked to fix the undercount, the fix is either to stop trimming below a floor that accounts for downvote/click-only engagement, or to snapshot GA4 counts before a row is trimmed so historical engagement isn't silently lost. Don't waste time re-checking the GA4 fetch step or `collection_interval` first — both were confirmed healthy on this date.
