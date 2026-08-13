# SEO Optimization — #EV114 – Има ли регистриран Tesla Cybertruck в България

**URL:** https://www.carlifebydani.com/ev-news/ev114-ima-li-registriran-tesla-cybertruck-v-blgariya/
**Post ID:** 7333 · **Published:** 2025-08-19 · **Prepared:** 2026-08-13
**Status:** **metatags applied 2026-08-13** (verified live: `<title>`, `<meta name=description>`
and `og:description` all updated — `og:description` did not exist before). On-page content
changes below remain open.

## What this article is about

EV News episode #114 (recorded 19.08.2025). The headline question is whether a Tesla
Cybertruck has been **registered in Bulgaria** — the episode's distinctive story and the
reason the page ranks at all. The rest of the episode is the standard news roundup:
Tesla Model X and Model S discontinued for Europe, BMW iX3 2026 efficiency, Leapmotor B10
entering Europe under €30,000, and Bulgaria ranking among the cheapest European countries
for home charging.

**The page's owned text is a single YouTube iframe.** `post_content` contains nothing but
the embed; Yoast schema reports `wordCount: 17`. The ~75 news summaries that render on the
page are fetched from a remote CSV at render time (`theme/single.php:110-115`) and are not
in `post_content`. **Critically, no text anywhere on the page answers the title's question** —
the only Cybertruck mentions in the rendered text are US sales promos and battery specs from
unrelated roundup items. The answer exists only inside the video.

## Current state

| | Value | Length |
|---|---|---|
| `<title>` | `#EV114 - Има ли регистриран Tesla Cybertruck в България - Car Life by Dani` | 74 |
| `<meta name=description>` | **absent entirely** | — |
| `og:description` | **absent** (Yoast derives it from metadesc) | — |
| Focus keyphrase | empty | — |
| H1 | `#EV114 - Има ли регистриран Tesla Cybertruck в България` | 55 |
| Owned word count | **17** (Yoast schema) | — |
| Headings | H1 → **~75× H5**, no H2/H3 in the article body | — |
| Images | 84 total; **68 with empty `src`** (JS-hotloaded); featured image `alt_text` **empty** | — |
| `post_excerpt` | empty | — |
| `<html lang>` / `og:locale` / `inLanguage` | `bg-BG` / `bg_BG` / `bg-BG` — correct | — |

## Demand research

**GSC — this URL, 2026-05-15 → 2026-08-12 (90d):**
**542 impressions · 22 clicks · 4.06% CTR · avg position 7.0**

| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|
| `cybertruck bulgaria` | 25 | 2 | 8.0% | 7.2 |
| `tesla cybertruck българия` | 23 | 1 | 4.3% | **5.8** |
| `cybertruck` | 8 | 0 | 0% | 25.8 |
| `tesla cybertruck bulgaria` | 6 | 0 | 0% | 10.3 |
| `tesla cybertruck` | 5 | 0 | 0% | 25.4 |
| `тесла сайбъртрък` | 5 | 0 | 0% | 12.2 |
| `сайбър трък` | 4 | 0 | 0% | 9.8 |
| `tesla cybertruck цена българия` | 1 | 0 | 0% | 11.0 |
| `има ли в българия` | 1 | 0 | 0% | **2.0** |
| `колко има в българия` | 1 | 0 | 0% | **1.0** |

13 distinct queries named, totalling **82 impressions of the 542** — the remaining ~460 are
anonymised long-tail. The named set is unambiguous: **Cybertruck + Bulgaria**, searched in
both alphabets (`сайбъртрък`, `сайбър трък`).

Note `има ли в българия` at position **2.0** and `колко има в българия` at position **1.0** —
Google already treats this page as the answer to "is there / how many are there in Bulgaria",
just with almost no impressions attached to those generic phrasings.

**Google autocomplete (hl=bg, gl=bg) — cached this run:**

| Seed | Completions |
|---|---|
| `cybertruck` | цена · price · tesla · **в българия** · interior · black · **bulgaria** · tesla цена · weight |
| `tesla cybertruck` | цена · price · interior · weight · играчка · black · dimensions · **цена българия** · **българия** |
| `сайбъртрък` | цена · **българия** · играчка · тесла · кола сайбъртрък |
| `тесла сайбъртрък` | цена · **българия** · характеристики |
| `cybertruck цена` | (mostly foreign-market: долларах, ташкенте, россии) + **tesla cybertruck цена българия** |

`cybertruck в българия` is a **top-5 completion for the bare `cybertruck` seed** — Google
itself considers "in Bulgaria" one of the most common continuations. `цена` is the dominant
modifier on every seed.

**Keyword metrics (bg):**

| Phrase | Volume | CPC | Comp | Provenance |
|---|---|---|---|---|
| `cybertruck` | 1,600 | — | — | cached 2026-08-04 |
| `tesla cybertruck` | 720 | 0.14 | 0.01 | **fresh, Semrush 2026-08-13** |
| `сайбъртрък` | 140 | 0 | 0.01 | **fresh, Semrush 2026-08-13** |
| `cybertruck цена` | 140 | 0.14 | 0.11 | **fresh, Semrush 2026-08-13** |
| `cybertruck българия` | — | — | — | Semrush: **NOTHING FOUND** |

Bought 3 rows this run (~30 units); 1 came free from cache. All 4 banked to
`data/seo-cache/keywords.csv`. Semrush returns **no BG data at all** for the Bulgaria-modified
phrases — as expected for Bulgarian long-tail, and **not** evidence of no demand: GSC shows
55 real impressions across the Bulgaria cluster.

**SERP check** (US-locale WebSearch — DataForSEO's BG SERP endpoint is account-blocked, so
this is a proxy, not a true BG SERP):

Competitors ranking for the Cybertruck-in-Bulgaria intent:
- `automedia.investor.bg` — "Tesla Cybertruck **вече е в България**"
- `dizzyriders.bg` — "**Първият** Cybertruck в България вече се продава" / "…**е във Варна**"
- `skandal.bg` — "Пикапът на Tesla — Cybertruck вече се продава и в България **на цена над 300 хил лв.**"
- `evpoint.bg` — spec page · `autoscout24.bg` — listings

**Every competitor title states a fact. Ours asks a question and never answers it.**
`skandal.bg` additionally puts the **price** in the title, and `цена` is the top autocomplete
modifier — that combination is what's taking the clicks.

**GA4 (90d, this landing page):** 23 sessions · 61% bounce · ~19s engagement per session.
Consistent with the 22 GSC clicks. The bounce rate is the tell: people arrive asking a
question, find a page with no text answer, and leave.

## Recommendation

**Focus keyphrase:** `Tesla Cybertruck България`

Why: it's the intent the page already ranks best for (position **5.8** on
`tesla cybertruck българия`, 7.2 on `cybertruck bulgaria`), it's confirmed by autocomplete
as a top continuation of the head term, and — critically — it is **not** contested by any
other page on this site (see cannibalisation check below).

**Secondary:** `cybertruck bulgaria` (Latin variant, 25 impr), `сайбъртрък` /
`сайбър трък` (Cyrillic transliteration, 10 impr combined), `Tesla Cybertruck цена България`.

**Deliberately not targeted:** the bare head terms `cybertruck` / `tesla cybertruck`. This
page sits at position 25.8 and 25.4 on those, while the `/ev-review/` Cybertruck page holds
position 9.0. Conceding the head term to that page is correct.

## Proposed metatags

| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to)* `#EV114 - Има ли регистриран Tesla Cybertruck в България - Car Life by Dani` (74) | `Има ли Tesla Cybertruck в България? %%sep%% %%sitename%%` | **54** rendered |
| `_yoast_wpseo_metadesc` | *(absent entirely)* | `Има ли регистриран Tesla Cybertruck в България? Отговорът е във видеото на EV News #114 – заедно с BMW iX3 2026 и Leapmotor B10 под 30 000 евро.` | **144** |
| `_yoast_wpseo_focuskw` | *(empty)* | `Tesla Cybertruck България` | 25 |

Title reasoning: the current title burns its first 9 characters on `#EV114 - `, which no one
searches, pushing the keyphrase out of the highest-value SERP real estate. Dropping the
prefix and the word `регистриран` (which appears in **zero** GSC queries) while keeping both
brand tokens `Tesla` and `Cybertruck` plus `България` fits the 60-char cap at 54 with room
to spare. Retaining the question mark matches the `има ли…` phrasings the page already ranks
1st–2nd for.

Description reasoning: front-loads the exact keyphrase, then names two verified specifics
from the episode. `Отговорът е във видеото` is deliberately honest about where the answer
lives — overselling a text answer that isn't there is what drives the 61% bounce.

## On-page changes (need a human)

- [ ] **Answer the question in text — the single highest-value change.** The page ranks
      1st–2nd for `колко има в българия` / `има ли в българия` and 5.8 for the main phrase,
      yet contains no text answer. Add 2–3 sentences immediately under the H1 stating the
      actual answer as of the episode date. **I could not write this myself — the answer is
      only in the video and I will not invent a fact.** Supply the answer and I'll draft it.
- [ ] **`post_excerpt`** — 100–150 words of Bulgarian intro, the biggest content lever on a
      17-word page. Should open with the answer and the keyphrase. Blocked on the same fact.
- [ ] **Heading structure** — the body goes H1 → ~75× H5 with no H2/H3. At minimum, wrap the
      new intro in an H2 such as `Има ли регистриран Tesla Cybertruck в България?` and demote
      the roundup items to H3.
- [ ] **Featured image (media 7334)** — `alt_text` is empty and the media title is the
      meaningless `#EVN 114-2`. Proposed alt: `Tesla Cybertruck в България – EV News #114`.
      Verify the write works on this one image before batching across the category.
- [ ] **68 images with empty `src`** — theme-level defect (JS-hotloaded thumbnails), affects
      every EV-News page. Out of scope here; tracked in `docs/SEO_EV_NEWS_TODO.md` P3.
- [ ] **Do not change the slug.** 542 impressions ride on this URL.

## Internal links

**Inbound — existing posts that should link here** (higher value):

| Source post | URL | Anchor text | Where |
|---|---|---|---|
| 7533 — Tesla CYBERTRUCK (ev-review) | `/ev-review/tesla-cybertruck-moshh-inovacziya-i-dizajn-bez-graniczi-zvyart-ot-bdeshheto-veche-e-tuk/` | `има ли регистриран Tesla Cybertruck в България` | In/near the Bulgaria-availability section — this page has 237 impressions of authority to pass |
| 5350 — #EVN71 Tesla Cybertruck в България | `/ev-news/evn71-tesla-cybertruck-v-blgariya-light-show-vv-veliko-trnovo/` | `Tesla Cybertruck в България` | Body; it already carries the phrase in its title |
| 7821 — #EV127 Cybertruck с FSD в Прага | `/ev-news/ev127-cybertruck-s-fsd-v-praga/` | `Cybertruck в България` | Body |

**Outbound — this article should link to:**

| Target post | URL | Anchor text | Where |
|---|---|---|---|
| 7533 — Tesla CYBERTRUCK review | `/ev-review/tesla-cybertruck-.../` | `пълния преглед на Tesla Cybertruck` | In the new intro paragraph |
| 6452 — #EV93 Cybertruck 5 звезди EuroNCAP | `/ev-news/ev93-cybertruck-postigna-5-zvezdi-na-euroncap-testa-za-sigurnost/` | `Cybertruck на EuroNCAP теста` | Body |
| 7037 — Станциите на Тесла в България вече са платени | `/publications/stancziite-na-tesla-v-blgariya-veche-sa-plateni/` | `зарядните станции на Tesla в България` | Body, Bulgaria context |

No `/tag/` pages as targets — thin taxonomy pages already outrank editorial content on this
site.

## Risks / notes

**Cannibalisation — checked, and the picture is healthy.** Google has already split the
Cybertruck cluster cleanly between two pages:

| Intent | Page | Evidence |
|---|---|---|
| **+ Bulgaria** | **7333 (this page)** | `cybertruck bulgaria` 7.2 · `tesla cybertruck българия` 5.8 · `tesla cybertruck bulgaria` 10.3 |
| **Specs / Cyrillic `кибертрак`** | 7533 (ev-review) | `тесла cybertruck` 5.6 · `тесла кибертрак` 9.4 · `tesla cybertruck характеристики` 6.5 |

Two soft overlaps worth watching, neither blocking:
- **Bare `cybertruck`** — this page 25.8, the review page 9.0. Concede it to the review page;
  the proposed keyphrase does that deliberately.
- **Transliteration split** — `сайбъртрък` variants land on this page, `кибертрак` variants on
  the review page. Harmless today, but if the review page ever targets `сайбъртрък` explicitly
  the two will start competing.
- **Post 5350 (#EVN71)** carries `Tesla Cybertruck в България` in its title but earns only
  16 impressions / 0 clicks / position 8.25 in 90 days, and **none** of its impressions come
  from Cybertruck queries. Not currently competing. Do not optimize it toward this phrase.

**Metatags alone will not fix this page.** Position 5.8 with 4.06% CTR is a genuine
snippet-and-title problem worth fixing, and adding the missing meta description should move
CTR on its own. But the 61% bounce and the 17-word body are content problems: competitors win
because they answer the question on the page. Expect the metatag write to lift CTR modestly;
expect the text answer to be what actually moves position and dwell time.

**Separate finding, arguably a bigger prize than this page.** Post 7533
(`/ev-review/tesla-cybertruck-.../`) has **237 impressions at position 6.2 with 0.84% CTR and
2 clicks** over the same 90 days. Position 6 should yield roughly 4–6%. That page is losing
more clicks than this one and is not in the `docs/SEO_EV_NEWS_TODO.md` P1 list — it should be.

## Measurement

Baseline (GSC, 2026-05-15 → 2026-08-12): **542 impressions · 22 clicks · 4.06% CTR · pos 7.0**.
Target queries to watch: `cybertruck bulgaria` (pos 7.2), `tesla cybertruck българия` (pos 5.8).
Re-check after 2–4 weeks — Google must re-crawl before the new snippet appears, then GSC needs
time to accumulate. The `seo-performance-report` skill picks this up on its next monthly run.
