# EV News SEO — Action Backlog

Created 2026-08-13. Working doc for the on-site SEO push on EV-News episode pages.
Companion to [SEO_EV_NEWS_PROPOSALS.md](SEO_EV_NEWS_PROPOSALS.md) (the 2026-07-28 deep-dive
that diagnosed root causes). This file tracks *what to do next*, in priority order.

Status legend: `[ ]` not started · `[~]` in progress · `[x]` done · `[?]` needs a decision

---

## Baseline as of 2026-08-13

**Yoast meta coverage across the EV News category (cat id 1, 128 posts):**

| | count |
|---|---|
| Posts with focus keyphrase + SEO title + meta description | **1** (post 9248) |
| Posts with all three fields empty | **127** |

**Search performance, 2026-05-15 → 2026-08-10 (GSC, `https://www.carlifebydani.com/`):**

- 28 of 128 episode pages earn any impressions
- **2,067 impressions → 65 clicks → 3.1% CTR** across those 28
- Top 15 pages hold ~90% of the impressions
- ~100 pages have no measurable search visibility at all

Caveat: the GSC page query hit its 250-row cap. Rows come back ordered by clicks
descending, so pages with impressions but **zero** clicks may be missing from the list
below. Re-run with a page-filtered query before treating this as exhaustive.

---

## ✅ Completed so far

Verified 2026-08-13 by reading `_yoast_wpseo_*` live over REST — this table is the
authoritative record, not the checkboxes scattered through the sections below.

| Date | Target | What was written | Baseline to beat (GSC 90d) | Proposal |
|---|---|---|---|---|
| 2026-08-13 | **9248** `evn-161-…tesla-i-spacex` | all 3 Yoast fields | — (too new to have data) | — (done before the skill existed) |
| 2026-08-13 | **7333** `ev114-…cybertruck-v-blgariya` | all 3 Yoast fields | 542 impr / 22 clicks / 4.06% / pos 7.0 | [7333](../reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md) |
| 2026-08-13 | **1751** `evn41-tesla-my-juniper-…` | all 3 Yoast fields | 65 impr / 3 clicks / 4.62% / pos 2.7 | [1751](../reports/seo-metatags/2026-08-13-1751-evn41-juniper.md) |
| 2026-08-13 | **category 1** `/ev-news/` hub | term `description` (bot-side complete) | 96 impr / 2 clicks / 2.08% / pos 9.5 | [hub](../reports/seo-metatags/2026-08-13-hub-ev-news-archive.md) |
| 2026-08-14 | **7333** `ev114-…cybertruck-v-blgariya` | **`post_content`** — 154 words of transcript-grounded Bulgarian prose at the bottom + 2 internal links + **8 tags**. `wordCount` **17 → 168**. **First page on the site to answer its own title question.** | 542 impr / 22 clicks / 4.06% / pos 7.0 | [report](../reports/seo-metatags/2026-08-14-7333-excerpt-draft.md) |

**That is 3 posts + 1 archive. Everything else in P1 below still has all three Yoast
fields empty** — confirmed live, not inferred.

> ⚠️ **The hub row is complete only as far as the bot can go.** Yoast taxonomy SEO is not
> REST-writable, so `/ev-news/` still has **no `<meta name="description">`**. Finishing it
> needs one paste in wp-admin — see "P1 — the hub page itself" below.

> **Note on selection:** 1751 and the hub were picked as *skill test* candidates (1751 for
> the fastest CTR feedback at position 2.7), **not** by traffic. The genuine top 3 by
> impressions is 7333 → 5240 → 8026, and **5240 and 8026 are still open.**

### Highest-value work still untouched

| Target | Impr | CTR | Pos | Why |
|---|---|---|---|---|
| **6165** `/publications/noviyat-tesla-model-y-juniper-2025-…` | **3,927** | **2.06%** | 5.3 | Biggest single CTR loss in the property; owns every Juniper query (`тесла джунипер` pos **1.4**) |
| **7533** `/ev-review/tesla-cybertruck-…` | 237 | **0.84%** | 6.2 | Owns the Cybertruck spec cluster |
| **5240** `evn67-novi-spekulaczii-okolo-tesla-model-y…` | 212 | **0.9%** | 10.9 | #2 in the P1 list |
| **8026** `ev133-izminalata-2025-rekordni-prodazhbi…` | 204 | **0.9%** | 7.4 | #3 in the P1 list |

~4,580 impressions converting at roughly 1%. **6165 alone is worth more than the entire
P1 list below combined** — and neither it nor 7533 is in that list, because the original
baseline scan only covered the EV-News category.

---

## P1 — Meta descriptions for pages already ranking (striking distance)

These rank on or near page 1 and convert almost none of it. Missing `<meta name="description">`
means Google auto-generates a snippet from a page whose owned text is ~17 words. This is the
highest-confidence, fastest-feedback work: CTR change shows in GSC within 2–4 weeks.

Method: run the [`seo-article-optimize`](../.claude/skills/seo-article-optimize/SKILL.md) skill
on the page's URL — it does the research, writes a proposal to `reports/seo-metatags/`, backs up
the current meta, and applies the Yoast fields after you approve. Manually, it is the same as
post 9248 — back up current meta to
`reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv` first, write `_yoast_wpseo_focuskw` /
`_yoast_wpseo_title` / `_yoast_wpseo_metadesc` via the WordPress MCP, then fetch the rendered
page to verify. Yoast title template vars work — write `"<title> %%sep%% %%sitename%%"`.

Do **not** target "EV новини" / "новини за електромобили" on episode pages — that cannibalises
the `/ev-news-feed/` hub. Each episode should own its distinctive story.

| | Post ID | Slug | Impr | CTR | Pos |
|---|---|---|---|---|---|
| [x] | 7333 | `ev114-ima-li-registriran-tesla-cybertruck-v-blgariya` | 530 | 4.1% | 6.9 |
| [ ] | 5240 | `evn67-novi-spekulaczii-okolo-tesla-model-y-i-predstavitelstvo-v-blgariya` | 212 | 0.9% | 10.9 |
| [ ] | 8026 | `ev133-izminalata-2025-rekordni-prodazhbi-na-novi-avtomobili-v-blgariya` | 204 | 0.9% | 7.4 |
| [ ] | 6898 | `ev103-byd-navliza-v-rumniya-s-brzoto-razshiryavane-na-trgovskata-si-mrezha` | 138 | 0.7% | 7.7 |
| [ ] | 8721 | `ev152-ferrari-imame-elektrichesko-ferari-mercedes-iskame-da-schupim-fizikata` | 99 | 1.0% | 7.2 |
| [ ] | 7472 | `ev120` | 98 | 4.0% | 8.0 |
| [ ] | 5673 | `evn79-kia-ev2-niskobyudzhetna-kola-otnovo` | 97 | 2.0% | 8.8 |
| [ ] | 7367 | `ev117-mercedes-glc-eq-2026-vs-bmw-ix3-2026` | 95 | 2.1% | 6.8 |
| [x] | 1751 | `evn41-tesla-my-juniper-se-otlaga-za-nqkolko-meseca` | 65 | 4.6% | **2.7** |
| [ ] | 5343 | `evn70-hyundai-ioniq-6n-novi-danni` | 65 | 4.6% | 9.3 |
| [ ] | 8319 | `ev144-byd-veche-e-v-blgariya` | 54 | 1.8% | 7.1 |
| [ ] | 5574 | `evn75-organizacziyata-na-naj-golyamoto-svetlinno-shou-v-blgariyaevn75` | 52 | 5.7% | 6.8 |
| [ ] | 7984 | `ev131-edin-gigacast-po-ksno` | 30 | 6.6% | 5.4 |
| [ ] | 7899 | `ev130-kak-se-zhivee-s-lucid-air-saphire` | 30 | 3.3% | 6.5 |
| [x] | 9248 | `evn-161-ilon-mask-zagatna-za-slivane-mezhdu-tesla-i-spacex-osthe-tazi-godina` | — | — | — |

Notes on individual rows:

- **7333 — DONE 2026-08-13.** Metatags written and verified live; proposal +
  full research in [`reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md`](../reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md).
  90-day baseline to beat: **542 impr / 22 clicks / 4.06% CTR / pos 7.0**.
- **7333 — content fix DONE 2026-08-14.** 154 words of transcript-grounded Bulgarian prose
  appended to `post_content` + 8 tags; `wordCount` **17 → 168**. It is now the first page on
  the site that answers its own title question. Report:
  [`2026-08-14-7333-excerpt-draft.md`](../reports/seo-metatags/2026-08-14-7333-excerpt-draft.md).
- **1751 — DONE 2026-08-13.** Metatags written and verified live; proposal in
  [`reports/seo-metatags/2026-08-13-1751-evn41-juniper.md`](../reports/seo-metatags/2026-08-13-1751-evn41-juniper.md),
  backup in [`reports/yoast-meta-backup/1751-2026-08-13.csv`](../reports/yoast-meta-backup/1751-2026-08-13.csv)
  (all three Yoast fields were empty pre-write). Baseline to beat: **65 impr / 4.6% CTR / pos 2.7**.

### Missing from this list — a bigger CTR prize than anything above

Neither of these two has run through `seo-article-optimize` yet. Per `SEO_SKILLS_REFACTOR.md`
§W6, both categories (`publications`, `ev-review`) already have real body content, so the
pipeline runs Phase A → Phase C only — no transcript gate, no Phase B. **Start with these two
before scanning the other ~183 unscanned `publications`/`ev-review`/`ev-masters` posts** — a
full site-wide baseline re-scan is deferred, not done, as of 2026-08-14 (cost/time tradeoff,
see `SEO_SKILLS_REFACTOR.md` §W6).

- [x] **Post 6165** `/publications/noviyat-tesla-model-y-juniper-2025-…` — **DONE 2026-08-14.**
      Original estimate cited 3,927 impressions/2.06% CTR/pos 5.3; a fresh URL-filtered pull
      found 90d: 2,200 impr/30 clicks/1.36% CTR, 28d baseline (ledger): 996 impr/23 clicks/
      2.31% CTR/pos 5.2. Owns `тесла джунипер` at pos 1.5 but only 1.41% CTR — missing metadesc
      was the clear cause. Metatags + tags + image alt/title applied; proposal + full research
      in [`reports/seo-metatags/2026-08-14-6165-tesla-model-y-juniper.md`](../reports/seo-metatags/2026-08-14-6165-tesla-model-y-juniper.md).
      Focus keyphrase `Tesla Model Y Juniper` (striking-distance cluster, pos 7.5–8.3, ~600
      combined impressions, ~0% CTR). Flagged for the record: post 6294 (`#EV90`, same event,
      near-identical title) is dormant (1 impression/90d) — do not target this phrase if 6294
      is ever optimized.
- [x] **Post 7533** `/ev-review/tesla-cybertruck-moshh-inovacziya-i-dizajn-bez-graniczi-zvyart-ot-bdeshheto-veche-e-tuk/`
      — **DONE 2026-08-14.** Original estimate cited 237 impr/0.84% CTR/pos 6.2; fresh pulls
      found 28d baseline (ledger): 68 impr/1 click/1.47% CTR/pos 4.8, and 90d query-level:
      zero clicks across every named query at pos 4.8–9.4 despite real impressions — the
      clearest presentation-not-ranking case found this session. Inbound link to 7333 (W5,
      earlier), metatags + tags + image alt/title all applied; full research in
      [`reports/seo-metatags/2026-08-14-7533-cybertruck-review-inbound-link.md`](../reports/seo-metatags/2026-08-14-7533-cybertruck-review-inbound-link.md).
      Focus keyphrase `Tesla Cybertruck характеристики` — owns the spec/`кибертрак` intent
      cluster (`тесла cybertruck` pos 5.6, `тесла кибертрак` pos 9.4); confirmed no overlap
      with 7333's `Tesla Cybertruck България`.
- **1751** sat at position **2.7** with 4.6% CTR — position 2–3 should pull 10–15%. Addressed
  2026-08-13 (see note above); watch GSC in 2–4 weeks to see whether the snippet was the cause.
- **6898** at 0.7% CTR is the worst ratio in the set.

## P1 — the hub page itself

- [x] `/ev-news/` category archive — **bot-side complete 2026-08-13**: **96 impressions,
  2.0% CTR, position 9.5.** Proposal → [`reports/seo-metatags/2026-08-13-hub-ev-news-archive.md`](../reports/seo-metatags/2026-08-13-hub-ev-news-archive.md).
  Term `description` written and verified live; the archive now has owned prose and an
  `og:description` for the first time.
  - [ ] **One manual step remains** (not doable over REST): the Yoast term SEO title +
    meta description. The archive still has **no `<meta name="description">`.
  - ~~"this is the one place where 'EV новини' *is* the right keyphrase"~~ — **struck 2026-08-13,
    the data does not support it.** A property-wide scan for any query containing `новини`
    returns exactly **one row**: `тесла новини`, 5 impressions, landing on `/tag/tesla/`. There
    is no measurable "EV новини" demand in this market. All 11 queries this hub actually ranks
    for are brand/navigational (`carlifebydani`, `carlife by dani`, `evnews`, `clbd`).

### Blockers and gotchas found while doing it

- **Yoast taxonomy SEO is NOT writable over REST.** `GET /wp/v2/categories/1` returns
  `meta: []` — Yoast stores term SEO in the `wpseo_taxonomy_meta` *option*, which needs
  `manage_options`; `seo-bot` doesn't have it. Only wp-admin can set a category's SEO title
  and meta description.
- **Writing the term `description` is not a workaround.** Tested live on Yoast v28.2: the term
  description populates `og:description` and renders visibly on the archive page, but does
  **not** emit `<meta name="description">`. The archive still has no meta description.
- **To finish the hub**, paste into Posts → Categories → EV News → Yoast box:
  - SEO title: `EV News – новини за електромобили %%sep%% %%sitename%%` (52 rendered; the
    current title is the English `EV News - Car Life by Dani`)
  - Meta description: `EV News е седмичното предаване на Car Life by Dani – новините за електромобили от България и света, обобщени в кратки епизоди всяка седмица.`

## P1 — brand-query dilution (new, found 2026-08-13)

The brand query `clbd` is split across seven-plus URLs. The homepage converts it properly;
the rest absorb impressions at ~0% CTR:

| URL | Impr | Clicks | Pos |
|---|---|---|---|
| `/tag/clbd/` | **418** | **4** | 3.2 |
| `/` | 269 | **193** | 1.0 |
| `/clbd-parts/` | 259 | 7 | 1.0 |
| `/za-nas/` | 215 | 0 | 1.0 |
| `/publications/` | 207 | 2 | 1.0 |
| `/promo-kodove/` | 190 | 3 | 1.0 |
| `/calendar/` | 133 | 0 | 1.0 |

- [ ] **`/tag/clbd/` — 418 brand impressions, 4 clicks.** A thin tag page outranking real pages
      on the site's own brand name, and the single worst offender. Decide `noindex` on thin tag
      pages. (Consistent with the competitor-gap finding that `/tag/` pages outrank editorial
      content here.)

---

## P2 — Duplicate / cannibalising URLs

Verified present in the post list as live posts alongside their originals:

- [ ] `clone-ev155-216-rst-kakvo-se-sluchva-s-ev-pazara-v-blgariya` (8824) vs `ev154-...` (8812)
- [ ] `clone-ev140-kak-mina-clbd-coffee-day-1-2026` (8187)
- [ ] `clone-ev106-bez-sinya-i-zelena-zona-v-sofiya` (7013) vs `ev106-...` (7007)
- [x] `ev122` (7584) and `ev122-zeekr-v-blgariya` (7577) — two posts, same episode number —
      **RESOLVED, verified 2026-08-15.** 7584 is no longer an EV122 duplicate: its live slug is
      now `ev123-imame-nova-sluzhebna-kola-...` (title `#EV123`, published 2025-10-21, a week
      after 7577). A site search for `ev122` now returns only 7577 (slug
      `ev122-zeekr-v-balgariya`). No action taken by this pipeline — already fixed upstream
      before this check; noting so 7577 is clear to run through `/seo-article-optimize` without
      a cannibalization blocker.
- [ ] `ev80-rivian-i-vw-obedinyavat-sili-za-po-dobro-badeshte` (5742) and
      `ev80-audi-startira-nova-marka-v-kitai-zaedno-sys-saic` (5691) — same number, different stories
- [ ] `ev90-tesla-model-y-juniper-2025-be-predstaven-v-evropa` (6294) and
      `ev91-tesla-model-y-juniper-2025-be-predstaven-v-evropa` (6350) — identical titles
- [ ] `ev103-...` (6898) and `ev104-byd-navliza-v-rumniya-...` (6906) — identical titles,
      and 6898 is in the P1 list above. Resolve the duplicate *before* writing its meta.

**URLs earning impressions that are NOT in the current post list** — these need checking for
404s or missing redirects after a slug change:

- [ ] `clone-ev153-eldrive-fest-2026-nie-shhe-sme-tam` — 29 impr, 6.9% CTR, pos 4.3
- [ ] `clone-ev152-ferrari-imame-elektrichesko-ferari-...` — 12 impr, pos 6.2
- [ ] `clone-ev150-watts-on-the-grill-2026-...` — 6 impr, pos 8.2
- [ ] `ev147` (bare) — 22 impr, pos 10.9, while the live post is `ev147-mercedes-benz-c-klasa-...` (8351)

Decision needed per URL: 301 redirect to the canonical post, set a canonical tag, or delete.
Do not bulk-delete — some of these hold better positions than their originals
(`clone-ev153` at position 4.3 outranks a lot of the P1 list).

---

## P3 — Root cause: thin content

From the deep-dive. Nothing above fixes this; meta descriptions only change how a page is
*presented*, not whether it deserves to rank. This is what would help the ~100 pages with no
visibility at all.

- [ ] **Persist AI-translated summaries into `post_content`.** Currently fetched from a remote
      CSV at render time ([theme/single.php:110-115](../theme/single.php#L110-L115)) — fragile
      (empty page if the fetch fails) and invisible to Yoast and to Google's indexer.
- [~] **Auto-generate a 100–150 word Bulgarian episode intro** appended to `post_content`.
      Biggest single content lever. **Shipped as the `ev-news-transcript-content` skill**,
      grounded in the transcript archive via the `youtube-rag` MCP rather than composed from
      the article summaries — proven on post 7333 (2026-08-14); see
      [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md).
      91 of 128 posts have their transcript ingested. **Resolved: per-post skill, gated on
      transcript availability** (`SEO_SKILLS_REFACTOR.md` §W12) — not a two-phase pipeline.
  - **Use `post_content`, not `post_excerpt`** (settled 2026-08-14). The excerpt slot renders
    poorly for this post type and Yoast derives `wordCount` from `post_content` only — 154
    words in the excerpt left it at 17; the same words in the content took it to **168**.
    Never write both fields (duplicate text on the page).
  - ⚠️ **Tags auto-link into the prose.** [`theme/functions.php:75`](../theme/functions.php#L75)
    links each post tag up to **5×** wherever its name appears in `the_content`. 8 tags on 7333
    injected **10 `/tag/` links into 154 words**. Decide: lower the cap to 1, or `noindex` the
    thin tag archives (already a P1 item below — this makes it more urgent).
- [ ] Summaries are hidden in a collapsed accordion (`max-h-0 opacity-0`, "Виж още").
- [ ] Heading structure jumps H1 → H5.
- [ ] Card thumbnails ship with empty `src`, JS-hotloaded from the source OG image.
- [ ] Titles/H1/slugs lead with `#EV160` — wastes the first characters of the SERP title.
      Post 9248 shows the fix: strip the prefix in `_yoast_wpseo_title`.

## P0 — verify this was actually fixed

- [x] **Site Language — FIXED (verified 2026-08-13).** The deep-dive found pages served as
      `en-US` (`<html lang>`, `og:locale`, Yoast `inLanguage`, breadcrumb "Home") while content
      is Bulgarian — caused by WP Settings → General → Site Language set to English (US).
      Re-checked on post 9248's live page: `<html lang="bg-BG">`, `og:locale=bg_BG`,
      schema `inLanguage="bg-BG"`. All three correct. `seo-bot` lacks `manage_options`, so this
      was verified by fetching the rendered page with `curl`, not over REST.

---

## Operational notes

- **WordPress revisions do NOT cover postmeta.** An overwritten meta description is
  unrecoverable. Always export to `reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv` first.
- **WAF gotcha:** the site 403s Python's default `urllib` User-Agent even with valid Basic
  auth; identical `curl` requests succeed. A 403 here is not a credentials problem.
- **Unauthenticated REST reads hide the Yoast fields** (`meta` shows only `footnotes`). That
  looks exactly like "the fields aren't registered" and is a trap. Authenticate.
- **Teardown when this push ends:** `claude mcp remove wordpress`,
  `security delete-generic-password -a "$USER" -s carlifebydani-wp-mcp`, then revoke the
  app password in the `seo-bot` profile. A standing production write credential shouldn't linger.
- ~~`reports/yoast-meta-backup/` is currently untracked — decide gitignore vs commit.~~
  Resolved — committed (56 files tracked as of 2026-09-04, not gitignored).

## Measuring whether any of this worked

Re-run the `seo-performance-report` skill and compare CTR on the P1 pages specifically.
Baseline to beat: **2,067 impressions / 65 clicks / 3.1% CTR** over a 3-month window.
Give it 2–4 weeks after the writes for Google to re-crawl and for GSC to accumulate data.
