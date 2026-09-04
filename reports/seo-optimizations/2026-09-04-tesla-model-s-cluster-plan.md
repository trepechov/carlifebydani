# Tesla Model S cluster — optimization plan

**Trigger:** DataForSEO competitor-gap refresh (2026-09-04) —
[`reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md`](../competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md).
`tesla model s` (1,000/mo, BG) is a head-to-head keyword where both domains
rank in the same SERP: carlifebydani position 10 vs. evpoint.bg position 4 —
the **closest race** of the three confirmed losses, and the most winnable
outright.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this cluster

Tesla is the podcast's namesake brand, and it shows — this is the largest
single-keyword content base on the site, 7 real posts spanning history,
Plaid variants, a drag-race review, a technical deep-dive, and a charging
comparison. None have been through `seo-article-optimize` (checked the
ledger and `reports/seo-metatags/` — no rows for any of the 7; one tagged
post, 7333, is excluded below because it's already optimized under a
different keyphrase).

**Verify in Phase A which specific URL is actually the one ranking at
position 10** before committing to a single target — this list is every
real Model S post on the site, not a confirmed single ranking page. Post
724 (below) is the best-guess anchor by scope and age, not a confirmed fact.

## Why this order

Oldest → newest, same `ec014b2` chronological-link rule as every other
cluster plan.

---

## The cluster (7 posts, oldest first)

| Order | ID | Date | Title | Category | Notes |
|---|---|---|---|---|---|
| 1 | **724** | 2024-01-14 | Историята на Tesla Model S | publications | oldest, broadest — best head-term match, **likely current pos-10 ranker (verify)** |
| 2 | **765** | 2024-01-17 | Tesla model S Plaid | publications | Plaid variant |
| 3 | **2359** | 2024-03-05 | Model S PLAID Шаро – зареждане и пътуване | publications | owner road-trip story |
| 4 | **4390** | 2024-04-24 | Tesla Model S и Model X Raven: Еволюция | publications | Raven refresh history |
| 5 | **4488** | 2024-04-29 | Tesla Model S Plaid – Състезание на 1/8 миля DRAG | ev-review | real hands-on/performance content |
| 6 | **4802** | 2024-06-07 | Tesla Model S P85 – U revision на мотора | publications | technical deep-dive |
| 7 | **5016** | 2024-07-04 | Сравнение при зареждане на IONIQ 5N, Tesla S Plaid, Tesla Y LR | publications | charging comparison |

URLs: `/publications/istoriyata-na-tesla-model-s/` (724) ·
`/publications/tesla-model-s-plaid/` (765) ·
`/publications/model-s-plaid-sharo-zarezhdane-i-ptuvane/` (2359) ·
`/publications/sledvashhoto-pokolenie-motori-na-tesla-s-x-motori-s-postoyanni-magniti-sled-2020/`
(4390) · `/ev-review/tesla-model-s-plaid-sstezanie-na-1-8-milya-drag/` (4488) ·
`/publications/tesla-model-s-p85-u-revision-na-motora/` (4802) ·
`/publications/zarezhdane-na-ioniq-5n-tesla-s-plaid-i-tesla-y-longrange/`
(5016)

### Per-post notes

**1. Post 724 — likely anchor, run first.** Oldest and broadest — "the
history of the Tesla Model S" is the closest thing on the site to a
general-purpose answer for the bare head term. Treat as the primary Phase A
target, but confirm via a URL-level GSC/DataForSEO check that this is
actually the page holding position 10 before finalizing.

### This cluster doesn't need to run end-to-end at once

Given its size, start with 724; the remaining 6 are real, legitimate
supporting content to work through opportunistically afterward, not a
blocking requirement for closing the specific "tesla model s" gap.

### Excluded from this pass

- **2555** (ALIENO hypercar prototype) — carries the tag but is unrelated;
  tag noise.
- **1909** (EVTour400 – multi-model Tesla lineup comparison) — a roundup
  across several Tesla models, not owned single-topic prose about the Model
  S specifically. Same "owned-prose-vs-cards" borderline call used to
  exclude the EVTour400 posts from the Renault and BYD clusters — optional,
  not required.
- **7333** (`#EV114 – Има ли регистриран Tesla Cybertruck в България`) —
  carries the `Model S` tag but is **already optimized** (ledger rows
  `7333-2026-08-14*`, Cybertruck-focused keyphrase). Do not re-run.

---

## Execution

Oldest → newest, starting with the likely anchor:

```
optimize post 724    (Историята на Tesla Model S — verify pos-10 ranker first)
optimize post 765    (Tesla model S Plaid)
optimize post 2359   (Model S PLAID Шаро – зареждане и пътуване)
optimize post 4390   (Tesla Model S и Model X Raven: Еволюция)
optimize post 4488   (Tesla Model S Plaid – DRAG 1/8 миля)
optimize post 4802   (Tesla Model S P85 – U revision на мотора)
optimize post 5016   (Сравнение при зареждане — IONIQ 5N / S Plaid / Y LR)
```

`ev-review` post (4488) and all `publications` posts here are Phase A +
Phase C only (no Phase B — gated to `ev-news`). Each run gets its own dated
report and ledger row with its own `verify_due`.
