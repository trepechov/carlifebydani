# CarLife by Dani — Business Case

**Document type:** Internal working document
**Last updated:** 2026-06-18
**Purpose:** Establish shared understanding of what CarLife by Dani is, who it serves, and how it makes money — so that all future development decisions on this WordPress theme and its plugins can be evaluated against the same goals.

> This is a decision-guiding document, not marketing copy. When in doubt about whether to build something, return to the [Decision Framework](#8-decision-framework).

---

## 1. Executive Summary

**CarLife by Dani (CLBD)** is a Bulgarian-language electric-vehicle (EV) media brand. It produces news, reviews, podcasts, and events for the Bulgarian EV community, and runs an engaged multi-platform audience across YouTube, Instagram, Facebook, TikTok, Discord, and a podcast distribution footprint (Spotify, Apple Podcasts).

- **Primary product / growth engine:** the **YouTube channel** — a weekly EV news review podcast plus a playlist of podcast interviews with people passionate about EV mobility in Bulgaria.
- **The website** (carlifebydani.com) is the brand's owned, durable home. It currently mirrors and archives content produced for other channels; the strategic intent is to develop it into a **powerful, self-sustaining tool** rather than a passive archive.
- **The community** lives largely in a Facebook group and a Discord server, the latter being an active content-sourcing pipeline (fans submit EV news that the team curates).

The brand matters because it occupies a focused niche — Bulgarian-market EV coverage in Bulgarian — at a moment of fast local EV adoption (the site itself cites a "216% growth" figure for the Bulgarian EV market). It is a credible, community-rooted voice in a market most international outlets ignore.

**Two strategic development workstreams** define near-term engineering priorities:

1. **EV News Automation** — replace the manual, per-episode CSV workflow with an automated, continuously-updated live news feed plus AI-assisted podcast script generation.
2. **On-Site SEO** — close meta/structured-data gaps so the site is discoverable and earns rich results for the Bulgarian EV audience.

---

## 2. Brand & Mission

### Core identity
Bulgarian EV media built by enthusiasts, for enthusiasts. The on-site tagline frames it as: *"News and reviews for electric vehicles, videos and photos."* The brand voice is community-first and passion-driven rather than corporate.

### Mission
Inform and grow the Bulgarian EV community — covering news, vehicle reviews, charging infrastructure, safe-driving education ("EV Masters"), and the local mobility scene — in Bulgarian, the language the audience actually uses.

### Values (inferred from content & structure)
- **Community over broadcast** — fans source news (Discord), gather at events, and participate in a Facebook group.
- **Local relevance** — content is anchored to the Bulgarian market (brand entries, local pricing, local events) rather than generic global EV news.
- **Practical enthusiasm** — reviews, safety guides, parts, and lifestyle content alongside news.

### Positioning in the Bulgarian EV space
A focused, independent, Bulgarian-language EV voice. Differentiation rests on: (a) language and local-market focus, (b) a genuine community pipeline, and (c) a multi-format presence (video, podcast, written, social) that few local competitors match.

---

## 3. Audience

| Segment | Who they are | What they come for |
|---|---|---|
| EV owners | People who already drive electric in Bulgaria | Reviews, charging info, safe-driving tips, ownership community |
| EV-curious | Considering an EV purchase | Reviews, market news, brand availability ("X is now in Bulgaria") |
| Enthusiasts / fans | Tech- and car-passionate followers | Weekly news podcast, interviews, events, Discord/Facebook community |
| Event attendees | Local community members | Meetups and gatherings (Watts on the Grill, CLBD Coffee Day, CLBD Trip) |

**What unites them:** they want Bulgarian-language, locally relevant EV content and a community to share it with. The Discord channel and Facebook group are where the most active fans live; YouTube is where the widest audience is reached.

---

## 4. Content Strategy & Channels

### Channel roles

| Channel | Role | Notes |
|---|---|---|
| **YouTube** | **Primary product & growth engine** | Weekly EV news review podcast + interview playlist with Bulgarian EV-mobility figures |
| **Website** | Owned home, archive, and (target state) durable tool | Mirrors/archives other channels today; intended to become a primary asset |
| **Instagram** | High-effort social presence | Significant manual effort; key brand surface |
| **Facebook group** | Community hub | `groups/carlifebydani` — the community proper |
| **Facebook page** | Broadcast surface | Brand updates |
| **TikTok** | Short-form distribution | Part of the per-review content set |
| **Discord** | **Content sourcing pipeline** | Fans submit EV news weekly; team curates into EV News pages |
| **Podcast platforms** | Audio distribution | Spotify, Apple Podcasts |
| **Patreon** | Direct support / monetisation | "Подкрепи ни" (Support Us) |

### Content types
- **Weekly EV news roundups** — the core recurring format; episode-numbered (e.g. `#EV155`), tied to the YouTube podcast.
- **Car reviews** — the strongest original written content; full authored reviews of specific EV models.
- **Podcast interviews** — conversations with people passionate about EV mobility in Bulgaria (YouTube playlist).
- **Events** — organised community gatherings and trips, documented under Publications.
- **EV Masters** — educational/explainer content, largely YouTube-embed based.

### How channels interrelate
The intended flywheel: **community (Discord/Facebook) feeds news → news becomes a YouTube podcast episode + website EV News page → clips and posts distribute to Instagram / Facebook / TikTok → audience returns to YouTube and the community.** The website is meant to be the durable backbone that captures, structures, and makes discoverable everything the ephemeral social channels produce.

### The "single car review" content set
A car review is not one artifact but a coordinated set:
- accompanying **website article**,
- **Instagram** post(s),
- **Facebook** post,
- **TikTok** post,
- **YouTube** video.

The team needs **structured information for a single review** that can drive all of these consistently. This is a known future requirement (see [Open Questions](#10-open-questions)) and a candidate for the same kind of structured/automated tooling being built for EV News.

---

## 5. Current Website Architecture

The site is a custom WordPress theme (Tailwind CSS build) now organised as a monorepo: `theme/` for the theme and `plugins/ev-news-automator/` for the news automation plugin (in development).

### Content taxonomy (from `theme/constants.php`)

| Category | ID | Content structure |
|---|---|---|
| EV News | 1 | Authored excerpt + curated external links (currently from a CSV) |
| EV Reviews | 3 | Full authored review text — strongest original content |
| EV Masters | 45 | YouTube video embed + minimal text |
| News | 6 | General news category |

Key configured pages: Top 10 (`TOP_10_PAGE_ID`), Share With Us (`SHARE_WITH_US_PAGE_ID`).

### Public sections
- **Publications** (`/publications/`) — mixed feed of events, reviews, and educational guides (paginated, reverse-chronological).
- **EV News** (`/ev-news/`) — episode-numbered weekly news roundups.
- **EV Reviews** — full vehicle reviews.
- **EV Masters** — educational video content.
- Secondary: **CLBD Parts** (Tesla original components), **Space by CLBD** (space/aviation).
- A **"Топ 10" (Top 10)** sidebar surfaces popular content across all categories.

### Front page composition (`theme/front-page.php`)
Stacked sections: featured posts → news → share-with-us → top 10 → EV news → EV reviews → EV masters → brands → newsletter → find-us. This makes the homepage a directory of every content stream plus community/newsletter calls to action.

### Content flow: Discord → EV News page (current, manual)
1. Fans post EV news in a Discord channel throughout the week.
2. The team manually collects items into a **CSV file**.
3. The CSV is linked to a weekly WordPress post via the `news_csv` post meta.
4. `single.php` (lines ~110–142) fetches the CSV and renders each external article via the `card-article-external.php` template part.

This produces a **static snapshot per episode** — there is no live, continuously-updated news view, and hosts prepare podcast talking points ad hoc. This is exactly what the EV News Automation workstream targets.

### Content flow: car test → review page (current)
A reviewed vehicle becomes a full authored post in the EV Reviews category (`single.php` renders `post_content` in full). The surrounding multi-channel posts (Instagram/Facebook/TikTok/YouTube) are produced manually and are not yet structured or tooled.

---

## 6. Monetisation & Growth Model

> Inferred from on-site signals. Where figures or contracts are unknown, this is flagged.

| Lever | Evidence | Status / notes |
|---|---|---|
| **Direct support** | Patreon ("Подкрепи ни") | Active; revenue scale unknown |
| **Events** | Watts on the Grill, CLBD Coffee Day, CLBD Trip, ticketing | Active; may generate ticketing/sponsor revenue — terms unknown |
| **Sponsorships / partnerships** | Charging-network and brand mentions (e.g. Eldrive), promo codes section | Likely sponsored/affiliate; specific deals unknown |
| **Affiliate** | Promotional codes section | Present; scale and partners unknown |
| **Product** | CLBD Parts (Tesla original components) | A commerce-adjacent line; revenue role unknown |
| **YouTube / podcast** | Primary audience platform | Ad revenue and reach are the growth foundation; figures unknown |

### Growth model
Audience growth is **YouTube-led**, amplified by Instagram and the community channels, and **monetisation is diversified and community-anchored** (support + events + sponsorship + parts) rather than dependent on a single ad stream. The strategic bet behind investing in the website is to convert ephemeral social reach into a **durable, discoverable, monetisable owned asset** — which is why SEO and automation are the two priorities.

**Unknowns to resolve:** revenue split across levers, sponsorship contract terms, Patreon/event contribution, and whether CLBD Parts is a meaningful revenue line or a community service. See [Open Questions](#10-open-questions).

---

## 7. Strategic Priorities

Two workstreams currently define engineering effort. Both serve the same goal: turn the website into a powerful, low-maintenance asset that strengthens the YouTube-to-website flywheel.

### 7.1 EV News Automation
*(Source: `docs/brainstorms/2026-06-17-ev-news-automation-requirements.md` — status: ready for planning. WordPress plugin: `ev-news-automator`.)*

**What it replaces:** the manual CSV-per-episode workflow, which is time-consuming, produces only static per-episode snapshots, and offers no live news view to visitors. Hosts also have no prepared podcast script.

**The automated pipeline:**
1. **Daily collection (WP-Cron):** before scraping, the plugin queries **Google Analytics (GA4)** for `ev_news_click` event counts (already tracked by the site) and writes the per-article click totals into the Sheet (column G). It then scrapes configured EV news sources (RSS-first, HTML fallback), deduplicates by URL, and for each new item uses OpenRouter to produce a Bulgarian title + short Bulgarian summary. Rows are appended to a **Google Sheet** (capped, default 50; oldest removed) with `clicks = 0`.
2. **Live team curation (Google Sheets):** the Sheet is the single source of truth. The 2–3-person editorial team edits, reorders, and deletes rows directly — no approval gate. Column G (clicks) is read-only for the team; it is maintained by the plugin.
3. **Engagement-based ordering (every sync):** before writing the live feed, articles are sorted into three groups — new-today articles first (no click penalty; they just arrived), then older articles ordered by click count descending, then older zero-click articles at the bottom. Nothing is deleted by the plugin; the editorial team retains full visibility of every article in the Sheet. Zero-click articles naturally sink to the bottom, making the feed self-organising: the most interesting content floats up, and unclicked content stays visible but deprioritised.
4. **Website sync (same cron run as collection):** the Sheet is synced into a `wp_options` JSON payload (`ev_news_live_articles`); a custom page template renders a **live rolling feed of 20–50 articles** with no API calls at render time, reusing the existing `card-article-external` partial.
5. **Podcast script generation (recording day):** a scheduled run snapshots the current articles, fetches full content, and uses OpenRouter to produce extended Bulgarian scripts, written to a Google Doc for the hosts. Also manually triggerable.

**Key constraints:** no breaking changes to existing `news_csv` posts; no API calls at render time; graceful failure on bad sources; Google credentials stored outside the webroot; the Sheet is always authoritative.

**Tie to business goals:**
- **Saves editorial time** — eliminates manual collection and CSV maintenance.
- **Keeps the site fresh** — a live, continuously-updated feed replaces stale per-episode snapshots, improving return visits and SEO freshness signals.
- **Strengthens the podcast** — automated script generation removes ad-hoc prep and directly supports the primary product (the YouTube news podcast).
- **Builds toward the "owned tool" vision** — moves the site from passive archive to live utility.

**Deferred in this workstream:** X/Twitter integration (API cost), auto-publishing of posts, public voting/reactions, AI-generated images, cron notifications.

### 7.2 On-Site SEO
*(Source: `docs/SEO_PROPOSALS.md` — overall readiness 6.5/10, language bg-BG.)*

**Current state:** solid semantic HTML foundation, but critical gaps in the **meta layer** (no Open Graph, no meta descriptions), **structured data** (no JSON-LD at all), **image optimisation** (no lazy loading, missing alt text), plus heading-hierarchy issues and a deprecated `wp_title()`.

**Highest-impact fixes (top of the implementation order):**
1. Meta descriptions + Open Graph + Twitter Card tags (fixes blank social previews).
2. JSON-LD Article + Organization schema.
3. **VideoObject schema for EV Masters** — unlocks Google video-carousel eligibility; the single biggest SERP opportunity for the embed-only video pages.
4. Replace `wp_title()` with `add_theme_support('title-tag')`; add homepage H1; BreadcrumbList JSON-LD.
5. **Review schema** for EV Reviews (rich results / star eligibility) and **CollectionPage + ItemList** schema for EV News (frames curated link roundups as intentional editorial, mitigating thin-content risk).
6. Lazy loading + alt text, deferred JS, preconnect hints, archive canonicals.

**Per-category SEO posture:** EV News pages risk thin content (excerpt is the only original text — guideline: 100–150 words); EV Reviews are the strongest type; EV Masters are near-invisible without VideoObject schema and a description (guideline: 80 words).

**Effort:** ~10 hours for the top 9 (schema + core meta); ~20 hours for all proposals. A full SEO plugin (Yoast/Rank Math) could replace several meta proposals if admin-UI control is preferred.

**Tie to business goals:**
- **Discoverability for the Bulgarian EV audience** — captures search demand the social channels can't.
- **Rich results & social previews** — better SERP and share appearance lifts click-through, feeding the flywheel.
- **Protects the owned asset** — structured data and freshness make the website a compounding, defensible property rather than a thin mirror of YouTube.

---

## 8. Decision Framework

Apply these principles when deciding whether to build, defer, or drop a feature. A strong "yes" on one or more is the bar for prioritisation.

1. **Does it reduce manual editorial work?** Time saved for a 2–3-person team compounds. (Automation, templating, structured input.)
2. **Does it improve content discoverability?** SEO, structured data, internal linking, freshness — anything that earns or retains audience without paid reach.
3. **Does it strengthen the YouTube-to-website pipeline?** Features that convert video/social reach into durable, owned, monetisable website value rank high.
4. **Does it serve or grow the community?** The Discord/Facebook pipeline is a moat; protect and feed it.
5. **Does it keep the site fresh and live?** Prefer continuously-updated content over static snapshots.
6. **Does it avoid breaking existing content or adding render-time fragility?** No breaking changes to existing posts; no external API calls at page-render time; fail gracefully.
7. **Does it fit the team's operating reality?** Tools the team already uses (Google Sheets/Docs) and infrastructure already in place (same-server WP plugin + WP-Cron) beat new systems.
8. **Is the effort proportional to the impact?** Favour high-impact/low-effort items (see the SEO implementation-order table) before speculative builds.

If a proposed feature scores low across all eight, default to **defer**.

---

## 9. Out of Scope

Explicitly deferred, with rationale:

| Item | Why deferred |
|---|---|
| X / Twitter integration in news collection | Official API ~$100+/month; revisit when budget allows |
| Engagement ranking beyond basic sort | GA4-driven click sort (3-group: new / engaged / zero-click) is now active; finer ranking (e.g. time-decay weighting, CTR vs raw count) is deferred |
| Automatic publishing/scheduling of WordPress posts | Editorial control retained; Sheet curation is the human gate |
| Public voting/reactions on articles | Out of scope for the live-feed phase; future signal via analytics |
| AI-generated featured images | Not core to current goals |
| Cron-run email/Slack notifications | Dashboard logging is sufficient for now |
| WebP/AVIF, sitelinks-search schema, hreflang, author schema | Low-priority SEO enhancements; after critical fixes land |
| Numeric rating system / `car-model` meta for reviews | Medium-priority; unlocks star snippets but needs editorial process |
| Multilingual expansion | Brand is intentionally Bulgarian-only today |

---

## 10. Open Questions

**Product / strategy**
- What is the actual revenue split across Patreon, events, sponsorships, affiliate, and CLBD Parts? Which lever should development optimise for?
- Is CLBD Parts a real revenue line worth tooling, or a community service?
- How should the **car-review content set** (article + Instagram + Facebook + TikTok + YouTube) be structured and tooled? This is a stated need with no spec yet — is it the next workstream after EV News Automation?

**EV News Automation (from the requirements doc)**
- Which specific EV news source URLs seed the initial scrape list? (Configuration, not code.)
- Should the live news page **replace** the `/ev-news/` category URL, or live at a separate URL (e.g. `/ev-live/`, `/ev-strim/`)?
- One Google Doc per recording session, or a single running master Doc updated weekly?
- What is the GA4 **numeric property ID** to enter in plugin settings? (Needed before click sync and engagement sort go live.)

**SEO**
- Hardcoded theme functions vs. a full SEO plugin (Yoast/Rank Math) for meta control — which fits the team's editorial workflow?
- Will editors reliably meet the excerpt-length guidelines (EV News 100–150 words; EV Masters 80 words) that the thin-content mitigations depend on?

**Measurement**
- What are the target success metrics for the website investment (organic sessions, return visits, rich-result impressions, time-on-page)? Without these, "develop the website into a powerful tool" can't be evaluated.
