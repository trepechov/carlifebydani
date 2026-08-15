# SEO Optimization — #EV151 – Zeekr вече е в България

**URL:** https://www.carlifebydani.com/ev-news/ev151-zeekr-veche-e-v-blgariya/ · **Post ID:** 8659 · **Category:** ev-news
**Prepared:** 2026-08-15
**Status:** applied
**Keyphrase:** `Zeekr представителство в България`
**Ledger:** 8659-2026-08-15 (Phase B, content); 8659-2026-08-15-C (Phase C, title|metadesc|focuskw|tags|alt|inbound)

**Business context (from the user, 2026-08-15):** part of the same push as post 9099 (Zeekr 7X
review, already optimized) — Zeekr recently entered the Bulgarian market and building the
brand relationship is a business priority. This episode is the news-side complement to 9099's
review: the hosts cover the first Zeekr X7 demo unit's arrival in Bulgaria.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`._

### What this article is about
EV News episode #EV151 (recorded/published 2026-05-19, YouTube `krIbXSFVPEY`, 147 transcript
chunks). **Headline story** (per transcript, not the news CSV — this is the hosts' own on-air
topic, not an external card): the first Zeekr X7 demo unit registered in Bulgaria was spotted
parked at a multi-brand car event ("The Mall" — a showroom event alongside Volvo, Ford,
Renault, Kia, Hyundai, MG, Peugeot, Citroën, DS, Opel, Alfa Romeo, Jeep, Fiat, Leapmotor,
Nissan, Omoda). The hosts note Zeekr now has a **Bulgarian-language website**, discuss the
lineup they saw (7X, 9X, 8X, 001, pricing from ~€38k for the smallest model), compare
positioning to Volvo/Lynk & Co, and say communication with Zeekr's local side is good enough
that dedicated Zeekr content ("review, first impressions") is coming soon once a demo car is
registered for them to drive. This is a **market-entry / dealer-presence story**, not a
review — no pricing table, no test-drive data.

After the headline segment, the episode covers ~15 unrelated external EV news cards (Tesla
Giga Berlin investment, Toyota bZ4X Touring, BYD/Stellantis, Honda loss, VW Golf EV delay,
etc. — full list + descriptions now in the Google Sheet `19.05.2026` tab, backfilled this
session since that tab had gone missing/needed recreation from an old copy).

**Owned word count: 14** (Yoast `wordCount`) — `post_content` is only the YouTube embed. This
is a Phase-B candidate.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(no SEO title → falls back to H1)* `#EV151 - Zeekr вече е в България - Car Life by Dani` | not keyphrase-optimized |
| `<meta name=description>` | **absent entirely** | — |
| Focus keyphrase | empty | — |
| H1 | `#EV151 - Zeekr вече е в България` — then jumps straight to H5 (no H2–H4) | defect, known site-wide pattern |
| Owned word count | **14** | thin — Phase B required |
| Images without alt | featured image (media 8662) alt empty, title raw (`#EVN 151`) | — |
| Internal links out / in | 0 out (no prose yet); the theme's own "Избrano за вас" auto-related block already surfaces `/ev-masters/zeekr-v-evropa-.../` and `/publications/zeekr-viziyata-zad-markata-.../` — not editorial links, doesn't count |

### Demand research
**GSC (90d, this URL):** effectively zero. Only 1 real row across the whole `query,page` pull
(top 1000 rows site-wide) touches this URL: `car life by dani` — 2 impressions, 0 clicks, pos
9 (a branded query, not topical). **Nothing to preserve — choosing a keyphrase from scratch.**

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `zeekr` | zeekr 7x, zeekr 9x, **zeekr българия**, zeekr 8x, zeekr 7x цена, zeekr 9x цена, zeekr 8x цена, zeekr 7gt, zeekr 001 цена |
| `zeekr x7` | zeekr x7, zeekr x7 цена, zeekr 7x, **zeekr 7x цена българия**, **zeekr 7x българия**, zeekr 7x мнения, zeekr 7x price/review/dimensions/2026 |
| `zeekr българия` | zeekr българия, zeekr българия цена, **zeekr 7x българия**, zeekr 9x българия, zeekr 8x българия, zeekr 001 българия, **zeekr представителство в българия**, zeekr 7x/8x/9x цена българия |
| `zeekr цена` | zeekr цена, zeekr цена в българия, zeekr цена в китае/украина/россии, zeekr цена 001/2025 |
| `zeekr дилър` / `zeekr официален` | *(no completions — no measurable demand for these exact strings)* |

**Cannibalisation check — critical finding:** `zeekr 7x българия` is **already owned by post
9099** and earning real traffic there: 52 impressions, 1 click, **position 6.8** (from 9099's
fresh GSC pull, cross-checked against this session's own 90d pull — same row). So is
`zeekr 7x цена българия` territory (9099 owns the un-suffixed `zeekr 7x цена` at 1,303
impressions/pos 7.2). **Any 7X-model+pricing phrasing is off-limits for this post** — it would
compete with a page 9099 already ranks for, not extend the site's coverage. `zeekr представителство
в българия` (dealer/representation-in-Bulgaria) is the one autocomplete-validated phrase that
(a) has real query demand, (b) matches this episode's actual content — market entry/dealer
presence, not the car itself — and (c) is unclaimed: checked `/wp/v2/search?search=представителство`,
the only hit is an unrelated Tesla/Model Y post (5240).

Also checked post **7577** (`#EV122 – Zeekr в България`, an earlier episode with the same
"Zeekr is in Bulgaria" framing) — its Yoast fields are all empty, so it currently claims
nothing. It's a softer overlap risk than 9099 but not a live conflict; flagged in
`docs/SEO_EV_NEWS_TODO.md` P2 as a separate duplicate-URL issue already, unrelated to this pass.

**SERP check:** not run — DataForSEO still blocked (`40104`, diagnosed 2026-08-13, not
re-probed this session per the known-traps note) and Semrush's BG index is too sparse for a
dealer-presence long-tail like this. Autocomplete + the GSC cross-check against 9099 already
give a clear, evidence-based picture for a zero-impression episode page.

**News CSV:** not fetched via `meta.news_csv` (still not REST-exposed for this post — the
`show_in_rest` registration may not have reached the live site yet, or this post predates the
plugin). Substituted with the actual underlying data: the Google Sheet's `19.05.2026` tab
(sheet ID `1cy87PXqGsJ8nLDrPNWXXZkAOkX_6ZpMvlO4Q-jYebzM`), which this session recreated and
backfilled with descriptions — same 15 stories, none of them Zeekr-related (the Zeekr story is
the hosts' own on-air segment, not a card). No tag candidates from this source for the Zeekr
angle; the 15 external cards are Phase-B/¶3 candidates for the *other* stories only if the
transcript covers them at comparable depth (it does, briefly, per the news-reading segment).

### Recommendation
**Focus keyphrase:** `Zeekr представителство в България` — the only autocomplete-validated,
currently-unclaimed phrase that matches this episode's actual content (dealer/market-entry
news, not a car review). Zero GSC baseline means there's nothing to lose and a real, if small,
demand signal to build toward.

**Secondary:** `Zeekr в България`, `Zeekr официален сайт`, `Zeekr X7 демо автомобил`

**Explicitly avoided:** `Zeekr 7X цена`, `Zeekr 7X българия`, `Zeekr 7X цена българия` — all
owned by post 9099 with real, measurable traffic already. Using any of these here would be
cannibalisation, not coverage.

### Proposed metatags
_Drafted here for Phase C to review after Phase B writes real body content — the metadesc
should quote the actual paragraph, not be guessed ahead of it._
| Field | Before | After (draft — Phase C finalizes against real prose) | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to H1)* | `Zeekr представителство в България — първият демо X7 %%sep%% %%sitename%%` | ~45 rendered (check at Phase C) |
| `_yoast_wpseo_metadesc` | *(absent)* | *(draft, pending Phase B)* `Zeekr вече има представителство в България — първият демо автомобил X7 беше забелязан в София, а марката пусна и български сайт. Разказваме какво видяхме.` | ~150 (check at Phase C) |
| `_yoast_wpseo_focuskw` | *(empty)* | `Zeekr представителство в България` | — |

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | Zeekr | 222 | 5 | ✅ keep — the brand, squarely in-band |
| Entity | Zeekr 7X | 428 | 2 | ✅ keep — below the 3–10 band, but same reasoning as 9099's report: this is the exact headline entity (the demo car is an X7), not a borrowed concept |

Checked `Регистрация` (id 327, count 1) and `Премиера` (id 48, count 7) as possible
keyword-intent tags. **Skipped both**: `Регистрация` is below-band and the demo unit isn't
registered yet per the transcript ("ще бъде първият... регистриран като демо автомобил" —
future tense). `Премиера` doesn't fit — this is a sighting/market-entry story, not an official
model premiere. No keyword-intent tag added; revisit once Phase B's prose is final in case it
surfaces a better-fitting existing tag.

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`._

**Episode resolved:** `krIbXSFVPEY` — "#EVNews - 19.05 - EV151 - ZEEKR вече е в България"
(published 2026-05-19T15:49:31Z), 147 chunks.
**Answer found in:** own episode — `search_transcripts(video_id="krIbXSFVPEY")` on the page's
own headline question returned 15 directly relevant chunks; no archive fallback needed.

| Claim | Quote / paraphrase | Timestamp |
|---|---|---|
| First Zeekr X7 demo unit in Bulgaria, spotted at a multi-brand event ("The Mall") | *"В The Mall е паркиран със сигурност X7 ... това е първият ZКР, който ще бъде регистриран като демо автомобил"* | [40:15](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2415) |
| Car is dark grey; cars arrived Saturday 5am | *"Аз го видях един тъмно сив"* / *"ние го получихме в събота в 5 сутринта, когато автомобилите влизаха в The Mall"* | [40:15](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2415) |
| Zeekr now has a Bulgarian-language website | *"А те вече български сайт имат ли? Имат. Имат."* | [40:15](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2415) |
| 3–4 models offered in Bulgaria; top trim positioned highest in segment, compared to Lynk & Co / Volvo | *"В България се предлагат трите или четирите? Всъщност четирите"* / *"това е сякаш по средата между Link и Volvo ... това е върха зикъра ... най-високото в сегмента"* — **ASR-ambiguous count, hedged as "3–4" in prose** | [44:38](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2678) |
| Good communication with Zeekr's local side; more Zeekr content coming to the channel | *"За щастие на тоя етап с Зикър имаме добра комуникация. Надявам се, че скоро ще почнете да виждате съдържание, свързано със ZКР в нашия канал"* | [44:38](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2678) |
| Review/first-impressions article promised once a demo unit is registered to them | *"Кога дали ще има статия ревю на моделите от Зикри първи впечатления? ... имаме уговорка, че съвсем скоро ще има регистриран такъв автомобил"* | [2:21:32](https://www.youtube.com/watch?v=krIbXSFVPEY&t=8492) |
| Episode reads through the week's 15 news stories, opening with Tesla's Giga Berlin battery investment | *"Влезте в cbd.bg. Вижте 151-ва статия на подкаста и там са новините ... Tesla гига Берлин. Допълнителни инвестиции от 250 милиона в батерийни клетки капацитет до 18 гигаватчас"* | [2:46:16](https://www.youtube.com/watch?v=krIbXSFVPEY&t=9976) |
| Android Auto's major update discussed | *"Android Auto масивен ъпдейт."* | [3:16:01](https://www.youtube.com/watch?v=krIbXSFVPEY&t=11761) |

### Draft paragraphs
¶1 (53 words): Zeekr представителство в България вече е факт — в епизод #EV151 екипът на Car
Life by Dani показва първия Zeekr X7, който предстои да бъде регистриран като демо автомобил у
нас, забелязан на мултибранд автомобилно събитие в столичен мол. Марката вече има и официален
български сайт, а водещите очакват скоро да получат колата за първи впечатления.

¶2 (69 words): Тъмносивият X7 пристига в мола в събота сутринта в 5 часа заедно с
автомобилите на конкурентни марки. Водещите разглеждат новия български сайт на Zeekr и
откриват, че у нас вече се предлагат три-четири модела — най-скъпият е позициониран
най-високо в сегмента, сравним с Volvo и Lynk & Co. Комуникацията с местния тим на Zeekr е
добра, казват те, и очакват скоро регистриран демо автомобил за <a
href="https://www.carlifebydani.com/ev-review/zeekr-7x/">пълен тест на X7</a>.

¶3 (57 words): Извън историята на Zeekr, епизодът минава набързо през 15-те новини на
седмицата от cbd.bg — инвестицията на Tesla от $250 млн. за разширяване на батерийното
производство в Гигафабрика Берлин и масивното обновление на Android Auto с цял екран и
Gemini. За по-обстоен поглед към <a
href="https://www.carlifebydani.com/ev-masters/zeekr-v-evropa-skorost-garancziya-i-serviz-bez-kompromisi/">гаранцията
и сервиза на Zeekr в Европа</a> Car Life by Dani е писал отделно.

**Total: 179 words** (target 130–190).

### Facts to confirm before publishing
- [ ] Starting price "38 евра/евро" for the smallest model heard at [40:15](https://www.youtube.com/watch?v=krIbXSFVPEY&t=2415) —
      ASR-ambiguous currency token, **not used in the draft** to avoid publishing a possibly-wrong price.
- [ ] "3–4 models" — the transcript itself is uncertain between the two numbers ("трите или
      четирите?"); hedged as written, not resolved to one.

### Internal links used
1. `/ev-review/zeekr-7x/` (post 9099) — anchor "пълен тест на X7", in ¶2.
2. `/ev-masters/zeekr-v-evropa-skorost-garancziya-i-serviz-bez-kompromisi/` (post 8950) —
   anchor "гаранцията и сервиза на Zeekr в Европа", in ¶3.

---

## Phase C — Apply
_Written by `seo-article-apply`._

### On-page changes proposed
- H1 — left unchanged (`#EV151 - Zeekr вече е в България`). Episode-number H1s are the site's
  branding convention; not proposed for change, consistent with the 9099/9248 precedent.
- First 100 words — already satisfies keyphrase-in-opening-sentence via Phase B's ¶1; nothing
  to add here.
- No H2/H3 subheadings proposed — EV News episode intros are 3 short paragraphs by convention
  (no site precedent for subheadings at this length).
- Image alt + title (media 8662, featured image) — proposed and approved (see below).

### Backups (before any write)
- `reports/yoast-meta-backup/8659-2026-08-15.csv` — pre-write Yoast fields (all empty).
- `reports/yoast-meta-backup/media-8662-2026-08-15.csv` — pre-write media alt/title
  (`alt_text=""`, `title="#EVN 151"`).

### Approval gate — outcome
All four groups approved as drafted, no revisions requested:
1. Metatags + tags — **approved, applied**.
2. Image alt/title — **approved, applied**.
3. Inbound link from 9099 — **approved, applied**.
4. Inbound link from 8913 — **approved, applied**.

### Applied
- [x] Metatags — title `Zeekr представителство в България %%sep%% %%sitename%%` (33 char body →
  52 rendered) / metadesc (153 chars, quoted above) / focuskw `Zeekr представителство в
  България` — all written, confirmed live via fresh `curl` (title, `<meta name=description>`,
  `wordCount:192` all match).
- [x] Tags written — `[222, 428]` (Zeekr, Zeekr 7X), replacing the empty set.
- [x] `post_content` written (Phase B) — wordCount: 14 → 192.
- [x] Image alt + title written — media 8662: alt "Zeekr представителство в България — обложка
  на епизод EVN151 с логото на Zeekr", title "Zeekr представителство в България — EVN151".
- [ ] Inbound links — **written, then reverted the same day.** Both proposed sources (9099,
  2026-07-09; 8913, 2026-06-20) postdate 8659 (2026-05-19) by 32–51 days — a historical-sequence
  violation the user caught after the write. Both posts were restored to their pre-write
  `content.raw` (verified byte-for-byte identical to the original pull). **No replacement
  inbound link was added** — see below.
- [x] Auto-linked `/tag/` count inside body prose: **3** (`/tag/zeekr` — once per paragraph, ¶1/¶2/¶3
  each link their first "Zeekr" mention). **Finding, not expected**: `docs/SEO_SKILLS_REFACTOR.md`
  §W7 documents the auto-linker as lowered to 1× per tag site-wide 2026-08-14 — live behavior
  here is 1× **per paragraph block**, not 1× per whole `post_content`. Worth a follow-up check
  against `theme/functions.php:75` next time this matters; not fixed in this pass (out of scope
  for a content edit). Separately, `/tag/zeekr-7x` never auto-linked in the body at all — the
  prose says "X7" (matching the transcript's spoken word order) while the tag name is "Zeekr 7X",
  and the auto-linker appears to match on the literal tag string, not variants.

### Declined
None at approval time — all four proposed groups were approved as drafted. The inbound-link
group was corrected *after* approval and write, once the user stated the historical-sequence
rule (source post must predate target); see below.

### Post-hoc correction — inbound links (2026-08-15, same day)
The two approved-and-written inbound links (from 9099 and from 8913) were selected by search
relevance and GSC traffic alone, with no check that the source predates 8659. Both are newer
than 8659. Reverted both:
- **9099** (`/ev-review/zeekr-7x/`) — closing paragraph restored to
  *"Пречката пред по-широко разпространение в момента е само едно: хората не я познават."*
- **8913** (`/publications/zeekr-viziyata-zad-markata-.../`) — 2026-bullet restored to
  *"...покривайки около 90% от континента."*

Both reverts verified byte-for-byte against the pre-write `content.raw`.

**Checked for a compliant replacement** (any Zeekr-mentioning post published before
2026-05-19) and found none usable:
| Candidate | Published | Why it doesn't work |
|---|---|---|
| 7577 (#EV122, "Zeekr в България") | 2025-10-14 | Best topical fit, but **no body prose at all** — just a video embed, identical to 8659's own pre-Phase-B state. No existing paragraph to weave a link into without inventing one from nothing (against Step 4's own mechanics). Also already flagged in `docs/SEO_EV_NEWS_TODO.md` P2 as a duplicate-URL issue with post 7584 — a structural problem worth fixing on its own before this post gets more editorial investment. |
| 4904 (#EVN60, "ZEEKR 001 R в България") | 2024-06-18 | Same problem — bare video embed, no prose. About a discontinued model (001 R) besides. |
| 4397 (#EVN52) | 2024-04-23 | Has real prose, but its only "Zeekr" occurrence is inside a scraped news-roundup table cell (not owned editorial text), and the context is negative (Zeekr registrations *down* that week) — not a natural link home. |

**Net result: 8659 currently has zero inbound links.** The honest fix here is Phase B content
on 7577 (or 4904) first — once either has real paragraphs, one could then carry a genuine,
date-compliant inbound link to 8659. Flagging as a follow-up rather than forcing a bad link
into a stub post or a table cell.

**Update, 2026-08-15 (later same day):** 7577 got its Phase B content and, in that pass,
Phase B initially *did* add exactly the inbound link this section anticipated (7577 → 8659,
old → new, matching the historical-sequence rule above). The user then flagged that this
specific case is a **same-story sequel** — 7577 is the original entry announcement and 8659 is
its confirmed-entry follow-up 7 months later, so an old post linking forward to narrate its own
still-open story's outcome reads as an anachronism, not a citation. Corrected the other way:
the link was removed from 7577, and 8659's own ¶1 now carries the citation instead — "...нещо,
което подкастът [обяви за пръв път] още през октомври 2025 г...." linking back to 7577. This
supersedes the plan above; 8659 still has zero *plain* inbound links by the general rule, but
does now cite its own predecessor. `seo-article-apply/SKILL.md` and
`ev-news-transcript-content/SKILL.md` both got a same-story-sequel exception documenting this.

**Second update, same day:** a follow-up audit of the whole Zeekr push (checking every post
already touched for the same bug) found 8659's ¶2 had the identical pattern pointing at 9099 —
*"...очакват скоро регистриран демо автомобил за **[пълен тест на X7]** → 9099"*. 8659
(2026-05-19) promised a specific future piece by name and linked forward to 9099 (2026-07-09,
not yet published at the time). Removed the link (plain text "пълен тест на X7" now); 9099
carries the citation instead — see its own report's correction section for the exact wording.

### Risks / notes
- Cannibalisation was the main risk this pass — resolved by deliberately choosing
  `Zeekr представителство в България` over any `Zeekr 7X` + Bulgaria/price phrasing, all of
  which post 9099 already owns with real GSC traffic.
- `meta.news_csv` still isn't REST-visible on this post (checked, empty) — worth confirming
  whether the `show_in_rest` registration from 2026-08-14 has reached the live site, next time
  a post needs its CSV read this way. Not blocking here since the Google Sheet tab (`19.05.2026`,
  recreated + backfilled this session) served as an equivalent source.
- The "38 евро/евра" starting price and the exact "3 vs 4 models" count were both left out of
  the published prose (ASR-ambiguous) — if either fact matters editorially, it needs confirming
  from the hosts directly, not from the transcript alone.

### Measurement
Baseline (GSC, 90d ending 2026-08-14): this URL had 2 impressions / 0 clicks / pos 9.0 (branded
query only, no topical impressions). Re-check GSC for this URL and for the keyphrase
`zeekr представителство в българия` in 2–4 weeks (metatags/tags/alt/links) and again at 4–8
weeks (new body content needs re-crawl/re-index time). Ledger rows: `8659-2026-08-15`
(`verify_due` 2026-10-10, Phase B/content) and `8659-2026-08-15-C` (`verify_due` 2026-09-12,
Phase C). `seo-performance-report`'s monthly run picks both up automatically.

---
