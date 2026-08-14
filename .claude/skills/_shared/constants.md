# Shared SEO constants and site-wide traps

Read this file at the start of any of the three SEO phase skills
(`seo-keyphrase-research`, `ev-news-transcript-content`, `seo-article-apply`) or
their orchestrator (`seo-article-optimize`). It is not a skill on its own — it
has no frontmatter and is never invoked directly — it exists so the four skills
that touch this site's WordPress don't each carry a slightly-drifted copy of the
same table.

> This file resolves Open Question 1 from
> [`docs/SEO_SKILLS_REFACTOR.md`](../../../docs/SEO_SKILLS_REFACTOR.md#6-open-questions):
> a skill's `SKILL.md` is loaded as its own document, but a markdown link to a
> path outside it (like the ones on this line, or the ones each phase skill
> carries in its own "Read this first" step) resolves the same way any other
> in-conversation file reference does — the agent executing the skill opens it
> with `Read` when the instructions say to. There is no special skill-inclusion
> mechanism, and none is needed: a plain link plus an explicit read-this-first
> instruction is enough, and it's exactly what `ev-news-transcript-content`
> already did before this file existed (linking into `seo-article-optimize`'s
> table by anchor). **The convention going forward:** each phase skill's own
> `SKILL.md` states near the top, as an instruction rather than a footnote,
> "Read `_shared/constants.md` before Step 1" — don't rely on the link being
> noticed in passing.

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
| DataForSEO account | `trepechov@gmail.com` — **still blocked as of 2026-08-13 21:2x** (`40104` on a real `search_volume/live` call). **`/v3/appendix/user_data` returning `20000 Ok` is NOT proof it works** — that endpoint answers on gated accounts by design; it is the only one that does. Probe with a **data** endpoint, never `user_data`. Balance $1. See traps below |
| Local data cache | `data/seo-cache/` via `tools/seo_cache.py` — **check it before any paid call** |
| Reports directory | `reports/seo-metatags/<YYYY-MM-DD>-<post-id>-<short-slug>.md` — one file per post, all phases append to it (see [`report-template.md`](report-template.md)) |
| Yoast postmeta backup | `reports/yoast-meta-backup/<id>-<YYYY-MM-DD>.csv` — postmeta has no WP revisions, this is the only recovery path |

---

## Site-wide known traps (apply to every phase)

- **Unauthenticated WP reads hide the Yoast fields.** `meta` comes back as just
  `['footnotes']` and it looks exactly like the fields aren't registered. They
  are, and they're writable by `seo-bot` — verified end-to-end with a live
  write test on 2026-08-13.
- **The WAF 403s Python's default `urllib` User-Agent** even with valid auth.
  Identical requests via `curl` succeed. A 403 here is not a credentials
  problem — use `curl`, or set a browser UA.
- **`post_content` ≠ what renders** on EV-News pages — the visible news-card
  summaries are fetched from a remote CSV at render time
  (`theme/single.php:110-134`), not stored in `post_content`. Never audit one
  and report on the other.
- **Postmeta has no revisions.** Back up to CSV before every Yoast-field write.
  No exceptions.
- **`POST /wp/v2/posts/<id>` with `tags` replaces the whole tag set, not
  merges.** Always read the post's current `tags` first and carry them forward
  unless deliberately dropping one.
- **`POST /wp/v2/posts/<id>` with `content` replaces, it does not append.**
  Always send the full block (existing embed/paragraphs + new content), or
  earlier content — including a video embed — gets wiped.
- **The theme auto-links tag names inside `the_content`, up to 1× per tag**
  ([`theme/functions.php:75`](../../../theme/functions.php#L75),
  `add_tag_links_to_content` — lowered from 5× to 1× 2026-08-14, see
  `docs/SEO_SKILLS_REFACTOR.md` §W7). Still worth checking: once body prose
  exists, re-fetch the rendered page and count `/tag/` links inside it before
  calling a tagging or content pass finished.
- **`post_excerpt` does not move Yoast's `wordCount`.** Proven on post 7333,
  2026-08-14: 154 words written to the excerpt left `wordCount` at 17; the same
  words in `post_content` moved it to 168. Yoast reads `post_content` only.
  **Never write to `post_excerpt`** — everything generated by these skills
  goes into `post_content`.
- **`seo-bot` cannot change site settings** (`manage_options` = false).
  Anything in WP Settings needs the user in wp-admin.
- **Category / tag archive SEO is a different write path entirely.** Yoast
  stores taxonomy SEO in the `wpseo_taxonomy_meta` **option**, not in term
  meta — `GET /wp/v2/categories/<id>` returns `meta: []` and there is **no
  REST way** to set a term's SEO title or meta description; it needs
  `manage_options`. The term `description` field is *not* a workaround
  (verified live on Yoast v28.2, 2026-08-13): it renders and populates
  `og:description`, but does **not** emit `<meta name="description">`. Hand
  the user the exact values to paste into wp-admin instead.
- **The GSC MCP dumps whole result sets into context.** For a single URL, call
  the Search Console API directly with a `page` `dimensionFilterGroups` filter
  instead of pulling `query,page` at high `row_limit` and filtering client-side
  — vastly cheaper, and gives per-query rows for exactly that page. See
  `seo-keyphrase-research` Step 3a for the exact call shape.
- **Teardown:** when a sustained SEO push ends, remove the standing production
  write credential — `claude mcp remove wordpress`, delete the Keychain entry
  `carlifebydani-wp-mcp`, revoke the app password in the `seo-bot` profile.
