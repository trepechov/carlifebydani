# Semrush Position Tracking vs GSC — Sep 2–16, 2026

**Trigger:** three Semrush Position Tracking emails (2 rank-change alerts + 1 weekly
update) reporting a sharp decline, landing days after the Sep 4–5 on-site SEO batch.

**Question asked:** did the Sep 4–5 changes cause the drop?

**Answer: no.** The drop was a one-week SERP/tracker artifact that fully reversed by
Sep 16, and GSC shows no corresponding movement on any day in the window. Separately,
the tracker roster itself is mis-specified — see §4.

---

## 1. Data extracted from the emails

Project `carlifebydani.com` · campaign `15935049_1716107` · Bulgaria (Google) ·
Bulgarian · **desktop** · alert rule "changes by more than 5 positions".

| # | Sent (UTC) | Type | Window |
|---|---|---|---|
| A | 2026-09-09 07:43 | Rank-change alert, 4 keywords | Sep 2 → Sep 9 |
| B | 2026-09-10 00:25 | Weekly update, "rankings declined" | Sep 2–9 |
| C | 2026-09-16 08:17 | Rank-change alert, 2 keywords | Sep 9 → Sep 16 |

### Keyword positions

Sep 2 is derived (Sep 9 position minus the reported diff); Sep 16 is reported directly.

| Keyword | Volume | Sep 2 | Sep 9 | Sep 16 | Net Sep 2→16 |
|---|---:|---:|---:|---:|---:|
| renault 5 | 1 300 | 9 | **63** (−54) | **8** (+55) | **+1** |
| volvo ex90 | 260 | 1 | **14** (−13) | **3** (+11) | **−2** |
| hyundai ioniq 5 мнения | 90 | 1 | **12** (−11) | not alerted | — |
| електромобил | 720 | 11 | **16** (−5) | not alerted | — |

### Visibility & estimated traffic, Sep 2–9 (email B)

| Domain | Visibility | Δ | Traffic | Δ |
|---|---:|---:|---:|---:|
| investor.bg | 10.79% | **+9.35** | 2.57 | +1.93 |
| **carlifebydani.com** | **6.73%** | **−20.17** | **1.31** | **−5.42** |
| carmarket.bg | 2.63% | +0.50 | 1.10 | −0.37 |
| auto-press.net | 0.53% | +0.02 | 0.19 | +0.01 |
| dizzyriders.bg | 0.34% | −0.32 | 0.26 | −0.26 |
| eurica.me | 0.13% | −0.01 | 0.01 | −0.00 |
| skorost.info / gracepacerace.com / automedia.bg | 0.00% | 0.00 | 0.00 | 0.00 |

### Top landing pages, Sep 2–9 (email B, Semrush estimated traffic)

| Page | Traffic | Δ |
|---|---:|---:|
| /ev-review/zeekr-7x/ | 0.65 | 0.00 |
| /publications/kak-da-smenim-dvgto-s-elektromobil/ | 0.29 | −0.19 |
| /publications/volvo-ex90-luks-inovacziya-i-bezopasnost/ | 0.11 | **−3.04** |
| /clbd-parts/ | 0.05 | 0.00 |
| /ev-review/hyundai-ioniq-5-n-…/ | 0.05 | −1.05 |
| /publications/volvo-ex90-bdeshheto-…/ | 0.03 | +0.01 |
| /ev-masters/kak-se-namirat-avtochasti-za-tesla-…/ | 0.01 | 0.00 |
| /publications/hyundai-ioniq-5-test-na-rekuperacziyata/ | 0.01 | −0.00 |
| /publications/domashno-zarezhdane-na-elektromobili/ | 0.00 | 0.00 |
| /publications/renault-5/ | 0.00 | **−1.30** |

---

## 2. Why this isn't our changes

### 2a. The untouched control moved identically

`volvo ex90` is **not in `reports/seo-optimizations/ledger.csv`** — neither Volvo EX90
page has ever been touched by the optimize pipeline. It dropped 13 places and recovered
11 on exactly the same schedule as `renault 5`, which we *did* rewrite on Sep 4–5.
Same movement, same week, one page changed and one not. That isolates the cause to
something outside the site.

`hyundai ioniq 5 мнения` (−11) is likewise untouched — post 4902 is IONIQ **6**, not 5.

### 2b. Everything recovered, to at or near pre-change levels

Over the full two weeks `renault 5` is **+1** and `volvo ex90` is **−2**. The "−54
positions" headline describes a single Tuesday snapshot, not a trend.

### 2c. The competitor table shows displacement, not decay

Our −20.17 visibility is almost exactly mirrored by investor.bg's +9.35 in the same
week, on a shared keyword set. A general news outlet running EV coverage for one week
pushes us down the SERP without anything changing on our end — then it reverses.

### 2d. GSC shows no such week

Site-wide daily average position, Sep 2–13 — the alert fired on Sep 9:

```
Sep 02  pos 6.4   impr 1982   clicks 61
Sep 03  pos 7.9   impr 1963   clicks 48
Sep 04  pos 6.7   impr 1809   clicks 43   ← optimize batch applied
Sep 05  pos 6.5   impr 1798   clicks 37   ← optimize batch applied
Sep 06  pos 6.7   impr 1654   clicks 30
Sep 07  pos 6.7   impr 1776   clicks 52
Sep 08  pos 6.4   impr 1929   clicks 50
Sep 09  pos 6.6   impr 1997   clicks 39   ← Semrush "−20.17% visibility"
Sep 10  pos 6.4   impr 1945   clicks 55
Sep 11  pos 6.7   impr 1937   clicks 54
Sep 12  pos 6.5   impr 1946   clicks 41
Sep 13  pos 6.4   impr 1983   clicks 41
```

Flat 6.4–6.7 with one 7.9 outlier on Sep 3 (*before* the changes). Impressions never
break their band. Semrush's "−5.42 estimated traffic" has no counterpart in real
Search Console data. This is the same artifact already recorded in the
`seo-rank-tracker-volatility` note.

---

## 3. Did the Sep 4–5 batch actually work?

24 pages changed on Sep 4–5. GSC impressions and average position, equal 9-day windows
(pre Aug 26–Sep 3, post Sep 5–13):

| Post | Page | Impr pre → post | Pos pre → post |
|---:|---|---:|---:|
| 5240 | evn67-novi-spekulaczii-…-predstavitelstvo | 4 → **68** (+1600%) | 9.0 → 8.9 |
| 130 | vw-id4-gtx-vpechatlyavashto… | 139 → **316** (+127%) | 7.9 → 7.8 |
| 1227 | kak-se-namirat-avtochasti-za-tesla-… | 37 → **78** (+111%) | 7.2 → 8.6 |
| 1027 | renault-megane-e-tech-… | 4 → 11 (+175%) | 4.0 → 6.6 |
| 2622 | renault-scenic-e-tech-… | 13 → 21 (+62%) | 3.5 → 4.6 |
| 9350 | kak-ebag-zarezhda-badestheto-… | 11 → 16 (+45%) | 6.6 → **3.9** |
| 8836 | renault-4-plein-sud | 13 → 17 (+31%) | 2.7 → 5.5 |
| 7407 | renault-4-e-tech-electric-… | 167 → 190 (+14%) | 6.9 → 6.7 |
| 8227 | byd-veche-e-v-blgariya-… | 19 → 21 (+11%) | 10.3 → **6.4** |
| 4902 | hyundai-ioniq-6-…-razhoda | 77 → 79 (+3%) | 6.4 → 6.4 |
| 3957 | domashno-zarezhdane-na-elektromobili | 1182 → 1174 (−1%) | 6.7 → 6.7 |
| 6889 | **renault-5** | 902 → 532 (−41%) | 7.4 → **6.9** |
| 2455 | renault-5-e-tech-istinski-malak-… | 27 → 18 (−33%) | 6.9 → 17.1 |
| 9333 | evn162-tesla-model-y-s-ofitsialen-v2l | 5 → 5 (0%) | 9.0 → **3.6** |
| 4129 | evn51-zashto-xiaomi-su7-katastrofira | 0 → 13 (new) | — → 8.0 |
| 1039 | porsche-taycan-turbo-s-… | 0 → 4 (new) | — → 6.8 |
| others | ≤ 20 impressions, no signal | | |

**Total across the set: 2 618 → 2 571 impressions (−1.8%).** Nine days is far too early
to read this as an outcome — the ledger's own `verify_due` dates are 2026-10-02/03 for
Phase C and 2026-10-30 for Phase B, which is the right time to judge. Nothing here
looks like damage.

### The one page that looks bad isn't

Post 6889 `/publications/renault-5/` shows −41% impressions — the page Semrush said
went to zero traffic. Daily shape:

```
Aug 19  100 | Aug 26  282 ←one-day news spike | Sep 04   61 ←changed
Aug 20   78 | Aug 27  130                     | Sep 05   50 ←changed
Aug 21   84 | Aug 28   73                     | Sep 06   42
Aug 22   65 | Aug 29   63                     | Sep 07   41
Aug 23   49 | Aug 30   58                     | Sep 08   48
Aug 24   69 | Aug 31   81                     | Sep 09   46 ←alert fired
Aug 25   69 | Sep 01   75                     | Sep 10   68
            | Sep 02   67                     | Sep 11   84
            | Sep 03   73                     | Sep 12   81
                                              | Sep 13   72
```

There is no break at Sep 4–5 and no collapse at Sep 9. Position holds 6.3–7.6
throughout and actually *improves* on average (7.4 → 6.9). The entire −41% is the
Aug 26 spike of 282 impressions sitting inside the pre-window. Sep 10–13 are running
above Sep 5–9.

**And the rewrite is doing what it was aimed at.** Query mix on that page:

| | pre (Aug 26–Sep 3) | post (Sep 5–13) |
|---|---|---|
| рено 5 | 106 impr, pos 5.1 | 105 impr, pos 5.8 |
| рено 5 електрическо | 69 impr, pos 6.5 | — |
| renault 5 | 4 impr | **23 impr** |
| renault 5 electric цена | — | **11 impr** |
| renault 5 e-tech | 1 impr | **5 impr** |
| renault 5 e-tech цена | — | 2 impr |
| renault 5 electric | — | 2 impr |

The Sep 4 keyphrase was **"Renault 5 E-Tech"** (Latin). The Latin-script long-tail
footprint went from ~6 impressions to ~45 in nine days while the Cyrillic head term
held flat. That's the change landing, early but visible.

---

## 4. The real finding: the tracker roster is measuring the wrong strings

Semrush position vs GSC position, same 9-day windows:

| Keyword | Semrush Sep 2 → 9 → 16 | GSC pos pre → post | GSC impr pre → post |
|---|---|---:|---:|
| renault 5 | 9 → 63 → 8 | 9.8 → 13.0 | 4 → 24 |
| volvo ex90 | 1 → 14 → 3 | 12.5 → 34.3 | 15 → 3 |
| hyundai ioniq 5 мнения | 1 → 12 → — | 18.0 → 27.3 | 12 → 3 |
| електромобил | 11 → 16 → — | 17.4 → 17.9 | 10 → 21 |
| **рено 5** *(not tracked)* | — | **5.1 → 5.8** | **106 → 105** |

Two things fall out:

1. **Semrush's absolute positions are unreachable in GSC.** It claims position 1 for
   `volvo ex90` and `hyundai ioniq 5 мнения`; GSC's blended average for those is 12.5
   and 18.0. Some gap is expected (Semrush = desktop, one location, best URL; GSC =
   blended across devices, locations and all URLs) but not 1 vs 12. Treat Semrush
   absolutes here as directional at best.

2. **`електромобил`, the one tracked keyword with a steady real footprint, is the one
   GSC calls dead flat** (17.4 → 17.9). Semrush reported it −5. Meanwhile `рено 5`,
   which carries ~25× the impressions of the tracked Latin `renault 5` and sits at
   position 5, **isn't tracked at all**.

The roster is spending its 10 free slots on Latin-script spellings while the site's
actual Bulgarian-language visibility sits on Cyrillic ones.

### Recommended swaps (manual, in Semrush's web UI)

| Retire | Add | Why |
|---|---|---|
| `renault 5` | `рено 5` | 4 vs 106 impressions in 9 days; pos 5.1 vs 9.8 |
| `hyundai ioniq 5 мнения` | `hyundai ioniq 6 мнения` | 11 impr on the IONIQ 6 variant vs 2 on the 5; post 4902 is the optimized page |
| `volvo ex90` | `zeekr 7x цена` | EX90 has no owned page in the ledger; Zeekr 7X is the site's strongest cluster (175 impr, pos 5.9) |

Keep `електромобил` — it's the only broad head term with a stable reading, which makes
it a usable baseline.

**Not applied.** `reports/seo-performance/tracked-keywords.csv` is still header-only, and
the emails only reveal 4 of the 10 tracked keywords — seeding the roster from an
incomplete list would bake in a wrong baseline. Seed it once the full Semrush roster
is known, via `python3 tools/keyword_tracking.py bootstrap`.

---

## 5. Actions

- [ ] **Nothing to roll back.** The Sep 4–5 batch is clean; leave it to its scheduled
      `verify_due` checks (2026-10-02/03 Phase C, 2026-10-30 Phase B).
- [ ] **Raise the Semrush alert threshold** from ">5 positions" or mute it. At this
      site's ranking volatility it fires on noise every week and its estimated-traffic
      numbers don't track GSC.
- [ ] **Swap the keyword roster** per §4, in the Semrush UI.
- [ ] **Seed `tracked-keywords.csv`** once the full 10-keyword roster is read off the
      Semrush UI.
- [ ] `reports/seo-optimizations/checks.csv` is still header-only — no `verify_due`
      checkpoint has been recorded yet. First ones come due 2026-09-11 (already passed,
      for the Aug 14 batch) and 2026-10-02.

## Sources

- Semrush Position Tracking emails, 2026-09-09 / 09-10 / 09-16 (raw EML, provided by user)
- Google Search Console API, property `https://www.carlifebydani.com/`, pulled 2026-09-16
- `reports/seo-optimizations/ledger.csv` @ 66 entries

---

# Revision — 2026-09-18

Semrush UI screenshots supplied for every weekly view Aug 5 → Sep 16 (the emails only
covered Sep 2–16, and only the 4 keywords that tripped the alert). **The core
conclusion is unchanged and now better supported — but §4 was wrong about one thing
and §2 overstated another.**

## Full roster and weekly position history

⭐ = Semrush SERP-feature (featured snippet) icon.

| Keyword | Vol | Aug 5 | Aug 12 | Aug 19 | Aug 26 | Sep 2 | Sep 9 | Sep 16 |
|---|---:|---|---|---|---|---|---|---|
| renault 5 | 1 300 | 8 | 4 | 4 | 6 | 9 | **63** | 8 |
| zeekr 7x цена | 320 | — | 4 | 5 | 5 | 5 | 5 | 5 |
| части за тесла | 20 | — | 5 | 4 | 4 | 4 | 4 | 6 |
| volvo ex90 | 260 | — | 48 | 49 | 3 | 1⭐ | 14 | 3⭐ |
| hyundai ioniq 5 мнения | 90 | — | 8 | 3⭐ | 1⭐ | 1⭐ | 12 | 13 |
| домашно зареждане на електромобил | **0** | — | — | — | 5 | 5 | 5 | 5 |
| електромобил | 720 | — | 20 | lost | — | 11 | 16 | 13 |
| зарядна станция | **1 900** | — | — | — | — | — | — | — |
| китайски електромобили | 170 | — | — | — | — | — | — | — |
| нощна тарифа | 260 | — | — | — | — | — | — | — |

*(The Aug-12 view renders `i —` for five keywords that the Aug-19 view shows numbers
for on the same date — most likely Semrush backfilling history after those keywords
were added. Values above use the later, populated view.)*

## Correction 1 — §4 was wrong: the Semrush/GSC gap is explained, not error

§4 said Semrush's position-1 readings were "unreachable in GSC… directional at best."
The screenshots show **every implausible position-1 reading carries Semrush's
featured-snippet icon**. `hyundai ioniq 5 мнения` held ⭐ at pos 3/1/1 across Aug 19 –
Sep 2 while GSC's blend for the same query sat at 18.0. Semrush is reporting the
snippet slot it actually held; GSC is blending in every device and location where it
didn't. **Both numbers are correct** — they measure different things. Drop the
"treat as directional" framing.

## Correction 2 — §2 overstated "everything recovered"

Two of four recovered; one did not, and a fifth keyword started sliding:

| Keyword | Sep 9 → Sep 16 | Verdict |
|---|---|---|
| renault 5 | 63 → 8 | recovered ✅ |
| volvo ex90 | 14 → 3⭐ | recovered ✅ |
| електромобил | 16 → 13 | partial ✅ |
| hyundai ioniq 5 мнения | 12 → **13** | **real loss — lost its featured snippet** ❌ |
| части за тесла | 4 → **6** | new small decline ⚠️ |

GSC corroborates the ioniq-5 loss independently: impressions 12 → 3, position 18.0 →
27.3. That one is genuine. **It is still not ours** —
`/ev-review/hyundai-ioniq-5-n-ryadko-…/` has zero entries in `ledger.csv`; it has
never been touched by the optimize pipeline. Losing a featured snippet to a competitor
or a SERP layout change is the likely cause.

## Correction 3 — `volvo ex90` isn't a clean control, but §2a still holds

Its Aug 19 → Aug 26 "+46 places" (49 → 3) came with a **changed URL**: it moved from
`/publications/volvo-ex90-bdeshheto-na-luksoznite-semejni-elektromobili-e-tuk/` to
`/publications/volvo-ex90-luks-inovacziya-i-bezopasnost/`. That's Google switching
between two of our own EX90 posts — **cannibalization**, not ranking movement. So the
keyword has its own instability independent of anything site-wide.

It remains a valid control for the Sep 9 event specifically: both EX90 pages are
absent from `ledger.csv`, so neither was touched on Sep 4–5, and it moved anyway.

**New action:** consolidate the two EX90 posts. Neither has ever been optimized.

## Strengthened — `renault 5` was already sliding before we touched it

The three weekly readings *before* the Sep 4 batch: **4 (Aug 19) → 6 (Aug 26) → 9
(Sep 2)**. The decline began around Aug 19, roughly two weeks before the optimization.
By Sep 16 it is back to **8** — its Aug 5 level. The Sep 4–5 changes interrupted a
pre-existing slide and it has since returned to baseline. This closes the original
question more firmly than the email data alone could.

## New finding — four of ten tracker slots produce no signal

`зарядна станция` (vol **1 900**, the highest-volume term tracked),
`китайски електромобили` (170) and `нощна тарифа` (260) have never registered a
position in six weeks. `домашно зареждане на електромобил` holds a steady 5 but has
**volume 0**.

GSC confirms this is not a tracker failure — all three unranked terms have **zero
impressions** in Sep 5–13. `зарядна станция` is a genuine **content gap**: the biggest
BG demand term in the set, and the site has no page that ranks for it at all.

## New finding — `zeekr 7x цена` is the best post-optimization evidence available

`/ev-review/zeekr-7x/` (post 9099) was optimized **2026-08-14**, `verify_due`
2026-09-11 — the one checkpoint already due and never recorded. Five weekly readings
since: **5, 5, 5, 5, 5**. Zero movement, and GSC gives it **244 impressions at pos
6.1** — by far the strongest footprint in the roster.

Two things follow. The tracker is *not* globally noisy — a stable keyword reads stable
every week, which isolates the volatility to SERP-feature-heavy terms. And the Aug 14
optimization held position without improving it; that's the honest read at five weeks.

## Roster now recorded

`reports/seo-performance/tracked-keywords.csv` is seeded with all 10 keywords
(`bootstrap --date 2026-08-05`, the earliest week visible in the UI) plus one real
reading each (`append --date 2026-09-18`, source `gsc`, window Sep 5–13):

| Keyword | GSC pos | GSC impr |
|---|---:|---:|
| zeekr 7x цена | 6.1 | 244 |
| renault 5 | 13.0 | 24 |
| електромобил | 17.9 | 21 |
| части за тесла | 6.5 | 11 |
| volvo ex90 | 34.3 | 3 |
| hyundai ioniq 5 мнения | 27.3 | 3 |
| домашно зареждане на електромобил | 8.5 | 2 |
| зарядна станция · китайски електромобили · нощна тарифа | — | 0 |

Readings are recorded as `gsc`, not `semrush_manual`, so the series stays numerically
comparable month to month — Semrush reports no impressions, and its position means
something different on SERP-feature terms (Correction 1). The Semrush weekly positions
live in this report and in the `seo-semrush-tracker-roster` memory.

## Revised actions

- [ ] **Still nothing to roll back.** Now supported by `renault 5`'s pre-change slide.
- [ ] **Swap 4 dead slots** — retire `зарядна станция`, `китайски електромобили`,
      `нощна тарифа`, `домашно зареждане на електромобил`. Candidates: `рено 5`
      (106 impr @ 5.1), `zeekr 7x` bare, `toyota bz4x мнения` (43 impr @ 4.6),
      `киа ниро електрик мнения` (52 impr @ 3.5). Run `keyword_tracking.py retire`
      after each swap actually ships in the Semrush UI.
- [ ] **Consolidate the two Volvo EX90 posts** — active cannibalization, neither optimized.
- [ ] **Build a `зарядна станция` page** — vol 1 900, zero impressions today.
- [ ] **Record the overdue 2026-09-11 checkpoints** in `checks.csv` (Aug 14 batch:
      7333, 7533, 6165, 9099). `zeekr 7x цена` data is in hand above.
- [ ] **Raise or mute the ">5 positions" Semrush alert** — unchanged recommendation.
