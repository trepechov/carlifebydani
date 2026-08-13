# SEO Optimization — /ev-news/ category archive (the hub)

**URL:** https://www.carlifebydani.com/ev-news/
**Term:** category id **1** (`ev-news`), 128 posts · **Prepared:** 2026-08-13
**Status:** **partially applied 2026-08-13.** Term `description` written and verified live.
**`<meta name="description">` is still absent** — see "What the write actually did" below.
The Yoast term title + metadesc still require wp-admin.

## What this page is

The EV News category archive: a paginated list of all 128 episode posts. Not an article — a
`CollectionPage`. Its H1 is the literal string **`EV News`** (English, on a `bg-BG` site) and
the term `description` field is **empty**, so the page has no owned prose whatsoever.

## Current state

| | Value |
|---|---|
| `<title>` | `EV News - Car Life by Dani` |
| `<meta name=description>` | **absent** |
| Term `description` | **empty** |
| H1 | `EV News` |
| Yoast term meta (`_yoast_wpseo_*`) | **not exposed over REST** — `GET /wp/v2/categories/1` returns `meta: []` |
| Schema | `CollectionPage`, `inLanguage: bg-BG` — correct |

## Demand research

**GSC — this URL, 2026-05-15 → 2026-08-12 (90d):**
**96 impressions · 2 clicks · 2.08% CTR · avg position 9.5**

| Query | Impr | Clicks | Pos |
|---|---|---|---|
| `carlifebydani` | 12 | 0 | 5.1 |
| `carlife by dani` | 8 | 0 | 5.5 |
| `car life by dani` | 7 | 0 | 12.1 |
| `evnews` | 6 | 1 | 4.5 |
| `car life` | 4 | 0 | 1.8 |
| `ev news` | 3 | 0 | 5.0 |
| `carlife` | 2 | 0 | 1.5 |
| `clbd` | 2 | 0 | 1.0 |
| `ev info` | 1 | 0 | 9.0 |

**Every query is brand or navigational.** Not one is a topical EV query.

### The `EV новини` premise does not survive the data

`docs/SEO_EV_NEWS_TODO.md` records this page as "the one place where `EV новини` *is* the
right keyphrase". A property-wide scan for any query containing `новини` returns exactly
**one row**:

| Query | Page | Impr | Pos |
|---|---|---|---|
| `тесла новини` | `/tag/tesla/` | 5 | 6.6 |

That is the total measured demand for "новини" phrasing across the entire site — 5
impressions, and they land on a tag page, not here. **Targeting `EV новини` on this hub would
be chasing a phrase with no demonstrated demand in this market.** Recommend striking that
line from the backlog.

### What this page is actually caught up in: brand dilution

The brand queries this hub ranks for are split across a lot of URLs. For `clbd` alone:

| URL | Impr | Clicks | Pos |
|---|---|---|---|
| `/tag/clbd/` | 418 | 4 | 3.2 |
| `/` (homepage) | 269 | **193** | 1.0 |
| `/clbd-parts/` | 259 | 7 | 1.0 |
| `/za-nas/` | 215 | 0 | 1.0 |
| `/publications/` | 207 | 2 | 1.0 |
| `/promo-kodove/` | 190 | 3 | 1.0 |
| `/calendar/` | 133 | 0 | 1.0 |

And for `car life by dani`: `/` 268 impr / 139 clicks, `/za-nas/` 249 impr / **1** click,
`/tag/podcast/` 116 impr / 0 clicks.

The homepage converts brand traffic properly. Six-plus other URLs absorb impressions at
roughly 0% CTR — **`/tag/clbd/` at 418 impressions and 4 clicks is the worst single offender**,
a thin tag page outranking real pages on the site's own brand name. `/ev-news/` (96 impr) is a
minor participant in this, not the cause.

## Recommendation

**Do not assign a topical focus keyphrase to this page.** It is a brand-navigational surface,
and the honest job here is (a) give it owned text, and (b) stop Google auto-generating its
snippet from a bare list of episode titles.

**The write path is the problem.** Yoast stores taxonomy SEO in the `wpseo_taxonomy_meta`
*option*, not in term meta — which is why `GET /wp/v2/categories/1` returns `meta: []`.
Writing it requires `manage_options`, and `seo-bot` does not have that capability. So the
Yoast SEO title and meta description for this term **cannot be set over REST**.

Two available routes:

**Route A — write the term `description` field (REST-writable by an Editor).**
Yoast falls back to the term description for the meta description when no explicit Yoast
metadesc is set. This would give the page both owned prose and a real `<meta name=description>`
in one write. **Caveat: the term description usually also renders visibly on the archive page**,
depending on the theme — so this is a content change, not a silent metadata change, and it
should be eyeballed after writing.

Proposed value (140 chars, sized to work as a meta description):

> `EV News е седмичното предаване на Car Life by Dani – новините за електромобили от България и света, обобщени в кратки епизоди всяка седмица.`

**Route B — set the Yoast term SEO in wp-admin** (Posts → Categories → EV News → edit → Yoast
box). Requires you, not the bot. This is the only way to control the SEO **title**, which is
currently the English `EV News - Car Life by Dani`. A Bulgarian title would serve this market
better.

### What the write actually did — Route A only half worked

Applied 2026-08-13 via `POST /wp/v2/categories/1 {"description": …}`. Verified on the live page:

| Outcome | Result |
|---|---|
| Term `description` persisted | ✅ |
| Renders **visibly** on the archive page (under the H1) | ✅ — the page now has owned prose for the first time |
| `og:description` populated | ✅ — was absent before |
| **`<meta name="description">`** | ❌ **still absent** |

**So the Yoast metadesc fallback assumption was wrong.** Yoast v28.2 on this install uses the
term description for `og:description` but does **not** emit it as `<meta name="description">`
for a taxonomy archive. Record this: writing the term description is *not* a substitute for
setting the Yoast term metadesc.

**Route B is therefore still required.** Paste these in Posts → Categories → EV News → Yoast:

- **SEO title:** `EV News – новини за електромобили %%sep%% %%sitename%%`  (→ 52 chars rendered)
- **Meta description:** `EV News е седмичното предаване на Car Life by Dani – новините за електромобили от България и света, обобщени в кратки епизоди всяка седмица.`  (140 chars)

## On-page changes (need a human)

- [ ] **H1 is `EV News` — English, on a Bulgarian site.** Consider
      `EV News – новини за електромобили` in the theme's archive template.
- [ ] **Yoast SEO title for the term** (Route B, wp-admin only).
- [ ] **`/tag/clbd/`** — 418 brand impressions at 4 clicks. Not this page's problem, but it is
      the biggest single brand-dilution item found; worth a `noindex` decision on thin tag pages.

## Risks / notes

- Upside here is **small and mostly defensive**: 96 impressions, brand-only, and the homepage
  already captures brand intent properly. Do not expect ranking movement — expect a cleaner
  SERP snippet and a page that finally has words on it.
- Writing the term description is a **visible** change. Verify the archive page after the write.
- Striking `EV новини` from the backlog is the more valuable outcome of this analysis than
  anything written to the page.

## Measurement

Baseline (GSC, 2026-05-15 → 2026-08-12): **96 impressions · 2 clicks · 2.08% CTR · pos 9.5**.
Brand-query CTR is the only thing to watch, and it is noisy at this volume.
