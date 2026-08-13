# SEO data cache

**Check here before calling a paid API.** Semrush charges per returned line and
DataForSEO per request; most of what a single article needs has usually been
bought already for another article.

Driven by [`tools/seo_cache.py`](../../tools/seo_cache.py) and used by the
[`seo-article-optimize`](../../.claude/skills/seo-article-optimize/SKILL.md) skill.

## What's stored

| Store | Path | Committed | Staleness |
|---|---|---|---|
| Keyword metrics ledger | `keywords.csv` | **yes** | 90 days |
| Verbatim API responses | `raw/<namespace>/` | no (gitignored) | per namespace |

**`keywords.csv`** — one upserted row per `(keyword, database)`:
`keyword,database,volume,cpc,competition,kd,source,fetched`. Search volume is a
12-month rolling average, so a 90-day-old row is as good as a fresh call. It is
committed deliberately: it's small, greppable, and `git log -p keywords.csv`
gives you volume history for nothing.

**`raw/`** — full responses keyed by a caller-supplied string, with a `fetched`
stamp. Default TTLs: `serp` 14d · `autocomplete` 30d · `gsc` 7d · `ga4` 7d ·
`labs` 30d · anything else 14d. Gitignored — bulky and regenerable.

## Usage

```bash
# 1. What do we already know? Exits 2 and prints MISSING= if a call is needed.
python3 tools/seo_cache.py kw get "тесла цена" "зареждане на електромобил"

# 2. Buy only the misses, then bank the result (TSV: keyword,volume,cpc,competition,kd)
python3 tools/seo_cache.py kw put --source semrush <<'TSV'
тесла цена	720	0.14	0.05	42
TSV

# Fuzzy: "do we know anything about X at all?"
python3 tools/seo_cache.py kw search "зарядна"

# Raw responses — `get` exits 2 on miss/stale, so it drives an if-then in the shell
python3 tools/seo_cache.py raw get serp "зареждане на електромобил|bg" > serp.json
python3 tools/seo_cache.py raw put serp "зареждане на електромобил|bg" --file resp.json

python3 tools/seo_cache.py stats
```

## Provenance of the seed data

The initial 109 rows were extracted on 2026-08-13 from
[`reports/competitor-gap/2026-08-04-semrush-competitor-gap.md`](../../reports/competitor-gap/2026-08-04-semrush-competitor-gap.md)
— Semrush BG volumes already paid for in that pull, dated `2026-08-04` so the TTL
math stays honest. They carry volume only; CPC/competition/KD are blank and will
fill in as articles get optimized.

## Rules

- **Never hand-write a number into `keywords.csv`.** Every row must come from an
  API response or a report that recorded one. A fabricated volume is worse than a
  missing one — a miss triggers a real call, a wrong row silently corrupts a
  decision. Use `--source manual` only for values transcribed from a real pull.
- Keywords are normalized (lowercased, whitespace collapsed) for lookup.
- `--database` defaults to `bg`; pass it explicitly for any other market.
