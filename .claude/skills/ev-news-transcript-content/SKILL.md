---
name: ev-news-transcript-content
description: Fix thin content on a carlifebydani.com EV News episode page by writing a grounded 130–190 word Bulgarian intro into post_content, sourced from the youtube-rag MCP's transcript archive rather than invented. Resolves the post to its episode, searches the episode's own transcript for the answer to the page's title question, falls back to the archive when the episode doesn't discuss it, drafts three paragraphs each with a timestamp-verified claim, adds two internal links, and applies after approval. This is Phase B of the seo-article-optimize pipeline — does not choose keywords or tags (Phase A / seo-keyphrase-research's job, runs first) and does not touch Yoast metatags (Phase C / seo-article-apply's job, runs after). Use when the user asks to "fix the thin content on this EV News post", "write the intro from the transcript", "answer this episode's title", or names an EV News post after Phase A has already run on it.
---

# EV News Transcript Content

**Goal: give a post its 130–190 words of owned, grounded Bulgarian prose —
sourced from what the hosts actually said, deep-linked to the second.** Not a
paraphrase of the episode description, and not invented to sound plausible.
Every factual claim in the output must trace to a transcript timestamp.

This is the fix for the root cause diagnosed in
[`docs/SEO_EV_NEWS_PROPOSALS.md`](../../../docs/SEO_EV_NEWS_PROPOSALS.md) and
implemented per [`docs/EV_NEWS_CONTENT_METHOD.md`](../../../docs/EV_NEWS_CONTENT_METHOD.md)
— read that doc's "The corpus", "Coverage map" and "The cross-episode finding"
sections before running this the first time.

**This is Phase B of the `seo-article-optimize` pipeline** — normally invoked
by that orchestrator between Phase A (`seo-keyphrase-research`, which picks
the keyphrase and tags) and Phase C (`seo-article-apply`, which writes
metatags/tags/alt/links). This skill doesn't touch Yoast fields or `post_tag`
at all; it only writes `post_content`. Run directly only when resuming a
report that already has `Status: researched` and a `Keyphrase:` line — Phase A
must run first so ¶1 has a phrase to front-load. The orchestrator also gates
this phase on transcript availability before starting Phase A at all (see
`docs/SEO_SKILLS_REFACTOR.md` §W12) — don't invoke this skill standalone on a
post whose episode isn't ingested yet.

---

## Constants specific to this skill

| Thing | Value |
|---|---|
| MCP server | `youtube-rag`, registered project-scope in [`.mcp.json`](../../../.mcp.json), `http://localhost:8000/mcp` |
| Backing stack | `docker compose up -d` in `~/Projects/youtube-rag-n8n` — must be running |
| Corpus | collection `clbd`, 99 videos / 12,669 chunks as of 2026-08-14 — **not the whole back-catalogue**, check coverage first |
| Episode-title schemes | both `EVN67` and `EV133` appear — match `EVN?\s*[-–]?\s*(\d+)`, never bare `EV\d+` |
| Episode resolver | `tools/resolve_episode.py` — **stopgap** until the MCP ships its own `resolve_episode` tool (see Requests to the producer side in the proposals doc) |

**Read [`_shared/constants.md`](../_shared/constants.md) before Step 0.** It has
the site constants table (WP alias, writer account, REST base, WAF trap, the
report directory) and the traps that apply to every phase of the pipeline —
not repeated here.

**Known producer-side bugs, still open as of 2026-08-14** (see
[`docs/MCP_SERVERS.md § YouTube-RAG`](../../../docs/MCP_SERVERS.md#youtube-rag)):
- Any tool call carrying `date_from`/`date_to` 422s (Qdrant `datetime_range` vs
  `range`). Don't use date filters in this skill until it's fixed.
- `trending_topics` has nothing to rank (no `topics[]` backfill run yet) —
  irrelevant to this skill, but don't reach for it here either.

---

## Procedure

### Step 0 — Confirm the stack is up

```bash
curl -s http://localhost:8000/health
# {"status":"ok","mcp":"/mcp"}
```
If this fails or `"mcp":"disabled"`, tell the user to `docker compose up -d`
in `~/Projects/youtube-rag-n8n` and stop. Do not proceed on a guess.

### Step 1 — Read the post

```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id-or-slug-lookup>",
  method="GET", params={"context":"edit","_fields":"id,slug,title,content,excerpt,tags"})
```
Confirm `content.raw` is just the video embed (or has room below it — this
skill **appends**, it does not overwrite). If `post_excerpt` is non-empty,
flag it for the user: this skill does not use that field, and mixing the two
duplicates text on the page (see the `post_excerpt` trap in
`_shared/constants.md`).

### Step 2 — Resolve the episode

If invoked directly rather than by the `seo-article-optimize` orchestrator,
this doubles as the orchestrator's own precondition check (§W12) — either
way, don't skip it.

```bash
python3 tools/resolve_episode.py "<episode number or title fragment from the post title>"
```
Three outcomes:
- **Resolved** → note the `video_id`, `n_chunks`, `published_at`.
- **`NOT_INGESTED`** → **stop.** Tell the user this post is blocked until the
  episode is ingested on the producer side (`~/Projects/youtube-rag-n8n`).
  Add a note to `docs/SEO_EV_NEWS_TODO.md` if one doesn't already exist for
  this gap (EV41/post 1751 is the known example).
- **`AMBIGUOUS`** → the episode number appears on more than one video title
  (rare but seen — episode-number collisions are tracked in the TODO's P2
  duplicate-URL section). Pick using `published_at` closest to the post's own
  `date`, and say so in the report.

### Step 3 — Search the episode's own transcript first

```
mcp__youtube-rag__search_transcripts(
  query="<the page's title, as a question>",
  video_id="<resolved video_id>", top_k=5)
```
Read the returned chunks. If they genuinely answer the title's question, this
is the "own episode" case — proceed to Step 5.

### Step 4 — If the episode doesn't discuss it, search the archive

**This is not a fallback to skip — check every time.** The finding on post
7333: EV114 is titled *"Има ли регистриран Tesla Cybertruck в България?"* and
the word "Cybertruck" appears zero times in its own transcript. Episode
titles are drawn from the news-roundup CSV, not from what was discussed on
air — this happens often enough that scoping search to one `video_id` is the
wrong default.

```
mcp__youtube-rag__search_transcripts(query="<the title, as a question>", top_k=8)
```
(no `video_id` — searches the whole `clbd` collection). Prefer results from
episodes published close to the post's own date. If nothing in the archive
answers it either, say so plainly in the report — do not stretch a loosely
related chunk into an answer, and do not write ¶1 without a real one.

### Step 5 — Draft the three paragraphs

| ¶ | Words | Job |
|---|---|---|
| **1** | 40–60 | Answers the page's own headline question, keyphrase near the front — read the `Keyphrase:` line Phase A wrote to the report. |
| **2** | 50–70 | What the hosts actually said — first-hand, specific, the uncopyable part. Prefer direct paraphrase over invented color. |
| **3** | 40–60 | What the episode *itself* actually covers (from Step 3, even if ¶1/¶2 came from elsewhere) — named with real brand/model terms, not "various other topics." |

**Every claim needs a `timestamp_url`.** Build a source table before writing
prose — claim, quote/paraphrase, `timestamp_url` — the same way the 7333
report did; it's what makes the draft checkable instead of trusted.

**ASR garbles numbers and brand names — the recognizer's most common failure.**
If a count, price, or name is ambiguous in the transcript (e.g. "два" vs "три"
audible as one blur), do not silently pick one. Hedge in the prose ("броят им
се брои на пръсти" beats a fabricated number) and flag it in the report as an
open fact to confirm with the user.

### Step 6 — Two internal links

Same technique as `seo-article-apply` Step 4 — `/wp/v2/search` on the
entities involved. Prefer:
1. Another post that already covers the cross-episode source material (Step
   4's find, if used) — this is the link that costs nothing extra, since the
   research already surfaced it.
2. An evergreen `/ev-review/` or `/publications/` page over another news
   episode.

**Before linking to any candidate, check whether it's a same-story sequel —
a post narrating the *same continuing news event* as this one, just at a
different point in time (an earlier rumor/announcement and its later
confirmation, part 1 and part 2 of a developing story).** If it is, and the
candidate is *newer* than the post being drafted, don't link to it — an older
post shouldn't narrate a newer post's outcome as if it were already settled
fact at its own publish time. Either skip the link (the newer post can cite
back to this one when *it* gets optimized) or, if you're certain the shared
event is real and current, flag it in the report for a human decision rather
than writing it. This restriction is narrower than it sounds: a candidate
covering a genuinely different subtopic (e.g. this episode mentions a battery
in passing, another post has the technical deep-dive) is an ordinary
"further reading" link and is fine regardless of date — only same-event
sequels need the check. Confirmed necessary 2026-08-15: post 7577 (#EV122,
the original Zeekr-Bulgaria market-entry announcement) had a Phase B link
added forward to post 8659 (#EV151, the confirmed-entry follow-up 7 months
later) — caught by the user after publish, fixed by reverting it and citing
7577 from 8659 instead.

Link naturally inside the prose (anchor text = a real phrase, never "тук").

### Step 7 — Append to the existing report

Open the report Phase A already created — `reports/seo-metatags/<YYYY-MM-DD>-
<post-id>-<short-slug>.md` — and append the § Phase B section from
[`_shared/report-template.md`](../_shared/report-template.md): the resolved
episode, whether the answer came from the episode itself or the archive (and
which episodes), the full source-claim table, the draft paragraphs, and open
facts to confirm. **Never start a new file** — if no report exists yet for
this post (this skill run standalone, ahead of Phase A), start one using the
same template rather than an ad-hoc structure.

### Step 8 — Ask, then apply

`post_content` **is** covered by WordPress revisions (unlike Yoast postmeta),
so there's no separate CSV backup ritual — but this is still visible,
production body copy. **Always ask before writing.**

```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="POST", params={"content": "<existing embed block>\n\n<wp:paragraph blocks for ¶1-¶3>"})
```
Send the **full** `content` (existing block + appended paragraphs) — this
endpoint replaces `content`, it doesn't append. Never send only the new
paragraphs.

If `post_excerpt` is non-empty from an earlier run of this skill or a manual
edit, clear it in the same call (`"excerpt": ""`) — never leave both fields
holding the text.

### Step 9 — Verify

1. Re-fetch the rendered page. Confirm: paragraphs appear after the video
   embed and before the news-card list; no duplicate text in the (now empty)
   excerpt slot; Yoast schema `wordCount` moved off its pre-write value.
2. **Count `/tag/` links inside the new paragraphs specifically** — the theme
   auto-links post tags into `the_content`
   ([`theme/functions.php:75`](../../../theme/functions.php#L75); lowered to
   1× per tag 2026-08-14, see `docs/SEO_SKILLS_REFACTOR.md` §W7). If Phase A
   already assigned tags, they will now auto-link inside this prose — note the
   count in the report.
3. Set **`Status: content-written`** in the report header.
4. Tick the row in `docs/SEO_EV_NEWS_TODO.md` and note the `wordCount` delta.
5. **Append one row to `reports/seo-optimizations/ledger.csv`** at the same
   moment as the write: `phase=B`, `changed=content`, baseline columns from
   the 28-day GSC window ending yesterday for this URL (reuse Phase A's pull
   if fresh), `verify_due = date_applied + 56d` — longer than Phase C's 28,
   because new body text needs to be crawled, indexed and start ranking
   before a check would mean anything. Add the row id to the report's
   `Ledger:` line.
6. Same measurement plan: re-check GSC in 4–8 weeks (new body text needs to
   be crawled, indexed and start ranking, which doesn't happen in two) — the
   `verify_due` above is what actually schedules this, not just a note.

---

## Decision rules

- **Never scope the first search to `video_id` and stop there if it comes up
  empty.** Empty on one episode is not empty on the archive — see Step 4.
- **No quote, no claim.** Anything not traceable to a transcript passage gets
  cut, not softened into vague plausible prose.
- **`post_content`, never `post_excerpt`.** Settled by measurement, not
  preference — see `_shared/constants.md`'s trap.
- **Don't decide tags here.** If the draft surfaces an entity that seems
  tag-worthy, mention it in the report for `seo-keyphrase-research` (Phase A)
  to evaluate against real demand next time — don't add it directly.
- **A blocked episode is a stop, not a workaround.** Never draft from the
  episode description, the news-CSV summaries, or general knowledge of the
  brand/model as a substitute for a missing transcript.

## Known traps

- **Episode titles lie about content.** Confirmed root cause on 7333 — always
  run Step 3 before assuming Step 4 is unnecessary, and always run Step 4
  before assuming the archive has nothing either.
- **ASR drops or mangles the exact token that carries the fact** — often a
  number or a brand name, i.e. exactly what a snippet needs to be worth
  publishing. Verified on 7333 (a Cybertruck count blurred between "два" and
  "три") and on EV114 itself ("Cybertruck" not recognized at all in its own
  transcript). Never resolve the ambiguity by picking the more plausible
  reading — hedge or omit.
- **This corpus is not the whole back-catalogue.** 99 of ~161+ episodes are
  ingested as of 2026-08-14. `resolve_episode.py` returning `NOT_INGESTED` is
  expected and common, not a bug to work around.
- **`tools/resolve_episode.py` is a stopgap reading Qdrant directly**, not
  going through the MCP — because the MCP has no resolver tool yet. If the
  producer ships one, switch to it and delete the script; don't let the
  workaround calcify into the permanent path.
- **The `POST /wp/v2/posts/<id>` `content` field replaces, it does not
  append.** Always send the full block (existing embed + new paragraphs), or
  the video embed itself gets wiped.
