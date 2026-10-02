# SEO Optimization — #EV168 – Watts on the Grill – Септември 2026. Скара, коли и добра компания!

**URL:** https://www.carlifebydani.com/ev-news/ev168-watts-on-the-grill-septemvri-2026-skara-koli-i-dobra-kompaniya/ · **Post ID:** 9502 · **Category:** ev-news
**Prepared:** 2026-10-02 (Phase A)
**Status:** content-written
**Keyphrase:** Rivian R2 recall
**Ledger:** 9502-2026-10-02 (Phase B)

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode EV168 (published 2026-09-29, video `nVPebAaZRrU`, 122 chunks). The
**title** is about the site's own community meetup, "Watts on the Grill"
(September 2026 edition — BBQ, cars, good company), not a news story — this is
a recurring recap-episode format distinct from a regular news-roundup title.
The **headline news story** (CSV row 0, editorial ranking) is unrelated to the
title: Rivian has told some R2 owners to stop driving while high-voltage
wiring connections are inspected — a 2026 safety stop-drive/recall notice, not
the 2024 R2/R3 reveal this site already covered on an older post. 19 more
EV-industry stories follow (Tesla Supercharger vandalism in Australia, Xpeng
L03, BMW i3 US reservations, BMW iX3 efficiency, a Tesla FSD camera-detection
bug, Geely/Nio battery-swap deal, VW ID.Tiguan replacing ID.4, a possible
Tesla Roadster reveal date **and** a Tesla warning the Roadster demo might
slip, **and a Tesla FSD-in-Europe delay story** — see the cannibalization
note below). `post_content` is currently just the YouTube embed — **owned
word count is 23** (Yoast `wordCount`), confirming this is a Phase-B
candidate.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EV168 - Watts on the Grill - Септември 2026. Скара, коли и добра компания! - Car Life by Dani` | — |
| `<meta name=description>` | *(absent — Yoast metadesc is empty)* | 0 |
| Focus keyphrase | *(empty)* | — |
| H1 | matches post title | — |
| Owned word count | 23 (JSON-LD `wordCount`) | thin |
| `<html lang>` | `bg-BG` — correct | — |
| Existing tags | `Watts on the Grill` (id 367, count 6) | — |

### Demand research
**GSC:** not pulled — the post is 3 days old (published 2026-09-29); at this
age any pull would be near-certain zero and isn't worth the token cost. Same
"choosing a target from scratch" situation as other brand-new EV News posts.

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `watts on the grill` | *(all results are the unrelated US cooking show of the same name — zero connection to this site's event; confirms zero independent BG search demand for the event name itself)* |
| `rivian r2 стоп` | rivian r2 cancel reservation, rivian r2 cancel, rivian r2 stopping distance *(English-language, general-interest — no recall-specific completion)* |
| `rivian r2 recall` | **`rivian r2 recall`** — standalone confirmed completion |
| `rivian r2 изтегляне` / `tesla fsd европа забавяне` / `tesla roadster премиера дата` | *(no completions — too fresh/narrow, expected for breaking news)* |

**Keyword metrics (bg, DataForSEO `google_ads/search_volume`, fresh this run):**
| Phrase | Volume/mo | Competition | Note |
|---|---|---|---|
| `rivian r2` | 110 | LOW | real, searched mostly in English even from BG — matches the brand's lack of BG-language coverage |
| `watts on the grill` | 10 | — | noise — confirmed via autocomplete to be the US TV show, not this event |
| `rivian r2 проблем`, `tesla supercharger вандализъм`, `tesla roadster дата`, `tesla full self driving бъг`, `xpeng l03`, `bmw i3 2026` | no reportable volume | — | too narrow/fresh; not evidence of no demand |

Banked to `data/seo-cache/keywords.csv` (2 new rows, dataforseo source).

**SERP check (`Rivian R2 recall`, bg, DataForSEO live SERP):** Entirely
English-language global automotive/enthusiast sources — rivian.com's own
recall page, Reddit, InsideEVs, USA Today, Autoblog, Yahoo Finance. **Zero
Bulgarian-language coverage of this story at all** — a genuine, wide-open
content gap for the BG market, not a SERP this article would be fighting an
established editorial pack for (unlike the Tesla FSD/Europe SERP from the
previous episode's research).

**News CSV (12-col, 20 rows, row order = editorial ranking):** Row 0
(headline) tags: `Ривиан, R2, инцидент`, region `US`. Row 15 is `Забавяне на
Tesla в Европа с FSD` (Tesla FSD-in-Europe delay) — **flagged, not pursued as
a keyphrase candidate**, see cannibalization note below.

### Recommendation
**Focus keyphrase:** `Rivian R2 recall` — the article's actual headline story
(editorial row 0), a confirmed standalone BG-market autocomplete completion,
real measured volume (110/mo for the bare `rivian r2`, LOW competition), and
a SERP with zero existing Bulgarian coverage — the strongest kind of gap this
pipeline looks for. Kept in English rather than translating "recall" — the
BG press and BG search behavior for this brand both run in English (no
Bulgarian outlet has written about Rivian's recall at all yet; the brand
itself has no Cyrillic naming convention on this site either).

**Cannibalization checks:**
- `/wp/v2/search?search=Rivian` surfaces post **3469** (#EVN46, 2024-03-12,
  *"Rivian R2/R3, Тесла модел У баничарка"*) — has "Rivian R2" in its own
  title, but is the original 2024 **reveal** episode, unoptimized (empty
  Yoast fields), and narrates a completely different event (a product
  unveiling, not a 2026 safety stop-drive notice) separated by 2.5 years.
  Not a same-story-sequel — different event on the same product line, the
  kind of link the pipeline's rules treat as a genuine different subtopic.
  No keyphrase conflict with `Rivian R2 recall`.
- **Not pursuing** `Tesla FSD` anything as a keyphrase here, even though row
  15 of this episode's own news roll is "Забавяне на Tesla в Европа с FSD"
  (a Tesla FSD-in-Europe delay). Post **9486** (#EV167, this site's
  immediately preceding episode, 2026-09-22) was just optimized around focus
  keyphrase `Tesla FSD Европа`, covering the *same continuing story* one week
  earlier (Czech approval, "is the door to Europe opening"). A delay story
  one week later is its direct sequel. Per the pipeline's same-story-sequel
  rule, if Phase B finds the transcript actually discusses this delay in any
  depth, the citation must run **new → old** (this post citing back to 9486,
  never the reverse) — flagged here so Phase B doesn't default to treating it
  as this episode's own independent topic just because it's in the news roll.

**Secondary:** `Rivian R2`, `Rivian R2 изтегляне`, `Rivian high voltage`

### Proposed metatags
_Deferred to Phase C (needs real body prose from Phase B first)._

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Event series | Watts on the Grill | 367 | 6 (in-band, unchanged — matches this episode's actual title/subject) |

**Gaps noted, not created:**
- `Rivian` exists (id 229) but at **count 1** — below the reuse floor. Skipped
  per Step 4b, consistent with the same decision made on `Stark` (EV167,
  also count 1). Worth batch-creating a proper EV-brand tag set deliberately
  later rather than one-off per article — this is now the second
  low-traffic/niche-brand tag found thin in two consecutive episodes.
- `R2` (model) and `инцидент` (keyword-intent, recall/incident pattern already
  used elsewhere on this site e.g. `Autopilot`+`инцидент`) — no existing tag
  for either. Leave for Phase C to re-decide once Phase B's prose exists.

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `nVPebAaZRrU` — #EVNews - 29.09 - EV168 - Watts on the
Grill - Септември 2026. Скара, коли и добра компания! (2026-09-29T16:10:15Z).
Note: `tools/resolve_episode.py "EV168"` (plain) initially returned
`NOT_INGESTED` against a stale 6h-TTL cache; `--refresh` found it immediately.
**Answer found in:** own episode — both the chosen keyphrase story (Rivian R2)
and the title's own subject (the Watts on the Grill event itself) are
genuinely discussed on air, at different points in a long, casual Q&A-style
episode.

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|
| Rivian notified some R2 owners to stop driving while high-voltage circuit connections are inspected; neither host owns a Rivian, joked the notice sounds scarier than an actual battery recall | "Ривиан е уведомил някои от собствениците на новия си R2 да спрат да шофират, докато се извършват проверки на високоволтовите вериги." / "Аз нямам ривиан." | nVPebAaZRrU | [7189](https://www.youtube.com/watch?v=nVPebAaZRrU&t=7189), [7281](https://www.youtube.com/watch?v=nVPebAaZRrU&t=7281) |
| The event was "a real madhouse" — so many people wanted to talk to the hosts all evening that nobody managed to take a single photo of the grill/cars; thanks to the partner team for organizing | "Беше хубаво събитие. Много ми хареса... Апи ми звънна днеска и вика: 'Абе, аз се прибрах в 8:00 и установих, че нямам нито една снимка.'" / "Много благодаря на [партньорския екип]... че всеки път са на линия и помагат" | nVPebAaZRrU | [1899](https://www.youtube.com/watch?v=nVPebAaZRrU&t=1899) |
| Nio is closing its Köln hub by end of September; Tesla FSD has a bug that falsely flags cameras as dirty, Tesla confirmed and cancelled scheduled service visits | "Нил затваря хъб в КОН... Няма да работи до края на септември." (ASR: Nio→"Нил", Köln→"КОН") / "Бък на Tesla FSD лъже за мръсни камери... Компанията потвърди проблема и отменя насрочените сервизни посещения." | nVPebAaZRrU | [8263](https://www.youtube.com/watch?v=nVPebAaZRrU&t=8263) |
| The EU vote on Tesla FSD approval still hasn't happened — postponed again, now to October 6th | "Гласуването за автономно шофиране на Tesla FSD все още не е минало, тъй като беше отложено на шести 10ти." | nVPebAaZRrU | [9090](https://www.youtube.com/watch?v=nVPebAaZRrU&t=9090) |

### Draft paragraphs
¶1 (51 words): Rivian R2 recall стана тема в новия епизод: компанията е
уведомила част от собствениците на новия си електрически SUV R2 да спрат да
шофират, докато текат проверки на високоволтовите електрически вериги в
колата. Никой от водещите не притежава Rivian, но се пошегуваха, че
известието звучи по-страшно от истински рекол на батерия.

¶2 (63 words): Водещите разказаха и как мина самото Watts on the Grill —
според тях било истинска лудница: толкова много хора искали да си говорят с
тях цяла вечер, че никой не успял да извади телефона и да снима и една
снимка от грила и колите. Благодарности отправиха и към партньорския екип, с
който организираха вечерта, задето винаги са на линия и помагат с всичко.

¶3 (49 words): Останалата част от епизода мина през новини като затварянето
на хъба на Nio в Кьолн до края на септември и бъг в Tesla Full Self-Driving,
който погрешно съобщава на собствениците за замърсени камери. Гласуването за
одобрение на Tesla FSD в Европа отново беше отложено, този път за 6
октомври.

**Internal links added (both older posts, correct "new→old" direction,
no same-story-sequel issue):**
- "Watts on the Grill" → post 9441 (the Sept 27 ticket page for this exact
  event, already optimized 2026-09-16)
- "Tesla FSD в Европа" → post 9486 (#EV167, last week's Czech-approval
  episode — resolves the cannibalization flag from Phase A: cited backward
  as the earlier chapter of this continuing story, not treated as an
  independent new topic)

Both targets already have `Status: applied` rows in
`reports/seo-optimizations/ledger.csv` — no update needed to
`docs/SEO_BACKLINK_TARGETS_TODO.md`.

### Facts to confirm before publishing
- [x] Applied as written. One hedge kept deliberately: at [1899] a host
  thanks a partner using a name the ASR renders as "Воловой" — phonetically
  close to "Волво" (Volvo) but not clean enough to assert confidently, so the
  prose says "партньорския екип" rather than naming a brand.
- [x] Fixed a transcription typo on the first write ("задълго" instead of
  the approved "задето") — corrected in a second write before verification.