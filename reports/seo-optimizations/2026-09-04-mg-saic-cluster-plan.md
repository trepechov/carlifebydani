# MG cluster — optimization plan

**Trigger:** ideation pass (2026-09-04) comparing GSC's 90-day query data
against which brand clusters `seo-article-optimize` has already touched.
MG surfaced as the single largest untapped cluster on the site — bigger by
impression volume than Porsche or Zeekr were before their passes, and with
zero rows in the ledger.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this cluster, why now

GSC (`https://www.carlifebydani.com/`, 2026-06-06 → 2026-09-03, query
dimension) shows **30+ distinct MG-model queries**, nearly all sitting at
position 4–10 — content that exists and gets found, but isn't converting
impressions into clicks because titles/descriptions are Yoast defaults.
Headline numbers:

| Query | Impressions | Clicks | Position |
|---|---|---|---|
| mg zs мнения | 1,349 | 38 | 7.0 |
| mg4 | 915 | 24 | 6.1 |
| mg car цена | 591 | 3 | 5.9 |
| mg zs ev | 384 | 18 | 5.1 |
| mg4 electric | 393 | 9 | 6.5 |
| mg 4 | 521 | 10 | 5.3 |
| mg zs | 288 | 3 | 8.2 |
| mg електрическа кола | 326 | 13 | 6.0 |
| mgs5 ev цена | 202 | 18 | 4.3 |
| mg s5 ev цена | 152 | 11 | 4.4 |
| mg джип / джип mg | 102 / 164 | 4 / 11 | 1.9 / 1.3 |
| mg electric | 161 | 1 | 6.2 |
| mg5 | 136 | 3 | 4.1 |
| mgs5 ev | 145 | 5 | 6.7 |
| mg s9 / mg s9 ev | 65 / 13 | 1 / 2 | 6.4 / 2.2 |
| mg cyberster цена | 27 | 2 | 4.3 |

None of this has been through Phase A/C. All 7 core posts are `publications`
category (same shape as the already-successful Zeekr pass) — Phase A + Phase
C only, no Phase B path.

## Why this order

Oldest → newest, same rule as the Porsche cluster
([`2026-09-03-porsche-cayenne-cluster-plan.md`](2026-09-03-porsche-cayenne-cluster-plan.md))
and enforced by commit `ec014b2`: a newer post links back to an older one in
the same story/brand line, so working oldest-first means each step's outbound
link target is already optimized when its turn comes. MGS5 EV (2025, the only
genuine hands-on review in the set) lands as the newest node — same anchor
role Taycan Turbo S plays for Porsche and the 7X review plays for Zeekr — and
should end up receiving inbound links from all 6 older posts.

---

## The cluster (7 posts, oldest first)

| Order | ID | Date | Title | Best-match query cluster (impr/90d) |
|---|---|---|---|---|
| 1 | **2800** | 2024-03-03 | MG4 приемливо качество на разумна цена | mg4, mg 4, mg4 electric(+цена), mg4 urban мнения, mg4 ev(+цена) — **~2,300 combined** |
| 2 | **3066** | 2024-03-04 19:19 | Cyberster нещо от MG за 100 годишнината на бранда | mg cyberster цена (27) — thin, brand-halo piece |
| 3 | **3032** | 2024-03-04 22:17 | MG 9 EV – Китайско чудо прикрито като Британска класика | no clean query match found yet — verify against "mg 9"/"mg9" in a fresh GSC pull before Phase A |
| 4 | **3303** | 2024-03-08 | MG5 – семейно комби на достъпна цена | mg5, mg 5, mg5 ev, mg5 electric, mg 5 ev, mg 5 цена — **~350 combined** |
| 5 | **2992** | 2024-03-09 | MG S9 EV – SUV с технология за смяна на батериите | mg s9, mg s9 ev — **~80 combined** |
| 6 | **3474** | 2024-03-12 | MG ZS EV – Компактен градски SUV | mg zs мнения, mg zs ev(+цена), mg zs, mg zs производитель/характеристики/цена, кола mg zs — **~2,400 combined, largest single-post opportunity on the site** |
| 7 | **6664** | 2025-04-18 | Скептичен бях… докато не тествах MGS5 EV (2025) | mgs5 ev(+цена), mg s5 ev(+цена) — **~590 combined**. Real hands-on review — **anchor, run last** |

URLs: `/publications/mg4-priemlivo-kachestvo-na-razumna-cena/` (2800) ·
`/publications/cyberster-neshto-ot-mg-za-100-godishninata-na-branda/` (3066) ·
`/publications/mg-9-ev-kitajsko-chudo-prikrito-kato-britanska-klasika/` (3032) ·
`/publications/mg5-semejno-kombi-na-dostpna-czena/` (3303) ·
`/publications/mg-s9-ev/` (2992) ·
`/publications/mg-zs-ev-kompakten-gradski-suv/` (3474) ·
`/publications/skeptichen-byah-dokato-ne-testvah-mgs5-ev-2025/` (6664)

None of the 7 have been through `seo-article-optimize` — checked
`reports/seo-optimizations/ledger.csv` (no MG IDs present) and
`reports/seo-metatags/` (no existing reports for any of them).

### Per-post notes

**1. Post 2800 — MG4 (oldest, run first).** Second-largest query cluster
after ZS. `mg4 мнения` already converts unusually well (18.75% CTR at
position 2.4) — check that page isn't this one before assuming it needs a
title rewrite; if it is, protect whatever's already working there.

**6. Post 3474 — MG ZS EV.** The single biggest opportunity in the whole
cluster — `mg zs мнения` alone (1,349 impr/90d) rivals the site's
brand-navigational queries. Prioritize this one if the cluster is run
partially rather than end-to-end.

**7. Post 6664 — MGS5 EV (2025 review), anchor, run last.** The only post in
the set that's an actual first-person test drive rather than a spec
write-up — same "hands-on review as cluster anchor" pattern as Taycan Turbo S
(Porsche) and the Zeekr 7X review. Should link back to all 6 older posts
where relevant (design lineage, comparisons within MG's EV lineup); they
should each pick up one inbound link to it once it's optimized.

### Excluded from this pass

- **IM L6 / L7 / LS6 / LS7** (posts 2811, 2857, 2894, 2937) — carry the `MG`
  tag but are a different brand (IM Motors, a separate SAIC/Alibaba joint
  venture, not MG itself) — tag looks like reuse-by-association rather than
  a real MG entity mention. No "IM L6"/"IM L7"-shaped query surfaced in the
  90-day GSC pull either, so there's no confirmed demand signal to chase yet.
  Worth a look as its own small cluster later if a fresh GSC/DataForSEO pull
  shows real search interest — not folded into this pass.
- **Post 2398** — "Автомобилно Изложение Женева 2024" (Geneva Motor Show
  roundup, `ev-news` category) mentions MG only as one bullet among Renault 5,
  Lucid Sapphire, Lucid Gravity and Renault Scenic. Multi-brand news roundup,
  not an MG-specific owned review — same reasoning as the pre-`news_csv`
  EV News exclusions in the Porsche plan. This post also carries Renault tags
  (see the Renault cluster plan) — treat as a shared low-priority bridge post,
  not a primary target in either cluster. If it's ever optimized, it's a
  content-editing decision (which brand does the title/keyphrase serve?), not
  a mechanical Phase A/C run.

---

## Execution

Oldest → newest, plain `/seo-article-optimize` calls, nothing custom:

```
optimize post 2800   (MG4)
optimize post 3066   (Cyberster)
optimize post 3032   (MG 9 EV — verify query match in Phase A before committing to a keyphrase)
optimize post 3303   (MG5)
optimize post 2992   (MG S9 EV)
optimize post 3474   (MG ZS EV — highest-value single post in the cluster)
optimize post 6664   (MGS5 EV review — anchor, run last, link back into all 6 above)
```

Each run hands its own dated report between phases
(`reports/seo-metatags/<date>-<id>-<slug>.md`) and gets its own ledger row
with its own `verify_due` date — same cadence as Zeekr/Porsche, no special
handling needed.
