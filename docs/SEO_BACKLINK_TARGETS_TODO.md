# Backlink-Target Backlog

Created 2026-09-04. Tracks posts that gained a fresh internal link — either
as the target of an outbound link written into another post's new prose, or
as the target of an inbound-link edit made to an older post — from a
`seo-article-optimize` run, **before they'd been through their own
optimization pass**.

Companion to [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md), but a different
discovery mechanism: that file is traffic-driven (GSC impressions found a
candidate); this one is **link-equity-driven** (something on the site now
points at a candidate, so it's worth capturing that inbound authority before
it goes stale). A post can land on both lists independently.

**This file is meant to be maintained automatically.** Phase B
(`ev-news-transcript-content`) and Phase C (`seo-article-apply`) both check,
after writing any internal link, whether the link's target has already been
through this pipeline — see [`_shared/constants.md`](../.claude/skills/_shared/constants.md)
§ "Backlink-target tracking" for the exact mechanism. Treat entries added
that way as authoritative; this file should rarely need a manual full
re-audit like the one that seeded it.

Status legend: `[ ]` not started · `[~]` in progress · `[x]` done · `[?]` needs a decision

**Priority = 90-day GSC page impressions at time of discovery** (highest
first) — matches this pipeline's own "distance beats volume" philosophy.
Re-sort a stale entry if its numbers have moved a lot by the time it's
picked up; a fresh Phase A run will get the current numbers regardless.

---

## Backlog

| Status | Post ID | Title | Category | Backlink source(s) | GSC 90d (impr / clicks / CTR / pos) | Report |
|---|---|---|---|---|---|---|
| [ ] | 5446 | #EVN72 – 100 ТЕСЛИ на Lightshow в София, 300 ТЕСЛИ Lightshow във Велико Търново | ev-news | 5574 (outbound, 2026-10-22) | — (not pulled) | — |
| `[ ]` | 1042 | BMW i4 M50 (G26) – По-бързо от Tesla M3LR, но с по-малък пробег | ev-review | 8824 (outbound, 2026-10-22) | 1000 / 9 / 0.90% / 6.1 (2026-06-18→09-15) | — |
| `[ ]` | 420 | Mercedes EQS 580 4Matic – Комфорт на всяка цена | ev-review | 9202 (outbound) | 3806 / 44 / 1.16% / 6.5 (2026-06-25→09-22) | — |
| `[ ]` | 6823 | Volvo EX90 – лукс, иновация и безопасност | publications | 8976 (outbound) | 828 / 8 / 0.97% / 4.8 (2026-06-25→09-22) | — |
| `[ ]` | 8075 | Eldrive откри втора сателитна зарядна станция в София | publications | 9350 (inbound-edit) | 314 / 6 / 1.91% / 8.2 | — |
| `[ ]` | 7631 | Hyundai Concept 3 – за първи път в България | publications | 9383 (inbound-edit) | 95 / 1 / 1.05% / 7.0 | — |
| `[ ]` | 3032 | MG 9 EV – Китайско чудо прикрито като Британска класика | publications | 4115 (outbound) | 91 / 1 / 1.10% / 5.1 | — |
| `[ ]` | 7351 | Tesla Lightshow – Ihtiman Airport LBHT | publications | 5350 (outbound) | 80 / 5 / 6.25% / 6.3 | — |
| `[ ]` | 1509 | Защо спряха автобусите в Осло? Какво ново около Tesla Model Y Juniper? | ev-news | 5240 (outbound) | 0 / 0 / — / — (no impressions in window) | — |
| `[ ]` | 5016 | Сравнение при зареждане на Hyundai IONIQ 5 N, Tesla S Plaid и Tesla Y Long Range | publications | 3957 (outbound) | 336 / 3 / 0.89% / 5.8 | — |
| `[ ]` | 7491 | Mercedes CLA (електрически): платформа, задвижвания, батерии, аеродинамика и технологии | publications | 9202 (outbound) | 468 / 8 / 1.71% / 6.6 (2026-06-25→09-22) | — |
| `[ ]` | 196 | Nissan Ariya – „Най-забавният педал на спирачката" | ev-review | 1027, 2622 (both outbound) | 0 / 0 / — / — (no impressions in window) | — |
| `[ ]` | 3946 | Tesla ще представи Robotaxi на 8ми Август | publications | 9447 (outbound) | 0 / 0 / — / — (no impressions in window) | — |
| `[ ]` | 8340 | #EV146 – FSD в Холандия; Model S/X Signature Edition | ev-news | 9486 (outbound) | not pulled this run | — |
| `[ ]` | 7821 | #EV127 – Cybertruck с FSD в Прага! | ev-news | 9486 (outbound) | not pulled this run | — |
| `[ ]` | 8061 | #EV136 – Volvo официално представи новия EX60 | ev-news | 8976 (outbound, same-story-sequel citation, new→old) | 3 / 0 / 0% / 8.0 (2026-06-25→09-22) | — |

GSC window: 2026-06-06 → 2026-09-03, page-dimension pull filtered to each
URL (9099's baseline below uses its own original 2026-05-15→2026-08-13
window instead — see its row).

> **2398 was missed at write time** — both 6889's and 2455's own Phase C
> runs (2026-09-04) added outbound links to it but neither logged it here,
> despite the "Backlink-target tracking" trap existing specifically for
> this. Caught on a manual check the next day (2026-09-05), not by the
> automated step. Worth double-checking this doesn't recur on the next few
> runs rather than assuming the mechanism is now reliably firing.
>
> **A second, different kind of miss (2026-09-05):** 8836's own Backlog row
> (added when 8968 linked to it) was never moved to Done when 8836 itself
> was applied later the same day — the tracking mechanism only covers
> *adding* rows when a new link appears, not *closing* a row when its
> subject gets optimized. Caught on this manual check, not automatically.
> Worth remembering to check the Backlog table for the post id currently
> being optimized, not just to log new targets, each time this pipeline
> runs.

### Also flagged — citation owed, not yet written

Not a backlink yet (nothing points at these), but a `seo-article-optimize`
report explicitly identified a same-story-sequel citation that should be
added to the post — different from the table above (there, the link already
exists; here, it's a known TODO for a future edit). Listed so it isn't lost.

| Status | Post ID | Title | Category | What's owed | Source report |
|---|---|---|---|---|---|
| `[x]` | 7407 | Renault 4 E-Tech Electric – поглед в миналото, задвижван от бъдещето | publications | ~~Should consider citing back to 6889 (new→old)~~ — **done 2026-09-05**: 7407's own Phase C wrapped its existing "Renault 5" mention (AmpR Small paragraph) with a link to 6889 | [2026-09-05-7407-renault-4-e-tech.md](../reports/seo-metatags/2026-09-05-7407-renault-4-e-tech.md) |
| `[x]` | 8836 | Renault 4 Plein Sud – Хронология на отворения покрив | publications | ~~Should consider citing back to 6889~~ — **resolved transitively 2026-09-05**: 8836's own text never mentions Renault 5/AmpR Small, so a direct citation would be ungrounded; instead 8836 now links to 7407, which itself already links to 6889 — the citation chain exists in one extra click | [2026-09-05-8836-renault-4-plein-sud.md](../reports/seo-metatags/2026-09-05-8836-renault-4-plein-sud.md) |

---

## Done

Kept here as the historical record once a row moves to `[x]` — do not delete
completed rows, they're what makes this file useful as an audit trail.

| Status | Post ID | Title | Category | Backlink source(s) | GSC baseline at optimization | Report |
|---|---|---|---|---|---|---|
| `[x]` | 9099 | ZEEKR 7X – батерия, зареждане, шофиране и цени 2026 | ev-review | 8913, 8968, 9178 (outbound) | 4,446 / 70 / 1.57% / 6.4 (2026-05-15→08-13) | [2026-08-14-9099-zeekr-7x.md](../reports/seo-metatags/2026-08-14-9099-zeekr-7x.md) |
| `[x]` | 1227 | Как се намират авточасти за Tesla в България? Има ли „монопол“ и какви са алтернативите? | ev-masters | 5240 (outbound + inbound-edit) | 295 / 6 / 2.03% / 8.2 | [2026-09-04-1227-avtochasti-tesla-monopol.md](../reports/seo-metatags/2026-09-04-1227-avtochasti-tesla-monopol.md) |
| `[x]` | 4115 | Китайските електромобили са тук, за да останат | publications | 9383, 9348 (outbound) | 22 / 0 / 0% / 8.4 | [2026-09-04-4115-kitajskite-elektromobili.md](../reports/seo-metatags/2026-09-04-4115-kitajskite-elektromobili.md) |
| `[x]` | 8227 | BYD вече е в България: глобалният електрически гигант стъпва официално на нашия пазар | publications | 4115 (outbound, same-story-sequel citation, new→old) | 227 / 0 / 0% / 10.6 (2026-06-06→09-03) | [2026-09-04-8227-byd-vece-e-v-blgariya.md](../reports/seo-metatags/2026-09-04-8227-byd-vece-e-v-blgariya.md) |
| `[x]` | 5350 | #EVN71 – Tesla Cybertruck в България, Light Show във Велико Търново | ev-news | 7333 (outbound) | 28 / 0 / 0% / 8.6 (2026-06-06→09-03) | [2026-09-04-5350-cybertruck-veliko-tarnovo.md](../reports/seo-metatags/2026-09-04-5350-cybertruck-veliko-tarnovo.md) |
| `[x]` | 4129 | #EVN51 – Защо Xiaomi SU7 катастрофира толкова често? | ev-news | 9348 (outbound) | 14 / 0 / 0% / 7.1 (2026-06-06→09-03) | [2026-09-04-4129-xiaomi-su7-katastrofi.md](../reports/seo-metatags/2026-09-04-4129-xiaomi-su7-katastrofi.md) |
| `[x]` | 3957 | Как да зареждаме електромобил в гаража | publications | 9350 (inbound-edit) | 11,519 / 263 / 2.28% / 7.0 (2026-06-06→09-03) | [2026-09-04-3957-domashno-zarezhdane.md](../reports/seo-metatags/2026-09-04-3957-domashno-zarezhdane.md) |
| `[x]` | 6889 | Renault 5 E-Tech Electric – завръщането на иконата | publications | 9383 (outbound) | 7,188 / 49 / 0.68% / 7.3 (2026-06-06→09-03) | [2026-09-04-6889-renault-5-e-tech.md](../reports/seo-metatags/2026-09-04-6889-renault-5-e-tech.md) |
| `[x]` | 2398 | Автомобилно Изложение Женева 2024 – Renault 5, Lucid Sapphire, Lucid Gravity, MG, Renault Scenic | ev-news | 6889, 2455 (both outbound) | 9 / 0 / 0% / 12.3 (2026-06-06→09-03) | [2026-09-05-2398-geneva-2024-lucid-gravity.md](../reports/seo-metatags/2026-09-05-2398-geneva-2024-lucid-gravity.md) |
| `[x]` | 8836 | Renault 4 Plein Sud – Хронология на отворения покрив | publications | 8968 (outbound) | 149 / 6 / 4.03% / 5.1 | [2026-09-05-8836-renault-4-plein-sud.md](../reports/seo-metatags/2026-09-05-8836-renault-4-plein-sud.md) |
| `[x]` | 4902 | Hyundai IONIQ 6 – Всичко, което знаем за разхода и зареждането | publications | 3957 (outbound) | 142 / 5 / 3.52% / 10.0 (2026-06-06→09-03, fresh pull — see report for note on the earlier, larger 616/21/6.8 figure) | [2026-09-05-4902-hyundai-ioniq6-razhod.md](../reports/seo-metatags/2026-09-05-4902-hyundai-ioniq6-razhod.md) |
| `[x]` | 9248 | #EVN161 – Илон Мъск загатна за сливане между Tesla и SpaceX още тази година? | ev-news | 9462 (outbound) | 0 / 0 / 0% / — (no impressions, 2026-06-18→09-15) | [2026-09-16-9248-evn161-tesla-spacex-sliyane.md](../reports/seo-metatags/2026-09-16-9248-evn161-tesla-spacex-sliyane.md) |

---

## How to work this list

Same procedure as `SEO_EV_NEWS_TODO.md`: run
[`seo-article-optimize`](../.claude/skills/seo-article-optimize/SKILL.md) on
the post id or URL. Pick from the top of the Backlog table (highest
impressions first) unless something else is more urgent. When a row ships:

1. Move it from **Backlog** to **Done**, flip `[ ]` → `[x]`.
2. Fill in the **Report** column with the path Phase A created.
3. Leave the GSC baseline column as the pre-optimization number — that's
   what a later verification checkpoint compares against, same convention
   as `reports/seo-optimizations/ledger.csv`.
