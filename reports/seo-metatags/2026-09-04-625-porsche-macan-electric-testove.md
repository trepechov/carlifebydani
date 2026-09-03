# SEO Optimization — Porsche поставя Macan на изпитания в името на производителността и ефективността

**URL:** https://www.carlifebydani.com/publications/porsche-postavya-macan-na-izpitaniq-v-imeto-na-proizvoditelnostta/ · **Post ID:** 625 · **Category:** publications
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Porsche Macan Electric
**Ledger:** 625-2026-09-04-C

**Cluster context:** step 2 of 4 in the Porsche/Cayenne cluster prep —
[`reports/seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md`](../seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md).
Runs after post 1039 (already applied 2026-09-04), which is now a valid
inbound-link target (older post). Draft post 9216 excluded — not touched.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Porsche's own pre-launch press material (published 2024-01-12) on the
development/testing of the second-generation, fully-electric Macan — its
first model on the new Premium Platform Electric (PPE): 0.25Cd drag
coefficient, WLTP range >500km, 100kWh gross / 95kWh usable battery, 800V
architecture with up to 270kW DC fast charging (10→80% in <22min at 400V
stations), Porsche Active Aerodynamics, PSM motors (>450kW combined), Porsche
Traction Management, and >3.5 million km of camouflaged-prototype testing
across climate extremes (Sweden to Death Valley). Real owned prose, Yoast
`wordCount: 919` — substantial existing content, no Phase B needed.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `Porsche поставя Macan на изпитания в името на производителността и ефективността - Car Life by Dani` (Yoast fallback) | ~102 chars |
| `<meta name=description>` | *(none rendered — no `_yoast_wpseo_metadesc`, no fallback)* | 0 |
| Focus keyphrase (Yoast) | *(empty)* | — |
| H1 | matches title | — |
| Owned word count | 919 (Yoast schema `wordCount`) | — |
| Images without alt | Featured image (media 1008) `alt_text: ""`; 5 more inline images in the post also have empty `alt` (not individually audited — featured image is the priority per Step 3.4) | — |
| Internal links out / in | 0 out, 0 in currently. Post 1039 (2022-07-06) now predates this post and is optimized — a valid inbound-source candidate (this post can link **out** to it; see Phase C). | — |

### Demand research
**GSC (this URL):** 90-day per-query pull (`page`-filtered `searchAnalytics.query`,
2026-06-06→2026-09-03) returned **zero rows** — no distinguishable query. A
28-day aggregate pull (no dimension) shows 4 impressions, 0 clicks, position
6.75 — thin enough that individual queries are below GSC's per-row reporting
threshold. Effectively a from-scratch keyphrase choice (Decision rules: zero/near-zero
impressions → autocomplete + SERP carry the decision).

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `porsche macan electric` | → self-echo, **porsche macan electric цена**, dimensions, macan electric 4, new, off road, suv, 2026 interior, 2026 price, 2025 |
| `porsche macan tests` | → porsche macan crash tests (different topic — safety testing, not this article's development testing) |
| `porsche macan ppe` | → self-echo, porsche macan ppe platform |
| `porsche macan 800v` | → self-echo, macan elektro 800v, macan ev 800v, macan electric 800v |
| `porsche macan заряждане` | none |

`porsche macan electric` is the real, recognized search entity for this
exact subject (the electric Macan generation, as opposed to the outgoing ICE
model) — matches the article precisely.

**Keyword metrics (bg, fresh DataForSEO 2026-09-04 — unblocked as of today,
see [[project-seo-perf-monitoring]]):**
| Keyword | Volume (bg, monthly avg) | Competition | CPC |
|---|---|---|---|
| porsche macan | 1900 | LOW (8) | €1.91 |
| **porsche macan electric** | **110** | LOW (10) | €1.14 |
| porsche macan цена | 70 | LOW (13) | €1.50 |
| porsche macan ppe / 800v | *(no data — too long-tail)* | — | — |

Bare `porsche macan` (1900/mo) is too broad and ambiguous — it's dominated by
buyers researching the outgoing ICE model (crash tests, price, used-market
queries per autocomplete), which this article does not cover. `porsche macan
electric` (110/mo, LOW competition) is the correctly-scoped phrase for what
this article actually is. All three rows banked to `data/seo-cache/keywords.csv`.
Semrush MCP access still returns a plan-upgrade message (see 1039's report) —
not re-attempted, DataForSEO covered this.

**SERP check:** skipped — no comparison/how-to ambiguity; the article is a
press-release-style development story, not a query type where SERP format
matters.

**GA4:** skipped — near-zero GSC traffic, not worth a pull.

**News CSV:** n/a — `publications` category, no news CSV field.

### Recommendation
**Focus keyphrase:** `Porsche Macan Electric` — matches the article's actual
scope (the electric generation's development/testing, not the outgoing ICE
model), real BG volume (110/mo, LOW competition), a recognized autocomplete
entity, no cannibalization (checked `/wp/v2/search?search=Porsche+Macan+Electric`
— only this post), not the `/ev-news-feed/` hub phrase, and distinct from
draft post 9216's eventual phrase ("Porsche Macan GTS Electric" — a specific
finished trim review, not this development-story angle).

**Secondary:** `Porsche Macan PPE платформа`, `Porsche Macan 800V`, `Porsche
Macan зареждане 270kW` (all three concrete numbers/specs the article itself
provides and are real autocomplete-adjacent phrasing).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to full title, ~102 chars)* | `Porsche Macan Electric – тестове %%sep%% %%sitename%%` → renders **`Porsche Macan Electric – тестове - Car Life by Dani`** | 32 body / 51 total |
| `_yoast_wpseo_metadesc` | *(empty, no fallback rendered)* | `Porsche Macan Electric на тестове: PPE платформа, 800V архитектура, 270kW зареждане и над 3.5 млн. км камуфлажни изпитания. Какво разкрива Porsche.` | 150 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Porsche Macan Electric` | — |

### Proposed tags
Current tags: `800V` (id 136, count 10), `Macan` (id 58, count 4), `Porsche`
(id 57, count 6), `PPE` (id 60, count 1), `SUV` (id 59, count 6), `Тестове`
(id 61, count 1).

| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Porsche | 57 | 6 |
| Entity | Macan | 58 | 4 |
| Entity | 800V | 136 | 10 |
| Entity | SUV | 59 | 6 |

All four sit in or at the edge of the 3–10 reuse band and are genuinely named
throughout the prose — keep, no change. `PPE` (1 use) and `Тестове` (1 use)
are below the band but are pre-existing, real-subject tags (PPE is literally
the platform name the article is about) — flagged as thin, not removed (same
reasoning as `Turbo S` on post 1039: a live tag's archive/inbound links
shouldn't be broken as a side-effect of an unrelated optimization pass).

No new tags proposed. No keyword-intent tag fits cleanly (this is
manufacturer press material, not a launch/rumor/spec-update news story in the
EV News sense).

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **Metatags:**

| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to full title, ~102 total)* | `Porsche Macan Electric – тестове %%sep%% %%sitename%%` → renders **`Porsche Macan Electric – тестове - Car Life by Dani`** | 32 body / 51 total |
| `_yoast_wpseo_metadesc` | *(empty, no fallback rendered)* | `Porsche Macan Electric на тестове: PPE платформа, 800V архитектура, 270kW зареждане и над 3.5 млн. км камуфлажни изпитания. Какво разкрива Porsche.` | 150 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Porsche Macan Electric` | — |

- [ ] **Tags:** no change — current `[136, 58, 57, 60, 59, 61]` carried forward as-is.
- [ ] **H1** — no change; carries "Porsche" and "Macan" naturally already.
- [ ] **First 100 words** — keyphrase concept (Macan going electric) already
  appears in the opening sentence; no redraft needed.
- [ ] **Subheadings** — none proposed; the 3 existing H3s already segment the
  article well (aero, charging, dynamics testing).
- [ ] **Image alt + title** (media 1008, featured image only — 5 inline
  images also have empty alt but are not individually proposed this pass,
  same as the 8913 precedent's "featured + priority image" scope; noting the
  remaining 5 as a gap rather than silently skipping):

| Field | Before | After |
|---|---|---|
| `alt_text` | `""` | `Porsche Macan Electric — камуфлажен прототип на тестове` |
| `title` | `porsche-macan-izpitania-proizvoditelnost-efektivnost` | `Porsche Macan Electric – тестове` |

### Internal links
**Inbound — existing posts that should link here:** none proposed as
edits-to-other-posts this pass — see Outbound below, which covers the
cluster's actual link (625 → 1039, not the reverse, since 1039 is older).

**Outbound — this article should link to 1039 (Porsche Taycan Turbo S):**
1039 (2022-07-06) now predates this post (2024-01-12) and is optimized —
per the cluster plan, 625 should link back to it. A natural, factual
insertion point exists in ¶1: the article already frames the electric Macan
as "the first Porsche model based on PPE," and it's true (and worth stating)
that Taycan — not Macan — was the brand's actual first electric model, just
on a different (J1) platform. Proposed edit to **this post's own ¶1** (one
`wp:paragraph` block, one inserted clause + link, mechanics per
`seo-article-apply` Step 4):

> Before: "Десет години след своя старт, Macan е на прага на второто
> поколение, което сега ще бъде в напълно електрическа форма. Като първият
> модел на Porsche, базиран на новата Premium Platform Electric (PPE), този
> SUV е напълно нова разработка."
>
> After: "Десет години след своя старт, Macan е на прага на второто
> поколение, което сега ще бъде в напълно електрическа форма — не първият
> електрически Porsche, тази чест вече принадлежи на **[Taycan](https://www.carlifebydani.com/ev-review/porsche-taycan-turbo-s-nai-burzata-kola/)**.
> Като първият модел на Porsche, базиран на новата Premium Platform Electric
> (PPE), този SUV е напълно нова разработка."

Anchor: "Taycan" → `/ev-review/porsche-taycan-turbo-s-nai-burzata-kola/`.
This is a direct edit to the **current post's** `post_content` (has WP
revisions, unlike postmeta) — treated as its own approval item, same
diff-before-write discipline as an inbound edit to a foreign post.

### Applied (2026-09-04)
- [x] Metatags — title / metadesc / focuskw written (verified in the POST
  response's `meta`, and confirmed live:
  `<title>Porsche Macan Electric – тестове - Car Life by Dani</title>`,
  `<meta name="description">` matching the approved text).
- [x] Tags — no change (approved as-is, carried forward
  `[136, 58, 57, 60, 59, 61]`).
- [x] Image alt + title — media 1008 written. Backup of pre-write values
  (both empty/slug-only) saved to `reports/yoast-meta-backup/media-1008-2026-09-04.csv`.
- [x] Outbound link — ¶1 edited to add "Taycan" → 1039
  (`/ev-review/porsche-taycan-turbo-s-nai-burzata-kola/`). Written via a
  programmatically-built full `content.raw` payload (not retyped through
  context — same discipline as the EV164 incident's fix) and verified
  byte-identical to the pre-write content except the one inserted clause+
  link (`difflib` confirmed exactly 1 diff hunk). Live page confirmed to
  contain the link.
- Yoast postmeta backup (pre-write, all empty):
  `reports/yoast-meta-backup/625-2026-09-04.csv`.

### Declined
_None — all three proposed groups (metatags+focuskw, image alt+title,
outbound link) approved in full._

### Risks / notes
- Near-zero GSC baseline (28d: 4 impressions, 0 clicks, position 6.75;
  90d per-query: no rows at all) — watch for the page starting to earn any
  "Porsche Macan Electric"-shaped impressions, not a large delta.
- `PPE` and `Тестове` tags sit at 1 use each — thin, pre-existing, not
  actioned this pass (same reasoning as post 1039's `Turbo S`).
- 5 inline images still have empty alt text — noted as a gap, not fixed this
  pass (scope matched to precedent, not exhaustive).
- Semrush MCP still returning a plan-upgrade message this run too — confirms
  it's not a one-off (see post 1039's report); worth the user checking their
  Semrush plan before the next Phase A that might need it.
- This post now carries the cluster's first outbound link (625 → 1039).
  1039 still has no inbound links pointing to it other than this one —
  7828 and 8035 will add their own when optimized next.

### Measurement
Baseline (GSC, 28d ending 2026-09-03): 4 impressions, 0 clicks, 0% CTR,
position 6.75. Re-check in **2–4 weeks** (metatags/alt/one outbound link,
no new body content or tag change). Ledger row: `625-2026-09-04-C`,
`verify_due`: `2026-10-02`.

### Step 8 verification (2026-09-04, post-write)
- Re-fetched rendered page: `<title>`, `<meta name="description">` both
  confirmed live and matching what was written.
- `/tag/` links found on the page for this post's own tags: `porsche` ×8,
  `macan` ×8, `800v` ×4, `suv` ×6 — includes template tag-chip widgets
  alongside any body-prose auto-links, not isolated further this pass (same
  caveat as post 1039).
- Outbound link to 1039 confirmed present in the live rendered HTML.

---
