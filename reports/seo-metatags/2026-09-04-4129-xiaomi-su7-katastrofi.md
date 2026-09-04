# SEO Optimization — #EVN51 – Защо Xiaomi SU7 катастрофира толкова често?

**URL:** https://www.carlifebydani.com/ev-news/evn51-zashto-xiaomi-su7-katastrofira-tolkova-chesto/ · **Post ID:** 4129 · **Category:** ev-news
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Xiaomi SU7 катастрофа
**Ledger:** `4129-2026-09-04` (Phase B); `4129-2026-09-04-C` (Phase C)

**How this post surfaced:** it's a [[docs/SEO_BACKLINK_TARGETS_TODO.md]] entry
— post 9348 (already-optimized #EVN163) links out to this post, giving it
fresh inbound link equity it hadn't earned on its own yet.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode #EVN51 (recorded 2024-04-16, YouTube `crcST7-orbU`, 174 chunks
ingested) — one of the **oldest back-catalogue posts** touched by this
pipeline so far, from before the site switched to the remote-CSV news-card
system. The headline story (per the title, and a genuine table row: *"Xiaomi
SU7 катастрофи"*, sourced from a viral TikTok/YouTube compilation) is a
reaction to a string of Xiaomi SU7 crash videos circulating at the time —
this predates the site's real Bulgarian crash-news coverage from 2025
(автопилот/autopilot-blamed fatal crashes, recall news) found during SERP
research below, so the episode itself is reacting to early viral clips, not
the later confirmed incidents.

**`post_content` here is a different vintage than every other post this
pipeline has touched**: instead of a thin embed + remote CSV, this one has
the **entire 73-row news table hard-coded as raw HTML inside `post_content`
itself** (`<!-- wp:html -->` block, a `<table>`). Yoast's schema `wordCount`
reads **2,088** — but this is table-cell text (link titles + one-line blurbs
for 73 external stories), not narrative prose that answers the page's own
title question. **Confirmed zero `wp:paragraph` blocks anywhere in
`post_content`** — the honest "does this page answer its own headline"
word count is 0, same starting point as every other thin EV News post, just
disguised by a large `wordCount` number. Phase B is still fully needed here;
don't let the raw wordCount read as "already has content."

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EVN51 - Защо Xiaomi SU7 катастрофира толкова често? - Car Life by Dani` (Yoast title empty, falls back to post title) | ~74 chars |
| `<meta name=description>` | none rendered — Yoast metadesc empty | 0 |
| Focus keyphrase | none set | — |
| H1 | same as title (minus suffix); jumps straight to H3 (site-wide theme gap, not this post's issue) | — |
| Owned word count | **0** narrative (2,088 raw Yoast count is the embedded news table, see above) | |
| Images without alt | 1 (featured image `EVN_51`, media id 4130, empty `alt_text`) | |
| Internal links out / in | 0 out / **1 in** (post 9348's own content links here as an outbound reference, added when 9348 was optimized) | |

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, page-level, this URL):** 14 impressions,
0 clicks, 0% CTR, **position 7.1**. Query-level breakdown didn't surface in
a `query,page` pull at `row_limit=5000` — below GSC's per-row disclosure
threshold at this volume, same situation as several other posts in this
project. Real, if tiny — this is not a cold start.

**Google autocomplete (hl=bg, gl=bg):** `xiaomi su7 катастрофа` → **zero
completions**. `xiaomi su7 произшествие` → zero. `xiaomi su7` bare → strong
cluster: `ultra`, `ultra цена`, `цена`, `цена българия`, `max`, `ultra
0-100`. The "crash" framing itself has **no autocomplete signal** — real
demand clusters around specs/price, not incidents.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-04):**
| Phrase | Volume/mo | Note |
|---|---|---|
| xiaomi su7 | 1,600 | fresh dataforseo — real, substantial volume for this site |
| xiaomi su7 ultra | 1,600 (spiked to 8,100 in 2026-04) | fresh dataforseo |
| xiaomi su7 цена | 320 | fresh dataforseo |
| xiaomi su7 цена българия | 140 | fresh dataforseo |
| xiaomi su7 катастрофа / защо xiaomi su7 катастрофира | no data | queried, DataForSEO returned nothing measurable for either — matches the autocomplete finding |

All banked to `data/seo-cache/keywords.csv`. **The crash-specific angle has
no measured keyword-tool volume at all** — but see the SERP check below for
why that's not the same as "no demand."

**SERP check** (`xiaomi su7 катастрофа`, DataForSEO live, Bulgaria/bg): a
**real, rich Bulgarian SERP** — AI Overview, then genuine BG auto-news
coverage (autobild.bg *"виновен ли е автопилотът за катастрофата?"*,
investor.bg, topgear.bg, myve.bg — all covering actual 2025 fatal
incidents/recalls), a "short videos" carousel of crash clips, and — **this
site's own YouTube video for this exact episode already ranks organically
at position 8**, title verbatim *"Защо Xiaomi SU7 катастрофира толкова
често?"*, 8.3k views. This is strong evidence the phrase has genuine
searcher intent-match in this market even though the keyword-volume tools
show nothing measurable for it — the GSC page's own 14 impressions at
position 7.1 corroborates this independently. **Absent tool-volume is not
evidence of no demand here — see the site-wide decision rule.**

**Cannibalisation check:** `/wp/v2/search?search=Xiaomi SU7` → only this
post itself, post 9348 (the already-optimized episode that links here), and
two unrelated episodes with no SU7 focus. Clear to proceed.

### Recommendation
**Focus keyphrase:** `Xiaomi SU7 катастрофа` — matches the article's actual
content (the crash-compilation reaction story), matches the SERP's own
dominant phrasing (every ranking BG competitor uses "катастрофа" singular,
not plural), and the page already earns real GSC impressions at a
reachable position (7.1) for presumably this or a close variant. Volume
tools show nothing for the exact phrase, but the site's own YouTube video
already ranking on page 1 for it is direct, un-ignorable evidence of real
search intent this page can serve.
**Secondary:** `xiaomi su7` (bare, 1,600/mo — real volume, if Phase B's
content also touches specs/context beyond the crash angle), `xiaomi su7
ultra` (the specific trim most crash coverage centers on, per SERP).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~74 chars, over budget, carries the #EVN51 prefix)* | TBD in Phase C, against real prose | — |
| `_yoast_wpseo_metadesc` | *(empty)* | TBD in Phase C, against real prose | — |
| `_yoast_wpseo_focuskw` | *(empty)* | `Xiaomi SU7 катастрофа` | — |

(Placeholder only — Phase C should not write final copy against a post with
zero narrative prose, per the orchestrator's own known trap.)

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Xiaomi | 258 | 1 (below the 3–10 band, but already attached and is the exact headline brand of this article — same reasoning as `Zeekr 7X` on post 9099: a post's own subject tag isn't the dilution the band rule exists to stop) |

**Gaps:** no existing tag for `SU7` (the specific model) or any
crash/incident-adjacent keyword-intent term (`Инцидент`, `Безопасност`,
`Автопилот` — checked, none exist). Not creating speculatively; noting here
in case a future Xiaomi/SU7 or ADAS-incident post makes a batch-create
worthwhile.

**Before Phase B runs, `post_content` has zero narrative prose** (the huge
embedded table doesn't count) — so per the tag rule, `Xiaomi` is the only
entity confirmed at this stage. Re-check after Phase B writes ¶1–¶3 whether
the transcript also substantively names other brands/models worth
evaluating (the table's Xiaomi factory story mentions no other EV brands
directly relevant here, but the episode itself covers other stories too —
check what Phase B actually finds).

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `crcST7-orbU` — #EVNews - 16.04 - #EVN51 - Защо Xiaomi
SU7 катастрофира толкова често? (2024-04-16T06:05:55Z)
**Answer found in:** own episode.

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|
| Hosts start the episode with SU7 "since it's on the cover" — a cluster of crash clips appeared in the two weeks after the car hit the roads | "трябва да почнем с су 7 все пак той е на корицата... в последните две седмици откакто су се почна да се появява по пътищата почнаха да се появяват много катастрофи с xiaomi su 7" | EV51 (own) | [1975s](https://www.youtube.com/watch?v=crcST7-orbU&t=1975) |
| First clip: car misjudges the speed needed for a turn (expected ~80 km/h) and hits something at ~65 km/h | "с каква скорост очакваш че той би могъл да завие еми 80... ей това е удар с 65" | EV51 (own) | [1975s](https://www.youtube.com/watch?v=crcST7-orbU&t=1975) |
| Second clip (TikTok): a journalist's car spins out making an ordinary right turn exiting an intersection | "имаме още едно клипче за su 7... някакъв журналист който излиза от една от едно кръстовище и я директно я върти" | EV51 (own) | [2462s](https://www.youtube.com/watch?v=crcST7-orbU&t=2462) |
| Hosts can't say whether the cause is a low-quality component or a traction-control problem — but note 3-4 crashes with this car in one week | "не мога да кажа дали има некачествен компонент или има недобра работа на тракшън контрола или не знам какво точно се случва там обаче за една седмица някак си три-четири катастрофи с тая кола" | EV51 (own) | [2462s](https://www.youtube.com/watch?v=crcST7-orbU&t=2462) |
| Joke: Xiaomi produces one SU7 every 76 seconds — faster than people manage to crash them | "xi произвеждат една su семица на всеки 76 секунди тоест им отнема по-малко време да ги произведат отколкото хората да ги забият челно някъде" | EV51 (own) | [2462s](https://www.youtube.com/watch?v=crcST7-orbU&t=2462) |
| Same episode also covers Chinese-brand battery innovation news — BYD's Blade battery being adopted across other marque brands, more development expected from NIO | "това което b id прави и го интегрира в други марки като батерии е страхотно blade батерията е много добра... тука се цитират още доста марки като например nio" | EV51 (own) | [6169s](https://www.youtube.com/watch?v=crcST7-orbU&t=6169) |

**ASR note:** "SU7" is transcribed inconsistently ("su 7", "su семица" —
literally "SU-piece/unit") throughout; no ambiguity about which car is meant
in context, so no hedging needed. The exact split between "40% faster" style
production-rate framing wasn't quoted verbatim as a stat — the 76-second
figure is a fact from the table's own Xiaomi-factory story, which the hosts
reference directly, not something they measured themselves.

### Draft paragraphs
¶1 (45 words): През април 2024 г., само две седмици след като Xiaomi SU7 излезе по пътищата, в мрежата плъзнаха десетки клипове с катастрофи на модела. Водещите разглеждат няколко от тях, за да предположат защо всяка следваща Xiaomi SU7 катастрофа се случва толкова бързо след старта на продажбите.

¶2 (78 words): В един клип колата навлиза в завой със скорост, за която е нужно рязко забавяне до около 80 км/ч, но удря препятствие все още на около 65 км/ч; в друг — кола на журналист буквално се завърта на кръстовище при обикновен десен завой. Водещите не са сигурни дали причината е некачествен компонент или проблем с тракшън контрола, но се пошегуват, че Xiaomi произвежда по един SU7 на всеки 76 секунди — по-бързо, отколкото хората успяват да ги разбият.

¶3 (57 words): В същия епизод се коментират и очаквани иновации в батериите на китайските марки — BYD вече внедрява своята Blade батерия и в други марки от концерна, а се очакват нови разработки и от NIO. Темата е част от <a href="https://www.carlifebydani.com/publications/kitajskite-elektromobili-sa-tuk-za-da-ostanat/">по-широкия разказ защо китайските електромобили остават на пазара за постоянно</a>, на който посветихме отделен преглед само два дни по-рано.

**Total: 180 words** (target 130–190). Only one internal link this time —
searched for a second candidate (Tesla Autopilot-lawsuit angle from a
different transcript segment, BYD Blade battery, traction-control) and found
nothing else on this site that's both genuinely topical and correctly dated;
better to ship one real link than force a second. The one link (→ post
4115, 2024-04-14, predates this post by 2 days) is already
**optimized** (Phase C applied earlier today, ledger `4115-2026-09-04-C`) —
per the backlink-tracking trap, no `docs/SEO_BACKLINK_TARGETS_TODO.md`
update needed for this link.

### Facts to confirm before publishing
- [ ] None — every claim in ¶1–¶3 traces directly to an own-episode
  timestamp; the only ASR quirk ("su семица" for SU7) doesn't create any
  factual ambiguity.

### Applied (Step 8/9 verification)
- Written 2026-09-04. **Mechanics note:** given the payload size (33,888
  chars — existing embed + the 73-row legacy news table + 3 new paragraph
  blocks), built the full `content` string programmatically in Python from
  the exact previously-fetched `content.raw` (never re-typed through a
  Read-tool rendering), verified locally (table row count unchanged, byte
  ranges outside the insertion point untouched) before sending, then POSTed
  directly via `curl` with the `seo-bot` app password from Keychain rather
  than pasting 33KB into a tool call — same recovery pattern documented on
  post 7631's write incident. Confirmed the live response's `content.raw`
  byte-identical to the sent payload before considering this done.
- Yoast schema `wordCount`: **2,088 → 2,263** (+175, close to the 180-word
  draft; the small gap is normal tokenization noise). The absolute number is
  still dominated by the legacy 73-row table — the honest "does the page
  answer its own question" delta is 0 → 3 real paragraphs, confirmed on the
  live rendered page.
- All 3 paragraphs render in order immediately after the video embed and
  before the (unchanged) news table; `excerpt` empty.
- `/tag/` links inside the new prose: **0** — `Xiaomi` (id 258) is the only
  tag on this post and doesn't appear as bare text in any of ¶1–¶3 (every
  mention is "Xiaomi SU7" as a compound, and the theme's auto-linker matches
  on the exact tag name "Xiaomi" only — worth re-checking once Phase C
  confirms the final tag set).
- Ledger row `4129-2026-09-04` added (`phase=B`, `changed=content`, baseline
  from Phase A's GSC pull: 14 impr / 0 clicks / 0% CTR / pos 7.1 over
  2026-06-06→2026-09-03, `verify_due=2026-10-30`, 56 days out).
- Internal link to post 4115 confirmed present; per the backlink-tracking
  trap, 4115 is already optimized (ledger `4115-2026-09-04-C`) — no
  `docs/SEO_BACKLINK_TARGETS_TODO.md` update needed for this link.

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change proposed. Current H1 already carries the keyphrase
  entities naturally; matches site convention of keeping the episode prefix.
- [ ] **Subheadings** — none proposed. 3-paragraph intro matches this
  pipeline's established EV News format; the legacy news table below it is
  unaffected either way.
- [ ] **Image alt + title** — featured image, media id 4130:
  - `alt_text`: *(empty)* → `Xiaomi SU7 катастрофа — защо моделът се удря толкова често`
  - `title`: `EVN_51` → `Xiaomi SU7 катастрофа`

### Internal links
**Inbound — existing posts that should link here:** none proposed. Checked
`/wp/v2/search` for "тракшън контрол автопилот" and the keyphrase directly —
this is one of the oldest posts touched by this pipeline (2024-04-16); no
older post on this site predates it with genuine topical overlap. Per the
date rule, zero proposed rather than reaching for a newer post.

**Outbound — this article should link to:** already covered by Phase B's
own prose (→ post 4115, the China-EV-battery piece). No further outbound
links proposed.

### Proposed metatags (final, against the real 180-word prose)
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~74 chars, over budget, carries the #EVN51 prefix)* | `Xiaomi SU7 катастрофа: какво не е наред %%sep%% %%sitename%%` | 39 + 19 suffix = 58 |
| `_yoast_wpseo_metadesc` | *(empty)* | `Седмици след старта на Xiaomi SU7 се появиха десетки катастрофи. Разгледахме клипове с удар при 65 км/ч и питаме виновен ли е тракшън контролът.` | 144 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Xiaomi SU7 катастрофа` | — |

### Proposed tags (carried forward from Phase A, unchanged)
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Xiaomi | 258 | 1 (below band, but the article's own headline brand — see Phase A reasoning) |

Post's current tag set is `[258]` (Xiaomi already attached) — no change to
tags this pass.

### Applied
- [x] Metatags — title / metadesc / focuskw written and confirmed live
  (re-fetched rendered page: `<title>` and `<meta name="description">` both
  match exactly).
- [x] Tags — unchanged (Xiaomi already attached; no add/drop this pass).
- [x] `post_content` written (Phase B) — wordCount: 2,088 → 2,263 (the raw
  number stays dominated by the legacy embedded table; owned narrative
  prose went 0 → 3 real paragraphs).
- [x] Image alt + title written — media id 4130 (confirmed in write
  response: `alt_text` and `title` both updated).
- [ ] Inbound links written — none proposed this pass (no older post on the
  site has genuine topical overlap; see Internal links above).
- [x] Auto-linked `/tag/` count inside body prose: **2×** (`Xiaomi`, in both
  ¶1 and ¶2) — confirms, same as post 5240's finding, that the theme's
  1×-per-tag cap fix is **not deployed live**. Not something this run
  introduced or can fix from the content side.

### Declined
_None — both proposed groups (metatags, image alt/title) were approved in
full._

### Risks / notes
- No cannibalisation risk — no other post targets `Xiaomi SU7 катастрофа`.
- This post's `post_content` retains a large legacy HTML table (73 rows of
  external news links) that Yoast counts toward `wordCount` — future audits
  of this post (or similarly old back-catalogue posts) should not read a
  high `wordCount` as evidence of real owned content; check for
  `wp:paragraph` blocks specifically.
- SERP research found the crash-specific angle has no measured
  keyword-tool volume, but this site's own YouTube video for this episode
  already ranks on page 1 for the near-exact phrase — the win here is
  converting existing off-site visibility into on-site traffic, not
  chasing fresh volume.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): 14 impr / 0 clicks / 0% CTR / pos
7.1 (page-level). Re-check in 2–4 weeks for the metatag/alt effect (ledger
`4129-2026-09-04-C`, `verify_due` 2026-10-02) and again at 4–8 weeks for
the new body content to be crawled/indexed (ledger `4129-2026-09-04`,
`verify_due` 2026-10-30).
