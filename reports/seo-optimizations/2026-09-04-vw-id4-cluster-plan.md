# VW ID.4 cluster — optimization plan

**Trigger:** DataForSEO competitor-gap refresh (2026-09-04) —
[`reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md`](../competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md).
`vw id 4` (1,000/mo, BG) is a head-to-head keyword: carlifebydani position
51 vs. evpoint.bg position 10 — a 41-position deficit, the **largest
relative gap** of the three confirmed head-to-head losses, despite a real
hands-on review already existing.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this cluster

Small and focused — only 2 posts are genuinely about the VW ID.4. Neither
has been through `seo-article-optimize` (checked the ledger and
`reports/seo-metatags/` — no rows for either).

## Why this order

Oldest → newest, same `ec014b2` chronological-link rule as every other
cluster plan.

---

## The cluster (2 posts, oldest first)

| Order | ID | Date | Title | Category | Notes |
|---|---|---|---|---|---|
| 1 | **130** | 2023-10-08 | VW ID.4 GTX – Впечатляващо комфортен, стилен и практичен | ev-review | oldest, real hands-on review — **anchor, primary target** |
| 2 | **1060** | 2023-12-23 | EVTour400: VW ID.4 GTX + Hyundai IONIQ 6 AWD (charging/range comparison) | ev-review | real content, comparison format |

URLs: `/ev-review/vw-id4-gtx-vpechatlyavashto-komforten-stilen-praktichen/`
(130) ·
`/ev-review/vw-id4-gtx-hyundai-ioniq-6-awd-izmervane-razhoda-evtour400/`
(1060)

### Per-post notes

**1. Post 130 — anchor, primary target, run first.** The only proper
first-person review of the ID.4 on the site — best match for the bare "vw
id 4" head term. Post 1060, a comparison ride-along, should link into it.

Both posts are `ev-review` — Phase A + Phase C only, no Phase B (gated to
`ev-news`).

### Excluded from this pass

- **1672** (Volkswagen ID.7 – Премиера в България) — a different model
  (ID.7, not ID.4). Tag mismatch, not a real ID.4 mention.
- **1712** (EVTour400 Group B – VAG-group comparison) — the same recurring
  multi-brand bridge post already excluded from the Renault cluster plan
  (it's tagged both Renault and ID.4). Not a primary target here either.

---

## Execution

```
optimize post 130    (VW ID.4 GTX review — anchor, run first)
optimize post 1060   (EVTour400 — VW ID.4 GTX + Hyundai IONIQ 6, link back to 130)
```

Each run gets its own dated report and ledger row with its own `verify_due`.
