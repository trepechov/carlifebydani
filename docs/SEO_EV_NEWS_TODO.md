# EV News SEO — Action Backlog

Created 2026-08-13. Working doc for the on-site SEO push on EV-News episode pages.
Companion to [SEO_EV_NEWS_PROPOSALS.md](SEO_EV_NEWS_PROPOSALS.md) (the 2026-07-28 deep-dive
that diagnosed root causes). This file tracks *what to do next*, in priority order.

Status legend: `[ ]` not started · `[~]` in progress · `[x]` done · `[?]` needs a decision

---

## Baseline as of 2026-08-13

**Yoast meta coverage across the EV News category (cat id 1, 128 posts):**

| | count |
|---|---|
| Posts with focus keyphrase + SEO title + meta description | **1** (post 9248) |
| Posts with all three fields empty | **127** |

**Search performance, 2026-05-15 → 2026-08-10 (GSC, `https://www.carlifebydani.com/`):**

- 28 of 128 episode pages earn any impressions
- **2,067 impressions → 65 clicks → 3.1% CTR** across those 28
- Top 15 pages hold ~90% of the impressions
- ~100 pages have no measurable search visibility at all

Caveat: the GSC page query hit its 250-row cap. Rows come back ordered by clicks
descending, so pages with impressions but **zero** clicks may be missing from the list
below. Re-run with a page-filtered query before treating this as exhaustive.

---

## P1 — Meta descriptions for pages already ranking (striking distance)

These rank on or near page 1 and convert almost none of it. Missing `<meta name="description">`
means Google auto-generates a snippet from a page whose owned text is ~17 words. This is the
highest-confidence, fastest-feedback work: CTR change shows in GSC within 2–4 weeks.

Method: run the [`seo-article-optimize`](../.claude/skills/seo-article-optimize/SKILL.md) skill
on the page's URL — it does the research, writes a proposal to `reports/seo-metatags/`, backs up
the current meta, and applies the Yoast fields after you approve. Manually, it is the same as
post 9248 — back up current meta to
`reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv` first, write `_yoast_wpseo_focuskw` /
`_yoast_wpseo_title` / `_yoast_wpseo_metadesc` via the WordPress MCP, then fetch the rendered
page to verify. Yoast title template vars work — write `"<title> %%sep%% %%sitename%%"`.

Do **not** target "EV новини" / "новини за електромобили" on episode pages — that cannibalises
the `/ev-news-feed/` hub. Each episode should own its distinctive story.

| | Post ID | Slug | Impr | CTR | Pos |
|---|---|---|---|---|---|
| [x] | 7333 | `ev114-ima-li-registriran-tesla-cybertruck-v-blgariya` | 530 | 4.1% | 6.9 |
| [ ] | 5240 | `evn67-novi-spekulaczii-okolo-tesla-model-y-i-predstavitelstvo-v-blgariya` | 212 | 0.9% | 10.9 |
| [ ] | 8026 | `ev133-izminalata-2025-rekordni-prodazhbi-na-novi-avtomobili-v-blgariya` | 204 | 0.9% | 7.4 |
| [ ] | 6898 | `ev103-byd-navliza-v-rumniya-s-brzoto-razshiryavane-na-trgovskata-si-mrezha` | 138 | 0.7% | 7.7 |
| [ ] | 8721 | `ev152-ferrari-imame-elektrichesko-ferari-mercedes-iskame-da-schupim-fizikata` | 99 | 1.0% | 7.2 |
| [ ] | 7472 | `ev120` | 98 | 4.0% | 8.0 |
| [ ] | 5673 | `evn79-kia-ev2-niskobyudzhetna-kola-otnovo` | 97 | 2.0% | 8.8 |
| [ ] | 7367 | `ev117-mercedes-glc-eq-2026-vs-bmw-ix3-2026` | 95 | 2.1% | 6.8 |
| [ ] | 1751 | `evn41-tesla-my-juniper-se-otlaga-za-nqkolko-meseca` | 65 | 4.6% | **2.7** |
| [ ] | 5343 | `evn70-hyundai-ioniq-6n-novi-danni` | 65 | 4.6% | 9.3 |
| [ ] | 8319 | `ev144-byd-veche-e-v-blgariya` | 54 | 1.8% | 7.1 |
| [ ] | 5574 | `evn75-organizacziyata-na-naj-golyamoto-svetlinno-shou-v-blgariyaevn75` | 52 | 5.7% | 6.8 |
| [ ] | 7984 | `ev131-edin-gigacast-po-ksno` | 30 | 6.6% | 5.4 |
| [ ] | 7899 | `ev130-kak-se-zhivee-s-lucid-air-saphire` | 30 | 3.3% | 6.5 |
| [x] | 9248 | `evn-161-ilon-mask-zagatna-za-slivane-mezhdu-tesla-i-spacex-osthe-tazi-godina` | — | — | — |

Notes on individual rows:

- **7333 — DONE 2026-08-13.** Metatags written and verified live; proposal +
  full research in [`reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md`](../reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md).
  90-day baseline to beat: **542 impr / 22 clicks / 4.06% CTR / pos 7.0**. The page still has
  **no text answer to its own title question** (`wordCount: 17`) — that content fix is open.

### Missing from this list — a bigger CTR prize than anything above

- [ ] **Post 7533** `/ev-review/tesla-cybertruck-moshh-inovacziya-i-dizajn-bez-graniczi-zvyart-ot-bdeshheto-veche-e-tuk/`
      — **237 impressions, position 6.2, 0.84% CTR, 2 clicks** over 2026-05-15→08-12. Position 6
      should yield 4–6%. It loses more clicks than post 7333 did and was never in this backlog
      because the baseline scan only covered the EV-News category (this is `/ev-review/`).
      **Re-scan the other categories before working further down the P1 list.**
      It owns the spec/`кибертрак` intent cluster (`тесла cybertruck` pos 5.6,
      `тесла кибертрак` pos 9.4) — do not target `Tesla Cybertruck България` on it; 7333 owns that.
- **1751** sits at position **2.7** with 4.6% CTR. Position 2–3 should pull 10–15%. Something
  about how it presents in the SERP is actively costing clicks.
- **6898** at 0.7% CTR is the worst ratio in the set.

## P1 — the hub page itself

- [ ] `/ev-news/` category archive: **96 impressions, 2.0% CTR, position 9.5.** It appeared in
  the GSC data and I nearly missed it because the slug is empty after prefix-stripping. The
  archive page is a ranking entity in its own right and needs its own Yoast treatment — this
  is the one place where "EV новини" *is* the right keyphrase.

---

## P2 — Duplicate / cannibalising URLs

Verified present in the post list as live posts alongside their originals:

- [ ] `clone-ev155-216-rst-kakvo-se-sluchva-s-ev-pazara-v-blgariya` (8824) vs `ev154-...` (8812)
- [ ] `clone-ev140-kak-mina-clbd-coffee-day-1-2026` (8187)
- [ ] `clone-ev106-bez-sinya-i-zelena-zona-v-sofiya` (7013) vs `ev106-...` (7007)
- [ ] `ev122` (7584) and `ev122-zeekr-v-blgariya` (7577) — two posts, same episode number
- [ ] `ev80-rivian-i-vw-obedinyavat-sili-za-po-dobro-badeshte` (5742) and
      `ev80-audi-startira-nova-marka-v-kitai-zaedno-sys-saic` (5691) — same number, different stories
- [ ] `ev90-tesla-model-y-juniper-2025-be-predstaven-v-evropa` (6294) and
      `ev91-tesla-model-y-juniper-2025-be-predstaven-v-evropa` (6350) — identical titles
- [ ] `ev103-...` (6898) and `ev104-byd-navliza-v-rumniya-...` (6906) — identical titles,
      and 6898 is in the P1 list above. Resolve the duplicate *before* writing its meta.

**URLs earning impressions that are NOT in the current post list** — these need checking for
404s or missing redirects after a slug change:

- [ ] `clone-ev153-eldrive-fest-2026-nie-shhe-sme-tam` — 29 impr, 6.9% CTR, pos 4.3
- [ ] `clone-ev152-ferrari-imame-elektrichesko-ferari-...` — 12 impr, pos 6.2
- [ ] `clone-ev150-watts-on-the-grill-2026-...` — 6 impr, pos 8.2
- [ ] `ev147` (bare) — 22 impr, pos 10.9, while the live post is `ev147-mercedes-benz-c-klasa-...` (8351)

Decision needed per URL: 301 redirect to the canonical post, set a canonical tag, or delete.
Do not bulk-delete — some of these hold better positions than their originals
(`clone-ev153` at position 4.3 outranks a lot of the P1 list).

---

## P3 — Root cause: thin content

From the deep-dive. Nothing above fixes this; meta descriptions only change how a page is
*presented*, not whether it deserves to rank. This is what would help the ~100 pages with no
visibility at all.

- [ ] **Persist AI-translated summaries into `post_content`.** Currently fetched from a remote
      CSV at render time ([theme/single.php:110-115](../theme/single.php#L110-L115)) — fragile
      (empty page if the fetch fails) and invisible to Yoast and to Google's indexer.
- [ ] **Auto-generate a 100–150 word Bulgarian episode intro** in the EV News Automator
      pipeline (OpenRouter step), written to `post_excerpt`. Biggest single content lever.
- [ ] Summaries are hidden in a collapsed accordion (`max-h-0 opacity-0`, "Виж още").
- [ ] Heading structure jumps H1 → H5.
- [ ] Card thumbnails ship with empty `src`, JS-hotloaded from the source OG image.
- [ ] Titles/H1/slugs lead with `#EV160` — wastes the first characters of the SERP title.
      Post 9248 shows the fix: strip the prefix in `_yoast_wpseo_title`.

## P0 — verify this was actually fixed

- [x] **Site Language — FIXED (verified 2026-08-13).** The deep-dive found pages served as
      `en-US` (`<html lang>`, `og:locale`, Yoast `inLanguage`, breadcrumb "Home") while content
      is Bulgarian — caused by WP Settings → General → Site Language set to English (US).
      Re-checked on post 9248's live page: `<html lang="bg-BG">`, `og:locale=bg_BG`,
      schema `inLanguage="bg-BG"`. All three correct. `seo-bot` lacks `manage_options`, so this
      was verified by fetching the rendered page with `curl`, not over REST.

---

## Operational notes

- **WordPress revisions do NOT cover postmeta.** An overwritten meta description is
  unrecoverable. Always export to `reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv` first.
- **WAF gotcha:** the site 403s Python's default `urllib` User-Agent even with valid Basic
  auth; identical `curl` requests succeed. A 403 here is not a credentials problem.
- **Unauthenticated REST reads hide the Yoast fields** (`meta` shows only `footnotes`). That
  looks exactly like "the fields aren't registered" and is a trap. Authenticate.
- **Teardown when this push ends:** `claude mcp remove wordpress`,
  `security delete-generic-password -a "$USER" -s carlifebydani-wp-mcp`, then revoke the
  app password in the `seo-bot` profile. A standing production write credential shouldn't linger.
- `reports/yoast-meta-backup/` is currently untracked — decide gitignore vs commit.

## Measuring whether any of this worked

Re-run the `seo-performance-report` skill and compare CTR on the P1 pages specifically.
Baseline to beat: **2,067 impressions / 65 clicks / 3.1% CTR** over a 3-month window.
Give it 2–4 weeks after the writes for Google to re-crawl and for GSC to accumulate data.
