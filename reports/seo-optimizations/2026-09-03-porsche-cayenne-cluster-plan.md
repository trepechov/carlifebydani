# Porsche cluster — optimization plan

**Trigger:** review of the new Porsche Cayenne Electric is scheduled. Prep the
existing Porsche/Macan/Cayenne/Taycan content now so that when the Cayenne
review publishes, it lands into a cluster where every older post is already
optimized and ready to both receive and give internal links.

**Status:** planned, none applied yet.
**Created:** 2026-09-03.

---

## Why this order

Oldest → newest. Two reasons:

1. **Internal links only pay off if the target is already optimized.** A link
   into a post with no focus keyphrase, no tags, and a Yoast-default title is
   a link into a weak page. Fixing the oldest post first means every
   subsequent optimize pass has a real, keyword-relevant page to link back to.
2. **Chronological link direction matches the site's existing rule.** Commit
   `ec014b2` ("enforce chronological order for same-story sequel links" in
   `seo-article-apply`) already encodes this: a newer post links back to an
   older one in the same story line, not the reverse, until the newer one is
   itself the most recent — then it's the one that gets linked *into* by
   whatever comes after it. Working oldest-first means each step's Phase C
   run finds its backlink targets already in place instead of having to be
   revisited later.

When the **new Cayenne Electric review** is published post-shoot, it becomes
the newest node — link every step below into it retroactively (see
"After the new review" at the end), and it should link back to all of them.

---

## The cluster (4 available now for optimization + 1 held draft, oldest first)

| Order | ID | Date | Title | Category | Status |
|---|---|---|---|---|---|
| 1 | **1039** | 2022-07-06 | Porsche Taycan Turbo S – Най-бързата кола, на която съм се качвал | ev-review | **available — optimize now** |
| 2 | **625** | 2024-01-12 | Porsche поставя Macan на изпитания в името на производителността и ефективността | publications | **available — optimize now** |
| 3 | **7828** | 2025-11-25 | #EV128 – Новия електрически Cayenne на Porsche | ev-news | **available — optimize now** |
| 4 | **8035** | 2026-01-13 | #EV134 – Porsche Cayenne – на къде са тръгнали Porsche? | ev-news | **available — optimize now** |
| 5 | **9216** | draft since 2026-07-19 — **not yet published** | Porsche Macan GTS Electric — Защо съществува и кой има нужда от него? | publications | **DRAFT — hold, do not optimize** |

**9216's slot changed from the original plan.** It's still unpublished as of
2026-09-04. WordPress stamps a draft's public `date` at the moment it's
actually published, not backdated to when it was drafted (2026-07-19) — so
whenever it goes live, its date will be *whatever "now" is at that moment*,
which is already past 8035 (2026-01-13) and will almost certainly still be
past 8035 by the time this cluster is worked. That makes 9216 the **newest**
node in the cluster once published, not something that slots in after 625 —
run it **last**, as step 5, after 1039/625/7828/8035 are all done.

URLs: 1039 `/ev-review/porsche-taycan-turbo-s-nai-burzata-kola/` · 625
`/publications/porsche-postavya-macan-na-izpitaniq-v-imeto-na-proizvoditelnostta/`
· 9216 `/publications/porsche-macan-gts-electric-zashho-sshhestvuva-i-koj-ima-nuzhda-ot-nego/`
(unpublished, no live URL yet) · 7828
`/ev-news/ev128-noviya-elektricheski-cayenne-na-porsche/` · 8035
`/ev-news/ev134-porsche-cayenne-na-kde-sa-trgnali-porsche/`

None of the 4 available posts have been through `seo-article-optimize` yet
(checked `reports/seo-optimizations/ledger.csv` and `reports/seo-metatags/` —
no existing rows/reports for any of them). All 4 are ready to run in any
order that respects the sequencing below — nothing is blocking them. 9216
hasn't been optimized either, and shouldn't be until it's out of draft.

### Per-post notes

**1. Post 1039 — Taycan Turbo S review (oldest, run first)**
Real hands-on review, ev-review category — same shape as the Zeekr 7X review
(9099) that already went through this pipeline, so treat it the same way:
Phase A + Phase C only (no Phase B — it's not an EV News post). This is the
performance/brand-halo anchor for the cluster; everything downstream can
credibly link to "the fastest Porsche we've driven" as context for Cayenne
Electric's positioning.

**2. Post 625 — Macan Electric development/testing article**
This is Porsche's own pre-launch press material (camuflaged-prototype testing,
PPE platform, 800V/270kW charging) — owned prose, not a review. Phase A +
Phase C (publications category, no Phase B path exists for it). Link back to
1039 once 1039 is live.

**3. Post 7828 — EV128, Cayenne Electric reveal**
EV News, title = the story (leading item). Phase A → Phase B (transcript
content — check episode has a mapped transcript first) → Phase C. Link back
to 1039 and 625.

**4. Post 8035 — EV134, Cayenne follow-up**
EV News, title = the story again — same Cayenne narrative continued ~7 weeks
later. Phase A → Phase B → Phase C. Link back to 1039, 625, **and 7828**
(same-story sequel — this is exactly the case `ec014b2` was written for).

**5. Post 9216 — Macan GTS Electric review — DRAFT, hold until published, then run last**
"Porsche Macan GTS Electric — Защо съществува и кой има нужда от него?"
(publications category, created 2026-07-19, still unpublished as of
2026-09-04). This is the hands-on review that 625 is not (625 is
manufacturer pre-launch press material, not a review) — **do not run any
optimize step on it while it's a draft**, it's not final. Tracked here so
it isn't missed once it publishes:

- Its eventual publish date will be set to whenever it actually goes live,
  not backdated to its 2026-07-19 draft date — so it lands **after** 8035
  (2026-01-13) chronologically, making it the newest node in the cluster,
  not something that slots in between 625 and 7828/8035 as originally
  assumed. Run it **last**, as step 5, after 1039/625/7828/8035 are all done.
- Once published and optimized, it should link back to all 4 posts above
  (1039, 625, 7828, 8035) — same `ec014b2` chronological-sequel rule as the
  Cayenne review handoff below.
- 8035 (and 7828, if relevant) should then be revisited to add a forward
  link into 9216, since 9216 is now the newer post in the same story line.
- Re-check its publish status before each future pass through this cluster —
  ping me when it goes live rather than assuming.

### Excluded from this pass

Three older EV News episodes carry the `Porsche`/`Macan` tags (EVN73, EVN74,
EVN75, all Sept–Oct 2024) but are pre-`news_csv` format: a YouTube embed plus
a raw table of 50–90 scraped external headlines, no editorial ranking, no
owned prose. Porsche appears as a single buried bullet in each, not a leading
story — same "owned-prose-vs-cards" reasoning as the tag-cap rule
([[feedback-tag-cap-owned-prose]]). Not worth an optimize pass; nothing to
write metatags or links *into* meaningfully. Skip unless you want them
retro-fitted into the current format first (separate, bigger job).

---

## Execution

Run one at a time, in order, via the standard orchestrator — each is a plain
`/seo-article-optimize` call, nothing custom needed. All 4 below are
available to run now, in this order:

```
optimize post 1039   (Taycan Turbo S)
optimize post 625    (Macan Electric development)
optimize post 7828   (EV128 – Cayenne reveal)
optimize post 8035   (EV134 – Cayenne follow-up)
```

Then, once 9216 is out of draft and published (not before):

```
optimize post 9216   (Macan GTS Electric review — run last, after the 4 above)
```

Each run hands its own dated report between phases as usual
(`reports/seo-metatags/<date>-<id>-<slug>.md`) and gets its own ledger row in
`reports/seo-optimizations/ledger.csv` with its own `verify_due` date — the
Zeekr-cluster verification cadence (`seo-performance-report` checks rows once
`verify_due` is reached) applies here unchanged, no special handling needed.

## After the new Cayenne Electric review is published

1. Run `seo-article-optimize` on the new review post itself once it exists —
   it should link back to whichever of the 5 posts above are live and
   optimized at that point (1039, 625, 7828, 8035, and 9216 if it has
   published by then), same chronological rule.
2. Revisit the **current newest node** (8035, or 9216 if it published and was
   optimized first) and add a forward link into the new review — it's now
   the "older post in the same story" relative to the review, so it's the
   one allowed to point forward per the `ec014b2` rule. Optionally the same
   for 7828 if the review references EV128's reveal directly.
3. If 9216 and the new Cayenne review both end up publishing around the same
   time, compare their actual publish dates before optimizing either — the
   older one links back to everything, the newer one gets linked into.
