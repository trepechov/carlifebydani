# Content-gap recovery strategy — carlifebydani's own core EV topic

**Trigger:** brainstorm (`ce-brainstorm`, 2026-09-04) challenging the
optimize-what-already-has-impressions approach used for the Zeekr/Porsche/MG/
Renault cluster plans. Counter-thesis: meaningful GSC impressions at position
4–10 mean a query is already found — the bigger opportunity may be topics
with low/zero impressions today because there's no (or losing) content
there, not because existing content underperforms. GSC can't surface that by
itself (it only reports queries we already appear for), so this needed
competitor data instead.

**Status:** Step 1 (refresh) done — see
[`reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md`](../competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md).
Steps 2a/2b not started.
**Created:** 2026-09-04.

---

## The evidence this is built on

[`reports/competitor-gap/2026-08-04-semrush-competitor-gap.md`](../competitor-gap/2026-08-04-semrush-competitor-gap.md)
already ran almost exactly this analysis a month ago, via Semrush
`domain_organic`/`domain_organic_organic` against the BG market. Findings:

- carlifebydani.com ranked for only **26 organic keywords total** in BG.
- The real competitor is **evpoint.bg**, not autobild.bg (autobild.bg is a
  larger generalist that shares almost no keyword overlap; evpoint.bg is the
  only domain with meaningful overlap with carlifebydani's own subject).
- Three of the ten identified gap clusters are **carlifebydani's own declared
  subject matter**, and evpoint.bg owns them outright:
  - **Cluster 2 — EV charging infrastructure**: `зарядна станция за
    електрически автомобили` (1,300/mo, evpoint pos 1), `зарядна станция`
    (1,600/mo, pos 2), etc. carlifebydani has only a thin `/tag/зареждане/`
    archive page at position 9 — no article.
  - **Cluster 3 — generic EV/Tesla head terms**: `електрически автомобил`
    (12,100/mo, evpoint pos 8), `тесла`/`tesla` (8,100+6,600/mo, pos 4),
    `електромобили`/`електромобил` (720/mo each, pos 1). No category or
    pillar page targets these at all.
  - **Cluster 4 — per-model EV spec/price pages**: carlifebydani already has
    dedicated articles and still **loses head-to-head** — `bmw i3` (evpoint
    pos 3 vs. carlifebydani pos 13), `tesla model s` (evpoint pos 3 vs.
    carlifebydani pos 12), plus `hyundai kona`, `tesla model y`, `cybertruck`,
    `tesla model 3` all held by evpoint.bg.
- Cluster 5 (EV maintenance / dashboard-warning evergreen content) was
  flagged unclaimed by anyone in the EV-specific space — noted here as
  adjacent, not part of this pass's target set.

**Why the old report needs re-running before anything else:** it blamed a
site-language misconfiguration for making Bulgarian informational phrases
unreachable and called clusters 1/5/6/9/10 "effectively unreachable" as a
result. That bug was fixed 2026-08-13 — after this report ran
(2026-08-04). The numbers above may already be stale, and cluster
reachability may have changed. Nothing in this plan should be treated as
final until the refresh (Step 1 below) confirms or corrects it.

---

## Decisions made in this brainstorm

1. **Priority track:** recover carlifebydani's own core EV topic authority
   (clusters 2/3/4 above), not a new evergreen-education hub and not a
   forward-looking new-model content calendar. Those two are real ideas —
   see **Deferred** below — just not this pass.
2. **First concrete step is a data refresh, not content work.** Re-run
   `domain_organic`/`domain_organic_organic` (Semrush MCP) against
   evpoint.bg; re-check whether `domain_domains` (the native keyword-gap
   report) is still plan-blocked (it was as of 2026-08-04); try DataForSEO as
   a substitute for domain-level gap analysis if so — this has **not been
   tested yet**, DataForSEO has only been used for keyword-volume lookups so
   far in this project, not competitor/domain analysis.
3. **Both work streams are in scope, once the refresh confirms targets:**
   - **Fix existing losers** (cluster 4): reformat/re-optimize the posts
     that already exist but lose head-to-head — bmw i3, tesla model s,
     hyundai kona, tesla model y, cybertruck, tesla model 3. Reuses
     `seo-article-optimize`, but likely needs a new check added to Phase A:
     look at what evpoint.bg's actual ranking page for the same model
     contains (structure, sections, data points) rather than assuming the
     old report's "structured spec page" read is correct — **that read has
     not been independently verified against a live evpoint.bg page**, it's
     inferred from the report's prose.
   - **Scope net-new pillar pages** (clusters 2-3): EV charging
     infrastructure and generic EV/Tesla head terms have no existing article
     to optimize — only a thin `/tag/` page. Closing this gap means new
     content, which nothing in the current pipeline produces
     (`seo-article-optimize` only ever touches posts that already exist in
     WordPress).
4. **Drafting scope for the new pillar pages: research + outline only.**
   Deliverable per topic is a target keyphrase, a competitor benchmark (what
   evpoint.bg's ranking page actually covers), and a structural outline — no
   drafted prose. Matches how `seo-keyphrase-research` already stops before
   writing copy. Actual article writing stays with the editorial side,
   outside this pipeline.

## Deferred (not out of scope forever, just not this pass)

- **Evergreen EV-education hub** (charging how-tos, range, real-world
  ownership costs) — cluster 5 in the old report already flagged this as
  unclaimed. Revisit once the core-topic recovery above is underway.
- **Forward-looking new-model content calendar** (upcoming Volvo, Hyundai,
  Mercedes EV launches) — a proactive, publish-before/at-launch play, distinct
  from gap-filling. Worth its own brainstorm when picked up; it wasn't
  explored in depth here beyond being named as a candidate direction.

## Open assumptions / risks — resolved at Step 1 (2026-09-04)

- **DataForSEO's suitability for domain-level competitor-gap analysis:
  confirmed.** `domain_intersection` and `domain_rank_overview` both worked
  cleanly against evpoint.bg with no plan-access issues — a full working
  substitute for Semrush's blocked `domain_domains`. Semrush's own block
  wasn't re-tested directly (no need, once DataForSEO worked).
- **The page-structure theory: confirmed, and sharper than assumed.**
  evpoint.bg isn't winning through better articles — it's a spec-database +
  charging-locator product (`/автомобили/<brand>-<model>` per-model pages,
  a charging-station directory, its own app). Closing clusters 2/3 means
  building that kind of page, not writing a better blog post. See the
  refresh report's "Structural finding" section.
- **Cluster boundaries: sharper than the August report implied.** Of the 18
  keywords where both domains actually compete in the same SERP, carlifebydani
  already wins 5 (mg zs цена, mazda mx 30, mazda mx-30, mg4 electric,
  представителство на тесла в българия) — cluster 4's real target list is
  just `bmw i3` (pos 64 vs. evpoint pos 4) and `vw id 4` (pos 51 vs. pos 10),
  with `tesla model s` (pos 10 vs. pos 4) as a close third. Not the whole
  model-post catalog.
- **New, unplanned finding:** `byd bulgaria` (4,400/mo, carlifebydani pos 45,
  dedicated article already live) is the single highest-volume keyword the
  site ranks for at all, badly placed — flagged for a future pass, not this
  one.

---

## Execution (once picked up)

```
Step 1 — DONE (2026-09-04). Refreshed via DataForSEO Labs API
         (domain_rank_overview, domain_intersection, ranked_keywords) —
         see reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md.

Step 2a — Fix existing losers: narrowed by the refresh to bmw i3 (pos 64 vs.
          evpoint pos 4) and vw id 4 (pos 51 vs. pos 10) as the real losses,
          plus tesla model s (pos 10 vs. pos 4) as a close third. Look at
          evpoint.bg's equivalent /автомобили/<model> page for each, then run
          seo-article-optimize with that benchmark informing Phase A's
          structural recommendations (not just metatags). Do NOT touch
          mg zs цена, mazda mx 30/mx-30, or mg4 electric — carlifebydani
          already wins those head-to-head.

Step 2b — Scope new pillar pages: for the confirmed cluster 2/3 topics
          (EV charging infrastructure — зарядна станция/зарядни станции,
          both evpoint pos 2; generic EV/Tesla head terms — електрически
          автомобил 12,100/mo, тесла/tesla 8,100/mo each), produce keyphrase
          + competitor benchmark + outline only. No drafting, no publish.
          Flag to editorial upfront that evpoint.bg's ranking pages are
          structured spec-database/locator pages, not articles — the outline
          should reckon with that content-shape gap explicitly, not propose
          a normal blog post as a like-for-like competitor.
```

Steps 2a and 2b can run in parallel; neither depends on the other.

**Not in this pass, flagged by the refresh:** `byd bulgaria` (4,400/mo,
carlifebydani pos 45, dedicated article already live) — the site's single
highest-volume ranking keyword, badly placed. Worth its own pass later.
