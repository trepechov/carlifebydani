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
| `[ ]` | **3957** | Как да зареждаме електромобил в гаража | publications | 9350 (inbound-edit) | **11,519 / 263 / 2.28% / 7.0** | — |
| `[ ]` | **6889** | Renault 5 E-Tech Electric – завръщането на иконата | publications | 9383 (outbound) | **7,188 / 49 / 0.68% / 7.3** | — |
| `[ ]` | 8075 | Eldrive откри втора сателитна зарядна станция в София | publications | 9350 (inbound-edit) | 314 / 6 / 1.91% / 8.2 | — |
| `[ ]` | 8836 | Renault 4 Plein Sud – Хронология на отворения покрив | publications | 8968 (outbound) | 149 / 6 / 4.03% / 5.1 | — |
| `[ ]` | 7631 | Hyundai Concept 3 – за първи път в България | publications | 9383 (inbound-edit) | 95 / 1 / 1.05% / 7.0 | — |
| `[ ]` | 3032 | MG 9 EV – Китайско чудо прикрито като Британска класика | publications | 4115 (outbound) | 91 / 1 / 1.10% / 5.1 | — |
| `[ ]` | 5350 | #EVN71 – Tesla Cybertruck в България, Light Show във Велико Търново | ev-news | 7333 (outbound) | 28 / 0 / 0% / 8.6 | — |
| `[ ]` | 4129 | #EVN51 – Защо Xiaomi SU7 катастрофира толкова често? | ev-news | 9348 (outbound) | 14 / 0 / 0% / 7.1 | — |
| `[ ]` | 1509 | Защо спряха автобусите в Осло? Какво ново около Tesla Model Y Juniper? | ev-news | 5240 (outbound) | 0 / 0 / — / — (no impressions in window) | — |

GSC window: 2026-06-06 → 2026-09-03, page-dimension pull filtered to each
URL (9099's baseline below uses its own original 2026-05-15→2026-08-13
window instead — see its row).

### Also flagged — citation owed, not yet written

Not a backlink yet (nothing points at these), but a `seo-article-optimize`
report explicitly identified a same-story-sequel citation that should be
added to the post — different from the table above (there, the link already
exists; here, it's a known TODO for a future edit). Listed so it isn't lost.

| Status | Post ID | Title | Category | What's owed | Source report |
|---|---|---|---|---|---|
| `[ ]` | 8227 | BYD вече е в България: глобалният електрически гигант стъпва официално на нашия пазар | publications | Should link back to 4115 (same-story sequel: 4115 anticipated BYD's EU ambitions in 2024, 8227 is the 2026-03-26 confirmed-entry follow-up) — new→old direction, so the edit belongs on 8227, not 4115 | [2026-09-04-4115-kitajskite-elektromobili.md](../reports/seo-metatags/2026-09-04-4115-kitajskite-elektromobili.md) |

---

## Done

Kept here as the historical record once a row moves to `[x]` — do not delete
completed rows, they're what makes this file useful as an audit trail.

| Status | Post ID | Title | Category | Backlink source(s) | GSC baseline at optimization | Report |
|---|---|---|---|---|---|---|
| `[x]` | 9099 | ZEEKR 7X – батерия, зареждане, шофиране и цени 2026 | ev-review | 8913, 8968, 9178 (outbound) | 4,446 / 70 / 1.57% / 6.4 (2026-05-15→08-13) | [2026-08-14-9099-zeekr-7x.md](../reports/seo-metatags/2026-08-14-9099-zeekr-7x.md) |
| `[x]` | 1227 | Как се намират авточасти за Tesla в България? Има ли „монопол“ и какви са алтернативите? | ev-masters | 5240 (outbound + inbound-edit) | 295 / 6 / 2.03% / 8.2 | [2026-09-04-1227-avtochasti-tesla-monopol.md](../reports/seo-metatags/2026-09-04-1227-avtochasti-tesla-monopol.md) |
| `[x]` | 4115 | Китайските електромобили са тук, за да останат | publications | 9383, 9348 (outbound) | 22 / 0 / 0% / 8.4 | [2026-09-04-4115-kitajskite-elektromobili.md](../reports/seo-metatags/2026-09-04-4115-kitajskite-elektromobili.md) |

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
