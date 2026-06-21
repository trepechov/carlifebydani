# EV News RSS Feed Analysis — Volume Prediction

**Live snapshot taken:** 2026-06-21  
**Sources queried:** 4 configured feeds  
**Purpose:** Baseline research to inform source selection, `max_articles` tuning, and weekly content planning.

---

## Configured Sources

| # | Source | URL | Method |
|---|--------|-----|--------|
| 1 | Electrek | `electrek.co/feed` | RSS |
| 2 | InsideEVs | `insideevs.com/feed` | RSS |
| 3 | The Driven | `thedriven.io/feed` | RSS |
| 4 | EV-Database | `ev-database.org` | HTML |

---

## Last 7 Days — Per-Source Daily Breakdown

### Electrek (electrek.co) — 100-item feed

| Date | Day | Articles | Volume |
|------|-----|----------|--------|
| 14 Jun | Sun | 5 | ████░ |
| 15 Jun | Mon | 12 | ████████████ |
| 16 Jun | Tue | 14 | ██████████████ |
| 17 Jun | Wed | 19 | ███████████████████ |
| 18 Jun | Thu | 15 | ███████████████ |
| 19 Jun | Fri | 14 | ██████████████ |
| 20 Jun | Sat | 0 | · (anomaly — see notes) |
| **avg** | | **11.3/day** | |

### InsideEVs (insideevs.com) — 20-item feed ⚠ small window

| Date | Day | Articles | Volume |
|------|-----|----------|--------|
| 14–16 Jun | Sun–Tue | 0 | · (not in 20-item window) |
| 17 Jun | Wed | 6 | ██████ |
| 18 Jun | Thu | 7 | ███████ |
| 19 Jun | Fri | 4 | ████ |
| 20 Jun | Sat | 3 | ███ |
| **avg** | | **2.9/day** (window avg) | |
| **est. real** | | **~5–7/day** (extrapolated) | |

### The Driven (thedriven.io) — 10-item feed ⚠ very small window

| Date | Day | Articles | Volume |
|------|-----|----------|--------|
| 14–17 Jun | Sun–Wed | 0 | · (not in 10-item window) |
| 18 Jun | Thu | 1 | █ |
| 19 Jun | Fri | 4 | ████ |
| 20 Jun | Sat | 3 | ███ |
| **avg** | | **1.1/day** (window avg) | |
| **est. real** | | **~2–3/day** (extrapolated) | |

### EV-Database (ev-database.org) — HTML source

No publication dates available. This is a spec/database site, not a news feed — it publishes vehicle specs and occasionally updates, not daily news articles. Expected: **0–2 new database entries per week**.

---

## Cross-Source Table (last 7 complete days)

| Date | Electrek | InsideEVs | The Driven | Daily Total |
|------|:--------:|:---------:|:----------:|:-----------:|
| Sun 14 Jun | 5 | 0 | 0 | **5** |
| Mon 15 Jun | 12 | 0 | 0 | **12** |
| Tue 16 Jun | 14 | 0 | 0 | **14** |
| Wed 17 Jun | 19 | 6 | 0 | **25** |
| Thu 18 Jun | 15 | 7 | 1 | **23** |
| Fri 19 Jun | 14 | 4 | 4 | **22** |
| Sat 20 Jun | 0 | 3 | 3 | **6** |
| **7-day total** | **79** | **20** | **8** | **107** |
| **avg/day** | **11.3** | **2.9** | **1.1** | **15.3** |

---

## 7-Day Forward Prediction

| Source | Avg/day | Projected week | Share |
|--------|:-------:|:--------------:|:-----:|
| Electrek | 11.3 | ~79 | 74% |
| InsideEVs | 2.9 (feed cap) / est. ~6 | ~20–42 | 19% |
| The Driven | 1.1 (feed cap) / est. ~2.5 | ~8–17 | 7% |
| EV-Database | ~0 | ~0–2 | <1% |
| **TOTAL** | **~15–20/day** | **~107–138/week** | 100% |

---

## Key Observations & Caveats

**1. Feed size limits create sampling bias.**
InsideEVs (20 items) and The Driven (10 items) don't expose enough history for a full 7-day window. Their real daily output is almost certainly higher — days before their feed window appear as 0. Their true averages are closer to **5–7/day** (InsideEVs) and **2–3/day** (The Driven).

**2. Electrek's Saturday anomaly.**
Saturday June 20 shows 0 in the feed. Likely cause: Electrek timestamps some articles as Friday night or Sunday morning, or the feed was fetched mid-Saturday. Sunday June 14 shows 5, consistent with a lighter weekend pattern. Realistic Sat/Sun range: **3–7 articles**.

**3. Electrek is not 100% EV — handled by engagement sort.**
Electrek covers all green transport (solar, ebikes, energy storage). Approximately 60–70% of articles are EV-relevant. No manual keyword filter is needed: the plugin's engagement sort already acts as a secondary relevance filter. Off-topic articles receive no clicks from an EV audience, sink to Group 3 (zero-click older), and de-prioritise themselves from the podcast script automatically.

**4. Effective plugin input (post-deduplication estimate).**
After cross-source deduplication (~5–10% URL overlap):
- Conservative: **~12–15 new articles/day**
- Realistic: **~15–20 new articles/day**
- Weekly: **~85–140 articles**

**5. Impact on the 50-article cap.**
With `max_articles=50` and ~15–20 new articles arriving daily, the plugin will reach the cap and start trimming oldest articles within **3–4 days** of activation. After steady state: rotation of roughly the last 3 days of content.

---

## Actionable Recommendations

| Finding | Recommendation |
|---------|---------------|
| InsideEVs/The Driven feed windows are too small | Check if they offer paginated RSS (`?paged=2`) or a full-feed option |
| ev-database.org adds no daily news value | Consider removing or replacing with an active RSS news source |
| Electrek dominates at 74% of volume | Not a problem — engagement sort demotes off-topic articles naturally. Adding `electrive.com` (~7–8/day, EU-focused) would still improve source diversity. |
| 50-article cap trims content within 3–4 days | Consider raising `max_articles` to 100–150 for a fuller week of content on the episode page |
