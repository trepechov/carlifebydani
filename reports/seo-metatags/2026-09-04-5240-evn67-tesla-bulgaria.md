# SEO Optimization — #EVN67 – Нови спекулации около Tesla Model Y И представителство в България

**URL:** https://www.carlifebydani.com/ev-news/evn67-novi-spekulaczii-okolo-tesla-model-y-i-predstavitelstvo-v-blgariya/ · **Post ID:** 5240 · **Category:** ev-news
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** представителство на тесла в България
**Ledger:** `5240-2026-09-04` (Phase B); `5240-2026-09-04-C` (Phase C)

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode #EVN67 (recorded 2024-08-06, YouTube `y13xKBDvvtc`, 193 chunks
ingested) — one of the oldest posts in this pipeline so far. The title is a
**compound headline, two stories**: (1) Tesla posted on its Facebook page
about building a store and service center in Bulgaria, complying with local
dealer-licensing law (a manufacturer can't sell directly without local civil
entity); (2) speculation about the Model Y "Juniper" redesign — an 800V/
~100kWh battery upgrade the hosts explicitly frame as unlikely in the first
production run, more probably a year+ later, rolled out US→China→Europe.
`post_content` is just the YouTube embed — **owned word count (Yoast
`wordCount`) = 20** — vs. what renders on the page: an H1 and 28 external
news cards (server-rendered from the CSV, same as post 9333's pattern).

**This post was named directly by the user** — it's the inbound-link target
`ev-news-transcript-content` added from post 9333 (`[[seo-ev-news-deep-dive]]`
pipeline), and `seo-article-apply`'s own Step 4 flagged it 2026-09-04 as a
poor inbound-link vehicle *from* 9333 precisely because it has zero owned
prose — this run fixes that gap directly rather than leaving it as a future
TODO.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EVN67 - Нови спекулации около Tesla Model Y И представителство в България - Car Life by Dani` (Yoast title meta empty, falls back to post title) | ~97 chars — well over budget |
| `<meta name=description>` | none rendered — Yoast metadesc empty | 0 |
| Focus keyphrase | none set | — |
| H1 | same as `<title>` (minus suffix) | — |
| Owned word count | 20 (video embed only) | |
| Images without alt | 1 (featured image `#EVN 67 copy`, media id 5241, empty `alt_text`) | |
| Internal links out / in | 0 out / **1 in** (post 9333's ¶1, added 2026-09-04: anchor `спекулациите за V2L при Tesla`) | |

### Demand research
**GSC (90d, this URL):**
| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|
| тесла представителство в българия | 12 | 0 | 0% | 13.4 |
| представителство на тесла в българия | 9 | 1 | 11.1% | 16.6 |
| tesla представителство в българия | 5 | 0 | 0% | 15.4 |
| тесла представителство българия | 1 | 0 | 0% | 12.0 |
(84 total page impressions, 2 clicks, page-level pos 11.1 — the four rows
above account for 27 of the 84; the rest fall below GSC's per-row disclosure
threshold.) **This is a real, already-partially-working page** — a
meaningfully stronger starting position than most EV News posts this
pipeline has touched (most start at zero impressions).

**Google autocomplete (hl=bg, gl=bg):** `тесла представителство в българия` →
`tesla представителство българия`, `официално представителство на тесла в
българия`. `тесла българия` → a rich commercial cluster: `цена`, `работа`,
`кога`, `сервиз`, `варна`, `еоод`, `папагал`, `телефон`, `фирма`. Real,
sustained Bulgarian demand for this exact topic — unlike the V2L case
(post 9333), this is **Cyrillic-phrased demand**, not English-verbatim.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-04):**
| Phrase | Volume/mo | Note |
|---|---|---|
| представителство на тесла в българия | 170 | fresh dataforseo — matches the already-ranking GSC query almost exactly |
| тесла българия | 480 | fresh dataforseo — broad, high-volume, but a hub-level phrase, not this episode's |
| тесла българия сервиз | 10 | fresh dataforseo |
| тесла българия цена | 10 | fresh dataforseo |
| tesla model y juniper | 170 | fresh dataforseo — the ¶3 candidate topic |
| тесла представителство в българия (exact GSC query) | no data | DataForSEO returned nothing for this exact word order; the reordered `представителство на тесла в българия` does carry volume — same intent, treat as one cluster |

All banked to `data/seo-cache/keywords.csv`. 170/mo is a strong number for
this site — most EV News keyphrases researched so far (Cybertruck Bulgaria,
Tesla V2L) sit at 10–50/mo.

**SERP check** (`представителство на тесла в българия`, DataForSEO live,
Bulgaria/bg): **the story moved on since this episode was recorded.** The
current SERP is dominated by 2026-04/05 Bulgarian coverage — money.bg
(2026-04-29, *"Скоро Tesla ще има официално представителство в България"*),
mobilebulgaria.com (2026-04-27, *"Tesla вече има фирма в България"*), and
teslaowners.bg (2026-05-03, on **Tesla BGR EOOD**, the actual registered
entity) — i.e. Tesla's company registration in Bulgaria became real news
**20 months after** this episode's 2024-08-06 Facebook-post speculation.
tesla-service.bg and tar.bg (independent/unauthorized service shops) also
rank, both stating there is *still* no official representation as of their
last update. **This site's own `EV147`/`EV148` episodes (2026-04-21/28,
posts 8351/8396) discuss the actual 2026 company registration** — but
neither is *titled* around it (they're generic multi-topic episode titles),
so **no cannibalisation**: post 5240 remains the only post on this site whose
title is dedicated to this topic, and it's the only reasonable owner of this
keyphrase.

**Important content-accuracy constraint for Phase B:** this post's own
transcript (2024-08-06) only supports the *early* stage of the story — a
Facebook post and host skepticism about timing. **Phase B must not present
2026 developments (the actual company registration) as something this
episode discussed** — that would be sourcing a claim from outside this
episode's transcript, which Step 4 (`no quote, no claim`) forbids. Ground ¶1–¶2
strictly in what was said on 2024-08-06; the keyphrase target is valid
because it's evergreen search intent ("is there a Tesla representation in
Bulgaria"), not because this episode has the final answer.

**News CSV (read via rendered HTML, `news_csv` still not REST-exposed on this
deploy — same trap as post 9333):** 28 stories, row order = editorial
ranking. The Bulgaria-representation and Model Y Juniper stories are **not**
sourced from the external news-card list — both are host-original topics
discussed live (confirmed by the transcript, not present in the card
scrape), consistent with the title itself (compound, not matching any single
card).

### Recommendation
**Focus keyphrase:** `представителство на тесла в България` — already the
dominant driver of this URL's real GSC impressions (27 of 84, across four
close variants, one already converting a click at pos 16.6), carries genuine
measured demand (170/mo, the strongest of any keyphrase this pipeline has
priced), matches confirmed Bulgarian autocomplete phrasing, and has no
competing post on this site. Distance-to-page-1 is realistic — pos 11–17
across variants, not pos 60.
**Secondary:** `тесла българия`, `официално представителство на тесла`,
`tesla model y juniper`

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~97 chars, over budget)* | TBD in Phase C, against real prose | — |
| `_yoast_wpseo_metadesc` | *(empty)* | TBD in Phase C, against real prose | — |
| `_yoast_wpseo_focuskw` | *(empty)* | `представителство на тесла в България` | — |

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Tesla | 4 | 57 (saturated hub, but Tesla **is** the headline entity — justified) |
| Entity | Model Y | 29 | 13 (above the 3–10 band, but literally in the title — established) |
| Entity | България | 398 | 4 (**clean 3–10-band match** — the story's actual subject is Tesla's Bulgaria presence) |

**Gaps:** `Регистрация` (id 327) and `слух` (id 431) both exist but at 1 use
each — below the reuse band, skipped. Neither is a great fit anyway: this
episode's own content is a Facebook-post announcement, not really a rumor,
and "Регистрация" (company registration) is the *2026* story, not what this
episode discusses — using it here would misrepresent the post's own content
even if the tag count were healthier.

**Before Phase B runs, `post_content` is still just the video embed** — per
the tag rule, these three are confirmed by the researched headline/title
itself (Tesla, Model Y, България all appear in the post's own title, which is
as strong a signal as pre-Phase-B evidence gets on this site). Re-check after
Phase B writes ¶1–¶3 that all three are genuinely named in the prose (they
should be, given the two-part headline maps directly onto them).

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `y13xKBDvvtc` — #EVNews - 06.08 - EVN67 Нови
спекулации около Tesla MY И представителство в България (2024-08-06T15:19:56Z)
**Answer found in:** own episode (both halves of the compound headline).

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|
| Tesla posted on its Facebook page about a "new development in Bulgaria" — building a store and service center | "тесла във facebook са писали ново развитие в българия... тесла ще строи магазин и сервиз в българия" | EV67 (own) | [3927s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=3927) |
| Framed as compliance with local law — a manufacturer can't sell cars directly without a registered local legal entity | "отговарящо на местните закони а че трябва да имаш гражданска свързаност за да продаваш коли тъй като нямаш право да продаваш директно като производител автомобили" | EV67 (own) | [3927s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=3927) |
| Location wasn't specified in the post; hosts joked it might be somewhere by the sea | "не пише в софията... да не е по морето някъде" | EV67 (own) | [3927s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=3927) |
| The news triggered a wave of Instagram messages asking if the host (Дани) would become Tesla's Bulgarian brand ambassador | "мен почват да ме заливат в instagram искат да им станеш рекламно лице за българия" | EV67 (own) | [3927s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=3927) |
| Host had earlier posted a story calling the news "too early to happen," then personally verified on Facebook that Tesla really had posted it | "аз тогава споделих едно стори на което казах че според мен това е твърде рано да се случи и след това съответно влязох във facebook и видях че действително тесла са го постнали" | EV67 (own) | [3927s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=3927) |
| Model Y Juniper redesign speculation: possible 800V architecture, ~100kWh battery, more power — but framed as a later-stage upgrade, not in first production units | "ако ти пуснеш в model y juniper всичко 800 волтова батерия 100 kw часа още малко мощност... няма да е в първите бройки най-вероятно ще е след първата година" | EV67 (own) | [1980s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=1980) |
| Rollout order for such a redesign: US first, then China | "най-вероятно първо ще го пус само ако пусна такова ще го пуснат само в щатите после ще го пуснат в китай" | EV67 (own) | [1980s](https://www.youtube.com/watch?v=y13xKBDvvtc&t=1980) |

**Content-accuracy note (per Phase A):** deliberately did **not** reference
the 2026 developments found during SERP research (Tesla BGR EOOD, the actual
2026-04/05 registration coverage) — none of that is in this episode's
transcript, and citing it here would violate "no quote, no claim." ¶1–¶2
describe strictly what the episode itself reported and discussed on
2024-08-06, phrased in the past tense appropriate to an archival news
episode — not as a claim about the present state of Tesla's Bulgarian
presence.

### Draft paragraphs
¶1 (57 words): През август 2024 г. Tesla обяви във Facebook, че ще отвори магазин и сервиз в България — първата крачка към официално представителство на Tesla в България, каквото местният закон изисква, защото производителят няма право да продава автомобили директно, а трябва регистрирано у нас юридическо лице — дотогава пазарът се крепеше на <a href="https://www.carlifebydani.com/ev-masters/kak-se-namirat-avtochasti-za-tesla-v-blgariya-ima-li-monopol-i-kakvi-sa-alternativite/">неофициални сервизи и внос на части</a>.

¶2 (72 words): Постът не уточняваше локацията — водещите се пошегуваха, че може да е някъде по морето — но новината веднага предизвика вълна от съобщения в Instagram, в които зрители питаха дали Дани ще стане рекламно лице на Tesla за България. Малко преди това той сам бе написал сторита, в което определи новината като „твърде рано да се случи“, преди лично да провери във Facebook и да установи, че Tesla наистина я е публикувала.

¶3 (59 words): В същия епизод се коментира и очакван редизайн на Tesla Model Y Juniper — тема, обсъждана и <a href="https://www.carlifebydani.com/ev-news/zashto-sprakha-avtobusite-v-oslo-kakvo-novo-okolo-tesla-my-juniper/">по-рано в канала</a>. Според водещите евентуален преход към 800-волтова архитектура и батерия от около 100 kWh е реалистичен, но по-скоро като по-късен ъпгрейд, не в първите произведени бройки, като подобни промени обикновено стигат първо до пазара в САЩ, а после до Китай.

**Total: 188 words** (target 130–190). Both internal links point to posts
older than this one (1227: 2024-01-27; 1509: 2024-01-09) — no
same-story-sequel risk. 1509 is specifically an *earlier chapter of the same
Model Y Juniper speculation thread*, cited new→old per the sequel rule
(correct direction).

### Facts to confirm before publishing
- [ ] None — every claim in ¶1–¶3 traces directly to an own-episode
  timestamp; no ASR ambiguity on names/numbers this time (Facebook, Instagram,
  800V, 100 kWh, US→China were all clearly recognized, unlike "V2L" on
  post 9333).

### Applied (Step 8/9 verification)
- Written 2026-09-04 via `POST /wp/v2/posts/5240` — full content (existing
  embed + 3 paragraphs) sent, `excerpt` cleared in the same call.
- Yoast schema `wordCount`: **20 → 201** (confirmed live on the re-fetched
  rendered page, not just the API response).
- Paragraphs render after the video embed, before the news-card list; no
  duplicate excerpt text.
- `/tag/` links inside body prose: **0** (no tags assigned yet — Phase C).
- Ledger row `5240-2026-09-04` added (`phase=B`, `changed=content`, baseline
  from the page-level GSC pull above: 84 impr / 2 clicks / 2.4% CTR / pos 11.1
  over the trailing 90d, `verify_due=2026-10-30`, 56 days out).

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change. Current H1 matches the full post title and already
  carries every keyphrase entity (Tesla, представителство, България);
  matches site convention of keeping the episode prefix in H1 even though the
  SEO title strips it.
- [ ] **Subheadings** — none proposed. 201-word, 3-paragraph body matches
  this pipeline's established EV News format.
- [ ] **Image alt + title** — featured image, media id 5241:
  - `alt_text`: *(empty)* → `Представителство на Tesla в България — магазин и сервиз`
  - `title`: `#EVN 67 copy` → `Tesla представителство България`

### Internal links
**Inbound — existing posts that should link here:** one candidate proposed —
post 1227 ("Как се намират авточасти за Tesla в България? Има ли „монопол“ и
какви са алтернативите?", 2024-01-27 — **predates** 5240 by ~6 months, so the
inbound-link date rule is satisfied). Its own ¶1 already describes the exact
problem (unofficial service/parts network, manipulated pricing "за всеки,
който е извън схемата") that this post's news item is a first step toward
solving — a natural forward-looking addendum, not a bolted-on link:

> "...за всеки, който е извън \"схемата\". [...] — проблем, за който Tesla
> обяви стъпка към решение няколко месеца по-късно, когато оповести <a
> href="https://www.carlifebydani.com/ev-news/evn67-novi-spekulaczii-okolo-tesla-model-y-i-predstavitelstvo-v-blgariya/">планове
> за официално представителство в България</a>."

(Full before/after diff shown in the approval gate — only this one clause is
added to post 1227's existing ¶1; every other block untouched.)

Post 4668 ("Цените на авточасти за Tesla MY 2022 в Китай", 2024-05-15, also
predates 5240) was considered but not proposed — one well-justified inbound
link beats two marginal ones, and 1227's opening paragraph is the more
natural fit.

**Outbound — this article should link to:** already covered by Phase B's own
prose — `неофициални сервизи и внос на части` → post 1227, `по-рано в канала`
→ post 1509. No further outbound links proposed.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~97 chars, over budget)* | `Представителство на Tesla в България %%sep%% %%sitename%%` | 36 + 19 suffix = 55 |
| `_yoast_wpseo_metadesc` | *(empty)* | `Tesla обяви във Facebook план за магазин и сервиз в България — крачка към официално представителство. Виж защо е нужно по закон и как реагираха водещите.` | 153 |
| `_yoast_wpseo_focuskw` | *(empty)* | `представителство на тесла в България` | — |

### Proposed tags (carried forward from Phase A, unchanged)
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Tesla | 4 | 57 |
| Entity | Model Y | 29 | 13 |
| Entity | България | 398 | 4 |

Post currently has zero tags (`tags: []`) — this write is additive.

### Applied
- [x] Metatags — title / metadesc / focuskw written; re-fetched rendered
  page confirms `<title>` = `Представителство на Tesla в България - Car Life
  by Dani`, `<meta name="description">` and `og:description` both match the
  drafted 153-char text.
- [x] Tags written — Tesla (4), Model Y (29), България (398).
- [x] `post_content` written (Phase B) — wordCount: 20 → 201.
- [x] Image alt + title written — media id 5241 (confirmed in write
  response: `alt_text` and `title` both updated).
- [x] Inbound links written — target post 1227.
  **Self-caught error during this step**: the first write to post 1227
  dropped the opening `"` before „схемата" (kept only the closing one),
  producing a stray floating quote mark in live body copy. Caught by
  re-fetching `content.raw` immediately after the write and comparing
  against the pre-edit version instead of trusting the diff by eye — the
  mismatch was obvious once compared programmatically. Fixed with a second,
  Python-verified write (checked the exact substring before sending) and
  confirmed correct on the re-fetched rendered page (`&#8222;схемата&#8220;`,
  properly paired, matching the site's existing wptexturize convention — see
  the original title's own `&#8222;монопол&#8220;` for the same pattern).
  **Process note for next time**: Step 4's "diff the before/after string
  yourself before writing" instruction is there for exactly this failure
  mode — construct the edited string programmatically (or paste it back for
  review) rather than hand-retyping surrounding punctuation inline in the
  tool call.
- [x] Auto-linked `/tag/` count inside body prose (re-fetched the live page
  **after** the tag write, all 3 paragraphs inspected directly):
  **Tesla 3×** (¶1, ¶2, ¶3), **България 2×** (¶1, ¶2), **Model Y 1×** (¶3).
  This is well over the "1× per tag" cap `theme/functions.php:107` claims
  (`preg_replace(..., 1)`) — **the live site is not running that code**,
  same undeployed-fix pattern already confirmed for `news_csv` REST exposure
  on post 9333's report. Correcting that report's claim too: 9333 only had
  one bare (non-anchored) "Tesla" mention in its prose, so its "1× found"
  observation never actually distinguished a working cap from an absent one
  — this post is the first real test, and it shows the cap fix is not live.
  Not a defect introduced by this write (the theme, not the content, controls
  this); flagging for the next manual deploy rather than working around it
  in content.

### Declined
_None — all three approval-gate groups (metatags+tags, image alt/title,
inbound link) were approved in full._
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|

### Risks / notes
- No cannibalisation — this remains the only post on the site whose title
  targets Tesla's Bulgarian representation.
- **Content-accuracy note carried from Phase B**: this post intentionally
  narrates only the 2024-08-06 Facebook-announcement stage of an ongoing
  story. The SERP research surfaced that Tesla's actual company registration
  (Tesla BGR EOOD) became real news in 2026-04/05, discussed on this site's
  own EV147/EV148 episodes (posts 8351/8396) but not as their title focus.
  **Follow-up worth flagging to the user**: if either of those posts gets
  its own optimization pass later, its Phase B should consider citing back
  to 5240 (new→old, correct direction) as the origin of the representation
  story — not proposed or written here, out of scope for this run.
- Post 1227's own inbound edit is now live production copy on a second post
  — noted here since Phase C touches two posts' history, even though only
  5240 is "the" post being optimized this run.

### Measurement
Baseline (GSC, 90d ending 2026-09-03): 84 impr / 2 clicks / 2.4% CTR / pos
11.1 (page-level); `тесла представителство в българия` pos 13.4 (12 impr),
`представителство на тесла в българия` pos 16.6 (9 impr, 1 click). Re-check
in 2–4 weeks for the metatag/tag/alt/inbound-link effect (ledger
`5240-2026-09-04-C`, `verify_due` 2026-10-02) and again at 4–8 weeks for the
new body content to be crawled/indexed (ledger `5240-2026-09-04`,
`verify_due` 2026-10-30).
