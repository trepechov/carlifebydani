# SEO keyword-gap plan — high-volume terms where we don't show up

**Created:** 2026-11-04 · **Status:** PLAN — nothing written to the site yet.
**Next session starts at:** [§6 Next-session checklist](#6-next-session-checklist).
**Scoreboards (append-only):** Track A [reports/seo-keyword-gaps/gap-tracker.csv](../reports/seo-keyword-gaps/gap-tracker.csv) · Track B [reports/seo-keyword-gaps/ctr-tracker.csv](../reports/seo-keyword-gaps/ctr-tracker.csv)

## 1. What this plan is

A short list of Bulgarian search terms that (a) have real monthly demand and (b) we rank badly for
or not at all, grouped into six content projects. Each project says what to build, what page owns the
term, how to avoid fighting our own pages, and how we will know it worked.

The plan has **two tracks**:

- **Track A — keyword gaps (§2–§6):** "no page, or wrong page" for a term with real demand.
- **Track B — ranks but doesn't get clicked (§7):** posts on page 1 (positions ~4–9) whose CTR is far below what that position should earn (ZEEKR 7X, Toyota bZ4X, Mercedes EQS ...). "Same rank, better snippet".

### Relationship to earlier work (read first — this plan builds on it, it does not replace it)

- `reports/seo-optimizations/2026-09-04-content-gap-recovery-strategy.md` already identified Track A's charging-station and generic EV head-term clusters (its clusters 2 and 3) from a competitor gap against **evpoint.bg**, and decided "research + outline only" for new pillar pages. Its key finding matters here: evpoint.bg wins those terms with a **spec database + charging-station locator**, not with blog articles. Projects 3.1 and 3.4 must take that into account (see their notes).
- `reports/seo-optimizations/2026-09-04-byd-bulgaria-plan.md` covers BYD; post 8227 was already optimized on 2026-09-04 (see the corrected §3.2).
- Cluster plans already exist for MG/SAIC, Renault, VW ID.4, BMW i3, Tesla Model S and Porsche in the same folder; Track B defers to them where they overlap.
- Every applied change goes through `ledger.csv` / `checks.csv` in that folder, so before touching any post: `grep <post_id> reports/seo-optimizations/ledger.csv`.

**Selection rule:** BG volume ≥ ~170/mo (or a head term for a topic we already cover) **and**
GSC shows no footprint or an average position > 10 over the last 90 days.

### Data caveats (read before trusting a number)

- Volumes are Google Ads planner buckets (DataForSEO, 2026-11-04) unless marked *Semrush Aug*.
  They are rounded (10/20/30 ... 720/880/1 000) and are monthly averages over 12 months — several of these terms
  are **rising fast** (see "trend"), so the Sep value is often 2× the average.
- GSC window: **2026-08-03 → 2026-11-01** (90 days). "Impr" = impressions, "pos" = average position.
- Terms where DataForSEO returned no volume are treated as < 10/mo and ignored.
- **Intent is unchecked.** `зарядна станция` and `byd българия` can be shopping / dealer searches. Step 1 of every
  project is a live-SERP check, so we don't write a guide for a results page made of shops.
- `електрически автомобил` = 12 100 is a *Semrush Aug* figure that looks high next to `електромобил` = 720;
  re-check it with DataForSEO before using it as a target.

## 2. Gap table (baseline, 2026-11-04)

| # | Project | Head term(s) → BG vol/mo | Where we stand now (GSC 90d) | Landing page |
|---|---|---|---|---|
| 1 | **Charging stations** | `зарядна станция` 1 600 · `зарядна станция за електрически автомобили` 1 300 · `зарядни станции` 1 000 · `зарядна станция за електромобил` 720 · `зарядни станции в българия` 170 (Sep: 390) · `wallbox` 210 · `зарядно за електромобил` 110 | **Head term: no GSC row at all.** Long tails already rank on two posts: `зарядна станция в гаража` pos 3.5, `тесла зарядни станции в българия` pos 3.9, `зарядна станция за тесла` 122 impr pos 8.4, `зарядна станция за гараж` pos 7.4. `wallbox`: no impressions | **New pillar** (see §3.1) |
| 2 | **BYD** | `byd българия` 6 600 (Sep 9 900) · `byd seal` 1 600 · `byd atto 3` 880 · `byd dolphin` 720 | **Article exists and was optimized 2026-09-04 (post 8227), yet the head term is still far down:** `byd българия` 6 impr pos 38, `byd bulgaria` 2 impr pos 27. The post is found for the wrong intent: `byd значение` 33 impr pos 7.5, `byd какво означава` 27 impr pos 8.7 (0 clicks). Also `byd румъния` 56 impr pos 7.6 (an EV News episode) | **Existing post 8227** + model pages |
| 3 | **Chinese newcomers** | `leapmotor` 2 900 · `xpeng` 2 400 · `leapmotor c10` 880 · `leapmotor t03` 590 · `xpeng g6` 480 (90 → 1 000 in 12 mo) · `китайски електромобили` 170 | `leapmotor` 3 impr pos 38 (on `/ev-news-feed/`); `xpeng` ~0; `китайски електромобили` 1 impr pos 28 | **New pillar** |
| 4 | **"Electric car" head terms** | `електромобил` 720 · `електромобили` 720 · `електрически автомобил` 12 100 *(verify)* · `зареждане на електромобил` 90–170 | `електромобил` **144 impr, pos 19.7, 0 clicks**; `зареждане на електромобил` 90 impr pos 12.5; `електромобили` 2 impr pos 19.5 | **Upgrade existing** `/publications/kak-da-smenim-dvgto-s-elektromobil/` |
| 5 | **Xiaomi SU7** | `xiaomi su7` 1 600 · `xiaomi su7 ultra` 1 600 · `xiaomi su7 цена` 320 | `xiaomi su7` 6 impr pos 6.8, via an EV News episode about crashes (`/ev-news/evn51-…`) | **New evergreen explainer** |
| 6 | **Tesla Supercharger BG** | `tesla supercharger` 2 400 · `tesla wall connector` 70 | `tesla supercharger bulgaria` 77 impr pos 8.4 · `tesla superchargers bulgaria` 47 impr pos 7.2 · `supercharger bulgaria` pos 11 — the post is **close** | **Upgrade existing** `/publications/stancziite-na-tesla-v-blgariya-veche-sa-plateni/` |

**Indicative prize** (clicks/mo if we reach avg position ~5 at ~6% CTR; a ceiling for sizing, not a forecast):
#2 ≈ 400 · #1 ≈ 100 (head) + long tails · #3 ≈ 170 · #5 ≈ 100 · #4 ≈ 45 (grows if `електрически автомобил` volume is real) · #6 ≈ 140.
Intent filtering in step 1 will shrink #1 and #2.

## 3. The six projects

For every project the first two tasks are the same:

1. **SERP/intent check** — DataForSEO live SERP for the head term (and Google autocomplete `hl=bg`). Record who ranks (shops, dealers, news, forums) and which result types appear. Decide go / adjust keyphrase / drop.
2. **Ground the content in what Dani has actually said** — `youtube-rag` (`search_transcripts`) for the topic, cite `timestamp_url`. No invented prices, specs or availability; ASR drops brand names, so never publish raw transcript text.

### 3.1 Charging stations (the one you flagged) — NEW PILLAR

- **Why it is the top item:** biggest verified demand in the set, plus it has been a dead slot in the Semrush tracker since August. We already own the long tails on two posts, which proves topical relevance — the missing piece is a page for the head term.
- **Page:** a guide-style pillar, working title *"Зарядна станция за електромобил: видове, цени, как да изберем и къде в България"*. Keyphrase candidate: `зарядна станция за електромобил` (720) with `зарядна станция` (1 600) as the secondary — decide after the SERP check.
- **Content outline (to confirm in step 2):** AC wallbox vs DC fast charging · home vs public · what to check before buying (phases, kW, cable/connector, installation) · public networks in Bulgaria · cost to charge (link to `колко струва зареждане на електромобил`, 70/mo, Sep 110) · FAQ block.
- **Don't cannibalise:** the pillar owns the head terms. The two existing posts keep their long tails (`в гаража`, `за тесла`). Add contextual links **pillar ⇄ both posts** and a link from the Публикации/EV News category descriptions if we ever move to HTML descriptions.
- **Sub-keywords to fold in as H2s, not separate posts (yet):** `wallbox` 210, `зарядно за електромобил` 110, `tesla wall connector` 70, `зарядни станции в българия` 170.
- **Success:** pillar indexed within 7 days; `зарядна станция за електромобил` in top 20 by day 45, top 10 by day 90; head term `зарядна станция` registers any GSC impressions by day 45.
- **Risk:** the head term may be shopping-dominated; then target the "за електромобил / как да изберем" variants and accept a smaller prize.
- **Competitor note (from the 2026-09-04 gap refresh):** evpoint.bg holds `зарядна станция` at position 1–2 with a charging-station *directory/locator*, not an article. A guide can still win the informational variants ("как да изберем", "цена", "видове"), but we should not expect to beat a directory on the bare head term. The honest target for this pillar is the `за електромобил` / `в България` / `как да изберем` family, plus topical authority that lifts the other charging pages.

### 3.2 BYD — EXISTING POST ALREADY OPTIMIZED; DIAGNOSE, THEN BUILD MODEL PAGES

- **Correction to the first draft of this plan:** it said "no BYD page". There is one: post **8227** *BYD вече е в България* (publications), optimized 2026-09-04 (title, meta, focus keyphrase, tags, alt, content; ledger `8227-2026-09-04-C`). The earlier plan measured `byd bulgaria` at 4 400/mo, position 45. The +28 day check (2026-11-04) shows 207 impressions at position 7.7, CTR 1.93%, verdict *inconclusive*; but in this window the **head term itself** is still `byd българия` pos 38 (6 impr) / `byd bulgaria` pos 27 (2 impr). So the page is indexed and relevant, it just does not win the head query.
- **Why it probably doesn't:** hypothesis, unverified — the head-term SERP is dealer/importer sites and news, which Google ranks above a single article; and our page currently picks up *explainer* intent (`byd значение`, `byd какво означава`: 60 impressions at pos 7.5–8.7, zero clicks).
- **Step 1:** live SERP for `byd българия` (who ranks: dealer, BYD site, news?). Decide whether the head term is winnable at all.
- **Step 2 (cheap):** the "what does BYD stand for" impressions are free demand — add a short *"Какво означава BYD"* answer block near the top of 8227 (verify the expansion from a source before writing it) to turn those 60 impressions into clicks.
- **Step 3 (if Step 1 says winnable):** model-level pages — `byd atto 3` (880), `byd dolphin` (720), `byd seal` (1 600) — each linking back to 8227; use first-hand material only (check the transcript archive first, see 9220 *BYD Denza Z* optimized 2026-11-04).
- **Success:** impressions on the head term > 50 by day 45 and `byd българия` position < 20 by day 90; clicks on `byd значение` / `какво означава` > 0.

### 3.3 Chinese newcomers pillar — NEW

- **Gap:** zero footprint on `leapmotor` (2 900), `xpeng` (2 400) and the model terms, all with LOW competition (index 4–15). `китайски електромобили` (170) is the dead Semrush slot.
- **Existing asset — check first:** post **4115** *Китайските електромобили са тук, за да останат* was already optimized on 2026-09-04 for exactly `китайски електромобили` (ledger `4115-2026-09-04-C`), yet that term is still at position 28 with 1 impression. So do **not** write a second page for it. Decide in the SERP check whether 4115 should be reworked into the pillar (preferred — it avoids cannibalisation and keeps its age/links) or stays as a context piece that links to new brand pages.
- **New content here is the brands we do not cover:** *Leapmotor* (C10, T03) and *Xpeng* (G6) — sections or short pages, linked from 4115 and from ZEEKR 7X (our strongest internal-link asset, 7.3k impressions on `zeekr 7x`), BYD ← 3.2, Xiaomi ← 3.5.
- **Dependency:** do 3.2 Step 1 first (same SERP type: importers/dealers).
- **Success:** `китайски електромобили` top 10 by day 60; first impressions on `leapmotor` / `xpeng` by day 30.

### 3.4 "Електромобил" head terms — UPGRADE EXISTING

- **Gap:** 144 impressions at pos 19.7 with zero clicks, and the post we track for it is a ДВГ→EV switching story, not a definitive page.
- **Action:** don't create a new page (it would compete with itself). Rework `/publications/kak-da-smenim-dvgto-s-elektromobil/`: title/H1/intro aligned to `електромобил`, a short definition + decision section (who it suits, costs, charging, range, winter), FAQ block, links down to the charging pillar (3.1), the reviews and the Chinese pillar (3.3).
- **Run through the existing 3-phase skills:** `seo-keyphrase-research` → `seo-article-apply` (this is a publication, so no transcript-content phase).
- **Success:** `електромобил` average position < 12 by day 60, then < 8; clicks > 0.

### 3.5 Xiaomi SU7 — NEW EVERGREEN EXPLAINER

- **Gap:** 1 600/mo head terms and we only appear through a crash-focused news episode (6 impressions).
- **Page:** *"Xiaomi SU7: цена, версии и какво знаем"* — facts-first. **Do not state Bulgarian availability or price unless a source is cited** (verify during step 2).
- **Link to:** the EV News crash episode, the Chinese pillar (3.3).
- **Success:** impressions on `xiaomi su7` > 100 by day 45; position < 10 by day 90.

### 3.6 Tesla Supercharger Bulgaria — UPGRADE EXISTING (cheapest win)

- **Gap:** striking distance. `tesla supercharger bulgaria` 77 impr pos 8.4, `…s bulgaria` 47 impr pos 7.2, `зарядна станция за тесла` 122 impr pos 8.4 — all on one post, none in the top 5.
- **Action:** keep the page, improve it: title/meta built on `tesla supercharger` + `България` + `цени`/`платени` (the page's own angle), up-to-date station list/map, price table with the date, FAQ, link to the new charging pillar (3.1). CTR on `зарядна станция за тесла` is 0.8% at pos 8 — the snippet is weak.
- **Success:** pos < 5 on the three supercharger/Tesla-station queries by day 45.
- **Do first among the six** — it is a 1-post change and de-risks the method for the rest.

## 4. Suggested order

1. **3.6** Supercharger post upgrade (1 post, quick, striking distance)
2. **3.1** Charging pillar (your priority; uses what 3.6 learns about the SERP)
3. **3.4** `електромобил` post rework (links to 3.1)
4. **3.2** BYD diagnosis + "what does BYD mean" block → **3.3** Chinese brands (3.3 links into 3.2)
5. **3.5** Xiaomi SU7

**Interleave Track B (§7):** it is cheaper per click than most of Track A, so run its Tier 1 batch (B1–B3) between 3.6 and 3.1 rather than after everything.

## 5. How we keep track

**Scoreboard:** `reports/seo-keyword-gaps/gap-tracker.csv`, append-only, one row per (keyword, date), seeded with today's baseline. Columns:
`date,keyword,project,bg_volume,volume_source,gsc_position,gsc_impressions,gsc_clicks,landing_page,status,note`.

**Cadence (reuse the existing monthly rhythm):**

- **Per project, after publishing:** record `date_applied` and verify at **+28 days** (same convention as `reports/seo-optimizations/ledger.csv`; add each change there with `verify_due`).
- **Monthly (with `seo-performance-report`):** re-pull GSC for every keyword in the tracker (same 90-day window logic), append rows, compare to baseline. GSC is the scoreboard; Semrush positions are a cross-check only (SERP-feature swings — see the tracker-volatility note).
- **Semrush free tier (10 slots):** the three dead slots (`зарядна станция`, `китайски електромобили`, `нощна тарифа`) + `домашно зареждане на електромобил` (volume 0). Proposed swap, to be done by hand in the Semrush UI: keep `зарядна станция` (it's the target now), retire `нощна тарифа` and `домашно зареждане на електромобил`, add `byd българия` and `leapmotor`.
- **Stop rules:** a project with no GSC impressions on its head term after 60 days gets a SERP re-check, not more content. A project whose SERP check shows shop-only results is dropped or re-aimed.

## 6. Next-session checklist

**Setup (once)**
- [ ] Re-read this file and `gap-tracker.csv`; confirm the Search Console date window (rolling 90 days ending 3 days ago).
- [ ] Decide go / no-go per project after the SERP checks (DataForSEO live SERP, `hl=bg`).

**Project 3.6 — Supercharger (start here)**
- [ ] Back up Yoast postmeta of the post (existing backup routine in `reports/yoast-meta-backup/`).
- [ ] Pull transcript evidence on Tesla charging in Bulgaria (`youtube-rag`).
- [ ] Draft title / meta / H1 / intro / FAQ; present before/after manifest; apply after approval.
- [ ] Add ledger row with `verify_due` = +28 days.

**Project 3.1 — Charging pillar**
- [ ] SERP + intent check for `зарядна станция`, `зарядна станция за електромобил`, `wallbox`.
- [ ] Pick final keyphrase; outline; gather transcript evidence + any facts that need a source.
- [ ] Draft in Bulgarian, with FAQ; internal-link plan to the garage post and Tesla-stations post.
- [ ] Publish as draft → review → publish; add ledger row.

**Project 3.4 — `електромобил` rework**
- [ ] Re-check `електрически автомобил` volume (12 100 *Semrush Aug*) with DataForSEO.
- [ ] Run keyphrase research + apply on the post.

**Projects 3.2 / 3.3 / 3.5**
- [ ] Inventory existing BYD / Leapmotor / Xpeng / Xiaomi coverage (site search + transcripts).
- [ ] SERP check each; decide go / no-go.
- [ ] Draft in the order of §4.

**Housekeeping**
- [ ] Update the Semrush roster (§5) and the tracker CSV.
- [ ] Add a monthly "gap tracker" step to `seo-performance-report` so progress is reviewed without being asked.

## 7. Track B — pages that rank but don't get clicked

**Window:** GSC 2026-08-03 → 2026-11-01 (90 days). **Benchmark:** the expected-CTR curve from `gsc_opportunities`
(pos 4 = 7.5%, pos 5 = 6%, pos 6 = 4.8%, pos 7 = 3.8%, pos 8 = 3.1%). It is a generic curve, so treat the numbers as a ceiling, not a forecast.
Across the 25 worst query rows the tool counts **~1 400 "missed" clicks per 90 days**, about 45% (≈630) of them on ZEEKR 7X alone.

### 7.1 What we already know — this is not a plain "rewrite the title" job

Earlier title/meta rewrites on four of these posts did **not** lift CTR (`reports/seo-optimizations/checks.csv`, +28 d, 2026-11-04):

| Post | Optimized | CTR before → after | Verdict |
|---|---|---|---|
| ZEEKR 7X (9099) | 2026-08-14 | 1.57% → 1.81% (pos 6.4 → 5.9) | flat |
| Tesla Model Y Juniper (6165) | 2026-08-14 | 2.31% → 1.32% (pos 5.2) | **regressed** |
| Renault 5 (6889) | 2026-09-04 | 0.68% → 0.70% | flat (and `-C2` regressed on impressions) |
| VW ID.4 GTX (130) | 2026-09-05 | 1.08% → 1.25% (pos moved >1) | inconclusive |

Of 66 recorded checks, 50 are inconclusive (mostly an impression floor) and only 5 improved. So a new round of rewrites without a diagnosis would repeat the same low-yield pattern.
**The method for Track B therefore changes:** diagnose the SERP first, change one thing, measure at +28 days.

Where a snippet *does* work on this site, it is the **"мнения" / opinion modifier**: `киа ниро електрик мнения` 10.7% (pos 3.6), `toyota bz4x мнения` 10.6% (pos 4.0), `mg4 мнения` 19.8% (pos 3.3). The bare model-name queries at pos 6–8 get ~1% — probably a different intent (images, news, specs, dealer pages) that our review post answers poorly.

### 7.2 Two tiers

**Tier 1 — never optimized (default title = post title, no meta description at all, so Google writes its own snippet).** Low risk, the obvious first batch. Current Yoast state read from the live REST output on 2026-11-04.

| ID | Post | Impr / clicks / CTR / pos (90 d) | Worst query rows | Note |
|---|---|---|---|---|
| 200 | **Toyota bZ4X** (ev-review) | 9 374 / 151 / 1.61% / 7.9 | `toyota bz4x` 1 977 impr 0.71% pos 6.5; `bz4x` 595 impr 0.50% | Title is a quote with no keyword; but `…мнения` already wins at 10.6% → keep that hook, add the model + "мнения/тест" |
| 420 | **Mercedes EQS 580** (ev-review) | 4 588 / 36 / 0.78% / 6.8 | `mercedes eqs` 745 impr, 1 click, pos 8.6 | Worst CTR in the ev-review set |
| 3366 | **Stark Varg** (publications) | n/a | `stark varg` **756 impr, 0 clicks**, pos 8.1; `stark varg цена` 583 impr 1.2% | Zero clicks from 756 impressions; Semrush volume 1 600 |
| 6280 | **MHero 917** (publications) | n/a | `mhero` 1 829 impr 0.82% pos 7.3; `dongfeng m hero` 634 impr 0.32% | `mhero 917` already 3.6% at pos 3.6 |
| 4845 | **BAW Pony** (ev-review) | 2 322 / 45 / 1.94% / 7.4 | `baw pony цена` 496 impr 2.2% | |
| 2800 | **MG4** (publications) | clicks 179 vs 295 prev. period (**−39%**) | `mg4` 1 030 impr 1.07%; `mg 4` 824 impr 1.7%; `mg car цена` 364 impr, 0 clicks | **Cannibalises MG ZS EV** (`mg електрическа кола`, `mg кола`). Title ends with a full stop. See the existing MG/SAIC plan |
| 3474 | **MG ZS EV** (publications) | clicks 186 vs 327 (**−43%**) | `mg zs мнения` 1 250 impr 2.1% pos 7.7; `джип mg` 165 impr 6.1% at pos **1.0** | Declining page; do together with MG4 |
| 3550 | **Rimac Nevera** (publications) | n/a | `rimac nevera` 771 impr 1.04% pos 6.0 | |
| 2648 | **Lucid Air** (publications) | n/a | `lucid` 690 impr 0.58% pos 6.3 | Head term is just the brand, ambiguous intent |
| 5946 | **Microlino** (publications) | n/a | `мини кола` 146 impr, 0.68% at pos **1.1**; `microlino цена` 588 impr 1.5% | CTR at position 1 is the signature of a SERP feature (AI answer / shopping / video) above us — diagnose, don't just retitle |
| — | **Volvo C40** (ev-review) | n/a | `volvo c40` 268 impr, 0 clicks, pos 4.4 | Meta state not checked yet |

(`n/a` page-level totals: only query rows were pulled for the publications posts; add the page totals to `ctr-tracker.csv` in the first session.)

**Tier 2 — already optimized, still low CTR. Diagnose before touching.**

| ID | Post | Impr / clicks / CTR / pos | What the data says |
|---|---|---|---|
| 9099 | **ZEEKR 7X** | 35 837 / 502 / 1.40% / 6.2 | Biggest single prize. Queries: `zeekr 7x` 7 266 impr 1.29%; `zeekr` 4 223 impr 0.36%; `zeekr 7x цена` 2 898 impr 1.38%; `zeekr bulgaria` 2 474 impr 0.53%; `zeekr българия` 1 774 impr 0.96%. Already has price in title and meta (€53,990). `zeekr 7x мнения` gets 4.6% at pos 5.3 — the opinion modifier again |
| 6165 | **Tesla Model Y Juniper** | `новата тесла` 892 impr 0.9% pos 4.5; `най новата тесла` 192 impr 5.2% pos 1.8 | Title "първи впечатления" does not contain "нова/новата"; CTR fell after the Aug rewrite |
| 6889 | **Renault 5** | `рено 5` 808 impr 1.1% pos 5.6; `рено 5 електрическо` 627 impr 0.8% | Title/meta use the Latin `Renault 5`; searchers use Cyrillic `рено 5` (≈25× the impressions of the Latin form, per the tracker notes) |
| 130 | **VW ID.4 GTX** | 4 293 / 40 / 0.93% / 7.4 | Position near 7 and no distinguishing hook |

**Special case — not a snippet problem:** `христо бъчваров` has 974 impressions at **position 1.9 but 1.75% CTR** (expected ~15%), and the `/partners/` page takes another 778 impressions at pos 5.4 for the same query. That is cannibalisation by a non-article page; the fix is to stop `/partners/` competing for the name (or merge), not to rewrite the Masters episode's title.

### 7.3 Method (per post)

1. **Skip if recently touched:** `grep <post_id> reports/seo-optimizations/ledger.csv`; if applied < 28 days ago, wait for the check.
2. **SERP diagnosis (new step):** live SERP for the 1–2 worst queries (`dataforseo` live SERP, `bg`): what is above us (AI answer, video, top stories, shopping, images)? Is our title shown as written or rewritten by Google? Which competitor snippets use "мнения / цена / тест / €"? Record in the post's report.
3. **One hypothesis, one change set:** e.g. "add the opinion modifier + the model name in the first 55 characters of the title, and write a 150–160 char meta that answers the query". Don't change body, tags and title together if the question is about CTR.
4. **Apply through `seo-article-optimize` (Phase A + C):** it backs up Yoast postmeta, shows a before/after manifest and waits for approval. EV News posts get Phase B too; none of the Track B posts are EV News.
5. **Write the ledger row** with `verify_due` = +28 days; the monthly `seo-performance-report` appends the check.
6. **Pass criterion:** CTR up ≥ 30% relative **at a position within ±1 of the baseline**, judged by `checks.csv` (`improved`). If position moves more than 1, the check says "not comparable" — wait another 28 days.

### 7.4 Suggested batches

| Batch | Posts | Why this order |
|---|---|---|
| **B1** | Toyota bZ4X (200), Mercedes EQS (420) | Biggest impression volume with no snippet at all; safest wins |
| **B2** | Stark Varg (3366), MHero 917 (6280), BAW Pony (4845) | Cheap, zero or near-zero clicks on hundreds of impressions |
| **B3** | MG4 (2800) + MG ZS EV (3474) together | Declining, mutually cannibalising; follow the existing MG/SAIC plan so we fix it once |
| **B4 (diagnose only)** | ZEEKR 7X (9099), Microlino (5946) | Biggest prize but already optimized / SERP-feature suspected: run steps 1–2 and write the finding down before any edit |
| **B5** | Rimac Nevera, Lucid Air, Juniper, Renault 5, Volvo C40 | Smaller or ambiguous-intent; do after B1–B4 results show what works |
| **Separate** | `/partners/` vs the Христо Бъчваров episode | Cannibalisation fix, not a snippet rewrite |

### 7.5 Track B checklist (add to the §6 session list)

- [ ] Pull page-level totals (impr/clicks/CTR/pos, same 90-day window) for the posts marked `n/a` and fill `ctr-tracker.csv`.
- [ ] Confirm the `meta description` is really absent on each Tier 1 post (live page, not only REST) and that the SEO title equals the post title.
- [ ] B1: run SERP diagnosis on `toyota bz4x`, `mercedes eqs`; then `seo-article-optimize` on 200 and 420.
- [ ] B2: Stark Varg, MHero 917, BAW Pony.
- [ ] B3: read `2026-09-04-mg-saic-cluster-plan.md`, then MG4 + MG ZS EV in one pass.
- [ ] B4: ZEEKR 7X and Microlino SERP findings written to a report (no edit yet).
- [ ] Decide how to handle `/partners/` for `христо бъчваров`.
- [ ] After each apply: ledger row with `verify_due` +28 days; append the row to `ctr-tracker.csv`.
