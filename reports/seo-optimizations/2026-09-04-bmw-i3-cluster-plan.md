# BMW i3 cluster — optimization plan

**Trigger:** DataForSEO competitor-gap refresh (2026-09-04) —
[`reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md`](../competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md).
Of the 18 keywords where carlifebydani.com and evpoint.bg rank in the same
SERP, `bmw i3` (2,400/mo, BG) is the worst loss: carlifebydani position 64
vs. evpoint.bg position 4 — the biggest absolute position gap of any
head-to-head keyword on the site, on real volume.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this cluster

carlifebydani has never actually reviewed the BMW i3 as a car — all 4
tagged posts are interview/history/accessory-angle content (owner
interviews, a "how BMW built it" history piece, a tires-and-wheels note).
That fits the site's personality-led format, but it may itself be part of
why a spec-focused competitor wins the head term: evpoint.bg's ranking page
is a database entry in a `/автомобили/<brand>-<model>` pattern, not an
article (see the refresh report's "Structural finding" section) — it's
built to directly answer "what is a BMW i3," which none of these four posts
set out to do on their own.

None of the four have been through `seo-article-optimize` — checked
`reports/seo-optimizations/ledger.csv` and `reports/seo-metatags/`, no
rows/reports for any of them.

## Why this order

Oldest → newest, same `ec014b2` chronological-link rule as every other
cluster plan on this site: a newer post links back to an older one in the
same story line, so working oldest-first means each step's outbound link
target is already optimized when its turn comes.

---

## The cluster (4 posts, oldest first)

| Order | ID | Date | Title | Category | Notes |
|---|---|---|---|---|---|
| 1 | **1240** | 2023-05-19 | Христо Бъчваров – Защо BMW i3 е любимата му кола | ev-masters | interview, real owner content |
| 2 | **188** | 2023-07-01 | Христо Бъчваров – На дълбоко в техническите тайни на BMW i3 | ev-masters | interview, technical detail |
| 3 | **5457** | 2024-09-17 | BMW i3 гуми и джанти | publications | narrow scope (tires/wheels), real but thin |
| 4 | **5924** | 2024-12-27 | BMW i3 – Историята как BMW го създаде и защо | ev-masters | newest, broadest scope — **anchor, primary target for "bmw i3"** |

URLs: `/ev-masters/hristo-bchvarov-zashho-bmw-i3-e-lyubimata-mu-kola/` (1240)
· `/ev-masters/hristo-bachvarov-dalboko-v-tehnicheskite-tayni-bmw-i3/` (188)
· `/publications/bmw-i3-gumi-i-dzhanti/` (5457)
· `/ev-masters/bmw-i3-istoriyata-kak-bmw-go-szdade-i-zashho/` (5924)

### Per-post notes

**4. Post 5924 — anchor, primary target, run last.** Newest and broadest of
the four — "the history of how BMW built it and why" is the closest thing
on the site to a general-purpose BMW i3 answer, so it's the best-positioned
page to actually rank for the bare "bmw i3" head term. The three older posts
should link into it once it's live.

`ev-masters` category — same as the eBag post's precedent: Phase A + Phase
C only, no Phase B (Phase B is gated to `ev-news`). Post 5457 is
`publications`, same treatment.

### A caveat worth setting expectations with

evpoint.bg's ranking page for this term is a spec-database entry, not an
article. Closing a 60-position gap may need more than a metatags/content
pass — Phase A should look at evpoint.bg's actual ranking page before
assuming a normal optimize pass closes it outright.

### Excluded from this pass

Post **1088** (Mazda MX 30 review) carries the `i3` tag, presumably from a
comparison mention in the body — Mazda MX 30 is one of the keywords
carlifebydani already **wins** against evpoint.bg (pos 5 vs. pos 7 — see the
refresh report's head-to-head table). Leave it alone; don't let a BMW i3
pass touch it.

---

## Execution

Oldest → newest:

```
optimize post 1240   (Хр. Бъчваров – защо BMW i3 е любимата му кола)
optimize post 188    (Хр. Бъчваров – техническите тайни на BMW i3)
optimize post 5457   (BMW i3 гуми и джанти)
optimize post 5924   (BMW i3 – Историята — anchor, run last, primary target)
```

Each run gets its own dated report and ledger row with its own `verify_due`
— same cadence as every other cluster.
