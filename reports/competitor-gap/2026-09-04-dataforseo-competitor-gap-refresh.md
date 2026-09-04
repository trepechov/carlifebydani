# Competitor & Keyword Gap Refresh — carlifebydani.com vs. evpoint.bg

**Data source:** DataForSEO Labs API (`domain_rank_overview`, `domain_intersection`,
`ranked_keywords`), Google BG, `location_name: Bulgaria`, `language_code: bg`.
**Data pulled:** 2026-09-04. Refreshes and supersedes
[`2026-08-04-semrush-competitor-gap.md`](2026-08-04-semrush-competitor-gap.md) —
that report predates the 2026-08-13 site-language-bug fix; this one confirms
and sharpens it rather than contradicting it.

**Trigger:** Step 1 of
[`reports/seo-optimizations/2026-09-04-content-gap-recovery-strategy.md`](../seo-optimizations/2026-09-04-content-gap-recovery-strategy.md).

**DataForSEO as a Semrush substitute — confirmed working.** Semrush's
`domain_domains` (native keyword-gap report) was plan-blocked in the August
report and repeatedly plan-blocked in recent per-article optimize runs.
DataForSEO's `domain_intersection` endpoint did the equivalent job cleanly on
the first attempt, at both the "keywords only they rank for" mode
(`intersections: false`) and the "both rank, compare positions" mode
(`intersections: true`). No plan or access issues encountered. This resolves
the strategy doc's open assumption — DataForSEO is a viable, working
substitute for this kind of analysis, not just for keyword-volume lookups.

---

## Domain overview comparison

| | carlifebydani.com | evpoint.bg | Ratio |
|---|---|---|---|
| Organic keywords ranked (BG) | 71 | 267 | evpoint 3.8x |
| Estimated monthly organic traffic (ETV) | 615.8 | 9,159.2 | evpoint **14.9x** |
| Position #1 | 1 | 14 | — |
| Position #2–3 | 2 | 60 | — |
| Position #4–10 | 32 | 106 | — |
| Position #11–20 | 16 | 34 | — |
| Running paid ads | no | yes (2 kw) | — |

The traffic gap (14.9x) is much wider than the keyword-count gap (3.8x) —
evpoint.bg disproportionately wins the **highest-volume** terms, which is
exactly the generic-EV-term and charging-infrastructure cluster this
strategy targets.

carlifebydani.com's own footprint grew from 26 keywords (Semrush,
2026-08-04) to 71 (DataForSEO, 2026-09-04). Part of this is real — the
Zeekr/Porsche optimize passes landed in between — part of it is the two
tools sourcing from different keyword databases, so **the two counts aren't
directly comparable as a before/after delta**. Treat 71 as the new DataForSEO
baseline going forward, not as "+45 keywords gained."

---

## Structural finding: evpoint.bg is not an editorial competitor — it's a spec database + charging locator

The August report's "structured spec page" theory is **confirmed, and
sharper than stated.** evpoint.bg's ranking URLs follow a consistent pattern:

- `evpoint.bg/автомобили/<brand>` — brand hub pages (`/автомобили/kia`,
  `/автомобили/tesla`, `/автомобили/mg`, `/автомобили/volvo`)
- `evpoint.bg/автомобили/<brand>-<model>-<variant>` — one page per model/trim
  (`/автомобили/hyundai-kona-electric-39kwh`,
  `/автомобили/tesla-model-y-long-range`,
  `/автомобили/porsche-taycan-turbo-s`)
- `evpoint.bg/en/charging-stations-for-electric-vehicles`,
  `evpoint.bg/en/app` — a charging-station locator, apparently with its own
  app

This is a **database + directory/utility product**, not an editorial site
publishing articles. It's built to have one URL per model and one per
charging-related query, which is why it shows up across dozens of
model-name and charging queries at once. carlifebydani.com is an editorial
podcast-personality brand — matching evpoint.bg keyword-for-keyword would
mean building a spec-database section, not just writing better articles.
This is a real strategic fork worth naming explicitly rather than assuming
"optimize the existing article" closes every gap in cluster 4.

---

## The gap: 249 keywords evpoint.bg ranks for that carlifebydani.com doesn't at all

Full count from `domain_intersection` (`intersections: false`,
target1=evpoint.bg, target2=carlifebydani.com): **249 keywords**. Top 30 by
volume:

| Keyword | Vol/mo | Competition | evpoint pos | Topic |
|---|---|---|---|---|
| електрически автомобил | 12,100 | LOW | 10 | generic EV term |
| киа | 9,900 | LOW | 5 | Kia brand hub |
| ev charging stations | 8,100 | LOW | 26 | charging (EN) |
| тесла / tesla | 8,100 ×2 | LOW | 3–4 | Tesla brand |
| hyundai kona / хюндай кона | 4,400 + 2,900 | LOW/MED | 6–7 | model spec page |
| charging stations | 3,600 | LOW | 42 | charging (EN) |
| fiat 500 | 3,600 | LOW | 29 | model spec page |
| ev charging near me | 3,600 | LOW | 69 | charging (EN) |
| charging station | 3,600 | LOW | **9** | charging (EN) |
| mini cooper | 2,900 | LOW | 15 | model spec page |
| electric car charging stations | 2,900 | LOW | 47 | charging (EN) |
| charging station near me | 2,900 | LOW | 14 | charging (EN) |
| волво | 2,900 | LOW | 17 | Volvo brand |
| peugeot 2008 / dacia spring | 2,400 ea | LOW | 9 / 2 | model spec page |
| mg motors / mg motor / mg cars / mg bulgaria | 2,400/2,400/1,000/1,900 | LOW/MED | 21/35/36/5 | **MG brand hub — overlaps our planned MG cluster** |
| hyundai ioniq 5 | 2,400 | LOW | 3 | model spec page |
| tesla model 3 / model y | 1,900 ea | LOW | 5 / 3 | model spec page |
| porsche taycan | 1,900 | LOW | 10 | model spec page (we just optimized our own Taycan post — worth comparing after it has time to settle) |
| **зарядна станция** | 1,900 | LOW | **2** | charging (BG) |
| **зарядни станции** | 1,300 | LOW | **2** | charging (BG) |
| електрически автомобили | 1,000 | MEDIUM | 14 | generic EV term |
| polestar / vw id4 / bmw ix / škoda enyaq / renault zoe | 1,000 ea | LOW | 3–8 | model spec pages |

*(219 more rows beyond this top-30, mostly lower-volume model/spec long-tail
— full 249-row set was pulled but not reproduced here; available on request
by re-running the same `domain_intersection` call.)*

**A caution on the English-language rows** (`ev charging stations`,
`charging station near me`, etc.): these carry real volume in this BG-market
pull, but round, sizeable English-query volumes inside a small non-English-
primary market are worth a sanity check before committing content to them —
not flagged as wrong, just not independently re-verified against a second
source here.

---

## Head-to-head: 18 keywords where both domains rank in the same SERP

This is the fairer comparison — not "does evpoint have a page," but "when we
both compete for the same query, who wins":

| Keyword | Vol/mo | carlifebydani pos | evpoint pos | Winner |
|---|---|---|---|---|
| bmw i3 | 2,400 | 64 | 4 | evpoint — badly |
| tesla cybertruck | 1,300 | 34 | 7 | evpoint |
| tesla model s | 1,000 | 10 | 4 | evpoint — close |
| vw id 4 | 1,000 | 51 | 10 | evpoint — badly |
| mg zs цена | 880 | **7** | 10 | **carlifebydani** |
| бмв i3 | 320 | 15 | 7 | evpoint |
| i3 | 260 | 25 | 4 | evpoint |
| mazda mx 30 | 210 | **5** | 7 | **carlifebydani** |
| mg4 electric | 210 | **12** | 82 | **carlifebydani — big margin** |
| киа ниро електрик | 210 | 14 | 3 | evpoint |
| mazda mx-30 | 210 | **4** | 7 | **carlifebydani** |
| безплатни зарядни станции в българия | 210 | 14 | 6 | evpoint |
| зареждане на електрически автомобил | 170 | 14 | 2 | evpoint |
| представителство на тесла в българия | 170 | 39 | 44 | **carlifebydani — both poor** |
| bmw i4 m50 | 170 | 9 | 1 | evpoint |
| kia niro ev | 170 | 17 | 4 | evpoint |
| kia niro electric | 170 | 15 | 3 | evpoint |
| тесла сайбъртрък | 140 | 12 | 3 | evpoint |

**carlifebydani wins 5 of 18** — not the clean sweep the old report implied.
The losses cluster on higher-volume, more contested terms (bmw i3, cybertruck,
model s, vw id 4); the wins are on lower-volume, MG/Mazda-specific long-tail.
This matters for cluster 4 of the original plan: `bmw i3` and `vw id 4` are
the genuinely bad losses (pos 64 and 51 — worse than the August report's
pos-13 figure for bmw i3, not better), `tesla model s` is close enough to be
winnable with a normal optimize pass, and `mg zs цена` / `mg4 electric` /
`mazda mx 30` are **already ahead** — don't spend effort "fixing" those.

---

## carlifebydani.com's own current top keywords (context, not a gap)

For reference — the site's actual current ranking footprint (top 20 of 71
total, DataForSEO `ranked_keywords`), useful for spotting existing assets
worth protecting or building on:

| Keyword | Vol/mo | Position |
|---|---|---|
| byd bulgaria | 4,400 | 45 |
| станция за зареждане eldrive | 3,600 | 25 |
| bmw i3 | 2,400 | 64 |
| tesla supercharger | 1,900 | 20 |
| renault 5 | 1,600 | 8 |
| tesla cybertruck | 1,300 | 34 |
| mg zs | 1,000 | 16 |
| tesla model s | 1,000 | 10 |
| vw id 4 | 1,000 | 51 |
| citroen c5 aircross | 880 | 24 |
| hyundai inster | 880 | 8 |
| hyundai ioniq 6 | 880 | 22 |
| mg zs цена | 880 | 7 |
| toyota bz4x | 880 | 4 |
| ioniq 6 | 720 | 25 |
| lucid | 720 | 6 |
| pony | 720 | 23 |
| volvo ex30 | 720 | 31 |
| byd българия представителство | 590 | 49 |
| dongfeng m-hero | 590 | 7 |

**`byd bulgaria`** (4,400/mo, pos 45) stands out — the site's single highest-
volume ranking keyword, badly placed, with a dedicated article already live
(same diagnosis the August report made for this term at pos 46 via Semrush —
consistent across both tools). Not part of this pass's scope (MG/Renault/
charging-infra), but the clearest "content exists, execution is failing"
case on the whole site if a future pass wants a fast, well-evidenced target.

---

## What this changes in the recovery strategy

- **Cluster 4 (per-model spec pages) is smaller and more specific than
  assumed.** Only `bmw i3` and `vw id 4` are genuine bad losses among the
  18 head-to-head keywords; `tesla model s` is close; three others are
  already won. Points the fix-existing-losers stream at those first, not the
  whole model-post catalog.
- **Clusters 2/3 (charging infra, generic EV terms) are confirmed real and
  large** (`зарядна станция` pos 2, `зарядни станции` pos 2, `электрически
  автомобил` 12,100/mo, `тесла`/`tesla` 8,100/mo each) — but closing them
  means building spec-database/locator-style pages, a different content
  shape than carlifebydani has published before, not just a better blog
  post. Worth surfacing to editorial explicitly before scoping outlines.
- **MG brand-hub terms** (`mg motors`, `mg motor`, `mg cars`, `mg bulgaria`
  — evpoint.bg pos 5–36) independently justify the already-planned
  [MG cluster](../seo-optimizations/2026-09-04-mg-saic-cluster-plan.md) —
  two separate signals (GSC impressions and now competitor-gap data) point
  at the same brand.
- **`byd bulgaria`** (4,400/mo, pos 45, existing article) is a new, unplanned,
  high-confidence candidate surfaced by this refresh — not in scope for this
  pass, flagged for a future one.

---

*Generated 2026-09-04 via DataForSEO Labs API, BG market. Figures are a
point-in-time snapshot (DataForSEO's own data updates weekly) and will drift.*
