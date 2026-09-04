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
| `[ ]` | 8075 | Eldrive откри втора сателитна зарядна станция в София | publications | 9350 (inbound-edit) | 314 / 6 / 1.91% / 8.2 | — |
| `[ ]` | 8836 | Renault 4 Plein Sud – Хронология на отворения покрив | publications | 8968 (outbound) | 149 / 6 / 4.03% / 5.1 | — |
| `[ ]` | 7631 | Hyundai Concept 3 – за първи път в България | publications | 9383 (inbound-edit) | 95 / 1 / 1.05% / 7.0 | — |
| `[ ]` | 3032 | MG 9 EV – Китайско чудо прикрито като Британска класика | publications | 4115 (outbound) | 91 / 1 / 1.10% / 5.1 | — |
| `[ ]` | 7351 | Tesla Lightshow – Ihtiman Airport LBHT | publications | 5350 (outbound) | 80 / 5 / 6.25% / 6.3 | — |
| `[ ]` | 1509 | Защо спряха автобусите в Осло? Какво ново около Tesla Model Y Juniper? | ev-news | 5240 (outbound) | 0 / 0 / — / — (no impressions in window) | — |
| `[ ]` | 4902 | Hyundai IONIQ 6 – Всичко, което знаем за разхода и зареждането | publications | 3957 (outbound) | 616 / 21 / 3.41% / 6.8 | — |
| `[ ]` | 5016 | Сравнение при зареждане на Hyundai IONIQ 5 N, Tesla S Plaid и Tesla Y Long Range | publications | 3957 (outbound) | 336 / 3 / 0.89% / 5.8 | — |

GSC window: 2026-06-06 → 2026-09-03, page-dimension pull filtered to each
URL (9099's baseline below uses its own original 2026-05-15→2026-08-13
window instead — see its row).

> **2398 was missed at write time** — both 6889's and 2455's own Phase C
> runs (2026-09-04) added outbound links to it but neither logged it here,
> despite the "Backlink-target tracking" trap existing specifically for
> this. Caught on a manual check the next day (2026-09-05), not by the
> automated step. Worth double-checking this doesn't recur on the next few
> runs rather than assuming the mechanism is now reliably firing.

### Also flagged — citation owed, not yet written

Not a backlink yet (nothing points at these), but a `seo-article-optimize`
report explicitly identified a same-story-sequel citation that should be
added to the post — different from the table above (there, the link already
exists; here, it's a known TODO for a future edit). Listed so it isn't lost.

| Status | Post ID | Title | Category | What's owed | Source report |
|---|---|---|---|---|---|
| `[ ]` | 7407 | Renault 4 E-Tech Electric – поглед в миналото, задвижван от бъдещето | publications | Should consider citing back to 6889 (new→old): 6889 (2025-06-05) mentions "Съвместимост с бъдещи модели → като Renault 4 и Alpine A290" on the shared AmpR Small/CMF-B platform; 7407 (2025-09-29, newer) is that model's own full review | [2026-09-04-6889-renault-5-e-tech.md](../reports/seo-metatags/2026-09-04-6889-renault-5-e-tech.md) |
| `[ ]` | 8836 | Renault 4 Plein Sud – Хронология на отворения покрив | publications | Same platform-compatibility mention in 6889 as above; 8836 (2026-06-20) is an even later Renault 4 trim piece — lower priority than 7407 for this citation but flagged for the same reason | [2026-09-04-6889-renault-5-e-tech.md](../reports/seo-metatags/2026-09-04-6889-renault-5-e-tech.md) |

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
