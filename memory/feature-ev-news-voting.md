---
name: feature-ev-news-voting
description: Cookie+GA4-based upvote/downvote on EV news feed cards — implemented 2026-07-09; drives sheet sort order and display grouping
metadata:
  type: project
---

## Upvote / Downvote feature

Implemented 2026-07-09. Visitors can upvote or downvote any article card on `/ev-news-feed/`.

### Frontend — `theme/js/ev-news-voting.js`
- Cookie `ev_news_votes` (365-day expiry, SameSite=Lax) stores per-article state: `{ current: 'up'|'down'|null, fired: { up: bool, down: bool } }`.
- GA4 events `ev_news_upvote` / `ev_news_downvote` fire **at most once per direction per article**, regardless of how many times the user switches. The `fired` map tracks this independently of the active `current` selection.
- Buttons: `[data-ev-news-upvote]` / `[data-ev-news-downvote]` with `data-article-id`, `data-article-url`, `data-title`. Counts rendered in `[data-vote-count="up"]` / `[data-vote-count="down"]` spans.
- Optimistic DOM update: count bumped/unbumped immediately; disabled state applied to the active button.

### Backend sync — `ENA_Analytics`
- `fetch_upvotes(array $urls)` and `fetch_downvotes(array $urls)` — same contract as `fetch_clicks()`, using GA4 `runReport` with `eventName` filter for `ev_news_upvote` / `ev_news_downvote`.
- GA4 truncates `article_url` at 100 chars; a prefix→full-url index handles long URLs.

### Sheet storage — `ENA_Sheets`
- `update_upvotes(array $url_to_count)` — writes to column E.
- `update_downvotes(array $url_to_count)` — writes to column F.
- `sort_by_upvotes()` — sortRange batchUpdate: col E (upvote) DESC → col I (pub_date) DESC → col H (added_date) DESC. Header row preserved.
- `trim_to_max(int $max)` — deletes bottom rows after sort (oldest zero-upvote articles removed).

### Sheet columns (9 total, A–I)
`title | description | link | author | upvote | downvote | clicks | added_date | pub_date`

### Display order — `ENA_Sync`
- Group 1: `added_date >= yesterday UTC` (last 24 h) — in sheet order
- Group 2: everything older — in sheet order
- Sheet order within groups already reflects upvote engagement.
- Status keys: `recent_24h`, `older`, `published_today`.

### GA4 requirement
One custom dimension `article_url` (event-scoped) covers all three events: click, upvote, downvote.

**Why:** upvote/downvote sorts carry more editorial signal than passive clicks. The sheet order is driven by upvotes so the podcast script naturally surfaces the most audience-validated articles.
