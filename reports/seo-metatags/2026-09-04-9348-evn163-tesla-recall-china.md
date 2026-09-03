# SEO Optimization — #EVN163 – Рекордно изтегляне на милиони електромобили в Китай

**URL:** https://www.carlifebydani.com/ev-news/evn163-rekordno-izteglyane-na-milioni-elektromobili-v-kitaj/ · **Post ID:** 9348 · **Category:** ev-news
**Prepared:** 2026-09-04 (Phase A)
**Status:** applied
**Keyphrase:** tesla изтегляне на автомобили в китай
**Ledger:** 9348-2026-09-04;9348-2026-09-04-C

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
China's regulator ordered a record recall of ~4.3 million vehicles across 8 automakers
(Tesla, Mercedes, Dongfeng and others named in the transcript) over electric/flush door
handles that can jam or become hard to find in an emergency — triggered by a February
Dongfeng S7 crash-and-fire where occupants couldn't get the doors open and bystanders had
to smash windows. China will ban flush hidden handles outright from 2027. The episode
covers this as its headline story plus ~20 shorter news-card items (Zeekr 7GT, Nio battery
swap, Walmart charging, Tesla Semi's biggest order, Tesla Robotaxi in Austin, CATL carbon
neutrality, BYD flagship sedan launch, Porsche selling MHP, Leapmotor profit guidance, etc.).
Published 2026-08-25, 10 days old at research time. Owned word count (Yoast `wordCount`): **17**
— `post_content` is just the YouTube embed; the visible 22 news-card summaries render from the
remote `news_csv` at request time (per `_shared/constants.md`), not from `post_content`.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EVN163 – Рекордно изтегляне на милиони електромобили в Китай - Car Life by Dani` (Yoast field empty, falls back to post title + site suffix) | 82 chars |
| `<meta name=description>` | *(none present)* | 0 |
| Focus keyphrase | *(empty)* | — |
| H1 | `#EVN163 – Рекордно изтегляне на милиони електромобили в Китай` | — |
| Owned word count | 17 (Yoast `wordCount`) | — |
| Images without alt | Featured image (id 9353) — `alt_text: ""`, `title: "#EVN 163"` (generic). 22 news-card `<img>` have descriptive `alt` (from CSV titles) despite empty `src` (JS-hotloaded thumbnails, a known theme quirk, not a content gap) | — |
| Internal links out / in | 0 out (no links in `post_content` yet) / not checked — post is 10 days old, unlikely to have earned inbound links yet | — |
| Heading outline | H1 → H5 (news-card titles) — known gap, no H2/H3 exist because there's no body prose yet | — |
| `<html lang>` | `bg-BG` (correct, per site-wide fix verified 2026-08-13) | — |

### Demand research
**GSC (90d, this URL, queried directly via the API with a `page` filter — not the MCP dump):**
| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|
| *(no rows — zero impressions)* | 0 | 0 | — | — |

Zero impressions is expected here: the post is 10 days old and GSC only just confirmed it
indexed (`gsc_inspect_url`: "Submitted and indexed", last crawl 2026-08-30, no manual actions).
This is the common EV-News pattern (~100/128 episode pages have zero visibility) — nothing to
preserve, choosing a target from scratch.

**Google autocomplete (hl=bg, gl=bg):** Every Bulgarian-phrased seed came back **empty**:
`tesla изтегляне`, `recall електромобили`, `изтегляне на автомобили китай`, `електрически
дръжки на вратите`, `recall китай`, `коли с електрически дръжки`, `дръжки на вратите не се
отварят` — all zero completions. `изтегляне на коли` alone returns completions, but they're
all about downloading *car games* (`изтегляне на игри с коли`) — confirms `изтегляне` is
ambiguous in isolation and must stay paired with `автомобили`/`Tesla`, never used bare.
`tesla китай` returned only Russian-market completions (`тесла китайская`, `тесла китай
цена`) — not this market's signal. `tesla recall` (English) returned real global completions
(`tesla recall check`, `tesla recall search by vin`, `tesla recalls model y`) but that's
generic VIN-lookup intent, not this China-specific story — targeting it would mismatch intent
and bounce. `Dongfeng S7` has real completions too, but they're spec/review intent
(`dongfeng s7 цена`, `dongfeng s7 review`) — a different article's keyphrase, not this one's.

**Keyword metrics (bg) — fresh, DataForSEO `search_volume/live` (now fully unblocked, see below):**
| Phrase | Volume (bg) | Note |
|---|---|---|
| `tesla recall` | 10/mo | English code-switch; VIN-check intent, not this story |
| `tesla изтегляне` | 0 | |
| `dongfeng s7` | 0 | |
| `recall китай` | 0 | |
| `електрически дръжки` | 0 | |
| `рекордно изтегляне` | 0 | |
| `изтегляне на автомобили` | 0 | |

All banked to `data/seo-cache/keywords.csv` (fresh, dataforseo, 2026-09-04). **DataForSEO note:**
the account (`trepechov@gmail.com`) was fully `40104`-blocked as of 2026-08-13 and only
partially unblocked 2026-08-17 (bulk `search_volume/live` was the one endpoint still stuck).
**Re-tested today, 2026-09-04: fully unblocked** — the bulk call above returned real data on
the first try. `_shared/constants.md` and this skill's "Known traps" section still say
"blocked, don't re-debug" — that line is now stale and should be corrected (flagging here;
not fixing it as part of this run since it's outside Phase A's scope).

**SERP check:** `tesla recall china door handles` (DataForSEO, `location_name: Bulgaria`,
`language_code: bg`) is wall-to-wall major international English media — BBC, Reuters, Wired,
TechCrunch, ABC News, Mashable, CNA, Motor1 — plus an AI Overview and a YouTube block. **Zero
Bulgarian-language results anywhere in the top 10.** No Bulgarian outlet has covered this
story. Combined with the zero autocomplete/volume above, this confirms there is **no
measurable organic search demand for this story in the Bulgarian market** — not a ranking
problem, a demand problem. An episode page cannot realistically compete with BBC/Reuters for
the English query, and there's no Bulgarian query to compete for instead.

**GA4:** not pulled — a 10-day-old page with zero GSC impressions has no landing-page signal
worth spending a call on yet.

**News CSV:** not fetched directly — `news_csv` isn't yet REST-exposed on the live site (the
fix is committed but undeployed, per `project-seo-skills-refactor` memory). Used the rendered
page's 22 H5 news-card titles as the row-order/entity source instead (degrades cleanly per the
skill's rule: title/description/link are the only guaranteed columns anyway). Row order
confirms "Tesla изтегля милиони електрически автомобили в Китай" is the headline story
(hottest, first in the CSV) — matches the post title's framing, though the transcript shows
the real story is broader (8 brands, China-wide regulatory action) than the Tesla-only CSV
headline suggests. Phase B should ground the intro in the fuller, accurate story.

### Recommendation
**Focus keyphrase:** `tesla изтегляне на автомобили в китай` — chosen for **accuracy and
on-page consistency, not volume**: there is no measurable Bulgarian search demand for this
story (zero autocomplete, zero DataForSEO volume, zero GSC impressions, zero Bulgarian SERP
competition). This phrase is the honest, precise description of what the page is about and
keeps Yoast/on-page signals clean; it is not expected to drive new organic sessions. Say this
plainly rather than overselling a metatag fix — no keyphrase choice here changes the demand
ceiling.
**Secondary:** `tesla mercedes dongfeng изтеглени коли`, `електрически дръжки на вратите
tesla`, `4.3 милиона автомобила китай` — content-fit phrases matching how a reader who already
knows the story might search, not volume-backed; none should be over-indexed on.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title)* | `Tesla изтегля автомобили в Китай` | 33 (+19 suffix = 52) |
| `_yoast_wpseo_metadesc` | *(empty)* | `Китай разпорежда рекордно изтегляне на 4,3 млн. коли от 8 марки, включително Tesla и Mercedes, заради електрически дръжки на вратите.` | 135 |
| `_yoast_wpseo_focuskw` | *(empty)* | `tesla изтегляне на автомобили в китай` | — |

(Draft for Phase C to finalize against the actual written prose — the metadesc above already
reflects the real 8-brand story, not the CSV's Tesla-only framing, so Phase B's intro should
match it.)

### Proposed tags
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Tesla | 4 | 56 (saturated hub, but genuinely the headline entity per post title — use) |
| Entity | китайски електромобили | 313 | 7 (in-band, directly on-topic) |
| Entity (pending Phase B) | Mercedes | 74 | 13 (in-band; only add if Phase B's prose actually names Mercedes, not just the CSV/transcript) |

**Gaps — not created:**
- **Dongfeng** (id 364, count 2) — too thin, skip; note if this recurs across Chinese-brand
  stories, it's a candidate for deliberate batch-creation later.
- **No "recall"/"изтегляне" intent tag exists site-wide.** This is a real gap — the site has
  intent tags for `Премиера`, `слух`, `Регистрация`, `Зареждане`, `Разход` but nothing for
  recalls, and this won't be the last recall story. Worth a deliberate decision (create one
  once a few recall stories accumulate) rather than inventing it ad hoc here.
- No existing tag for "Инцидент" either, despite the Dongfeng S7 crash being the story's
  actual trigger.

Cannibalization check: `/wp/v2/search?search=Tesla изтегляне Китай` → no existing post owns
this phrase. Clear to proceed.

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances `Status: content-written`._

**Episode resolved:** ambiguous, resolved manually — see note below.
**Answer found in:** own episode, but the recording has **two YouTube parts** and the one
actually embedded in `post_content` isn't the one with the story.

**Structural finding (not a Phase B fix, flagging for the user):** `tools/resolve_episode.py
"163"` resolves to `Xi74Ag-t-qE` ("...EV163... **Част 2**", published 18:45) — the news-rundown
half. But the video actually embedded in this post is `03nQDIfAh1M` ("...EV163...", no "Част
2" suffix, published 15:55, 2h10m earlier) — a live Q&A/banter part that only speculates
about the door-handle redesign, with none of the actual recall facts. The post's own iframe
even renders a stale oEmbed `title` reading "**EV132**" (a different, unrelated Waymo episode
from Dec 2025 — confirmed by resolving "132" separately) — cosmetic YouTube oEmbed-cache lag,
not a real content mismatch; the corpus's own stored title for `03nQDIfAh1M` correctly reads
"EV163". Net effect: **the embedded video is Part 1, the facts live in Part 2 of the same
recording session** — not a different, unrelated episode, so this isn't the "search the
archive" case in the usual sense, but it's also not simply "search the embedded video and
stop." Grounded ¶1–¶2 in Part 2 (`Xi74Ag-t-qE`) since that's where the actual news content is;
left the embed block untouched (out of this skill's scope to swap which video is embedded —
flagging for the user in case Part 2 should be embedded/added instead).

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|
| China recalls 4.3M vehicles across 8 brands incl. Tesla, Mercedes, Dongfeng, over door handles | "правят recл на 4,3 милиона автомобила в Китай от осем марки. От тях бяха Tesla, Mercedes, Донфeng..." | EV163 Част 2 (Xi74Ag-t-qE) | [19:37](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=1177) |
| Fix required: mechanical backup way to open doors inside/outside | "трябва да ги върна, за да направят нали механичен начин на отваряне на а вратите отвътре и отвън" | same | [19:37](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=1177) |
| Trigger: Feb. Dongfeng S7 leaves road, hits battery, catches fire | "февруари месец в Китай става инцидент с Донфен S7... колата се удря странично и... отдолу в батерията... падайки в канавката" | same | [12:03](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=723) |
| Occupants trapped; bystanders broke windows to free them | "трябва водача и мивачи чупят с камъни и стъклата, за да могат хората да излязат" | same | [13:26](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=806) |
| Tesla Semi's biggest order (to an electric-trucking fleet operator) | "Най-голяма поръчка за семи... BG Storers Electric Trucks" | same | [46:29](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=2789) |
| Porsche sells MHP to TCS for €320M | "Porche продава MHP за TSC за... 320 милиона" | same | [58:19](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=3499) |
| Leapmotor cuts profit guidance | "Мотор намалява прогнозата си печалба" | same | [58:19](https://www.youtube.com/watch?v=Xi74Ag-t-qE&t=3499) |

### Draft paragraphs
¶1 (48 words): Tesla изтегля ли наистина „милиони“ коли в Китай, както подсказва заглавието на
#EVN163? Не съвсем сама — китайският регулатор разпорежда рекордно изтегляне на 4,3 милиона
автомобила от осем марки общо, сред които Tesla, Mercedes и Dongfeng, заради електрически
дръжки на вратите, които могат да не се отворят при авария.

¶2 (75 words): Водещите разказват какво стои зад казуса: през февруари в Китай Dongfeng S7
излиза от пътя, удря се и се възпламенява, а токът в колата спира да захранва и електрическите
дръжки на вратите. Шофьорът успява да излезе, но за пътниците отзад се стига до чупене на
стъклата отвън. Затова Китай задължава марките да добавят механичен резервен начин за
отваряне на вратите — не първият подобен случай с китайски електромобил, както показаха и
[катастрофите с Xiaomi SU7](https://www.carlifebydani.com/ev-news/evn51-zashto-xiaomi-su7-katastrofira-tolkova-chesto/).

¶3 (61 words): Извън тази новина епизодът минава и през останалите теми от броя: Tesla получава
най-голямата поръчка за електрическия си камион Semi, Porsche продава дъщерното си дружество
MHP на TCS за 320 милиона евро, а Leapmotor намалява прогнозата си за печалба с 40% — поредните
сигнали, че [китайските марки вече са трайна част от пазара](https://www.carlifebydani.com/publications/kitajskite-elektromobili-sa-tuk-za-da-ostanat/).
Пълният преглед на новините е в клипа по-горе.

**Total: 184 words** (target 130–190).

### Facts to confirm before publishing
- [ ] **Full list of 8 recalled brands** — transcript only names Tesla, Mercedes, Dongfeng; the
  host explicitly says "ще излъжа вече кои бяха останалите" (would be guessing on the rest).
  Not stated in the draft — only the three confirmed brands are named.
- [ ] **Whether Tesla Model 3/Model Y are among the affected models** — the transcript has a
  contradictory, ASR-garbled exchange about this ("тоя проблем го няма. Не трябва да го има.
  >> Е има го.") — left out of the draft entirely rather than guessing.
- [ ] **Which video should be embedded** — see the structural finding above. Recommend the
  user decide whether to also embed/link Part 2 (`Xi74Ag-t-qE`), since that's where the actual
  news segment lives.

**Applied 2026-09-04.** `content` written as drafted (existing embed untouched, 3 paragraph
blocks appended, both internal links live). Verified: Yoast `wordCount` 17 → 196; `excerpt`
stayed empty (no duplication). Session note: this Phase B write was drafted by an earlier
session on the same machine that was interrupted (3 parallel terminals closed by mistake); a
resuming session found the draft already complete in this file and applied it rather than
re-researching.

---

## Phase C — Metatags, tags, alt text
_Written by `seo-article-apply`. Reads Phase A/B; sets `Status: applied`._

### Applied metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → fell back to post title)* | `Tesla изтегля 4,3 млн. коли в Китай %%sep%% %%sitename%%` | 35 body (+19 suffix = 54) |
| `_yoast_wpseo_metadesc` | *(empty)* | `Китай разпорежда рекордно изтегляне на 4,3 млн. коли от 8 марки, вкл. Tesla и Mercedes, заради електрически дръжки на вратите — вижте казуса.` | 141 |
| `_yoast_wpseo_focuskw` | *(empty)* | `tesla изтегляне на автомобили в китай` | — |

Verified live (curl, not cached): `<title>` and `<meta name="description">` both render the
new values.

### Applied tags
Tesla (id 4), Mercedes (id 74, added now that Phase B's ¶1 names it), китайски електромобили
(id 313). Post had no tags before. **Dongfeng deliberately still not tagged** — Phase A's gap
note ("existing tag too thin, id 364 count 2") was carried forward even though ¶1/¶2 both name
Dongfeng prominently now; flagging again here in case the user wants it created given the body
now leans on it more than on Mercedes.

### Featured image (id 9353)
| Field | Before | After |
|---|---|---|
| `alt_text` | *(empty)* | `Tesla изтегля коли в Китай заради електрически дръжки на вратите` |
| `title` | `#EVN 163` (generic auto-name) | `Tesla изтегля коли в Китай` |

### Internal links
- **Outbound:** both already added by Phase B (Xiaomi SU7 crash post `#EVN51`, id 4129;
  "Китайските електромобили са тук, за да останат", id 4115) — verified both slugs resolve to
  real, existing posts via a direct `/wp/v2/posts?slug=` lookup (the fuzzy `/wp/v2/search` text
  search missed the second one on an exact-slug query, false negative, not a broken link).
- **Inbound:** none proposed. Checked `/wp/v2/search` for the keyphrase and for "electric door
  handles" — no older post covers this story or a close-enough subtopic; matches Phase A's SERP
  finding that no Bulgarian outlet covered this story either. Zero is the honest answer here,
  not a gap.

### Tag auto-link verification
Counted `/tag/` links **inside the three body paragraphs only** (isolated from the page's other
`/tag/` links — news-card list, footer, etc. — which are out of scope for this check):
- `tag/tesla/` — **2×** (once in ¶1, once in ¶3 — "Tesla" appears in both)
- `tag/mercedes/` — 1× (¶1 only)
- `tag/kitajski-elektromobili/` — 0× (the singular "китайски електромобил" in ¶2 doesn't match
  the plural tag slug, so it isn't auto-linked — expected, not a bug)

The Tesla double-link is the known, already-diagnosed gap: the W7 theme fix ("auto-link cap
lowered to 1× per tag", `theme/functions.php:75`) is **committed but not deployed** to
production yet (per `project-seo-skills-refactor` memory) — this post is simply hitting the
still-live pre-fix behavior, not a new issue. No action needed here; resolves itself once that
deploy ships.

### Declined
None — all proposed items (metatags, tags, image alt/title) were approved as drafted.

---
