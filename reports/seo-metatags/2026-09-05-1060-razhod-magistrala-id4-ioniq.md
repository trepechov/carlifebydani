# SEO Optimization — Пътуваме с VW ID.4 GTX и Hyundai IONIQ 6 AWD, за да измерим разхода и да се подготвим за EVTour400

**URL:** https://www.carlifebydani.com/ev-review/vw-id4-gtx-hyundai-ioniq-6-awd-izmervane-razhoda-evtour400/ · **Post ID:** 1060 · **Category:** ev-review
**Prepared:** 2026-09-05 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Разход на електромобил на магистрала
**Ledger:** `1060-2026-09-05-C`

**How this post surfaced:** direct user request, following up on post 130
(VW ID.4 GTX review, applied earlier today) — flagged there as a
cannibalization candidate to resolve with its own differentiated
keyphrase, and explicitly requested to receive an outbound backlink to 130.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A **561-word** real-world consumption test (**561 words**, Yoast wordCount
537) — part of the EVTour400 trip-planning prep. Two authors independently
took a VW ID.4 GTX and a Hyundai IONIQ 6 AWD on the same route
(София→Пловдив→Карлово→София), logging battery %, range, trip km,
kWh/100km, time, average speed and SOC at each leg — and adds a third car's
data (Hyundai IONIQ 5, tested 2 weeks earlier on the same route) for a
genuine 3-way comparison. Real findings: ID.4 GTX consumed 18.5/17.3/15.8
kWh/100km across the three legs vs. IONIQ 6's 16.7/15.4/13.7 and IONIQ 5's
20.9/20.3/17.3 — roughly a 10% efficiency gap between IONIQ 5 and ID.4
despite a smaller aerodynamic difference (Cd 0.288 vs 0.28) than the
consumption gap would suggest. Also notes real consumption drops ~20% on
non-highway roads vs. highway, and a shared quirk in VW's and Hyundai's
lane-keep/autonomous-driving systems losing track at the same road
locations. Genuinely first-hand, data-driven content — not a spec-sheet
rehash.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(empty → falls back to post title)* `Пътуваме с VW ID.4 GTX и Hyundai IONIQ 6 AWD, за да измерим разхода и да се подготвим за EVTour400 - Car Life by Dani` | ~124 chars, way over budget |
| `<meta name=description>` | absent | — |
| Focus keyphrase | **`VW ID.4 GTX`** (stale — pre-existing from before this pipeline touched the post; now wrong, since post 130's own optimization today claimed that exact phrase for its full-model review) | — |
| H1 | matches post title verbatim | — |
| Owned word count | **537** (Yoast schema) | — |
| Images without alt | 1 body image (media 1657) — its **rendered `alt` has a real typo** ("Сражнение" instead of "Сравнение" = "comparison"), even though the media library's own `alt_text` field is empty (the alt was hardcoded directly into the `<img>` tag at insertion time, not synced from the library); featured image media 1063 also `alt=""`, `title` a raw slug | |
| Internal links out / in | **0 internal** (1 correctly-external YouTube link) / 0 inbound confirmed | |

`post_excerpt` is non-empty — noted per the standing trap, not touched.

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, page-level, this URL — reused from the
shared cached pull):** essentially **zero visibility** — 1 impression on a
garbled query ("vw ioniq", pos 9.0). A from-scratch keyphrase choice, same
situation as post 1027 earlier this session.

**Cannibalization check — the critical finding this pass:** `/wp/v2/search?search=IONIQ+6`
surfaces post **4902** ("Hyundai IONIQ 6 – Всичко, което знаем за разхода и
зареждането", 2024-07-07, `publications`, real existing traffic per
`docs/SEO_BACKLINK_TARGETS_TODO.md` — 616 impr/21 clicks/6.8 pos in an
earlier pull) — a **dedicated** IONIQ 6 consumption/charging reference
piece. That post, not this one, is the natural owner of any
"Hyundai IONIQ 6 разход" phrasing. Also checked: "VW ID.4 GTX" itself is
now owned by post 130 (applied earlier today). **Neither of this post's two
main cars' names can be the keyphrase without cannibalizing an existing or
sibling post** — the genuine differentiator here is the **3-way real-world
highway-consumption comparison** itself, not any single model.

**Google autocomplete:** thin across every candidate tried (`ioniq 6
разход`, `id.4 gtx разход`, `hyundai ioniq 6 vs vw id.4`) — real BG search
volume for this specific comparison angle essentially doesn't exist yet.
Same "absent volume isn't evidence of no demand" situation as elsewhere in
this pipeline, but here it's genuinely a low-ceiling niche piece, not a
hidden big opportunity — realistic expectations should reflect that.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-05):** no volume data for
any comparison-specific phrase tried (`ioniq 6 разход`, `id.4 разход`,
`hyundai ioniq 6 разход`, `разход на електромобил на магистрала`, `hyundai
ioniq 6 vs vw id 4`). For scale/context only (not candidates, both
cannibalization-blocked): `hyundai ioniq 6` bare = 880/mo (way too generic,
owned by 4902's territory), `ioniq 6 цена` = 30/mo. Nothing banked this run
— every specific candidate returned no data, and the two bare high-volume
phrases are deliberately not being targeted.

### Recommendation
**Focus keyphrase:** `Разход на електромобил на магистрала` (highway EV
consumption) — the one honest, differentiated phrase this specific
article's actual data genuinely satisfies (the highway-vs-non-highway ~20%
consumption swing, the 3-car real-world comparison), without stepping on
130's "VW ID.4 GTX" or 4902's "Hyundai IONIQ 6 разход" territory. No
measurable search volume found, so this is a from-scratch, modest-ceiling
bet — flagging that plainly rather than overselling it.
**Secondary:** `VW ID.4 GTX разход`, `Hyundai IONIQ 5 IONIQ 6 сравнение`,
`EVTour400`.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~124 chars, way over budget)* | `Разход на електромобил на магистрала: тест %%sep%% %%sitename%%` | 42 + 19 suffix = 61 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Разход на електромобил на магистрала: сравнихме реални данни от VW ID.4 GTX, Hyundai IONIQ 5 и IONIQ 6 на едно и също трасе. Вижте пълните таблици.` | 147 |
| `_yoast_wpseo_focuskw` | `VW ID.4 GTX` (stale, now wrong — see above) | `Разход на електромобил на магистрала` | — |

Every metadesc fact traces to the real data tables (three cars, same
route) — nothing invented.

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | E-GMP | 162 | 8 | ✅ keep — in-band, the Hyundai platform underlying both IONIQ models |
| Entity | EVTour400 | 69 | 9 | ✅ keep — in-band, this site's own recurring trip-prep series |
| Entity | GTX | 13 | 2 | ✅ keep despite below-band — named, part of the tested car |
| Entity | Hyundai | 15 | 21 | ✅ keep despite saturated (ceiling) — genuinely one of the two headline brands compared here, not a passing mention |
| Entity | ID.4 | 12 | 4 | ✅ keep — in-band, named repeatedly |
| Entity | IONIQ 6 | 120 | 5 | ✅ keep — in-band, one of the three cars with full data |
| Entity | MEB | 55 | 9 | ✅ keep — in-band, VW's platform |
| Entity | VW | 9 | 13 | ✅ keep — established, one of the two headline brands |
| Keyword-intent | Разход | 139 | 8 | ✅ keep — in-band, the article's whole subject |
| Entity | **IONIQ 5** | **16** | **10** | ➕ **add — in-band, genuinely named with its own full data table** (not a passing mention — the whole 3-way comparison hinges on it) |

**Final tag set (10):** `[162, 69, 13, 15, 12, 120, 55, 9, 139, 16]`

---

## Phase C — Metatags, tags, alt text, links
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
approved._

### Internal links — research
**Outbound — explicitly requested: post 130** (VW ID.4 GTX review,
applied earlier today). Natural anchor already exists in the opening
paragraph: *"аз бях взел за ревю **VW ID.4**, a Чочо беше взел също за ревю
Hyundai IONIQ 6."* — wrap "VW ID.4" with a link to 130. Accurate (130 is
exactly the dedicated review this sentence refers to), new→old direction
(1060 is 2023-12-23, 130 is 2023-10-08 — 130 is in fact slightly older),
and not a same-story-sequel (a field-test citing the dedicated review it
drew the car from, ordinary further-reading).

**Also considered, not proposed this pass:** post 4902 (Hyundai IONIQ 6
consumption/charging reference, 2024-07-07, postdates this post) — a
plausible future "deeper IONIQ 6 data" outbound link, same-subtopic and
date-unrestricted, but not part of the explicit request this pass. Noting
it here rather than writing an unrequested link.

**Inbound:** not researched this pass — out of scope for a request scoped
to "optimize 1060, add a backlink to 130."

**Backlink-target check:** 130 has ledger row `130-2026-09-05-C` (already
optimized today) — no `docs/SEO_BACKLINK_TARGETS_TODO.md` entry needed.

### Additional finding — alt-text typo (not the featured image)
Body image (media 1657)'s **rendered** alt text (hardcoded in the `<img>`
tag, not the media library's own field, which is empty) reads *"Сражнение
Разход VW ID.4 GTX Hyundai IONIQ 6 AWD"* — "Сражнение" (battle/combat) is a
real-word typo for "Сравнение" (comparison). Proposing to fix this directly
in `post_content` (same mechanics as the internal link edit), and to also
set the media library's own `alt_text`/`title` fields to match for future
consistency.

### Proposed image alt + title
| Media | Field | Before | After |
|---|---|---|---|
| 1063 (featured) | `alt_text` | *(empty)* | `Тест на разхода: VW ID.4 GTX и Hyundai IONIQ 6 AWD` |
| 1063 (featured) | `title` | raw slug | `Разход VW ID.4 GTX и Hyundai IONIQ 6` |
| 1657 (body, rendered alt fix) | `alt` (in `post_content`) | `Сражнение Разход VW ID.4 GTX Hyundai IONIQ 6 AWD` (typo) | `Сравнение на разход VW ID.4 GTX и Hyundai IONIQ 6 AWD` |
| 1657 (body) | library `alt_text`/`title` | both empty/raw | set to match the corrected text, for consistency |

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live
  (`<title>`, `<meta name="description">` and `og:description` all match
  exactly; the stale `VW ID.4 GTX` focuskw is now corrected).
- [x] Tags — replaced with `[9, 12, 13, 15, 16, 55, 69, 120, 139, 162]`
  (added IONIQ 5), confirmed live.
- [x] Image alt + title — media 1063 (featured) written and confirmed
  live.
- [x] Alt-text typo fixed — media 1657's library `alt_text`/`title` set,
  **and** the hardcoded `alt` inside `post_content` corrected from
  "Сражнение" to "Сравнение" (the word that's actually rendered on the
  page). Both confirmed live.
- [x] Outbound link — added to the opening paragraph, wrapping the
  existing "VW ID.4" mention with a link to post 130. Built and verified
  the spliced content programmatically (both edits — link + typo fix —
  single-occurrence fragment matches, all block counts unchanged, link
  count 1→2, char delta exactly the combined 116 chars) before sending via
  `curl` with the `seo-bot` Keychain password. Confirmed byte-identical
  live response.
- [x] Auto-linked `/tag/` count inside body prose: **9** (`Hyundai` ×2,
  `IONIQ 6` ×2, `ID.4` ×2, `EVTour400` ×1, `IONIQ 5` ×1, `VW` ×1) —
  confirms yet again the theme's 1×-per-tag cap fix is not deployed live
  (same finding as every other post this session). `E-GMP`, `GTX`, `MEB`
  and `Разход` did not auto-link in body prose (checked, genuinely absent
  as standalone text matches there) — not something this pass changes.

### Declined
_None — all three approval-gate groups (metatags+tags, image alt/title +
typo fix, outbound link) were approved in full._

### Risks / notes
- This is a genuinely low-search-ceiling piece — realistic expectations
  should be modest; the value here is mostly internal link equity and
  correctness (fixing the stale wrong focuskw, the alt typo), not a big
  traffic swing.
- Flagged for a future pass: post 4902 as a possible outbound target once
  this post is revisited, or as its own inbound-link opportunity from
  1060's IONIQ 6 data section.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): 1 impr / 0 clicks / — pos. Re-check
in 4–8 weeks rather than the usual 2–4 — from a near-zero baseline, a
shorter window is unlikely to show anything meaningful either way.
