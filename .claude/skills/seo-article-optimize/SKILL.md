---
name: seo-article-optimize
description: Optimize a single carlifebydani.com article for search — orchestrates keyphrase research, EV News transcript content (when applicable), and the write-side apply step in the correct order, so the user just says "optimize post 5240" without thinking about sequencing. Detects the post's category, runs seo-keyphrase-research (Phase A), then ev-news-transcript-content (Phase B, EV News only, gated on transcript availability), then seo-article-apply (Phase C), handing the same dated report between them. Use when the user gives a URL and asks to "optimize this article", "write SEO metatags", "fix the meta description", "tag this post", or "do the SEO for this page".
---

# Article SEO Optimizer (orchestrator)

**Goal: take one URL and turn it into a shipped, measurable on-page SEO
improvement, in the right order, without the user having to know the order.**
This skill does no research and no writing itself — it detects what the post
needs, runs the phase skills in sequence, and hands the same report path
between them.

## Why three phases, in this order

Read [`docs/SEO_SKILLS_REFACTOR.md`](../../../docs/SEO_SKILLS_REFACTOR.md) §2
for the full reasoning. Short version: half of what used to be one skill needs
no body content to run (research), and half needs real prose to be meaningful
(the meta description, the wordCount check, the tag auto-link check). Neither
"research and apply everything, then fix content" nor "content first, metatags
never" works — so the pipeline is:

```
Phase A  seo-keyphrase-research      → focus keyphrase, secondary phrases, tag candidates
             │ (Keyphrase: line in the report)
             ▼
Phase B  ev-news-transcript-content  → EV News only; grounded body prose, keyphrase front-loaded
             │ (real post_content now exists)
             ▼
Phase C  seo-article-apply           → metatags, tags, alt text, internal links — all write paths
```

Every phase writes to the **same** dated report:
`reports/seo-metatags/<YYYY-MM-DD>-<post-id>-<short-slug>.md` — see
[`_shared/report-template.md`](../_shared/report-template.md). `Status:`
advances `researched → content-written → applied` as each phase completes.
Never let a phase start a second file for a post that already has one.

---

## Procedure

### Step 1 — Resolve the post and its category

```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts",
  method="GET", params={"slug":"<slug>", "_fields":"id,slug,categories"})
```
Map `categories` to one of: `ev-news`, `publications`, `ev-review`,
`ev-masters`. This decides whether Phase B runs at all.

### Step 2 — Check for an existing report

Grep `reports/seo-metatags/` for this post id. If a report already exists,
read its `Status:` and resume from the next phase rather than restarting —
`researched` means run Phase B (if EV News) then Phase C; `content-written`
means run Phase C; `applied` means the pipeline already finished — tell the
user and ask whether they want a fresh pass (e.g. after a content edit) rather
than silently re-running everything.

### Step 3 — EV News precondition (EV News only)

Before starting Phase A on an EV News post, confirm its transcript is
ingested — **don't start a run that can't finish end-to-end.**
```bash
python3 tools/resolve_episode.py "<episode number or title fragment>"
```
- Resolved → proceed.
- `NOT_INGESTED` → **stop before Phase A.** Tell the user this post is blocked
  until the episode is ingested on the producer side, and add a note to
  `docs/SEO_EV_NEWS_TODO.md` if one doesn't already exist. Running Phase A
  (or worse, Phase C) against a post that will need a second pass once the
  transcript lands is wasted work, not progress — there is no partial-credit
  mode here.

Non-EV-News categories skip this check entirely; they have real body content
from day one.

### Step 4 — Run Phase A

Invoke `seo-keyphrase-research` for this post id/URL. It reads/creates the
report, sets `Status: researched` and the `Keyphrase:` line.

### Step 5 — Run Phase B (EV News only)

If the category is `ev-news`, invoke `ev-news-transcript-content` for this
post id, pointing it at the report Phase A just wrote. It appends its section,
sets `Status: content-written`.

Skip this step entirely for `publications`, `ev-review` and `ev-masters` —
those categories have real body content already, so Phase A's research reads
something substantial and there is nothing for Phase B to fix.

### Step 6 — Run Phase C

Invoke `seo-article-apply` for this post id, pointing it at the same report.
It drafts metatags/tags/alt/links, asks for approval, applies what's approved,
and sets `Status: applied`.

### Step 7 — Summarize

Tell the user what shipped, what was declined (and why, if recorded), and the
measurement plan from Phase C's Step 8.

---

## Companion skill

`seo-performance-report` (site-wide monthly snapshot). **That skill finds the
pages worth optimizing; this pipeline optimizes one of them.** The backlog it
feeds is `docs/SEO_EV_NEWS_TODO.md`.

## Known traps

See [`_shared/constants.md`](../_shared/constants.md) for the traps that apply
across all three phases (WP auth, WAF, postmeta revisions, tag-replace,
`post_excerpt`, tag auto-linking, taxonomy SEO). This file only tracks
orchestration-level mistakes:

- **Running Phase C before Phase B on an EV News post** — the meta description
  gets written against a 17-word placeholder, `wordCount` verification is
  vacuous, and the tag auto-link check is undetectable because there's no body
  prose yet to check. This already happened once, on post 7333
  (2026-08-13/14) — see `docs/SEO_SKILLS_REFACTOR.md` §2 for the full account.
- **Starting Phase A on an EV News post whose transcript isn't ingested yet.**
  Don't half-run it — the precondition check in Step 3 exists specifically to
  prevent redoing Phase A's research once the transcript lands.
- **Two reports for one post.** Always check Step 2 before either phase
  creates a new file.
