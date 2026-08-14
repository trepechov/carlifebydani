# Using the YouTube-RAG MCP to Fix EV News SEO — Proposals

**Date:** 2026-08-14
**Status:** Proposal — nothing built on the site yet. **The MCP itself is live**: registered
at project scope in [`.mcp.json`](../.mcp.json) and verified 2026-08-14 (9 tools, 4 slash
commands, 1 resource). Setup, verification results and two open producer-side defects:
[`MCP_SERVERS.md § YouTube-RAG`](MCP_SERVERS.md#youtube-rag).
**Producer side:** [`~/Projects/youtube-rag-n8n/docs/mcp-server.md`](../../youtube-rag-n8n/docs/mcp-server.md)
· [`plan.md`](../../youtube-rag-n8n/plan.md)
**Consumer side:** this repo — [`SEO_EV_NEWS_PROPOSALS.md`](SEO_EV_NEWS_PROPOSALS.md) (root-cause diagnosis),
[`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md) (the live backlog)

---

## Measured starting position (verified 2026-08-14, not estimated)

**The corpus (`clbd` in Qdrant, stack up locally):**

| | |
|---|---|
| Videos ingested | **99** (all from the `EVNews` playlist) |
| Chunks | 12,669 |
| Total transcript text | **14.9 M characters ≈ 2.2 M Bulgarian words** |
| Median episode length | ~185 min |
| Punctuated transcripts (usable prose) | **65 / 99** |
| Transcripts carrying `>>` speaker-turn markers | **52 / 99** |
| By year | 2024: 23 ingested, **0** punctuated · 2025: 46 / 35 · 2026: **30 / 30** |

**The pages that should be built on it:**

| | |
|---|---|
| Posts in the EV News category | 128 |
| …with a YouTube embed in `post_content` | 126 |
| …that map to an already-ingested episode by episode number | **91** |
| Median `post_content` word count | **0** (the embed block and nothing else) |
| Posts with a non-empty `post_excerpt` | **0** — the slot renders on every page and has never been used |
| Yoast `wordCount` on a live page | **17** (derived from `post_content` only) |
| External news cards per post | 14–68 (typical 27–28; EV114 has **68**) |
| Text hidden inside collapsed `max-h-0` accordions, EV114 | **~20,600 chars** |
| Pages with any search visibility | 28 / 128 |

So the published ratio on a typical page is **17 words of owned visible prose** against ~28
nofollow outbound links and several thousand characters of collapsed, third-party-derived
summary.

**And the two sides already line up where it matters:** 13 of the 14 posts in the P1
backlog have their episode transcript sitting in Qdrant right now. Only EV41 (post 1751)
is missing.

### The one-sentence version

The site owns **2.2 million words of original Bulgarian spoken commentary that exists
nowhere else in text form on the internet**, and the 128 pages built on top of it publish
**zero words of it**. Every page is a title, a video embed, and a wall of nofollow links
to other people's articles. That is the whole problem, and the MCP is the tool that fixes
it.

---

## First, a correction to the starting plan

> *"generate some text summarizing the youtube video content"*

Directionally right, and it is Proposal A below. But taken literally it is the **weakest**
thing this corpus can do, for one reason:

**Nobody searches for an episode summary.** There is no query volume for "EV114 обобщение".
An episode-shaped summary produces episode-shaped text, which competes for episode-shaped
queries, which are branded and already won. It adds 150 words to the page and changes almost
nothing about which queries the page can rank for.

What *does* have demand is **the question the episode answers**. Concretely, from this repo's
own data:

- Post 7333's title asks *"Има ли регистриран Tesla Cybertruck в България?"* The page's body
  answer is **17 words**, and the answer is not in that episode's transcript either — see
  the cross-episode finding below. It *is* in the archive, in EV113 and EVN71, deep-linked
  to the second. Nobody else on the Bulgarian web has that text.
- Post 6165 sits at **position 1.4** for `тесла джунипер` and converts **2.06%** of 3,927
  impressions. It is not a ranking problem. It is a "the page has nothing to read" problem.

So the generation should be **query-shaped, not episode-shaped**: GSC says which queries a
page already gets impressions for, the transcript supplies the hosts' answer to exactly
those queries, and the page publishes it. That is Proposal E, and it is the highest-value
item here.

Everything below is ordered by value-per-hour, not by build order.

### 🔑 The cross-episode finding — retrieval must not be scoped to one video

Discovered while drafting post 7333 on 2026-08-14, and it changes how Proposals A and E
are built.

**A page's title question is often not answered in that page's own episode.** Episode
titles are picked from the *news CSV* — the roundup of external articles — not from what
the hosts actually discussed on air. EV114 is titled *"Има ли регистриран Tesla Cybertruck
в България?"* and the words *Cybertruck* and *сайбър* appear **zero times** in its
2h16m transcript.

The archive answers it anyway, in two other episodes:

- **EVN71** (2024-09-10, post 5350) — the first Cybertruck unloaded in Bulgaria, on Varna
  plates, still carrying its US plate.
- **EV113** (2025-08-12, one week *before* EV114) — the import route, and a first-hand
  count of the ones they know of in the country.

**Consequences:**

1. **Never scope `search_transcripts` to `video_id` alone.** Search the collection, then
   prefer the page's own episode when it has material and fall back to the archive when it
   does not. A video-scoped search on 7333 returns nothing and looks like an empty archive.
2. **Cross-episode answers are internal links for free** — answering 7333 naturally links
   to 5350 and to the `/ev-review/` Cybertruck page, which is exactly the internal-graph
   work the backlog already wants.
3. **This strengthens Proposal F.** If the answers already live across episodes, the hub
   pages are the natural home for them and the episode pages are the entry points.

---

## Proposal A — Grounded episode intro at the bottom of `post_content`

**The starting idea, tightened and pinned to the actual template.** 130–190 words of
Bulgarian prose per post, appended to **`post_content`** after the video embed as Gutenberg
`wp:paragraph` blocks. Settled on post 7333, 2026-08-14 — see the field decision below.

### Where it goes, and why not the excerpt slot

The theme has an unused `post_excerpt` slot at
[`theme/single.php:100`](../theme/single.php#L100) that renders full-width between the H1 and
the video — on paper the strongest position on the page, and empty on all 128 posts:

```php
<div class="lg:mb-0 post-content"><?php echo apply_filters('the_content', $current_post->post_excerpt); ?></div>
```

**It was tried and rejected.** Two reasons, both established on 7333:

1. **It renders poorly for this post type** — a full-width `col-span-3` row above a 2/3-width
   column, giving ~180-character lines at `max-w-screen-2xl`.
2. **Yoast derives `wordCount` from `post_content` only.** 154 words in the excerpt left
   `wordCount` at **17**; the same words appended to `post_content` took it to **168** and
   made the prose visible to Yoast's content analysis.

So the copy goes at the **bottom of `post_content`** — after the video, before the news cards.
Measured DOM order on the live page after the change:

| element | offset |
|---|---|
| `<h1 class="title">` | 44,660 |
| YouTube embed | 47,534 |
| **the three paragraphs** | **47,798** |
| first news card | 52,739 |

**Never write both fields** — that duplicates the text on the page. Clear `post_excerpt` when
moving copy into `post_content`.

> **Trap worth keeping:** the WP REST API reports a 56-word `excerpt.rendered` on 9 of the 128
> posts, which looks like they already have an excerpt. That is WordPress auto-trimming
> `post_content`. The template reads the **raw `post_excerpt` field**, so there is no fallback.
> Do not infer "has an excerpt" from REST.

**Ships with no theme change.** `post_content` is a core field, writable over REST through the
WordPress MCP, and — unlike postmeta — **covered by WP revisions**, so it does not need the
`reports/yoast-meta-backup/` ritual that Yoast fields do.

### The three paragraphs

Not "this episode covered X, Y, Z". Each paragraph has a distinct job:

| ¶ | Words | Job |
|---|---|---|
| **1** | 40–60 | **Answer the page's own headline question**, with the focus keyphrase verbatim. This is what Yoast turns into the meta description and what Google lifts as the snippet. |
| **2** | 50–70 | **What the hosts actually said** — the first-hand Bulgarian angle from the transcript. The uncopyable part (see Proposal B). |
| **3** | 40–60 | **The other 2–3 notable stories**, named with the brand/model terms people search, plus an internal link to an EV Review where one exists. |

130–190 words total: enough for `wordCount` and keyword coverage, short enough not to
compete with the list for attention.

### ⚠️ Tags auto-link into the prose — budget for it

[`theme/functions.php:75`](../theme/functions.php#L75) `add_tag_links_to_content` links every
post tag wherever its name appears in `the_content`, **up to 5 occurrences per tag**. On post
7333, 8 tags injected **10 `/tag/` links into 154 words** — one per 15 words, all pointing at
thin archives, diluting the 2 editorial links placed deliberately.

Deliberate theme behaviour, not a bug, but it cuts against the backlog's own rule (*"no
`/tag/` pages as targets"*) and makes the open `noindex`-thin-tags decision more urgent, since
every optimized post now feeds them ~10 more links. Either lower the per-tag cap to 1, or
`noindex` the thin archives.

### Tags and keyphrase alignment — use two tiers

The card count is higher than it looks: **68 on EV114**, 27–28 typical. If the Automator
tags per-article across ~28 stories, the post inherits 28 stories' worth of tags while its
title covers 2. That dilutes topical focus and spawns exactly the thin tag archives the
backlog already flags — `/tag/clbd/` absorbs 418 brand impressions at 4 clicks while
outranking real pages.

**The site's 365-term vocabulary already follows the right pattern** — brands (`Tesla` 56
uses), models (`Model Y`, `Cybertruck`), and keyword terms (`Зареждане` 12, `Премиера` 6,
`Разход`, `слух`, `Регистрация`). Reuse it; creating new terms spawns new thin archives.

- **Post tags = only the 1–2 headline stories' entities**, matching the focus keyphrase and ¶1.
- Everything else stays analysis metadata and never becomes a public tag term.
- Per the existing backlog rule, keep `EV новини` off episode pages — that head term belongs
  to the hub.

### Which stories count as "top" is currently unrecorded

Picking the 1–2 headline stories is editorial and lives nowhere machine-readable. But the
CSV already carries `upvote` / `downvote` / `clicked` per row
([`single.php:134-136`](../theme/single.php#L134)) — real audience signal on which of the
~28 stories readers cared about. Usable both for choosing what ¶2 and ¶3 cover and for
ordering the cards.

### The ratio problem this only half-fixes

EV114 carries ~20,600 characters of summary text inside collapsed `max-h-0` containers
against 68 nofollow outbound links. The intro fixes the *presence* of owned text; the
collapsed-by-default summaries are a separate fix — show the first 1–2 sentences per card
([`SEO_EV_NEWS_PROPOSALS.md §1.3`](SEO_EV_NEWS_PROPOSALS.md)).

---

**MCP tools:** `search_transcripts` (raw chunks) → Claude writes. Do **not** route this
through the server's LLM; see "Where the writing should happen" below.

**Fixes, directly:** a real source for the meta description · keyword-bearing prose in the
page's strongest position · the owned-text-to-outbound-link ratio that a link roundup lives
or dies by.

> ⚠️ **Use `post_content`, not `post_excerpt` — settled on post 7333, 2026-08-14.**
> The excerpt slot renders poorly for this post type, and Yoast derives `wordCount` from
> `post_content` only, so 154 words in the excerpt left it stuck at **17**. Appending the
> same three paragraphs to `post_content` after the video embed took it to **168** and made
> the prose visible to Yoast's analysis. The copy lands after the video and before the news
> cards. Never write both fields — that duplicates the text on the page.

**Coverage today: 91 / 128 posts.** Ingesting the ~35 missing episodes takes it to ~126.

**Effort:** the research step is already automated by the corpus and the render slot already
exists. This is the cheapest item on the list and it unblocks the meta-description work
already in the P1 backlog.

---

## Proposal B — The "what the hosts actually said" block *(the differentiator)*

This is the one that cannot be copied by anyone.

Every EV News post links out to articles from Electrek, InsideEVs, CarNewsChina. So does
every other aggregator. What no one else has is **three Bulgarians with EVs arguing about
what that news means here** — registration, service, charging, price, whether the importer
exists at all.

A short block per post — *"Какво казаха в подкаста"* — carrying 2–4 first-hand claims with
a timestamp deeplink on each:

> **Апи, 1:47:20** — «Това е регистрирана машина и ограничението по българските улици е
> 50 км/ч… 51 км/ч през цялото време.» [→ гледай момента](https://youtube.com/watch?v=i5duLJuVyl4&t=6440)

That is original Bulgarian content, first-hand, unavailable anywhere else, and it is
sitting unpublished in 99 episodes.

**MCP tools:** `extract_opinions` (plan Phase 6). The plan's quote-validation guard
(every claim must carry a verbatim span that survives a substring check) is **mandatory
here, not optional** — see the ASR caveat below.

**Finding for the producer side:** the plan states *"YouTube auto-captions contain zero
speaker information"* and budgets Phase 5 (`llm_turns`) accordingly. **That is not true for
the majority of this corpus.** 52 of 99 transcripts already carry `>>` turn markers, and
every 2026 episode does. Turn segmentation on those is free — a `split('>>')`, not an LLM
pass. `llm_turns` is only needed for the 34 unpunctuated 2024-era transcripts, which are
also the least commercially interesting ones. **Phase 5 is much cheaper than budgeted, and
Phase 6 can start on the 52 clean episodes before Phase 5 exists at all.**

---

## Proposal C — Chapters → real H2 outline + `VideoObject` / `Clip` schema

The lowest-effort structural win in the entire backlog, and it requires **no editorial
writing at all**.

`generate_chapters` (plan Phase 2, "Low" risk) returns `M:SS Title` derived from actual
topic transitions in the episode. That single output solves three separate open items:

1. **Fixes the H1 → H5 jump** ([`SEO_EV_NEWS_PROPOSALS.md §2.2`](SEO_EV_NEWS_PROPOSALS.md)) —
   chapters become the H2 outline the page has never had, with headings derived from what
   was actually discussed rather than invented.
2. **Earns key-moments rich results** — `VideoObject` with `hasPart: Clip[]` built from the
   chapter timestamps. `start_time` is already on every chunk; nothing new needs storing.
3. **Bonus, off-site:** the same output pasted into the YouTube description gives the video
   its own chapter markers and its own search surface.

Every heading deep-links into the video, which is also the strongest internal reason for
these pages to exist.

---

## Proposal D — FAQ block from real Q&A turns → `FAQPage`

The `>>` turn markers make question turns mechanically findable in 52 episodes. A host asks
something, a guest answers, and that pair is a `FAQPage` entry with a real answer instead of
an invented one.

Best on the pages whose GSC queries are already question-shaped (`има ли…`, `колко струва…`,
`къде да заредя…`). Lower priority than A–C, but it is nearly free once B exists, and FAQ
answers surface as their own SERP feature.

---

## Proposal E — Query-shaped generation: content radar, applied per page ⭐

**The highest-value item here, and the only one that needs both MCP servers at once.**

The `content-radar` workflow in the producer plan aims at a content *calendar* — future
articles. Point the same machinery at **pages that already have impressions**, and the
feedback loop shortens from months to weeks:

```
GSC (already connected here)
  → for post P, the queries it gets impressions for but ~no clicks
     e.g. 6165: "тесла джунипер" pos 1.4, CTR 2.06%, 3,927 impr
        │
youtube-rag MCP: search_transcripts(query=<that exact query>, video_id=<P's episode>)
        │
  → the hosts' own answer to the query, at a timestamp
        │
Claude writes 60–120 words that literally answer it, + updates the meta description
        │
wordpress MCP (already connected here): write post_excerpt
        │
  → GSC CTR delta in 2–4 weeks
```

Semrush/DataForSEO only enter when a page has **no** impressions yet and the query has to
be discovered rather than read off GSC — which keeps the paid calls off the common path
(and the local `data/seo-cache/` rule still applies).

**Start here, on the 13 P1 backlog posts whose transcripts are already ingested.** The
backlog, the transcripts, and both MCP servers already exist; this proposal is the wiring
between them.

---

## Proposal F — Cross-episode evergreen hubs *(biggest ceiling, longest fuse)*

161 episodes have circled the same Bulgarian topics for two years: charging infrastructure
here, real prices and import routes, service and warranty, winter range on our roads. Any
one episode mentions each in passing. **The corpus as a whole is the authority.**

`trending_topics` + `summarize_topics` (plan Phase 3) makes it possible to build evergreen
pages of the form *"Какво казахме за зареждането в България — от 40 епизода"*: the show's
accumulated position on one topic, deep-linked across episodes, updated as new episodes land.

Two reasons this matters more than it looks:

- **It resolves the cannibalisation constraint already recorded in the backlog.** The TODO
  correctly forbids targeting head terms like `EV новини` on episode pages. Hub pages are
  where head terms belong — episode pages keep their distinctive story, hubs take the
  category demand.
- **It is the only content type here that compounds.** Each new episode makes every hub
  page better, automatically.

Phase 3 also ships as a payload backfill (`set_payload`, no re-embedding), so it does not
wait on the Phase 5 re-ingest.

---

## Proposal G — What *not* to do: publish raw transcripts

The tempting move is a `/transcript/` page per episode. 2.2 M words, instantly indexable.

**Don't.** Three reasons, in order of severity:

1. **ASR errors become published misquotes about named people and companies.** In EV114 the
   word "Cybertruck" — the subject of the episode's own title — does not appear in the
   transcript at all; the recogniser dropped it. Elsewhere it produces confident nonsense.
   Publishing that attributed to a named host is a reputational and legal problem, not an
   SEO one. This is also why Proposal B's verbatim-quote validator is non-negotiable.
2. 185-minute unstructured auto-caption dumps are exactly what Google's helpful-content
   systems classify as low-value mass-produced text. At 99 pages it is a site-level risk.
3. Transcripts of a video that is embedded on the same site produce near-duplicate intent
   across two URLs.

**The safe form of the same idea:** cleaned, human-checked, structured excerpts — which is
Proposals B, C and D. Same asset, published in the shape that is defensible.

---

## Where the writing should happen (and what it means for plan §8)

Plan §8 flags that `gemma-4-31b-it:free` will not carry Bulgarian SEO copy and budgets a
paid generation model. **For this consumer, that decision is not on the critical path.**

The SEO workflow runs inside Claude Code, which is already a strong Bulgarian model with
the site's own GSC data, the competitor-gap report, and the Yoast write path in context.
The server's job for SEO is to **return grounded raw chunks with timestamps** — not to
summarise them first. The plan already says this ("letting *it* reason over raw grounded
chunks usually beats a small server-side model summarising first"); it is worth making it
the explicit default for the SEO consumer.

Practical consequence: **`search_transcripts` + `get_transcript` + `generate_chapters` are
enough to start.** The synthesising agents (`seo_brief`, `opinion_digest`) are convenience,
not prerequisites — which is why Proposals A, C and E can begin the day Phase 1 lands.

---

## Requests to the producer side

Updated after the 2026-08-14 verification run. Small, and each unblocks something above:

1. 🐞 **Fix the `datetime_range` bug — blocking.** Every call carrying `date_from` /
   `date_to` 422s, which kills `trending_topics` outright and every dated
   `search_transcripts` call. Qdrant 1.18.0 has no `datetime_range` condition; the plain
   `range` key works against the existing `datetime` index. One-word fix in
   `services/qdrant.py::date_filter`. Details in
   [`MCP_SERVERS.md § YouTube-RAG`](MCP_SERVERS.md#youtube-rag).
2. 🐞 **Run the topic backfill.** No chunk carries a `topics` payload, so Proposal F has
   nothing to rank. `scripts/backfill_topics.py --propose` → curate `collections.yml` →
   backfill for real.
3. **`resolve_episode(episode_ref)` tool.** Episode number, WP post URL, or slug → `video_id`.
   The mapping is currently a regex against video titles, done client-side, and it is the
   first step of every single workflow above. It should live in the server.
   *(Titles carry two schemes — `EVN67` and `EV133` — so match `EVN?\s*\d+`.)*
4. ✅ ~~**`timestamp_url` on every returned chunk.**~~ **Shipped** — confirmed present on
   every `search_transcripts` and `ask` source.
5. **Ingest the ~35 missing episodes.** 91/128 posts are covered; the gap is mostly
   EVN44–EVN64, plus EV100 and EV121. EV41 blocks a post already in the P1 backlog.
6. ✅ ~~**Reorder: pull Phase 2 (chapters) ahead.**~~ **Shipped** — `generate_chapters`
   returns in ~5 s. But see the model caveat: on the free `GENERATION_MODEL` the Bulgarian
   chapter titles come back too generic to publish as H2s. **This makes plan §8's
   generation-model decision a live blocker for Proposal C**, where it is not one for
   Proposals A and E (which are written client-side by Claude).
7. **Revisit the Phase 5 estimate** given the `>>` markers finding in Proposal B.

---

## Suggested order of execution

| # | Proposal | Needs | Effort | Impact |
|---|---|---|---|---|
| 1 | **E** — query-shaped text on the 13 P1 posts | ✅ ready — MCP + GSC + WP MCP all live | 30–45 min/post | **Highest** — CTR feedback in 2–4 weeks |
| 2 | **A** — grounded intro → `post_content` + tags, all 91 covered posts | ✅ ready (**no theme change**) | ~20 min/post | **High** — `wordCount` 17 → 168 on 7333, plus schema `keywords` |
| 3 | **C** — chapters → H2 outline + `Clip` schema | tool ready; needs a better `GENERATION_MODEL` | 1 day theme + per-post | **High** — no writing required |
| 4 | **B** — hosts' own claims block | Phase 6 (or 52 clean episodes now) | Medium | **High** — the only uncopyable content |
| 5 | **F** — cross-episode evergreen hubs | blocked: 422 bug + topic backfill | Medium–High | **Highest ceiling**, slowest |
| 6 | **D** — FAQ block | after B | Low | Medium |
| — | ~~**G** — raw transcript pages~~ | — | — | **Do not build** |

Items 1 and 2 both run on Phase 1 alone — `search_transcripts` and nothing else. They can
start the day the MCP server answers a request.

---

## Open questions

1. ~~**Store generated text in `post_excerpt`, `post_content`, or a new meta field?**~~
   **Resolved 2026-08-14 — `post_excerpt`.** It renders above the video and the card list
   today, needs no theme change, is REST-writable, and is covered by WP revisions. See
   Proposal A. Proposals C and D still need block-level structure, so they want
   `post_content` and a theme change; that decision stands open.
2. **Automate in the EV News Automator pipeline, or run as a Claude Code skill per post?**
   The skill route gives editorial review per page and reuses the existing
   `seo-article-optimize` pattern. The pipeline route covers every future episode with no
   human in the loop. Recommend: skill first for the ~91 back-catalogue posts, pipeline once
   the prompt has stabilised on real pages.
3. **Does published transcript-derived text need a visible disclosure** that it is derived
   from the episode? Editorially cleaner; costs nothing in SEO terms.
4. **How much human review per page?** Proposal B publishes attributed quotes about named
   companies. The validator catches fabrication; it does not catch ASR mangling a brand
   name. Recommend a human read on anything carrying a `«»` quote.
