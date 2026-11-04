# Same-story deduplication — plan proposal

Status: **proposal** (2026-10-14). Not started.

## Problem

`ENA_Collector::run()` dedups by **exact URL** only (`existing_urls()`, now across the 3 newest tabs —
see the rollover fix). When several outlets report the same event, each URL is new, `analyze()`
writes a fresh Bulgarian title/summary for each, and the feed shows the same news 2–4 times.

Evidence from the production sheet, tab `20.10.2026` (100 rows, by hand):

| Story | Copies | Sources |
|---|---|---|
| XPeng G9L launches in Europe | 3 | eletric-vehicles, thedriven, arenaev |
| Cheaper VW ID. Buzz | 3 | thedriven, electrek, insideevs |
| Smart #2 debut | 3 | electrek, arenaev, insideevs |
| BYD Atto 8 7-seat SUV in Europe | 3 | electrek, arenaev, insideevs |
| Peugeot concepts (208 / RCZ / E-T16) | 3 | insideevs ×2, arenaev |
| Tesla stops Model 3 orders in Canada | 2 | eletric-vehicles, electrek (identical BG title) |
| Doug Field → Mind Robotics | 2 | eletric-vehicles, electrek |
| Fiat Multipla returns | 2 | arenaev, insideevs |

That is about **11 redundant cards out of 100**, roughly 10%. `06.10.2026` shows the same pattern
(Lucid Cosmos testing, Tesla fan patent, Musk on Robotaxi).

## Why the existing tags are not enough on their own

I tested a deterministic rule on the last three tabs: ≥2 shared brand/model tags and pub_date within
2 days.

- **Precision about 50%.** It flags distinct stories that share a model: three different Rivian R2
  stories (sales, seat fans, second shift), Tesla Model 3 price vs range update, and Model Y rescue
  vs Robotaxi probe.
- **Misses about 40%.** Tags are missing (Fiat Multipla and Peugeot rows have empty tags) or
  inconsistent (`Ривиан` vs `Rivian`, `VW` vs `Volkswagen`, `#2` vs `Smart #2`, `SUV` vs `Atto 8`).

So "same entities" does not mean "same event". The decision needs semantic judgment. Tags are still
useful as a hint, and as a cheap fallback when the LLM call fails.

## Options considered

| | Approach | Verdict |
|---|---|---|
| A | Tag/title similarity rule only (no LLM) | Cheap, but the measured ~50% precision would hide real stories. Rejected as the decider. |
| B | Add a `story_key` field to `analyze()` and match on it | Each article is analysed alone, so the model invents keys inconsistently. Rejected. |
| C | Per-article "is this a duplicate of any of these candidates?" call | Accurate, but needs a candidate pre-filter (which has the recall problem above) and adds N calls per run. |
| **D** | **One clustering call per run:** new rows + recent existing rows in, groups out | **Recommended.** Sees every title and summary at once, does not depend on tags, and costs one extra call per run. |

## Recommended design (option D)

### Where it plugs in

`ENA_Collector::run()`, **after** the `analyze()` loop and **before** `append_rows()`. At that point
we have the Bulgarian title, summary and tags for every new row, which is exactly what the
comparison needs.

```
scrape → URL dedup → analyze() per article → [NEW] find_same_story() → build_row(+dup_of) → append
```

`add_manual()` runs the same check against existing rows. For a manual add it **warns in the admin
notice** ("looks like the same story as …") but does not block, because the editor chose the URL
on purpose.

### The call

New `ENA_OpenRouter::cluster_stories( array $new, array $existing ): array|WP_Error`, with the
prompt in `prompts/story-dedup.md`, following the `analysis-system.md` pattern.

- **Input:** compact lines `id | source | pub_date | bg_title | first sentence of bg_summary | tags`
  - `existing`: rows from the feed tab plus the collection tab (the same tab set as
    `existing_urls()`), limited to pub_date within ±3 days of the new items. That is about
    100–150 rows, roughly 10–15k input tokens.
  - `new`: this run's analysed articles.
- **Output:** `{"matches":[{"new":"n3","same_as":"e17","reason":"..."}]}`, where `same_as` is an
  existing id or another new id.
- **Rule in the prompt:** "same specific event or announcement". The same brand or model alone does
  not count, and when unsure the answer is no. The Rivian R2 and Tesla Model 3 rows above go into
  the prompt as negative examples.
- Temperature 0. `reason` is logged per match, just as `topic_reason` is today.

### What happens to a duplicate (reversible, not dropped)

The row is still **appended**, with a new column **M `dup_of`** that holds the canonical row's
**link**. It points to a link, not a row index, so `sort_by_upvotes()` and `trim_to_max()` cannot
break the reference.

- **Canonical = the row already in the sheet** (first seen). It keeps its GA4 votes and clicks, so
  vote integrity is not affected.
- Within one batch, the earliest `published_at` becomes canonical.
- `ENA_Sync` **skips** rows with a non-empty `dup_of` whose target is still present in the tab. If
  the canonical row was trimmed away, the duplicate is shown again, so nothing gets orphaned.
- The editor can undo a false merge by clearing the cell in the sheet, and the next sync shows the
  row again.
- Podcast doc (`ENA_Podcast`): duplicates are skipped as main items, and their sources can be
  listed under the canonical one as "Също: arenaev.com, insideevs.com".

### Failure mode: fail open

If the clustering call fails (429, parse error, missing key), log a `warn` and append every row with
an empty `dup_of`, which is the same as today. The call never blocks collection.

### Rollout (same approach as v1.2.0 off-topic)

1. **Observation:** fill column M only; the feed is unchanged. Review about 1–2 weeks of matches in
   the sheet.
2. **Enforce:** a settings toggle `hide_duplicate_stories` makes `ENA_Sync` hide them.
3. *(Optional)* The feed card shows "+2 източника" with the alternative source links. This is the
   user-facing benefit of keeping the duplicate rows instead of dropping them.

### Small win with no dependencies

Add a line to `analysis-system.md` to keep brand tags canonical in Latin script (`Rivian`, not
`Ривиан`; `Volkswagen`, not `VW`). This improves the tags for every consumer and the dedup hints.
It is one prompt line and goes through the existing test-06 gate.

## Implementation steps & effort

| # | Step | Files | Effort |
|---|---|---|---|
| 0 | **Offline eval first.** Label true pairs in the last 4–6 prod tabs, replay `cluster_stories()` over them, and measure precision and recall. Target: precision ≥95%, recall ≥80%. Same harness style as `tests/test-05`. | `tests/test-07-story-dedup.php`, `prompts/story-dedup.md` | 0.5–1 d |
| 1 | `cluster_stories()` + prompt loading + JSON parse/validate (ids must exist; drop self-matches and cycles) | `class-ena-openrouter.php` | 0.5 d |
| 2 | Collector integration: build candidate set, call, map to `dup_of`, logging, fail-open; manual-add warning | `class-ena-collector.php`, `class-ena-ajax.php` | 0.5 d |
| 3 | Column M schema: `COLUMNS`, the `A:L`/`A1:L1` ranges, `array_slice(…,12)`, header backfill on existing tabs | `class-ena-sheets.php` | 0.25 d |
| 4 | Sync hides dups behind a setting; podcast doc groups sources; admin setting + "How it works" text | `class-ena-sync.php`, `class-ena-podcast.php`, `class-ena-docs.php`, settings/admin views | 0.5 d |
| 5 | *(optional)* "+N източника" on the feed card | `theme/template-parts/ev-news-feed/card.php`, CSS | 0.5 d |

**Total: about 2.5–3 days for steps 0–4, plus 0.5 d for the optional card UI.** Elapsed time is
longer because of the 1–2 week observation phase.

Running cost: one extra call per collection run, about 15k input and 0.5k output tokens. That is
negligible next to the N `analyze()` calls the run already makes.

## Risks

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| **False merge** hides a real, distinct story (e.g. two different Rivian R2 stories) | Medium | Medium | Step-0 precision gate, "unsure = no" rule, negative examples, observation phase, reversible column, `reason` logged |
| Missed duplicates | Medium | Low | Nothing gets worse than today; tune the prompt using the observation data |
| Column-M schema change breaks reads/appends (index-based `array_combine`) | Low | High | Pad short rows (already done), update every `A:L` site in one commit, run test-02 |
| Extra call hits 429 and slows the run | Low | Low | Fail open; the call runs once, after the analyze loop |
| Interplay with the freeze window / rollover (uncommitted `DEDUP_TAB_COUNT` fix) | Low | Medium | Reuse `recent_session_tabs()` for the candidate set; commit the rollover fix first |
| Votes split across copies | n/a | n/a | Already avoided: the canonical row is the existing one, and hidden rows get no feed votes |

**Overall risk: low–medium.** The only change visible to users (hiding rows) is behind a toggle,
comes after an observation phase, and is undone by clearing a sheet cell.

## Open questions

1. When a duplicate comes from a **better** source (e.g. the original press release vs a re-write),
   should the canonical row switch? Proposal: no for v1, because the first row holds the votes.
2. Should duplicates from an **older** tab (last week's story re-reported) be hidden as well, or only
   duplicates within the feed tab? Proposal: compare across the dedup tab set, ±3 days of pub_date.
