# SEO Skills — Refactoring Plan

**Created:** 2026-08-14 · **Status:** ✅ all 12 execution-order items landed 2026-08-14 — see
Progress tracker below. Two items shipped partial by explicit user decision rather than left
incomplete: W6's full 313-post GSC scan and W7's `noindex` decision are deferred, documented as
open TODOs in their own sections, not silently dropped.
**Companions:** [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md) (the live backlog) ·
[`EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md) (the built transcript-content method) ·
[`SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md) (what's still proposal-only) ·
[`MCP_SERVERS.md`](MCP_SERVERS.md) (toolchain)

## Progress tracker

Execution follows §5's order, not numeric order. Updated as each item lands — check here first
if a session was interrupted; this is the resumption point.

| Order | Item | Status |
|---|---|---|
| 1 | W9 (partial) — fix `post_excerpt` contradiction | ✅ done 2026-08-14 |
| 2 | W8 — commit what exists | ✅ already done — verified 2026-08-14, all 5 items landed in prior commits (7cdb40e, ddb01e8, 3add1f7, 9111a32) |
| 3 | W7 — tag auto-link decision | ✅ partial 2026-08-14 — limit 5→1 shipped; noindex deferred, see TODO in §W7 |
| 4 | W1 + W2 — split + handoff | ✅ done 2026-08-14 — also picked up W12 as a side effect (orchestrator rewrite touched the same lines) and the W11 wording fix (extraction-time, as W11 specified) |
| 5 | W10 — approval gate | ✅ done 2026-08-14 |
| 6 | W3 + W11 — news CSV + tag-band | ✅ done 2026-08-14 — also resolved Open Question 4 (column 3 = `author`) and registered `news_csv` for REST (needs deploy, see §W3) |
| 7 | W4 — alt write path | ✅ done 2026-08-14 — live-verified on media 7334 |
| 8 | W9 (rest) — docs restructure | ✅ done 2026-08-14 |
| 9 | W6 — generalise + rescan | ✅ partial 2026-08-14 — audit + backlog entries done; full 313-post GSC scan deferred by user decision, see §W6 |
| 10 | W5 — inbound links | ✅ done 2026-08-14 — live-verified, 7533→7333 |
| 11 | ~~W12 — gate on transcript availability~~ | ✅ done 2026-08-14, landed with item 4 |
| 12 | W13 — ledger + verification step | ✅ done 2026-08-14 — 2 rows backfilled, first verdicts due 2026-09-11 |

**The one-sentence version:** the capabilities are ~85% built and the *sequencing* is wrong —
two skills document conflicting run orders, and the half of the SEO skill that should run
*after* content exists currently runs before it.

---

## 1. Audit — what already exists

Verified by reading the skill files on 2026-08-14, not inferred.

| Capability you asked for | Where it lives today | State |
|---|---|---|
| Read the article's content | `seo-article-optimize` Step 2 — reads **both** WP `post_content` and the rendered page, because they differ on this site | ✅ built |
| Research the topics | Step 3a–3e — GSC → autocomplete → DataForSEO → Semrush → live SERP → GA4, cheapest-first, cache-gated | ✅ built |
| Propose SEO keyword | Step 4 — one focus keyphrase + 2–4 secondary, with cannibalisation check | ✅ built |
| Propose SEO description | Step 5 — title + metadesc + focuskw, char-budgeted, **applied after approval** | ✅ built |
| Propose tags | Step 4b — mapped to the site's existing 365-term vocabulary, reuse-only, capped 1–2 entity + 0–2 intent | ⚠️ built, but the **frequency rule is backwards** — see W11 |
| Image alt optimization | Step 6.4 | ⚠️ **proposed only — never written** |
| Links to other existing articles | Step 7 — inbound *and* outbound, via `/wp/v2/search` | ⚠️ **proposed only — never written** |
| EV News content from the YouTube MCP | `ev-news-transcript-content` — resolves episode, searches own transcript then the archive, 3 grounded paragraphs, every claim timestamped | ✅ built |
| Read the episode's news CSV | — | ❌ **missing** |
| Coverage beyond EV News | — | ❌ **185 posts never scanned** |
| Orchestration between the two skills | — | ❌ **manual, and the two skills document opposite orders** |
| Machine-readable record of what was already optimized | prose `Status:` lines in `reports/seo-metatags/`, worded 3 different ways across 4 files | ❌ **not queryable — see W13** |
| Verifying an optimization actually worked | — | ❌ **nothing measured after a write; no baseline frozen, no re-check due — see W13** |

**So: you were right that most of it exists.** The gaps are the last three rows plus two
half-open write paths.

### Scale check — "every article we have"

| Category | Posts | Ever scanned for SEO? |
|---|---|---|
| `ev-news` | 128 | yes — 3 posts + the hub optimized |
| `publications` | **121** | **no** |
| `ev-review` | **41** | **no** |
| `ev-masters` | **23** | **no** |
| **Total** | **313** | 3 done (1.0%) |

The original baseline scan only covered category 1, which is why post **6165**
(`/publications/…juniper…`, **3,927 impressions at 2.06% CTR, position 5.3**) has never been
in a backlog. It is worth more than the entire current P1 list combined. Post **7533**
(`/ev-review/…cybertruck…`, 237 impr, 0.84% CTR) is the same story. Both are already flagged
in [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md#highest-value-work-still-untouched) as
missing from the list.

---

## 2. The core finding — the run order is wrong in both directions

The two skills currently contradict each other's premise, and **both are half right**:

> `seo-article-optimize`: *"On EV News posts this skill does not write the body-text content
> fix — run this skill first for the keyphrase, metatags and tags; run that one after."*

> `ev-news-transcript-content`: *"Does not choose keywords or tags — that is
> seo-article-optimize's job, run first."*

Your instinct (content first, then SEO) is right about the **second half** of the SEO skill and
wrong about the **first half**. The actual dependency graph:

```
                    needs body content?
Steps 0–4b  research → keyphrase, tags      NO   ← title, GSC, autocomplete, SERP, cards
Step  5     draft meta description          YES  ← should describe the real prose
Step  6.2   keyphrase in first 100 words    YES  ← can only verify, not place
Step  6.4   image alt text                  partly
Step  7     internal links                  YES  ← anchors live inside the prose
Step  10    verify wordCount, /tag/ count   YES  ← meaningless against 17 words

¶1 of the transcript intro                  needs the KEYPHRASE from Step 4
```

Neither pure ordering works. Run SEO wholly first and the meta description gets written
against a 17-word page, `wordCount` verification is vacuous, and the tag auto-link problem is
undetectable. Run it wholly second and ¶1 has no keyphrase to front-load.

**This already happened.** Post 7333 was optimized on 2026-08-13 (metatags) and 2026-08-14
(content) — in that broken order. The consequence is recorded in the skill's own trap list:
the theme auto-linked 8 tags into **10 `/tag/` links inside 154 words**, discovered only
*after* the "finished" SEO skill had already run and signed off.

### Resolution: three phases, one entry point

```
PHASE A — seo-keyphrase-research          (extracted from optimize Steps 0–4b)
  GSC + autocomplete + SERP + news cards → focus keyphrase, secondary, tag candidates
  writes reports/seo-metatags/<date>-<id>-<slug>.md   status: researched
        │  keyphrase handed forward via the report
        ▼
PHASE B — ev-news-transcript-content       (EV News only; skipped otherwise)
  youtube-rag → 3 paragraphs, keyphrase front-loaded in ¶1, cards choose ¶3's stories
  appends to the same report                          status: content-written
        │  real post_content now exists
        ▼
PHASE C — seo-article-apply                (optimize Steps 5–10 + the two new write paths)
  meta description written against actual prose
  tags + image alt + internal links applied · wordCount and /tag/ count verified
  same report                                         status: applied
```

**`seo-article-optimize` keeps its name and becomes the orchestrator** — A→C for a normal
post, A→B→C for an EV News post. You say *"optimize post 5240"* and never think about order
again. The name is already referenced from
[`SEO_EV_NEWS_TODO.md:78`](SEO_EV_NEWS_TODO.md) and from the project memory, so keeping it
costs nothing and breaks nothing.

**The handoff mechanism already exists.** The dated report in `reports/seo-metatags/` is
already the durable artifact, and `ev-news-transcript-content` already says *"append to the
`seo-article-optimize` template if a metatag proposal already exists."* Formalising that into
a `Status:` field the phases read and advance is a small change, not a new system.

---

## 3. The approval gate — no unattended writes

**Governing constraint. Every phase, every write path, no exceptions.** A skill never changes
live WordPress content on its own authority. It researches, drafts, and *proposes*; the human
decides what ships.

Partly true today — `seo-article-optimize` Step 9b and `ev-news-transcript-content` Step 8
both say "never write without an explicit yes." But the gate is **coarse** (four fixed
bundles: metatags+tags / metatags only / tags only / don't write), the "revise first" option
has no defined loop behind it, each skill words it differently, and the two new write paths
(W4 alt text, W5 inbound links) have no gate at all yet.

### 3.1 The change manifest

Before *any* write, present a numbered manifest showing the **exact final value** — not a
summary, not "updated meta description". Grouped by write target, because that is also the
rollback boundary:

| # | Write target | Endpoint | Revisions? | Backup required |
|---|---|---|---|---|
| 1 | Yoast postmeta (title / metadesc / focuskw) | `POST /wp/v2/posts/<id>` `meta` | ❌ **none** | CSV to `reports/yoast-meta-backup/` |
| 2 | Tags (`post_tag`) | `POST /wp/v2/posts/<id>` `tags` | ❌ none | record current `tags` array — the field **replaces** |
| 3 | This post's `post_content` | `POST /wp/v2/posts/<id>` `content` | ✅ yes | full existing block must be resent |
| 4 | Media `alt_text` | `POST /wp/v2/media/<id>` | ❌ **none** | record current alt |
| 5 | **Other** posts' `post_content` (inbound links) | `POST /wp/v2/posts/<other-id>` | ✅ yes | one approval **per post** |

Each row carries before → after verbatim, plus character counts on the two length-budgeted
fields. For `post_content`, show the paragraphs as they will render, not the raw Gutenberg
block markup.

### 3.2 Selecting a subset

The user picks **any combination** — approving the meta description while rejecting the tags
must be possible, and must not silently drop the rest of the run.

Mechanically: `AskUserQuestion` with `multiSelect: true` caps at **4 options per question**,
which is why the manifest groups by write target rather than listing every individual field.
Groups 1–3 fit one question. Groups **4 and 5 get their own gates** — alt text because it has
no revision safety net, inbound links because each one edits a different post and deserves its
own yes. Never bundle a foreign-post edit into the current post's approval.

If a manifest genuinely exceeds what one question can hold, ask sequentially by group. Do not
collapse groups to fit the widget.

### 3.3 Iterating on the draft

"Revise" is a first-class outcome, not a polite decline. On revise: take the user's note,
regenerate **only** the contested items, re-present the **full** manifest (so nothing
approved-then-changed slips through unseen), and ask again. Unbounded — no "last chance"
framing, no drift toward writing something the user hasn't seen in final form.

The report in `reports/seo-metatags/` is **rewritten each iteration**, so the artifact always
matches what was actually approved. A value from a superseded draft must never survive into
the write call.

### 3.4 After a partial approval

`Status:` advances only for the items actually written. Declined items are recorded in the
report with the reason, so a later run knows they were a decision rather than an oversight —
and does not helpfully re-propose them.

---

## 4. Work items

Ordered by dependency, not by value. Effort is rough.

### W1 — Split `seo-article-optimize` into A / C + orchestrator · ~2h · ✅ done 2026-08-14

- Extract Steps 0–4b → new `seo-keyphrase-research/SKILL.md`. ✅ — also carries the W11
  tag-band wording fix, done at extraction time as W11 itself specified.
- Extract Steps 5–10 → new `seo-article-apply/SKILL.md`. ✅
- Rewrite `seo-article-optimize/SKILL.md` as a thin orchestrator: detect category, sequence
  the phases, hand the report path between them. ✅ — also added the W12 transcript-availability
  precondition check (Step 3) since the orchestrator was being rewritten anyway; §W12 below no
  longer needs its own pass.
- Move the shared **Site constants** table somewhere all three read. ✅ —
  `.claude/skills/_shared/constants.md`, confirmed working (see Open Question 1's resolution).
- Keep both skills' **Known traps** sections with whichever phase can actually trip them. ✅

### W2 — Formalise the report as the phase handoff · ~30m · ✅ done 2026-08-14

Single template, one `Status:` line advancing `researched → content-written → applied`, and a
`Keyphrase:` line Phase B reads. Each phase appends its own section rather than starting a
new file. Prevents the current situation where post 7333 has *two* separate reports
(`2026-08-13-7333-cybertruck-bulgaria.md` and `2026-08-14-7333-excerpt-draft.md`) describing
one post's optimization.

**Done:** `.claude/skills/_shared/report-template.md`, referenced by all three phase skills and
by `reports/seo-metatags/README.md`. The two pre-existing 7333 report files were left as
historical record rather than merged — the mechanism now prevents new duplicates, which is
what this item asked for; retroactively editing a closed report wasn't in scope.

### W3 — Read the episode's news CSV directly · ~1.5h · ✅ done 2026-08-14

**Read the CSV, not the rendered cards.** The `news_csv` post meta holds the URL; fetch and
parse it directly. Scraping card DOM out of the rendered page would be strictly worse — the
theme renders only a subset of the columns, and the CSV is already ordered editorially.

The theme fetches the same file at render time and maps just seven positions
([`theme/single.php:110-134`](../theme/single.php#L110-L134)):

```
[0] title   [1] description   [2] link   [3] (parsed, then dropped)   [4] upvote   [5] downvote   [6] clicked
```

But the EV News Automator writes a **12-column sheet (A–L)** including `Тема` (topic),
`Тагове` (tags) and `Регион` (region) — per story, already classified. The theme reads none of
them. That is pre-computed classification sitting unused in a file the site already fetches on
every page load.

- **Phase A** — story titles become entity seeds for demand research (today the seeds come
  only from the post title and a 17-word `post_content`), and the Automator's per-story
  `Тагове` / `Регион` become **tag candidates to validate against real demand** — not to
  adopt directly. Step 4b's reuse-only rule still governs: a suggested tag with no existing
  term is a noted gap, not a new thin archive.
- **Phase B** — **row order picks the stories.** The CSV is ordered editorially, hottest and
  most interesting first, so the top rows *are* the headline stories ¶3 should name.
  `upvote` / `downvote` / `clicked` corroborate where present, but they do not override the
  ordering. [`SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md) flags this
  choice as *"editorial and lives nowhere machine-readable"* — it does, and it is the row
  order.

> 🔑 **Row order is the only invariant. Treat the schema as versioned, not fixed.**
> The format has changed and been extended over the years — some vintages carry `Тема` /
> `Тагове` / `Регион`, others do not, and vote columns may be absent or empty on older files.
> **Order works on all of them.** So: detect the width per file, read positionally only after
> confirming the shape, treat every column except title/description/link as optional, and
> treat a short row as missing data rather than an error. Never branch the core logic on a
> column that may not exist — enrich with it when present, degrade cleanly when not.

> ⚠️ **Read two real files before building against any layout.** Open question: what column 3
> holds (parsed by `str_getcsv`, never passed to the card template — if it is the story's image
> URL it also answers the featured-image question and feeds W4). Pull `news_csv` from a recent
> post and from post 7333 to see both a modern and a back-catalogue vintage side by side.

> **The CSV never becomes a content source.** It is third-party derived — that is the whole
> reason the transcript approach exists. It informs *which stories* to cover and *what to
> research*; the transcript remains the only source of published *claims*.

**Verified against two real files, 2026-08-14 — this section's guesses were partly wrong:**
- `news_csv` isn't REST-exposed by default (plain legacy postmeta, no `register_meta` anywhere
  in the codebase) — had to register it (`theme/functions.php`, editor-gated `show_in_rest`)
  before it could be fetched at all. Confirmed via wp-admin's Custom Fields panel first, to
  avoid guessing blind while that registration waits on a manual deploy (`docs/DEPLOYMENT.md`
  — this repo's theme changes aren't live until someone uploads the zip).
- **Column 3 is `author`** (source/reporter attribution), not an image — see Open Question 4's
  resolution below. No featured-image data lives in this CSV.
- The 12-column vintage's real headers are **English** —
  `title,description,link,author,upvote,downvote,clicks,added_date,pub_date,off_topic,tags,region`
  — not the Bulgarian `Тема`/`Тагове`/`Регион` assumed above. There's no separate topic column;
  `tags` is one free-text comma-separated field.
- Post 7333's file (2025) has **6 columns only** — `title,description,link,author,upvote,downvote`
  — confirming the width-detection requirement is real, not theoretical caution.

Implemented in `seo-keyphrase-research/SKILL.md` Step 2c (fetch/parse/width-detect) and Step 4b
(tag candidates from the `tags` column, validated against the 3–10 band, never adopted directly).

### W4 — Close the image alt write path · ~1h · ✅ done 2026-08-14

Step 6.4 already documents the endpoint and correctly says *"verify on one image before
proposing a batch"* — that verification has never been run.

- Confirm `seo-bot` (Editor) can `POST {"alt_text": …}` to `/wp/v2/media/<id>`. ✅ — verified
  live on media 7334 (post 7333's featured image, empty → real Bulgarian alt text), approved
  through the W10 gate as its own separate ask, backed up first, confirmed rendering on the
  live page afterward. Addendum in `reports/seo-metatags/2026-08-13-7333-cybertruck-bulgaria.md`.
- If W3 finds an image column, describe the actual image rather than deriving alt from the
  keyphrase alone. — moot: W3 found column 3 is `author`, not an image; no image data exists
  in the news CSV, so this branch never applies.
- Add to Phase C behind the same approve-first rule as the metatags. ✅ — already landed with
  W1/W10 (`seo-article-apply` Step 3.4 and Step 6, gated as manifest group 4).
- Note: media alt is **not** covered by post revisions — back it up like Yoast meta. ✅ —
  confirmed in practice on this write; backup at `reports/yoast-meta-backup/media-7334-2026-08-14.csv`.

### W5 — Close the inbound internal-link write path · ~2h · ✅ done 2026-08-14

The higher-value half and the riskier one: it means editing `post_content` on **other**
posts. The skill itself calls inbound internal links *"the strongest on-site lever available
here."*

- Read target post → locate the anchor paragraph → insert the link → write full `content`
  back (the endpoint **replaces**, never appends — the trap that can wipe a video embed). ✅ —
  `seo-article-apply` Step 4 now has concrete Gutenberg-block mechanics: fetch `content.raw`
  via `context=edit`, locate one `<!-- wp:paragraph -->` block, edit only its `<p>` text,
  reassemble the full string, and — new — a mandatory post-write byte-diff check that every
  other block is untouched.
- One post at a time, explicit approval each, never batched. ✅ — routed through the W10
  approval gate as group 5, its own separate ask.
- `post_content` **is** revision-covered, so this is recoverable — unlike W4 and the Yoast
  fields.
- Outbound links land inside the prose Phase B writes, so they are cheaper and can ship first.

**Live-verified**, not just documented: added an inbound link from post 7533
(`/ev-review/…tesla-cybertruck…`) to post 7333 (the "is there a registered Cybertruck in
Bulgaria" post) — 7533's closing paragraph thanks the Bulgarian owner who lent his own
Cybertruck for the review, a natural, already-there opening for the link. Approved through the
gate, applied, then confirmed on the **live rendered page**: the new link present exactly
once, the video embed present exactly once, all gallery images and the spec table
byte-unchanged. `post_content` is revision-covered, so this is recoverable if it needs undoing
(revision history on the post, not a separate CSV backup).

### W6 — Generalise beyond EV News · ~3h · partial ✅ done 2026-08-14

The skill body is already category-agnostic; the *carve-outs* and the *backlog* are not.

- Re-run the GSC baseline scan across all four categories and extend
  `SEO_EV_NEWS_TODO.md` — or split a general backlog out of it, since the name will stop
  fitting. — **deferred by user decision 2026-08-14**: a 313-post scan is a large API/token
  spend for one pass; do it as its own dedicated run rather than folded into this refactor.
- Audit the EV-News-specific assumptions and gate them on category rather than assuming:
  the `post_content` ≠ rendered-page warning, the `#EV160` prefix stripping, the
  *"never target EV новини"* rule, the Phase B hand-off. ✅ — audited all four; three were
  already correctly gated from the W1 rewrite, `#EV160` stripping wasn't explicitly scoped to
  EV News and now is (`seo-article-apply` Step 2). Also found and closed a real gap while
  auditing: the orchestrator's category detection had no concrete id mapping — added a
  **Category IDs** table to `_shared/constants.md`, verified against `/wp/v2/categories`
  (`ev-news`=1/128 posts, `publications`=6/121, `ev-review`=3/41, `ev-masters`=45/23 — 313
  total, matching this doc's own audit numbers).
- **Start with 6165 and 7533** — both already identified, both bigger than anything in P1. ✅ —
  both promoted from prose mentions to real `- [ ]` backlog items in `SEO_EV_NEWS_TODO.md`
  ("Missing from this list" section). Not yet run through the pipeline — that's separate work
  from making sure they're trackable.
- `publications` and `ev-review` posts likely have real body content already, which means
  Phase B is skipped and Phase A's research reads something substantial for once. — confirmed
  by the category-id audit above; unverified by an actual pipeline run (deferred with the scan).

### W7 — Resolve the tag auto-link conflict · decision, then ~30m · partial ✅ done 2026-08-14

[`theme/functions.php:74-90`](../theme/functions.php#L74-L90) `add_tag_links_to_content` links
every post tag inside `the_content`, **up to 5× per tag**. Every post this pipeline optimizes
now feeds ~10 more links into thin `/tag/` archives — while `/tag/clbd/` already absorbs
**418 brand impressions at 4 clicks**, outranking real pages on the site's own name.

This cuts directly against the skills' own rule (*"skip `/tag/` pages as link targets"*).
Every run makes it worse, so it should land **before** W6 scales the pipeline to 313 posts.

Options: lower the `preg_replace` limit from `5` to `1` (one-character change), `noindex`
the thin archives, or both. Not a skill change — a theme/SEO-config decision.

**Done:** the `preg_replace` limit dropped `5` → `1` in `theme/functions.php` 2026-08-14 — each
tag now links at most once per post instead of up to five times.

> 📌 **TODO — noindex decision deferred.** Whether to `noindex` the 268 thin (≤2-use) `/tag/`
> archives, and how — a self-updating `functions.php` robots filter keyed on live post-count
> (code-only, no wp-admin, but a blanket rule with no per-tag override) vs. a manual Yoast
> wp-admin bulk edit (precise, per project memory Yoast taxonomy SEO isn't REST-writable so
> this needs the UI, but doesn't self-update as tags cross the threshold) — is intentionally
> left open. User decision 2026-08-14: revisit later with real usage data rather than deciding
> blind now. Needs its own review before W6 scales the pipeline further.

### W8 — Commit the uncommitted · ~30m · ✅ done (verified 2026-08-14, landed in prior commits)

Currently untracked and load-bearing:

- `.claude/skills/ev-news-transcript-content/` — an entire working skill
- `.mcp.json` — the youtube-rag registration the skill depends on
- `tools/resolve_episode.py` — the episode resolver, marked a **stopgap** until the MCP ships
  `resolve_episode` (producer request #3)
- `reports/seo-metatags/2026-08-14-7333-excerpt-draft.md` — the proof the method works
- Decide gitignore-vs-commit for `reports/yoast-meta-backup/` (open since 2026-08-13). It holds
  the only recovery path for postmeta, which argues for committing it.

### W9 — Convert the proposal docs into documentation · ~2h · ✅ done 2026-08-14

Most of [`SEO_TRANSCRIPT_MCP_PROPOSALS.md`](SEO_TRANSCRIPT_MCP_PROPOSALS.md) describes work
that now exists, so it reads as a plan for something already built. Worse, it has **drifted
into contradicting itself**, and the stale half is the dangerous one:

| Line | Says | Reality |
|---|---|---|
| 233 (Proposal A) | *"Use `post_content`, **not** `post_excerpt` — settled on 7333"* | ✅ correct, measured |
| 332 (Proposal E) | `wordpress MCP → write post_excerpt` | ❌ stale |
| 459–461 (Open Q1) | *"**Resolved** 2026-08-14 — `post_excerpt`"* | ❌ **stale, and it is the "resolved" answer** |

Two separate reasons killed `post_excerpt`, not one:

1. **The excerpt slot doesn't render well for this post type** — the original feedback that
   triggered the move on 7333, before `wordCount` was even checked.
2. **Yoast's `wordCount` only reads `post_content`.** 154 words written to `post_excerpt` left
   it at **17**; the same words in `post_content` took it to **168** — the measured
   confirmation, not the original reason.

The shipped shape: transcript-grounded prose appended to **`post_content`, at the end of the
post** (after the video embed, before the news cards), never to `post_excerpt`. Anything
reading the Open Questions section for the settled answer gets the one that was tested and
rejected. Fix this regardless of whether the rest of W9 happens.

**Implementation status of the seven proposals:**

| | Proposal | State |
|---|---|---|
| A | Grounded intro → `post_content` | ✅ **built** — the `ev-news-transcript-content` skill, proven on 7333 |
| E | Query-shaped generation (GSC → transcript → page) | ✅ **built, unnamed** — this is precisely the A→B→C flow in §2 |
| B | Hosts' own claims block | ⏳ proposal — needs producer Phase 6 (`extract_opinions`) |
| C | Chapters → H2 outline + `Clip` schema | ⏳ proposal — tool ships, but free `GENERATION_MODEL` returns unpublishable BG titles |
| D | FAQ block → `FAQPage` | ⏳ proposal — depends on B |
| F | Cross-episode evergreen hubs | 🚫 blocked — producer `datetime_range` 422 + no topic backfill |
| G | Don't publish raw transcripts | 📌 standing decision — permanently true, never a proposal |

**Done — the split:**

- **[`docs/EV_NEWS_CONTENT_METHOD.md`](EV_NEWS_CONTENT_METHOD.md)** (new, documentation) — the
  measured facts and settled decisions: corpus stats (99 videos / 12,669 chunks / 14.9M chars,
  65 punctuated, 52 with `>>` markers), the 91/128 coverage map, the **cross-episode finding**
  (EV114's own transcript never says "Cybertruck"), the `post_content` decision with its
  DOM-offset evidence, the tag auto-link measurement, and the `excerpt.rendered` REST trap.
  All of this is load-bearing for the skills and none of it is a proposal.
- **`SEO_TRANSCRIPT_MCP_PROPOSALS.md`** — shrunk to keep only B, C, D, F, the producer
  requests, and G as a standing decision. Retitled *"Further Proposals (B, C, D, F)"* so it
  stops implying A and E are pending.

Two neighbouring docs were checked for drift in the same pass, lightly (a header caveat, not a
full rewrite — out of scope for this item):
[`SEO_EV_NEWS_PROPOSALS.md`](SEO_EV_NEWS_PROPOSALS.md) (2026-07-28 — its P0 locale bug is now
flagged **✅ RESOLVED** inline rather than reading as open) and
[`SEO_PROPOSALS.md`](SEO_PROPOSALS.md) (2026-06-18, 34 KB — flagged as not re-reviewed since,
pointing at the newer docs for current ground).

---

### W10 — Implement the shared approval gate · ~2h · ✅ done 2026-08-14

Turn §3 into one shared procedure all phases invoke, rather than three skills each wording it
their own way.

- Write it once (candidate: `.claude/skills/_shared/approval-gate.md`, same question as W1's
  shared constants — see Open Questions). ✅ — built at that exact path, reusing the pattern
  Open Question 1 already confirmed works.
- Replace `seo-article-optimize` Step 9b's four fixed bundles with the manifest + multiSelect
  + revise loop. ✅ — `seo-article-apply` Step 6 now runs the shared gate instead of embedding
  its own version of the mechanics.
- Give W4 (alt text) and W5 (inbound links) their own gates from the start — neither has one.
  ✅ — the gate's groups 4 and 5 are gated separately by construction; W4/W5 still need to build
  the actual write mechanics (the gate exists ahead of them, as required).
- Add the `Status:` / declined-items convention from §3.4 to the W2 report template. ✅ — Phase
  C section of `_shared/report-template.md` now has a `### Declined` subsection.
- **Prerequisite for W4 and W5.** Both open new write paths; neither ships before the gate
  covering it exists. — satisfied; W4/W5 can now proceed.

### W11 — Tag selection: target the frequency band · ~1h · ✅ done 2026-08-14 (landed with W1 + W3)

Belongs in **Phase A**, alongside the keyphrase — tags come from demand research, not from
re-reading whatever prose ends up on the page.

**The golden rule: choose a tag that already exists *and* sits in the 3–10 use band.**

Existence alone is not enough. Measured across all 365 tags on 2026-08-14:

| Uses | Tags | Share | Verdict |
|---|---|---|---|
| 0 | 24 | 7% | ❌ orphaned — a term with no archive behind it |
| 1 | **187** | 51% | ❌ **the liability** — adding a post makes it 2, still thin |
| 2 | 57 | 16% | ❌ still thin |
| **3–10** | **83** | **23%** | ✅ **the target band** |
| 11–19 | 11 | 3% | ⚠️ acceptable — established, but saturated |
| ≥20 | 3 | 1% | ⚠️ `Tesla` 57 · `Volvo` 24 · `Hyundai` 20 — at most one, only if genuinely the headline entity |

**73% of the vocabulary sits at ≤2 uses.** Those 268 near-empty archives are the same
mechanism behind the `/tag/clbd/` finding — thin taxonomy pages outranking real editorial
content, absorbing **418 brand impressions at 4 clicks**. Tagging into them makes the problem
one post worse; the fix (W7 + the open `noindex` decision) is to shrink them, not feed them.

The 3–10 band is where a tag is a *real topical cluster* a post meaningfully joins —
`Model S` (10), `IONIQ 5` (10), `Разход` (7), `Премиера` (6), `BYD` (5), `Supercharger` (5),
`китайски електромобили` (6). At the top end, `Tesla` at 57 is a brand hub that is already
saturated: one more post adds no differentiating signal.

> ⚠️ **This reverses the current wording.** Step 4b today says *"`count` is the signal for
> 'this is an established landing page'"*, which reads as higher-is-better. It is a **band**,
> with a floor and a ceiling — and both ends are wrong for different reasons. Correct the text
> when W1 extracts Step 4b into Phase A.

Unchanged from today: **reuse only, never create speculatively**, and the 1–2 entity + 0–2
intent cap. A needed concept with no existing term stays a noted gap in the report, so
recurring gaps get batch-created deliberately rather than one-off per article. W3's
Automator-supplied `Тагове` feed this rule as *candidates* — they are checked against the band
like any other, never adopted because the CSV suggested them.

### W12 — Gate EV News optimization on transcript availability · ~1h · ✅ done 2026-08-14 (landed with W1)

**Done as a side effect of W1.** The orchestrator was being rewritten from scratch anyway, so
the precondition check landed in the same pass as Step 3 of `seo-article-optimize/SKILL.md`
rather than waiting for its own turn in the execution order — no reason to touch that file
twice for two items that both live in the same few lines.

Resolves the open *"per-post skill vs. pipeline automation"* question in
[`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md) — **not** by splitting the run into two passes,
but by not starting it at all until it can finish in one:

- **No two-phase trigger.** Earlier drafts of this item proposed a Stage 1 (embed-only, day 0)
  and a Stage 2 (transcript backfill, day 1–3) as two separate runs. Rejected: there is no
  reason to run the optimization pipeline on day 0 at all — Phase A's research doesn't need to
  happen before Phase B can ground content, and running metatags against a placeholder page
  just to redo them later is wasted work, not progress.
- **The actual rule: for EV News posts, don't trigger `seo-article-optimize` (A→B→C) until the
  episode's video is finished and its transcript is ingested.** One run, once, when it can
  complete end to end — same as every other category already works today.
- **Precondition check, not a new stage.** Before starting Phase A on an EV News post, resolve
  its episode and confirm the transcript exists — `tools/resolve_episode.py <episode-ref>`
  (or the MCP's future `resolve_episode` tool, producer request #3) exiting `0` rather than
  `NOT_INGESTED`. If not yet ingested, don't start the run — wait, don't half-run it.
  Non-EV-News categories skip this check entirely; they have real body content from day one.
- **Depends on W1** so Phase A has a clean entry point to gate, and on transcript coverage
  itself — currently 91/128 EV News posts (producer request #5 covers the rest).

### W13 — The optimization ledger and the verification loop · ~2h · ✅ done 2026-08-14

**Done:**
- `reports/seo-optimizations/ledger.csv` and `checks.csv` created with the exact schemas
  below, plus a `README.md` explaining both (mirrors `reports/seo-metatags/README.md`'s style).
- `_shared/report-template.md` gained the `Ledger:` header line and a `## Verification`
  section (appended by the monthly check, never a new file).
- `seo-keyphrase-research` Step 0 now greps `ledger.csv` by `post_id` before any research.
- `seo-article-apply` Step 7 and `ev-news-transcript-content` Step 9 each append a ledger row
  at the same moment as their write (`phase=C` / `phase=B`), from the same approved-changes
  data — `verify_due` = `+28d` / `+56d` respectively.
- `seo-performance-report` gained **Step 4a — Verify optimizations that came due**, inserted
  between the GSC pull (Step 4) and action-item mining (Step 4b) exactly as specified: select
  due rows, check whether the change actually shipped (not just applied), pull the after
  window, control for site-wide + cohort tide, apply the impression floor, write verdicts to
  `checks.csv` + the post's own report + the snapshot's new roll-up section. Entirely
  read-only, no approval gate needed. Regressed/not-shipped rows now feed into Step 4b's
  action-item mining as their own priority category.
- **Backfilled two real rows** rather than leaving the ledger empty at first use: the W4 alt-text
  write (post 7333) and the W5 inbound-link write (post 7533), both from this session, with
  real GSC baselines pulled for the 28-day window ending the day before each write. First
  `checks.csv` verdicts can't exist until `verify_due` (2026-09-11) — genuinely last in
  wall-clock terms, as the plan itself predicted.

**Not done, deliberately:** no `checks.csv` rows yet (nothing has reached `verify_due`), and
Open Question 7's control-band threshold (≥0.5 positions / ≥0.5pp CTR) is carried as a
calibration guess per the plan's own instruction, not resolved with real data — there isn't
any yet.

Two problems with one artifact between them.

**Problem 1 — nothing machine-readable says a post was already optimized.** The per-article
reports in `reports/seo-metatags/` are prose, and their status lines are worded three different
ways across four files (`metatags applied 2026-08-13` · `partially applied 2026-08-13` ·
`✅ **APPLIED 2026-08-14**` · `proposed`). Post 7333 has **two** files for one post (W2). A
skill starting a run cannot cheaply answer *"has 5240 been touched, and what was declined?"* —
so at 313 posts it will re-propose work that was already rejected, or redo work already shipped.

**Problem 2 — nothing is measured afterwards.** Every change so far was applied and signed off
on the assumption it helps. No baseline is frozen, no re-check is scheduled, and no run has ever
gone back to ask whether the metadesc actually earned clicks. Note this is **not lossy** — GSC
retains 16 months, so a baseline can be reconstructed retroactively for anything applied to date.
What is missing is a *fixed* window and a *due date*, without which the comparison is improvised
per run (and therefore trivially flattering) and in practice never happens at all.

#### 13.1 `reports/seo-optimizations/ledger.csv` — one row per applied change set

Append-only, committed, one row **only when something was actually written**. Phase A writes no
row — research is not a change. Declined items do not get rows; they stay in the report per §3.4.

```
id,date_applied,post_id,slug,category,phase,changed,report,keyphrase,
base_start,base_end,base_impr,base_clicks,base_ctr,base_pos,kw_base_impr,kw_base_pos,
backup,verify_due
```

| Column | Notes |
|---|---|
| `id` | `<post-id>-<YYYY-MM-DD>`; suffix `-b` if two phases apply on the same day |
| `phase` | `C` (metatags/tags/alt/links) or `B` (transcript content) — the two write different things and move different metrics |
| `changed` | pipe-separated subset of `title\|metadesc\|focuskw\|tags\|content\|alt\|inbound` — the manifest groups from §3.1, so a partial approval is recorded exactly |
| `report` | filename in `reports/seo-metatags/` — the prose stays there, the ledger never duplicates it |
| `base_*` | the **frozen** 28-day GSC window ending the day before `date_applied` |
| `kw_base_*` | same window, filtered to the focus keyphrase alone |
| `backup` | the `reports/yoast-meta-backup/<id>-<date>.csv` for this row — makes a `regressed` verdict a one-step undo |
| `verify_due` | `date_applied + 28d` (phase C) or `+ 56d` (phase B — new body text has to be crawled, indexed and start ranking, which does not happen in four weeks) |

Placing it in a new sibling directory rather than inside `reports/seo-metatags/` because it
covers content, alt text and links too — the existing directory name is metatags-specific.

#### 13.2 `reports/seo-optimizations/checks.csv` — one row per measurement

```
ledger_id,date_checked,checkpoint,win_start,win_end,
impr,clicks,ctr,pos,kw_impr,kw_pos,
ctrl_impr_pct,ctrl_pos_delta,verdict,note
```

`checkpoint` is `28d` / `56d` / `90d`. Append-only, so a post checked twice keeps both readings
and the trajectory is visible. Joined to the ledger by `ledger_id`; the deltas are derived, not
stored, so a corrected baseline never leaves stale arithmetic behind.

#### 13.3 The verification run — a new step in `seo-performance-report`

**Not a new skill.** It becomes a step in the existing monthly snapshot, because that skill is
already exactly this shape: it pulls GSC, compares against the previous capture, and re-scores
carried-forward action items as ✅ done / ↔ flat / ⬆️⬇️ moved (its
[README](../reports/seo-performance/README.md) step 7). Verifying optimizations is the same
operation over a different queue. Three things come free:

- **the GSC connection and the site-wide window are already open** — the site-wide control in
  step 4 below is a column of `history.csv`, not a second API pull;
- **the cadence already exists** — the monthly run is the only recurring ritual in this project,
  and a check nobody schedules is a check that never happens;
- **read-only** — the step has no write path to WordPress, so it needs no approval gate. The
  monthly report stays a safe, unattended-friendly run.

The steps, inserted after the GSC pull and before the action-item mining (so a `regressed` post
can feed straight into that month's action items):

1. **Select** ledger rows where `verify_due <= today` and no `checks.csv` row exists for that
   checkpoint. If none, the snapshot says *"no optimizations came due this month"* in one line
   and moves on.
2. **Did the change actually ship?** — the step that makes this honest rather than decorative.
   `gsc_inspect_url` for last-crawl time, and a live fetch of the SERP snippet. Google rewrites
   meta descriptions freely, so *applied* ≠ *served*. If the page has not been re-crawled since
   `date_applied`, or the served snippet is not the one that was written, the verdict is
   **`not-shipped`**: record it, push `verify_due` out, and **do not** compute a delta. Measuring
   CTR against a snippet users never saw is the single easiest way to draw a false conclusion here.
3. **Pull the after window** — GSC page-level for `date_applied+1 … +28`, plus the focus
   keyphrase's own row. Query no earlier than `verify_due + 3` days; GSC finalises with a lag.

   > 🔑 **The window is anchored to `date_applied`, never to the run date.** A monthly cadence
   > means a row due on the 3rd waits ~4 weeks for the next report. That lateness is harmless —
   > GSC has fully settled by then, so a late check is *more* accurate — but only if the measured
   > window stays the fixed 28 days after the change. Reading "the last 28 days" instead would
   > give two posts optimized a week apart two different windows, and the ledger immediately loses
   > the ability to compare across posts. `checkpoint` therefore records the **nominal** milestone
   > (`28d`), `win_start`/`win_end` the window actually measured, and `date_checked` when it was
   > read — three different dates, all of which matter.
4. **Control for the tide.** A raw before/after delta credits the optimization for whatever the
   whole site did that month. Two controls, both required:
   - **site-wide** — the same two windows from `gsc_performance_overview` (already trended in
     [`reports/seo-performance/history.csv`](../reports/seo-performance/history.csv));
   - **cohort** — median delta across un-optimized posts in the same category within ~6 months of
     the same publish date. EV News episodes decay naturally, so falling impressions on a
     year-old episode is the baseline behaviour, not a failure. The cohort is what separates the
     two.

   `ctrl_impr_pct` / `ctrl_pos_delta` record what the control did, so every verdict carries the
   counterfactual it was judged against.
5. **Impression floor.** Below ~50 impressions in *either* window, average position is noise and
   CTR is a coin flip. Verdict is `inconclusive` — never dressed up as flat.
6. **Write** three places, each at its own altitude:
   - one `checks.csv` row per due item — the machine-readable record;
   - a short dated *Verification* section appended to the post's existing report in
     `reports/seo-metatags/` (never a new file — that is the W2 mistake). The per-post reasoning
     lives here, not in the snapshot;
   - a roll-up table in the month's snapshot: one line per verified post, plus a standing
     *"optimized to date: N posts · verdicts x/y/z"* count. This is the section that answers
     *"which articles have been optimized, and did it work?"* at a glance.

   Keeping the prose in the article report is what stops the monthly snapshot — currently a
   site-wide document — from turning into a per-post digest as the ledger grows past a handful of
   rows.

#### 13.4 Verdicts

| Verdict | Meaning | What follows |
|---|---|---|
| `improved` | beats the control on the metric the change targeted | record the pattern; it is evidence for the next post of that shape |
| `flat` | inside the control band | not a failure — the page may simply have been fine; do not re-optimize reflexively |
| `regressed` | worse than control, snippet confirmed live | surface it with the `backup` path; rollback is a decision for the human, same as every other write |
| `not-shipped` | not re-crawled, or Google serves its own snippet | re-check later; if it persists across two checkpoints, that is a finding about the *page*, not the change |
| `inconclusive` | below the impression floor | the page needs visibility before metatags can matter — a demand problem, not a copy problem |

**Match the metric to the change.** `changed` says which to read: `metadesc`/`title` → page CTR at
stable position; `focuskw`/`tags` → the keyphrase's own position; `content`/`inbound` →
impressions and query count (new text ranks for phrases the page could not reach before). A
metadesc rewrite that moves impressions moved nothing — impressions are a ranking signal, and the
snippet does not touch ranking.

#### 13.5 What the ledger gives the rest of the pipeline

- **Phase A step 0** — grep `ledger.csv` by `post_id` before any research. Prior rows print as
  *"phase C applied 2026-08-13: title, metadesc, tags — verdict flat at 28d"*, and the report's
  declined-items list (§3.4) is loaded so nothing rejected gets re-proposed.
- **W6's 185-post scan** — the ledger is what makes a resumable bulk run possible at all; without
  it, batch triage has no idea where it stopped.
- **The backlog** — [`SEO_EV_NEWS_TODO.md`](SEO_EV_NEWS_TODO.md) currently tracks done/not-done by
  hand. It can read the ledger instead, and stop drifting from reality.
- **`seo-performance-report`** — the host (§13.3). It gains a measured answer to whether any of
  this works, and **the loop finally closes.** The skill already describes itself as the one that
  *"finds the pages worth optimizing"* while `seo-article-optimize` *"optimizes one of them"* —
  today that arrow only points one way. With the ledger it becomes: report finds → optimizer
  fixes → report verifies → verdicts re-prioritize the next month's backlog. A `regressed` or
  `not-shipped` post is an action item with more evidence behind it than anything the mining step
  currently produces on its own.

#### 13.6 Changes this implies elsewhere

Folds into the existing items rather than adding a new phase:

- **W2** — the report template gains `Ledger:` (the row id) alongside `Status:`, and the
  *Verification* section is part of the template from the start.
- **W10** — the approval gate already knows exactly which groups were approved; that set **is**
  the `changed` column. The ledger row is written at the same moment as the write, from the same
  data, or it will drift.
- **W1** — the ledger lookup belongs at the top of Phase A, the ledger write at the end of Phase C
  and Phase B. Both are cheap to add during the split and expensive to retrofit after.
- **`seo-performance-report/SKILL.md`** — gains the verification step and the roll-up section in
  its snapshot template; its README's numbered "what the skill does" list goes from 7 items to 8.
  This is the only skill outside the A/B/C split that W13 touches, and it is additive — the
  existing site-wide capture is unchanged.

---

## 5. Suggested execution order

| # | Item | Why here |
|---|---|---|
| 1 | **W9 (partial)** fix the `post_excerpt` contradiction | 3 lines; it is an active trap today |
| 2 | **W8** commit what exists | Stop working on untracked load-bearing files |
| 3 | **W7** tag auto-link decision | Every later run makes the problem bigger |
| 4 | **W1 + W2** split + handoff | Everything else attaches to the new structure |
| 5 | **W10** approval gate | **Blocks W4 and W5** — both open new write paths |
| 6 | **W3 + W11** news CSV + tag band | Both land in Phase A; W3's `Тагове` feed W11's rule |
| 7 | **W4** alt write path | Small, isolated, verifiable on one image — needs W10 |
| 8 | **W9 (rest)** docs restructure | Do it once the skill split has settled the vocabulary |
| 9 | **W6** generalise + rescan | Now the pipeline is correct — point it at 6165 |
| 10 | **W5** inbound links | Riskiest write; needs W10; do it once the rest is stable |
| 11 | **W12** gate on transcript availability | Small precondition check; needs W1's Phase A entry point |
| 12 | **W13** ledger + verification step | Ledger schema lands with W1/W2/W10 and gates W6's resumability; the verification step bolts onto the monthly `seo-performance-report`, and **the first verdict cannot exist until 28d after the next write** |

W1 is the structural dependency; **W10 is the safety dependency** — W4 and W5 do not ship
before it. W7 and the W9 contradiction are the two that get *worse* with delay; everything
else is stable if left alone.

**W13 splits cleanly across that order.** Its *ledger* half is a precondition for W6 — a
185-post batch run with no record of where it stopped is not resumable — and every write shipped
before the ledger exists starts un-baselined (recoverable from GSC's 16-month history, but only
by hand). Its *verification* half is a step inside `seo-performance-report` (§13.3), independent
of the A/B/C split, and cannot produce a verdict until 28 days after the first ledger row exists —
so it is genuinely last in wall-clock terms no matter when it is built. **Build the ledger early,
the verification step whenever; the calendar sets the pace, not the backlog.**

---

## 6. Open questions

1. ~~**Does `_shared/constants.md` actually work?**~~ **Resolved 2026-08-14 — yes.** Built it at
   `.claude/skills/_shared/constants.md` and `_shared/report-template.md` (no frontmatter, so
   they don't register as skills of their own — confirmed against the live skill listing after
   creating them). A `SKILL.md`'s own body is what's loaded as a document; a markdown link
   inside it to a path outside it resolves exactly like any other file reference in a
   conversation — the agent executing the skill opens it with `Read` because the instructions
   say to ("Read `_shared/constants.md` before Step 0"), not through some special inclusion
   mechanism. `ev-news-transcript-content` already relied on this before this file existed
   (anchor-linking into `seo-article-optimize`'s table), which was the working proof it holds.
   No staleness convention needed — there's exactly one copy now, not three. W10's approval
   gate can use the same pattern.
2. **Should the gate ever allow a "yes to all, don't ask again" for a batch run?** W6 points
   the pipeline at ~185 unscanned posts, and a per-item gate on each is slow. The safe answer
   is no — but if bulk triage (Open Q4 below) produces obviously-safe classes of change, a
   scoped exception may be worth defining rather than left to improvisation mid-run.
2. **What does Phase A do on a post with no GSC impressions?** ~100 EV News pages have zero
   visibility, so Step 3a returns nothing and the keyphrase comes entirely from autocomplete +
   SERP. Worth an explicit branch rather than letting each run improvise.
3. **Should Phase A be runnable in bulk?** Triaging 185 unscanned posts one at a time is slow.
   A cheap batch mode — GSC scan only, ranking posts by CTR-loss — would produce the backlog
   that W6 needs, without doing full research on posts that turn out not to be worth it.
4. ~~**Column 3 of the news CSV** — image, date, source name, or something else.~~ **Resolved
   2026-08-14 — `author` (the source/reporter attribution, e.g. `thedriven.io` or a Reddit
   handle), confirmed on both a 2025 back-catalogue file (post 7333) and a 2026 current one
   (post 9248).** No image data in this CSV at all — W4's alt text has to come from the media
   object, the CSV can't help there. Also found while resolving this: the 12-column vintage's
   real header names are English (`title,description,link,author,upvote,downvote,clicks,
   added_date,pub_date,off_topic,tags,region`), not the Bulgarian `Тема`/`Тагове`/`Регион`
   this doc assumed elsewhere — no separate topic column exists, `tags` is free-text
   comma-separated. The 2025 file has only 6 columns (`title,description,link,author,upvote,
   downvote`) — confirms the width-detection requirement below is not theoretical.
5. **DataForSEO is still `40104`-blocked** (diagnosed 2026-08-13, account-side, not code). The
   plan assumes Semrush + autocomplete carry Step 3c. If it clears, bulk `search_volume`
   (1,000 keywords per request) makes W6's 185-post scan dramatically cheaper — worth one
   probe before starting W6.
6. **Transcript coverage caps Phase B at 91/128 posts.** The ~35 missing episodes are producer
   request #5. Phase B correctly stops on `NOT_INGESTED` rather than improvising, so this
   limits throughput, not correctness.
7. **What size delta beats the control?** (W13) Without a defined band, `improved` vs `flat` is
   decided fresh each run and will drift optimistic. A starting proposal: the change must clear
   the control by **≥0.5 positions** or **≥0.5pp CTR** to count as moved — but that number should
   come from the observed spread of the cohort, which means the first few checks are calibration
   runs, not verdicts. Say so in the report rather than pretending otherwise.
8. ~~**Who triggers the verification sweep?**~~ **Decided 2026-08-14: the monthly
   `seo-performance-report`**, as a step rather than a separate skill — see §13.3. It already
   holds the GSC connection, the site-wide control window, and the compare-and-re-score pattern.
   The cost is that a row due on the 3rd of a month waits ~4 weeks, which is acceptable because a
   late check is *more* accurate (GSC has settled) — provided the measured window stays anchored
   to `date_applied`. **That anchoring is the load-bearing detail of this decision**; get it wrong
   and the monthly cadence quietly makes posts incomparable.
9. **Is the cohort control actually computable?** (W13) It needs per-page GSC data for ~100
   un-optimized EV News posts, most of which sit near zero impressions. If the median cohort
   member falls below the impression floor, the cohort control is noise and only the site-wide
   control survives — which is weaker, because it cannot see news decay. Worth one probe against
   real GSC data before building the step.
