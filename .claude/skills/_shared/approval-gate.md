# Shared approval gate — no unattended writes

Read this file before any write step in `seo-article-apply` (or any future
skill that writes to live WordPress content on this site). It is not a skill
on its own — no frontmatter, never invoked directly — see
[`_shared/constants.md`](constants.md) for why a plain cross-file link plus an
explicit "read this before Step N" instruction is enough for this to resolve.

**Governing constraint, every write path, no exceptions: a skill never
changes live WordPress content on its own authority.** It researches, drafts,
and *proposes*; the human decides what ships. Everything below exists to make
that gate precise instead of a fixed set of yes/no bundles — see
[`docs/SEO_SKILLS_REFACTOR.md`](../../../docs/SEO_SKILLS_REFACTOR.md) §3 for
the reasoning this formalises.

---

## 1. The change manifest

Before *any* write, present a numbered manifest showing the **exact final
value** — not a summary, not "updated meta description". Grouped by write
target, because that is also the rollback boundary:

| # | Write target | Endpoint | Revisions? | Backup required |
|---|---|---|---|---|
| 1 | Yoast postmeta (title / metadesc / focuskw) | `POST /wp/v2/posts/<id>` `meta` | ❌ none | CSV to `reports/yoast-meta-backup/` |
| 2 | Tags (`post_tag`) | `POST /wp/v2/posts/<id>` `tags` | ❌ none | record current `tags` array — the field **replaces** |
| 3 | This post's `post_content` | `POST /wp/v2/posts/<id>` `content` | ✅ yes | full existing block must be resent |
| 4 | Media `alt_text` **and** `title` | `POST /wp/v2/media/<id>` | ❌ none | record both current values |
| 5 | **Other** posts' `post_content` (inbound links) | `POST /wp/v2/posts/<other-id>` | ✅ yes | one approval **per post** |

Each row carries before → after verbatim, plus character counts on the two
length-budgeted fields (title, metadesc). For `post_content`, show the
paragraphs as they will render, not the raw Gutenberg block markup.

## 2. Selecting a subset

The user picks **any combination** — approving the meta description while
rejecting the tags must be possible, and must not silently drop the rest of
the run.

Mechanically: `AskUserQuestion` with `multiSelect: true` caps at **4 options
per question**, which is why the manifest groups by write target rather than
listing every individual field. **Groups 1–3 fit one question.** Groups **4
and 5 get their own gates** — media alt/title because they have no revision
safety net, inbound links because each one edits a different post and
deserves its own yes. **Never bundle a foreign-post edit into the current
post's approval**, and never bundle media alt/title into the metatag/tags
question just because there's room.

If a manifest genuinely exceeds what one question can hold (e.g. several
inbound-link candidates), ask sequentially by group — group 5 becomes one
question per candidate post, not one crowded multiSelect. Do not collapse
groups to fit the widget.

## 3. Iterating on the draft

"Revise" is a first-class outcome, not a polite decline. On revise: take the
user's note, regenerate **only** the contested items, re-present the **full**
manifest (so nothing approved-then-changed slips through unseen), and ask
again. Unbounded — no "last chance" framing, no drift toward writing
something the user hasn't seen in final form.

The report in `reports/seo-metatags/` is **rewritten each iteration**, so the
artifact always matches what was actually approved. A value from a
superseded draft must never survive into the write call.

## 4. After a partial approval

`Status:` in the report (see
[`report-template.md`](report-template.md)) advances only for the items
actually written. Declined items are recorded in the report's **Declined**
subsection with the reason, so a later run knows they were a decision rather
than an oversight — **and does not helpfully re-propose them.**

```markdown
### Declined
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|
```

A later phase or a resumed run reads this subsection before drafting anything
new for that post — a declined item is a closed decision, not a TODO.
