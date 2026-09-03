# Renault cluster — optimization plan

**Trigger:** same ideation pass as the MG cluster plan (2026-09-04) — second
runner-up cluster identified from GSC's 90-day query data, tag-cross-checked
against `Renault` / `Renault 4` / `Renault 5`.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this cluster

GSC (2026-06-06 → 2026-09-03) shows real, sustained demand across the
Renault 5 and Renault 4 electric line, all currently unoptimized:

| Query | Impressions | Clicks | Position |
|---|---|---|---|
| рено 5 | 1,110 | 9 | 6.2 |
| renault 5 | 928 | 6 | 9.1 |
| рено 5 електрическо | 536 | 2 | 7.2 |
| рено 5 електрик | 514 | 2 | 6.7 |
| renault 5 electric | 308 | 5 | 8.2 |
| reno 5 | 349 | 3 | 7.8 |
| рено 4 електрик | 255 | 3 | 6.5 |
| renault 4 e-tech | 81 | 1 | 8.2 |
| renault 4 | 80 | 2 | 9.3 |
| рено 4 цена | 31 | 2 | 8.9 |
| renault 4 plein sud | 31 | 4 | 3.9 |

**~4,200 combined impressions**, positions clustered 6–9 (page-1-bottom /
page-2-top — the same "found but not converting" signature as MG and the
earlier Porsche/Zeekr passes). No Renault post has an entry in
`reports/seo-optimizations/ledger.csv`.

## A cannibalization risk to resolve in Phase A, not skip past

Two posts cover the **same model under the same name**:

- **2455** (2024-02-29) — "RENAULT 5 E-Tech – истински малък, но
  изключително стилен" — reads as a pre-launch first-look (design/impressions
  framing, published before the car was on sale in BG).
- **6889** (2025-06-05) — "Renault 5 E-Tech Electric – завръщането на
  иконата" — 16 months later, likely the full launch/pricing article.

Both would naturally want the keyphrase "Renault 5 E-Tech" — don't let Phase
A pick the same phrase for both. Same treatment the Porsche cluster used to
split 625 (pre-launch development story) from 9216 (finished-trim review):
read both bodies first, then split by real angle — e.g. 2455 keeps a
design/first-impressions phrase, 6889 takes the pricing/specs/launch phrase.
`/wp/v2/search?search=Renault+5+E-Tech` should be re-checked at Phase A time
to confirm this is still the right split.

## Why this order

Oldest → newest, same `ec014b2` chronological-link rule as MG and Porsche.

---

## The cluster (6 posts to optimize + 1 already-optimized touchpoint, oldest first)

| Order | ID | Date | Title | Category | Status |
|---|---|---|---|---|---|
| 1 | **1027** | 2023-03-08 | Renault Megane E-Tech — една огромна крачка... | ev-review | available |
| 2 | **2455** | 2024-02-29 | RENAULT 5 E-Tech — истински малък... | publications | available — pre-launch framing (see split above) |
| 3 | **2622** | 2024-03-07 | Renault Scenic E-Tech — Автомобил на годината 2024 | publications | available |
| 4 | **6889** | 2025-06-05 | Renault 5 E-Tech Electric — завръщането на иконата | publications | available — launch/pricing framing (see split above) |
| 5 | **7407** | 2025-09-29 | Renault 4 E-Tech Electric — поглед в миналото | publications | available |
| 6 | **8836** | 2026-06-20 | Renault 4 Plein Sud — Хронология на отворения покрив | publications | available |
| — | **8968** | 2026-06-23 | #EV156 — Renault 4 Plein Sud & Zeekr 7GT | ev-news | **already optimized** (2026-08-15, keyphrase "Zeekr 7GT vs BMW" — a Zeekr-cluster pass, not Renault) |

URLs: `/ev-review/renault-megane-e-tech-ogromna-krachka-posoka-inovatsiya/`
(1027) ·
`/publications/renault-5-e-tech-istinski-malak-no-izkljuchitelno-stilen/`
(2455) ·
`/publications/renault-scenic-e-tech-avtomobil-na-godinata-2024/` (2622) ·
`/publications/renault-5/` (6889) ·
`/publications/renault-4-e-tech-electric-pogled-v-minaloto-zadvizhvan-ot-bdeshheto/`
(7407) · `/publications/renault-4-plein-sud/` (8836)

None of the 6 available posts have a ledger row or an existing report.

### Per-post notes

**1. Post 1027 — Renault Megane E-Tech (oldest, run first).** Real
hands-on review, `ev-review` category — same shape as the Taycan/7X anchor
posts in the other clusters. No direct GSC query match surfaced in this
pull (Megane E-Tech didn't show up by name) — treat as a from-scratch
keyphrase choice in Phase A rather than assuming it feeds the table above.

**4. Post 6889 — Renault 5 E-Tech Electric.** Newest node once 2455 and it
are both optimized — should end up as the one other posts link *into*, same
anchor logic as MGS5/Taycan/7X, **provided** the Phase A split above holds up
against the actual body content.

**6. Post 8836 — Renault 4 Plein Sud, newest standalone post.** Should link
back to 7407 (same Renault 4 E-Tech line) once both are optimized. Also a
natural candidate to add a cross-link *to* post 8968 (already live, Zeekr
angle) if the two share a factual thread worth citing — light-touch addition
only, not a re-run of 8968's own Phase A/C.

**8968 — do not re-run Phase A/C.** Already has a ledger row and applied
metatags under a Zeekr-focused keyphrase (correct for that pass — Zeekr 7GT
was the article's actual lead story). If this cluster wants a link from it,
that's a manual `post_content` edit to 8968, not a fresh optimize pass.

### Excluded from this pass (tag reuse, not confirmed as real Renault content)

Four more posts carry the `Renault` tag but don't read as being about a
Renault EV: **1712** (EVTour400 Group B — VAG-group comparison test), **872**
(EVTour400 Group A), **196** (Nissan Ariya review), **1036** (Citroën ë-C4
review). These likely mention Renault only in a comparison paragraph, not as
the article's subject — same "owned-prose-vs-cards" / tag-relevance reasoning
used elsewhere ([[feedback-tag-cap-owned-prose]]). Not verified against body
content — flagging as excluded-pending-check rather than asserting it;
worth a quick read before writing them off entirely if this cluster is
revisited.

**Post 2398** (Geneva Motor Show 2024 roundup) also carries a Renault tag —
see the MG cluster plan's "Excluded" section; treated as a shared bridge
post between the two clusters, not a primary target in either.

---

## Execution

Oldest → newest, plain `/seo-article-optimize` calls:

```
optimize post 1027   (Renault Megane E-Tech)
optimize post 2455   (Renault 5 E-Tech — pre-launch framing)
optimize post 2622   (Renault Scenic E-Tech)
optimize post 6889   (Renault 5 E-Tech Electric — launch/pricing framing, run after 2455)
optimize post 7407   (Renault 4 E-Tech Electric)
optimize post 8836   (Renault 4 Plein Sud — newest, anchor, link back into all 5 above)
```

Read 2455 and 6889's actual `post_content` before running either — confirm
the pre-launch/launch split in "A cannibalization risk" above still matches
reality before Phase A locks in two different keyphrases.

Each run gets its own dated report and ledger row with its own `verify_due`
date — same cadence as MG/Zeekr/Porsche, no special handling needed.
