# Off-topic flag + Article tags + Region code — planning notes

Status: **IMPLEMENTED in v1.2.0** (2026-07-18/19). Written 2026-07-17, expanded 2026-07-18, then
built. "Country" was renamed **region** during implementation. All the work below shipped as a single
**1.2.0** — the intermediate 1.2.1/1.2.2 bumps made while iterating were never deployed and were
collapsed back into 1.2.0.

**What shipped in 1.2.0:**
- `ENA_OpenRouter::analyze()` (replaces `summarize()`) returns `{title, summary, on_topic,
  topic_reason, tags, region}` in ONE OpenRouter call.
- `ENA_Collector::build_row()` — shared row builder used by both `run()` (automatic) and
  `add_manual()` (manual submissions), so every row carries the same signals.
- Sheets extended to **12 cols A–L**: J=off_topic ("yes"/"no", yes = NOT about EVs), K=tags (comma-separated BG), L=region
  (ISO alpha-2, e.g. "US"/"US,EU"/"").
- Off-topic is **dictionary-steered**: editable whitelist/blacklist + Bulgarian tag-descriptor vocab
  in `ENA_Settings`, surfaced in the admin Settings page under "Article Analysis".
- **Tags are Bulgarian** (brand/model kept as proper nouns); default descriptor list expanded to ~20
  event types derived from the live feed (deliveries, charging, partnership, subsidy, tax, rumor,
  regulation, autonomy, …).
- **`on_topic` prompt broadened** after the first real false positive (a Rivian R2 delivery-cancel
  story flagged OFF-TOPIC): brand/model business news — deliveries, production, sales, recalls,
  incidents, pricing, legal/business disputes — is on-topic even when not about the tech; "when
  unsure, prefer true". A `topic_reason` field is emitted and logged per article for auditing.
- **Script doc integration** (`ENA_Docs` + `ENA_Podcast`): the generated podcast-script Google Doc
  now shows, between the article title/link and the description, a bold `Тема:` line (on/off-topic,
  off-topic in red), a `Тагове:` line, and a `Регион:` line — each rendered only when present.

Observation-only — nothing on the **frontend** reads the new columns yet. The notes below are the
original design, kept for rationale.

## Background

`ENA_Collector` used to have a keyword-based off-topic detector (`TOPIC_KEYWORDS` + `is_on_topic()`,
added in commit `46e482b`). It flagged articles that didn't match any EV/clean-energy keyword by
writing `downvote = 1`. That was removed in commit `e6c459c` (2026-07-09) when real user
upvote/downvote voting shipped, because it collided with the `downvote` column: one column can't mean
both "a real visitor disliked this" and "our keyword filter didn't recognize this as EV content."

Features to bring back / add, on a **new, dedicated column each** — never reuse `upvote`/`downvote`:

1. **Off-topic flag** — successor to the removed keyword filter. LLM-judged, but **steered by
   positive/negative dictionaries** (see §1) so the editor can tune what counts as on-topic without a
   code change.
2. **Tags** — a small pack per article: **brand + model + event descriptors** (premiere, promo,
   recall, accident, sales, price cut, review, …). For future use (filtering, browsing). See §2.
3. **Country code** — an optional geographic marker for what region the article is *about* (US
   regulations, China market, EU policy, …). See §3.

No existing tag/brand/model/country taxonomy exists anywhere in this codebase or the theme (checked:
no such conventions in the plugin or `theme/template-parts/ev-news-feed/`) — this is all new plumbing.

## Rollout stance: observe first, decide later

This is an **observation phase**, planned to run for **a couple of weeks** so the dictionaries and
prompt can be fine-tuned against real output. During this phase, all three signals are **written to
their own Sheets columns only** — nothing on the frontend reads or acts on them, and no row is
auto-hidden or filtered. After the data has been reviewed, we decide how to use it (frontend filter,
auto-hide off-topic, grouping by country, etc.). Everything below is designed to that stance:
capture the data cleanly now, build no consumer yet.

## Key architectural decision: one reusable "analyze" layer, one OpenRouter call

All four outputs — `bg_title` + `bg_summary` (existing), `on_topic`, `tags`, `country` — should be
produced by a **single reusable method** that both the automatic collector and the manual-add path
call. Today those two paths each call `ENA_OpenRouter::summarize()` separately and then build a row
array by hand (`ENA_Collector::run()` ~line 90/115, `ENA_Collector::add_manual()` ~line 186/191).
The new plan:

- Rename/extend `ENA_OpenRouter::summarize()` → **`analyze()`** (or add `analyze()` alongside): one
  OpenRouter call whose JSON response carries all fields. **Not** separate calls per signal.
- Add a shared **row-builder helper** in `ENA_Collector` (e.g. `build_row( array $analysis, array
  $meta )`) so `run()` and `add_manual()` produce identical row shapes and both automatically pick up
  the new columns. This is the "reusable method … in place also for the manual adding" the user asked
  for — the analysis + row assembly lives in one place, not duplicated.

Why one call, not several:

- OpenRouter free tier is capped at 20 req/min; the collector already paces itself at
  `REQUEST_DELAY_SECONDS = 4` between calls (`class-ena-collector.php`). Extra calls per article
  multiply run time and 429 risk (`ENA_OpenRouter::is_fatal_batch_error()` already stops the batch on
  429/401 — more calls = more chances to trip it).
- The model already has the title + excerpt/body loaded for the summarize call — classifying
  on-topic and extracting tags/country from that same context is essentially free extra output.

## 1. Off-topic flag — dictionary-driven

The judgment stays semantic (LLM), but is **steered by two editable dictionaries** rather than a hard
keyword match:

- **Positive / whitelist** — topics we actively want, biasing borderline calls toward on-topic.
  Seed examples from discussion: new **battery technologies**, novel **energy generation/storage**,
  new **electric motors** that could go into EVs, charging infrastructure.
- **Negative / blacklist** — topics we don't want, biasing toward off-topic. Seed examples: ICE-only
  content (e.g. "new model with a **V8 engine**"), **scooters**/micro-mobility we don't cover.

Design:

- Store both lists as editable settings (`ENA_Settings`), e.g. `offtopic_whitelist` /
  `offtopic_blacklist`, newline- or comma-separated. Default-seed them with the examples above so the
  feature works out of the box, but keep them tunable from the admin UI without a deploy.
- Inject both lists into the `analyze()` prompt as *guidance* ("treat these as strongly on/off topic;
  use judgment for everything else"), not as a literal filter — so the model still catches an
  on-topic article that uses none of the whitelist words, and rejects an off-topic one that happens
  to mention a whitelist word in passing (the exact failure modes a pure keyword list had).
- Response field: `"on_topic": true|false` (plus optionally `"topic_reason": "..."` for debugging in
  logs — cheap, and makes false positives auditable).
- New Sheets column, **flag-only to start**: keep off-topic rows in the sheet, don't auto-hide on the
  frontend until the flag's real-run accuracy is trusted. A false positive would silently drop a
  genuine EV article.

## 2. Tags — brand + model + event descriptors (in Bulgarian)

- Response field: `"tags": ["Tesla", "Model Y", "премиера", ...]`.
- The "pack" per the user: **brand** (e.g. Tesla, BYD), **model** (e.g. Model Y, Seal), and one or
  more **event descriptors**.
- **Language: tags in Bulgarian** — same as the summary. Brand and model stay as their proper-noun
  spelling (Tesla, Model Y — not transliterated), but the **event descriptors are Bulgarian words**.
  So the fixed descriptor vocabulary is defined in Bulgarian: `премиера` (new-model launch), `промо`,
  `продажби`, `инцидент`, `рекол`, `намаление` (price cut), `ревю`. Keep brand/model free-form (new
  models ship constantly — no rigid enum); keep the descriptor list short and fixed so it stays
  consistent and filterable. Put the vocabulary in the prompt and, ideally, in a settings-editable
  list so it can grow. Store now, don't build a consumer yet.
- New Sheets column, stored as a single comma-separated string (Sheets cells are scalar — join on
  write in `ENA_Collector`, split on read in `ENA_Sheets`). Keep the delimiter filter-friendly for a
  possible future frontend filter UI.

## 3. Country code (optional)

- Marks the region the article is *about*, not where it was published: US regulations → US, China
  market → CN, EU policy → EU.
- Response field: `"country": "US"` (or `""` when not region-specific — this is optional/blank-ok).
- **Storage format:** recommend **ISO 3166-1 alpha-2 codes** (`US`, `CN`, `DE`, plus `EU` / `GLOBAL`
  as pseudo-codes) rather than storing the flag emoji directly — codes are easy to filter/group and
  the flag emoji (🇺🇸) is trivially derived from the alpha-2 code on the frontend for display. If an
  article spans regions, allow a short comma-separated list (`US,EU`). (The user said "probably
  Unicode" — we can render the Unicode flag from the stored code; storing the code keeps it queryable.)
- New Sheets column. Blank is a valid value — no backward-compat defaulting needed beyond empty.

## Schema / plumbing impact

`class-ena-sheets.php` currently hardcodes a **9-column A–I** layout (`COLUMNS` now =
`title, description, link, author, upvote, downvote, clicks, added_date, pub_date`; `pub_date` is
col I / index 8). Adding the three new columns → **A–L (12 columns)**. Proposed order:

- **J = on_topic**, **K = tags**, **L = country**

All of these move together when columns change:

- `COLUMNS` const (line 29) — append `on_topic`, `tags`, `country`
- `array_pad( $row, 9, '' )` / `array_slice( $padded, 0, 9 )` in `read_data_rows()` (lines 62–63) → 12
- `"{$sheet}!A:I"` ranges in `append_rows()` (line 119) and `read_sheet_rows()` (line 444) → `A:L`
- `endColumnIndex: 9` in `sort_by_upvotes()`'s `sortRange` (line 282) → 12, so the new columns travel
  with each row when the sheet is re-sorted instead of being left behind
- the per-column value-mapping closure in `append_rows()` (lines 124–129) — new keys default to `''`
  (they're not auto-generated like `added_date`/`clicks`)

Backward compat for old tabs that predate these columns: missing `on_topic` → default (decide: empty
vs. treat-as-on-topic so old rows aren't accidentally flagged), `tags`/`country` → empty. Follow the
existing pattern in `read_data_rows()` (lines 64–68) where `upvote`/`downvote`/`clicks`/`added_date`
are defaulted.

## Integration points (all change in tandem)

- **Prompt + response parsing:** `ENA_OpenRouter::summarize()` → `analyze()` — extend JSON schema to
  `{title, summary, on_topic, tags, country}`, inject the whitelist/blacklist + descriptor vocab.
- **Reusable row assembly:** new `ENA_Collector::build_row()` used by both `run()` (~line 115) and
  `add_manual()` (~line 191), so the manual path gets on_topic/tags/country for free.
- **Column plumbing:** `ENA_Sheets` (see §Schema above).
- **Settings/dictionaries:** `ENA_Settings` — whitelist, blacklist, event-descriptor vocab, all
  admin-editable and default-seeded.

## Open questions before implementing

- Old-tab `on_topic` default: empty vs. treat-as-on-topic (leaning: don't flag rows we never judged).
- Whether off-topic articles should eventually be excluded from `ENA_Sync`'s frontend snapshot, or
  stay visible-but-flagged indefinitely.
- Whether `ENA_Docs` podcast-script generator / `ENA_Podcast` should skip off-topic rows.
- Country: single value vs. comma-separated list; whether to render flag emoji on the frontend card
  (`theme/template-parts/ev-news-feed/card.php`).
- Whether to log `topic_reason` for auditability (cheap, recommended during the trust-building phase).
