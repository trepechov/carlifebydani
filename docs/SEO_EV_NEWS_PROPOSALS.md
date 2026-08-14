# EV News Pages — SEO Deep-Dive & Proposals

**Analysis date:** 2026-07-28 · **Status check 2026-08-14:** Priority 0 below is **fixed and
verified** (2026-08-13) — the language/locale mismatch it describes no longer exists. See the
note at that section rather than treating it as open. Priority 1's grounded-content proposal
is now **built**, see [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md). The rest of
this doc (thin `/tag/` archives, collapsed-card ratio, internal linking) has not been
re-verified against current state — treat as directionally current, not re-measured.
**Scope:** The `/ev-news/` archive and the individual EV News post pages (the site's
highest-traffic section — the AI-translated podcast news roundups).
**Companion doc:** [SEO_PROPOSALS.md](SEO_PROPOSALS.md) covers the whole site; this doc
goes deep on EV News only and supersedes its Section A.

Live page inspected for this analysis:
`/ev-news/ev160-byd-denza-z-1582-konya-chista-elektricheska-mosht/`

---

## What these pages actually are (the structural reality)

Each EV News post is a **curated link roundup** tied to one podcast episode:

- **H1** = the episode title, e.g. *"#EV160 - BYD Denza Z - 1582 коня чиста електрическа мощ!"*
- **`post_content` / `post_excerpt` are nearly empty** — Yoast's own schema reports
  `"wordCount":17` for this page.
- The real content is a **remote CSV** fetched live at render time
  ([single.php:110-115](../theme/single.php#L110-L115)) via `wp_remote_get(... timeout 5)`,
  then rendered as external-article cards
  ([card-article-external.php](../theme/template-parts/single/card-article-external.php)).
- Each card = an **H5 headline** linking out (`target="_blank" rel="nofollow"`), a
  thumbnail with **empty `src`** (JS hotloads the source site's OG image), an upvote/downvote
  widget, and a **"Виж още" accordion** hiding the only real prose — the AI-translated
  Bulgarian summary — behind a collapsed `max-h-0 opacity-0` container
  ([card-article-external.php:35](../theme/template-parts/single/card-article-external.php#L35)).

So from a crawler's perspective the page is: one Bulgarian headline, ~17 words of owned
text, a wall of H5 links to *other* domains, and unique prose that is both **collapsed**
and **served from a remote CSV that isn't even in the database**.

That's the problem to solve. Below, ordered by impact-to-effort.

---

## 🔴 Priority 0 — The bug that's capping everything: pages are served as English

> ✅ **RESOLVED 2026-08-13.** Site Language is now Bulgarian; `<html lang>`, `og:locale` and
> Yoast's `inLanguage` all verified `bg`/`bg-BG` live. The section below is kept for the
> diagnosis, not as an open item.

**Finding.** Every EV News page declares itself English while the content is Bulgarian:

| Signal | Current value | Should be |
|--------|---------------|-----------|
| `<html lang>` | `en-US` | `bg-BG` |
| `og:locale` | `en_US` | `bg_BG` |
| Yoast schema `inLanguage` (Article, WebPage, WebSite, ImageObject) | `en-US` | `bg` |
| Breadcrumb root label (schema) | `Home` | `Начало` |

This comes from **WordPress → Settings → General → Site Language** being set to *English
(United States)*. Yoast, the theme's `language_attributes()`, and OG output all read that
single setting.

**Why it matters most.** Google uses page language to decide *which* market and queries to
rank you for. Telling Google "this is en-US" while serving Bulgarian to a Bulgarian audience
(GA4 confirms ~95% Bulgaria) creates a language/content mismatch that suppresses ranking for
the exact Cyrillic queries the site should own — and can trigger "translated result" or
wrong-market treatment. It's a one-setting fix with sitewide upside.

**Fix.**
1. Settings → General → **Site Language → Български**. (Verify no other plugin/theme
   constant hardcodes `WPLANG`/locale.)
2. Re-check a post in the [Rich Results Test](https://search.google.com/test/rich-results) —
   `inLanguage` should flip to `bg` and the breadcrumb root to `Начало`.
3. If the admin UI must stay in English, set the **site** language to Bulgarian and set the
   *user* profile language to English instead — those are independent.

> This is #0 because several proposals below (schema, meta descriptions) inherit the wrong
> locale until this is fixed. Do it first, it's ~10 minutes.

---

## 🔴 Priority 1 — Give each page owned, indexable body text

Right now the page's unique editorial value is ~17 words. Three complementary fixes.

### 1.1 Add an episode intro paragraph (the single highest-leverage content change)

Every EV News post should open with **100–150 words of original Bulgarian prose** that
frames the episode: what was covered, the 2–3 biggest stories, and why they matter to a
Bulgarian EV buyer. This text becomes:

- the SERP snippet (Yoast currently has nothing to build a `meta description` from — the
  page ships **no `<meta name="description">` at all** today),
- the `wordCount` Google sees,
- the keyword-bearing prose that lets the page rank for topic queries ("BYD Denza Z цена",
  "нови електромобили България юли 2026") instead of only the branded episode number.

**Where it goes:** the `post_excerpt` (rendered at [single.php:100](../theme/single.php#L100))
is already the top slot — just require it to be filled. This is the cheapest, biggest win.

**Automation opportunity:** the pipeline already runs each article through OpenRouter for
translation and analysis. Add one more LLM step that composes a 120-word Bulgarian episode
intro from the collected article summaries, and write it into `post_excerpt` on publish.
That removes the editorial burden entirely and guarantees every page has owned text.

### 1.2 Store the roundup content in the post, don't fetch it live from CSV

Today the unique summaries live in a **remote CSV fetched at render** with a 5s timeout
([single.php:110-115](../theme/single.php#L110-L115)). Two SEO risks:

- **Fragility:** if that fetch is slow or fails when Googlebot crawls, the page renders with
  **zero** article content — Google may index an empty roundup.
- **Ownership:** content that isn't in `post_content` doesn't count toward the page in the
  way stored content does, and Yoast can't see it (hence `wordCount:17`).

**Fix:** at publish time, persist the roundup (titles + Bulgarian summaries) into
`post_content` (or a serialized meta the template reads locally). Render from the DB; treat
the CSV as the ingestion source, not the render-time source. The page then always has its
content, instantly, owned, and countable.

### 1.3 Surface the AI-translated summaries instead of hiding them

The summaries are the only unique prose, yet they sit in a **collapsed accordion**
(`max-h-0 opacity-0`, revealed by the "Виж още" checkbox —
[card-article-external.php:35](../theme/template-parts/single/card-article-external.php#L35)).
Google can read CSS-hidden text but **discounts it** and it produces weaker engagement
signals.

Options, in order of preference:
- **Show the first 1–2 sentences by default**, collapse only the remainder ("read more"
  pattern) — best of both worlds for UX and crawlers.
- Or expand-by-default on the primary story of each episode.
- At minimum, keep the text in the DOM (it already is) but stop relying on it as the *only*
  prose — 1.1 covers that.

---

## 🟠 Priority 2 — Stop burying the keyword; fix the on-page signals

### 2.1 Lead titles/H1/slug with the topic, not the episode number

Current: title, H1, and URL all begin **`#EV160 -`**. "EV160" is meaningless to searchers
and occupies the most valuable position in the `<title>` (the part Google weights most and
users scan first).

- **SEO title (Yoast template):** put the topic first, episode tag last or dropped:
  `BYD Denza Z – 1582 к.с. електрическа мощ | Car Life by Dani` (keep `#EV160` in the visible
  H1 for brand identity if you like, but not in the SEO title).
- **Slug:** `byd-denza-z-1582-konya-...` already carries the topic — consider dropping the
  `ev160-` prefix on *new* posts (don't rewrite old URLs without redirects).
- This is configurable per-type in **Yoast → Search Appearance → Content Types → Posts**,
  plus a template tweak in how the pipeline sets the Yoast SEO title.

### 2.2 Fix the heading hierarchy (H1 → H5 jump)

The page goes straight from H1 to **H5** for every external article headline — H2/H3/H4 are
skipped entirely ([card-article-external.php:16](../theme/template-parts/single/card-article-external.php#L16)).
That's a broken outline and dilutes the heading signal.

- Make each roundup story an **H2** (or H3 under an H2 section like "Акценти от епизода").
- Add a real H2 above the list, e.g. `## Новините от този епизод`.
- The H5 is currently chosen for font-size — decouple size (CSS) from level (semantics).

### 2.3 Make thumbnails crawlable and give them Bulgarian alt text

Card thumbnails ship with **empty `src`** filled by JS from the source site's OG image
([card-article-external.php:9](../theme/template-parts/single/card-article-external.php#L9)).
Result: invisible to Google Images, no image-SEO value, and dependent on third-party
hotlinks. Alt text exists but is the raw source title.

- If you keep hotloading, at least emit a real `src` server-side where the OG image URL is
  known so it's in the initial HTML.
- Better: store a copy of the episode's key image locally and use it as the featured/OG image
  (some posts already have `evn-160-scaled.jpg` — good; make it consistent).

### 2.4 Add a `meta description` source for every post

There is **no meta description** on these pages today. Once 1.1 (intro paragraph) lands,
Yoast can auto-derive it, but explicitly set the Yoast description template for the EV News
category, or write it from the intro in the pipeline. Target 150 chars, lead with the topic
keyword. This alone lifts CTR on pages that already get impressions.

---

## 🟡 Priority 3 — Schema that matches what the page is

### 3.1 CollectionPage + ItemList (declare "this is editorial curation")

Yoast currently stamps generic `Article` schema on a page that is really a curated list. Add
a `CollectionPage` with an `ItemList` of the roundup items so Google understands the page is
intentional curation, not scraped/thin content. (Full code sketch in the companion doc,
[SEO_PROPOSALS.md §A1](SEO_PROPOSALS.md) — build the item list from the same data source, and
after Priority 1.2 read it from the DB, not a live CSV.)

### 3.2 Consider NewsArticle over Article

For genuinely news-type roundups, `NewsArticle` (a subtype of `Article`) is more precise and
is what Google News / "Top stories" eligibility keys off. If the site ever pursues Google News
inclusion, this + a news sitemap is the path. Lower priority than the content fixes above.

---

## 🟢 Priority 4 — Turn the archive into a topic hub, and add internal links

### 4.1 Interlink episodes to evergreen review content

EV News roundups mention cars the site has full **EV Reviews** for (Toyota bZ4X, MG4, Kia
e-Niro — the site's best organic performers per the analytics snapshot). Add contextual
internal links from the episode intro (1.1) to those review pages. This passes relevance to
the pages that already convert to clicks, and gives the thin roundup pages a reason to exist
in the internal graph.

### 4.2 Give the `/ev-news/` archive a real intro + it's own text

The archive is a bare list (placeholder SVGs, dates, H3 links). Add a short evergreen
paragraph at the top describing what the EV News section is (weekly Bulgarian EV news from the
podcast), so the archive itself can rank for head terms like "новини електромобили".

### 4.3 Cluster by topic/brand tags

The pipeline already assigns tags/region (v1.2.0). Ensure tag archive pages (`#BYD`,
`#Denza`) are indexable and have intro text — they're natural landing pages for brand queries
and turn scattered episodes into topical clusters.

---

## The external-links posture (you're doing this right — keep it)

All outbound article links already carry `rel="nofollow" target="_blank"`
([card-article-external.php:4,17](../theme/template-parts/single/card-article-external.php#L4)).
That's correct for a link roundup and protects you from passing equity to sources. Two refinements:

- `nofollow` is fine; you could also use `rel="nofollow noopener"` for the `_blank` security
  best practice (functional, not SEO).
- The SEO risk of a roundup isn't the nofollow links themselves — it's the **ratio of owned
  text to outbound links**. Priority 1 (owned intro + stored summaries) is what fixes that
  ratio. Don't add `sponsored`/`ugc` — `nofollow` is the right signal for editorial curation.

---

## Suggested order of execution

| # | Proposal | Effort | Impact | Type |
|---|----------|--------|--------|------|
| 0 | Site Language → Bulgarian (fixes en-US everywhere) | 10m | **Critical** | Config |
| 1 | Episode intro paragraph, auto-generated in pipeline (1.1) | 2–3h | **Critical** | Pipeline + editorial |
| 2 | Store roundup content in the post, render from DB (1.2) | 3–4h | High | Pipeline/theme |
| 3 | Topic-first SEO titles + meta description template (2.1, 2.4) | 1–2h | High | Yoast + pipeline |
| 4 | Fix heading hierarchy H2/H3 (2.2) | 1h | Medium–High | Theme |
| 5 | Show first sentences of summaries by default (1.3) | 1h | Medium | Theme |
| 6 | CollectionPage + ItemList schema (3.1) | 1.5h | Medium | Theme |
| 7 | Crawlable thumbnails + alt text (2.3) | 1–2h | Medium | Theme/pipeline |
| 8 | Internal links to reviews + archive intro (4.1, 4.2) | 2h | Medium | Editorial/theme |
| 9 | Tag-archive intros / clustering (4.3) | 2h | Low–Medium | Editorial |

**Biggest levers:** #0 (language) and #1 (owned intro text) together address the root cause —
these pages currently give Google almost nothing in their own language to rank. Everything
else compounds on top of those two.

---

## Validate after changes
- [Rich Results Test](https://search.google.com/test/rich-results) — confirm `inLanguage:bg`,
  CollectionPage/ItemList detected, no duplicate schema vs Yoast.
- Search Console → URL Inspection on an EV News URL — confirm the rendered HTML contains the
  intro text and summaries (not an empty CSV-failed render).
- Re-pull `wordCount` from the page's Yoast schema — it should climb from 17 to 120+.
