---
name: seo-keyphrase-research
description: Research demand for a single carlifebydani.com article and propose a focus keyphrase, secondary phrases and tags — reads Search Console, Google autocomplete, Semrush BG, live SERP and GA4, cheapest-first, cache-gated. Writes (or opens and appends to) a dated report in reports/seo-metatags/ with Status: researched. Does not touch live WordPress content, does not draft metatags copy or body prose — that is seo-article-apply and ev-news-transcript-content's job. This is Phase A of the seo-article-optimize pipeline; normally invoked by that orchestrator, not directly. Use directly only for a research-only pass (e.g. bulk backlog triage) where no write is planned yet.
---

# SEO Keyphrase Research (Phase A)

**Goal: turn one URL into a focus keyphrase, secondary phrases and a validated
tag list — grounded in what people actually search, not guessed.** This is the
first of three phases in the `seo-article-optimize` pipeline (see that skill
for the orchestration and [`docs/SEO_SKILLS_REFACTOR.md`](../../../docs/SEO_SKILLS_REFACTOR.md)
for why the pipeline is split this way). Phase A needs no body content to run —
research, GSC, autocomplete and SERP checks are all title/entity-level — which
is why it goes first even on EV News pages that are still thin.

**Read [`_shared/constants.md`](../_shared/constants.md) before Step 0.** It
has the site constants table and the traps that apply to every phase; this
file only documents what's specific to research.

**Output:** a research section appended to
`reports/seo-metatags/<date>-<id>-<slug>.md` (template:
[`_shared/report-template.md`](../_shared/report-template.md) § Phase A),
`Status: researched`, and the `Keyphrase:` line the content phase reads. This
skill makes **no writes to live WordPress** — it only reads and researches.

---

## Cost discipline — the cache comes first

Semrush bills **per returned line** and DataForSEO **per request**. Researching
one article does not justify re-buying keyword data the project already owns,
and most articles here share the same entity vocabulary (Tesla, зареждане,
BYD, обхват…), so the overlap between runs is large.

**The rule: never issue a paid keyword call without checking `data/seo-cache/`
first, and always write the result back.**

```bash
# Before any Semrush/DataForSEO call — exits 2 and prints MISSING= if data is needed
python3 tools/seo_cache.py kw get "тесла цена" "зареждане на електромобил" "обхват"

# Fuzzy check when you don't have exact phrases yet
python3 tools/seo_cache.py kw search "заряд"

# After the call — bank every row you paid for (TSV: keyword,volume,cpc,competition,kd)
python3 tools/seo_cache.py kw put --source semrush <<'TSV'
тесла цена	720	0.14	0.05	42
TSV
```

The same applies to whole responses — SERPs, autocomplete, and the expensive
90-day GSC pull (free in money, costly in tokens):

```bash
KEY="зареждане на електромобил|bg"
python3 tools/seo_cache.py raw get serp "$KEY" > serp.json || {
    :  # miss or stale -> fetch it, then:
    python3 tools/seo_cache.py raw put serp "$KEY" --file serp.json --note "dfs serp live/regular"
}
```

Defaults: keyword rows go stale after **90 days** (volume is a 12-month average),
SERPs after 14, autocomplete after 30, GSC/GA4 after 7. `stats` shows what's
banked. Full details in [`data/seo-cache/README.md`](../../../data/seo-cache/README.md).

> **Only real numbers go in the ledger.** Every row must trace to an API response.
> A missing row triggers a fresh call, which is fine; a fabricated row silently
> corrupts a keyphrase decision, which is not.

Order of preference when data *is* missing, cheapest first:
1. **Cache** — free.
2. **GSC + Google autocomplete** — free, and the best signal for this market.
3. **DataForSEO** — cheap per request, batches up to 1,000 keywords in one
   `search_volume` call. Preferred paid source **once it stops returning `40104`.**
4. **Semrush** — most expensive per keyword. Use for what the others can't give
   (KD, `phrase_related`, `phrase_questions`), capped with `display_limit`.

---

## Procedure

### Step 0 — Check what's already banked, and whether this post was already touched

Run `kw search` on the article's main entities before anything else. If the
article is about Tesla pricing and the ledger already holds `тесла`, `тесла цена`
and `tesla model y`, most of Step 3c is already done.

Also check whether `reports/seo-metatags/` already has a report for this post
id (grep the directory, don't assume by date) — if so, open it, read its
`Status:` and any declined items, and append to it rather than starting a new
file (see the report template's header note).

**Also grep `reports/seo-optimizations/ledger.csv` by `post_id`** (this is the
*optimization* ledger — a different file from the keyword cache above). A
prior row means this post already went through Phase C or Phase B at least
once; read `changed` and, if `reports/seo-optimizations/checks.csv` has a
matching `ledger_id`, the verdict. A `flat` or `regressed` verdict on a
specific field is a reason to reconsider before re-proposing the same change,
not a reason to skip the post — a `regressed` metadesc doesn't mean the tags
were wrong too.

### Step 1 — Resolve the URL to a WP post

Take the slug (last path segment) and look it up. All content types on this site
(`/ev-news/`, `/publications/`, `/ev-review/`, `/ev-masters/`) are standard
`post` objects with category-based permalinks, so `/wp/v2/posts` is the right
endpoint.

```
mcp__wordpress__wp_call_endpoint(
  site="carlifebydani", endpoint="/wp/v2/posts", method="GET",
  params={"slug":"<slug>", "_fields":"id,slug,link,date,modified,title,excerpt,categories,tags,meta,featured_media"}
)
```

Record the **post id**, its **category**, and the **current values of the
three Yoast fields**. If `meta` comes back containing only `footnotes`, the
request was not authenticated — stop and fix that; do not conclude the fields
are unregistered (see `_shared/constants.md` traps).

If the slug returns nothing, try `/wp/v2/search?search=<words from the URL>` to
find the real post, and flag the URL as a possible orphan/404 (there are known
`clone-*` URLs earning impressions with no live post behind them — see
`docs/SEO_EV_NEWS_TODO.md` P2).

### Step 2 — Read the article twice: the DB and the live page

**Both, always.** They differ on this site.

a) **Post content from WP** — what Yoast and the indexer see as owned text:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="GET", params={"_fields":"content,excerpt,title"})
```

b) **The rendered page** — what a user and Googlebot actually get:
```bash
curl -s --max-time 20 -H 'User-Agent: Mozilla/5.0' "<url>" -o "$SCRATCH/page.html"
```
> ⚠️ **On EV-News episode pages the visible summaries are fetched from a remote
> CSV at render time** (`theme/single.php:110-134`) and are **not in
> `post_content`**. So the rendered page can look rich while the post the
> indexer sees is ~17 words. Always state which of the two you are describing.

c) **On EV News posts, also read the episode's news CSV directly** — don't scrape
the rendered cards instead: the theme renders only 6 of up to 12 columns, and
the CSV is already ordered editorially (see Decision rules below for how to
read it).

```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="GET", params={"context":"edit","_fields":"meta"})
```
Read `meta.news_csv` — a public Google Sheets CSV-export URL. Fetch it:
```bash
curl -sL --max-time 20 -H 'User-Agent: Mozilla/5.0' "<news_csv value>" -o "$SCRATCH/news.csv"
```
(needs `-L` — the publish URL 307-redirects — and a UA, or Google serves an
empty response.) Parse with Python's `csv` module, not manual splitting
(descriptions contain commas).

**Detect the width before reading anything positionally** — confirmed against
two real files 2026-08-14, and they differ:
| | Header | Cols |
|---|---|---|
| Back-catalogue (e.g. post 7333, 2025) | `title,description,link,author,upvote,downvote` | 6 |
| Current (e.g. post 9248, 2026) | `title,description,link,author,upvote,downvote,clicks,added_date,pub_date,off_topic,tags,region` | 12 |

So: **column 3 is `author`** (the source/reporter attribution, e.g. `thedriven.io`
or a Reddit handle) on every vintage checked — not an image URL, not a date.
This resolves Open Question 4: there is no image data in this CSV, W4's alt
text has to come from the media object itself, not here. Read the header row,
map by position up to whatever width is present, and treat every column past
`link` (index 2) as optional — `getattr`/`.get`-style access with a default,
never a bare index that throws on a 6-column file. `off_topic` is a plain
`yes`/`no` string when present; `tags` is a **free-text, comma-separated
candidate list** (e.g. `"Volkswagen, ID Polo, ID Cross, премиера, продажби"`)
— not existing WP tag IDs, and not what the plan doc calls `Тагове`/`Регион`
(the actual header names are English: `tags`, `region` — no separate topic
column was found in either sample file).

From the rendered HTML extract and record:
- `<title>`, `<meta name="description">`, canonical, `og:*`, `<html lang>`
- Heading outline (H1→H6) — note gaps (a known defect is H1 jumping straight to H5)
- Every `<img>`: `src`, `alt`, `title`, and whether `src` is empty (card thumbnails
  are JS-hotloaded on this theme, which is itself a finding)
- Internal and external links already present
- Yoast schema `wordCount` (in the JSON-LD blob) — the honest owned-text count

Then write a 3–5 line factual summary of what the article is actually about:
the entities (brands, models, people, places), the event, the numbers, the date.
**This entity list is the seed for Step 3** — do not invent keywords before it.
On EV News posts, the news CSV's story titles are additional entity seeds —
often richer than the 17-word `post_content` alone. Skip rows with
`off_topic: yes` when pulling seeds, where that column is present.

### Step 3 — Research what people actually search

Work outward from strongest evidence to weakest. Stop when the picture is clear;
don't run every source on every article.

#### 3a — Search Console: what this URL already earns (strongest signal)
Google is already showing this page for *something*. Start there.

```
mcp__google-search-console__gsc_query(
  site_url="https://www.carlifebydani.com/",
  date_from=<90 days ago>, date_to=<yesterday>,
  dimensions="query,page", row_limit=1000)
```
Filter the rows down to this URL (the tool result is large — save it and `jq`
rather than reading it inline; a 400+-row `query,page` pull will blow the token
limit). Record every query with impressions, CTR and average position.

This pull is free in money but expensive in tokens, and it's **identical for every
article optimized in the same week** — so cache it and reuse it across runs:
```bash
KEY="query-page|90d|<date_to>"
python3 tools/seo_cache.py raw get gsc "$KEY" > gsc.json   # exit 2 = go fetch
# ...after fetching: raw put gsc "$KEY" --file gsc.json
```

For a single URL, prefer calling the Search Console API directly with a `page`
`dimensionFilterGroups` filter instead of pulling the whole `query,page` table
— see `_shared/constants.md` traps for the exact call shape.

Read it as:
- **Position 5–20 with real impressions** → the phrase to build the title around.
- **Position ≤5 with weak CTR** → ranking is fine, the *snippet* is the problem;
  the meta description is the whole job.
- **Zero impressions** → nothing to preserve; you are choosing a target from
  scratch, and Steps 3b–3d carry the decision. This is common on EV News pages —
  roughly 100 of the 128 have zero visibility — so don't treat an empty pull as
  an error; fall straight through to autocomplete + SERP.

Use a 90-day window here (not 28) — single-article volumes are small and 28 days
of data on one URL is mostly noise.

#### 3b — Google autocomplete (free, Bulgarian, no auth — always run this)
Real query completions from Google, in-market. The cheapest good data available.

```bash
Q=$(python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "<seed phrase>")
curl -s --max-time 15 -H 'User-Agent: Mozilla/5.0' \
  "https://suggestqueries.google.com/complete/search?client=firefox&hl=bg&gl=bg&q=$Q"
```
Run it for each main entity from Step 2 (e.g. `тесла цена`, `зареждане на
електромобил`). Also run the alphabet-soup trick — append ` а`, ` б`, ` к`, ` ц`
to a seed — when you need long-tail variants. Completions are ordered by
popularity; the modifiers that recur across seeds (`цена`, `българия`, `2026`,
`втора ръка`, `обхват`) are what the market actually cares about.

Free, but not instant and highly reusable — cache each seed's completions under
the `autocomplete` namespace (30-day TTL) keyed by the seed phrase.

#### 3c — Paid keyword data: cache → DataForSEO → Semrush
Use paid sources to *price* the candidates from 3a/3b, never to discover from
zero. **Run `kw get` on the full candidate list first** and only buy the
`MISSING=` set.

**DataForSEO (preferred paid source — cheap, batches hard).** Probe it once with a
throwaway call; if it returns `40104` the account verification still hasn't
landed, so fall through to Semrush and note it in the proposal.
- `/v3/keywords_data/google_ads/search_volume/live` — **up to 1,000 keywords in
  one request**, `{"location_name":"Bulgaria","language_code":"bg"}`. This is the
  one that makes bulk pricing nearly free; send the whole candidate set at once.
- `/v3/dataforseo_labs/google/keyword_suggestions/live` and
  `/v3/dataforseo_labs/google/related_keywords/live` — expansion around a seed.
- `/v3/serp/google/organic/live/advanced` — a **real Bulgarian SERP** including
  People-Also-Ask and related searches. Far better than `WebSearch` for Step 3d.
- `/v3/dataforseo_labs/google/ranked_keywords/live` — everything a URL ranks for.

> Confirm request/response shapes with `mcp__dataforseo__docs_search` before the
> first call of a session rather than trusting a remembered schema — the task
> arrays and result nesting are easy to get wrong, and a malformed POST still
> costs a request.

**Semrush (fallback / what DataForSEO can't give).** Bills **10–50 units per
returned line**, so `display_limit` is the spend control:
- `phrase_these` — 10/line, semicolon-batched. Cheapest for a candidate set.
- `phrase_kdi` — 50/line, keyword difficulty. The main thing worth paying for.
- `phrase_related` — 40/line, semantic neighbours. Cap at `display_limit=20`.
- `phrase_questions` — 40/line, question queries → H2 subheadings.
- `phrase_organic` — 10/line, who currently ranks.

```
mcp__semrush__execute_report(report="phrase_these",
  params={"phrase":"<the MISSING= list, semicolon-joined>", "database":"bg"})
```

**Whatever you buy, bank it** — `kw put --source dataforseo|semrush` — before
moving on. That is what makes the next article cheaper.

> Expect thin or zero data for Bulgarian long-tail; Semrush's BG index is sparse
> (the site itself shows only ~26 tracked keywords). **Absent volume is not
> evidence of no demand** — autocomplete + GSC outrank it for this market.

#### 3d — Live SERP check (do this before committing to a keyphrase)
Fetch the actual Google result page for the top 1–2 candidate phrases and look at
what's ranking: is it news, forums, YouTube, dealer listings, Wikipedia? If the
SERP is entirely YouTube and marketplaces, an article page won't win it regardless
of the title. Check the intent match, the format Google is rewarding, and how
competitors phrase their titles.

Best source is DataForSEO `/v3/serp/google/organic/live/advanced` with
`location_name: "Bulgaria"`, `language_code: "bg"` — a genuine BG SERP with
People-Also-Ask boxes (those PAA questions are ready-made H2s). Cache it under
`serp` (14-day TTL) keyed `"<keyword>|bg"`.

Without DataForSEO: `WebSearch` is US-locale and only a rough proxy — inspect
competitor titles/descriptions with `WebFetch` on a known ranking URL instead, and
lean on 3a/3b for the Bulgarian SERP reality.

#### 3e — GA4: is the page worth the effort? (optional)
```
mcp__ga4__get_ga4_data(dimensions=["landingPage"], metrics=[...],
  date_range_start="90daysAgo", intent="engagement for one landing page")
```
Discover exact field names with `search_schema` first — never hand-type GA4
metric names. High impressions + high bounce means the *content* is the problem
and better metatags will only feed more people into a bad experience; say so in
the proposal instead of pretending the meta fix is sufficient.

### Step 4 — Choose the focus keyphrase

Exactly **one** focus keyphrase, plus 2–4 secondary phrases. Rules:
- It must be something the article genuinely satisfies. Ranking for a phrase the
  page doesn't answer produces a bounce, not a win.
- Prefer a phrase already at **position 5–20** in GSC over a bigger phrase at
  position 60. Distance to page-1 beats volume.
- Prefer Bulgarian informational phrasing. The site's tracked keywords are almost
  all bare brand/model names — the entire content gap is Bulgarian how-to,
  comparison and question phrasing.
- **Never target `EV новини` / `новини за електромобили` on an episode page** —
  that's the `/ev-news-feed/` hub's phrase and targeting it cannibalises the hub.
  Each episode owns its distinctive story.
- Check the phrase isn't already owned by another post on this site
  (`/wp/v2/search?search=<phrase>`). If it is, this is a cannibalisation decision,
  not a metatag decision — flag it and stop before writing.

### Step 4b — Map the research to existing tags

Tags come from the **same research as the keyphrase**, not from a fresh read of
whatever prose ends up on the page — a separate content-analysis pass over the
finished article would pick up whatever happens to be mentioned (a passing
brand name, a sponsor) rather than what the article's *demand* actually is.

Candidates are the entities from Step 2 and the keyphrase/secondary phrases
from Step 3–4: the headline story's brand(s) and model(s), plus one keyword-
intent term if the story fits an existing pattern — `Премиера` (launch),
`слух` (rumor/speculation), `Регистрация`, `Зареждане`, `Разход`, `Инцидент` —
this site already tags EV News posts this way (`Tesla`+`SpaceX`+`слух` on the
Илон Мъск/SpaceX merger story is the existing pattern to match, not invent). On
EV News posts on modern (12-column) CSVs, each story's `tags` column (Step 2c)
is a free-text comma-separated candidate list, and `region` is a locale hint
(`GLOBAL`, etc.) — additional candidates to check against the rule below, not
to adopt directly; the CSV's classification is a suggestion, not a validated
decision, and it's absent entirely on back-catalogue (6-column) files.

**Reuse only — do not create speculatively.** Check every candidate against
what's already there:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/tags",
  method="GET", params={"search":"<candidate>","_fields":"id,name,slug,count"})
```

**The golden rule: choose a tag that already exists *and* sits in the 3–10 use
band.** Existence alone is not enough — measured across all 365 tags on
2026-08-14, 73% sit at ≤2 uses (51% at exactly 1), and tagging into those makes
the same problem the `/tag/clbd/` finding already describes one post worse: a
thin taxonomy archive outranking real editorial content. The band has a
**ceiling** too, not just a floor — at ≥20 uses (`Tesla` 57, `Volvo` 24,
`Hyundai` 20) the tag is a saturated brand hub; one more post adds no
differentiating signal, so use it only if that brand is genuinely the
headline entity. 11–19 uses is acceptable but treat it as established rather
than reaching for it. The 3–10 band is the real topical cluster a post
meaningfully joins (`Model S` 10, `IONIQ 5` 10, `Разход` 7, `Премиера` 6,
`BYD` 5, `Supercharger` 5). If a needed concept has no existing term, or only
one at ≤2 uses, **skip it** rather than create or reuse a thin one — note the
gap in the report so a recurring gap can be batch-created deliberately later,
not one-off per article.

**The line that matters: owned prose vs. external cards, not "headline story
only."** An EV News episode has 1 headline story and 27–68 external news
cards; tagging for every card is the dilution to cap. But it is normal for
the 130–190 word intro (¶1–¶3, once Phase B has run) to substantively name
several entities — the headline story in ¶1–¶2, and 2–4 more stories in ¶3.
If an entity is a real word in that owned prose (not just a card link),
tag it: the theme's auto-linker only fires on text that's actually in
`post_content`, so an untagged entity that *is* named in the prose is a
missed, free internal link, and a tagged entity that's *only* in a card is a
tag nobody's page ever mentions. **Corrected 2026-08-14** on post 7333 — the
original wording here capped entity tags at 1–2 and dropped `Model S`,
`Model X`, `Ford`, `Supercharger` even though ¶3 names all four; that was
wrong. Concretely:
- Before Phase B runs (post_content still thin): tag only what's confirmed —
  usually just the headline story's 1–2 entities, since nothing else is
  written yet to check the "named in prose" test against.
- After Phase B runs, or on non-EV-News posts with real body text from day
  one: check **every** entity named in the actual prose against the reuse +
  band rule, not just the headline one. A post with 5–6 in-band entities
  genuinely covered in its own text can carry 5–6 entity tags; a post that
  only names 2 carries 2. The prose length is the natural cap — a 168-word
  intro cannot name 20 entities, so this self-limits without an arbitrary
  number.
- Keyword-intent tags stay capped at **0–2** — those describe the piece as a
  whole (`Премиера`, `слух`, `Регистрация`), not a per-mention count, so a
  fixed small cap is still right there.

Record the chosen tags with their existing `id` and `count` in the report.

### Step 5 — Write the research section, set Status

Append (or start) the report at
`reports/seo-metatags/<YYYY-MM-DD>-<post-id>-<short-slug>.md` using
[`_shared/report-template.md`](../_shared/report-template.md) § Phase A. Set
**`Status: researched`** and the **`Keyphrase:`** line at the top of the file
— Phase B reads that line to front-load ¶1.

This phase makes **no live writes**. Everything here is research and a report
— `seo-article-apply` (Phase C) is the only phase that touches Yoast fields or
`post_tag`.

---

## Decision rules

- **Presentation vs deserving.** Position ≤5 with weak CTR → a metatag problem,
  hand off to Phase C. Position >20 or zero impressions → a *content* problem;
  say plainly that metatags alone won't fix it.
- **Distance beats volume.** Position 5–20 with ≥100 impressions on a single URL
  is a better target than a high-volume phrase the page ranks 60th for.
- **CTR norms by position** (page 1): pos 1 ≈25–30%, pos 2–3 ≈10–15%, pos 4–5
  ≈5–8%. Materially below the norm for its rank = a snippet worth rewriting.
- **One keyphrase per page.** If two pages would target the same phrase, that's
  cannibalisation — resolve it first (consolidate or canonicalise).
- **Tags: reuse only, in the 3–10 use band, never invent speculatively.** See
  Step 4b.
- **Bulgarian, always.** Research in the phrasing autocomplete shows people
  using — not a translation of English SEO phrasing.
- **The news CSV never becomes a content source.** It informs which stories to
  cover and what to research; it is third-party derived, never quoted as fact.
  Row order is the only invariant across schema vintages — treat every column
  except title/description/link as optional, degrade cleanly when absent.

## Known traps

- **GSC `query,page` at high `row_limit` blows the token limit.** Save the tool
  result and `jq` it.
- **Semrush charges per returned line.** `display_limit` is the spend control, and
  `data/seo-cache/keywords.csv` is the thing that stops you paying twice.
- **DataForSEO is blocked server-side (`40104`), and it is not a credentials
  problem.** Fully diagnosed 2026-08-13 — don't re-debug this locally:
  - `/v3/appendix/user_data` returns `20000 Ok` with the correct login, so **auth
    is fine**. Do not read that as the API working — it is the *only* endpoint
    that answers.
  - Every other path returns HTTP 403 / `40104`, including **free reference data**
    (`/v3/serp/google/locations`) and the **free sandbox host**
    (`sandbox.dataforseo.com`), which needs no balance at all.
  - Balance is `$1` (the untouched signup trial credit), daily limit 1000. Funds
    are therefore not the gate, and the blocked sandbox proves it.
  - Not `40201` (the suspicious-activity/duplicate-trial block) — different code.

  Probe once per session with a real data call. Until it clears, Semrush +
  autocomplete carry Step 3c, and the report should say so.
- **Two reports for one post is a bug, not a feature.** Always check Step 0
  before creating a new file.
- **`news_csv` is not REST-exposed by default.** It's plain legacy postmeta;
  `theme/functions.php` registers it (`show_in_rest`, editor-gated
  `auth_callback`) as of 2026-08-14 — if a fetch returns nothing in `meta`,
  confirm that registration reached the live site (deploy is manual, see
  `docs/DEPLOYMENT.md`) before assuming the post has no CSV.
- **The publish CSV URL 307-redirects and Google serves empty without a
  User-Agent.** `curl -sL -H 'User-Agent: Mozilla/5.0'`, not a bare `curl -s`.
