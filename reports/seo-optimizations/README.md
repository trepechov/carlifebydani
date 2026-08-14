# SEO Optimization Ledger — carlifebydani.com

Machine-readable record of every applied SEO change and whether it worked. Two files, both
append-only and committed:

- **`ledger.csv`** — one row **per applied change set**. A row is written only when something
  was actually written to WordPress — Phase A (research) writes no row, and declined items
  don't get one either (they stay in the post's report, see
  [`reports/seo-metatags/README.md`](../seo-metatags/README.md)).
- **`checks.csv`** — one row **per verification measurement** against a ledger row, appended
  by `seo-performance-report`'s monthly verification step. A post checked twice keeps both
  readings, so the trajectory is visible.

Full design and reasoning: [`docs/SEO_SKILLS_REFACTOR.md` §13](../../docs/SEO_SKILLS_REFACTOR.md#w13--the-optimization-ledger-and-the-verification-loop--2h).

## Why this exists

Two problems, one artifact:

1. **Nothing machine-readable said a post was already optimized.** The prose reports in
   `reports/seo-metatags/` are the durable *reasoning*, but a skill starting a new run
   couldn't cheaply answer "has post 5240 been touched, and what was declined?" without
   reading every report file. `ledger.csv` is the index; grep it by `post_id` before starting
   Phase A on any post.
2. **Nothing was ever measured after a write.** Every change so far was applied on the
   assumption it helps, with no baseline frozen and no re-check scheduled. `verify_due` on
   each ledger row plus the monthly verification step in `seo-performance-report` closes that
   loop — the report finds pages worth optimizing, the pipeline fixes them, this ledger
   verifies whether it worked, and the verdicts feed back into next month's priorities.

## `ledger.csv` columns

| Column | Notes |
|---|---|
| `id` | `<post-id>-<YYYY-MM-DD>`; suffix `-b` if a second phase applies to the same post on the same day |
| `date_applied` | the write date |
| `post_id`, `slug`, `category` | which post, which of the 4 categories |
| `phase` | `C` (metatags/tags/alt/links, via `seo-article-apply`) or `B` (transcript content, via `ev-news-transcript-content`) — they move different metrics, see Verdicts below |
| `changed` | pipe-separated subset of `title\|metadesc\|focuskw\|tags\|content\|alt\|media_title\|inbound` — the approval-gate manifest groups from `_shared/approval-gate.md`, so a partial approval is recorded exactly. `title` is the Yoast SEO title (post-level); `media_title` is the featured image's own library title (media-level) — kept distinct since they're different objects on different endpoints |
| `report` | filename in `reports/seo-metatags/` — the prose stays there, this ledger never duplicates it |
| `keyphrase` | the focus keyphrase at time of write |
| `base_start`, `base_end`, `base_impr`, `base_clicks`, `base_ctr`, `base_pos` | the **frozen** 28-day GSC window ending the day before `date_applied`, for the whole page |
| `kw_base_impr`, `kw_base_pos` | same window, filtered to the focus keyphrase alone |
| `backup` | path under `reports/yoast-meta-backup/` for this row — makes a `regressed` verdict a one-step undo |
| `verify_due` | `date_applied + 28d` (phase C) or `+ 56d` (phase B — new body text needs to be crawled, indexed and start ranking) |

## `checks.csv` columns

| Column | Notes |
|---|---|
| `ledger_id` | joins to `ledger.csv`'s `id` |
| `date_checked` | when the measurement was actually read (may be later than `checkpoint` implies — see the anchoring note below) |
| `checkpoint` | the nominal milestone: `28d` / `56d` / `90d` |
| `win_start`, `win_end` | the window **actually measured** — always `date_applied+1 … date_applied+28` (or `+56`), never "the last 28 days from today" |
| `impr`, `clicks`, `ctr`, `pos` | page-level, the after window |
| `kw_impr`, `kw_pos` | keyphrase-filtered, the after window |
| `ctrl_impr_pct`, `ctrl_pos_delta` | what the site-wide + cohort control did over the same window — the counterfactual the verdict is judged against |
| `verdict` | see below |
| `note` | free text — calibration caveats, control-quality flags, anything a future reader needs |

> 🔑 **The window is anchored to `date_applied`, never to the check date.** A monthly cadence
> means a row due on the 3rd of a month waits ~4 weeks for the next report; that's harmless —
> GSC has fully settled by then — but only if `win_start`/`win_end` stay the fixed window after
> the change. Reading "the last 28 days" instead would give two posts optimized a week apart
> two different windows, and cross-post comparison breaks immediately.

## Verdicts

| Verdict | Meaning |
|---|---|
| `improved` | beats the control on the metric the change targeted |
| `flat` | inside the control band — not a failure, the page may simply have been fine |
| `regressed` | worse than control, snippet/content confirmed actually live — use `backup` to roll back |
| `not-shipped` | not re-crawled yet, or Google is serving its own rewritten snippet instead of what was written |
| `inconclusive` | below the impression floor (~50) — a demand problem, not a copy problem |

**Match the metric to `changed`:** `metadesc`/`title` → page CTR at stable position;
`focuskw`/`tags` → the keyphrase's own position; `content`/`inbound` → impressions and query
count (new text ranks for phrases the page couldn't reach before). A metadesc rewrite that
moves impressions moved nothing — impressions are a ranking signal, the snippet doesn't touch
ranking.

## Who writes what

- `seo-article-apply` (Phase C) and `ev-news-transcript-content` (Phase B) append a `ledger.csv`
  row at the same moment as the live write, from the same approved-changes data — never as a
  separate step, or it drifts.
- `seo-keyphrase-research` (Phase A) reads `ledger.csv` at Step 0, before any research, so a
  prior `regressed` or `flat` verdict is visible before proposing the same change again.
- `seo-performance-report`'s monthly verification step (see its own README) selects due rows,
  checks whether the change actually shipped, pulls the after window, controls for the
  site-wide + cohort tide, and appends to `checks.csv`.
