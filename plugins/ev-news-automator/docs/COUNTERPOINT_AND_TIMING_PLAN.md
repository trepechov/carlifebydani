# Implementation Plan — Counterpoint / Fact-check Section + Timing Report

**Branch:** `feature/ena-counterpoint-and-timing`
**Status:** Awaiting review — _do not implement until approved_
**Author:** Claude (Opus 4.8)
**Date:** 2026-06-27

---

## 1. What we have today

The "script generation document" is produced by [`ENA_Podcast::run()`](../includes/class-ena-podcast.php). The current pipeline:

1. **Read rows** from Google Sheets (`title`, `description`, `link`, `clicks`).
2. **Refresh GA4 clicks** and overlay them onto the in-memory rows.
3. **Sort by clicks desc**, take the top `max_script_articles` (default 10).
4. **Generate a summary** per article via [`ENA_OpenRouter::podcast_summary()`](../includes/class-ena-openrouter.php) — 3–5 Bulgarian sentences from the existing title + description.
5. **Write the doc** via [`ENA_Docs::append_sections()`](../includes/class-ena-docs.php), which appends, per article:
   - numbered title (HEADING_2)
   - "Read the original article" hyperlink
   - the AI **summary**
   - the original **description** (gray italic)
   - a separator

Each section is the array `{ bg_title, url, description, summary }`.

Token usage is tracked per call type in `ENA_OpenRouter::record_usage()` and surfaced on the dashboard. **There is no per-step wall-clock timing today.**

---

## 2. Goals (from the request)

1. **Counterpoint / fact-check field** — a new generated section per article that *challenges* the article: argues the opposite, fact-checks the claims, offers a different angle rather than just restating it.
2. **Real sources** — that section must cite where the counter-arguments come from, so it is grounded, not arguing in a vacuum.
3. **Performance report** — measure how heavy script generation is, broken down per step, reported as **average time per step**.

---

## 3. Design

### 3.1 Feature A — Counterpoint section with verifiable sources

**The sourcing problem & decision.** LLMs hallucinate URLs, so we must not ask the model to "invent" sources. OpenRouter supports real web search that returns **`url_citation` annotations** (real `url` + `title` + excerpt) alongside the completion — either via the `:online` model suffix or the `web` plugin (verified 2026-06-27, see §7). **Decision: use OpenRouter web search and read the returned annotations as the source list**, so every cited link is one the search engine actually returned.

**New OpenRouter method** — `counterpoint( string $bg_title, string $description ): array|WP_Error`:

- System prompt (Bulgarian): a critical EV analyst whose job is to *challenge* the article — find the strongest opposing argument, flag dubious or one-sided claims, and back each point with a real, recently-found source. Plain text, no markdown.
- Calls the model **with web search enabled** (configurable, see settings below).
- Returns `{ 'text' => string, 'sources' => [ { 'title' => string, 'url' => string } ] }` where `sources` is built from the response `annotations[].url_citation` (deduped by URL, capped at ~5).
- New usage type `counterpoint` for token tracking + a `counterpoint_calls` counter.

**Plumbing in `ENA_OpenRouter`.** `chat()` currently returns only the message string. We need annotations too, so:
- Add a private `chat_raw( ... ): array|WP_Error` returning `{ content, annotations }` and route both `chat()` and the new `counterpoint()` through it (no behavior change for existing callers).
- Add a `web_search` toggle to the request body: `:online` suffix on the model, OR `plugins: [{ id: 'web', max_results: N }]`. Recommend the **plugin form** so `max_results` is controllable.

**Pipeline change in `ENA_Podcast::run()`** (step 3 loop): after the summary, call `counterpoint()` and attach to the section:
```php
$sections[] = [
    'bg_title'     => $row['title'],
    'url'          => $row['link'],
    'description'  => $row['description'],
    'summary'      => $summary,
    'counterpoint' => $cp['text']    ?? '',   // graceful '' on WP_Error
    'sources'      => $cp['sources'] ?? [],
];
```
On error: log a `counterpoint` step (error) and continue with empty counterpoint — the doc still generates (same fallback philosophy as the existing summary step).

**Rendering in `ENA_Docs::append_sections()`** — after the summary/description, before the separator:
- `"Контрапункт / Fact-check"` sub-heading (HEADING_3).
- the counterpoint text.
- a `"Източници:"` label followed by each source as an italic blue hyperlink line (reusing the existing `req_text_style` link styling already used for "Read the original article"). Skip the whole block cleanly when `counterpoint` is empty.

### 3.2 Feature B — Per-step timing report

**Approach.** A small accumulator that records named durations and averages repeated labels (the per-article steps run N times).

- **New helper `ENA_Timer`** (`includes/class-ena-timer.php`):
  - `start($label)` / `stop($label)` using `microtime(true)`.
  - Accumulates into buckets keyed by label: `{ total_s, count }`.
  - `report(): array` → per label `{ total_s, count, avg_s }`, plus a `wall_total_s`.
- **Instrument `ENA_Podcast::run()`** around: `analytics_fetch`, and per article `summary_gen` and `counterpoint_gen`, and `doc_write` (the `append_sections` call).
- **Isolate API latency**: wrap the HTTP call in `ENA_OpenRouter::chat_raw()` with `microtime(true)` and include `latency_ms` in each `logger->step()` detail, so LLM time is visible vs. orchestration overhead.
- **Persist**: store `report()` in a new status option `ENA_OPT_STATUS_TIMING` (`ena_status_last_timing`) at the end of the run.
- **Display**: new "Last Run Timing" box on the dashboard — a small table of step / runs / avg / total. (Mirrors the existing usage box markup.)

**New constant:** `define( 'ENA_OPT_STATUS_TIMING', 'ena_status_last_timing' );`

### 3.3 New settings

Add to `ENA_Settings::defaults()`:
- `counterpoint_enabled` → `1` (lets you turn the extra LLM call off — it ~doubles cost/time per article).
- `counterpoint_web_search` → `1` (web search adds per-result cost).
- `counterpoint_max_sources` → `5`.

Surface these as checkboxes/number on the settings page; persist in `ENA_Admin::save()` next to `max_script_articles`.

---

## 4. Files touched

| File | Change |
|------|--------|
| `includes/class-ena-openrouter.php` | `chat_raw()`, `counterpoint()`, web-search body, `counterpoint` usage stats, latency capture |
| `includes/class-ena-podcast.php` | call `counterpoint()`, attach to section, wrap steps in `ENA_Timer`, save timing status |
| `includes/class-ena-docs.php` | render counterpoint sub-heading + text + hyperlinked sources |
| `includes/class-ena-timer.php` | **new** — duration accumulator |
| `includes/class-ena-settings.php` | 3 new defaults |
| `ev-news-automator.php` | `require` timer class, `ENA_OPT_STATUS_TIMING` constant |
| `admin/class-ena-admin.php` | persist new settings |
| `admin/views/settings-page.php` | new controls |
| `admin/views/dashboard-page.php` | "Last Run Timing" box + counterpoint call count |
| `docs/EPISODE_WORKFLOW.md` / `README.md` | document the new section + cost note |

No DB migration needed (options auto-default via `array_merge`).

---

## 5. Cost & performance impact (be explicit)

- The counterpoint step is **a second LLM call per article** → roughly **2× the per-article AI cost and time** of script generation.
- Web search is billed by OpenRouter **per result returned** on top of tokens. With `max_results = 5` × 10 articles that is up to 50 searched results per run. The `counterpoint_enabled` / `counterpoint_web_search` toggles exist precisely so this can be dialed down or off.
- The timing report (Feature B) is what will make this cost visible after each run.

---

## 6. Rollout / testing

1. Implement behind `counterpoint_enabled` (default on, but trivially disabled).
2. Manual test via dashboard "Generate Podcast Script" with `max_script_articles = 2` to keep cost low.
3. Verify in the generated Google Doc: counterpoint text present, sources are **clickable real URLs**, layout intact.
4. Verify the dashboard "Last Run Timing" table populates with sane averages.
5. Confirm graceful degradation: temporarily use a bad model id → doc still generates without the counterpoint block, error logged.

---

## 7. Verified assumptions

- **OpenRouter web search** returns `annotations[]` of type `url_citation` (`url`, `title`, `content`, `start_index`, `end_index`); enabled via `:online` suffix or `plugins: [{ id: 'web', max_results }]`. Confirmed 2026-06-27 via OpenRouter docs:
  - [Web Search plugin](https://openrouter.ai/docs/guides/features/plugins/web-search)
  - [Web Search server tool](https://openrouter.ai/docs/guides/features/server-tools/web-search)

## 8. Decisions (confirmed 2026-06-27)

1. **Section heading:** `Другата гледна точка` (HEADING_3) — replaces the "Контрапункт / Fact-check" placeholder used elsewhere in this doc.
2. **Framing:** **Contrarian argument only** — make the strongest opposing case, backed by real sources. Not a claim-by-claim fact-check. The system prompt is written accordingly (no "fact-check the claims" instruction; focus on the counter-case).
3. **Web search:** **On by default** (`counterpoint_web_search → 1`), toggleable in settings. Sources come from `url_citation` annotations.
4. **Timing report:** **Last-run averages only** — store/show the most recent run's per-step avg + total. No rolling historical average. (`ENA_Timer::report()` + single `ENA_OPT_STATUS_TIMING` option, overwritten each run.)

---

## 9. ⚠️ Known limitation — OpenRouter free-tier limits (discovered in testing)

During the first live runs (model set to `openai/gpt-oss-120b:free`) the counterpoint section
came back empty and summaries were duplicating the description. Reading the site's run transcript
showed **two distinct failures**, both rooted in OpenRouter's three free-tier limits:

| # | Limit | What hits it | Symptom observed |
|---|-------|--------------|------------------|
| 1 | **20 requests / minute** — account-wide across **all** `:free` models | A script run fires `max_script_articles × 2` calls back-to-back (~30 in a burst) | `podcast_summary` → **HTTP 429**; code falls back to `$row['description']`, so the description gets copied into the summary slot ([class-ena-podcast.php:67](../includes/class-ena-podcast.php#L67)) |
| 2 | **50 requests / day** (raised to **1,000/day** once ≥ $10 of credits is ever purchased) | Repeated dev re-runs exhaust the daily quota | `HTTP 429` once the daily cap is reached |
| 3 | **Web search is a paid feature** (billed per result) | `counterpoint()` attaches the `web` plugin → request is no longer free | `counterpoint` → **HTTP 402 Payment Required** on every call; section skipped ([class-ena-podcast.php:69](../includes/class-ena-podcast.php#L69)) |

**Key facts established:**
- The 429 is **not** specific to `gpt-oss-120b` — it is account-wide, so switching to another
  `:free` model (e.g. `nvidia/nemotron-3-super-120b-a12b:free`) draws from the **same pool** and does not help.
- **No `:free` model can ever run the counterpoint with sources** — web search is paid regardless of model (402).
- General web search has **no native date filter** (only xAI models expose `x_search_filter` `from_date`/`to_date`),
  so source **recency is enforced via prompt** (current date injected + "use newest data, avoid facts older than ~1 year")
  plus a custom `search_prompt`. This was added after a counterpoint cited 2023/2024 Lucid financials. Best-effort, not guaranteed.

**Production vs. dev:** normal production volume (one daily collection of *new* articles + ~weekly script run)
greatly relieves the **daily** cap, and collection self-heals (429'd articles are skipped and retried next run,
[class-ena-collector.php:77](../includes/class-ena-collector.php#L77)). It does **not** fix the **per-minute burst**
on the ~30-call script run, and does **nothing** for the counterpoint 402.

**Resolution:**
1. **Purchase $10 of OpenRouter credits (once, non-expiring)** — raises daily cap 50 → 1,000, unblocks web search
   (counterpoint 402 resolved), and allows a cheap paid model to sidestep the 20/min throttle entirely. **Required for the
   counterpoint feature to function at all.**
2. *(Optional, free)* Add pacing/backoff between per-article calls to stay under 20/min — fixes the summary 429 fallbacks
   only; the counterpoint still needs credits. _Not yet implemented._
