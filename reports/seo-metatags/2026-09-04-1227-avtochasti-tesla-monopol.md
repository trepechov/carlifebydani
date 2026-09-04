# SEO Optimization — Как се намират авточасти за Tesla в България? Има ли „монопол“ и какви са алтернативите?

**URL:** https://www.carlifebydani.com/ev-masters/kak-se-namirat-avtochasti-za-tesla-v-blgariya-ima-li-monopol-i-kakvi-sa-alternativite/ · **Post ID:** 1227 · **Category:** ev-masters
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** части за Tesla в България
**Ledger:** 1227-2026-09-04-C

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
EV Masters episode (published 2024-01-27, embedded YouTube video) featuring
Пламен Петков, who bought an EV and had to source Tesla crash-repair parts in
Bulgaria. The piece describes an informal network of body shops, importers
and investors around wrecked Teslas that has effectively controlled and
inflated parts prices for anyone outside that circle, notes Tesla later
announced plans for an official Bulgarian representation, then pivots to a
CTA: readers who want new/original parts can use the site's own **CLBD
Parts** service (`/clbd-parts/`). Real body content — `post_content` word
count and rendered page match; this is not an EV News thin-content case.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | Как се намират авточасти за Tesla в България? Има ли "монопол" и какви са алтернативите? - Car Life by Dani | 108 chars (no Yoast title set — falling back to raw H1 + suffix; quotes render unescaped) |
| `<meta name=description>` | *(absent — no `<meta name="description">` tag rendered at all)* | 0 |
| Focus keyphrase | *(empty)* | — |
| H1 | Как се намират авточасти за Tesla в България? Има ли "монопол" и какви са алтернативите? | — |
| Owned word count (Yoast `wordCount`) | 192 | — |
| Images without alt | Featured image (media id 1267) — `alt_text: ""` | 1 |
| Internal links out / in | Out: 1 (→ EVN67 Tesla Bulgaria post) + 1 (→ `/clbd-parts/`) · In: not checked this pass | — |

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, this URL):**
| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|
| tesla masters | 48 | 0 | 0.00% | 7.6 |
| авточасти за tesla | 5 | 0 | 0.00% | 20.4 |
| части за тесла | 4 | 0 | 0.00% | 31.2 |
| tesla master service | 1 | 0 | 0.00% | 20.0 |
| *(page total, incl. anonymized long tail)* | **295** | **6** | **2.03%** | **8.2** |

Only 58 of 295 impressions map to named queries — the rest are anonymized
long-tail GSC won't reveal individually, spread thin (consistent with a page
that ranks page-1 for a diffuse cluster of parts/service queries, none of
them individually strong). The named query with the most impressions
(`tesla masters`, pos 7.6) looks like brand/show confusion rather than
purchase intent — not a phrase to chase.

The number that matters: **position 8.2 with CTR 2.03%** is far below the
~5–8% norm for that rank. That gap is fully explained by the current state —
there is no meta description at all, and the raw title carries unescaped
`"` characters from the H1. This is a **presentation problem, not a content
problem**: the page already earns page-1-adjacent visibility for its topic
cluster; the snippet is what's failing to convert impressions to clicks.

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| части за tesla | части за tesla, **части за тесла модел 3**, **части за тесла модел y**, части за tesla 3 |
| авточасти за tesla | авточасти за тесла |
| сервиз за tesla | **сервиз за тесла в българия**, сервиз за тесла, сервиз за тесла варна/софия |
| tesla резервни части | тесла резервни части |
| монопол tesla | *(no completions)* |
| резервни части за тесла в българия / части за тесла българия | *(no completions)* |

Recurring modifiers: `модел 3` / `модел y` (model-specific long tail),
`в българия` (locale-qualified). No autocomplete demand for the "monopoly"
angle itself — that's this article's original framing, not a search term.

**Keyword metrics (bg, DataForSEO `google_ads/search_volume`, fresh 2026-09-04):**
| Phrase | Volume/mo | Competition | CPC | Note |
|---|---|---|---|---|
| части за тесла | 30 | MEDIUM (37) | €0.22 | **only phrase with measurable volume**; 12-mo range 10–70 |
| авточасти за тесла | — | — | — | no data (effective zero) |
| сервиз за тесла в българия | — | — | — | no data |
| части за тесла модел 3 / модел y | — | — | — | no data |
| резервни части за тесла | — | — | — | no data |
| tesla резервни части | — | — | — | no data |
| монопол tesla части | — | — | — | no data |
| тесла българия сервиз | 10 | — | — | cached 2026-09-04 (prior run) |

Banked to `data/seo-cache/keywords.csv` (part of this run's 152 total rows).

**SERP check (DataForSEO `serp/google/organic/live`, "части за тесла", bg):**
Top 10 is dominated by **commercial** listings: `avtosklad.bg` (#1),
`olx.bg` classifieds (#2), a Facebook parts-trading group (#3), **this
site's own `/clbd-parts/` page at #4**, `dreamcars77.com` classifieds (#5),
then more parts-catalog stores (`topavtochasti.bg`, `autopower.bg`,
`euavtochasti.bg`). Related searches: `Оригинални части за тесла`, `Части
за тесла модел y/3`, `Тесла българия`.

**⚠️ Cannibalization flag:** `/clbd-parts/` — this site's own commercial
parts-request page, which this very article links to — already ranks
**organic position 4** for the bare phrase "части за тесла". Setting this
EV Masters article's focus keyphrase to the same bare phrase would pit two
pages from this site against each other in the same SERP for a phrase a
transactional page already owns. Per the reuse/cannibalization rule, the
editorial article should **not** target "части за тесла" directly — it
should target the locale-qualified variant its title already promises
(`части за Tesla в България`), which keeps the two pages complementary
(informational analysis piece → CTA → transactional catalog page) rather
than competing.

`/clbd-parts/` has no Yoast fields set either (its `meta` on the WP REST
`pages` endpoint returns only `footnotes`, same signature as an
unauthenticated read elsewhere — but this is an authenticated call, so on
pages the Yoast fields are simply unset, consistent with it ranking on raw
on-page content alone). Out of scope for this report; noting it here so a
future pass on `/clbd-parts/` itself doesn't accidentally choose the same
keyphrase as this article.

**GA4:** not pulled this pass — GSC + SERP already gave a clear
presentation-not-content diagnosis; not needed to justify the metatag fix.

### Recommendation
**Focus keyphrase:** `части за Tesla в България` — locale-qualified variant
of the one phrase with real measured volume (30/mo), matches the article's
own title and content (parts availability specifically *in Bulgaria*), and
does **not** compete with `/clbd-parts/`'s existing #4 ranking for the bare
"части за тесла".

**Secondary:** `части за тесла` (the volume-bearing head term — used in
body/alt text, not as focus, precisely to avoid the cannibalization above),
`сервиз за тесла в българия`, `части за тесла модел 3`, `алтернативи части
Tesla`

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to raw H1 with unescaped quotes)* | `Как да намерите части за Tesla в България` | 41 (+19 suffix = 60) |
| `_yoast_wpseo_metadesc` | *(empty → no `<meta description>` rendered at all)* | `Има ли реален монопол върху частите за Tesla в България? Разказваме откъде да намерите резервни части и кои са алтернативите на познатите доставчици.` | 149 |
| `_yoast_wpseo_focuskw` | *(empty)* | `части за Tesla в България` | — |

### Proposed tags
Existing tags on the post: `Tesla` (id 4, count 59), `авточасти` (id 291,
count 4), `Покупка на автомобил` (id 249, count 2), `Ремонт` (id 152, count 4).

| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | Tesla | 4 | 59 | keep — saturated hub, but genuinely the headline entity |
| Entity | авточасти | 291 | 4 | keep — in-band (3–10), named throughout the prose |
| Keyword-intent | Ремонт | 152 | 4 | keep — in-band, matches the crash-repair-parts framing |
| Keyword-intent | Покупка на автомобил | 249 | 2 | **below 3–10 band** — flagging for Phase C to reconsider; the prose does mention Пламен Петков's EV purchase, but the tag itself sits at only 2 uses site-wide. Not a strong enough concept-fit to justify keeping a sub-band tag; not proposing a replacement — no better-banded existing term matches "car purchase" specifically |

**Gaps:** no existing tag for "монопол" (article's own framing) or
"алтернативи" — both would need to be created from scratch, and neither has
recurring demand elsewhere on the site yet (checked: zero results for
"монопол"). Not creating speculatively per the reuse-only rule; noting here
in case the monopoly/alternatives framing recurs on a future EV Masters
piece and a real cluster tag becomes justified.

---

## Phase C — Apply
_Written by `seo-article-apply`._

### On-page changes proposed
- [ ] **H1** — no change proposed; current H1 already carries the keyphrase entities naturally.
- [ ] **Subheadings** — no change; the single existing H3 ("А ако търсите оригинални авточасти за Tesla?") already fits the CTA section, no question-query gap to fill.
- [ ] **Image alt + title** (media id 1267, the featured image) —
  - `alt_text`: *(empty)* → `Търсене на части за Tesla в България — сервизи, доставчици и алтернативи`
  - `title`: `kak-se-namirat-chasti-za-tesla-v-bulgariya-ima-li-monopol-i-kakvi-sa-alternativite` (raw filename) → `Части за Tesla в България`

### Internal links
**Inbound — existing posts that should link here:** none proposed. Checked
posts predating 2024-01-27 for topical fit (`Tesla от Щатите – струва ли си
приключението?`, id 1479, 2023-08-25; `Колко ми струваше пътуването с Tesla
MY за цялата 2023 година?`, id 424, 2023-12-22). Neither has a natural
textual home: 1479's `post_content` is a bare video embed with zero body
paragraphs, and 424 is entirely charging/consumption statistics with no
sentence that a parts/monopoly link could extend without reading as bolted
on. Per the mechanics rule (never invent an unrelated sentence just to plant
a link), proposing zero rather than forcing either.

**Outbound — this article should link to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|
| Цените на авточасти за Tesla MY 2022 в Китай (id 4668, 2024-05-15) | `/publications/czenite-na-avtochasti-za-tesla-my-2022-v-kitai/` | „колко струват авточасти за Tesla MY в Китай" | End of ¶1, as a comparison clause |

**Note:** this is a genuine different-subtopic reference (pricing in a
different market), not a same-story sequel, so the date-unrestricted outbound
rule applies even though the target post is newer. **Not written by this
skill** — Phase C's write scope is Yoast postmeta, tags, media alt/title, and
*inbound* links to other posts (manifest rows 1/2/4/5); editing this post's
own `post_content` to insert the outbound anchor is row 3, which only Phase B
writes, and Phase B doesn't run on `ev-masters`. Recorded here as an
editorial recommendation for a manual edit or a future targeted pass, not
part of this run's write manifest.

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live in rendered `<title>`, `<meta name="description">` and `og:description` 2026-09-04
- [ ] Tags — declined (kept as-is, see Declined below)
- [x] Image alt + title written — media id: 1267 (confirmed in write response; not independently re-verifiable on the rendered page — this theme doesn't render the featured image as an inline `<img>` on the article page, only via `og:image`/schema, so there's no visible `alt=""` slot for it here)
- [x] Auto-linked `/tag/` count inside body prose (unchanged, tags not touched): `tesla` 6, `avtochasti` 4, `remont` 2, `pokupka-na-avtomobil` 2 (includes header/footer tag-pill instances, not just in-prose auto-links)

### Declined
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|
| Tags | Drop `Покупка на автомобил` (id 249, count 2, below 3–10 reuse band) | User chose "keep all 4 tags" | 2026-09-04 |

### Risks / notes
No cannibalization risk with the chosen focus keyphrase (see Phase A flag on
`/clbd-parts/` — avoided by not targeting the bare "части за тесла" phrase).
This is a presentation fix on a page that already has real content and
page-1-adjacent visibility (pos 8.2) — plausible for metatags alone to move
CTR; not a "needs more content" case.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03, 90d): impressions 295, clicks 6, CTR
2.03%, position 8.2. Re-check after 2–4 weeks (metatags/tags/alt — no body
content changed this pass). Ledger row: *(set on apply)*, `verify_due`: *(date_applied + 28d)*.
