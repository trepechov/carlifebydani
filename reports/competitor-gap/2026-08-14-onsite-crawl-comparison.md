# On-Site Structural Crawl — carlifebydani.com vs. competitors

**Data source:** Manual crawl (WebFetch) of live homepages/articles, 2026-08-14.
**Not from Semrush.** The Semrush MCP returned `Api units balance is zero` on
every `execute_report` call today — no fresh keyword/traffic/competitor-list
data could be pulled. The competitor set below is reused from
[`2026-08-04-semrush-competitor-gap.md`](2026-08-04-semrush-competitor-gap.md)
(Semrush's own auto-detected list was low-relevance; that report re-derived
the real competitive set manually). **Top up Semrush API units to refresh the
quantitative side** — this report only covers what a page's markup and layout
show, not rankings or traffic.

**Method:** WebFetch each domain's homepage (+ one representative page where
useful) and describe title/meta, navigation, content format, structured data,
and any tools/calculators present. This is a structural read, not a
line-by-line HTML audit — treat findings as directional.

**Crawled:** carlifebydani.com (home + 1 EV News article), autobild.bg,
evpoint.bg, dizzyriders.bg, testdrive.bg, novakola.bg.
**Blocked:** evpoint.bg returned HTTP 403 to WebFetch directly (Cloudflare-style
bot protection) — worked around via targeted Google searches (`site:evpoint.bg
...`) that surfaced page titles and snippet text for its model spec pages and
charging-station content.

---

## carlifebydani.com baseline

**Homepage:** Title "Car Life by Dani - Новини и ревюта за електромобилите,
Видео и снимки". Nav: Публикации / EV News / EV Ревюта / EV Masters. Cards show
date + category + headline, some excerpts. Has: newsletter signup, monthly
Top 10, tag cloud (Tesla, BMW, Mercedes, BYD, Volvo, Hyundai…), CLBD Parts
section, wide social footer (YouTube, Instagram, Spotify, Facebook, Discord,
Patreon, TikTok). **No breadcrumbs, no visible schema.org markup, no visible
meta description** on the homepage fetch.

**Article** (`ev114-ima-li-registriran-tesla-cybertruck-v-blgariya`): H1 = title,
breadcrumb Home > EV News present here (unlike homepage), byline + date visible,
tags shown, ~300–400 words body, "Избрано за вас" (4 related) + Top 10 module,
extensive internal linking to categories/tags/author. **No comments, no social
share buttons, no schema.org Article/NewsArticle markup detected.**

This matches the standing finding from prior SEO deep-dive work: content is
thin relative to competitors and the summary text lives in the remote CSV
rather than the stored post.

---

## Competitor reads

### evpoint.bg — the one that actually beats us head-to-head
Per the 2026-08-04 gap report, evpoint.bg holds positions 1–4 on EV charging,
generic EV terms, and per-model spec pages — exactly carlifebydani's declared
subject matter. The crawl explains *why* structurally:

- **A permanent, evergreen spec page per model *and* per trim** — separate
  URLs for `tesla-model-y-long-range`, `...-long-range-performance`,
  `...-long-range-awd-juniper-2025`, `...-long-range-rwd-juniper-2025`, etc.
  Titles follow a consistent "Model • Цени и характеристики" / "ᐉ Спецификации"
  pattern. These are reference pages, not news posts — they don't expire and
  don't compete with each other for the same query.
- **A commercial charging-station catalog** (home/mobile/DC fast stations) plus
  a dedicated **mobile app landing page** (`evpoint.bg/app`) for a charging-map
  product. It's not a pure media site — content funnels into a product.
  carlifebydani has no equivalent capture point.
- **Cost-of-charging explainer content** (`колко струва зареждането...`,
  `цена за зареждане...`) — exactly cluster 2 from the gap report, and cheap
  informational content that doesn't need a news hook to justify existing.
- Blog posts live under a separate `/блог/YYYY/MM/DD/slug` path from the model
  pages — format is deliberately split: reference pages vs. dated posts.

**Takeaway:** evpoint.bg wins not because of better prose, it wins because it
built a page type carlifebydani doesn't have — a static per-model/per-trim spec
page — and paired it with a product (the app) that gives people a reason to
bookmark rather than bounce.

### autobild.bg — breadth via format variety, not depth per article
Categories: TV, News, Tests, Future, Technology, Health, Sports, Retro, Tuning,
Enthusiasm, Dealerships, Motorcycles, Heavy Vehicles (with Agriculture/Loaders
sub-splits). Notable modules:

- **"Нашите съвети" (Our Tips)** — comparison/buying-guide framing ("best
  compact ICE models under €40k", "500hp+ cars") — this is the format behind
  gap cluster 1 (used-car buyer guides), the single largest cluster carlifebydani
  has zero presence in.
- **Auto Bild TV** — dedicated video section, separate from articles.
- **7-day / 30-day trending modules** — a lightweight, always-fresh
  "what's hot" surface that's cheap to maintain and keeps stale sections looking
  alive.
- Card-based layout consistent enough across sections to suggest templated
  schema.org markup, though not confirmed from the fetch alone.

**Takeaway:** autobild.bg isn't winning cluster 1/5/6/8/9 with better single
articles — it's winning by having a page *format* (buying-guide list, retro
profile, trending sidebar) that maps directly to a search-intent pattern, then
running that format at volume across many models/topics.

### dizzyriders.bg — an explainer format worth copying
Nav includes **"Анатомията на автомобила" (Car Anatomy)** — a standing
explainer series distinct from news. This is the same shape as gap cluster 5
(maintenance/parts/dashboard-symbol content): evergreen, low-competition,
high-search-volume, and doesn't require a news peg. EV content already appears
on the homepage (Tesla Model Y, Dongfeng Forthing Friday EV) mixed into the
general feed rather than siloed.

### testdrive.bg — service-directory breadth, not a content-format lesson
Nav splits into a large **Services** mega-menu (auto services, parts, car
washes, driving schools, tire shops, insurance, rentals, gas stations) alongside
manufacturer-indexed test drives. This is a local-directory/marketplace play,
not an editorial one — off-strategy for carlifebydani to copy directly, but the
manufacturer-indexed test-drive archive (one canonical page per model,
consistently tagged) is the same "durable page per model" idea evpoint.bg uses.

### novakola.bg — adjacent market, but the interactive-tool pattern is portable
Not an editorial competitor (it's a leasing marketplace), but two things are
directly reusable ideas: a **leasing/budget calculator** as a standalone
interactive page, and an **AI assistant widget ("НиКи")** for recommendations.
Also runs a dedicated "Електрически" listing category (293 active listings) —
confirms EV-specific browsing is a normal, expected nav item in this market
even for non-editorial sites.

---

## What carlifebydani.com can take from this

Ranked by leverage-vs-effort, tied back to the existing gap clusters:

1. **Build an evergreen EV model spec-page template**, separate from EV News
   episode posts — one static page per model (battery, WLTP range, 0–100,
   price, charging speed, comparison to 1–2 rivals). This directly targets gap
   clusters 3 & 4 (evpoint.bg currently owns them outright) and is the single
   highest-leverage structural gap found in this crawl — it's a page *type*
   carlifebydani doesn't have at all, not a content-quality problem.
2. **Add Article/NewsArticle + BreadcrumbList schema.org JSON-LD** to EV News
   posts, and mirror the article page's breadcrumb onto the homepage. Neither
   crawl target showed this cleanly either, so it's not table-stakes yet in
   this market — but it's cheap, testable, and the article page structure
   (byline, date, tags) is already there to hang it on.
3. **Add a short "cost of charging" evergreen page/section** — evpoint.bg's
   cheapest-to-build content type and a direct hit on gap cluster 2.
4. **Pick one explainer series in carlifebydani's own EV voice**, modeled on
   dizzyriders.bg's "Анатомията на автомобила" — e.g. "anatomy of an EV
   battery/motor/charging port" — hits gap cluster 5 without abandoning the EV
   focus, and per the 2026-08-04 report this cluster has near-zero competition.
5. **Skip copying autobild.bg's and testdrive.bg's full category breadth**
   (retro, tuning, heavy vehicles, service directories) — off-strategy for an
   EV-only brand and would dilute the current positioning for volume that
   isn't the target audience.
6. Lower priority, worth a later look: a lightweight interactive tool (charging
   cost calculator, EV vs. ICE cost-of-ownership) in the spirit of novakola.bg's
   calculators — pairs naturally with item 1 once the spec-page template exists.

---

## Data coverage & limitations

- Semrush: **blocked entirely today** (`Api units balance is zero`). No
  quantitative ranking/traffic/keyword-gap refresh in this report — everything
  above is structural/qualitative from live pages only.
- evpoint.bg: homepage and a direct model-page fetch both returned HTTP 403
  (bot protection). Evidence for evpoint.bg is reconstructed from Google search
  snippets (`site:evpoint.bg ...`), which give page titles and short excerpts
  but not full page markup — treat the schema.org/structured-data claims for
  evpoint.bg as unconfirmed, not absent.
- This was one fetch per domain (plus one article for carlifebydani.com and one
  search pass for evpoint.bg) — not a full-site crawl. Findings describe the
  page(s) actually fetched, not the whole competitor site.

---

*Generated 2026-08-14. Manual WebFetch crawl, not Semrush. Re-run the
2026-08-04-style Semrush gap report once API units are restored to pair this
structural read with fresh ranking numbers.*
