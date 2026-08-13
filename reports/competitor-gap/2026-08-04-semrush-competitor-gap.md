# Organic Competitor & Keyword Gap Analysis — carlifebydani.com

**Data source:** Semrush (BG database, desktop) via the Semrush MCP server.
**Data pulled:** 2026-08-04 — the date this report was requested. All positions,
search volumes, and keyword counts below are the values Semrush returned on that
date and are **not** refreshed automatically. Semrush's BG database updates on its
own cadence, so treat every figure here as a 2026-08-04 snapshot.

**Market:** Bulgaria (`database=bg`).
**Reports used:** `domain_organic_organic` (competitor discovery),
`domain_organic` (keyword footprints).

> ⚠️ **Coverage is partial.** The native keyword-gap report (`domain_domains`) is
> not included in the current Semrush plan and returned a plan-access message on
> every attempt. The gap below was rebuilt manually by subtracting
> carlifebydani.com's keyword set from competitors' organic keyword pulls. Larger
> keyword pulls also began failing mid-session. See
> [Data coverage & limitations](#data-coverage--limitations) for exactly what was
> and was not retrieved.

---

## Baseline: carlifebydani.com organic footprint

As of 2026-08-04, Semrush shows carlifebydani.com ranking for **26 organic
keywords** in the BG database — the complete set was retrieved.

Composition of those 26:

- **Single-model news posts** — `renault 5` (1,300 / pos 6), `mg zs` (880 / pos 9),
  `bmw i3` (1,900 / pos 13), `citroen c5 aircross` (720 / pos 39),
  `lucid air` (320 / pos 21), `renault 4` (390 / pos 12),
  `ford capri` (480 / pos 33), `skoda citigo` (320 / pos 42),
  `microlino` (320 / pos 8), `ford tourneo` (390 / pos 10)
- **Brand / navigational** — `davids car` (480 / pos 8), `dani` (390 / pos 4),
  `данкар` (480 / pos 33)
- **Podcast-guest names** — `станислав михов сенто` (1,000 / pos 4),
  `станислав михов sento` (720 / pos 5)
- **Tag pages** — `зареждане` (1,000 / pos 9), `microlino` (320 / pos 8)

Top traffic driver: `stark varg` (1,600 / pos 7) at 19.8% of organic traffic.

**Two structural findings:**

1. **Zero Bulgarian-language informational phrases.** Every keyword held is a brand
   name, model name, or person's name. Nothing is a question, a how-to, or a
   comparison. This is consistent with the P0 site-language misconfiguration
   recorded in [`docs/SEO_EV_NEWS_PROPOSALS.md`](../../docs/SEO_EV_NEWS_PROPOSALS.md).
2. **Two `/tag/` pages rank instead of articles** (`зареждане`, `microlino`),
   meaning thin taxonomy pages are outranking the site's own editorial content.

---

## Top organic competitors (Bulgaria)

Semrush's auto-detected competitor list for carlifebydani.com is **not usable on
its own**. With only 26 ranking keywords, competitor relevance scores come back at
0.00–0.22 and the list is dominated by generic giants (youtube.com, wikipedia.org,
facebook.com at relevance 0.00).

The real competitive neighbourhood was re-derived by running competitor discovery
on **autobild.bg**, the closest large BG auto-editorial domain. Results split into
three tiers:

| Tier | Domains | Relevance to autobild.bg | Note |
|---|---|---|---|
| **Editorial competitors** | autobild.bg, dizzyriders.bg, testdrive.bg, drive.place, novakola.bg, vsi4kibri4ki.com | 0.14–0.16 | Direct content competitors — same format, same audience |
| **EV specialists** | evpoint.bg, gigacharger.net, carexpertbg.eu | evpoint.bg = 0.06 rel. to carlifebydani | evpoint.bg is the **only** domain with meaningful keyword overlap with carlifebydani.com |
| **Marketplaces & spec DBs** | mobile.bg, auto.bg, cars.bg, autoscout24.bg, bazar.bg, olx.bg, car24.bg, auto-data.net, automoli.com, autodata24.com | 0.16–0.38 | Occupy the same SERPs but do not compete editorially |

**Primary competitor for carlifebydani.com is evpoint.bg** — it targets the same
EV subject matter and currently beats carlifebydani head-to-head on the exact
models carlifebydani publishes about (see cluster 4 below).

---

## Keyword gap — top 10 topic clusters with low or no visibility

Every keyword listed is one a competitor ranks for and carlifebydani.com does not.
Format: `keyword` volume / competitor position.

| # | Topic cluster | Who owns it | Gap keywords (vol / their pos) | carlifebydani status |
|---|---|---|---|---|
| 1 | **Used-car buyer guides** — "X на старо" / "X мнения" / "употребяван X" | autobild.bg (dominant, ~40+ pages) | `toyota yaris` 5,400/11 · `audi a3` 5,400/10 · `тойота ярис` 4,400/11 · `kia ceed` 2,900/12 · `nissan juke` 2,900/12 · `audi q7` 2,900/12 · `porsche macan` 1,900/9 · `peugeot 208` 1,900/9 · `jeep cherokee` 1,900/10 · `alfa romeo giulia` 1,900/10 · `seat ibiza` 1,600/9 · `volvo xc40` 1,300/5 · `форд куга мнения` 480/3 · `нисан кашкай мнения` 590/7 · `опел мока мнения` 480/10 | **None.** Largest single cluster in BG auto search; zero pages |
| 2 | **EV charging infrastructure** | evpoint.bg (pos 1–2 across the head) | `зарядна станция за електрически автомобили` 1,300/1 · `зарядна станція` 1,600/1 · `зарядна станция` 1,600/2 · `зарядни станции` 1,000/2 | **Near-zero.** Only `зареждане` (1,000) at pos 9 — a bare `/tag/` page, not an article |
| 3 | **Generic EV head terms + Tesla brand** | evpoint.bg | `електрически автомобил` 12,100/8 · `тесла` 8,100/4 · `tesla` 6,600/4 · `електромобили` 720/1 · `електромобил` 720/1 · `тесла цена` 720/2 | **None.** No category or pillar page for the site's own core topic |
| 4 | **EV model spec / range / price pages** | evpoint.bg (structured spec page per variant) | `hyundai kona` 3,600/6 · `хюндай кона` 2,400/6 · `tesla model y` 1,600/2 · `cybertruck` 1,600/4 · `tesla model 3` 1,300/4 · `renault zoe` 1,000/3 · `audi e tron` 1,000/4 · `vw id 4` 880/4 · `hyundai ioniq 5` 720/3 | **Losing head-to-head.** `bmw i3` — evpoint pos 3 vs carlifebydani pos 13; `tesla model s` — evpoint pos 3 vs carlifebydani pos 12 |
| 5 | **Maintenance, parts & dashboard warnings** | autobild.bg | `двг` 1,600/3 · `лампи на таблото` 1,600/9 · `ламбда сонда` 1,600/3 · `алтернатор` 1,300/4 · `ангренажен ремък` 1,300/7 · `колянов вал` 1,000/8 · `символи жълта лампа на таблото` 880/8 · `прахосмукачка за кола` 2,400/2 · `гуми kleber` 1,000/10 · `как се кара автоматик` 320/2 | **None.** High-intent evergreen; EV-maintenance angle entirely unclaimed |
| 6 | **Socialist-era & classic cars** | autobild.bg (~30 pages) | `bmw e92` 1,900/9 · `волга` 1,600/11 · `mercedes w124` 720/8 · `mercedes w203` 720/9 · `москвич 408` 720/6 · `москвич 412` 720/7 · `голф 2` 720/9 · `w210` 720/8 · `зил 131` 1,000/11 · `газ 66` 590/6 · `икарус` 480/6 · `вартбург` 480/9 | **None.** Cheap-to-rank nostalgia traffic, near-zero competition |
| 7 | **Chinese & new-entrant brands** | autobild.bg + evpoint.bg | `dong feng` 18,100/14 · `mg` 3,600/7 (evpoint pos 3) · `dr` 1,900/6 · `omoda 5` 720/5 · `togg` 590/5 · `китайски електрически автомобили пловдив` 390/4 · `toyota mirai` 320/5 | **Weakest defensible loss.** `byd bulgaria` 2,400 — autobild pos 14 vs carlifebydani pos 44; `byd българия` 2,900 — carlifebydani pos 46 despite a dedicated BYD article |
| 8 | **Supercars & performance icons** | autobild.bg | `bmw m4` 3,600/11 · `chevrolet camaro` 1,300/8 · `m4` 1,300/8 · `ferrari f40` 1,000/4 · `bmw m4 competition` 880/10 · `bugatti veyron` 720/4 · `golf gti` 720/8 · `nissan 370z` 590/5 · `koenigsegg gemera` 480/3 · `ferrari testarossa` 480/5 · `pagani huayra` 390/5 | **None** |
| 9 | **Motorsport & car-culture figures** | autobild.bg | `пол уокър` 5,400/3 · `ралф шумахер` 2,400/5 · `niki lauda` 1,600/6 · `ники лауда` 880/3 · `филми на пол уокър` 590/2 · `кобра 11 обади се` 590/6 · `нюрбургринг` 480/5 · `писта софия` 480/8 · `кими райконен` 320/5 | **None.** Closest match to the site's existing personality/podcast format |
| 10 | **Fuel retail, dealers & ownership admin** | autobild.bg | `eko` 33,100/8 · `еко` 18,100/12 · `тунел витиня` 1,300/10 · `проверка вин кода бесплатно европа` 720/2 · `примекс софия` 720/2 · `балкан стар` 720/9 · `новите полицейски коли` 480/4 · `софия франс ауто оказион` 480/10 · `бензиностанции еко карта` 390/5 | **None.** Highest raw volume, weakest topical fit — lowest priority of the ten |

---

## Read

Beyond the individual clusters, two conclusions follow from the gap data:

1. **Clusters 2, 3, and 4 are carlifebydani's own declared subject matter, and all
   three are lost to a single competitor.** evpoint.bg holds positions 1–4 on EV
   charging, generic EV head terms, and per-model EV spec pages, and outranks
   carlifebydani on `bmw i3` and `tesla model s` — models carlifebydani has
   published dedicated articles about. This is the most addressable loss because
   the content already exists; it is the on-page execution that is failing.
2. **The absence of any Bulgarian informational phrase across all 26 ranking
   keywords** points back at the site-language misconfiguration already logged as
   P0. Until pages are served as `bg`, clusters 1, 5, 6, 9 and 10 — all of which
   are won with Bulgarian-language editorial content — are effectively unreachable.

Cluster 7 (`byd българия` at pos 46 with a dedicated article live) is the clearest
single diagnostic: the content exists, is topically correct, and still ranks 30+
positions behind a competitor's passing mention.

---

## Data coverage & limitations

All figures pulled **2026-08-04** from Semrush, BG desktop database.

**Retrieved successfully:**

| Domain | Report | Rows returned |
|---|---|---|
| carlifebydani.com | `domain_organic` | 26 — **complete footprint** |
| carlifebydani.com | `domain_organic_organic` | 30 competitors |
| autobild.bg | `domain_organic` | 200 keywords |
| autobild.bg | `domain_organic_organic` | 30 competitors |
| evpoint.bg | `domain_organic` | 25 keywords |

**Blocked — not retrieved:**

- **`domain_domains` (native keyword gap)** — not included in the current Semrush
  plan. Returned a plan-access message on every attempt, for both the
  editorial-competitor set and the EV-specialist set. The gap in this report is
  therefore **manually reconstructed**, not Semrush's own gap output.
- **dizzyriders.bg** — `domain_organic` failed at limits 80, 60, 25, and 15.
- **testdrive.bg** and **drive.place** — not attempted after repeated failures.
- **evpoint.bg beyond the top 25 keywords** — pagination request failed.

**Effect on the findings:** the table under-represents dizzyriders.bg,
testdrive.bg, and drive.place entirely. Clusters **1, 5, 6, 8, and 9** are likely
larger than shown, since those are exactly the editorial clusters those three
domains compete in. No cluster in the table is overstated — each is backed by
retrieved data — but the list is a floor, not a ceiling.

Plan options for restoring `domain_domains` access: https://www.semrush.com/mcp-access

---

*Generated 2026-08-04. Semrush BG database. Figures are a point-in-time snapshot
and will drift as Semrush refreshes its index.*
