# SEO Optimization — Renault Megane E-Tech – една огромна крачка в посока иновация, никаква промяна в начина на мислене

**URL:** https://www.carlifebydani.com/ev-review/renault-megane-e-tech-ogromna-krachka-posoka-inovatsiya/ · **Post ID:** 1027 · **Category:** ev-review
**Prepared:** 2026-09-05 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Renault Megane E-Tech
**Ledger:** `1027-2026-09-05-C`

**How this post surfaced:** oldest post (2023-03-08) in
[`reports/seo-optimizations/2026-09-04-renault-cluster-plan.md`](../seo-optimizations/2026-09-04-renault-cluster-plan.md),
run first per the plan's oldest→newest order. Not a GSC-query match in that
plan's original pull — treated as a from-scratch keyphrase choice per its own
note.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A real, opinionated hands-on review (Dani's own critique, `ev-review`
category) of the **Renault Megane E-Tech Electric**. Owned prose (375 words,
Yoast `wordCount`) covers: the rear-view-mirror-as-monitor (poor refresh
rate/contrast/brightness, camera too close), an odd trunk shape, a
button/lever-cluttered wheel and driving position, underwhelming traction
control, the shared **Renault/Nissan CMF-EV platform** (FWD-only for Megane
vs. Nissan's AWD option), 130 kW DC charging ("edda 130kW при подходящи
условия" — modest despite marketing), and the `openR link` Google-services
infotainment (much improved after updates, still has UI quirks). Genuinely
critical, first-hand content — not a spec sheet rehash.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(empty → falls back to post title)* `Renault Megane E-Tech - една огромна крачка в посока иновация, никаква промяна в начина на мислене - Car Life by Dani` | ~145 chars, way over budget |
| `<meta name=description>` | absent | — |
| Focus keyphrase | none set | — |
| H1 | matches post title verbatim (same over-length text) | — |
| Owned word count | **375** (Yoast schema) | — |
| Images without alt | 1 (featured image, media id 1093, `alt_text=""`, `title` is a raw slug) | |
| Internal links out / in | **0 internal** (one external link to the channel's own YouTube page — leave alone) / 0 inbound confirmed | |

No H2/H3 subheadings anywhere in the body — five plain paragraphs plus a
`wp:embed` for the YouTube review. `excerpt` confirmed empty (no
`post_excerpt` conflict here, unlike some other posts this pipeline has
touched).

### Demand research
**GSC (90d, 2026-06-06→2026-09-03, page-level, this URL):** **0 impressions**
— confirms the cluster plan's own note that this post didn't surface in the
original query pull. Nothing to preserve; choosing from scratch.

**Cannibalization check:** `/wp/v2/search?search=Renault+Megane` returns only
this post (1027) as a genuine match — the phrase is unclaimed. (2622 also
matches the search on "Renault" alone but is Scenic, a different model.)

**Google autocomplete (hl=bg, gl=bg):**
- `renault megane e-tech` → rich cluster: `ev60`, `ev60 220 equilibre`,
  `2026`, `цена`, `electric`, `100 electric`, `reichweite`, `ev40` — real,
  current demand, trim-level specific.
- `рено меган е-тех` / `рено меган електрик` → thinner but real: `цена`,
  `технически характеристики` modifiers recur.

**Keyword metrics (bg, DataForSEO, fresh 2026-09-05):**
| Phrase | Volume/mo | Note |
|---|---|---|
| renault megane e-tech | **170** | largest, unclaimed — the pick |
| renault megane electric | 70 | secondary, English-phrasing variant |
| renault megane e-tech цена | 30 | secondary — price intent |
| рено меган електрик | 10 | thin |
| рено меган е-тех | 10 | thin |

All banked to `data/seo-cache/keywords.csv`.

**SERP check** (`renault megane e-tech`, bg): dominated by classifieds
(auto.bg, autoscout24 listings), Wikipedia, a forum thread, dealer pages, and
a Google AI Overview citing Renault's own site + Wikipedia + a YouTube test.
**No dedicated editorial review article currently ranks** — this site's own
channel already appears in the video pack (rank 2, this exact video). Related
searches surfaced: `Renault megane e tech мнения`, `... характеристики`,
`... ev60` — "мнения" (opinions/review) matches this article's actual
first-hand-critique nature exactly, and there's a genuine content gap (an
editorial review page) for this phrase in the BG SERP.

### Recommendation
**Focus keyphrase:** `Renault Megane E-Tech` — by far the largest volume
(170/mo), genuinely unclaimed on this site, and the SERP shows a real gap
for an editorial review (vs. the classifieds/spec-sheet pages currently
ranking) that this article's actual critical, hands-on content can fill.
**Secondary:** `Renault Megane E-Tech мнения` (matches the "review/opinions"
related-search and the article's real critical framing), `openR link`,
`CMF-EV`.

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to post title, ~145 chars, over budget)* | `Renault Megane E-Tech — честни впечатления %%sep%% %%sitename%%` | 42 + 19 suffix = 61 |
| `_yoast_wpseo_metadesc` | *(absent)* | `Renault Megane E-Tech — честни впечатления: огледало-монитор без изпипване, 130kW зареждане и openR link с Google. Вижте пълното ревю на Дани.` | 142 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Renault Megane E-Tech` | — |

Every claim traces to the article's own real prose (the mirror-monitor
complaint, the 130 kW DC ceiling, the openR link/Google infotainment) —
nothing invented.

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | E-Tech | 101 | 5 | ✅ keep — in-band, named repeatedly |
| Entity | Megane | 100 | 3 | ✅ keep — in-band, the headline model |
| Entity | Renault | 80 | 9 | ✅ keep — in-band, named |
| Entity | **Nissan** | **19** | **3** | ➕ **add — in-band, genuinely named** ("Renault/Nissan са създали изцяло нова платформа RNM CMF-EV") |
| Entity | **CMF-EV** | **224** | **1** | ➕ **add despite below-band — article's own platform-name exception, same logic as `AmpR Small`/`CMF-B` elsewhere this pipeline; explicitly named in prose** |

**Gaps:** no existing tag for `openR link` (the infotainment system, named
substantively) — only one post (this one) would use it; not creating
speculatively, noting the gap. `Ariya` (id 20, count 1) stays unused — the
model name itself isn't in this post's own prose, only the shared platform
is; tagging it here would be a stretch this pipeline's own rule warns
against.

---

## Phase C — Metatags, tags, alt text, links
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
approved._

### Internal links — research
**Inbound:** none proposed. This is the oldest Renault-cluster post
(2023-03-08); checked `/wp/v2/posts?before=2023-03-08...` for any
CMF-EV/Renault/Nissan-adjacent older post — nothing found (the earliest
pre-dating posts are Mazda MX-30, BMW iX M60/iX3, Smart EQ, Opel Mokka-e,
none topically connected). No older post exists to link forward into this
one, so zero inbound is the correct answer here, not a gap.

**Outbound:** one strong candidate — **Nissan Ariya (post 196, 2023-09-17)**,
the genuine CMF-EV platform sibling this article's own prose already
references indirectly ("Renault/Nissan... платформа RNM CMF-EV"). Proposal:
add the parenthetical model name so the reference is concrete, then link it —
*"Renault/Nissan (напр. Nissan Ariya) са създали изцяло нова платформа RNM
CMF-EV"* with `Nissan Ariya` wrapped in an `<a>` to post 196. Factual, not
invented — Ariya genuinely shares this platform.

**Considered and declined:** linking outward to the other Renault-cluster
posts (2455, 2622, 6889 — Renault 5/Scenic, all newer than 1027). Not
proposing this: those models share a *different* platform (AmpR Small/
CMF-B, not CMF-EV) and nothing in 1027's own prose discusses them — forcing
a link would mean inventing a bridging sentence not grounded in the
article's real content, which this pipeline's own rule warns against ("the
anchor must read as something the author would have written"). Once 6889
(the cluster's designated newer anchor) exists in final form, it — not this
post — is the natural place for a "more Renault EV reviews" cross-link if
one gets added later.

**196's optimization status:** checked `reports/seo-optimizations/ledger.csv`
— no row for post 196. Will need adding to
`docs/SEO_BACKLINK_TARGETS_TODO.md` once this outbound link is written.

### Proposed image alt + title (media 1093, featured image)
| Field | Before | After |
|---|---|---|
| `alt_text` | *(empty)* | `Renault Megane E-Tech — електрическа хечбек кола на Renault` |
| `title` | `renault-megane-e-tech-ogromna-krachka-posoka-inovatsiya` (raw slug) | `Renault Megane E-Tech` |

### Applied
- [x] Metatags — title / metadesc / focuskw written, confirmed live
  (`<title>`, `<meta name="description">` and `og:description` all match
  exactly).
- [x] Tags — replaced with `[19, 80, 100, 101, 224]` (added Nissan + CMF-EV),
  confirmed live.
- [x] Image alt + title — media 1093 written and confirmed live.
- [x] Outbound link — added to post 1027's own CMF-EV paragraph, wrapping
  the new "Nissan Ariya" parenthetical with a link to post 196. Built and
  verified the spliced content programmatically (single-occurrence fragment
  match, all block counts unchanged, link count 1→2, char delta exactly the
  122-char inserted text) before sending via `curl` with the `seo-bot`
  Keychain password. Confirmed byte-identical live response.
- [x] Auto-linked `/tag/` count inside body prose: **6** (`Renault` ×2,
  `Nissan`, `CMF-EV`, `Megane`, `E-Tech` ×1 each) — `Renault` linking twice
  (two separate literal occurrences of the word, both auto-linked) confirms
  yet again the theme's 1×-per-tag cap fix is not deployed live (same
  finding as posts 6889, 4129, 5240, 2398).

### Declined
_None — all three approval-gate groups (metatags+tags, image alt/title,
outbound link) were approved in full._

### Risks / notes
- No H2/H3 structure in the body — a real content gap (the "мнения"/review
  framing this keyphrase wants would benefit from subheadings like
  "Недостатъци", "openR link система", "CMF-EV платформа"), but restructuring
  the actual prose is a larger edit than this pipeline's metatag/tag/alt/link
  scope — flagging for a possible future content pass, not attempting it
  here.
- Zero GSC impressions means this is a from-scratch bet on autocomplete +
  SERP-gap evidence, not a "presentation problem" fix on an already-ranking
  page — metatags alone earning traffic here will take longer to show up
  than on a post with existing impressions.

### Measurement
Baseline (GSC, 2026-06-06→2026-09-03): 0 impr / 0 clicks / — CTR / — pos.
Re-check in 4–8 weeks for the metatag/tag/link changes (longer than the
usual 2–4, since this is a from-scratch target with no existing ranking to
build on).
