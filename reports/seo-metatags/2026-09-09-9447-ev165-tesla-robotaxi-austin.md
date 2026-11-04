# SEO Optimization — #EV165 – ROBOTAXI на Tesla тръгна! Първите пътувания в Austin

**URL:** https://www.carlifebydani.com/ev-news/ev165-robotaxi-ha-tesla-tragna-parvite-patuvaniya-v-austin/ · **Post ID:** 9447 · **Category:** ev-news
**Prepared:** 2026-09-09 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Tesla Robotaxi Austin
**Ledger:** `9447-2026-09-09` (Phase B); `9447-2026-09-09-C` (Phase C)

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode #EV165 (published 2026-09-08, YouTube `Z9W3orjINIg`, 145 chunks
ingested — resolved via `tools/resolve_episode.py`). Post/video title names
the headline story as Tesla's Robotaxi service beginning its first paid rides
in Austin, Texas — a distinct headline story, not one of the 20 rows in the
episode's own news-card list (the CSV's closest related rows are #11 "Tesla
adds special shifts at Giga Berlin", #13 "Tesla opens Cybercab fleet-sale
search" and #16 "Tesla Robotaxi passes 1M unsupervised miles" — background
context for the same rollout, not the headline event itself). `post_content`
is just the YouTube embed — **owned word count (Yoast `wordCount`) = 19** —
vs. what renders on the page: an H1, the video, and 20 external news cards.
Post is 1 day old at research time.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EV165 - ROBOTAXI HA TESLA ТРЪГНА! Първите пътувания в Austin - Car Life by Dani` (Yoast title meta empty, falls back to post title) | ~85 chars, well over budget |
| `<meta name=description>` | none rendered — Yoast metadesc empty, no fallback | 0 |
| Focus keyphrase | none set | — |
| H1 | `#EV165 - ROBOTAXI HA TESLA ТРЪГНА! Първите пътувания в Austin` | — |
| Owned word count | 19 (video embed only; heading outline jumps H1→H5, known theme gap) | |
| Images without alt | featured image (media 9448, empty `alt_text`, title `#EVN 165`) + 20 of 20 news-card thumbnails (empty `src`, JS-hotloaded) | |
| Internal links out / in | 0 / 0 (`/wp/v2/search?search=Tesla Robotaxi Austin` returns only this post — no cannibalisation, no existing inbound link either) | |

### Demand research
**GSC:** post published 2026-09-08 (yesterday) — too new for a meaningful
pull; skipped rather than burning the 90d `query,page` token cost on a page
that hasn't been re-crawled/indexed yet. Baseline for the measurement plan
will be effectively zero across the board.

**Google autocomplete (hl=bg, gl=bg):**
- `tesla robotaxi` → `tesla robotaxi tracker`, `tesla robotaxi san francisco`,
  `tesla robotaxi news`, **`tesla robotaxi austin`**, `tesla robotaxi price`,
  `tesla robotaxi accident`, `tesla robotaxi launch`, `tesla robotaxi vs waymo`
- `robotaxi tesla` → same cluster, `tesla robotaxi austin` again, `...price`,
  `...for sale`, `...app`
- `тесла robotaxi` (Cyrillic seed) → identical English completions — no
  Cyrillic expansion exists, confirming BG searchers type this in Latin
- `tesla austin` → `tesla austin robotaxi`, `tesla austin robotaxi map`
- `роботакси` (bare Cyrillic) → `роботакси тесла`, `роботакси в сша`,
  `роботакси waymo` — the one genuinely Cyrillic entry point, but generic
  (not Austin-specific)

`tesla robotaxi austin` recurring across two independent seeds is the
strongest signal here — real, specific demand, not a one-off suggestion.

**Keyword metrics (bg, DataForSEO `search_volume/live`, fresh 2026-09-09):**
| Phrase | Volume/mo | Note |
|---|---|---|
| tesla robotaxi | 40 | fresh dataforseo |
| tesla robotaxi austin | 10 | fresh dataforseo |
| роботакси тесла | 10 | fresh dataforseo |
| tesla robotaxi tracker | 10 | fresh dataforseo |

All banked to `data/seo-cache/keywords.csv`. Floor-level volume is expected —
this is a live-rolling-out service (Austin/Dallas/Houston/Miami/Orlando/Tampa
per SERP), not a one-time launch event with a volume spike; BG-market volume
for a US-only service will stay low regardless.

**SERP check** (`tesla robotaxi austin`, DataForSEO live, Bulgaria/bg): two
Tesla-owned pages (rank 1–2), Wikipedia (rank 3), then PAA (*Where is Tesla's
robotaxi available? Is Robotaxi available in Austin? How much does a Tesla
robotaxi cost? How to get a Tesla robotaxi in Austin?*), a YouTube video
carousel, then WSJ / Reddit / Yahoo Finance / Business Insider / Motor1 —
all English-language global news/tech coverage. **No Bulgarian-language
competitor exists for this phrase at all** — the entire SERP is English
global authority sites. A BG-language article can't outrank tesla.com or
Wikipedia for the bare English phrase, but there is zero competition for a
Bulgarian-language treatment of the same story, which is the actual
opportunity (matches the `tesla model y v2l` pattern from post 9333: low
volume, zero BG competitors, real GSC pickup once indexed).

**GA4:** not pulled — page is 1 day old, no landing-page sessions to speak of yet.

**News CSV (EV News, 12-column, fetched via `meta.news_csv` — REST-exposed
and working on this post, unlike 9333/9248 where the fix wasn't yet
deployed):** 20 rows, row order = editorial ranking. Top of the list (not
the chosen headline, which is the video's own Tesla Robotaxi Austin story):
Australia EV market share record 24.9% (#1), Glencore mine EVs in Sudbury
(#2), used-EV market growth (#3), Polestar 4 SUV variant (#4), EU EV sales
record (#5). Three Tesla-adjacent rows exist further down (#11 Giga Berlin
shifts, #13 Cybercab fleet-sale search, #16 Robotaxi 1M-mile milestone) —
candidate ¶3 "also this episode" material if the transcript covers them,
since they're the same Tesla-Robotaxi story arc as the headline rather than
unrelated brands.

### Recommendation
**Focus keyphrase:** `Tesla Robotaxi Austin` — matches the post's own
headline story exactly, recurs across two independent autocomplete seeds
(genuine demand, not a guess), has a real if small measured volume (10/mo
exact-match, 40/mo for the broader `tesla robotaxi`), faces zero Bulgarian
competition on the SERP, and no cannibalisation risk (no other post on this
site targets it). Distance-to-page-1 doesn't apply yet (0 GSC history, 1 day
old) — this is a fresh-content play, not a repositioning of an existing
ranking.
**Secondary:** `tesla robotaxi`, `роботакси тесла`, `Tesla Cybercab Austin`

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~85 chars)* | `Tesla Robotaxi Austin — първите пътувания %%sep%% %%sitename%%` | 41 + 19 suffix = 60 |
| `_yoast_wpseo_metadesc` | *(empty)* | *(directional draft — to be finalized in Phase C against real body prose, per the orchestrator's known trap of writing metadesc against a 19-word placeholder)* | — |
| `_yoast_wpseo_focuskw` | *(empty)* | `Tesla Robotaxi Austin` | — |

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Tesla | 4 | 59 (saturated hub, but Tesla **is** the headline entity here — justified) |
| Entity | Robotaxi | 252 | 3 (squarely in the 3–10 band) |

**Gaps:** no existing tag for `Austin`, `Cybercab`, or `Автономност`
(autonomy) — checked, zero results for each — skip rather than create
speculatively. Worth watching whether more EV News episodes cover the
Austin/Cybercab rollout in coming weeks; if so, batch-create deliberately
rather than ad hoc, same pattern as the `V2L` gap noted on post 9333.

**Before Phase B runs, `post_content` is still just the video embed**, so
per the tag rule in `seo-keyphrase-research` Step 4b, Tesla and Robotaxi are
the only entities confirmed named in owned prose. Once Phase B writes ¶1–¶3,
re-check whether the ¶3 "also this episode" Tesla-adjacent stories (Giga
Berlin shifts, Cybercab fleet-sale search, 1M-mile milestone) surface any
additional in-band tag — none of the brands involved differ from Tesla
itself, so no new entity tag is expected, but the check still applies per
the standing rule.

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `Z9W3orjINIg` — #EVNews - 09.08 - EV165 - ROBOTAXI HA
TESLA ТРЪГНА! Първите пътувания в Austin (2026-09-08T15:42:54Z)
**Answer found in:** own episode. The episode itself is conversational
riffing on the Cybercab/Robotaxi rollout rather than a formal news read —
no single clean "we launched paid rides today" statement exists in the
transcript, but multiple chunks ground specific, real details about the
service and the hosts' reaction to it. `search_transcripts` scoped to
`video_id` worked correctly this time (unlike the filter bug hit on 9333) —
three separate queries against just this episode returned relevant, on-topic
chunks.

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|
| Fleet is small (hosts estimate "roughly a hundred" cars, hedged) but has already visibly hurt Uber's business in Austin within days | "нямат много коли... ама май са към стотина... А и доколкото виждам, а много тежко са ударили Uberър и то за няколко дни" | EV165 (own) | [3877s](https://www.youtube.com/watch?v=Z9W3orjINIg&t=3877) |
| Car-finding system: app colors the on-screen arrow, the Cybercab's own LED bar blinks the matching color, so a rider can pick out their car among several queued at once | "В апликацията ти оцветява в определен цвят а стрелката... и лед бара отпред светло мига в тоя цвят... ако има наредени примерно 10 сайбър каба за 10 различни човека, те са от 10 различни цвята" | EV165 (own) | [3877s](https://www.youtube.com/watch?v=Z9W3orjINIg&t=3877) |
| Viral short-form clips tour the car from the moment it arrives; recurring complaints are doors opening too wide (blocking traffic when stopped curbside) and a one-piece seat that slides as a whole, not split in two halves | "Линкът... е точно на такъв тур, който показва какво се случва от момента, в който вече роботаксито е дошло... хората се оплакват от вратите, че... блокира трафика... тази седалка е цяла... без да са две половинки" | EV165 (own) | [3710s](https://www.youtube.com/watch?v=Z9W3orjINIg&t=3710) |
| Tesla now gives a Cybercab loaner voucher while your car is in for service, instead of a traditional loaner car sitting idle — keeps the robotaxi fleet working nonstop instead | "като си оставиш колата в сервиза, просто ще ти дават един ваучер за сайбъркапа... няма да държат едни коли във всички представителства, които да седят там" / "тая кола работи постоянно" | EV165 (own) | [4770s](https://www.youtube.com/watch?v=Z9W3orjINIg&t=4770) |
| Hosts recall Musk's earlier promise that these cars would sell for $29,999, with visible skepticism about the timeline | "Иванън Мъск е казал, че до 2026 тея коли ще се продават за 29,99[9]... той по-скоро се е обзаложил" | EV165 (own) | [4770s](https://www.youtube.com/watch?v=Z9W3orjINIg&t=4770) |

### Draft paragraphs
¶1 (48 words): Tesla Robotaxi Austin бе основната тема в епизода — водещите разказаха как флотата от Cybercab, макар да наброява по техни думи „май стотина“ коли, само за няколко дни осезаемо удари бизнеса на Uber в града. Хайпът около новата услуга обяснява защо толкова малко коли вече правят такава разлика.

¶2 (69 words): Специално внимание получи системата за разпознаване на колата: приложението оцветява стрелката на екрана, а светодиодната лента на Cybercab мига в същия цвят, за да познаеш колата си сред няколко чакащи таксита. От вирусните клипове с обиколка на купето водещите цитираха и оплаквания — вратите се отварят твърде широко и блокират трафика на спряла кола, а седалката е един детайл, който се плъзга напред-назад цял, без разделяне на две половини.

¶3 (61 words): Водещите разказаха и по-практическа подробност: докато колата ти е на сервиз, Tesla вече дава ваучер за Cybercab вместо класическа кола под наем — така флотата работи постоянно, вместо да стои паркирана в представителството. Припомниха и <a href="https://www.carlifebydani.com/publications/eln-msk-obyavi-che-tesla-shhe-predstavi-svoeto/">обещанието на Илон Мъск</a> цената на тези коли да стигне 29 999 долара до 2026 г., но с известен скептицизъм дали това изобщо ще се случи.

**Total: 178 words** (target 130–190).

**Internal link:** one outbound link, ¶3 → post 3946 ("Tesla ще представи
Robotaxi на 8ми Август", `publications`, 2024-04-05) — the original Robotaxi
reveal announcement, which itself already discusses the $25k budget-car
platform sharing that Musk's $29,999 promise refers to. Older than this post
(no same-story-sequel risk — it narrates an earlier chapter, this post
narrates the current one). A second link was deliberately not forced:
`/wp/v2/search` for `Cybercab`, `Waymo`, `самоуправляващ се автомобил` and
FSD-adjacent terms surfaced only thin embed-only EV News posts (4792, 8004,
4002 — no owned prose to make a natural anchor) or topically unrelated
publications pages — better to ship one well-grounded link than pad a second.
**Backlink-target check:** post 3946 has no row in
`reports/seo-optimizations/ledger.csv` (not yet through this pipeline) and
0 impressions in the 90d page-level GSC pull (2026-06-11→09-08, cached
`gsc:page-only|90d|2026-09-08`) — added to
`docs/SEO_BACKLINK_TARGETS_TODO.md`.

### Facts to confirm before publishing
- [ ] **Fleet-size figure ("около стотина")** — hosts hedge this themselves
  ("май са към стотина"); kept as a paraphrased hedge in ¶1 rather than a
  hard number, per the ASR-ambiguity rule.
- [ ] **$29,999 price figure** — transcript renders it as "29,99" with the
  final digit cut off before a new sentence starts; reconstructed as
  $29,999 because that specific number is Musk's long-public Cybercab/robotaxi
  price target (matches the "$25,000 budget car" figure already documented
  in post 3946 from 2024). Flagging in case the user wants to double check
  against the source video before publishing.

### Applied
- Written 2026-09-09 via `POST /wp/v2/posts/9447` — full content (existing
  embed + 3 paragraphs, one outbound link) sent, `excerpt` cleared in the
  same call.
- Yoast schema `wordCount`: **19 → 191** (confirmed in the live JSON-LD
  response, not just the API echo).
- Paragraphs render after the video embed, before the news-card list; no
  duplicate text in the excerpt slot (auto-generated trimmed excerpt from
  `post_content`, expected/fine).
- `/tag/` links inside the new prose: **0** — post has zero tags assigned
  yet (that's Phase C's job); nothing to auto-link against.
- Ledger row `9447-2026-09-09` added (`phase=B`, `changed=content`,
  baseline effectively zero — page is 1 day old, `verify_due=2026-11-04`,
  56 days out for the new body text to be crawled/indexed).
- `docs/SEO_BACKLINK_TARGETS_TODO.md` updated with post 3946 (Backlog row:
  0 impr in the 90d page-level pull, source 9447 outbound, not yet through
  this pipeline).

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change proposed. Current H1 (`#EV165 - ROBOTAXI HA TESLA
  ТРЪГНА! Първите пътувания в Austin`) already carries the keyphrase and
  matches this site's convention of keeping the episode prefix in H1 even
  though the SEO title strips it.
- [ ] **Subheadings** — none proposed. Body is 3 short paragraphs (191 words),
  matching the established EV News format; no H2/H3 pattern exists on
  comparable applied posts to match.
- [ ] **Image alt + title** — featured image, media id 9448:
  - `alt_text`: *(empty)* → `Tesla Robotaxi Austin — Cybercab на улиците на Austin`
  - `title`: `#EVN 165` → `Tesla Robotaxi Austin`

### Internal links
**Inbound — existing posts that should link here:**
| Source post | URL | Anchor text | Where |
|---|---|---|---|
| 3946 (2024-04-05, `publications`, predates this post) | /publications/eln-msk-obyavi-che-tesla-shhe-predstavi-svoeto/ | „днес Robotaxi вече вози пътници в Austin“ | Appends to the existing ADAS/FSD paragraph, whose closing claim ("Tesla currently cannot operate without human intervention") is now factually outdated — a natural editorial update, not a bolted-on link. |

**Outbound — this article should link to:** already covered by Phase B's own
prose (¶3 → post 3946, "обещанието на Илон Мъск"). No further outbound links
proposed — `/wp/v2/search` for `Cybercab`, `Waymo`, `самоуправляващ се
автомобил` surfaced only thin embed-only EV News posts or unrelated
publications pages (see Phase B's note above).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~85 chars)* | `Tesla Robotaxi Austin: първите пътувания %%sep%% %%sitename%%` | 40 + 19 suffix = 59 |
| `_yoast_wpseo_metadesc` | *(empty)* | `Tesla Robotaxi Austin е факт — стотина коли от Cybercab вече удариха бизнеса на Uber. Виж какво разказаха водещите за возенето и оплакванията.` | 146 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Tesla Robotaxi Austin` | — |

### Proposed tags (carried forward from Phase A, unchanged)
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Tesla | 4 | 59 |
| Entity | Robotaxi | 252 | 3 |

Post currently has zero tags (`tags: []`) — this write is additive.

### Applied
- [x] Metatags — title / metadesc / focuskw written and confirmed live
  (re-fetched rendered page: `<title>`, `<meta name="description">` and
  `og:description` all match, not stale-cached).
- [x] Tags written — Tesla (4), Robotaxi (252).
- [x] `post_content` written (Phase B, 2026-09-09) — wordCount: 19 → 191.
- [x] Image alt + title written — media id 9448 (confirmed in write response:
  `alt_text` and `title` both updated; backed up pre-write values to
  `reports/yoast-meta-backup/9448-media-2026-09-09.csv`).
- [x] Inbound link written — post 3946 (2024-04-05, predates this post),
  edited to add "днес обаче Robotaxi вече вози пътници в Austin" linking to
  this article, appended to its existing outdated ADAS/FSD paragraph. Diffed
  the full before/after `content.raw` (against revision 3956, the last
  pre-edit revision) programmatically: the only change is the intended
  inserted clause + `<a>` tag, byte-for-byte identical everywhere else — no
  accidental block reflow or wiped embed. (Note: the *live* post's stored
  copy of that same paragraph independently shows "бюджетния" →
  "бюджетният" and "като" → "както" in an unrelated later sentence — this
  was NOT part of what was sent; some other process on the WP side appears
  to normalize this Bulgarian text on save. Cosmetic, doesn't change
  meaning, not investigated further here.)
- [x] Auto-linked `/tag/` count inside body prose: **2** (Tesla and
  Robotaxi, both once, both in ¶1 — confirms the 1×/tag cap from W7 is live
  on this deploy).

### Declined
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|
_None — all proposed groups (metatags+tags, image alt/title, the inbound
link to 3946) were approved in full._

### Risks / notes
- No cannibalisation risk — no other post targets `Tesla Robotaxi Austin`.
- The inbound-link edit to post 3946 doubles as a factual correction (its
  2024 claim that Tesla "currently cannot operate without human
  intervention" is now outdated) — framed as an update, not just a link.
- Thin-content risk is now resolved for this post specifically (19 → 191
  owned words); the same news-cards-not-owned-content pattern still applies
  to the ~127 other EV News posts not yet run through this pipeline.
- Page is 1 day old — no GSC baseline to speak of yet; both ledger rows
  (Phase B and this Phase C write) will need the full re-crawl/index cycle
  before a checkpoint means anything.

### Measurement
Baseline: 0 impressions/clicks (page published 2026-09-08, 1 day old at
research time — no meaningful GSC window). Re-check in 2–4 weeks for the
metatag/tag/alt/inbound-link effect and again at 4–8 weeks for the new body
content to be crawled/indexed.

---


## Verification — 2026-11-04

| Ledger id | Changed | Checkpoint | Window | Page impr / clicks / CTR / pos | Verdict | Note |
|---|---|---|---|---|---|---|
| 9447-2026-09-09-C | title|metadesc|focuskw|tags|alt|inbound | 28d | 2026-09-10–2026-10-07 | 2 / 0 / 0.00% / 6.0 | **inconclusive** | impression floor: base 0 / after 2 |

Controls: site-wide + same-category un-optimized cohort median (GSC page-level; impressions compared per-day because some ledger baselines span 90 days). Page-level metrics only; keyphrase-filtered rows not pulled. Verdict bands (±0.5pp CTR / ±0.5 pos / ±25pp impressions) are calibration guesses.
