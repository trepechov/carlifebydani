# EV News Transcript Content — Further Proposals (B, C, D, F)

**Date:** 2026-08-14 (retitled and shrunk 2026-08-14, see the note below)
**Status:** Proposals B, C, D and F below are **not built**. Proposals A (grounded intro) and
E (query-shaped generation) **are built and shipping** — their rationale, measurements and
settled decisions moved to [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md), which is
documentation, not a proposal. This file previously described A and E as pending, which had
drifted into being actively wrong (`SEO_SKILLS_REFACTOR.md` §W9) — read the method doc for
what's actually running, this file for what isn't built yet.

**The MCP itself is live**: registered at project scope in [`.mcp.json`](../.mcp.json) and
verified 2026-08-14 (9 tools, 4 slash commands, 1 resource). Setup, verification results and
open producer-side defects: [`MCP_SERVERS.md § YouTube-RAG`](MCP_SERVERS.md#youtube-rag).
**Producer side:** [`~/Projects/youtube-rag-n8n/docs/mcp-server.md`](../../youtube-rag-n8n/docs/mcp-server.md)
· [`plan.md`](../../youtube-rag-n8n/plan.md)
**Consumer side:** this repo — [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md) (what's
built), [`SEO_EV_NEWS_PROPOSALS.md`](SEO_EV_NEWS_PROPOSALS.md) (root-cause diagnosis),
[`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md) (the live backlog)

---

## Proposal A — Grounded episode intro at the bottom of `post_content` — ✅ built

Shipped as the `ev-news-transcript-content` skill (Phase B of the `seo-article-optimize`
pipeline), proven on post 7333. Full rationale, the `post_content`-vs-`post_excerpt` decision,
the DOM-offset measurements, the tag auto-link finding and fix, and the three-paragraph
structure all moved to [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md).

**Coverage today: 91 / 128 posts.** Ingesting the ~35 missing episodes takes it to ~126 (see
Requests to the producer side, below).

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
here, not optional** — see Proposal G's ASR caveat.

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

**Blocked:** tool ships, but the free `GENERATION_MODEL` returns Bulgarian chapter titles too
generic to publish as H2s (see Requests to the producer side, item 6).

---

## Proposal D — FAQ block from real Q&A turns → `FAQPage`

The `>>` turn markers make question turns mechanically findable in 52 episodes. A host asks
something, a guest answers, and that pair is a `FAQPage` entry with a real answer instead of
an invented one.

Best on the pages whose GSC queries are already question-shaped (`има ли…`, `колко струва…`,
`къде да заредя…`). Lower priority than B/C, but it is nearly free once B exists, and FAQ
answers surface as their own SERP feature. **Depends on B.**

---

## Proposal E — Query-shaped generation: content radar, applied per page — ✅ built

Shipped as the `seo-keyphrase-research` → `ev-news-transcript-content` → `seo-article-apply`
pipeline (`SEO_SKILLS_REFACTOR.md` §2). Full rationale and the diagram moved to
[`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md) § Query-shaped generation.

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

**Blocked:** the `datetime_range` 422 bug (item 1 below) and the topic backfill (item 2 below).

---

## Proposal G — What *not* to do: publish raw transcripts *(standing decision)*

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
Proposals B, C and D. Same asset, published in the shape that is defensible. This is a
**permanent** decision, not a proposal awaiting a build slot.

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
   The mapping is currently a regex against video titles, done client-side
   (`tools/resolve_episode.py`, a stopgap), and it is the first step of every workflow above.
   It should live in the server. *(Titles carry two schemes — `EVN67` and `EV133` — so match
   `EVN?\s*\d+`.)*
4. ✅ ~~**`timestamp_url` on every returned chunk.**~~ **Shipped** — confirmed present on
   every `search_transcripts` and `ask` source.
5. **Ingest the ~35 missing episodes.** 91/128 posts are covered; the gap is mostly
   EVN44–EVN64, plus EV100 and EV121.
6. ✅ ~~**Reorder: pull Phase 2 (chapters) ahead.**~~ **Shipped** — `generate_chapters`
   returns in ~5 s. But see the model caveat: on the free `GENERATION_MODEL` the Bulgarian
   chapter titles come back too generic to publish as H2s. **This makes plan §8's
   generation-model decision a live blocker for Proposal C**, where it was not one for the
   now-built Proposals A and E (written client-side by Claude, not the server's model — see
   `EV_NEWS_CONTENT_METHOD.md` § Where the writing happens).
7. **Revisit the Phase 5 estimate** given the `>>` markers finding in Proposal B.

---

## Suggested order of execution

| # | Proposal | Needs | Effort | Impact |
|---|---|---|---|---|
| — | ~~**A** — grounded intro~~ | — | — | **✅ Built**, see `EV_NEWS_CONTENT_METHOD.md` |
| — | ~~**E** — query-shaped text~~ | — | — | **✅ Built**, see `EV_NEWS_CONTENT_METHOD.md` |
| 1 | **B** — hosts' own claims block | Phase 6 (or 52 clean episodes now) | Medium | **High** — the only uncopyable content |
| 2 | **C** — chapters → H2 outline + `Clip` schema | tool ready; needs a better `GENERATION_MODEL` | 1 day theme + per-post | **High** — no writing required |
| 3 | **F** — cross-episode evergreen hubs | blocked: 422 bug + topic backfill | Medium–High | **Highest ceiling**, slowest |
| 4 | **D** — FAQ block | after B | Low | Medium |
| — | ~~**G** — raw transcript pages~~ | — | — | **Do not build** |

---

## Open questions

1. ~~**Store generated text in `post_excerpt`, `post_content`, or a new meta field?**~~
   **Resolved — see [`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md).** Proposals C
   and D still need block-level structure, so they also want `post_content` and a theme
   change; that decision stands open.
2. **Automate in the EV News Automator pipeline, or run as a Claude Code skill per post?**
   The skill route gives editorial review per page and reuses the existing
   `seo-article-optimize` pattern. The pipeline route covers every future episode with no
   human in the loop. Recommend: skill first for the back-catalogue posts, pipeline once
   the prompt has stabilised on real pages. **Currently: skill route, per
   `SEO_SKILLS_REFACTOR.md`.**
3. **Does published transcript-derived text need a visible disclosure** that it is derived
   from the episode? Editorially cleaner; costs nothing in SEO terms.
4. **How much human review per page?** Proposal B publishes attributed quotes about named
   companies. The validator catches fabrication; it does not catch ASR mangling a brand
   name. Recommend a human read on anything carrying a `«»` quote.
