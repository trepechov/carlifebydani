# SEO Optimization — Tesla CYBERTRUCK – Мощ, иновация и дизайн без граници

**URL:** https://www.carlifebydani.com/ev-review/tesla-cybertruck-moshh-inovacziya-i-dizajn-bez-graniczi-zvyart-ot-bdeshheto-veche-e-tuk/
**Post ID:** 7533 · **Category:** ev-review
**Prepared:** 2026-08-14
**Status:** researched (Phase A complete 2026-08-14; metatags/tags proposed below, not yet applied)
**Keyphrase:** `Tesla Cybertruck характеристики`
**Ledger:** `7533-2026-08-14` (inbound link)

---

## Note on how this report came to exist

This is **not** a normal Phase A → C run. It exists because `docs/SEO_SKILLS_REFACTOR.md` §W5
needed a live verification of the inbound-link write path against a real post, and 7533 was
already flagged in `SEO_EV_NEWS_TODO.md` as a known, unaddressed opportunity (237 impressions,
0.84% CTR, position 6.2 — see that file for the full case). Only the inbound-link write
happened here; the full optimization (keyphrase research, metatags, tags) is still open and
should go through the normal `seo-article-optimize` pipeline, which will read this file and
append its own Phase A section rather than starting a new one.

## Phase A — Keyphrase research (2026-08-14, run properly via `seo-keyphrase-research`)

### What this article is about
A full spec review of the Tesla Cybertruck — history/backstory, class & positioning, platform
(800V architecture, 4680 cells, steel exoskeleton), the three trim versions (Single Motor RWD,
Dual Motor AWD, Cyberbeast) in a spec table, interior, exterior/design, key innovations list,
charging/FSD, conclusion. Real, substantial, well-structured content: **690 words**, and — the
only page in this session so far with a **correct H1→H2→H3 heading outline**, no defect.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(no SEO title set → falls back to H1)* `Tesla CYBERTRUCK - Мощ, иновация и дизайн без граници — звярът от бъдещето вече е тук. - Car Life by Dani` | H1 alone is ~89 chars, all-caps "CYBERTRUCK", no keyphrase-first framing |
| `<meta name=description>` | **absent entirely** | — |
| Focus keyphrase | empty | — |
| Headings | ✅ correct H1→H2→H3, no defect | — |
| Owned word count | **690** (Yoast schema) | — |
| Internal links out | 1 (added 2026-08-14, to post 7333 — see Phase C below) |

### Demand research
**GSC, this URL — two windows, both fresh pulls today (the numbers already on file for this
post, 237 impr/0.84% CTR/pos 6.2, are from an earlier/wider pull; treating these as current):**
- 28-day (2026-07-17→2026-08-13, page-level): **68 impressions · 1 click · 1.47% CTR · pos 4.8**
- 90-day (2026-05-16→2026-08-13, query-level breakdown):

| Query | Impr | Clicks | Pos |
|---|---|---|---|
| `тесла cybertruck` | 23 | 0 | 5.6 |
| `тесла кибертрак` | 11 | 0 | **9.4** |
| `tesla cybertruck характеристики` | 4 | 0 | 6.5 |
| `cybertruck` | 3 | 0 | 9.0 |
| `пикап тесла кибертрак` | 3 | 0 | 8.3 |

(Query-level total is lower than the page-level total — GSC doesn't break out every
low-volume query individually, a known reporting-threshold effect, not a data error.)

**Zero clicks across every named query in both windows except one anonymised click in the
28-day page total.** Position 4.8–9.4 with real impressions and essentially no clicks is the
clearest possible "presentation problem, not a ranking problem" case this session — exactly
what a missing meta description does.

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `тесла кибертрак` | цена · цена в евро · **технически характеристики** · интерьор · салон · цена в долларах · **характеристики** · купить · фото |
| `tesla cybertruck характеристики` | вес · технически характеристики · cyberbeast характеристики · 2024 tesla cybertruck характеристики |

`характеристики` (specs/features) is a **top completion on both seeds** — and is exactly what
this article delivers (a full spec review), unlike 7333 (the "is it in Bulgaria" news angle).

**Cannibalisation check:** `/wp/v2/search?search=Tesla Cybertruck характеристики` returns only
this post as a real match (two unrelated fuzzy hits, different topics entirely). Confirmed
distinct from 7333's `Tesla Cybertruck България` keyphrase — no overlap, matches the split
already recorded in `docs/SEO_EV_NEWS_TODO.md` ("owns the spec/`кибертрак` intent cluster...
do not target `Tesla Cybertruck България` on it; 7333 owns that").

**Keyword metrics:** nothing cached for `кибертрак`/`характеристики` phrases; not worth a
fresh paid pull — GSC + autocomplete already give a precise, real picture, consistent with
this project's established pattern for Bulgarian long-tail.

### Recommendation
**Focus keyphrase:** `Tesla Cybertruck характеристики` — matches what the article actually is
(a specs/characteristics review), confirmed by autocomplete as top demand on both the Cyrillic
and Latin seeds, distinct from 7333's phrase, and the article already ranks pos 6.5 for the
exact phrase with real impressions.

**Secondary:** `тесла кибертрак` (pos 9.4, striking distance), `тесла cybertruck` (pos 5.6,
closest to page 1), `Tesla Cybertruck технически характеристики`.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to H1, ~89 chars, all-caps, no keyphrase-first)* | `Tesla Cybertruck — пълни характеристики %%sep%% %%sitename%%` | **41** rendered |
| `_yoast_wpseo_metadesc` | *(absent)* | `Tesla Cybertruck характеристики: 800V архитектура, до 515 км пробег, 0–100 км/ч за 2.6 сек при Cyberbeast. Пълен преглед на версии, интериор и технологии.` | **154** |
| `_yoast_wpseo_focuskw` | *(empty)* | `Tesla Cybertruck характеристики` | — |

Reasoning: every number traces to the article's own spec table (800V, up to 515 km LR AWD,
2.6s 0-100 for Cyberbeast). Title drops the all-caps dramatic tagline in favour of the
keyphrase front-loaded, matching what `характеристики`-seeking searchers actually want.

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | Cybertruck | 41 | 13 | ✅ keep — established, headline entity |
| Entity | Tesla | 4 | 56 | ✅ keep — saturated hub, but genuinely the headline manufacturer of a single-model review |
| Entity | Beast | 383 | 1 | ❌ drop — below the 3–10 band (orphan), and only a passing spec-table row (Cyberbeast trim), not substantively discussed in prose |

**Gap noted:** no existing tag matches "characteristics/specs review" as a keyword-intent
term — checked `ревю` (review), no match. Not created speculatively; note for a future
deliberate batch-create if more spec-review posts need it.

---

## Phase C — Apply (inbound link only)

### Internal links
**Inbound — added this post → an existing post that should link here:** none proposed in this
pass (the direction was the reverse — see below).

**Outbound — this article now links to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|
| 7333 (`#EV114 – Има ли регистриран Tesla Cybertruck в България`) | `/ev-news/ev114-ima-li-registriran-tesla-cybertruck-v-blgariya/` | "дали изобщо има регистриран Tesla Cybertruck в България" | Closing "Благодарност" paragraph, appended after the existing thank-you sentence — a natural fit since that paragraph already names the Bulgarian owner who lent his own Cybertruck for the review |

### Applied
- [x] Inbound link written — target post 7333, one block edited, verified via post-write
      byte-diff (video embed, all galleries, spec table confirmed unchanged) and via the live
      rendered page (new link present exactly once).
- [ ] Metatags, tags, alt text — not yet run.

### Risks / notes
`post_content` is revision-covered — no separate CSV backup needed for this write. Yoast
fields (`_yoast_wpseo_title`/`_metadesc`/`_focuskw`) are all still empty on this post per the
live fetch at write time.

### Measurement
Baseline (GSC, 2026-07-17 → 2026-08-13, this URL): 68 impressions · 1 click · 1.47% CTR ·
position 4.8. Ledger row `7533-2026-08-14`, `verify_due` 2026-09-11. Keyword-level baseline not
pulled in this pass (no keyphrase chosen yet) — Phase A's future run should backfill it if it
matters for that comparison.
