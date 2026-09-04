# BYD / BYD Bulgaria — optimization plan

**Trigger:** DataForSEO competitor-gap refresh (2026-09-04) —
[`reports/competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md`](../competitor-gap/2026-09-04-dataforseo-competitor-gap-refresh.md).
`byd bulgaria` (4,400/mo, BG) is carlifebydani.com's **highest-volume
ranking keyword of any kind**, sitting at position 45 despite a dedicated,
exact-match article already published. Not a head-to-head loss to
evpoint.bg like the other three plans in this batch — a different
diagnosis: content and intent match exist, execution is what's failing.
Same pattern the August 2026 Semrush competitor-gap report flagged for this
same term, independently confirmed by this refresh.

**Status:** planned, none applied yet.
**Created:** 2026-09-04.

---

## Why this is the highest-confidence fix in the competitor-gap batch

Unlike BMW i3, Tesla Model S, and VW ID.4, this isn't a cluster that needs
building out — it's one article that already says exactly what the query
wants, just not optimized. Lowest effort, highest confidence of the four
competitor-gap plans.

---

## The target (1 primary + 2 supporting, oldest first)

| Order | ID | Date | Title | Category | Status |
|---|---|---|---|---|---|
| — | **4115** | 2024-04-14 | Китайските електромобили са тук, за да останат | publications | **already optimized** (2026-09-04, keyphrase "китайски електромобили" — ledger row `4115-2026-09-04-C`). Broader Chinese-EV context piece; BYD is one of several brands covered. Do not re-run — candidate to link *from*, into 8227 |
| 1 | **8227** | 2026-03-26 | BYD вече е в България: глобалният електрически гигант стъпва официално на нашия пазар | publications | **available — primary target.** Exact-match content for "byd bulgaria"/"byd българия" |
| 2 | **9220** | 2026-07-21 | #EV160 – BYD Denza Z – 1582 коня чиста електрическа мощ! | ev-news | available, optional. Denza is BYD's premium sub-brand — real but narrower topic |

URLs:
`/publications/kitajskite-elektromobili-sa-tuk-za-da-ostanat/` (4115,
already optimized) ·
`/publications/byd-veche-e-v-blgariya-globalniyat-elektricheski-gigant-stpva-oficzialno-na-nashiya-pazar/`
(8227) ·
`/ev-news/ev160-byd-denza-z-1582-konya-chista-elektricheska-mosht/` (9220)

### Per-post notes

**8227 — the fix.** This is the article the August Semrush report and this
refresh both point at: on-topic, published, ranking, just under-optimized
(pos 45 for a 4,400/mo term with exact-match content). One clean Phase A +
Phase C run is the whole plan — Phase A + Phase C only, no Phase B (Phase B
is gated to `ev-news`; this post is `publications`).

**9220 — optional, not required.** Denza Z is BYD's premium sub-brand, a
real and newer story, `ev-news` category (gets the full A→B→C pipeline if
run). Worth linking to/from 8227 as "BYD's broader lineup" context, but
closing the "byd bulgaria" gap doesn't depend on it.

**4115 — already done, link-source only.** Optimized today
(2026-09-04) under a different, broader keyphrase ("китайски електромобили")
covering Chinese EVs generally, of which BYD is one example. Don't re-run
Phase A/C on it; if a link from it into 8227 makes sense once both are
settled, that's a light-touch content edit, not a fresh optimize pass.

### Excluded from this pass

- **4941** (`#EVN61 – Повече информация за Citroën C3 Aircross`) — carries
  the `BYD` tag but is unrelated; tag noise.
- **2398** (Geneva Motor Show 2024 roundup) — the same recurring multi-brand
  bridge post already excluded from both the MG and Renault cluster plans
  (also tagged BYD here) — **third** cluster this exact post has shown up in
  and been excluded from as a primary target. If it's ever worth its own
  pass, that's a one-off decision independent of any single brand cluster,
  not folded into this one.

---

## Execution

```
optimize post 8227   (BYD вече е в България — the fix, run this)
optimize post 9220   (BYD Denza Z — optional, run if/when convenient)
```

8227's run gets its own dated report and ledger row with its own
`verify_due` — same cadence as every other cluster. Given the exact-match
content and clear diagnosis, this is the fastest-to-verify win in the whole
competitor-gap batch.
