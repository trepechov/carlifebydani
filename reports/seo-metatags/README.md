# Article SEO Optimization Proposals — carlifebydani.com

One file per optimized article: what the page is about, what the demand research
showed, the proposed focus keyphrase / SEO title / meta description, and the
on-page and internal-linking changes that need a human.

These are **generated artifacts and the durable backup of the reasoning** — they
stand on their own whether or not the metatags were ever applied. Each file
records its own status (`proposed` / `metatags applied <date>` / `fully applied`).

## Source

Produced by the [`seo-article-optimize`](../../.claude/skills/seo-article-optimize/SKILL.md)
skill — ask Claude to **"optimize this article for SEO"** with a URL, or
`/seo-article-optimize <url>`.

Data pulled per run: Search Console (90-day query→page for the URL), Google
autocomplete (`hl=bg&gl=bg`), keyword metrics (DataForSEO / Semrush BG), a live
SERP check, optionally GA4 landing-page engagement, plus the WordPress REST API
for the post itself.

Paid keyword data is looked up in [`data/seo-cache/`](../../data/seo-cache/)
before any API call and written back after, so the same phrase is never bought
twice. Each proposal marks its keyword rows as cached or freshly pulled.

## Files

- `YYYY-MM-DD-<post-id>-<short-slug>.md` — one proposal per article.

## Before/after Yoast values

Pre-write snapshots of the three Yoast fields live separately in
[`reports/yoast-meta-backup/`](../yoast-meta-backup/) as
`<post-id>-<YYYY-MM-DD>.csv`. WordPress revisions do **not** cover postmeta, so
that CSV is the only way back after a write.

## Related

- **Backlog of what to optimize next:** [`docs/SEO_EV_NEWS_TODO.md`](../../docs/SEO_EV_NEWS_TODO.md)
- **Root-cause diagnosis:** [`docs/SEO_EV_NEWS_PROPOSALS.md`](../../docs/SEO_EV_NEWS_PROPOSALS.md)
- **Monthly site-wide snapshots:** [`reports/seo-performance/`](../seo-performance/)
- **Competitive landscape:** [`reports/competitor-gap/`](../competitor-gap/)
