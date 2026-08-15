# SEO Optimization — #EV122 – Zeekr в България

**URL:** https://www.carlifebydani.com/ev-news/ev122-zeekr-v-balgariya/ · **Post ID:** 7577 · **Category:** ev-news
**Prepared:** 2026-08-15
**Status:** applied
**Keyphrase:** `Zeekr влиза в България`
**Ledger:** 7577-2026-08-15 (Phase B, content); 7577-2026-08-15-C (Phase C, title|metadesc|focuskw|tags|alt)

**Business context:** fifth post in the same Zeekr push as 9099 (`Zeekr 7X цена`), 8659
(`Zeekr представителство в България`), 9178 (`Zeekr 7X vs BMW iX3`) and 8950 (`Zeekr Golden
Brick батерия`). Previously blocked by a flagged duplicate-URL issue (vs post 7584, same
episode-number collision) — checked live 2026-08-15 and confirmed resolved upstream (7584 is
now correctly slugged/titled as `#EV123`); see `docs/SEO_EV_NEWS_TODO.md` P2. Clear to run.

---

## Phase A — Keyphrase research
_Written by `seo-keyphrase-research`._

### What this article is about
EV News episode #EV122 (recorded/published 2025-10-14, YouTube `CjEPOOFk3Ao`, 196 transcript
chunks). **Headline story** (per transcript, the hosts' own on-air topic — "новината на корицата"):
this is the **first announcement** that Zeekr is entering the Bulgarian market. Mid-recording,
one host reads a marketing email live on air: Zeekr is coming to the Bulgarian market through
a local partnership (partner name ASR-garbled — audible as something like "СИГ", not resolvable
confidently; flagged, not guessed). Key facts, all hedged/speculative in the hosts' own words:
- First **three models** expected to be imported initially; test-drive sign-ups already open,
  though the format isn't the classic one-hour test drive the partner initially described.
- No confirmed timeline — the partner ("Ивелина", a contact at the local side) could only say
  cars are expected around **Q1–Q2 2026**; most detail questions were deferred ("не е подходящо
  да ти отговарям още").
- Uncertain whether the **001 R** configuration will be part of the initial lineup.
- The **"Golden Brick" battery** comes up as a secondhand curiosity, not a technical deep-dive:
  the host recalls real gold separators between battery cells from a past presentation, improving
  thermal/electrical isolation between modules. (Contrast: post 8950 covers this battery from an
  actual Zeekr Europe technical expert, in real depth — this episode is hearsay by comparison,
  don't treat it as a source for technical claims.)
- Positions Zeekr within the **Geely** group alongside Volvo and Lynk & Co, and notes the same
  importer who already brings in Lynk & Co is behind Zeekr's entry too.
- Hosts frame this explicitly as the week's top story — "новината на тая седмица е, че Zeekr
  влиза в България."

After the headline segment the episode covers unrelated external EV news cards (no `news_csv`
available for this post — see below) — not pulled as tag/entity seeds since there's no way to
separate on-topic from off-topic rows without it.

**Owned word count: 16** (Yoast `wordCount`) — `post_content` is only the YouTube embed
(`https://www.youtube.com/live/CjEPOOFk3Ao`). This is a Phase-B candidate.

**`news_csv` not available.** This post predates the `show_in_rest` registration for that
postmeta field (2025-10 vs. the field landing 2026-08-14 per `_shared/constants.md`) — the
`meta` object returns only the four registered Yoast/footnotes keys, nothing else. Degrading
cleanly per the skill's guidance: no CSV-derived entity seeds for this post, entities come from
the transcript only.

### Current state
| | Value | Length |
|---|---|---|
| `<title>` | *(no SEO title → falls back to H1)* `#EV122 - Zeekr в България - Car Life by Dani` | not keyphrase-optimized |
| `<meta name=description>` | **absent entirely** | — |
| Focus keyphrase | empty | — |
| Owned word count | **16** | thin — Phase B required |
| Images without alt | featured image (media 7578, `evn-122-scaled.jpg`) — alt/title not yet checked individually, will confirm at Phase C | — |
| Existing tags | none | — |
| Internal links out / in | 0 out (no prose yet); 0 confirmed in |

### Demand research
**GSC (90d, reusing this session's `query,page` pull, filtered to this exact URL):** zero
matching rows. Nothing to preserve — choosing a keyphrase from scratch, same as every other
Zeekr post in this push so far.

**Google autocomplete (hl=bg, gl=bg):** thin across the board for this specific announcement
framing — consistent with the skill's own note that absent BG long-tail volume isn't evidence
of no demand, this market is under-indexed by the tools available.
| Seed | Result |
|---|---|
| `zeekr идва в българия` / `zeekr навлиза българия` / `zeekr влиза в българия` / `zeekr идва българия` | no completions |
| `zeekr партньор` / `zeekr дилър зикр` | no completions |
| `zeekr golden brick батерия злато` | no completions |
| `zeekr` (from 8659's research, reused) | zeekr 7x, zeekr 9x, **zeekr българия**, zeekr 8x, zeekr 7x цена... |
| `zeekr българия` (from 8659's research, reused) | zeekr българия, zeekr българия цена, zeekr 7x българия, **zeekr представителство в българия**... |

**Cannibalisation check — the important one for this post.** 7577 and 8659 share the same
"Zeekr is in Bulgaria" framing by title, and this was flagged as a soft overlap risk in 8659's
own Phase A report (2026-08-15). Resolved by content, not just phrasing:
- **8659** (`Zeekr представителство в България`, EV151, 2026-05-19) — the *later* story: a Zeekr
  X7 demo unit already spotted in Bulgaria, dealer presence confirmed, Bulgarian-language
  website live. Confirmed-presence framing — "вече е" (already is).
- **7577** (this post, EV122, 2025-10-14) — the *original* story, **7 months earlier**: the
  first announcement/rumor that Zeekr is coming, no confirmed timeline, speculative Q1–Q2 2026
  window, unconfirmed model lineup. Pre-launch/announcement framing.

These are two distinct newsworthy moments in the same arc, not duplicate content — chose
`Zeekr влиза в България` (present-tense "entering", not "already is") specifically to keep the
announcement-stage framing distinct from 8659's confirmed-presence phrasing. Checked
`/wp/v2/search?search=Zeekr Българя` — 7 hits, none with an existing Yoast focus keyphrase on
this exact phrase (8659 targets `представителство`, 9099 targets `7X цена`, 9178 targets the
BMW comparison, 8950 targets `Golden Brick`, 4904 (#EVN60, "Zeekr 001 R в България") has no
Yoast optimization done — a sixth, older, lower-priority Zeekr/Bulgaria post noted for later).
Also checked `/wp/v2/search?search=Golden Brick` — only 8950 — confirms the battery mention in
this episode is safe to reference briefly (with the "hearsay, not technical source" caveat
above) without competing for that keyphrase.

**SERP check:** not run — DataForSEO still blocked (`40104`), Semrush's BG index too sparse for
this long-tail. Same reasoning as every other post in this push.

### Recommendation
**Focus keyphrase:** `Zeekr влиза в България` — genuinely unique framing (announcement-stage,
not confirmed-presence), zero cannibalisation against the other four Zeekr posts once checked
by actual content and not just title, and the article authentically is about this exact moment
(the entry announcement, not the entry itself).

**Secondary:** `Zeekr пазар България`, `Zeekr представител партньор`, `Zeekr Golden Brick`
(mention only — not a target, owned by 8950)

### Proposed tags
| Tier | Tag | id | Existing count | Verdict |
|---|---|---|---|---|
| Entity | Zeekr | 222 | 6 | ✅ keep — headline brand, in-band |
| Entity | Geely | 150 | 6 | 🟡 **likely add** — genuinely discussed (parent group, Golden Brick co-development, Volvo/Lynk & Co siblings), in-band — confirm once Phase B prose exists and actually names it |
| Keyword-intent | Премиера | 48 | 7 | 🟡 **likely add** — this episode is exactly a market-entry/launch announcement, matches the site's existing pattern for this tag, in-band — confirm at Phase C |

Per the skill's own rule for thin posts (Phase B hasn't run yet): only tagging what's
confirmed now (Zeekr). Geely and Премиера are strong candidates but held pending Phase B's
actual prose — Phase C will do the final "named in prose" check before writing either.

`Zeekr 001 R` (id 358, count 1) — **skip**, below the 3–10 band despite being mentioned
(uncertain lineup detail) — noted as a recurring-gap candidate, not created/reused thin.

---

## Phase B — Content
_Written by `ev-news-transcript-content`._

**Episode resolved:** #EV122, YouTube `CjEPOOFk3Ao`, 196 chunks, published 2025-10-14T16:00:51Z
— matches the post's own `date` (2025-10-14T18:01:23, same-day publish).

**Own-episode search (Step 3)** answered the headline directly — no archive fallback (Step 4)
needed. The episode's own transcript is the sole source for every claim below.

### Source-claim table

| Claim | Transcript (paraphrase) | `timestamp_url` |
|---|---|---|
| Zeekr's Bulgarian-market entry announced via a marketing email, read live on air | "Zкър идва на българския пазар с партньорство..." — partner name audible but ASR-garbled (~"сиг"), not usable as a confirmed name | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2517 |
| First 3 models to be imported; test-drive sign-ups already open, exact format still to be clarified with the partner | "...първите три автомобила, които те ще започнат да внасят в България... включително и записване за тестдрайв. На тоя етап ние не сме им обяснили още, че ние тест драйв не правим точно във формата, който те си представят" | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2616 |
| "Golden Brick" battery — real gold separators between cells, from an earlier Zeekr presentation, aimed at reducing thermal/electrical crosstalk between modules | "...преди може би година и половина изкараха така наречената батерия Goldн Брик... вътре между елементи, между клетките на батерията имаше разделители от истинско злато" | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2698 |
| Zeekr sits within the Geely group alongside Volvo and Lynk & Co; the same importer already selling Lynk & Co is behind Zeekr's entry | "...в Гийли са и Volvo, Zкър, Link C... Link, компанията майка, която внася линк в България... тя внася Easy икър" (ASR renders "Zeekr" inconsistently as "Zкър"/"Zикър"/"Easy икър" throughout — same entity, not three different ones) | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2698 |
| No confirmed timeline — the local contact ("Ивелина") could only give a rough window | "Очакването на тоя етап е, че 26 година може би Q1, Q2 ще започне да има такива автомобили в България"; "на много голяма част от нещата не е подходящо да ти отговарям още" | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2857 |
| Uncertain whether the 001 R configuration will be part of the initial lineup | "На тоя етап няма отговор дали ще има а 001 R конфигурацията" | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2929 |
| Framed as the top story of the week | "Та за мен новината на тая седмица е, че Zкър влиза в България" | https://www.youtube.com/watch?v=CjEPOOFk3Ao&t=2929 |

**Open fact NOT used:** the partner/importer company name is audible but the ASR renders it as
an unintelligible fragment ("сиг") — not resolvable with confidence. Left out of the prose
entirely rather than guessed, per the skill's hedge-or-omit rule. If the user knows the actual
partner name from other context, it can be added in a follow-up edit.

### Draft paragraphs (152 words)

**¶1** (44 words) — answers the headline, keyphrase front-loaded:
> Zeekr влиза в България — това е новината, която задвижи подкаста на #EV122. По думите на
> водещите, обявата дойде като маркетингов имейл: марката стартира партньорство за официално
> навлизане на българския пазар, а първите записвания за тестдрайв вече текат, макар форматът
> да предстои да се уточни.

**¶2** (60 words) — what the hosts actually said, first-hand:
> Контактното лице от локалния партньор е потвърдило само част от детайлите — очакването на
> този етап е автомобилите да пристигнат около първото или второто тримесечие на 2026 година, а
> дали конфигурацията Zeekr 001 R ще влезе в първоначалната гама остава неясно. Водещите
> припомнят и "Golden Brick" батерията на марката — с реални златни разделители между клетките,
> според по-ранна презентация на Zeekr.

**¶3** (48 words) — episode's actual coverage, brand positioning:
> Zeekr е част от концерна Geely, редом с Volvo и Lynk & Co — и именно вносителят, който вече
> продава Lynk & Co в България, стои зад навлизането на Zeekr. Водещите определят новината като
> най-важната за седмицата, въпреки че голяма част от подробностите все още се пазят в тайна.

### Internal links (2, per Step 6)

1. **"Golden Brick" батерията** (¶2) → **8950** (`/ev-masters/zeekr-v-evropa-skorost-garancziya-i-serviz-bez-kompromisi/`)
   — the dedicated feature covers this exact battery from an actual Zeekr Europe technical
   expert; this episode's mention is hearsay by comparison, so the link sends the reader to the
   real depth. Outbound, no historical-sequence restriction (that rule only governs inbound
   links to the post being optimized).
2. **навлизането на Zeekr** (¶3) → **8659** (`/ev-news/ev151-zeekr-veche-e-v-blgariya/`) — the
   natural "what happened next" link: 8659 is the confirmed-presence follow-up story (Zeekr X7
   demo unit spotted, dealer presence live), 7 months after this announcement-stage episode.

### Open facts to confirm with the user
- Partner/importer company name — ASR-garbled, omitted entirely (see above).
- Whether "Ивелина" (the local contact's first name, audible clearly) should be named in the
  prose or left generic ("контактното лице от локалния партньор") — currently drafted generic,
  since a first name alone without surname/role confirmation felt too informal for on-page copy;
  easy to add back if preferred.

### Applied (2026-08-15)

Written as drafted (user approved "as drafted", generic contact reference, partner name
omitted). Verified live:
- `wordCount` moved from 16 → **163**.
- All 3 paragraphs present after the video embed, in order.
- Both internal links present and pointing correctly (`Golden Brick батерията` → 8950,
  `навлизането на Zeekr` → 8659) — each appears twice on the rendered page, once in body prose
  and once in the theme's own "Избрано за вас" auto-related-posts widget (driven by shared
  category/entities, not `post_content` — same pattern seen on every other post in this push).
- No `/tag/` auto-links yet — post has zero tags assigned (Phase A proposed Zeekr confirmed,
  Geely/Премиера pending); Phase C will assign and this gets re-checked then.
- `excerpt.raw` confirmed empty (cleared in the same write) — no duplicate-text risk.

---

## Phase C — Apply
_Written by `seo-article-apply`._

### Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | *(empty → falls back to `#EV122 - Zeekr в България`)* | `Zeekr влиза в България — какво знаем %%sep%% %%sitename%%` | 36 body |
| `_yoast_wpseo_metadesc` | *(absent)* | `Zeekr влиза в България — партньорство за официално навлизане, записвания за тестдрайв и очаквания за 2026 Q1–Q2. Какво разкриха водещите на #EV122.` | 147 |
| `_yoast_wpseo_focuskw` | *(empty)* | `Zeekr влиза в България` | — |

Keyphrase is in the first 22 characters of the title body (well within the first-30 rule) and
the metadesc's opening clause. Every claim in the metadesc traces to Phase B's source table —
partnership, test-drive sign-ups, the Q1–Q2 2026 window — nothing invented.

### On-page checks (Step 3)
- H1 unchanged — carries the keyphrase's core terms (`Zeekr`, `България`) naturally already.
- First 100 words: keyphrase `Zeekr влиза в България` opens ¶1 verbatim — confirmed, no edit
  needed (Phase B already front-loaded it per the report's `Keyphrase:` line).
- No new H2/H3 proposed — a 163-word, 3-paragraph EV News post doesn't carry subheadings on
  this site (matches every other post in this push).

### Proposed tags
| Tag | id | Existing count | Verdict |
|---|---|---|---|
| Zeekr | 222 | 6 | ✅ confirmed — named in all 3 paragraphs |
| Geely | 150 | 6 | ✅ confirmed — named directly in ¶3 ("Zeekr е част от концерна Geely") |
| Премиера | 48 | 7 | 🟡 judgment call — this is a market-*entry* announcement, not a specific model premiere/reveal in the strict sense, but it's the closest existing keyword-intent tag to "brand launching in a new market" and matches the site's own loose usage of the term for launch-adjacent news. Flagging for explicit approval rather than treating as settled. |

New tag set if all three approved: `[222, 150, 48]`.

`Zeekr 001 R` (358, count 1) — still skipped, below band (unchanged from Phase A).

### Proposed image alt + title (featured image only — the post has no inline images)
| Media | Image | `alt_text` (proposed) | `title` (proposed) |
|---|---|---|---|
| 7578 (featured) | `evn-122-scaled.jpg` | `Zeekr в България — обявата за партньорство и първите модели за пазара` | `Zeekr в България` |

Currently: `alt_text` empty, `title` raw `#EVN 122` (auto-generated pattern, same issue caught
on 7333's featured image previously).

### Internal links (Phase C review)
**Inbound — none available.** Checked `/wp/v2/search` for the keyphrase and for `Zeekr` — the
only Zeekr/Bulgaria-market posts on the site are 9099 (2026-07-09), 8950 (2026-06-21) and this
post itself (2025-10-14). **7577 is the oldest of all five Zeekr posts in this push** — no
existing post predates it, so per the historical-sequence rule there is no valid inbound-link
source. Zero proposed, not forced.

**Outbound — already covered.** Both of Phase B's own internal links (`"Golden Brick"
батерията` → 8950, `навлизането на Zeekr` → 8659) went out under Phase B's approval since they
were embedded directly in the drafted prose the user approved — no further action here. Checked
for a third possible outbound (Geely mention → `/publications/razlichnite-platformi-na-geely/`,
post 3518) but declined: it's about Geely's platform architecture, not the brand's Bulgarian
retail presence, and two links in a 163-word piece is already proportionate — a third would be
padding, not reader value.

### Applied (2026-08-15)

Both approved groups written:
- **Metatags + tags** — title/metadesc/focuskw as drafted; tag set `[222, 150, 48]` (Zeekr,
  Geely, Премиера — all three approved).
- **Featured image alt/title** — media 7578 written with descriptive alt + real title,
  replacing the empty alt / auto-generated `#EVN 122` title.

**Backups:** `reports/yoast-meta-backup/7577-2026-08-15.csv` (Yoast, pre-write all empty),
`reports/yoast-meta-backup/media-7578-2026-08-15.csv` (image alt/title, pre-write empty/auto).

### Live verification

```
$ curl -s .../ev-news/ev122-zeekr-v-balgariya/ | grep -oE '<title>...|<meta name="description"...'
<title>Zeekr влиза в България — какво знаем - Car Life by Dani</title>
<meta name="description" content="Zeekr влиза в България — партньорство за официално
навлизане, записвания за тестдрайв и очаквания за 2026 Q1–Q2. Какво разкриха водещите на
#EV122." />
```
Both changed from the pre-write empty/fallback state.

**Tag auto-link count** (body prose): `zeekr` ×5, `geely` ×3, `premiera` ×2. All three tags
now auto-link inside the new Phase B paragraphs, as expected once tags exist on body text.

### Declined
Nothing declined — both approval-gate questions came back "apply all" / "yes, write both".

### Link-direction correction (2026-08-15, post-publish)

The user caught that ¶3's outbound link (`навлизането на Zeekr` → 8659) had this backwards:
7577 (2025-10-14, the original entry *announcement*) was linking forward to 8659 (2026-05-19,
the *confirmed*-entry follow-up) as if narrating settled fact about a story that, at 7577's own
publish time, hadn't happened yet. Both posts narrate the *same continuing event* — this is a
same-story-sequel citation, which should run new → old, not old → new (distinct from an
ordinary outbound "further reading" link to a different subtopic, which stays date-unrestricted
— see the "Golden Brick" → 8950 link in ¶2, kept as-is on review since it points to a genuinely
different subtopic, not this story).

Fixed: removed the link from 7577's ¶3 (plain text `навлизането на Zeekr` now), and added the
citation to **8659** instead — its ¶1 now reads "...нещо, което подкастът [обяви за пръв път]
още през октомври 2025 г. — в епизод #EV151..." linking back to 7577. Verified live on both
URLs; the one remaining `ev151`-href match on 7577's rendered page is the theme's own "Избрано
за вас" related-posts widget, not `post_content`.

Also updated `seo-article-apply/SKILL.md` and `ev-news-transcript-content/SKILL.md` with a
same-story-sequel rule so this doesn't happen again on the next post.

### Measurement plan
Phase B (new body content, 16→163 words): re-check GSC in **4–8 weeks** — `verify_due`
2026-10-10 (ledger row `7577-2026-08-15`). Phase C (metatags/tags/alt): re-check in **2–4
weeks** — `verify_due` 2026-09-12 (ledger row `7577-2026-08-15-C`). Baseline for both was
genuinely zero (0 impressions/0 clicks for this exact URL, confirmed via the cached 90-day
`query,page` pull) — a cold start, so any real pickup is a clean signal.

---
