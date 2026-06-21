# Documentation vs Implementation — Discrepancy Tracker

Each item is one discrepancy. Work through them in order and mark the resolution.

**Status values:** `[ ]` open · `[x]` done · `[s]` skipped

---

## 1. Spreadsheet has 9 columns, not 8

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — Sheets adapter spec, column list, read range `A:H`
- **Reality:** 9 columns — `title | description | link | author | upvote | downvote | clicks | added_date | summary` (col I). All read/write ranges are `A:I`.
- **Fix options:**
  - Update plan doc column table, range references, and `read_data_rows()` / `append_rows()` signatures to include `summary`.

---

## 2. Podcast pipeline: body scraping + `podcast_script()` never happens

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-podcast.php` pipeline spec
- **Reality:** No scraping. Uses existing `title` + `description` from the Sheet. Calls `podcast_summary()` (not `podcast_script()`). `podcast_script()` still exists in `ENA_OpenRouter` but is unused.
- **Fix options:**
  - Update plan doc pipeline to match actual flow (no `extract_body`, `podcast_summary()` not `podcast_script()`).
  - Or decide if `podcast_script()` should be removed from `ENA_OpenRouter`.

---

## 3. Podcast creates a new Google Doc (plan) vs writes to existing manually-created doc (reality)

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-podcast.php` pipeline, `class-ena-docs.php` spec
- **Reality:** User creates a Google Doc manually and pastes its ID in settings. Plugin only calls `append_sections()`. `create_doc()` and `move_to_folder()` are never called by the podcast run. Drive Folder ID field is commented out in the settings form.
- **Note:** `EPISODE_WORKFLOW.md` already describes this correctly.
- **Fix options:**
  - Update plan doc podcast pipeline and docs class spec.
  - Optionally note that `create_doc()` / `move_to_folder()` exist for future use but are not in the current flow.

---

## 4. `ENA_Sync` runs in the daily cron — docs and class comment say it doesn't

- **Status:** `[ ]`
- **Affected docs:** `EV_NEWS_AUTOMATOR_PLAN.md` — ENA_Sync spec; `class-ena-sync.php` — class-level comment
- **Reality:** `$plugin->sync->run()` is called at the end of both `run_daily_collection()` (cron) and `handle_run_collection()` (manual AJAX trigger). Sync always runs after collection.
- **Fix options:**
  - Update plan doc and the class comment to say sync runs after every collection (cron and manual).
  - Or remove the sync call from cron and keep it manual-only (revert code).

---

## 5. 24-hour age filter on collected articles — not documented anywhere

- **Status:** `[ ]`
- **Affected docs:** `EV_NEWS_AUTOMATOR_PLAN.md` — ENA_Collector pipeline; `EPISODE_WORKFLOW.md` — automated step table
- **Reality:** `ENA_Collector` filters out articles with `published_at < time() - DAY_IN_SECONDS`. Articles with no publish date (`published_at === 0`) are always accepted. Code comment marks this as temporary/configurable.
- **Fix options:**
  - Document the filter in both docs (what it does, why, when to disable).
  - Or decide if it should become a settings field.

---

## 6. `sort_by_clicks()` has a secondary tiebreaker sort on `added_date`

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `sort_by_clicks()` spec in Sheets adapter
- **Reality:** `sortSpecs` has two entries: clicks DESC (col G), then `added_date` DESC (col H) as a tiebreaker for equal click counts.
- **Fix options:**
  - Update plan doc spec to mention the secondary sort.

---

## 7. Two undocumented AJAX handlers and several undocumented OpenRouter methods

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-ajax.php` spec, `class-ena-openrouter.php` spec
- **Reality:**
  - `ENA_Ajax` also registers `ena_openrouter_usage` and `ena_reset_usage_stats`.
  - `ENA_OpenRouter` also has: `podcast_summary()`, `get_key_info()`, `get_local_stats()`, `reset_local_stats()`, and `record_usage()` (private). Token usage is persisted in `ena_openrouter_usage` wp_option.
- **Fix options:**
  - Add these methods and handlers to the plan doc specs.

---

## 8. `ENA_Podcast` depends on 6 classes — plan doc says "storage adapter only"

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-podcast.php` spec
- **Reality:** Constructor signature is `(ENA_Sheets, ENA_Analytics, ENA_OpenRouter, ENA_Docs, ENA_Logger, ENA_Settings)`.
- **Fix options:**
  - Update plan doc dependency list to match the actual constructor.

---

## 9. `placeholder_page_id`: internal plan doc contradiction + not in defaults or settings form

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-settings.php` spec, Context section
- **Reality:** `placeholder_page_id()` method exists in `ENA_Settings` but is absent from `defaults()` and has no form field. `ENA_Sync` never updates any WP post meta. The plan doc's own Context section says "The plugin never modifies WP page meta" — contradicting the settings spec which says "ENA_Sync updates its news_csv meta."
- **Fix options:**
  - Remove all `placeholder_page_id` references from the plan doc (feature was not implemented).
  - Or implement it: add to defaults, add a settings form field, add the post meta update to ENA_Sync.

---

## 10. `ENA_OPT_LIVE_ARTICLES` missing from Option Keys Reference table

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — Option Keys Reference table
- **Reality:** `ENA_OPT_LIVE_ARTICLES` = `ev_news_live_articles` is defined in `ev-news-automator.php` and written by `ENA_Sync` on every run.
- **Fix options:**
  - Add a row for `ENA_OPT_LIVE_ARTICLES` to the table.

---

## 11. `max_script_articles` setting missing from plan doc defaults listing

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-settings.php` spec defaults comment
- **Reality:** `max_script_articles` defaults to `10`. Exposed in the settings form. Used by `ENA_Podcast` to cap the top-N articles included in the script.
- **Fix options:**
  - Add `max_script_articles` to the defaults listing in the plan doc.

---

## 12. New articles sorted by `published_at` DESC before summarizing — not documented

- **Status:** `[ ]`
- **Affected doc:** `EV_NEWS_AUTOMATOR_PLAN.md` — `class-ena-collector.php` pipeline description
- **Reality:** After deduplication, new articles are sorted newest-first (`published_at` DESC) before OpenRouter calls and before appending to the Sheet.
- **Fix options:**
  - Add this sort step to the collector pipeline description.
