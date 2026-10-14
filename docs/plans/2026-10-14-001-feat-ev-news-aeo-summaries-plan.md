# feat: AEO (answer-first) titles & summaries in EV News Automator

**Date:** 2026-10-14 · **Plugin:** ev-news-automator 1.2.5 → 1.3.0 · **Status:** planned

## Goal

Change how `ENA_OpenRouter::analyze()` writes the Bulgarian **title** and **summary** for collected articles so they follow answer-engine (AEO) rules: answer-first, explicit entities, key number/date in sentence 1, self-contained sentences. The AEO rules live **as a file in the project**, not hard-coded strings. The same AEO structure (one exact question plus a well-structured answer) also replaces the podcast script's "Резюме" (Change 6). Everything else in the pipeline (on_topic, tags, region, sheet schema, sync, votes) must behave exactly as today.

## Evidence (A/B run, 2026-10-14)

`tests/test-05-aeo-prompt-ab.php` on sheet tab `20.10.2026`, 100 articles, model `openai/gpt-4o-mini`. Report: `plugins/ev-news-automator/tests/output/test-05-20261014-123855.md`. Reviewed by Dimitar: wording approved.

| Variant | Title median | Summary median / p90 / max | Sentence 1 median |
|---|---|---|---|
| current (sheet today) | 59 | 216 / 254 / 286 | 114 |
| baseline (prod prompt, body input) | 55 | 252 / 320 / 369 | 109 |
| **aeo-answer-first** | 62 | **282 / 376 / 474** | 106 |
| aeo-structured | 62 | 142 / 177 / 221 (+ key_facts ~242) | 137 |

Two caveats from the run:

1. **Input mismatch.** The test fed the **article body (first 1500 chars)**. Production feeds `analyze()` only the **RSS excerpt (≤500 chars)**, and HTML-scraped sources get an **empty excerpt** (title only), per `class-ena-scraper.php:65,109`. What was reviewed is not what production would produce.
2. **on_topic drift.** 7/100 articles flipped to `on_topic: true` under the AEO variants (Android Auto, Renault small cars, Hyundai Bayon, Fiat Multiplay…). The cause was the test prompts, which shortened the on_topic definition and dropped the whitelist/blacklist injection. This was not the AEO style itself.

## Evidence (hand-written reference set, 2026-10-14)

`plugins/ev-news-automator/tests/output/aeo-rewrite-2026-10-14.md` holds the top 5 on-topic stories of episodes EV166–EV169 (20 stories), rewritten by hand from the source article bodies. They follow the answer-first rules plus the audience/market rules below. Findings:

- **The live `region` column is unreliable.** The Xpeng L03 Australian launch is tagged `CN`. The EU T&E truck-cost study and the US-priced BMW iX4 are tagged `GLOBAL`. The Australian Mazda CX-6E launch and Alpitronic's Europe-first launch are empty. The market/region consistency rule (Change 1) addresses this.
- **Live descriptions can carry facts that aren't in the article.** The EV167 Nio Firefly description says Nio exported 181 cars in August, which isn't in the linked article. This supports Decision 3 = A (body input).
- **The live on_topic flag has false negatives too.** The EV169 Citroën 2CV van is an EV concept (URL `…/citroen-2cv-van-ev-concept-fourgonette/`) but is flagged `off_topic=yes`. The old prompt is therefore a stability baseline, not ground truth (see test-06).
- **Body fetch is viable for the main sources.** InsideEVs blocked local fetches (502), but all 15/15 InsideEVs articles were fetched as body from the WP server in the A/B run. 98/100 bodies were fetched overall.
- **The reference set shows the content rules, not the length budget.** Its summaries run to 3 sentences and 300–450 chars, and some titles reach ~85 chars. Under the budget below, the market and Europe-relevance fact has to fit in sentence 2.

## Where the text is published and how much fits

Measured live on carlifebydani.com (2026-10-14).

| Surface | Template | Constraint | Fits | Today |
|---|---|---|---|---|
| Feed card title (`/ev-news-feed/`) | `theme/template-parts/ev-news-feed/card.php:82` | `line-clamp-2` | ~79 chars desktop, ~76 mobile | 17–28/100 clipped |
| Feed card description | `card.php:87` | `line-clamp-3`, 14px | **~156 chars desktop, ~118 mobile** | 98–100/100 clipped (ellipsis) |
| Episode post "Виж още" description | `theme/template-parts/single/card-article-external.php:35` | `max-h-60` (240px), 16px/24px | ~584 chars desktop, **~196 mobile** (177px column) | **already silently clipped on mobile**: median desc 233, no ellipsis |
| Podcast Google Doc "Описание:" | `class-ena-docs.php:193` | none | unlimited | — |
| `podcast_summary()` input | `class-ena-openrouter.php:177` | none | unlimited | — |
| GA4 votes/clicks | `class-ena-analytics.php` | keyed by **URL**, not title | n/a | title changes are safe |

What this means:

- On the feed, **only sentence 1 is visible**. The answer-first rule is the right fix, but sentence 1 must fit ~118 chars on mobile. Today 67/100 AEO first sentences fit in 118 and 91/100 fit in 156.
- The AEO answer-first summary is about 30% longer than today's. Without a cap it worsens the existing mobile clipping on episode posts (only 6/100 fit in 196, 48/100 fit in 280).
- → **Soft length targets, anchored to today's sheet** (current median / p90: title 59 / 77, summary 216 / 254, sentence 1 114). The prompt asks for **title ~60, preferably ≤ 75**; **summary ~220, preferably ≤ 250**; **sentence 1 preferably ≤ 120**. These are guidance, not hard caps: the model may go over when that is the only way to keep an essential fact, but it must never pad to reach them. Nothing is truncated in code. The goal is that readers and agents get the whole text without hitting the ellipsis, as often as possible.
- → **Theme fix needed regardless of AEO:** the episode-post description is clipped on mobile today.

## Decisions

1. **Variant: `aeo-answer-first`.** It is a drop-in: same JSON keys, same sheet columns A–L, no template changes.
   **`aeo-structured` (key_facts / why_it_matters) is dropped, not deferred** (Dimitar, 2026-10-14). The sheet structure stays as it is, and no bullet-formatted text goes into the sheet. The description must read as a genuine short paragraph that carries the essence of the article. A live mockup on EV167 confirmed key facts would only lengthen the «Виж още» panel without adding much.
2. **Keep on_topic / tags / region instructions verbatim.** Only the `title` and `summary` field rules change, so on_topic agreement with today should be ~100%. The new summary rule "the market named must match `region`" lives inside the summary rule. The region rule text itself is unchanged.
3. **Input text (needs your call):**
   - **A (recommended):** fetch the article body in the collector (first 1500 chars, same as the reviewed test). Fall back to the RSS excerpt if extraction fails or is empty. This matches what you reviewed and fixes HTML sources that currently get title-only input. Cost: one extra HTTP fetch per new article in the daily run (~20–60 per run). `extract_body()` already exists and is used by `add_manual()`.
   - **B:** keep the excerpt. There is no extra fetch, but results will be thinner than reviewed, and AEO's "include the number/date" rule risks the model inventing figures it can't see.

## Changes

### 1. AEO guidelines as a project file (new)

`plugins/ev-news-automator/prompts/analysis-system.md` is the full analyze() system prompt as editable text, with placeholders:

```
{{AEO_RULES}}   {{DESCRIPTOR_LINE}}   {{WHITELIST_LINE}}   {{BLACKLIST_LINE}}
```

`{{AEO_RULES}}` is replaced with `prompts/aeo-rules.md`, the shared AEO rules also used by the «Резюме» prompt (Change 6).

Content: the current prompt's JSON contract plus the on_topic/tags/region rules (verbatim from `build_analysis_system_prompt()`), with the **title and summary rules replaced** by the AEO rules:

- title: a Bulgarian headline stating the news as a fact (who + what), brand/model included, no clickbait. **Aim for about 60 characters and preferably no more than 75**; go longer only if a shorter title would drop the brand/model or the core fact.
- summary: **2–3 Bulgarian sentences written as one natural, flowing paragraph**, **aim for about 220 characters and preferably no more than 250** (today's sheet average). Going over is acceptable when needed to keep an essential fact, but prefer cutting secondary detail and never pad to reach the target. No lists, bullets, labels or line breaks. Sentence 1 (**preferably ≤ 120 chars**, the part visible on mobile feed cards) answers "what happened?" on its own: it names the company/model explicitly (never "it"/"the company") and gives the single most important number or date. Sentences 2–3 add the most essential supporting facts and **don't repeat** sentence 1 or the title. Use **only facts present in the text**; no invented figures, no opinions.
- **Every sentence names its subject**, not only sentence 1, because sentence 2 also gets quoted out of context.
- **Absolute dates only.** Never "this week", "yesterday" or "on Monday". Relative dates are resolved from the publication date, which is now passed in (Change 2).
- **Attribution.** When the fact is a company claim or a report, say so ("според Tesla", "съобщи Reuters").

**Audience and market rules** (readers are in Bulgaria; Bulgarian and European news matters most, and distant markets matter less unless they affect Europe):

- If the news is tied to one market (price, launch, subsidy, recall, sales figure, regulation), name that market **in the title or sentence 1**. Those are the only parts visible on the feed card, so the reader learns there whether the story applies to them.
- For news from outside Europe, sentence 2 states whether it applies to Europe/Bulgaria **only if the source says so**. Never guess or imply European availability.
- Global news (company results, technology, worldwide reveals) gets no market label.
- Keep prices in the original currency and don't convert. Use compact forms ("41 900 AUD", "$66 200") to save characters.
- The market named in the text must match the `region` field.

`plugins/ev-news-automator/docs/AEO_CONTENT_GUIDELINES.md` is the human-readable rationale: the rules above, why (answer engines quote sentences out of context, and the feed shows only sentence 1), the measured display budgets from this plan, and the A/B evidence link. The prompt file is the operative copy; the doc explains it.

Both live inside the plugin directory, so they ship with the plugin zip/rsync.

### 2. `includes/class-ena-openrouter.php`

- `build_analysis_system_prompt()` loads `ENA_PLUGIN_DIR . 'prompts/analysis-system.md'` and substitutes the three placeholders with the existing settings-derived lines (same text as today).
- If the file is missing or unreadable, it falls back to the current hard-coded prompt (kept as a private const) and logs a warning. A bad deploy then cannot stop collection.
- `analyze()` gets a post-parse **format** safety net, not a length one: strip any line breaks or leading bullet characters, so the summary is always one plain paragraph. **Text is never truncated**, because the length targets are soft. If `summary` > 300 chars or `title` > 90 chars, it's kept as is and logged as `over_length`, so prompt drift shows up in the run transcript.
- The return shape is unchanged: `bg_title, bg_summary, on_topic, topic_reason, tags, region`.
- `analyze()` gets an optional third parameter `string $pub_date = ''` (Y-m-d). It's added to the user message as `Publication date: …` (or `unknown`) so the model can resolve "on Monday" into an absolute date. Existing callers keep working. The collector passes `gmdate( 'Y-m-d', $article['published_at'] )` when `published_at > 0`. HTML sources have `0`, so they pass `''`.

### 3. `includes/class-ena-collector.php` (only if Decision 3 = A)

- In `run()` at line 82, before `analyze()`: `$text = $this->scraper->extract_body( $article['url'] )`, capped at 1500 chars. Fall back to `$article['excerpt']` on WP_Error or empty result.
- `add_manual()` is already body-based. Align its cap with the 1500 used here (currently `MANUAL_BODY_CAP`).
- Log the source used per article (`body` / `excerpt`) in the existing `openrouter_call` step line.

### 4. Theme: `template-parts/single/card-article-external.php:35`

`peer-has-[:checked]:max-h-60` → `peer-has-[:checked]:max-h-[40rem]` (this only bounds the open animation). It fixes the existing mobile clipping and gives headroom for the occasional over-target summary (300 chars ≈ 15 lines ≈ 360px at the 177px mobile column; 40rem = 640px). This is a theme deploy, independent of the plugin.

### 5. Tests

- `tests/test-05-aeo-prompt-ab.php`: add an `--input=excerpt|body` mode. Excerpt mode re-pulls articles from the RSS sources (the sheet stores no excerpts).
- **New `tests/test-06-analyze-contract.php`**: a regression gate that calls the real `analyze()` on N articles and asserts:
  - JSON parses; all 6 keys present with the right types (`tags` array ≤ 8, `region` matches `^[A-Z]{2,6}(,[A-Z]{2,6}){0,2}$|^$`)
  - **Format (hard, fails the test):** summary has 2–3 sentences and contains no `\n`, `•` or `- ` list markers
  - **Length (soft, reported, never fails):** median / p90 for title, summary and sentence 1, printed next to today's sheet numbers (59/77, 216/254, 114). Expected: summary median ≈ 220–240, and at least 80% of summaries ≤ 250, titles ≤ 75 and sentence 1 ≤ 120. If it comes out well above that, tighten the wording in `prompts/aeo-rules.md` and re-run; don't add code caps.
  - **on_topic agreement with the frozen old prompt ≥ 97%**, with every disagreement listed for manual review
  - **on_topic agreement measures stability, not correctness.** The live flag has known false negatives (Citroën 2CV EV concept). Disagreements are reviewed by hand and aren't automatically counted as regressions.
  - the summary contains no numbers that don't appear in the source text (cheap hallucination check for invented figures). The publication date's day/month/year count as source, because resolved dates legitimately add them. Hits are listed for review, not failed: a unit conversion such as miles→km also trips it.
  - **market check:** when `region` is a single market code, the title or sentence 1 must contain the matching Bulgarian market word (`AU`→Австрал, `US`→САЩ, `CN`→Кита, `DE`→Герман, `EU`→Европ/ЕС, …). Misses are listed for review.
- Fix the stale `tests/test-01-scraper-openrouter.php`: it calls `summarize()`, which was renamed to `analyze()`, so it currently fatals.

### 6. Podcast script "Резюме" → AEO Q&A brief

**Why.** The podcast Google Doc shows each story's sheet title and description verbatim (`class-ena-podcast.php:100-105`), followed by a "Резюме:" paragraph from `podcast_summary()` (`class-ena-openrouter.php:177`). That prompt asks for **8–10 sentences from only the title plus the 2–3 sentence description**. It has nothing more to say, so it pads or invents. The hosts asked for the same AEO structure here: one exact question and a well-structured answer.

**`podcast_summary()` → `podcast_summary( $bg_title, $description, $body_text = '', $pub_date = '' )`.**
- The prompt returns JSON `{question, answer, facts[], relevance}`, all in Bulgarian:
  - `question`: the one question a Bulgarian reader would type or ask an AI. It names the brand/model and the market when market-specific, and ends with "?".
  - `answer`: 1–2 standalone sentences with the explicit entity, the key number, an absolute date and attribution.
  - `facts`: 3–5 single-sentence facts, each naming its subject.
  - `relevance`: what the story means for Europe/Bulgaria, **only if the source says so**, otherwise "".
  - Same audience/market rules as Change 1, and the same "only facts from the source" rule.
- PHP formats the brief as labelled lines: `Въпрос: …?` / `Отговор: …` / `• fact` / `За Европа и България: …` (omitted when empty). A missing question or answer returns `WP_Error('openrouter_parse')`, which goes down the existing error-placeholder path.
- Source text is the scraped body, capped at 6000 chars. If the fetch fails, it falls back to title + description, and the prompt is told the body is unavailable.

**Prompt lives in the project, like Change 1.** The system prompt is `plugins/ev-news-automator/prompts/podcast-summary.md`, loaded by `podcast_summary()` with the same fallback-to-built-in-prompt and warning log as `build_analysis_system_prompt()`. The AEO rules it shares with the sheet prompt (explicit subject in every sentence, absolute dates, attribution, the audience/market rules, only facts from the source) live **once** in `prompts/aeo-rules.md`. Both prompt files pull it in through an `{{AEO_RULES}}` placeholder, so the sheet description and the «Резюме» can never drift apart. `docs/AEO_CONTENT_GUIDELINES.md` documents both outputs.

**`class-ena-podcast.php`**: inject `ENA_Scraper` (constructor plus `class-ena-plugin.php:43`). Per article: `extract_body( $row['link'] )`, then pass the body and `$row['pub_date']` to `podcast_summary()`.

**`class-ena-docs.php:207-217`**: render `Резюме:` on its own line, then the brief. Bold the whole `Въпрос:` line and the `Отговор:` / `За Европа и България:` labels. The label strings are shared with `podcast_summary()`, so keep them in sync. Failure placeholders have none of these prefixes and render as plain text.

**Cost**: one extra page fetch per script article (`max_script_articles`, default 10) in the podcast run. No length limit applies, because the doc doesn't clamp text.

**Test**: extend `test-03-google-docs.php` to generate briefs for 3 rows of a tab. It asserts that the `Въпрос:` line ends with "?" and that there are 3–5 `•` facts, then writes them to the test doc so the bolding can be checked by eye.

### 7. Version + docs

- Bump `ENA_VERSION` to 1.3.0 (plugin header + const).
- Add a one-paragraph pointer in the plugin README to `prompts/` and `docs/AEO_CONTENT_GUIDELINES.md`.

## Must not break: checklist

| Flow | Why it's safe / how verified |
|---|---|
| Sheet schema A–L, `build_row()` | analyze() return keys unchanged → test-06 contract |
| Off-topic flag (col J) | on_topic rules verbatim + dictionaries injected → test-06 agreement ≥ 97% |
| Tags (K) / region (L) | rules verbatim → test-06 format asserts |
| Sync → `ev_news_live_articles` → feed cards | reads title/description strings only; soft length targets match today's lengths → test-06 length report |
| Episode post `news_csv` cards | description longer → theme fix #4 |
| GA4 clicks/votes, upvote sort | keyed by URL → unaffected |
| Podcast Doc "Описание:" | consumes the sheet description as free text; no length limit |
| Podcast Doc "Резюме:" (Change 6) | same shared `aeo-rules.md` as the sheet description; new Q&A format; failure placeholders unchanged and render plain; 429/401 batch stop path unchanged → test-03 |
| 429/401 batch stop | `chat()` untouched |
| Manual "Add article" | same `analyze()` path → covered by test-06 |
| Already-collected rows | not rewritten; only new rows use the new prompt (no backfill) |

## Rollout

1. Local: implement steps 1–3, 5 and 6. Re-add the audience/market rules to `tests/prompts/aeo-answer-first.txt`, re-run test-05, and compare against the reference set. Then run test-06 on the `20.10.2026` tab (body mode) and on live RSS (excerpt mode). Review the on_topic disagreement list and the length report.
2. Local: run one real collection into a **throwaway tab** (temporarily point `spreadsheet_id` at a copy of the sheet). Check the feed renders at 390px and 1400px.
3. Deploy the theme fix (#4), then the plugin 1.3.0.
4. Watch the first two production collection runs: transcript log for `over_length` entries and body-fetch fallbacks; Sheet column J for off_topic rate vs the previous week.

**Rollback:** revert the plugin to 1.2.5, or simply restore the old prompt text in `prompts/analysis-system.md`, since it is just a file. Rows already written stay as they are.

## Open questions

1. Decision 3: body fetch (A) or excerpt only (B)?
2. Backfill the current `20.10.2026` tab with the new titles/summaries before recording, or only new rows from now on? (Not planned by default.)
3. ~~Phase 2 `aeo-structured` (key_facts) on episode posts~~: **dropped** (see Decision 1). Change 6's facts live only in the podcast Google Doc and never touch the sheet.
4. **Ranking by market.** The sheet is sorted upvote → pub_date → added_date (`class-ena-sheets.php:380`), so distant-market stories can outrank European ones. In EV168 the Australian Supercharger vandalism story is #2 and the EU truck-cost study is #3. In EV169 an Australian Mazda video review is #3 and the Smart #2 European launch is #4. The market rules make the market visible but don't reorder anything. A region-weighted sort is out of scope here.
5. **Unit conversion.** Is miles→km allowed for Bulgarian readers (e.g. "428 мили (около 689 км)")? Currency conversion stays forbidden.
