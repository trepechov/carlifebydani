---
name: project-ev-news-automator
description: EV News Automator plugin — status, architecture decisions, and outstanding blockers
metadata:
  type: project
---

Plugin implemented and ready for activation. RSS scraping verified (Electrek: 100 articles). Awaiting Google SA key + OpenRouter key to go live.

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
