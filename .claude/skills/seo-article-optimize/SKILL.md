---
name: seo-article-optimize
description: Optimize a single carlifebydani.com article for search — reads the live page + WP post, researches what people actually search (Search Console, Google autocomplete, Semrush BG, live SERP, GA4), then proposes a focus keyphrase, SEO title, meta description, on-page content edits, image alt text and internal links; writes a dated proposal MD to reports/seo-metatags/, backs up existing Yoast meta, and offers to apply the metatags via the WordPress MCP. Reads paid keyword data from the local cache in data/seo-cache/ before spending API units. Use when the user gives a URL and asks to "optimize this article", "write SEO metatags", "fix the meta description", or "do the SEO for this page".
---

# Article SEO Optimizer

**Goal: take one URL and turn it into a shipped, measurable on-page SEO
improvement.** Not "write a nice title" — match the article to phrases Bulgarians
actually type into Google, propose every on-page change that helps it rank and
earn the click, persist the whole thing as a reviewable artifact, then apply the
metatags with the user's approval.

The output is always **two things**:
1. A dated proposal document in `reports/seo-metatags/` — the durable backup, so
   the reasoning survives even if nothing gets applied.
2. An **applied** set of Yoast fields (after explicit user approval), plus a
   checklist of the changes that still need a human.

Companion skill: `seo-performance-report` (site-wide monthly snapshot). **That
skill finds the pages worth optimizing; this skill optimizes one of them.** The
backlog it feeds is `docs/SEO_EV_NEWS_TODO.md`.

---

## Site constants — do not re-derive these

| Thing | Value |
|---|---|
| WP MCP site alias (`site` param) | `carlifebydani` |
| WP REST base | `https://carlifebydani.com/wp-json` (canonical public host is **www**) |
| Writer account | `seo-bot` (id 28, **Editor**) — no `manage_options`, cannot touch site settings |
| Yoast meta keys | `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` |
| Title template suffix | `%%sep%% %%sitename%%` → renders ` - Car Life by Dani` (**19 chars**) |
| GSC property | `https://www.carlifebydani.com/` (www URL-prefix, **not** the apex) |
| Semrush database | `bg` (desktop; there is no `mobile-bg`) |
| GA4 property id | `427729375` |
| Site language | `bg-BG` — **verified fixed 2026-08-13** (`<html lang>`, `og:locale`, schema `inLanguage` all correct) |
| DataForSEO account | `trepechov@gmail.com` — **live, verified 2026-08-13** (`20000 Ok`; it returned `40104` earlier the same day, so activation lags). **Balance $1** — a pilot budget, not a crawl budget. Probe `/v3/appendix/user_data` (free) before planning a run around it (see Step 3c) |
| Local data cache | `data/seo-cache/` via `tools/seo_cache.py` — **check it before any paid call** |

---

## Cost discipline — the cache comes first

Semrush bills **per returned line** and DataForSEO **per request**. Optimizing one
article does not justify re-buying keyword data the project already owns, and most
articles here share the same entity vocabulary (Tesla, зареждане, BYD, обхват…),
so the overlap between runs is large.

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

### Step 0 — Check what's already banked

Run `kw search` on the article's main entities before anything else. If the
article is about Tesla pricing and the ledger already holds `тесла`, `тесла цена`
and `tesla model y`, most of Step 3c is already done.

### Step 1 — Resolve the URL to a WP post

Take the slug (last path segment) and look it up. All content types on this site
(`/ev-news/`, `/publications/`, `/ev-review/`) are standard `post` objects with
category-based permalinks, so `/wp/v2/posts` is the right endpoint.

```
mcp__wordpress__wp_call_endpoint(
  site="carlifebydani", endpoint="/wp/v2/posts", method="GET",
  params={"slug":"<slug>", "_fields":"id,slug,link,date,modified,title,excerpt,categories,tags,meta,featured_media"}
)
```

Record the **post id** and the **current values of the three Yoast fields**. If
`meta` comes back containing only `footnotes`, the request was not authenticated —
stop and fix that; do not conclude the fields are unregistered (see Traps).

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
> CSV at render time** (`theme/single.php:110-115`) and are **not in
> `post_content`**. So the rendered page can look rich while the post the
> indexer sees is ~17 words. Always state which of the two you are describing.

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

Read it as:
- **Position 5–20 with real impressions** → the phrase to build the title around.
- **Position ≤5 with weak CTR** → ranking is fine, the *snippet* is the problem;
  the meta description is the whole job.
- **Zero impressions** → nothing to preserve; you are choosing a target from
  scratch, and Steps 3b–3d carry the decision.

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

### Step 5 — Draft the metatags

**`_yoast_wpseo_title`** — write `"<title body> %%sep%% %%sitename%%"`. Template
variables render correctly; use them rather than hardcoding the brand.
- Body budget: **35–45 chars** (the suffix eats 19; hard cap ~60 total).
- Focus keyphrase in the **first 30 characters**.
- Strip the `#EV160` episode prefix — it wastes the highest-value characters of
  the SERP title. (Post 9248 is the reference implementation.)

**`_yoast_wpseo_metadesc`** — **140–155 characters.**
- Front-load the focus keyphrase; add the 2–3 concrete specifics that make the
  page worth clicking (a number, a model, a price, a date), and end on a light CTA.
- Reference: post 9248 — `Илон Мъск загатна за сливане между Tesla и SpaceX,
  Tesla пуска FSD V14 Lite за Hardware 3, а VW представи новия ID Cross. EV
  Новини #161 - виж всичко.` (149 chars.)
- Write it to be **true**. A description that oversells depresses dwell time.
- Yoast derives `og:description` from this field, so one write fixes both.

**`_yoast_wpseo_focuskw`** — the exact focus keyphrase, nothing else.

Always present **before → after** for all three, with character counts.

### Step 6 — Additional on-page proposals (the part that actually moves rankings)

Metatags change *presentation*. These change whether the page deserves to rank.
Propose concretely — exact text, exact location — not "add more keywords".

1. **H1 / title tag alignment** — H1 should carry the keyphrase in natural
   Bulgarian; it does not have to equal the SEO title.
2. **First 100 words** — the keyphrase should appear in the opening sentence of
   owned text. On EV-News pages this usually means *creating* owned text, since
   there is barely any (`wordCount: 17`).
3. **Body coverage** — 2–4 secondary phrases and the `phrase_questions` results
   turned into H2/H3 subheadings that the article then answers. Quote the exact
   sentences to add; never say "sprinkle keywords".
4. **Image alt text** — every `<img>` needs descriptive Bulgarian alt naming the
   subject; the featured image's alt should include the keyphrase. Alt lives on
   the media object, not the post:
   ```
   mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/media/<id>",
     method="GET", params={"_fields":"id,alt_text,title,source_url"})
   ```
   Editor role can `POST` `{"alt_text": "..."}` back — **verify on one image
   before proposing a batch.**
5. **Excerpt** — `post_excerpt` is the single biggest content lever on thin pages
   (a 100–150 word Bulgarian intro). Propose the actual text.
6. **Structured data / slug** — only if genuinely wrong. **Never propose a slug
   change on a URL with GSC impressions** unless a 301 ships with it.

### Step 7 — Internal linking (both directions)

WordPress's own search is the discovery tool — it ranks by relevance across all
posts:
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/search",
  method="GET", params={"search":"<keyphrase or entity>","per_page":10,
                        "_fields":"id,title,url,subtype"})
```
Run it for the focus keyphrase and for each main entity, then propose:
- **Inbound links (higher value)** — 2–4 *existing, already-ranking* posts that
  should link **to** this article. Give the source post, the anchor text (a
  natural Bulgarian phrase containing the keyphrase — never "тук" / "виж тук"),
  and roughly where in that post it belongs. Inbound internal links from pages
  with existing authority are the strongest on-site lever available here.
- **Outbound links** — 2–3 related posts this article should link **out** to,
  same detail level. Prefer the deep evergreen `/publications/` and `/ev-review/`
  pages over other news episodes.
- Skip `/tag/` pages as link targets — thin taxonomy pages already outrank real
  editorial content on this site, and linking to them makes it worse.

External links on this site are already correctly `rel="nofollow" target="_blank"`
— leave them alone.

### Step 8 — Write the proposal document

`reports/seo-metatags/<YYYY-MM-DD>-<post-id>-<short-slug>.md`, using the template
below. Write this **before** touching anything live — it is the backup the user
asked for, and it must stand on its own if the writes never happen.

### Step 9 — Back up, then ask before writing

**a) Back up first — always.** WordPress revisions do **not** cover postmeta, so
an overwritten meta description is unrecoverable.
```
reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv
```
Header: `id,slug,link,_yoast_wpseo_title,_yoast_wpseo_metadesc,_yoast_wpseo_focuskw`
with the **current** (pre-write) values, even when they're all empty.

**b) Ask.** Show the before → after for the three fields and ask the user to
approve applying them via the WordPress MCP. Use `AskUserQuestion`: apply all
three / apply metadesc only / revise first / don't write. **Never write without
an explicit yes** — this is production content.

**c) Apply** (only the approved fields):
```
mcp__wordpress__wp_call_endpoint(site="carlifebydani", endpoint="/wp/v2/posts/<id>",
  method="POST", params={"meta":{
    "_yoast_wpseo_title":"...", "_yoast_wpseo_metadesc":"...",
    "_yoast_wpseo_focuskw":"..."}})
```

**d) Be explicit about what is NOT auto-appliable.** Body text, headings,
excerpt, alt text and internal links are separate, riskier writes. Offer them
one at a time with the same approve-first rule; never bundle them into the
metatag write.

### Step 10 — Verify and hand off

1. Re-fetch the rendered page with `curl` and confirm `<title>`,
   `<meta name="description">` and `og:description` changed. Yoast caches — if
   the old values persist, say so rather than assuming success.
2. Tick the row in `docs/SEO_EV_NEWS_TODO.md` if the post is listed there.
3. Tell the user the measurement plan: **re-check this URL's CTR and position in
   GSC in 2–4 weeks** (Google needs to re-crawl, then GSC needs to accumulate).
   The `seo-performance-report` skill picks it up on the next monthly run.

---

## Proposal document template

```markdown
# SEO Optimization — <article title>

**URL:** <url> · **Post ID:** <id> · **Prepared:** <YYYY-MM-DD>
**Status:** proposed | metatags applied <date> | fully applied <date>

## What this article is about
<3–5 lines: entities, event, numbers, date. Owned word count (Yoast `wordCount`)
vs what renders on the page.>

## Current state
| | Value | Length |
|---|---|---|
| `<title>` | | |
| `<meta name=description>` | | |
| Focus keyphrase | | |
| H1 | | |
| Owned word count | | |
| Images without alt | | |
| Internal links out / in | | |

## Demand research
**GSC (90d, this URL):**
| Query | Impr | Clicks | CTR | Pos |
|---|---|---|---|---|

**Google autocomplete (hl=bg, gl=bg):** <seeds → completions; recurring modifiers>
**Keyword metrics (bg):** <phrase / volume / KD / competition — mark each row
`cached <date>` or `fresh <source>`, and say what was newly bought this run>
**SERP check:** <what ranks for the target phrase, and what format Google rewards>
**GA4:** <sessions / engagement / bounce for this landing page, if pulled>

## Recommendation
**Focus keyphrase:** `<phrase>` — <why: position, impressions, intent match, and
what it is *not* competing with on this site>
**Secondary:** `<a>`, `<b>`, `<c>`

## Proposed metatags
| Field | Before | After | Chars |
|---|---|---|---|
| `_yoast_wpseo_title` | | | |
| `_yoast_wpseo_metadesc` | | | |
| `_yoast_wpseo_focuskw` | | | |

## On-page changes (need a human)
- [ ] **H1** — <exact proposed text>
- [ ] **Opening paragraph** — <exact proposed text>
- [ ] **Subheadings** — <exact H2/H3s, from question queries>
- [ ] **Excerpt** — <exact 100–150 word text>
- [ ] **Image alt** — <media id → exact alt text>

## Internal links
**Inbound — existing posts that should link here:**
| Source post | URL | Anchor text | Where |
|---|---|---|---|

**Outbound — this article should link to:**
| Target post | URL | Anchor text | Where |
|---|---|---|---|

## Risks / notes
<cannibalisation, slug-change warnings, thin-content caveats, whether better
metatags alone can plausibly move this page>

## Measurement
Baseline (GSC, <window>): <impr / clicks / CTR / pos>. Re-check after 2–4 weeks.
```

---

## Decision rules

- **Presentation vs deserving.** Position ≤5 with weak CTR → a metatag problem,
  fix the snippet. Position >20 or zero impressions → a *content* problem; say
  plainly that metatags alone won't fix it and put the weight on Steps 6–7.
- **Distance beats volume.** Position 5–20 with ≥100 impressions on a single URL
  is a better target than a high-volume phrase the page ranks 60th for.
- **CTR norms by position** (page 1): pos 1 ≈25–30%, pos 2–3 ≈10–15%, pos 4–5
  ≈5–8%. Materially below the norm for its rank = a snippet worth rewriting.
- **One keyphrase per page.** If two pages would target the same phrase, that's
  cannibalisation — resolve it first (consolidate or canonicalise).
- **Never invent facts** to fill a meta description. Everything in the snippet
  must be in the article.
- **Bulgarian, always.** Titles and descriptions in Bulgarian, in the phrasing
  autocomplete shows people using — not a translation of English SEO phrasing.
- **Don't propose slug changes** on URLs with impressions unless a 301 ships too.

## Known traps (each of these has already cost real time)

- **Unauthenticated WP reads hide the Yoast fields.** `meta` comes back as just
  `['footnotes']` and it looks exactly like the fields aren't registered. They
  are, and they're writable by `seo-bot` — this was verified end-to-end with a
  live write test on 2026-08-13.
- **The WAF 403s Python's default `urllib` User-Agent** even with valid auth.
  Identical requests via `curl` succeed. A 403 here is not a credentials problem —
  use `curl`, or set a browser UA.
- **`post_content` ≠ what renders** on EV-News pages (remote CSV at render time).
  Never audit one and report on the other.
- **Postmeta has no revisions.** Back up to CSV before every write. No exceptions.
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
  autocomplete carry Step 3c, and the proposal should say so.
- **`seo-bot` cannot change site settings** (`manage_options` = false). Anything
  in WP Settings needs the user in wp-admin.
- **Teardown:** when the SEO push ends, remove the standing production write
  credential — `claude mcp remove wordpress`, delete the Keychain entry
  `carlifebydani-wp-mcp`, revoke the app password in the `seo-bot` profile.
