# SEO Optimization — #EV134 – Porsche Cayenne – на къде са тръгнали Porsche?

**URL:** https://www.carlifebydani.com/ev-news/ev134-porsche-cayenne-na-kde-sa-trgnali-porsche/ · **Post ID:** 8035 · **Category:** ev-news
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Porsche Cayenne Electric тест драйв
_(revised during Phase B — see note below; was "...интериор" during Phase A alone)_
**Ledger:** 8035-2026-09-04; 8035-2026-09-04-C

**Cluster context:** step 4 of 5 in the Porsche/Cayenne cluster prep —
[`reports/seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md`](../seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md).
Runs after 1039, 625 and 7828 (all applied 2026-09-04). Will link back to all
three (same-story sequel, chronological rule per commit `ec014b2`). Draft post
9216 excluded — not touched.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode #EV134 (published 2026-01-13, video `HDaa5oC-yhk`, 93 transcript
chunks ingested). `post_content` is currently just the YouTube embed — Yoast
`wordCount: 18` — the standard EV News thin-content pattern. The post's own
title asks "накъде са тръгнали Porsche?" ("where is Porsche headed?") and
continues the same Cayenne Electric story `post 7828` (EV128, 2025-11-25)
opened ~7 weeks earlier: per `summarize_episode` and a direct transcript
search of this video, the hosts react to Cayenne Electric footage — approving
of the new interior/design and revisiting the induction (wireless) charging
angle already teased in EV128 — plus a filmed comparison/chase clip against
another Porsche model. No other Porsche/Cayenne story appears in this
episode's news list (see below) — the headline is entirely title-driven, same
pattern as 7828.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EV134 - Porsche Cayenne - на къде са тръгнали Porsche? - Car Life by Dani` (Yoast fallback) | ~76 chars |
| `<meta name=description>` | *(none — no fallback, no content to build one from)* | 0 |
| Focus keyphrase (Yoast) | *(empty)* | — |
| H1 | matches title | — |
| Owned word count | 18 (Yoast schema `wordCount`) | — |
| Images without alt | Featured image (media 8037) not yet checked — will confirm in Phase C | — |
| Internal links out / in | 0 out (video embed only). In: none yet. |  |

### Demand research
**GSC (90d, this URL):** page-level pull (query-level too thin to break out):
**1 impression, 0 clicks, position 6.0**. Per Decision rules, effectively zero
visibility — nothing to preserve, choosing a target from scratch (common for
~100 of 128 EV News pages).

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `porsche cayenne electric интериор` | → interior, interior 2026, turbo electric interior, 2025 interior, interior reveal, interior screens, interior features, inside, interior display, design |
| `porsche cayenne electric тест` | → тест драйв, test, testing (virtual/real-world), testing, turbo electric test, 2026 test, s electric test, range test, road test, test drive |
| `porsche cayenne electric видео` | → 2026 youtube, youtube, video, turbo video, turbo electric youtube, youtube video |
| `porsche cayenne electric индукционно зареждане` | none — the wireless-charging angle still has no BG-market autocomplete footprint (same finding as 7828 for the sibling phrase) |

Real, if modest, demand clusters around **interior/design** and **test
drive/video** modifiers — a good match for what this specific episode
actually covers (interior reaction + a driving/chase clip), distinct from
7828's reveal-announcement angle.

**Keyword metrics (bg):**
| Keyword | Volume (bg, monthly avg) | Competition | CPC | Source |
|---|---|---|---|---|
| **porsche cayenne electric test drive** | **10** (sparse: only 2 of 12 months) | — | — | fresh DataForSEO 2026-09-04, banked |
| **porsche cayenne electric weight** | **10** (steadier: 6 of last 12 months) | — | — | fresh DataForSEO 2026-09-04, banked |
| porsche cayenne electric interior | 10 (flat, all 12 months) | LOW (0.19) | — | fresh DataForSEO 2026-09-04, banked (Phase A's original candidate, superseded — see Recommendation) |
| porsche cayenne electric vs macan / towing | no data | — | — | fresh DataForSEO 2026-09-04 (zero/no-volume) |
| porsche cayenne electric (bare, cached from 7828) | 170 | LOW (19) | €0.91 | cached 2026-09-03 |

Volume is thin (10/mo) but real and English-phrased, consistent with how
BG buyers search car specs. Per the skill's decision rules, absent/low
paid-tool volume isn't evidence of no demand here — autocomplete is the
stronger signal for this niche, and it clearly favors interior/design intent.

**SERP check:** skipped — this is a reaction/impressions piece continuing an
existing story, not a comparison/how-to query type competing for a
head-term SERP.

**GA4:** skipped — thin, near-zero-traffic post, same reasoning as 7828.

**News CSV:** ⚠️ `meta.news_csv` not returned even with `context=edit` (only
`_yoast_wpseo_*` + `footnotes`) — same known W12 REST-exposure gap noted on
7828/9348. Fell back to parsing the rendered page's news cards (27 unique
`js-external-article` entries). **None of the 27 cards mention Porsche or
Cayenne at all** (Zeekr, CATL, Mazda, Volvo, Tesla, ZF, Toyota, Genesis,
Skoda, Kia, etc.) — confirms the Cayenne story here is carried entirely by
the video content/title, not the news roundup, same as 7828.

### Recommendation
**Focus keyphrase:** `Porsche Cayenne Electric тест драйв` — **revised during
Phase B** once the episode's own transcript was read in full (Phase A alone,
working from `summarize_episode`'s gloss, had picked `...интериор`). The
actual segment is the hosts' own hands-on drive of the Cayenne Turbo
Electric — spoiler/software quirk, towing capacity, weight vs Macan (150–200
kg heavier), charging-speed observation, dynamics vs Tesla Model X/Y — a
first-drive/comparison piece, not primarily an interior-design reaction.
`тест драйв` matches that content, is distinct from 7828's `Porsche Cayenne
Electric` (reveal story — avoids cannibalization), and has the same real,
modest measured demand as the interior phrase did.
**Secondary:** `Porsche Cayenne Electric тегло`, `Porsche Cayenne Electric срещу Macan`, `Porsche Cayenne Turbo Electric теглене`

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → Yoast fallback)* | *(drafted in Phase C once body content exists)* | — |
| `_yoast_wpseo_metadesc` | *(empty)* | *(drafted in Phase C)* | — |
| `_yoast_wpseo_focuskw` | *(empty)* | `Porsche Cayenne Electric интериор` | — |

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Porsche | 57 | 7 (in-band) |
<Gap: no `Cayenne` tag exists on the site (checked `/wp/v2/tags?search=Cayenne`
— zero results). Not creating it speculatively. Same gap will recur on 9216
once it publishes — worth batch-creating deliberately if the cluster keeps
growing rather than one-off per article.>
<`Зареждане` (id 40, count 13) is a candidate for the induction-charging
angle but sits just above the 3–10 band (established/reach territory) — defer
the final call to Phase C once the actual body prose confirms whether
charging is substantively named in ¶1–¶3, not just implied.>

Only `Porsche` is confirmed pre-Phase-B, per the "tag only what's confirmed
before body content exists" rule — full entity check happens after Phase B
writes real prose.

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `HDaa5oC-yhk` — #EVNews 13.01 EV134 (published 2026-01-13T16:44:13Z)
**Answer found in:** own episode. `search_transcripts` scoped to this
`video_id` returned the actual segment directly — the hosts personally drive
the new electric Cayenne Turbo and give hands-on impressions, which is a
richer and different answer than Phase A's initial read (via
`summarize_episode`'s gloss) suggested. **This changed the keyphrase** — see
the revised `Keyphrase:` line above and the Recommendation note in Phase A.

| Claim | Quote / paraphrase | Timestamp |
|---|---|---|
| Hosts personally drive the new electric Cayenne Turbo; overall verdict "много сериозна и много, много изпипана кола" (a very serious, very finely finished car), no compromises in build quality | direct paraphrase + near-quote | [t=1460](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=1460) |
| Cayenne Electric (Turbo trim) is ~150–200 kg heavier than Macan Electric Turbo; the base trim's gap is smaller | "грубо между 150 и 200 кила е разликата между Каен и Макан... Това е за турбото... За базовия е по-малко" | [t=1754](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=1754) |
| Can tow "3 tons and up" (exact upper figure not stated — ASR-ambiguous, hedged in prose) | "може да тегли 3 и по... Не знаем колко може да ги тегли" | [t=1460](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=1460) |
| The rear spoiler's open/close button logic is confusing; hosts call Porsche's software "still in another era" | "при Porдации в софтуера, но да кажем, че те на тема софтуера още са в друга епоха" | [t=1386](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=1386) |
| Compared directly to Tesla Model X on power and dynamics | "това е конкурент на Tesla Model Xат и като мощност и като динамика" | [t=1754](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=1754) |
| Episode also covers: Tesla 2025 sales down ~27.8% (326,000→235,000 units) and Giga Berlin closure rumors; Zeekr 7GT called a European EV benchmark; Beijing air quality improving as EV share of new China car sales passed 50% | direct paraphrase | [t=6779](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=6779), [t=6880](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=6880), [t=2932](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=2932), [t=5611](https://www.youtube.com/watch?v=HDaa5oC-yhk&t=5611) |

Two figures in the transcript are ASR-garbled and **not** used as hard
numbers in the draft: an exact charging-speed comparison (heard as "285" kW
average vs Model Y, context unclear) and a 24-minute duration tied to an A7
highway stretch. Both omitted per the no-fabricated-number rule rather than
guessed.

### Draft paragraphs

¶1 (46 words): На въпроса „накъде са тръгнали Porsche?" епизод #EV134
отговаря през собствен **Porsche Cayenne Electric тест драйв**, а не през
спекулации. Водещите карат новия електрически Cayenne Turbo на живо и стигат
до едно общо впечатление: „много сериозна и много изпипана кола", направена
без компромиси в изработката.

¶2 (69 words): В самата кола водещите отбелязват, че новият електрически
Cayenne Turbo е между 150 и 200 кг по-тежък от <a href="https://www.carlifebydani.com/publications/porsche-postavya-macan-na-izpitaniq-v-imeto-na-proizvoditelnostta/">Porsche Macan Electric</a>
в турбо изпълнение — усеща се, но не прави колата тромава. Тегли над 3 тона,
а спойлерът отгоре все още се управлява с леко объркваща логика на
бутоните — според тях софтуерът на Porsche „още е в друга епоха". По
динамика колата се сравнява директно с Tesla Model X.

¶3 (56 words): Извън теста, епизодът разглежда и спад от близо 28% в
продажбите на Tesla през 2025 г., слухове за спиране на Gigafactory Berlin,
новия Zeekr 7GT, представен като бенчмарк за електромобилите в Европа, и
по-чистия въздух над Пекин заради нарастващия дял електромобили в Китай.
Историята на самия Cayenne Electric продължава оттам, докъдето стигна
<a href="https://www.carlifebydani.com/ev-news/ev128-noviya-elektricheski-cayenne-na-porsche/">предишният епизод #EV128</a>.

Total: 171 words. Internal links: 2 (Macan Electric → post 625, EV128 Cayenne
reveal → post 7828 — both older than this post, so the same-story-sequel
check per Step 6 passes cleanly. Post 1039 (Taycan) is not naturally on-topic
for these three paragraphs; Phase C's cluster inbound/outbound table will add
it if warranted rather than forcing it into the prose here.)

### Facts to confirm before publishing
- [ ] Exact towing capacity beyond "3 tons and up" — ASR did not resolve a
  clear upper figure.
- [ ] The 285 kW / 24-minute charging figures heard in the transcript — too
  ASR-ambiguous to publish as stated; omitted from the draft.

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change needed (matches title, keyphrase intent already
  covered by the new ¶1).
- [ ] **Image alt + title** — media 8037 (featured image), current
  `alt_text` empty, `title` = `#EVN 134-2` (meaningless auto-generated —
  same defect class caught on 7828/9350):
  - `alt_text`: `Porsche Cayenne Electric на тест драйв`
  - `title`: `Porsche Cayenne Electric тест драйв`

### Internal links
**Inbound — existing posts that should link here:** none proposed. All three
cluster candidates (1039, 625, 7828) predate this post, but 625 and 7828
narrate the *same continuing Cayenne/Macan story* — per the same-story-sequel
rule, the citation runs new→old (already done: this post's own ¶2/¶3 link
out to both), not old→new, so 625/7828 should not be edited to link forward
here. **1039 (Taycan Turbo S)** is not a same-story sequel (different model
line) so is technically date-unrestricted, but there is no natural topical
sentence in either 1039's review prose or this post's three paragraphs to
carry an honest link — forcing one would violate the "reads as something the
author would have written" rule. Recommendation: leave 1039 uncited from this
post; the Porsche-cluster's natural place to tie all four together is 9216
(Macan GTS Electric review, still draft) once it publishes and gets its own
Phase C — see the cluster plan's step 5.

**Outbound — this article should link to:** already written by Phase B
(inline in ¶2/¶3, no separate write needed here):
| Target post | URL | Anchor text | Where |
|---|---|---|---|
| 625 (Porsche Macan Electric) | `/publications/porsche-postavya-macan-na-izpitaniq-v-imeto-na-proizvoditelnostta/` | "Porsche Macan Electric" | ¶2 |
| 7828 (#EV128 Cayenne reveal) | `/ev-news/ev128-noviya-elektricheski-cayenne-na-porsche/` | "предишният епизод #EV128" | ¶3 |

### Proposed tags (carried forward from Phase A + confirmed against ¶1–¶3 prose)
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Porsche | 57 | 7 |
| Entity | Macan | 58 | 4 |
| Entity | Zeekr | 222 | 7 |

All three are named in the actual body prose (Macan and Zeekr each appear
once, Porsche throughout) — matches the owned-prose-vs-cards rule
([[feedback-tag-cap-owned-prose]]). `Tesla` (id 4, count 56) intentionally
excluded despite being named twice — saturated brand hub, not the headline
entity here. `Cayenne` remains a gap (no existing tag) — noted in Phase A,
not created speculatively.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → Yoast fallback: "#EV134 - Porsche Cayenne - на къде са тръгнали Porsche? - Car Life by Dani")* | `Porsche Cayenne Electric тест драйв %%sep%% %%sitename%%` (renders: *Porsche Cayenne Electric тест драйв - Car Life by Dani*) | 35 body chars |
| `_yoast_wpseo_metadesc` | *(empty)* | `Porsche Cayenne Electric тест драйв: 150–200 кг по-тежък от Macan Electric, тегли над 3 тона и се сравнява директно с Tesla Model X. Вижте впечатленията.` | 153 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Porsche Cayenne Electric тест драйв` | — |

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live via `curl` re-fetch
- [x] Tags written — Porsche (57), Macan (58), Zeekr (222)
- [x] `post_content` written (Phase B) — wordCount: 18 → 181
- [x] Image alt + title written — media 8037
- [ ] Inbound links — none proposed (see Internal links above)
- [x] Auto-linked `/tag/` count inside body prose: **2** (Porsche ×1 in ¶2,
  Zeekr ×1 in ¶3 — both match the theme's 1×-per-tag cap exactly; verified
  by isolating body-prose hrefs from the separate tag-pill list, which also
  renders `#Porsche`/`#Zeekr`/`#Macan` and was initially miscounted as
  auto-links). Macan gets 0 auto-links because its only prose mention is
  already inside the manual link to post 625 — expected, the regex skips
  text already wrapped in `<a>`.

### Declined
_None — both manifest questions (metatags+tags, image alt+title) were
approved in full._
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|

### Risks / notes
Zero-impression baseline (both 28d and 90d) means there's nothing to
cannibalize and no downside risk from the metatag/keyphrase change itself.
The real test is whether new body content (181 words, up from 18) gets
crawled and starts drawing any impressions at all — thin EV News pages are
~100 of 128 posts, so this is a representative case for that cohort, not an
outlier.

### Measurement
Baseline (GSC, 28d ending 2026-09-03): 0 impr / 0 clicks / — CTR / — pos.
Baseline (90d): 1 impr / 0 clicks / pos 6.0.
Re-check: 2026-10-02 (metatags/tags/alt, 28d ledger row) and 2026-10-30
(new body content, 56d ledger row) — both via `seo-performance-report`'s
monthly verification pass. Ledger rows: `8035-2026-09-04` (Phase B),
`8035-2026-09-04-C` (Phase C).

---
