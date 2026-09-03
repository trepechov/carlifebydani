# SEO Optimization — #EV128 – Новия електрически Cayenne на Porsche

**URL:** https://www.carlifebydani.com/ev-news/ev128-noviya-elektricheski-cayenne-na-porsche/ · **Post ID:** 7828 · **Category:** ev-news
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** Porsche Cayenne Electric
**Ledger:** 7828-2026-09-04; 7828-2026-09-04-C

**Cluster context:** step 3 of 4 in the Porsche/Cayenne cluster prep —
[`reports/seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md`](../seo-optimizations/2026-09-03-porsche-cayenne-cluster-plan.md).
Runs after 1039 and 625 (both applied 2026-09-04). Draft post 9216 excluded —
not touched.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
Episode #EV128 (published 2025-11-25, video `rYm2ZO8AL1c`, 102 transcript
chunks ingested). `post_content` is currently just the YouTube embed — Yoast
`wordCount: 17` (title only, no article body) — the standard EV News thin-content
pattern. The post's own title names the headline story directly: the new
electric Porsche Cayenne. The episode's news list (56 rendered cards, 28
unique after de-duplication — `meta.news_csv` not yet REST-exposed on
production, same known gap as EV164, fell back to parsing rendered cards)
contains **two distinct Cayenne Electric stories**, both relevant:
- Card #3: the Cayenne Electric can charge wirelessly (induction, no plug).
- Card #10: "Most powerful Porsche ever is electric" — the new Cayenne EV,
  1139 hp.

Card #1 (Tesla LFP battery advice) and #2 (Renault Trafic Van 800V) rank
above the Cayenne stories in the rendered list, but the post's own title
already establishes Porsche Cayenne as this episode's chosen headline (per
the cluster plan) — treated as such here, consistent with how titling
overrides raw card position when the two diverge.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | `#EV128 - Новия електрически Cayenne на Porsche - Car Life by Dani` (Yoast fallback) | ~68 chars |
| `<meta name=description)` | *(none — no fallback, no content to build one from)* | 0 |
| Focus keyphrase (Yoast) | *(empty)* | — |
| H1 | matches title | — |
| Owned word count | 17 (Yoast schema `wordCount`) | — |
| Images without alt | Featured image (media 7829) not yet checked — will confirm in Phase C | — |
| Internal links out / in | 0 out (video embed only). In: none yet. |  |

### Demand research
**GSC (this URL):** 90-day per-query pull returned zero rows (too thin for
per-query breakdown). 28-day aggregate: **12 impressions, 0 clicks, position
5**. Per Decision rules, position ≤5 with weak/zero CTR is a **presentation**
problem — the page is already being shown, the snippet isn't earning clicks.
This is exactly the case metatags can fix.

**Google autocomplete (hl=bg, gl=bg):**
| Seed | Completions |
|---|---|
| `porsche cayenne electric` | → self-echo, **цена**, 2026, turbo, coupe, 2026 цена, turbo 2026, 2026 interior, real range, 2026 характеристики |
| `porsche cayenne ev` | → self-echo, 2026, range test, price, for sale, acceleration, release date, coupe |
| `porsche cayenne electric зареждане` | none |
| `porsche cayenne безжично зареждане` | none (the wireless-charging angle has no autocomplete footprint yet — too new/niche) |

Strong, real autocomplete demand for the bare model-generation phrase with
BG modifiers (цена, характеристики).

**Keyword metrics (bg, fresh DataForSEO 2026-09-04):**
| Keyword | Volume (bg, monthly avg) | Trend | Competition | CPC |
|---|---|---|---|---|
| **porsche cayenne electric** | **170** | rising: 20 (2025-08) → 210 (2026-07) | LOW (19) | €0.91 |
| porsche cayenne ev | 30 | flat/noisy | LOW (19) | €1.39 |
| porsche cayenne (bare) | 4400 | flat | LOW (14) | €1.16 |

`porsche cayenne electric` is rising month-over-month as the real launch
approaches — a genuinely growing-demand phrase, not just a recognized one.
Bare `porsche cayenne` (4400/mo) is far too broad/ambiguous — dominated by
buyers of the outgoing ICE model — and cannibalization-adjacent to any future
Cayenne (ICE) content; not used. All three rows banked.

**SERP check:** skipped — announcement/reveal story, not a comparison/how-to
query type.

**GA4:** skipped — one-day-old-equivalent thin post with near-zero traffic
pattern, not worth a pull.

**News CSV:** ⚠️ `meta.news_csv` not returned even with `context=edit` (only
`_yoast_wpseo_*` + `footnotes`) — confirms the known W12 REST-exposure gap
(committed, not yet deployed — [[project-seo-skills-refactor]]) still applies
5 weeks after the EV164 report noted the same thing. Fell back to parsing the
rendered page's news cards (28 unique after dedup — the theme renders each
card twice in the DOM, a minor rendering redundancy noted but not actioned).

Story order as rendered (1 = top of list):
1. Tesla — LFP battery advice for used Teslas
2. Renault Trafic Van — first 800V Renault
3. **Porsche Cayenne Electric — wireless (induction) charging** — Cayenne story A
4. BYD — Europe expansion
5. Hyundai — 3-minute charging target
6–9. Tesla FSD Europe regulatory pushback / Ford Trucks electric big-rig / global EV sales still US-led / Volvo dropping LiDAR
10. **Porsche Cayenne EV — 1139 hp, "most powerful Porsche ever"** — Cayenne story B
11–15. CATL solid-state roadmap / Audi E concept SUV / Tesla Model X weight cut / EU fast-charger spending / grid-readiness warning

### Recommendation
**Focus keyphrase:** `Porsche Cayenne Electric` — matches the post's own
title exactly, real and *rising* BG search volume (170/mo, LOW competition),
a strong recognized autocomplete entity, position-5 GSC baseline (a
snippet-fixable presentation problem, not a content problem), no
cannibalization on this site (`/wp/v2/search?search=Porsche+Cayenne+Electric`
— zero hits; the only other Cayenne-titled post, 8035, covers a distinct
later follow-up angle and will get its own distinct phrase when optimized
next), and not the `/ev-news-feed/` hub phrase.

**Secondary:** `Porsche Cayenne безжично зареждане` (wireless charging —
card #3's specific angle, no autocomplete volume yet but a genuine
differentiator), `Porsche Cayenne 1139 к.с.` (card #10's power figure),
`Porsche Cayenne EV`.

### Proposed metatags
_Deferred to Phase C — `post_content` is still just a video embed (17 words).
Phase B needs to run first (per pipeline order, avoiding the 7333 mistake)._

### Proposed tags
Current tags: `[]` (none).

| Tier | Tag | id | Existing count |
|---|---|---|---|
| Entity | Porsche | 57 | 6 |

`Porsche` (6 uses) sits inside the 3–10 reuse band and is unambiguously the
headline entity — safe to add now. No second entity tag proposed yet — no
prose exists to check the "named in owned text" test against until Phase B
runs; re-evaluate `Cayenne` (checked: no existing tag — `/wp/v2/tags?search=Cayenne`
returned empty, a gap noted below) once ¶1–¶3 exist.

**Gap noted, not created:** no `Cayenne` tag exists yet despite this being
the second Cayenne-titled EV News post in the cluster (7828, and soon 8035) —
a real recurring model-name gap. Not created speculatively per the reuse-only
rule (would start at count 0/1), but flagged for the user: if 8035 also wants
it, that's two posts wanting the same missing tag, which is exactly the
"recurring gap → batch-create deliberately" case the tag rules describe.

No keyword-intent tag fits as a strong candidate (checked `Премиера` — this
is closer to a spec-reveal/rumor-adjacent story than a launch, "премиера"
would overstate it since Cayenne Electric hasn't launched yet).

---

## Phase B — Transcript content
_Written by `ev-news-transcript-content`. Reads `Keyphrase:` above; advances
`Status: content-written`._

**Episode resolved:** `rYm2ZO8AL1c` — "#EVNews - 25.11 - EV128 - Новият
електрически Cayenne на Porsche" (published 2025-11-25T16:54:05Z)
**Answer found in:** own episode — hosts play and react to Porsche's own
promo clip for the new Cayenne Electric starting ~t=923, plus the two news-card
mentions (wireless charging ~t=4003, 1139hp power figure ~t=5124).

| Claim | Quote / paraphrase | Source | Timestamp |
|---|---|---|---|
| Most powerful Porsche ever is electric — new Cayenne EV, 1139 hp | "Най-мощното Porsche досега електрическо. Новия Cayen EV с 1139 коня" | own episode | [1:25:24](https://www.youtube.com/watch?v=rYm2ZO8AL1c&t=5124) |
| Porsche's own promo clip shows the Cayenne Electric beating the 918 Spyder on track | "Каяна е по-бърз от 918-ката. Спайдър" | own episode | [15:23](https://www.youtube.com/watch?v=rYm2ZO8AL1c&t=923) |
| Same clip: the Cayenne test car goes off-road (fields, forest, river-adjacent terrain) while the 918 Spyder is limited to asphalt | "тя прескача през ливади, изпреварва го. Тоест тя не се дава, защото той може да кара само по асфалта. Тя може да кара вече навсякъде" | own episode | [17:14](https://www.youtube.com/watch?v=rYm2ZO8AL1c&t=1034) |
| Wireless (inductive) charging, 11 kW out of the box, no cable | "Out of the box 11 kW... Ти е индукционно, нали? Паркираш, тя каза пуп и почва да зарежда" | own episode | [1:06:43](https://www.youtube.com/watch?v=rYm2ZO8AL1c&t=4003) |

### Draft paragraphs
¶1 (54 words): Porsche Cayenne Electric е официално най-мощният Porsche
досега — новият изцяло електрически Cayenne разполага с 1139 конски сили,
обявено направо в собствения промо клип на марката, пуснат в ефир. В същия
клип Porsche показва Cayenne Electric да изпреварва на писта легендарния
хибриден хиперкар 918 Spyder — демонстрация на реалната мощ зад цифрите.

¶2 (48 words): Другата изненада е зареждането: Cayenne Electric поддържа
безжично (индукционно) зареждане с мощност 11 kW direct из кутията —
паркираш колата и тя започва да се зарежда сама, без кабел. Технология,
която досега Porsche не е предлагала серийно, и посочва накъде е насочен
фокусът за зареждащата инфраструктура на следващото поколение.

¶3 (52 words): Отвъд числата, промо клипът показва и практическата страна:
докато 918 Spyder е ограничен до асфалт, тестовият Cayenne Electric минава
спокойно през ливади и гористи участъци — демонстрация на офроуд
способности, различни от предишния електрически "перф" еталон на марката,
[Porsche Taycan Turbo S](https://www.carlifebydani.com/ev-review/porsche-taycan-turbo-s-nai-burzata-kola/),
чисто шосеен спортен автомобил.

**Total: 154 words** (target 130–190).

### Internal links
- ¶3 → "Porsche Taycan Turbo S – Най – бързата кола, на която съм се качвал"
  (post 1039, `/ev-review/`, 2022-07-06, already optimized) — anchor
  "Porsche Taycan Turbo S", supporting a factual contrast (on-road sports
  coupe vs. off-road-capable SUV), not a same-story sequel — different
  subtopic, date-unrestricted per the outbound rule.

### Facts to confirm before publishing
- [ ] The exact 0–100 km/h comparison numbers mentioned during the promo-clip
  reaction ("0 е 7,4, а другата е 7,3" ~t=1034) are ASR-ambiguous about which
  car each figure belongs to — **omitted from the draft** rather than guessed,
  consistent with the "never invent facts" rule.
- [ ] "1139 коня" (t=5124) is read directly off an on-screen news card in the
  episode, not independently verified against a Porsche press source — stated
  here as reported on-air, same convention as other EV News Phase B drafts.

### Applied (2026-09-04)
- [x] `post_content` written — full block sent (video embed preserved + 3
  paragraph blocks appended, including the ¶3 internal link to 1039).
- **wordCount:** 17 → 172 (Yoast schema, confirmed in the POST response).
- **Auto-linked `/tag/porsche/` count inside body prose:** to be confirmed in
  Phase C once the `Porsche` tag is assigned (post currently has no tags).
- **Ledger row:** `7828-2026-09-04` appended to
  `reports/seo-optimizations/ledger.csv` — `phase=B`, `changed=content`,
  base window 2026-08-07→2026-09-03 (28d: 12 impr / 0 clicks / pos 5),
  `verify_due=2026-10-30` (56d, new body content needs re-crawl/re-index time).

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **Metatags:**

| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to `#EV128 - Новия електрически Cayenne на Porsche - Car Life by Dani`, ~68)* | `Porsche Cayenne Electric – 1139 к.с. %%sep%% %%sitename%%` → renders **`Porsche Cayenne Electric – 1139 к.с. - Car Life by Dani`** | 37 body / 56 total |
| `_yoast_wpseo_metadesc` | *(empty, no fallback rendered)* | `Porsche Cayenne Electric е най-мощният Porsche досега – 1139 к.с. и безжично 11kW зареждане направо от кутията. Вижте промо клипа с 918 Spyder.` | 147 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Porsche Cayenne Electric` | — |

- [ ] **Tags:** current `[]` → propose `[57, 110]` (`Porsche`, `Taycan`).
  `Porsche` (count 6, in-band) is the unambiguous headline entity, named
  throughout. `Taycan` (count 2, below band but pre-existing/established via
  post 1039) is genuinely named in ¶3's own prose (the comparison sentence
  and its link) — per the corrected owned-prose rule
  ([[feedback-tag-cap-owned-prose]]), a real prose mention earns the tag even
  though it's not the headline entity; this reuse also nudges `Taycan` from
  2→3 uses, into the 3–10 band, organically rather than by creation.
- [ ] **H1** — no change; already carries "Cayenne" and "Porsche" naturally.
- [ ] **First 100 words** — satisfied by Phase B's ¶1 (keyphrase in the
  opening sentence).
- [ ] **Subheadings** — none proposed; standard 3-paragraph EV News format,
  consistent with every other post in this pipeline.
- [ ] **Image alt + title** (media 7829, featured image):

| Field | Before | After |
|---|---|---|
| `alt_text` | `""` | `Porsche Cayenne Electric – 1139 к.с., най-мощният Porsche` |
| `title` | `#EVN 128` | `EV128 – Porsche Cayenne Electric` |

### Internal links
**Inbound — existing posts that should link here:** none proposed. Checked
`/wp/v2/search?search=Cayenne` — only 8035 (this cluster's next step, and
newer than 7828) mentions Cayenne; no valid pre-dating source exists outside
this cluster.

**Outbound — already satisfied by Phase B's own prose** (¶3 → post 1039,
Taycan Turbo S review — a factual contrast, not a same-story sequel, safe
regardless of date). No additional outbound proposed.

### Applied (2026-09-04)
- [x] Metatags — title / metadesc / focuskw written (verified in the POST
  response's `meta`, and confirmed live:
  `<title>Porsche Cayenne Electric – 1139 к.с. - Car Life by Dani</title>`,
  `<meta name="description">` matching the approved text).
- [x] Tags — `Porsche` (id 57) and `Taycan` (id 110) written; post's `tags`
  was `[]` before, pure add.
- [x] Image alt + title — media 7829 written. Backup of pre-write values
  (empty alt / auto-generated "#EVN 128" title) saved to
  `reports/yoast-meta-backup/media-7829-2026-09-04.csv`.
- Yoast postmeta backup (pre-write, all empty):
  `reports/yoast-meta-backup/7828-2026-09-04.csv`.

### Declined
_None — both proposed groups (metatags+focuskw+tags, image alt+title)
approved in full._

### Risks / notes
- Position-5 GSC baseline with 0 clicks (28d) is exactly the "presentation
  problem" case — the realistic test is whether CTR moves at this position
  once the new title/description are indexed, not whether position itself changes.
- `Cayenne` has no existing tag (checked, empty) — a real, recurring gap
  since 8035 will likely want it too; flagged for a possible deliberate
  batch-create, not created speculatively here.
- The two ASR-ambiguous 0–100 figures from the promo-clip reaction were
  deliberately omitted from ¶1–¶3 rather than guessed (see Phase B's facts-
  to-confirm).

### Measurement
Baseline (GSC, 28d ending 2026-09-03): 12 impressions, 0 clicks, 0% CTR,
position 5. Re-check the **metatag/tag/alt change in 2–4 weeks**
(`verify_due` 2026-10-02, ledger `7828-2026-09-04-C`) and the **new body
content in 4–8 weeks** (`verify_due` 2026-10-30, ledger `7828-2026-09-04`,
Phase B) — content needs re-crawl/re-index time beyond the metatag change.

### Step 8 verification (2026-09-04, post-write)
- Re-fetched rendered page: `<title>` and `<meta name="description">` both
  confirmed live and matching what was written.
- `/tag/` links found on the page for this post's own tags: `porsche` ×4,
  `taycan` ×2 — includes template widgets alongside any body-prose
  auto-links, not isolated further this pass (same caveat as prior posts).

---
