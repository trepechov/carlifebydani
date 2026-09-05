---
title: On-site SEO performance fix plan (GTmetrix/Lighthouse regression)
type: fix
date: 2026-09-05
---

# On-site SEO performance fix plan

## Summary

The 2026-09-05 monthly SEO snapshot (`reports/seo-performance/2026-09-05-snapshot.md`)
shows PSI Lighthouse **lab** Performance scores dropping hard on both form factors —
mobile 60 → **43** (−17), desktop 71 → **59** (−12), TBT roughly doubling on each — which
is the "scores around fifty, used to be around sixty" the user flagged. GTmetrix's own
score is flatter (70 → 65 → 65 → 66 → 65 over five snapshots, grade D throughout) but its
**Top Lighthouse Issues list has been identical, in the same order, since at least
2026-08-13**: nothing has actually been fixed, the site has just been sitting at a
mediocre baseline. Real-user field data (CrUX) is unaffected and still passes CWV on both
form factors, so this is a technical/structural problem, not a ranking emergency — but it
is a real, actionable backlog that has gone untouched for at least two monthly cycles.

This plan turns that backlog into a scoped, deployable fix list. It is being written on
`feature/on-site-seo` because that's the only branch with the SEO reporting toolchain
(GTmetrix/PSI/GSC MCPs, `reports/seo-performance/`) needed to diagnose it — but this
branch itself is research/tooling only and must not be deployed. The actual code changes
below belong on a fresh branch off `main` (see **Branch plan** at the end).

## Source data

- `reports/seo-performance/2026-09-05-snapshot.md` (this run) and `history.csv` (5-run trend)
- GTmetrix report `7ksv4vEw` — https://gtmetrix.com/reports/carlifebydani.com/7ksv4vEw/ (2026-09-05 01:20 UTC, unthrottled, Chrome/Seattle)
- PSI MCP Lighthouse pulls, mobile + desktop, captured 2026-09-05
- CrUX field data via PSI REST `runPagespeed`, 28-day window ending 2026-09-04

## Problem frame

**Two separate signals, don't conflate them:**

1. **Lab scores (PSI Lighthouse) regressed sharply this run** — mobile Performance
   43, desktop 59, both down double digits, TBT +280 ms (mobile) / +190 ms (desktop),
   mobile Speed Index reverted to 8.8 s (was 4.0 s last run). The snapshot report flags
   this as a "watch item, not a confirmed regression" because (a) it's a single-sample
   throttled run on a shared node — this doc's own history shows swings before — and
   (b) field data the same day is unaffected. **But** it's the first time both form
   factors moved together by a double-digit margin, which is less typical of pure
   noise. Action: re-run PSI lab once (cheap, already have the MCP) before writing this
   off, then treat persistence as confirmation.

2. **GTmetrix's Top 5 Lighthouse issues are a real, unaddressed backlog** — same 5
   issues, same order, for at least the last two monthly snapshots (2026-08-13 →
   2026-09-05). This is not noise; nothing has shipped against any of them. This is
   the actionable part of the plan.

| # | Issue | Impact | Current value |
|---|---|---|---|
| 1 | Reduce initial server response time | 🔴 Critical | Root document TTFB 1.5–1.6 s |
| 2 | Avoid chaining critical requests | 🟠 Moderate | 23 critical request chains |
| 3 | Preload the LCP image | 🟠 Moderate | 1.4 s of available savings |
| 4 | Avoid enormous network payload | 🟠 Moderate | 3.07 MB total; JS 1.19 MB transferred / 4.2 MB uncompressed (20 files) + fonts 1.1 MB (6 files) = **75% of total payload** |
| 5 | Images missing explicit `width`/`height` | 🟠 Moderate | 3 image elements |

Both Performance and Structure GTmetrix sub-scores (55% / 79%) and the resource mix
(page size, request count) have been essentially flat for the same window — consistent
with the last 65 commits being content/metadata via the WordPress REST API
(`seo-article-optimize` sprint), not theme or asset changes. **This backlog predates
that sprint and isn't caused by it.**

## Scope decision

**In scope for this plan:** front-end/server performance fixes (theme templates,
`functions.php` asset enqueuing, image markup, server/caching config) that address the
GTmetrix Top-5 list above.

**Out of scope, tracked elsewhere, do not duplicate here:**
- Per-post keyphrase/content/metatag work (`toyota-bz4x`, `христо бъчваров`
  cannibalisation, `mhero`, `mg zs мнения`, `microlino`, etc.) — these are already
  tracked in `docs/SEO_SITE_TODO.md` / `docs/SEO_EV_NEWS_TODO.md` and the
  `reports/seo-optimizations/` ledger, and go through the `seo-article-optimize`
  skill via the WordPress REST API. They need no branch or deploy.
- `page-sitemap.xml`'s 1 persistent GSC warning — no URL identified by the API across
  five snapshots; needs a manual look in the GSC UI, not a code fix.

## Prioritized fixes

### P0 — Reduce Time to First Byte (Critical, 1.5–1.6s)

TTFB this slow on a WordPress origin almost always means uncached PHP execution per
request (no object cache / page cache, or a cache that's misconfigured/bypassed) or an
undersized/overloaded host tier. Before touching code:
- Confirm what's actually caching today (host-level? a caching plugin? none?) — this
  repo's `docs/DEPLOYMENT.md` should say what the current hosting/caching setup is;
  verify it against reality since GTmetrix shows no evidence of cache hits.
- If a page cache exists but isn't hitting on the homepage, find out why (logged-in
  bypass, no-cache headers, cookie-based bypass from `cookieconsent`/`ev-news-voting`
  cookies making every request look "logged in").
- If there's no page cache at all, this is the single highest-leverage fix on the
  list — it improves every metric below it for free (chains, LCP, TTI all start
  counting from TTFB).

### P1 — Trim JS + font payload (1.19 MB JS + 1.1 MB fonts = 75% of 3.07 MB)

- Audit the 20 JS files and 6 font files actually being transferred (GTmetrix HAR:
  `https://gtmetrix.com/reports/carlifebydani.com/7ksv4vEw/net.har`) — `functions.php`'s
  own `wp_enqueue_script` calls only account for ~7 files (`gtag`, `glightbox` +
  `glightbox-init`, `cookieconsent` + `cookieconsent-init`, `ev-news-tracking`,
  `ev-news-voting`, `ogimageloader-init`); the rest are plugin- or core-injected —
  identify and eliminate/defer what isn't needed on every page load.
  - `gtag.js` and `ev-news-tracking`/`ev-news-voting` are analytics — good candidates
    for `defer`/`async` if not already, since they don't block first paint content.
  - `glightbox` only matters on pages with a lightbox gallery — consider conditional
    enqueue (only on post types/templates that use it) rather than site-wide.
- Fonts at 1.1 MB across 6 files is heavy for a Bulgarian/Latin site — check for
  unused weights/subsets, missing `font-display: swap`, and whether self-hosted fonts
  are pre-compressed (woff2, not woff/ttf).

### P2 — Preload the LCP image (1.4s available savings)

- Identify the current LCP element per template (`front-page.php`,
  `template-parts/header.php`, `single.php` — likely a hero/featured image).
  Add a `<link rel="preload" as="image" href="...">` for it in `<head>`, matching
  the actual rendered size/format (avoid preloading an image that then gets replaced
  by a responsive `srcset` pick — preload the same URL the browser will actually
  choose).

### P3 — Reduce critical request chains (23 chains) + missing image dimensions (3 images)

- 23 chains is high; usually caused by CSS `@import`s, render-blocking stylesheets
  loading fonts/background-images, or JS that loads more JS. Pull the Lighthouse JSON
  (`https://gtmetrix.com/reports/carlifebydani.com/7ksv4vEw/lighthouse.json`) for the
  actual chain list rather than guessing further.
- The 3 images without explicit `width`/`height` are a CLS risk (desktop CLS's NI
  share has been this report's other standing watch item) — same Lighthouse JSON
  names them; add explicit dimensions (or `aspect-ratio` CSS) at the markup source,
  not just in the theme's image-size registration.

## Carry-forward: already-committed theme fixes on `feature/on-site-seo`

Two clean, isolated commits on this branch already touch `theme/functions.php` and are
real fixes worth shipping — they were never deployed because this branch mixes them
with 65 commits of reports/tooling that shouldn't go to `main`. Cherry-pick both onto
the new fix branch:

- `a0fcd10` — `fix(seo): stop the tag auto-linker flooding posts with duplicate /tag/ links (W7 partial)`
- `d5141b8` — `feat(seo): read the episode news CSV directly, verified against live data (W3, W11)`

Neither is a performance fix, but both are small, reviewed-in-spirit, deployable
changes sitting idle on the wrong branch — bundling them with this work avoids a
second round-trip.

## Verification plan

- Before/after: re-run `mcp__gtmetrix__gtmetrix_start_test` and
  `mcp__psi__psi_lighthouse` (mobile + desktop) directly against the fix branch's
  staging/preview if one exists, otherwise against production immediately after
  deploy.
- The existing `seo-performance-report` skill already tracks GTmetrix score, PSI lab
  scores, and CrUX field data run-over-run in `reports/seo-performance/history.csv` —
  the next monthly snapshot (due ~2026-10-05) is the formal verification point; don't
  need new tooling for this.
- Success criteria: GTmetrix Top-5 issues list changes (items resolved drop off or
  shrink in impact) and/or GTmetrix score moves off the 65–70/D plateau; PSI lab
  Performance returns to or exceeds the pre-regression baseline (mobile ~60, desktop
  ~71) on a second/third sample, not just one; CrUX field data continues passing
  (it's not currently at risk, just don't regress it).

## Branch plan

1. This plan is committed on `feature/on-site-seo` (research/tooling branch — stays
   here, not deployed).
2. Switch to `main`, create a new branch (e.g. `fix/onsite-seo-performance`) off it.
3. Cherry-pick `a0fcd10` and `d5141b8` (see above) onto the new branch.
4. Implement P0–P3 above on the new branch.
5. Open for review; merge to `main` and deploy through the normal (manual) deploy
   process once approved — per `docs/DEPLOYMENT.md`.
6. Content/keyphrase action items from `docs/SEO_SITE_TODO.md` /
   `docs/SEO_EV_NEWS_TODO.md` continue independently through `seo-article-optimize`
   against the WordPress REST API — they don't need this branch or a deploy at all.
