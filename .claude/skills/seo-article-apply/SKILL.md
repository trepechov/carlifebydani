---
name: seo-article-apply
description: Draft and apply the write side of a single carlifebydani.com article's SEO — meta title/description/focuskw, tags, image alt text and internal links — against a report that already has Phase A's keyphrase research (and Phase B's transcript content, on EV News posts). Presents a before/after manifest, asks before writing anything, backs up Yoast postmeta first, and applies only what's approved via the WordPress MCP. This is Phase C of the seo-article-optimize pipeline; normally invoked by that orchestrator after Phase A (and Phase B on EV News), not directly. Use directly only when a report already exists with Status researched or content-written and you're resuming just the write step.
---

# SEO Article Apply (Phase C)

**Goal: turn Phase A's research (and Phase B's content, where it ran) into
shipped, measurable on-page changes — metatags, tags, alt text, internal
links — with the user's explicit approval on every write.** This is the last
of three phases in the `seo-article-optimize` pipeline (see that skill for
orchestration and [`docs/SEO_SKILLS_REFACTOR.md`](../../../docs/SEO_SKILLS_REFACTOR.md)
§2 for why this phase runs last: the meta description should describe the
real prose, `wordCount` verification is meaningless against a placeholder
page, and the tag auto-link check can only run once body text exists).

**Read [`_shared/constants.md`](../_shared/constants.md) before Step 1, and
[`_shared/approval-gate.md`](../_shared/approval-gate.md) before Step 6.** The
first has the site constants table and the traps that apply to every phase;
the second is the manifest format, question-grouping and revise-loop this
skill's Steps 6–7 apply — read it rather than re-deriving the gate mechanics
here.

**Precondition:** a report already exists at
`reports/seo-metatags/<date>-<id>-<slug>.md` with `Status: researched` (or
`content-written` on EV News posts) and a `Keyphrase:` line. If none exists,
stop and say so — this phase does not invent research, it applies it.

---

## Procedure

### Step 1 — Read the existing report and the current live state

Open the report, read `Keyphrase:`, the Phase A recommendation, proposed
metatags/tags, and — if present — Phase B's draft paragraphs. Re-fetch the
post to get the **current** live values (Step 1 of Phase A may be stale if
time has passed, or if Phase B just wrote new content):
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="GET", params={"_fields":"content,excerpt,title,tags,meta"})
```

### Step 2 — Draft the metatags

**`_yoast_wpseo_title`** — write `"<title body> %%sep%% %%sitename%%"`. Template
variables render correctly; use them rather than hardcoding the brand.
- Body budget: **35–45 chars** (the suffix eats 19; hard cap ~60 total).
- Focus keyphrase in the **first 30 characters**.
- On EV News posts, strip the `#EV160`-style episode prefix from the title —
  it wastes the highest-value characters of the SERP title. (Post 9248 is the
  reference implementation.) Other categories don't carry this prefix; nothing
  to strip.

**`_yoast_wpseo_metadesc`** — **140–155 characters.**
- Front-load the focus keyphrase; add the 2–3 concrete specifics that make the
  page worth clicking (a number, a model, a price, a date), and end on a light CTA.
- Write it against the **real prose** — Phase B's paragraphs on EV News posts,
  the article's actual body on every other category. Never draft this against
  a placeholder page.
- Reference: post 9248 — `Илон Мъск загатна за сливане между Tesla и SpaceX,
  Tesla пуска FSD V14 Lite за Hardware 3, а VW представи новия ID Cross. EV
  Новини #161 - виж всичко.` (149 chars.)
- Write it to be **true**. A description that oversells depresses dwell time.
- Yoast derives `og:description` from this field, so one write fixes both.

**`_yoast_wpseo_focuskw`** — the exact focus keyphrase from the report,
nothing else.

Always present **before → after** for all three, with character counts.

### Step 3 — On-page proposals that need a human

Metatags change *presentation*. These change whether the page deserves to rank.
Propose concretely — exact text, exact location — not "add more keywords".

1. **H1 / title tag alignment** — H1 should carry the keyphrase in natural
   Bulgarian; it does not have to equal the SEO title.
2. **First 100 words** — verify the keyphrase appears in the opening sentence
   of owned text. On EV-News pages this is Phase B's ¶1 — check it, don't
   redraft it here. On other categories, propose the edit if it's missing.
3. **Body coverage** — 2–4 secondary phrases and the `phrase_questions` results
   turned into H2/H3 subheadings that the article then answers. Quote the exact
   sentences to add; never say "sprinkle keywords".
4. **Image alt text** — every `<img>` needs descriptive Bulgarian alt naming the
   subject; the featured image's alt should include the keyphrase. Alt lives on
   the media object, not the post:
   ```
   mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/media/<id>",
     method="GET", params={"_fields":"id,alt_text,title,source_url"})
   ```
   Editor role can `POST` `{"alt_text": "..."}` back — **verify on one image
   before proposing a batch.** Media `alt_text` is **not** covered by post
   revisions — back up the current value before writing, same discipline as
   Yoast postmeta.
5. **Structured data / slug** — only if genuinely wrong. **Never propose a slug
   change on a URL with GSC impressions** unless a 301 ships with it.

**Never propose `post_excerpt`** for any of the above — see
`_shared/constants.md` traps. Everything generated by this pipeline goes into
`post_content`, and only Phase B writes `post_content`.

### Step 4 — Internal linking (both directions)

WordPress's own search is the discovery tool — it ranks by relevance across all
posts:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/search",
  method="GET", params={"search":"<keyphrase or entity>","per_page":10,
                        "_fields":"id,title,url,subtype"})
```
Run it for the focus keyphrase and for each main entity, then propose:
- **Inbound links (higher value)** — 2–4 *existing, already-ranking* posts that
  should link **to** this article. Give the source post, the anchor text (a
  natural Bulgarian phrase containing the keyphrase — never "тук" / "виж тук"),
  and roughly where in that post it belongs. Inbound internal links from pages
  with existing authority are the strongest on-site lever available here.
  Writing one **edits a different post's `post_content`** — per the approval
  gate (Step 6), this is never bundled into the current post's metatag
  approval.
- **Outbound links** — 2–3 related posts this article should link **out** to,
  same detail level. Prefer the deep evergreen `/publications/` and `/ev-review/`
  pages over other news episodes.
- Skip `/tag/` pages as link targets — thin taxonomy pages already outrank real
  editorial content on this site, and linking to them makes it worse.

External links on this site are already correctly `rel="nofollow" target="_blank"`
— leave them alone.

### Step 5 — Write the Phase C section

Append to the existing report (never start a new file — see
[`_shared/report-template.md`](../_shared/report-template.md) § Phase C) with
the drafted metatags, tags carried forward from Phase A, on-page proposals,
and internal links. Write this **before** touching anything live.

### Step 6 — Back up, then run the approval gate

**a) Back up first — always.** WordPress revisions do **not** cover postmeta or
media `alt_text`, so an overwritten value is unrecoverable without this.
```
reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv
```
Header: `id,slug,link,_yoast_wpseo_title,_yoast_wpseo_metadesc,_yoast_wpseo_focuskw`
with the **current** (pre-write) values, even when they're all empty. For
image alt, record the current `alt_text` in the same backup directory before
writing.

**b) Run [`_shared/approval-gate.md`](../_shared/approval-gate.md)** against
the five groups this skill can write (rows 1, 2, 4, 5 of its manifest table —
this skill never writes row 3, `post_content`, that's Phase B): present the
manifest, group 1+2 (metatags + tags) in one `multiSelect` question, groups 4
(alt text) and 5 (inbound links, one question per candidate post) each on
their own. Honor a revise request as described there — regenerate only the
contested items and re-present the full manifest before asking again. **Never
write without an explicit yes** — this is production content.

### Step 7 — Apply (only what was approved)

Metatags and tags can go in one call:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="POST", params={
    "tags": [<existing tag ids from Phase A>],
    "meta":{
      "_yoast_wpseo_title":"...", "_yoast_wpseo_metadesc":"...",
      "_yoast_wpseo_focuskw":"..."}})
```
`tags` on this endpoint **replaces** the post's tag set, not merges — read the
post's current `tags` array in Step 1 and include it in this list unless a
current tag is being deliberately dropped.

Image alt text — one image at a time, same approve-first rule:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/media/<id>",
  method="POST", params={"alt_text": "..."})
```

Inbound links — read the target post's full `content`, insert the link at the
proposed location, send the **full** content back (the endpoint replaces, not
appends — never send a partial block). One post, one approval, one write —
never batched even when several inbound links were approved together.

**Record `Status: applied` in the report, and log any declined items in its
Declined subsection with the reason** (see `_shared/approval-gate.md` §4) —
so a later run doesn't re-propose something the user already said no to.

### Step 8 — Verify and hand off

1. Re-fetch the rendered page with `curl` and confirm `<title>`,
   `<meta name="description">` and `og:description` changed. Yoast caches — if
   the old values persist, say so rather than assuming success.
2. **Count `/tag/` links inside the body prose specifically** — the theme
   auto-links post tags into `the_content`
   ([`theme/functions.php:75`](../../../theme/functions.php#L75); lowered to
   1× per tag 2026-08-14, see `docs/SEO_SKILLS_REFACTOR.md` §W7). Note the
   count in the report.
3. Tick the row in `docs/SEO_EV_NEWS_TODO.md` if the post is listed there.
4. Tell the user the measurement plan: **re-check this URL's CTR and position
   in GSC in 2–4 weeks** for metatags/tags/alt/links, **4–8 weeks** for new
   body content (needs re-crawl and re-indexing time). The
   `seo-performance-report` skill picks it up on the next monthly run.

---

## Decision rules

- **Never write without an explicit yes.** No exceptions, no "last chance"
  framing, no writing something in a form the user hasn't seen.
- **A partial approval is a normal outcome, not a failure to redo.** Write
  exactly the approved subset; record declined items with reason.
- **Never bundle a foreign-post edit into this post's approval.** Every
  inbound-link write gets its own explicit yes.
- **Never invent facts** to fill a meta description. Everything in the snippet
  must be in the article.
- **Don't propose slug changes** on URLs with impressions unless a 301 ships too.

## Known traps

- **Postmeta and media `alt_text` have no revisions.** Back up to CSV before
  every write. No exceptions.
- **`post_content` on `POST /wp/v2/posts/<id>` replaces, it does not append.**
  For inbound links, always send the target post's full existing content plus
  the inserted link — never only the new fragment.
- **The theme auto-links tag names inside `the_content`.** Once body prose
  exists (Phase B, or any pre-existing content on non-EV-News posts), re-fetch
  the rendered page and count `/tag/` links inside it before calling a tagging
  pass finished.
- **Category / tag archive SEO needs wp-admin**, not this endpoint — see
  `_shared/constants.md` traps.
