# AEO content guidelines — EV News Automator

Since 1.3.0, the plugin writes two kinds of text with answer-engine (AEO) rules:

| Output | Where it appears | Prompt file |
|---|---|---|
| **Title + description** (sheet columns A/B) | `/ev-news-feed/` cards, the «Виж още» panel on EV News episode posts, the podcast Doc «Описание» | `prompts/analysis-system.md` |
| **«Резюме»** (question + answer paragraph) | Podcast Google Doc only, read aloud by the hosts | `prompts/podcast-summary.md` |

Both share the rules in **`prompts/aeo-rules.md`**, pulled in through the `{{AEO_RULES}}` placeholder. To change a rule, edit that file: the sheet description and the «Резюме» stay consistent automatically. Text inside `<!-- … -->` in a prompt file is a note for humans and is stripped before the prompt is sent. If a prompt file is missing or empty, the plugin uses a built-in fallback and logs a `prompt_file` warning to the run transcript. For `analyze()` the fallback is the exact pre-1.3.0 prompt, so collection never stops.

## Why these rules

Answer engines (Google AI Overviews, ChatGPT, Perplexity) quote **single sentences out of context**. A sentence like "Тя ще струва 40 000 евро от догодина" is useless on its own. "Tesla Model Y Standard ще струва 39 990 евро в Германия от януари 2027 г." can be quoted as-is. Hence:

- **Answer first.** Sentence 1 answers "what happened?" on its own, with the key number or date.
- **Explicit subject in every sentence.** Never "тя", "компанията", "автомобилът".
- **Only facts from the source.** No invented figures. The model reads the scraped article body (first 1500 chars) for this reason. The RSS excerpt is only a fallback.
- **Absolute dates.** "Вчера" becomes a date, resolved from the publication date passed with each article.
- **Attribution.** "според Tesla", "съобщи Reuters".
- **Units.** Prices stay in the original currency. Imperial distances get the metric value in brackets ("428 мили (около 689 км)").
- **Market first, for Bulgarian readers.** A market-specific story names the market in the title or sentence 1. Relevance to Europe/Bulgaria is stated only when the source says so.
- **A paragraph, not a list.** The description is 2–3 flowing sentences. Bullet-style key facts were tried and dropped (2026-10-14): they don't belong in the sheet, and they only lengthened the «Виж още» panel.

## Length targets (soft)

Measured on carlifebydani.com, 2026-10-14:

| Surface | Visible without expanding |
|---|---|
| Feed card title (`line-clamp-2`) | ~79 chars desktop, ~76 mobile |
| Feed card description (`line-clamp-3`) | ~156 chars desktop, **~118 mobile** |
| Episode post «Виж още» | whole description (cap raised to `max-h-[40rem]` in the theme) |

Targets are anchored to the sheet's lengths before AEO (median / p90: title 59 / 77, description 216 / 254, sentence 1 114):

- **Title:** about 60 characters, preferably ≤ 75
- **Description:** about 220 characters, preferably ≤ 250, in 2–3 sentences
- **Sentence 1:** preferably ≤ 120. It's the only part visible on mobile feed cards.

These are **soft**. The model may go over to keep an essential fact, but it never pads. Code never truncates. A title over 90 or a description over 300 characters is kept and logged as `over_length` in the run transcript. If those entries become frequent, tighten the wording in the prompt files instead of adding code caps.

## What must not change

`analyze()` returns the same six keys (`bg_title, bg_summary, on_topic, topic_reason, tags, region`). The on_topic / tags / region instructions are word for word the same as before 1.3.0, and the sheet columns A–L are unchanged. GA4 votes and clicks are keyed by article URL, so wording changes never affect them.

## Testing a prompt change

```bash
# Contract + regression gate. Fails on contract/format breaks; reports lengths,
# on_topic agreement with the pre-1.3.0 prompt, numbers not found in the source, and market misses.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-06-analyze-contract.php [tab|rss] [limit] [body|excerpt]

# Side-by-side A/B: legacy vs production vs any tests/prompts/*.txt variant.
wp eval-file wp-content/plugins/ev-news-automator/tests/test-05-aeo-prompt-ab.php [tab|rss] [limit] [body|excerpt]

# «Резюме» briefs for 3 rows, written to the test Doc (check the bolding by eye).
wp eval-file wp-content/plugins/ev-news-automator/tests/test-03-google-docs.php [tab]
```

Background and evidence: `docs/plans/2026-10-14-001-feat-ev-news-aeo-summaries-plan.md` in the repository root.
