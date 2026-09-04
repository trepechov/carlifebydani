# SEO Optimization — Renault Scenic E-Tech – Автомобил на годината 2024

**URL:** https://www.carlifebydani.com/publications/renault-scenic-e-tech-avtomobil-na-godinata-2024/ · **Post ID:** 2622 · **Category:** publications
**Prepared:** 2026-09-05 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Renault Scenic E-Tech
**Ledger:** `2622-2026-09-05-C`

**How this post surfaced:** third post (of 6) in
[`reports/seo-optimizations/2026-09-04-renault-cluster-plan.md`](../seo-optimizations/2026-09-04-renault-cluster-plan.md),
run per the plan's oldest→newest order (after 1027 and 2455, both already
applied).

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A real, substantive technical review (**744 words** of owned prose, Yoast
`wordCount` 716 rendered) of the **Renault Scenic E-Tech**, framed around its
win of **"Автомобил на годината" (Car of the Year) 2024** at the first event
of the Geneva Motor Show — beating a shortlist of 7 (BMW 5-series/i5, Peugeot
3008/e-3008, Kia EV9, Volvo EX30, BYD Seal, from 28 models total). Structured
with real H2 subheadings (Интериор, Екстериор, Инфотейнмънт Система,
Спецификации, Иновации, Багажник) — unusually well-organized for this
pipeline. Covers: 80% recycled interior materials, a 545 L trunk (fixed rear
seats, no flat-fold), a 12" touchscreen with Google-embedded `openR link`,
two battery/motor configs (60 kWh/170 PS/420 km and 87 kWh/218 PS/620 km
WLTP), three regen-braking levels, the shared **CMF-EV platform** (also
under Nissan Ariya and Renault Mégane E-Tech), and a December 2023 Mitsubishi
announcement of a Scenic-based EV SUV sharing the same factory. No price
mentioned anywhere in the article — a real gap, not an oversight to fix here
(nothing to source a number from).

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(empty → falls back to post title)* `Renault Scenic E-Tech - Автомобил на годината 2024 - Car Life by Dani` | ~74 chars, over budget |
| `<meta name=description>` | absent | — |
| Focus keyphrase | none set | — |
| H1 | matches post title verbatim | — |
| Owned word count | **716** (Yoast schema) | — |
| Images without alt | **24** (all gallery images, `alt=""`; featured image media 3429 also `alt=""`, `title` a raw filename `renault_scenic-19`) | |
| Internal links out / in | **0 internal** (24 images link only to themselves, standard gallery behavior) / 0 inbound confirmed | |

`post_excerpt` is **non-empty** (a real 289-char summary used as the
front-page teaser) — noted per this pipeline's standing trap, not touched:
this pass writes `_yoast_wpseo_metadesc` only, never `post_excerpt`.

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, page-level, this URL — reused from the
shared cached pull, `query-page|90d|2026-09-03`):** 23 impressions, 1 click,
across 8 queries, all with a "2024" modifier:
| Query | Impr | Clicks | Pos |
|---|---|---|---|
| рено 2024 | 8 | 0 | 4.0 |
| рено сценик 2024 | 4 | 1 | 2.8 |
| renault scenic 2024 | 3 | 0 | 2.7 |
| renault 2024 | 2 | 0 | 5.5 |
| renault grand scenic 2024 | 2 | 0 | 8.0 |
| reno 2024 | 2 | 0 | 5.0 |

Real signal: `рено сценик 2024` already ranks **pos 2.8** with a 25% CTR on
one click — the page is genuinely earning something on model+year queries,
just at low volume. None of these use the bare "Scenic E-Tech" phrasing —
consistent with autocomplete/DataForSEO below showing that's where the real
volume actually is.

**Cannibalization check:** `/wp/v2/search?search=Renault+Scenic` returns only
this post (2622) as a genuine match (2398 mentions Scenic only in passing and
already links to 2622 as the deep-dive owner, from its own optimization
pass). Phrase is unclaimed.

**Google autocomplete (hl=bg, gl=bg):**
- `renault scenic e-tech` → rich, current: `мнения`, `electric`, `alpine`,
  `ev60`, `цена`, `87kwh`, `ev87`, `autodata` — real demand, trim-specific.
- `renault scenic electric` → `2025`/`2026`/`2027` model-year variants,
  `review`, `range`, `price` (English-phrasing).
- `рено сценик е-тех` / `електрик` → thinner, but `цена` and
  `технически характеристики` recur.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-05):**
| Phrase | Volume/mo | Note |
|---|---|---|
| renault scenic e-tech | **110** | largest, unclaimed — the pick |
| renault scenic electric | 30 | secondary, English-phrasing variant |
| renault scenic e-tech цена | 20 | secondary — price intent (article has no price to satisfy this, so not the focus) |
| рено сценик електрик | 10 | thin |
| автомобил на годината 2024 | *(no data returned)* | event-name query — same pattern as `автосалон женева 2024` on post 2398: doesn't sustain search demand after the show ends, not a keyphrase candidate despite framing the article |

All banked to `data/seo-cache/keywords.csv`.

### Recommendation
**Focus keyphrase:** `Renault Scenic E-Tech` — largest unclaimed volume
(110/mo), the article genuinely satisfies it (a real technical review with
proper subheadings), and it doesn't compete with anything else on this site.
**Secondary:** `Renault Scenic E-Tech мнения` (matches recurring
autocomplete modifier), `CMF-EV`, `Автомобил на годината 2024` (low
standalone demand but accurately frames the piece and matches the existing
"2024"-modifier GSC queries above).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~74 chars, over budget)* | `Renault Scenic E-Tech — пълен преглед %%sep%% %%sitename%%` | 37 + 19 suffix = 56 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Renault Scenic E-Tech спечели Автомобил на годината 2024. Разгледахме интериора, багажника от 545 л и CMF-EV платформата. Прочетете пълния преглед.` | 147 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Renault Scenic E-Tech` | — |

Kept the title deliberately factual ("пълен преглед", not "честни
впечатления" as used on 1027) — this article's actual voice is descriptive/
technical, not first-person critique, so the metatags shouldn't promise a
tone the page doesn't deliver. Every metadesc fact traces to the real prose
(Car of the Year 2024, 545 L trunk, CMF-EV platform) — nothing invented, no
price claimed since none exists in the article.

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | E-Tech | 101 | 5 | ✅ keep — in-band, named |
| Entity | Geneva Motor Show | 192 | 13 | ✅ keep — established, the actual event |
| Entity | Renault | 80 | 9 | ✅ keep — in-band, named |
| Entity | **CMF-EV** | 224 | 1 | ✅ keep despite below-band — article's own platform-name exception (same as 1027) |
| Entity | **Scenic** | 199 | 1 | ✅ keep despite below-band — the headline model itself |
| Entity | **Nissan** | **19** | **3** | ➕ **add — in-band, named** ("платформата CMF-EV, споделена с Nissan Ariya и Renault Mégane E-Tech") |
| Entity | **Megane** | **100** | **3** | ➕ **add — in-band, named** (same sentence, Renault Mégane E-Tech) |

**Final tag set (7):** `[224, 101, 192, 80, 199, 19, 100]`

**Gaps:** no existing tag for `Mitsubishi` (the December 2023 EV-SUV
announcement, named substantively) — only this post would use it currently;
not creating speculatively, noting the gap (same gap flagged in 1027's
report — a recurring Mitsubishi mention across this cluster with no tag at
all, worth a batch-create decision if it keeps recurring).

---

## Phase C — Metatags, tags, alt text, links
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
approved._

### Internal links — research
**Inbound:** none proposed. Checked posts predating 2622 (2024-03-07) for a
natural CMF-EV/Renault-adjacent anchor — 1027 (2023-03-08, Renault Megane
E-Tech) and 196 (2023-09-17, Nissan Ariya) both predate it and are topically
related, but neither mentions "Scenic" anywhere in its own prose; forcing an
edit into either would mean inventing a sentence not grounded in what's
actually written there, which this pipeline's own rule warns against. Zero
inbound is the correct answer, not a gap.

**Outbound:** two candidates, both **already named verbatim** in the
article's own existing "Иновации" paragraph — no invented text needed, just
wrap the existing words:
- **Post 1027** (Renault Megane E-Tech, 2023-03-08) — anchor "Renault Mégane
  E-Tech", the exact phrase already in the sentence about the shared CMF-EV
  platform.
- **Post 196** (Nissan Ariya, 2023-09-17) — anchor "Nissan Ariya", same
  sentence.

Both already optimized this session (1027 applied 2026-09-05; 196 has a
ledger check below).

**Backlink-target check:** 1027 has ledger row `1027-2026-09-05-C` (already
optimized, no TODO entry needed). 196 does **not** have a ledger row yet —
already present in `docs/SEO_BACKLINK_TARGETS_TODO.md`'s Backlog (added when
1027's own outbound link to it was written earlier today) — no duplicate
entry needed, this is a second backlink to the same already-tracked post.

### Proposed image alt + title (media 3429, featured image only — the other
23 gallery images are out of scope for this pass, same batch-size decision
made on 6889's 51 images)
| Field | Before | After |
|---|---|---|
| `alt_text` | *(empty)* | `Renault Scenic E-Tech — Автомобил на годината 2024 на Geneva Motor Show` |
| `title` | `renault_scenic-19` (raw filename) | `Renault Scenic E-Tech` |

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live
  (`<title>`, `<meta name="description">` and `og:description` all match
  exactly).
- [x] Tags — replaced with `[19, 80, 100, 101, 192, 199, 224]` (added Nissan
  + Megane), confirmed live.
- [x] Image alt + title — media 3429 written and confirmed live.
- [x] Outbound links — added to post 2622's own CMF-EV paragraph, wrapping
  the existing "Nissan Ariya" → post 196 and "Renault Mégane E-Tech" → post
  1027. Built and verified the spliced content programmatically
  (single-occurrence fragment match, all block counts unchanged, link count
  24→26, char delta exactly the 212-char inserted text) before sending via
  `curl` with the `seo-bot` Keychain password. Confirmed byte-identical live
  response.
- [x] Auto-linked `/tag/` count inside body prose: **17** (`Renault` ×5,
  `Scenic` ×5, `E-Tech` ×5, `Megane` ×1, `CMF-EV` ×1 — excluding the
  bottom-of-post hashtag-pill widget, which is separate UI, not body prose).
  The 5× repeats on the three headline-model tags confirm yet again the
  theme's 1×-per-tag cap fix is not deployed live (same finding as posts
  6889, 4129, 5240, 2398, 1027) — this article names "Scenic E-Tech" in
  nearly every section heading's lead sentence, so the effect is more
  pronounced here than elsewhere.

### Declined
_None — all three approval-gate groups (metatags+tags, image alt/title,
outbound links) were approved in full._

### Risks / notes
- 23 of 24 body images still lack alt text — same accessibility/SEO gap
  pattern as 6889 (38/51) and other galleries this pipeline has touched;
  batch-writing them is a separate, larger task than this pass's scope.
- Low-volume phrase overall (110/mo bare keyphrase) — realistic ceiling is
  modest, but it's the largest genuinely available option and the page
  already shows early ranking traction (`рено сценик 2024` at pos 2.8).

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): 23 impr / 1 click / 4.3% CTR / — pos
(blended across 8 queries). Re-check in 2–4 weeks for the metatag/tag/link
changes (this page already has real impressions to build on, unlike 1027).
