# SEO Optimization — RENAULT 5 E-Tech – истински малък, но изключително стилен

**URL:** https://www.carlifebydani.com/publications/renault-5-e-tech-istinski-malak-no-izkljuchitelno-stilen/ · **Post ID:** 2455 · **Category:** publications
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Renault 5 Interior
**Ledger:** `2455-2026-09-04-C`

**How this post surfaced:** [`reports/seo-optimizations/2026-09-04-renault-cluster-plan.md`](../seo-optimizations/2026-09-04-renault-cluster-plan.md) — part of the Renault cluster,
run out of order relative to the plan's recommended sequence (it wanted 2455
optimized *before* 6889, specifically to split their keyphrases before either
locked in). 6889 already shipped today with keyphrase `Renault 5 E-Tech` — see
"A real cannibalization case, already partly resolved" below for how this
run reconciles that.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A **pre-launch preview** of the Renault 5 E-Tech Electric (published
2024-02-29, over a year before the car's actual Bulgarian on-sale date) —
genuinely different in kind from post 6889's post-launch hands-on review.
Every substantive claim is forward-looking/speculative: *"ще бъде
конкурент на Fiat 500 и MINI Electric"*, *"ще предлага две опции за
батерия... се очаква обхватът да е..."* — future tense throughout, framed
around the 2021 concept-to-2024 production journey. Four sections: exterior
design vs. the 1970s original, interior, platform/battery/range
expectations, performance. **1,337 owned words** — real content, distinctly
shorter and more speculative than 6889's 2,420-word finished review.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(no SEO title → falls back to H1)* `RENAULT 5 E-Tech - истински малък, но изключително стилен - Car Life by Dani` | ~75 chars |
| `<meta name=description>` | **absent entirely** | — |
| Focus keyphrase | empty | — |
| Headings | `Как ще изглежда новият Renault 5?` / `Интериор?` / `Какво знаем за платформата, батериите и автономността на Renault 5?` / `Производителност и управление?` — all phrased as open questions, confirming the preview framing | — |
| Owned word count | **1,337** (Yoast schema) | — |
| Images without alt | **71 of 84** images have no alt text (large gallery); featured image (media 2537) also empty | |

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, this URL):** ~59 total impressions across
22 distinct queries, **0 clicks**. Critically, on every query this post
shares with 6889, **it loses**:

| Query | This post's pos | 6889's pos |
|---|---|---|
| renault 5 e-tech | 10.8 | 6.8 |
| renault 5 | 44.2 | 8.8 |
| рено 5 | 33.3 | 6.2 |

Google has already picked 6889 as the canonical result for the shared
phrase — confirms the cannibalisation finding from 6889's own report, from
this post's side of it. **But a handful of queries are genuinely distinct**
and this post earns them on its own, not competing with 6889:

| Query | Impr | Pos |
|---|---|---|
| renault 5 interior | 3 | 10.0 |
| renault 5 e-tech 52kwh | 3 | 10.7 |
| renault 5 e-tech white | 3 | 8.0 |

Small numbers, but real and **not shared with 6889** — exactly the
differentiated angle the cluster plan wanted.

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `renault 5 дизайн` | *(zero — not a real Bulgarian search phrase)* |
| `renault 5 интериор` | **рено 5 интериор** · interior 2025/2026 · inside · colours · old (1972/1980) · techno |
| `renault 5 concept` | concept car · concept 2021 · **concept vs production** · concept interior · e-tech concept · electric concept |

`интериор`/`interior` is a real, confirmed cluster in both scripts.
`concept` also completes richly — matches this article's own 2021-prototype
framing — but no Cyrillic equivalent completions turned up, weaker signal
than interior.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-04):**
| Phrase | Volume/mo |
|---|---|
| renault 5 interior | 50 |
| renault 5 concept | 10 |
| renault 5 дизайн | no data |
| рено 5 интериор | no data |

Modest but real, and — unlike `renault 5 e-tech`/`рено 5` — **not already
owned by 6889**.

### A real cannibalization case, already partly resolved
6889's own Phase A (run earlier today) found this exact pair and flagged it
with the user, who chose to proceed with 6889 only at the time. That
decision remains correct — 6889 is decisively winning every shared query
(see table above) and shouldn't be touched. **What changes here is 2455's
own keyphrase choice**: rather than leaving this post pointed at the same
`Renault 5 E-Tech` phrase it's already losing on, this run gives it a
**genuinely different, real-demand phrase this post can actually own** —
resolving the cannibalisation forward instead of leaving two posts
competing for the same losing position.

### Recommendation
**Focus keyphrase:** `Renault 5 Interior` — real volume (50/mo), a
dedicated section of this article answers it directly, already earning
independent (if tiny) GSC traction distinct from 6889, and creates no
overlap with 6889's launch/pricing keyphrase. Kept in the mixed Latin
phrasing that's how it's actually searched (`renault 5 interior`, not a
Cyrillic translation) — same pattern as this site's other English-technical-
term keyphrases.
**Secondary:** `Renault 5 concept` (10/mo, matches the article's 2021-
prototype framing), `Renault 5 дизайн` (no measured volume but zero
autocomplete competition either — safe supporting phrase in the metadesc,
not title-driving).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~75 chars)* | `Renault 5 E-Tech — дизайн и интериор %%sep%% %%sitename%%` | 38 + 19 suffix = 57 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Renault 5 E-Tech интериор и дизайн — как изглежда отвътре новата икона, вдъхновена от концепта от 2021 г. Очаквани батерии, обхват и версии.` | 141 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Renault 5 Interior` | — |

Reasoning: title keeps the brand/model recognizable (matches the shared
"Renault 5 E-Tech" family term, since bare removal would look like a
different car) but leads the differentiating words ("дизайн и интериор")
right after it, avoiding a title identical or near-identical to 6889's.
Metadesc front-loads "интериор" and "дизайн", cites the real 2021-concept
fact from the article's own text, and closes on the battery/range
expectations — everything traceable to the actual article.

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | Renault | 80 | 9 | ✅ keep |
| Entity | Renault 5 | 194 | 3 | ✅ keep |
| Entity | E-Tech | 101 | 5 | ✅ keep |
| Entity | Geneva Motor Show | 192 | 13 | ✅ keep — established (11–19 band); the car's real-world reveal venue, consistent with this site's other Geneva-2024 coverage (post 2398), even though the word doesn't appear verbatim in this post's own text |
| Entity | **AmpR** | **195** | **1** | ❌ **drop, replace with `AmpR Small` (id 369, count 3)** — the article's own text says *"платформа наречена AmpR Small"* verbatim; `AmpR` alone is a fragmented duplicate of the correct tag 6889 already uses, sitting at a thin count-1. Consolidating onto the real tag name fixes a taxonomy split rather than creating a new one. |

Net tag set: `[80, 194, 101, 192, 369]` — same size, one correction.

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change proposed. Current H1 already carries the model name.
- [ ] **Subheadings** — none proposed; existing 4-heading outline already
  matches the article's actual structure.
- [ ] **Image alt + title** — featured image, media id 2537. **Unusual
  finding: already decent.** `alt_text` = "малък електрически автомобил
  Renault 5 E-Tech", `title` = "RENAULT 5 E-Tech – Премиера на Автосалон
  Женева 2024" — both descriptive and accurate (the title even confirms the
  Geneva Motor Show connection used for the outbound link below). **First
  post in this project where the featured image needed no fix** — not
  proposing a change.
  **Not proposed:** the other 71 images without alt text — same
  proportionate-scope reasoning as 6889, a separate larger task.

### Internal links
**Outbound — this article should link to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|
| 2398 (`Автомобилно Изложение Женева 2024`, 2024-02-28, `ev-news`, **predates this post by 1 day**) | `/ev-news/avtomobilno-izlozhenie-zheneva-2024-renault-5-lucid-sapphire-lucid-g/` | "се демонстрира" | Opening sentence: *"Забележително е, че крайният продукт **се демонстрира** с изключителна прилика към своя концептуален предшественик..."* — wraps the existing phrase describing the car's public unveiling; the 1-day gap and the featured image's own title (explicitly naming "Автосалон Женева 2024") both confirm this post is a direct reaction to that show. New→old direction, correct. |

**Inbound — existing posts that should link here:** none proposed. This is
one of the oldest Renault-cluster posts (2024-02-29); no older post has
genuine topical overlap beyond 2398, already used above.

### Proposed metatags (unchanged from Phase A)
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~75 chars)* | `Renault 5 E-Tech — дизайн и интериор %%sep%% %%sitename%%` | 38 + 19 suffix = 57 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Renault 5 E-Tech интериор и дизайн — как изглежда отвътре новата икона, вдъхновена от концепта от 2021 г. Очаквани батерии, обхват и версии.` | 141 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Renault 5 Interior` | — |

### Proposed tags (carried forward from Phase A)
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Renault | 80 | 9 |
| Entity | Renault 5 | 194 | 3 |
| Entity | E-Tech | 101 | 5 |
| Entity | Geneva Motor Show | 192 | 13 |
| Entity | AmpR Small | 369 | 3 (replacing `AmpR`, id 195, count 1 — see Phase A) |

Net: `[80, 194, 101, 192, 369]` — drops `195` (AmpR), adds `369` (AmpR Small).

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live
  (rendered `<title>` and `<meta name="description">` both match exactly).
- [x] Tags — `AmpR` (195) dropped, `AmpR Small` (369) added; net set
  `[80, 194, 101, 192, 369]` confirmed in the write response and via the
  rendered page's now-correct "AmpR Small" auto-link (previously would have
  been "AmpR", which no longer matches the article's own text).
- [x] Image alt + title — no change (already adequate, see Phase C notes).
- [x] Outbound link — added inside this post's own opening paragraph,
  wrapping "се демонстрира" with a link to post 2398 (Geneva Motor Show
  2024). Built and verified the 37.8KB spliced content programmatically
  (single-occurrence phrase, exact byte delta, all block counts unchanged)
  before sending via `curl` with the `seo-bot` Keychain password; confirmed
  byte-identical live response.
- [x] Auto-linked `/tag/` count inside body prose: **13** across 26
  paragraphs — same undeployed-cap finding as every other post this
  session (4129, 5240, 6889).

### Declined
_None — both approval-gate groups (metatags+tags, outbound link) were
approved in full._

### Risks / notes
- This post's own GSC performance is minimal (0 clicks, ~59 impressions
  over 90d) — the realistic outcome here is small: capturing the
  differentiated `interior`/`concept` long-tail (real but modest volume:
  50/mo + 10/mo) without continuing to compete with 6889 on the phrase it
  was already losing. Primary value is resolving the cannibalisation
  forward, not a big traffic gain.
- 71 of 84 images still lack alt text — same proportionate-scope note as
  6889, a separate larger task.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): ~59 impr (across 22 queries) / 0
clicks (page-level aggregate not separately pulled — the per-query rows
above are the available baseline). Re-check in 2–4 weeks specifically on
`renault 5 interior` and `renault 5 e-tech 52kwh`/`white` — the
differentiated queries this post can actually move, rather than the shared
terms 6889 already owns. Ledger `2455-2026-09-04-C`, `verify_due`
2026-10-02.
