# Site-Wide SEO — Action Backlog (non-EV-News categories)

Created 2026-09-04. This is **W6** from [`SEO_SKILLS_REFACTOR.md`](SEO_SKILLS_REFACTOR.md#w6--generalise-beyond-ev-news--3h--partial--done-2026-08-14)
— the full GSC baseline scan across `publications`, `ev-review` and `ev-masters` that was
deferred on 2026-08-14 for cost reasons. Run today because DataForSEO's `40104` block cleared
the same day, which was the condition the deferral was waiting on (see Open Question 5 in that
doc). Companion to [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md), which stays EV-News-only —
the name there no longer fit once this scan covered the other three categories, so it's split
out here rather than folded in.

Status legend: `[ ]` not started · `[x]` done (per `reports/seo-optimizations/ledger.csv`)

---

## Method

GSC page-level query, `dimensions=page`, `row_limit=25000` (no 250-row cap this time — the
original EV News baseline hit that cap and undercounted as a result), window
**2026-06-04 → 2026-09-01** (90 days). Rows matched to category by URL path prefix
(`/publications/`, `/ev-review/`, `/ev-masters/`), not by post ID — cheaper than paginating
every post list through GSC one at a time, at the cost of possibly including a stale/redirected
URL that no longer resolves to a live post in the category. Category archive pages themselves
(`/publications/`, etc.) are excluded from the rankings below — they're brand-query traffic,
already tracked in `SEO_EV_NEWS_TODO.md`'s brand-dilution table, not article demand.

## Baseline — the scale finding

| Category | Posts | Pages w/ impressions (90d) | Impressions | Clicks | Avg CTR |
|---|---|---|---|---|---|
| `publications` | 121 | 125 | **102,740** | 2,127 | 2.07% |
| `ev-review` | 41 | 45 | **34,258** | 748 | 2.18% |
| `ev-masters` | 23 | 25 | 3,900 | 109 | 2.79% |
| **Total (these 3)** | **185** | **195** | **140,898** | **2,984** | **2.12%** |

For comparison, `ev-news`'s own 90-day total is 2,708 impressions / 67 clicks across 128 posts.
**These three "never scanned" categories carry ~52× the search demand of EV News combined** —
`SEO_SKILLS_REFACTOR.md`'s own audit undersold this by citing single posts (6165, 7533) as the
prize; the prize is the categories themselves. `publications` alone outweighs everything the
pipeline has touched to date by a wide margin.

Pages-w/-impressions counts exceeding post counts (125 > 121, 45 > 41, 25 > 23) mean a handful
of matched URLs are clones, redirects, or historical slugs no longer live — expected given the
prefix-matching method above, not a sign of hidden inventory. Not reconciled row-by-row here;
treat the ranking below as directional, verify the specific post exists before optimizing it.

## Highest-value work — ranked by impressions, top 30

Method mirrors `SEO_EV_NEWS_TODO.md`'s P1 list: run [`seo-article-optimize`](../.claude/skills/seo-article-optimize/SKILL.md)
on the post. Per W6's audit, `publications` and `ev-review` posts already have real body content
(Phase B/transcript-content is skipped, EV-News-only) — so for these, Phase A → Phase C only.

| | Post ID | Category | Slug | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|---|---|---|
| [x] | 3957 | `publications` | `domashno-zarezhdane-na-elektromobili` | 11843 | 268 | 2.26% | 7.0 |
| [ ] | 2800 | `publications` | `mg4-priemlivo-kachestvo-na-razumna-cena` | 9605 | 205 | 2.13% | 5.7 |
| [ ] | 200 | `ev-review` | `toyota-bz4x-tromava-iznenada-izvan-putya` | 9256 | 159 | 1.72% | 6.9 |
| [ ] | 6280 | `publications` | `mhero-917-luksozen-vsedehod` | 9039 | 130 | 1.44% | 5.9 |
| [x] | 9099 | `ev-review` | `zeekr-7x` | 7617 | 133 | 1.75% | 6.2 |
| [ ] | 3474 | `publications` | `mg-zs-ev-kompakten-gradski-suv` | 7449 | 203 | 2.73% | 6.4 |
| [ ] | 6889 | `publications` | `renault-5` | 7426 | 52 | **0.70%** | 7.3 |
| [ ] | 7037 | `publications` | `stancziite-na-tesla-v-blgariya-veche-sa-plateni` | 5107 | 180 | 3.52% | 6.2 |
| [x] | 6165 | `publications` | `noviyat-tesla-model-y-juniper-2025-be-predstaven-v-evropa` | 3519 | 76 | 2.16% | 5.2 |
| [ ] | 420 | `ev-review` | `mercedes-eqs-580-4matic-komfort-na-vsyaka-tsena` | 3417 | 46 | 1.35% | 6.2 |
| [ ] | 765 | `publications` | `tesla-model-s-plaid` | 3375 | 19 | **0.56%** | 7.1 |
| [ ] | 1090 | `ev-review` | `kia-e-niro-pokupkata-parvite-2-godini-elektromobil` | 3097 | 180 | 5.81% | 6.0 |
| [ ] | 5946 | `publications` | `microlino` | 2940 | 100 | 3.40% | 5.6 |
| [ ] | 7629 | `publications` | `ford-e-transit-courier-i-e-tourneo-courier-elektricheskite-bliznaczi-za-gradski-zadachi` | 2897 | 53 | 1.83% | 7.0 |
| [ ] | 8643 | `publications` | `kak-da-karash-po-bezopasno-pozicziya-spirane-i-pravilna-podgotovka` | 2734 | 32 | 1.17% | 6.7 |
| [ ] | 7833 | `publications` | `citroen-e-c3-novoto-licze-na-dostpnata-elektricheska-mobilnost` | 2564 | 39 | 1.52% | 7.3 |
| [ ] | 4845 | `ev-review` | `baw-pony-dostpen-kitajskite-gradski-avtomobil-s-mnogo-ekstri` | 2139 | 63 | 2.95% | 7.1 |
| [ ] | 3366 | `publications` | `stark-varg-veche-se-predlaga-v-blgariya` | 2057 | 22 | 1.07% | 7.2 |
| [ ] | 487 | `publications` | `statistika-na-kat-za-2023-blgarskiyat-avtomobilen-pazar` | 2040 | 36 | 1.76% | 4.8 |
| [ ] | 7407 | `publications` | `renault-4-e-tech-electric-pogled-v-minaloto-zadvizhvan-ot-bdeshheto` | 1465 | 11 | **0.75%** | 7.3 |
| [ ] | 1544 | `publications` | `tesla-m3-highland-kakvi-sa-razlikite-mezhdu-sr-i-lr` | 1421 | 31 | 2.18% | 5.3 |
| [ ] | 1088 | `ev-review` | `mazda-mx-30-suv-grada-malka-bateriya-bavno-zarejdane` | 1391 | 31 | 2.23% | 5.9 |
| [ ] | 3550 | `publications` | `rimac-nevera-zavladyavashhata-elektricheska-superkola` | 1351 | 33 | 2.44% | 5.6 |
| [ ] | 7189 | `publications` | `hyundai-ioniq-6-n-po-brz-po-sporten-po-radikalen` | 1338 | 5 | **0.37%** | 7.6 |
| [ ] | 724 | `publications` | `istoriyata-na-tesla-model-s` | 1262 | 13 | 1.03% | 6.6 |
| [ ] | 6664 | `publications` | `skeptichen-byah-dokato-ne-testvah-mgs5-ev-2025` | 1229 | 87 | 7.08% | 5.1 |
| [ ] | 188 | `ev-masters` | `hristo-bachvarov-dalboko-v-tehnicheskite-tayni-bmw-i3` | 1162 | 14 | 1.20% | **2.6** |
| [ ] | 2648 | `publications` | `lucid-air-luksozen-elektromobil-bez-kompromisi` | 1086 | 21 | 1.93% | 7.1 |
| [ ] | 130 | `ev-review` | `vw-id4-gtx-vpechatlyavashto-komforten-stilen-praktichen` | 1072 | 9 | **0.84%** | 7.3 |
| [ ] | 3828 | `publications` | `kak-da-smenim-dvgto-s-elektromobil` | 988 | 26 | 2.63% | 10.4 |

**Bold CTR** = worst ratio in the set relative to its position (page-1 position, sub-1% CTR —
the same "presentation, not ranking" pattern the EV News P1 list was built to catch).
**Bold position** = 188 (`hristo-bachvarov...bmw-i3`) sits at position **2.6** with only 1.2%
CTR — position 2–3 should pull 10–15%; this is the single clearest snippet-fix candidate in the
whole scan, same shape as `1751` was for EV News.

### Two rows already done, cross-referenced from `SEO_EV_NEWS_TODO.md`

- **6165** and **zeekr-7x (9099)** were optimized 2026-08-14, before this scan existed — see
  `SEO_EV_NEWS_TODO.md#highest-value-work-still-untouched` and `ledger.csv`. Both still appear
  in this table's ranking for context (6165 at #9, zeekr-7x at #5), marked `[x]`.
- **8950** (`zeekr-v-evropa-skorost-garancziya-i-serviz-bez-kompromisi`, `ev-masters`) and
  **1227** (`kak-se-namirat-avtochasti...`, `ev-masters`) are also done but didn't clear the
  top-30 impression threshold shown here — they're in `ledger.csv`, not omitted, just below
  the cutoff.

## What this changes about the original P1 framing

`SEO_SKILLS_REFACTOR.md` §1's scale-check table treated **6165** as "worth more than the entire
P1 list combined." That was true only relative to the EV-News-only P1 list it was compared
against. **`domashno-zarezhdane-na-elektromobili` (3957) alone earns more impressions (11,843)
than 6165 did (3,519 in this same window)**, and `toyota-bz4x` (200, `ev-review`, 9,256 impr)
beats every post in the original EV News P1 list outright. The real priority order across the
whole site is the table above, not the category the post happens to sit in.

## Not done by this pass

- **Per-post verification that each URL still resolves to a live post** — the prefix-match
  caveat above. Check before running the optimize pipeline on any given row.
- **Query-level detail** (which search terms drive each page, cannibalisation checks) — that's
  Phase A's job per-post, not this scan's.
- **`ev-news`'s own re-scan.** Its 90d total came back as 129 pages w/ impressions against 128
  total posts — inconsistent with `SEO_EV_NEWS_TODO.md`'s baseline of "28 of 128," almost
  certainly because that baseline was itself truncated by the 250-row global cap this scan
  avoided. Worth a dedicated re-pull before trusting the old EV News numbers as current —
  out of scope for this pass, which was about the three uncovered categories.
