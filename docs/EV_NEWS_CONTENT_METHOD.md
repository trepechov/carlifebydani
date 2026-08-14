# EV News Content Method

**Status:** documentation — this describes what is **built and shipping**, not a proposal.
For work not yet built, see [`SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md)
(Proposals B, C, D, F) and [`SEO_SKILLS_REFACTOR.md`](SEO_SKILLS_REFACTOR.md) (the pipeline
that runs this).

This is the measured facts and settled decisions behind the `ev-news-transcript-content` skill
(Phase B of the `seo-article-optimize` pipeline) and the query-shaped research it feeds from
(`seo-keyphrase-research`, Phase A). Split out of `SEO_TRANSCRIPT_MCP_PROPOSALS.md` on
2026-08-14 (`docs/SEO_SKILLS_REFACTOR.md` §W9) because that document had drifted into
describing already-shipped work as a pending proposal — load-bearing facts belong here, live
proposals stay there.

---

## Why grounded content, not an episode summary

The starting idea was *"generate some text summarizing the youtube video content."*
Directionally right, but literally that is the **weakest** thing this corpus can do:

**Nobody searches for an episode summary.** There is no query volume for "EV114 обобщение".
An episode-shaped summary produces episode-shaped text, which competes for episode-shaped
queries, which are branded and already won. It adds words to the page and changes almost
nothing about which queries the page can rank for.

What *does* have demand is **the question the episode's own title asks**. Concretely:

- Post 7333's title asks *"Има ли регистриран Tesla Cybertruck в България?"* The page's body
  answer was **17 words**, and — see the cross-episode finding below — the real answer isn't
  even in that episode's own transcript. It's in EV113 and EVN71, deep-linked to the second.
- Post 6165 sits at **position 1.4** for `тесла джунипер` and converts **2.06%** of 3,927
  impressions. Not a ranking problem — a "the page has nothing to read" problem.

So generation is **query-shaped, not episode-shaped**: GSC (or, absent that, the page's title)
says which question a page needs to answer, the transcript archive supplies the hosts' actual
answer, and the page publishes it, grounded and timestamped.

## The corpus (measured 2026-08-14)

`clbd` collection in Qdrant:

| | |
|---|---|
| Videos ingested | **99** (all from the `EVNews` playlist) |
| Chunks | 12,669 |
| Total transcript text | **14.9 M characters ≈ 2.2 M Bulgarian words** |
| Median episode length | ~185 min |
| Punctuated transcripts (usable prose) | **65 / 99** |
| Transcripts carrying `>>` speaker-turn markers | **52 / 99** — every 2026 episode has them |
| By year | 2024: 23 ingested, **0** punctuated · 2025: 46 / 35 · 2026: **30 / 30** |

The site owns **2.2 million words of original Bulgarian spoken commentary that exists nowhere
else in text form on the internet.** The pages built on top of it, before this work, published
zero words of it.

## Coverage map

| | |
|---|---|
| Posts in the EV News category | 128 |
| …with a YouTube embed in `post_content` | 126 |
| …that map to an already-ingested episode by episode number | **91** |
| Median `post_content` word count (pre-fix) | **0** (embed block only) |
| Posts with a non-empty `post_excerpt` | **0** — never used, see the field decision below |
| Yoast `wordCount` on a live page (pre-fix) | **17** (derived from `post_content` only) |

Episode-title schemes: `EVN67` and `EV133` both appear — the resolver matches `EVN?\s*[-–]?\s*(\d+)`,
never bare `EV\d+`. `tools/resolve_episode.py` is the stopgap resolver until the MCP ships
`resolve_episode(episode_ref)` (producer request, still open).

**Coverage caps Phase B at 91/128 posts.** `NOT_INGESTED` is the correct, expected outcome for
the other ~35 until the producer side ingests them — not a bug to route around (see
`SEO_SKILLS_REFACTOR.md` §W12: the pipeline doesn't start on those posts at all until the
transcript exists).

## 🔑 The cross-episode finding

Discovered while drafting post 7333, 2026-08-14. **A page's title question is often not
answered in that page's own episode.** Episode titles are drawn from the news-roundup CSV —
what external articles that week covered — not from what the hosts actually discussed on air.

EV114 is titled *"Има ли регистриран Tesla Cybertruck в България?"* and the words *Cybertruck*
and *сайбър* appear **zero times** in its 2h16m transcript. The archive answers it anyway, in
two other episodes:

- **EVN71** (2024-09-10, post 5350) — the first Cybertruck unloaded in Bulgaria, on Varna
  plates, still carrying its US plate.
- **EV113** (2025-08-12, one week *before* EV114) — the import route, and a first-hand count
  of the ones the hosts know of in the country.

**Consequences, load-bearing for the skill:**
1. **Never scope `search_transcripts` to `video_id` alone.** Search the whole collection first;
   prefer the page's own episode when it has material, fall back to the archive when it
   doesn't. A video-scoped search on 7333 returns nothing and looks like an empty archive when
   the archive isn't empty at all.
2. **Cross-episode answers are internal links for free** — answering 7333 naturally links to
   5350 and to the `/ev-review/` Cybertruck page.
3. **ASR drops brand names, not just facts.** The word "Cybertruck" — the subject of the
   episode's own title — wasn't recognized at all in its own transcript. Never treat an
   ASR-empty result as proof the topic wasn't discussed; check the archive before concluding
   that.

## The `post_content` vs `post_excerpt` decision

**Settled on post 7333, 2026-08-14 — `post_content`, never `post_excerpt`.** Two independent
reasons:

1. **The excerpt slot renders poorly for this post type.** It's an unused full-width
   `col-span-3` row above a 2/3-width column
   ([`theme/single.php:100`](../theme/single.php#L100)), giving ~180-character lines at
   `max-w-screen-2xl` — the original feedback that triggered the move, before `wordCount` was
   even checked.
2. **Yoast's `wordCount` only reads `post_content`.** 154 words written to `post_excerpt` left
   it at **17**; the same words appended to `post_content` took it to **168**, and made the
   prose visible to Yoast's content analysis — the measured confirmation.

Copy goes at the **bottom of `post_content`**, after the video embed and before the news cards.
Measured DOM order on the live page after the change:

| element | offset |
|---|---|
| `<h1 class="title">` | 44,660 |
| YouTube embed | 47,534 |
| **the three paragraphs** | **47,798** |
| first news card | 52,739 |

**Never write both fields** — that duplicates the text on the page; clear `post_excerpt` if it
holds anything from an earlier run. `post_content` is a core field, REST-writable, and —
unlike Yoast postmeta — **covered by WP revisions**, so it needs no separate backup ritual.

> ⚠️ **REST trap:** the WP REST API reports a 56-word `excerpt.rendered` on 9 of the 128 posts,
> which looks like they already have an excerpt. That's WordPress auto-trimming
> `post_content` for the API response — the template reads the **raw `post_excerpt` field**,
> which is genuinely empty. Don't infer "has an excerpt" from `excerpt.rendered`.

## The three paragraphs

Not "this episode covered X, Y, Z" — each paragraph has a distinct job:

| ¶ | Words | Job |
|---|---|---|
| **1** | 40–60 | **Answers the page's own headline question**, focus keyphrase verbatim near the front. This is what Yoast turns into the meta description and what Google lifts as the snippet. |
| **2** | 50–70 | **What the hosts actually said** — the first-hand Bulgarian angle from the transcript. The part nobody else can copy. |
| **3** | 40–60 | **The other notable stories**, named with real brand/model terms, with an internal link where one exists. |

130–190 words total: enough to move `wordCount` and give real keyword coverage, short enough
not to compete with the news-card list for attention.

## Query-shaped generation, applied per page (the shipped pipeline)

```
GSC (or, absent impressions, the page's own title)
  → the question a page needs to answer
        │
youtube-rag MCP: search_transcripts — own episode first, archive on fallback
        │
  → the hosts' own answer, at a timestamp
        │
Claude writes the grounded paragraphs; seo-keyphrase-research separately
picks the keyphrase and metatags from the same GSC/autocomplete/SERP research
        │
wordpress MCP: write post_content (never post_excerpt)
        │
  → GSC CTR / impression delta in 2–4 weeks (metatags) or 4–8 weeks (new body text)
```

This is the `seo-keyphrase-research` (Phase A) → `ev-news-transcript-content` (Phase B) →
`seo-article-apply` (Phase C) pipeline documented in
[`SEO_SKILLS_REFACTOR.md`](SEO_SKILLS_REFACTOR.md) §2, orchestrated by `seo-article-optimize`.
Semrush/DataForSEO only enter when a page has no impressions yet and the query has to be
discovered rather than read off GSC.

## Where the writing happens — client-side, not the MCP server's model

The producer-side plan flagged that its free `GENERATION_MODEL` won't carry Bulgarian SEO copy
and budgeted a paid generation model for that. **For this consumer, that decision is not on
the critical path.** The SEO pipeline runs inside Claude Code, which is already a strong
Bulgarian model with the site's own GSC data and the WordPress write path in context. The
MCP server's job for this workflow is to **return grounded raw chunks with timestamps** — not
to summarise them first — which is also the producer plan's own stated default
("letting *it* reason over raw grounded chunks usually beats a small server-side model
summarising first").

Practical consequence: `search_transcripts` + `get_transcript` + `generate_chapters` are
enough. The server's synthesising tools (`seo_brief`, `opinion_digest`) are convenience, not
prerequisites.

## Tags: two tiers, and why the auto-linker measurement matters

The news-card count is higher than the theme shows — up to 68 on some episodes, 27–28 typical.
Tagging an episode post for every story it links to inherits ~28 stories' worth of tags for a
title that covers 2, diluting topical focus and spawning the thin `/tag/` archives the site
already has too many of (`/tag/clbd/` absorbs 418 brand impressions at 4 clicks while
outranking real pages).

**Post tags = only the 1–2 headline stories' entities**, matching the focus keyphrase and ¶1.
Everything else stays research metadata and never becomes a public tag term — the full rule,
including the 3–10 use-count band, is `seo-keyphrase-research` Step 4b
(`SEO_SKILLS_REFACTOR.md` §W11).

**Measured cost of not budgeting for this:** [`theme/functions.php:75`](../theme/functions.php#L75)
`add_tag_links_to_content` links every post tag wherever its name appears in `the_content`. On
post 7333, before the fix, 8 tags injected **10 `/tag/` links into 154 words of fresh prose** —
one per 15 words, outnumbering the 2 deliberate editorial links 5:1. **Fixed 2026-08-14**
(`SEO_SKILLS_REFACTOR.md` §W7) — the per-tag cap dropped from 5 occurrences to 1. Still worth
checking on every run: re-fetch the rendered page after writing body prose and count `/tag/`
links inside it.

## Which stories count as "top" — the news CSV's row order

Picking the 1–2 headline stories for ¶3 used to be undocumented editorial judgment. It isn't:
the episode's `news_csv` is ordered editorially, hottest and most interesting first — see
`SEO_SKILLS_REFACTOR.md` §W3 for the verified column mapping (`upvote`/`downvote`/`clicks`
corroborate where present on modern-vintage files, but don't override the ordering).

## Still open, not yet built

**The collapsed-summary ratio problem.** EV114 carries roughly 20,600 characters of
news-card summary text inside collapsed `max-h-0` containers against ~68 nofollow outbound
links. The grounded intro fixes the *presence* of owned text; the collapsed-by-default
external summaries are a separate, unbuilt fix — showing the first 1–2 sentences of each card
by default. See [`SEO_EV_NEWS_PROPOSALS.md` §1.3](SEO_EV_NEWS_PROPOSALS.md).
