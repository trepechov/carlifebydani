# SEO Optimization — Hyundai IONIQ 6 – Всичко, което знаем за разхода и зареждането

**URL:** https://www.carlifebydani.com/publications/hyundai-ioniq-6-vsichko-koeto-znaem-za-razhoda-i-zarezhdaneto/ · **Post ID:** 4902 · **Category:** publications
**Prepared:** 2026-09-05 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Hyundai IONIQ 6 разход
**Ledger:** `4902-2026-09-05-C`

**How this post surfaced:** top of
[`docs/SEO_BACKLINK_TARGETS_TODO.md`](../../docs/SEO_BACKLINK_TARGETS_TODO.md)'s
backlog by real GSC impressions (142 in the fresh pull below). Also the
post flagged twice earlier today (in 130's and 1060's reports) as the
rightful owner of "Hyundai IONIQ 6 разход"-style phrasing — confirmed here.
Explicit user request to add an outbound link to post 1060.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A **1,218-word** (Yoast wordCount 994) deep, data-driven consumption and
charging investigation of the **Hyundai IONIQ 6** — genuinely first-hand,
not a spec rehash. Covers: real RWD data from summer (via a Hyundai
Bulgaria loan car in the EVTour400 sedan group) vs. AWD+RWD data from a
December test (2-5°C), both on the site's own standard EVTour400 route
(София→Пловдив→Карлово→София); a second-day trip to Pleven with detailed
telemetry tables; a genuinely investigative "Конспирацията: лъже ли IONIQ
за заряда на батерията?" section comparing dashboard SOC% against BMS-
reported SOC% (finding a real discrepancy, most pronounced at the low and
high ends of charge); and a charging-curve section testing DC fast-charging
behavior, including a real gotcha (the car won't pre-condition the battery
for fast charging if the navigation ETA shows arrival above 20% SOC).
Genuinely useful, first-hand technical content.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(empty → falls back to post title)* `Hyundai IONIQ 6 - Всичко, което знаем за разхода и зареждането - Car Life by Dani` | ~85 chars, over budget |
| `<meta name=description>` | absent | — |
| Focus keyphrase | none set | — |
| H1 | matches post title verbatim | — |
| Owned word count | **994** (Yoast schema) | — |
| Images without alt | 12 body images (all `alt=""`), featured image media 4924 also `alt=""`, `title` a raw filename | |
| Internal links out / in | **14 total**: 1 correct existing internal link (to a dedicated IONIQ 5 consumption post), **1 broken internal link appearing twice** (see below), rest external-image self-links / 0 inbound confirmed | |

**Broken-link finding:** the "E-GMP" anchor (2 occurrences, same sentence
area) points to `http://tainite-na-hyundai-ioniq-5-platformata-e-gmp-razgovor-s-plamen-maldzhanski`
— a bare slug with no scheme or domain, not a working URL at all (would
resolve as an invalid hostname). The real post exists: id 1350, "Тайните на
Hyundai IONIQ 5 в платформата E-GMP (разговор с Пламен Малджански)",
`/ev-masters/`, 2023-11-23. Proposed fix: point both occurrences at the
correct full URL, same anchor text.

`post_excerpt` is non-empty — noted per the standing trap, not touched.

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, page-level, this URL — reused from the
shared cached pull):** **142 impressions, 5 clicks**, across 24 queries —
the strongest existing signal of any post checked from the backlog today.
Top rows:
| Query | Impr | Clicks | Pos |
|---|---|---|---|
| айоник 6 | 39 | 1 | 7.0 |
| hyundai ioniq 6 мнения | 32 | 3 | 7.4 |
| ioniq 6 | 20 | 1 | 14.4 |
| ионик 6 | 14 | 0 | 9.1 |
| hyundai ioniq 6 | 7 | 0 | 17.1 |
| хюндай айоник 6 | 6 | 0 | 9.8 |

Real signal spread across bare-model-name transliterations plus one
"мнения" (opinions/reviews) query at a decent position (7.4) — matches
this article's genuinely evaluative, data-driven tone.

**Cannibalization check:** `/wp/v2/search?search=Hyundai+IONIQ+6` surfaces
only IONIQ 6 **N** (performance-variant) posts (7303, 7189, 5664, 5343 —
a clearly distinct sub-model, no conflict) and post 1060 (the ID.4 GTX/
IONIQ 5/6 comparison test, already keyphrase-differentiated today to avoid
this exact phrase). **No dedicated base-model IONIQ 6 review exists other
than this post** — the bare "Hyundai IONIQ 6" phrase (880/mo, see below) is
technically unclaimed, but this article's own title deliberately scopes
itself to "разхода и зареждането" (consumption and charging), not a
general review — so the narrower, content-matched phrase is the safer
choice per this pipeline's own "must genuinely satisfy the phrase" rule.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-05):** no volume data
returned for any `разход`/`зареждане`/`мнения`-qualified combination
tried — same "absent volume isn't evidence of no demand" situation hit
elsewhere this session, but here backed by real GSC traffic already
present. For context: `hyundai ioniq 6` bare = 880/mo (real, but too broad
for this content-matched pick — banked to cache for future use, e.g. if
this site ever runs a general IONIQ 6 review).

### Recommendation
**Focus keyphrase:** `Hyundai IONIQ 6 разход` — matches the article's own
actual scope (consumption + charging data, not a general review), the
strongest content-match available, with real existing GSC traffic on
adjacent bare-model queries to build from.
**Secondary:** `Hyundai IONIQ 6` (used in the title/metadesc body, not the
narrow focus target), `IONIQ 6 зареждане`, `IONIQ 6 мнения`, `E-GMP`.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~85 chars, over budget)* | `Hyundai IONIQ 6 разход и зареждане %%sep%% %%sitename%%` | 34 + 19 suffix = 53 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Hyundai IONIQ 6 разход: реални данни от зима и лято, до 15,7 kWh/100км на магистрала. Разкриваме и как работи бързото зареждане на 800V архитектурата.` | 150 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Hyundai IONIQ 6 разход` | — |

Every metadesc fact traces to the real data (summer RWD data + winter
AWD/RWD test, 15.7 kWh/100km highway consumption figure, the 800V
architecture and its fast-charging behavior) — nothing invented.

### Proposed tags
No changes — existing set already correct:
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | E-GMP | 162 | 8 | ✅ keep — in-band, the platform this post investigates directly |
| Entity | Hyundai | 15 | 21 | ✅ keep despite saturated — genuinely the headline brand |
| Entity | IONIQ 5 | 16 | 11 | ✅ keep — established, genuinely compared throughout (not passing) |
| Entity | IONIQ 6 | 120 | 5 | ✅ keep — in-band, the headline model |
| Keyword-intent | Зареждане | 40 | 13 | ✅ keep — established, half the article's subject |
| Keyword-intent | Разход | 139 | 8 | ✅ keep — in-band, the other half |

---

## Phase C — Metatags, tags, alt text, links
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
approved._

### Internal links — research
**Fix (not a new link):** repoint the broken "E-GMP" anchor (2 occurrences)
to the correct URL for post 1350 (`https://www.carlifebydani.com/ev-masters/tainite-na-hyundai-ioniq-5-platformata-e-gmp-razgovor-s-plamen-maldzhanski/`),
same anchor text both times.

**Outbound — explicitly requested: post 1060** (the ID.4 GTX/IONIQ 5/6
consumption comparison test, applied earlier today, 2023-12-23, predates
this post). Checked for a literal existing anchor first — the obvious
candidate ("IONIQ 5" in *"Тази разлика я забелязах още при IONIQ 5"*) is
**already correctly linked to a different, more specific post** (a
dedicated "IONIQ 5 after 1500km" consumption review) — reusing that anchor
would have been wrong, not an oversight to fix. Also checked the "summer
data" sentence — but that specifically references *earlier, separate*
summer testing, not 1060's own (winter) data, so anchoring there would
misattribute the citation. Instead, added one honest new sentence after the
December-cold-weather paragraph: *"Подобни зимни данни вече бяхме събрали и
за ID.4 GTX и IONIQ 5 в нашия по-ранен **тест на разхода на магистрала**."*
— accurate (1060 is genuinely a winter/December consumption test of those
exact two cars, on the same standard route), linking "тест на разхода на
магистрала" to 1060.

**Backlink-target check:** 1060 has ledger row `1060-2026-09-05-C` (already
optimized today) — no `docs/SEO_BACKLINK_TARGETS_TODO.md` entry needed.

**Inbound:** not researched this pass — scoped to the explicit request
(optimize 4902, link to 1060) rather than a full inbound sweep.

### Proposed image alt + title (media 4924, featured image only — the 12
body images are a separate, out-of-scope batch, same discipline as
elsewhere this session)
| Field | Before | After |
|---|---|---|
| `alt_text` | *(empty)* | `Hyundai IONIQ 6 — тест на разход и зареждане` |
| `title` | `dsc08760` (raw filename) | `Hyundai IONIQ 6` |

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live
  (`<title>`, `<meta name="description">` and `og:description` all match
  exactly).
- [x] Tags — unchanged, confirmed still `[15, 16, 40, 120, 139, 162]`.
- [x] Image alt + title — media 4924 written and confirmed live.
- [x] Content edit — both changes applied together: the broken "E-GMP"
  link (2 occurrences) now points to the correct URL for post 1350; the
  new grounded sentence citing 1060 was added after the December
  cold-weather paragraph. Built and verified the combined spliced content
  programmatically (both fragments single-occurrence matches, all block
  counts unchanged, link count 14→15) before sending via `curl` with the
  `seo-bot` Keychain password. Confirmed byte-identical live response.
- [x] Auto-linked `/tag/` count inside body prose: **16** (`Зареждане` ×5,
  `IONIQ 6` ×4, `Hyundai` ×4, `Разход` ×2, `IONIQ 5` ×1 — the last one from
  the plain-text "IONIQ 5" mention inside the newly added sentence,
  auto-linked normally since it wasn't itself wrapped in the editorial
  link) — confirms yet again the theme's 1×-per-tag cap fix is not
  deployed live (same finding as every other post this session). `E-GMP`
  did not auto-link in body prose (checked, genuinely absent as a
  standalone match there).

### Declined
_None — all three approval-gate groups (metatags, image alt/title, content
edit) were approved in full._

### Risks / notes
- 12 of 12 body images still lack alt text — out of scope for this pass.
- The broken E-GMP link fix and the requested 1060 backlink are both
  `post_content` edits — applied together in one write, verified
  structurally as a single combined diff before sending.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): 142 impr / 5 clicks / 3.52% CTR
(blended) / ~10 pos (blended across top queries). Re-check in 2–4 weeks —
real existing impressions to build on.
