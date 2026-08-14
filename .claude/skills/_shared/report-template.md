# Report template — one file per post, every phase appends

`reports/seo-metatags/<YYYY-MM-DD>-<post-id>-<short-slug>.md`. The date in the
filename is the date the file was **first created** (by whichever phase runs
first) — it does not change on later phases, so the file stays the single
artifact for that post. **Never start a second file for a post that already has
one** — that was the W2 mistake: post 7333 had
`2026-08-13-7333-cybertruck-bulgaria.md` (metatags) and
`2026-08-14-7333-excerpt-draft.md` (content) as two separate files for one
optimization. If a report already exists for a post, open it, append the new
phase's section, and advance `Status:`.

`Status:` is the machine-readable field a later run or a bulk scan reads before
starting work — grep it rather than re-deriving from prose. It only advances
for phases that actually **wrote** something; research alone does not advance
it past `researched`.

```markdown
# SEO Optimization — <article title>

**URL:** <url> · **Post ID:** <id> · **Category:** <ev-news | publications | ev-review | ev-masters>
**Prepared:** <YYYY-MM-DD> (first phase to touch this post)
**Status:** researched | content-written | applied
**Keyphrase:** <focus keyphrase — set by Phase A, read by Phase B to front-load ¶1>

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
<3–5 lines: entities, event, numbers, date. Owned word count (Yoast `wordCount`)
vs what renders on the page.>

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | | |
| `<meta name=description>` | | |
| Focus keyphrase | | |
| H1 | | |
| Owned word count | | |
| Images without alt | | |
| Internal links out / in | | |

### Demand research
**GSC (90d, this URL):**
| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|

**Google autocomplete (hl=bg, gl=bg):** <seeds → completions; recurring modifiers>
**Keyword metrics (bg):** <phrase / volume / KD / competition — mark each row
`cached <date>` or `fresh <source>`, and say what was newly bought this run>
**SERP check:** <what ranks for the target phrase, and what format Google rewards>
**GA4:** <sessions / engagement / bounce for this landing page, if pulled>
**News CSV (EV News only):** <row order → candidate headline stories; per-story
`Тагове`/`Регион` → tag candidates, validated against real demand, not adopted
directly>

### Recommendation
**Focus keyphrase:** `<phrase>` — <why: position, impressions, intent match, and
what it is *not* competing with on this site>
**Secondary:** `<a>`, `<b>`, `<c>`

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | | | |
| `_yoast_wpseo_metadesc` | | | |
| `_yoast_wpseo_focuskw` | | | |

### Proposed tags
<existing terms only, each in the 3–10 use band unless a saturated brand hub is
genuinely the headline entity>
| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | | | |
| Keyword-intent | | | |
<Gaps: concepts with no existing tag, deliberately not created — note here if
this gap recurs across articles, so it can be batch-created instead of ad hoc.>

---

## Phase B — Transcript content (EV News only — omit this section entirely for other categories)
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** <video_id> — <title> (<published_at>)
**Answer found in:** own episode | archive (<other video_id(s)/titles>) | not found

| Claim | Quote / paraphrase | Source episode | Timestamp |
|---|---|---|---|

### Draft paragraphs
¶1 (N words): ...
¶2 (N words): ...
¶3 (N words): ...

### Facts to confirm before publishing
- [ ] <anything ASR-ambiguous or not directly quoted>

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — <exact proposed text, if different from what Phase B wrote>
- [ ] **Subheadings** — <exact H2/H3s, from question queries>
- [ ] **Image alt** — <media id → exact alt text>

### Internal links
**Inbound — existing posts that should link here:**
| Source post | URL | Anchor text | Where |
|---|---|---|---|

**Outbound — this article should link to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|

### Applied
- [ ] Metatags — title / metadesc / focuskw written
- [ ] Tags written
- [ ] `post_content` written (Phase B) — wordCount: <before> → <after>
- [ ] Image alt written — media id(s): <…>
- [ ] Inbound links written — target post(s): <…>
- [ ] Auto-linked `/tag/` count inside body prose: <N> (editorial links: <N>)

### Declined
_See [`_shared/approval-gate.md`](../_shared/approval-gate.md) §4. One row per
item the user said no to, so a later run treats it as a decision, not a gap._
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|

### Risks / notes
<cannibalisation, slug-change warnings, thin-content caveats, whether better
metatags alone can plausibly move this page>

### Measurement
Baseline (GSC, <window>): <impr / clicks / CTR / pos>. Re-check after 2–4 weeks
(metatags/tags/alt/links) or 4–8 weeks (new body content — needs re-crawl and
re-indexing time).
```
