# SEO Optimization — Как да зареждаме електромобил в гаража

**URL:** https://www.carlifebydani.com/publications/domashno-zarezhdane-na-elektromobili/ · **Post ID:** 3957 · **Category:** publications
**Prepared:** 2026-09-04 (first phase to touch this post)
**Status:** applied
**Keyphrase:** зареждане на електрически автомобил
**Ledger:** 3957-2026-09-04

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`. Sets `Status: researched` and the `Keyphrase:` line above._

### What this article is about
A long-form how-to (published 2024-04-09, category `publications`) on planning home EV
charging in a Bulgarian garage: how to size daily charging need (km/day × kWh/100km),
battery buffers and the practical 20–80% daily window, night-tariff economics, station
selection (Tesla Gen 3 Wall Connector as the no-access-control pick), phase/cable choice
(single- vs three-phase, CEE 16A/32A, cable gauge by run length), electrical-meter/partida
sizing, and outlet types (Шуко vs industrial CEE). Links out to `ev-database.org` for a
Tesla Model Y Long Range spec example and to four charging-installer partners, plus one
internal link to the eBag fleet-electrification piece (`ev-masters`, post 9350) — the
reciprocal link from that post's own `seo-article-optimize` run (2026-09-04) is what put
3957 on `docs/SEO_BACKLINK_TARGETS_TODO.md`. This is real, substantial owned prose — not
an EV News thin-content case; Phase B does not apply.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | Как да зареждаме електромобил в гаража - Car Life by Dani | rendered, no Yoast title set |
| `<meta name=description>` | *(none — not emitted at all)* | 0 |
| Focus keyphrase | *(empty)* | — |
| H1 | Как да зареждаме електромобил в гаража | — |
| Owned word count (Yoast) | 2355 | — |
| Images without alt | several (`alt=""` on multiple diagrams; some have good descriptive alt already) | — |
| Internal links out / in | 1 out (→ post 9350) / 1 in (← post 9350, inbound-edit) | — |

### Demand research
**GSC (90d, 2026-06-06→09-03, this URL — page-level from cache, query-level via full
`query,page` pull filtered client-side, top 1000 site rows by clicks so long-tail
zero-click queries are undercounted):**

Page total: **11,519 impr / 263 clicks / 2.28% CTR / pos 7.0** — by far the single
biggest opportunity found in this project to date.

| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|
| зареждане на електрически автомобил | 467 | 1 | 0.21% | 9.3 |
| зареждане на електрически автомобили | 194 | 1 | 0.52% | 22.0 |
| зарядна станция за гараж | 69 | 3 | 4.35% | 7.1 |
| зарядна станция в гаража | 38 | 2 | 5.26% | **2.7** |
| bz4x трифазно зареждане ако е налично | 33 | 0 | 0% | 9.4 |
| bz4x 11 kw ac зареждане максимална мощност за домашно зареждане | 32 | 0 | 0% | 12.1 |

These 9 visible rows sum to only ~837 of the 11,519 impressions — the other ~92% is
long-tail (many distinct low-volume Bulgarian charging questions the 2355-word article
already answers) that didn't survive the site-wide top-1000-by-clicks cap. Read as:
position 7.0 average with real clicks means the page is genuinely earning long-tail
visibility already; the missing meta description (Google is auto-snippeting a random
mid-article sentence about kWh/100km) is suppressing CTR across all of it.

**Google autocomplete (hl=bg, gl=bg):**
- `зареждане на електромобил` → …**вкъщи**, …**в домашни условия**, …цена, …в дъжд
- `зарядна станция` → …**за електромобил**, …7.4 kw, …11kw, …22kw (matches the article's
  own kW/amperage tables almost exactly)
- `зареждане на електромобил в гаража` → no completions (too specific — confirms this
  is *this article's own* long-tail territory, not a competed phrase)

**Keyword metrics (bg, DataForSEO, fresh 2026-09-04 — banked):**
| Phrase | Volume/mo | CPC | Competition |
|---|---|---|---|
| зарядна станция за електромобил | 720 (up to 1,600 in 2026-06/07) | €0.72 | Medium |
| зареждане на електрически автомобил | 170 (up to 260 recently) | €0.53 | Medium |
| зарядна станция за гараж / в гаража / за дома, домашно зареждане на електромобил | no volume returned (long-tail, sub-threshold) | — | — |

(`зареждане на електромобил` — 90/mo — was already cached from 2026-09-03.)

**SERP check (DataForSEO live, bg, depth 10, cached):** For `зареждане на електрически
автомобил`, this exact page **already ranks organic position 6 (rank_absolute 8, behind
an AI Overview + a video carousel)** — confirmed independently of GSC's averaged 9.3.
The page 1 field is EV-charging-network/OEM content (elmotiv.bg, evpoint.bg,
volkswagen.bg, opel.bg, eldrive.eu, solar.bg, elnexus.bg) — informational intent, which
this article genuinely satisfies (it is not a product/dealer page competing against
those). Google's auto-generated snippet for our row is the weak "15-16 кВч/100км...зимата
харчат около 30% повече" sentence — exactly the kind of mid-article fragment a real meta
description fixes.

### Recommendation
**Focus keyphrase:** `зареждане на електрически автомобил` — already ranks organic
position 6 (GSC avg 9.3) on this exact URL with 467 impressions/90d and real (if tiny)
click volume; distance-beats-volume applies here (closer to page 1 than the higher-volume
`зарядна станция за електромобил`), and the article's own content — sizing daily need,
choosing a station — directly answers this phrase's informational intent. Not competing
with any other post on this site (`/wp/v2/search?search=` for the phrase returns nothing
else matching).

**Secondary:** `зарядна станция за електромобил` (720/mo, the broader synonym the
article's station-selection sections cover), `зарядна станция в гаража` (preserve —
already at position 2.7, just needs a better snippet to lift its 5.26% CTR toward the
~10-15% norm for that rank), `домашно зареждане на електромобил` (matches an existing
tag already on the post).

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty — page falls back to raw post title)* | `Зареждане на електрически автомобил в гаража %%sep%% %%sitename%%` | 44 + 19 = 63 rendered |
| `_yoast_wpseo_metadesc` | *(empty — no meta description emitted)* | `Как да изберете зарядна станция, кабел и партида за домашно зареждане на електрически автомобил в гаража — практично ръководство с примери.` | 139 |
| `_yoast_wpseo_focuskw` | *(empty)* | `зареждане на електрически автомобил` | — |

Note on the title: it front-loads the exact focus keyphrase (currently the H1/rendered
`<title>` uses "зареждаме електромобил", a conjugated near-match, not the exact phrase)
while keeping "гаража" to protect the existing position-2.7 ranking for that word. This
is a Yoast-field-only change — **H1 is left untouched**, since the page already ranks for
the target phrase under the current H1 and there's no evidence the H1 itself is the
problem.

### Proposed tags
Existing tags on the post: Гараж (id 253, count 1), домашно зареждане (id 434, count 3),
Зареждане (id 40, count 13), Нощна тарифа (id 254, count 3). No changes proposed — none
of the additional entities checked clear the reuse+band rule:

| Candidate checked | id | count | Verdict |
|---|---|---|---|
| Tesla | 4 | 59 | Saturated brand hub; Tesla (Wall Connector, Model Y example) is not the headline entity here — skip |
| Зарядна станция | 153 | 1 | Below the 3–10 band — skip, don't create/reuse a thin tag |
| Кабел / cable | — | 0 (no term exists) | No existing term — skip, don't create speculatively |

**Gap worth flagging:** this article's actual second-biggest theme (choosing a charging
*station*) has no viable in-band tag (`Зарядна станция` sits at count 1). If future
articles keep needing this concept, it's a candidate for deliberate batch-tag creation
rather than being skipped article-by-article indefinitely.

---

## Phase C — Apply
_Written by `seo-article-apply`. Advances `Status: applied` for whatever was
actually approved and written._

### On-page changes proposed
- [ ] **H1** — no change. Already ranks organic position 6/GSC pos 9.3 for the focus
  keyphrase under the current H1; nothing points at the H1 being the problem.
- [ ] **First 100 words** — the keyphrase doesn't appear in the opening sentence. Propose
  changing the lead sentence (before the video embed) from:
  > Тази статия ще синтезира информацията представена в този клип:

  to:

  > Тази статия обяснява стъпка по стъпка **зареждането на електрически автомобил** у
  > дома, в гаража, и синтезира информацията от този клип:
- [ ] **Body coverage** — none proposed. The 2355-word article already covers every
  `related_searches` modifier that fits its scope (time-to-charge, cost/tariff, station
  selection); the two modifiers it doesn't cover (`приложение за зареждане`, `безплатни
  станции`) are public-charging-app territory, out of scope for a home-charging guide —
  forcing them in would not be true to the article.
- [ ] **Featured image alt + title** (media id 4021, Tesla Supercharger stock photo):

  | Field | Before | After |
  |---|---|---|
  | `alt_text` | `Зареждане електромобили` | `Зареждане на електрически автомобил у дома, в гаража` |
  | `title` | `Зареждане на електрически автомобили` | `Как да заредим електрически автомобил у дома, в гаража` |

### Internal links

**Inbound — existing (older) posts that should link here:**
| Source post | URL | Anchor text | Where |
|---|---|---|---|
| 1230 — Gigacharger (2023-05-07) | `/ev-masters/gigacharger-mrezhata-pozvolyavashha-zarezhdane-na-spravedlivi-czeni/` | "гараж" | Existing paragraph already says *"Не е необходимо всеки електромобил да си има **гараж**"* — wrap "гараж" in a link to this article (the natural counterpoint: here's the guide if you *do* have one). |
| 1232 — Greencharger (2023-04-30) | `/ev-masters/greencharger-perspektivite-pred-as-zarezhdaneto/` | "как да изберат и инсталират собствена зарядна станция у дома" | Existing paragraph ends *"...възможностите пред клиентите да наемат станция."* — append clause: *", а за собствениците на гараж – [как да изберат и инсталират собствена зарядна станция у дома]."* |

Both predate 3957 (2024-04-09) — 1230 by ~11 months, 1232 by ~11.5 months — so the
forward-in-time direction is correct. Both are thin single-paragraph podcast-description
posts, so the edit is a clean one-clause addition, not a reflow risk.

**Outbound — this article should link to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|
| 5016 — Сравнение при зареждане на IONIQ 5N/Model S Plaid/Model Y (2024-07-04) | `/publications/zarezhdane-na-ioniq-5n-tesla-s-plaid-i-tesla-y-longrange/` | "сравнение на скоростта на зареждане между различни модели" | End of the "Колко често и колко ще зареждаме?" section, after the Tesla Model Y Long Range example — add: *"За по-подробно [сравнение на скоростта на зареждане между различни модели](…), вижте нашия анализ на Hyundai IONIQ 5N, Tesla Model S Plaid и Tesla Model Y Long Range."* |
| 4902 — Hyundai IONIQ 6: разход и зареждане (2024-07-07) | `/publications/hyundai-ioniq-6-vsichko-koeto-znaem-za-razhoda-i-zarezhdaneto/` | "разхода и зареждането на Hyundai IONIQ 6" | Same section — add a second sentence: *"Друг добър пример е [разхода и зареждането на Hyundai IONIQ 6](…)."* |

Neither is a same-story-sequel (both are brand/model-specific deep-dives, a different
subtopic from this general planning guide) — unrestricted by date, and both happen to
postdate 3957 anyway, which is irrelevant for an outbound link. Neither target has a
`reports/seo-optimizations/ledger.csv` row yet — if either link ships, both get added to
`docs/SEO_BACKLINK_TARGETS_TODO.md` per the backlink-target-tracking trap.

### Applied
- [x] Metatags — title / metadesc / focuskw written (verified live via `curl`, Step 8 below)
- [x] Tags — no change (Phase A found none in-band to add; declined items below are separate)
- [x] Inbound links written — target posts: **1230** (Gigacharger, link on "гараж") and **1232**
  (Greencharger, appended clause) — both diffed byte-for-byte against pre-write content;
  only the intended `<a>` insertion differs in each.
- [x] Outbound links written on 3957 itself — to **5016** and **4902**, appended to the Tesla
  Model Y Long Range paragraph in the "Колко често и колко ще зареждаме?" section. Diffed
  against pre-write `post_content`: exactly this one paragraph changed.
- [x] Backlink-target tracking — 5016 and 4902 had no `ledger.csv` row; both added to
  `docs/SEO_BACKLINK_TARGETS_TODO.md` (90d GSC: 4902 = 616/21/3.41%/6.8, 5016 = 336/3/0.89%/5.8).
  3957 itself moved from that file's Backlog to Done.
- [ ] `post_content` — first-100-words keyphrase sentence — **declined this run** (see below).
- [ ] Featured image (4021) alt + title — **declined this run** (see below).
- [ ] Auto-linked `/tag/` count inside body prose: unchanged from before (no new tag-bearing
  prose was added — the two new sentences use brand/model names, not this post's own tags).

### Declined
| Group | What was proposed | Reason declined | Date |
|---|---|---|---|
| First-100-words | Front-load the keyphrase in the lead sentence before the video embed | Not selected in the metatags/content approval question | 2026-09-04 |
| Featured image alt+title | Update media 4021's `alt_text`/`title` to name the keyphrase | Not selected in the metatags/content approval question | 2026-09-04 |

### Risks / notes
This is a presentation + first-100-words + light internal-linking pass on an already
2355-word, already-ranking article — no thin-content risk. The featured image is a
generic Tesla Supercharger stock photo, topically loose for a home-charging guide, but
swapping it is outside this pipeline's scope (image sourcing, not metadata) — flagging
only, not proposing a change.

### Measurement
Baseline (GSC, 28d ending 2026-09-03): **4,061 impr / 78 clicks / 1.92% CTR / pos 6.7.**
Keyphrase baseline (90d, this URL, `зареждане на електрически автомобил`): 467 impr / 1
click / 0.21% CTR / pos 9.3. Re-check in 2–4 weeks for the metatag/link changes.
Ledger row: `3957-2026-09-04`. `verify_due`: **2026-10-02**.

### Step 8 — Verify (live, 2026-09-04)
| Check | Result |
|---|---|
| `<title>` | `Зареждане на електрически автомобил в гаража - Car Life by Dani` ✅ |
| `<meta name="description">` | now present (was entirely absent before) ✅ |
| `og:description` | matches the new meta description ✅ |
| `/tag/` links inside rendered body | 18 (pre-existing — the two new sentences link to other posts by title text, not tag names, so this count is unaffected by this run) |
| Post 1230 content diff | exactly one `<a>` inserted around "гараж", rest byte-identical ✅ |
| Post 1232 content diff | exactly one appended clause with one `<a>`, rest byte-identical ✅ |
| Post 3957 content diff | exactly one paragraph gained two appended sentences/links, rest byte-identical ✅ |

**Measurement plan:** re-check this URL's CTR/position in GSC in 2–4 weeks (`verify_due`
2026-10-02) — this was a metatags + internal-links pass, no new body content, so no
extended re-crawl window is needed. `seo-performance-report`'s monthly run will also pick
this up automatically once due.
