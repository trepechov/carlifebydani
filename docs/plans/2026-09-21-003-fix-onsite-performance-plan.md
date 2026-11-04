---
title: On-site performance fix plan, round 2 (GTmetrix mobile E-grade + recovered orphaned fixes)
type: fix
date: 2026-09-21
---

# On-site performance fix plan, round 2

## Summary

The GTmetrix page added on 2026-09-15 (`odTFqgLq`, weekly-monitored, Chrome/Seattle,
**iPhone 15/16 simulated**) produced its first report — score **57 (grade E)**,
Performance **40%**, Structure 82%. This is the "bad report" that prompted this plan. It
is not directly comparable to the desktop baseline this project has tracked since July
(`3k5t7XjE`, no device simulation, last at 65/D on 2026-09-04) — it's a new, stricter,
mobile-emulated test — but the underlying Top-5 Lighthouse issues are the same kind of
problem this repo already worked on once, in `docs/plans/2026-09-05-002-fix-onsite-seo-performance-plan.md`
(merged as PR #36, `f426d18`).

**Two things came out of digging into this:**

1. **PR #36 (Sep 5) worked** — the preload-LCP and missing-image-dimensions issues it
   fixed are gone from the Top-5 list, and critical request chains dropped 23 → 15. But
   TTFB (P0 of that plan) was never actually addressed — it was scoped as "confirm
   what's caching today" and nobody followed up. It's still the #1 Critical issue.
2. **A second, older, entirely unmerged branch — `seo-on-site-optimizations` (last
   commit 2026-07-12, 6 commits) — already contains fixes for two of today's other live
   issues** (the font-loading chain, and the icon font being unsubsetted), plus a
   documented landmine (don't defer jQuery — it broke Ninja Forms) that the current
   `main` respects only by accident. This branch predates the Sep 5 plan; whoever wrote
   that plan didn't know it existed. See **Recovered orphaned fixes** below.

## Source data

- GTmetrix report `8AzXWOno` — https://gtmetrix.com/reports/carlifebydani.com/8AzXWOno/
  (2026-09-15 14:04 UTC, Chrome/Seattle, iPhone 15/16 simulated, unthrottled) — the new
  weekly-monitored mobile page, 1st and only data point so far
- Lighthouse JSON for that report (downloaded and inspected directly for audit detail
  beyond GTmetrix's own Top-5 summary: `render-blocking-resources`, `unused-javascript`,
  `third-party-summary`, `modern-image-formats`, `dom-size`)
- `reports/seo-performance/history.csv` — 5-run desktop trend, GTmetrix flat at 65–70/D
  since July
- `docs/plans/2026-09-05-002-fix-onsite-seo-performance-plan.md` and its outcome
  (`f426d18`, merged to `main`)
- `git log --all`, diffed against `main`/`HEAD` — found the unmerged
  `seo-on-site-optimizations` branch
- Direct `curl -D-` against `https://www.carlifebydani.com/` — confirmed
  `cf-cache-status: DYNAMIC` on the homepage (see P0)

## Current Top-5 (GTmetrix/Lighthouse, report `8AzXWOno`)

| # | Issue | Impact | Value |
|---|---|---|---|
| 1 | Reduce initial server response time | 🔴 Critical | Root document TTFB 1,540–1,686 ms |
| 2 | Avoid chaining critical requests | 🟠 Major | 15 chains (was 23 pre-PR#36) |
| 3 | Avoid long main-thread tasks | 🟡 Moderate | 11 long tasks, 2.4s main-thread work |
| 4 | Avoid enormous network payload | 🟡 Moderate | 3.23 MB total |
| 5 | Avoid an excessive DOM size | 🟡 Moderate | 864 elements |

Dropped off the list since Sep 5 (PR #36 fixed these): preload-the-LCP-image,
missing image `width`/`height`. **Not fixed, not even attempted:** TTFB.

Digging past GTmetrix's Top-5 into the full Lighthouse JSON surfaces the same root
causes in more actionable form:

- **`render-blocking-resources`** (1,800 ms potential savings): jQuery core (1,880 ms),
  jQuery-migrate (1,485 ms), `gtag.js` (1,485 ms — deliberately left blocking, see PR
  #36), `cookieconsent.min.css` (1,485 ms), `glightbox.min.css` (1,485 ms),
  `style.min.css` (1,090 ms), Sofia Sans Google Fonts CSS (775 ms), Material Symbols
  Google Fonts CSS (157 ms).
- **`third-party-summary`** (380 ms total main-thread blocking from third parties):
  Google Tag Manager 222 ms / 326 KB, **"Google CDN" 117 ms / 756 KB**, sibforms.com
  (Brevo newsletter) 42 ms / 128 KB, **Google Fonts 0 ms blocking but 1.155 MB
  transfer** — the single largest resource bucket on the page, bigger than all JS
  combined.
- **`unused-javascript`** (461 KB potential savings): reCAPTCHA (`recaptcha__en.js`,
  355 KB, 53% unused), GTM/gtag (193 KB, 51% unused), **sibforms `main.js` (118 KB, 80%
  unused)**, `gtm.js` (132 KB, 52% unused).
- **`modern-image-formats`** (437 KB potential savings, not in the Top-5 but real):
  three uploaded JPEGs — `bbq-event-2026-sep-scaled.jpg`, `evn-165-scaled.jpg`,
  `evmasters-ebag-scaled.jpg` — would save 100–200 KB each as WebP/AVIF.

## Root cause: no HTML edge caching (confirms P0)

```
$ curl -sD- -o /dev/null https://www.carlifebydani.com/
server: cloudflare
cf-cache-status: DYNAMIC
```

The site sits behind Cloudflare, but the homepage HTML is explicitly **not** cached at
the edge (`DYNAMIC` = passthrough to origin every request). Every visitor pays full
WordPress PHP execution + DB queries. This is exactly the TTFB root cause the Sep 5 plan
flagged as an open question ("confirm what's actually caching today") and never
resolved. It's an infra/dashboard fix, not a theme code change — can proceed in
parallel with everything else below, doesn't block a PR.

## Recovered orphaned fixes (`seo-on-site-optimizations` branch, 2026-07-10/12, never merged)

`git log --all` surfaces 6 commits on a branch that shares no history with `main` past
the monorepo restructure. They predate PR #36 and were apparently never noticed by it.
Two are directly relevant to today's live issues; one is a landmine to avoid repeating;
one is a zero-cost bonus SEO fix. **None can be cherry-picked cleanly** — `main` has
diverged (different defer mechanism, different `featured-image.php` signature) — so
these are ported as *ideas*, re-implemented fresh against current `main`, not merged as
diffs.

| Commit | What it did | Status today |
|---|---|---|
| `1fe7338` | Killed the CSS `@import` for Google Fonts (serial request chain: fetch `style.min.css` → parse → discover import → fetch `fonts.googleapis.com` → fetch `fonts.gstatic.com`), replaced with `wp_enqueue_style()` + `wp_resource_hints` preconnects | **Still broken today** — `theme/css/style.css:1-2` still has the raw `@import url(...)` for both fonts. This is exactly what `render-blocking-resources` flags today (775 ms + 157 ms) and a likely contributor to the 15 remaining critical request chains. |
| `4007126` | Scoped the Material Symbols icon font request to `icon_names=...` for the ~10 icons actually used, cutting it from the full ~980 KB glyph set to ~4 KB | **Still broken today** — current `@import` has no `icon_names` param. This is almost certainly *why* "Google Fonts" is a 1.155 MB transfer bucket in today's `third-party-summary` — bigger than every JS file on the page combined. Single biggest fixable win on this whole list. |
| `c47f935` | **Reverted** an earlier attempt (`d8b5c7c`) to defer jQuery core/`jquery-migrate`, because Ninja Forms expects the jQuery global synchronously and broke | **A constraint, not a bug** — today's `render-blocking-resources` still flags jQuery core (1,880 ms) and jQuery-migrate (1,485 ms) as the two single biggest render-blocking savings on the page. **Do not defer them** — this was already tried in July and caused a real regression on any page with a Ninja Forms embed. Leave as-is. |
| `d8b5c7c` | Made `recaptcha/api.js` load `async`, plus old-style defer (superseded by PR #36's `strategy=>defer`) | The defer part is superseded and doesn't need porting. **The reCAPTCHA `async` part was never carried forward** — `theme/template-parts/front-page/newsletter.php:33` still loads it as a plain blocking `<script src>` with no `async`/`defer`. |
| `0e9ae0b` | Fixed an inaccurate `sizes` attribute causing full-resolution images to ship on high-DPR mobile | Today's `uses-responsive-images` audit passes (score 1) — worth a quick look but may be moot now; low priority either way. |
| `aa6cab8` + `c47f935` | Added a visually-hidden `<h1>` to the homepage (`front-page.php` had none — its highest-authority URL had no top-level heading) | Not a performance issue, but zero-cost to pick up alongside this work since it's already written. Bonus, not required. |

## Prioritized fixes

### P0 — Enable HTML edge caching (Critical, TTFB 1.5–1.7s)

Not a theme code change — infra/Cloudflare dashboard task, can run in parallel with the
PR below.

- Check whether Cloudflare **APO (Automatic Platform Optimization) for WordPress** is
  available on the current plan — it's built for exactly this stack (WP + Cloudflare)
  and handles cache invalidation on publish automatically.
- If APO isn't available/affordable, add a Cloudflare **Cache Rule** caching HTML
  responses (even a short 60–300s edge TTL removes the "every visitor re-executes PHP"
  problem for the vast majority of traffic) — need a purge-on-publish hook (a WP
  Cloudflare plugin, or a small `save_post` webhook) so edits go live promptly.
- Fallback/complement: verify whether the host offers a server-side page cache
  (object cache, OPcache, LiteSpeed cache if that's the stack) — `docs/DEPLOYMENT.md`
  doesn't mention any caching layer today, and this confirms none is active.
- This is the single highest-leverage item on the list — every other metric below
  (chains, LCP, TTI, long tasks) starts its clock after TTFB.

### P1 — Fix the font-loading chain + kill the icon font (biggest fixable payload win) — ✅ DONE (2026-09-21)

`seo-on-site-optimizations` (`1fe7338`, `4007126`) already diagnosed both problems —
serial `@import` chain for Sofia Sans, unscoped Material Symbols family — but on
revisiting this with the brand constraint (Sofia Sans stays; the icons don't have to
be a *font* at all) a stronger fix is available for each than the July branch used.
Both go further than a cherry-pick, so re-implement fresh against current `main`.

**Sofia Sans — self-host instead of scoping the Google Fonts request.** OFL-licensed,
so pulling the woff2 files onto our own origin is legitimate and identical visually —
no identity change, just no third-party round-trip:

- Download the 4 variants actually declared today (400/700, normal/italic) and add
  them under `theme/fonts/`.
- Register with `wp_enqueue_style()` in `functions.php` pointing at the local files,
  with `font-display: swap` in the `@font-face` rule.
- Drop the `fonts.googleapis.com` `@import` for Sofia Sans from
  `theme/css/style.css:1` entirely — no preconnect needed since there's no longer an
  external origin to connect to for this font.
- This alone removes the 775 ms Sofia Sans render-blocking line from
  `render-blocking-resources` and one full origin from `third-party-summary`.

**Icons — replace the Material Symbols web font with an inline SVG sprite**, since the
icons themselves aren't the brand constraint and Material Symbols is Apache-2.0 (the
SVGs are the literal same glyphs, not an approximation). Exact usage, confirmed by
grep across `theme/template-parts/` — 11 glyphs, 8 files:
`favorite`, `search`, `close`, `menu`, `keyboard_arrow_up`, `keyboard_arrow_down`,
`check`, `fiber_new`, `thumb_up`, `thumb_down`, `newspaper`.

- Pull each glyph's SVG from the Material Symbols source (outlined style,
  `opsz24 wght200 FILL1 GRAD200` to match today's `@import` params) into one sprite
  partial (e.g. `theme/template-parts/icons-sprite.php`) using `<symbol id="icon-…">`
  defs, included once near the top of `theme/footer.php` (or `header.php`) as inline
  markup so the sprite has zero network cost.
- In each of the 8 template files, swap
  `<span class="material-symbols-outlined ...">glyph</span>` for
  `<svg class="..."><use href="#icon-glyph"/></svg>`, carrying over the existing
  Tailwind sizing/color classes — `currentColor` on the SVG path picks up
  `text-brand-red` etc. the same way the font glyph did.
- Delete the Material Symbols `@import` from `theme/css/style.css:2` — no icon font
  request left at all, beating even the ~4 KB scoped `icon_names=` result the July
  branch measured (~980 KB → 0 KB network, vs → ~4 KB).
- Watch for size/vertical-alignment drift per call site — several spans use
  inline `font-size` overrides (e.g. `card-article-external.php:41/45`,
  `ev-news-feed/card.php:58/67/74` at 9–10px) that need an equivalent `width`/`height`
  on the `<svg>`.

**Do not touch jQuery core/`jquery-migrate` defer status** — `c47f935` already tried
this in July and broke Ninja Forms. Leave `render-blocking-resources`' jQuery lines
(1,880 ms + 1,485 ms) as a known, accepted cost.

While in `functions.php`, note the live site is currently serving theme `?ver=7.1`
(per today's HAR) but the repo's `theme/style.css` says `Version: 1.1.3` — the
deployed build and repo have drifted (manual-deploy process, per
`docs/DEPLOYMENT.md`). Worth a sanity check before/after deploying this fix so the
before/after comparison isn't confused by an unrelated version mismatch.

**Implementation notes:**

- Sofia Sans: 8 unique woff2 files (not 16) — cyrillic/cyrillic-ext/latin/latin-ext
  × normal/italic, in `theme/fonts/sofia-sans/`. Weights 400 and 700 share one file
  per subset/style (`font-weight: 400 700`) because Google serves Sofia Sans as a
  variable font — the css2 response returns the identical URL for both discrete
  weight requests. Registered via a new `theme/css/fonts.css` (`wp_enqueue_style`
  in `functions.php`, loaded before `theme-css`), not folded into the Tailwind
  `style.css` pipeline. Dropped the `greek`/`vietnamese` subsets Google served —
  not needed for a Bulgarian-content site.
- Icons: 11-glyph inline SVG sprite (`theme/template-parts/icons-sprite.php`,
  included once right after `<body>` in `header.php`), sourced from the same
  `wght200fill1` Material Symbols asset Google's own font would have rendered — a
  new `.icon` class (`theme/css/style.css`) gives each `<svg>` `width/height: 1em`
  + `fill: currentColor` so the existing `text-*xl` / `text-brand-*` Tailwind
  classes still control size/color unchanged. All 15 purely-decorative icons got
  `aria-hidden="true"`; the one informational one (`fiber_new`, "Нова статия") got
  an `<svg><title>` instead, which is strictly better a11y than the plain-text
  `title=""` attribute it replaced.
- Found and fixed two *additional* icon references the original plan didn't know
  about: `theme/css/ninja-form.css`'s `.nf-error-field-errors:after` /
  `.nf-response-msg:after` used raw Material Symbols codepoints (`\e645`, `\e5ca`)
  that would have silently gone blank once the font import was removed. Replaced
  with `mask-image` (`background-color: currentColor`) pointing at base64-inlined
  SVGs of the same two glyphs (`priority_high`, `check`) — zero extra requests,
  same visual result.
- `theme/package.json`'s `copyFiles` list (used by `npm run pack` per
  `docs/DEPLOYMENT.md`) didn't know about a `fonts/` directory — added `fonts/**`.
  Verified with a real `npm run pack` run that all 8 woff2 files, `fonts.css`, and
  `icons-sprite.php` land in the deploy zip.
- **Not verified in a browser** — this repo has no local dev/staging environment
  (confirmed via `docs/DEPLOYMENT.md`, manual-deploy-only). Spot-check after
  deploy: icon vertical alignment (`.icon`'s `vertical-align: -0.2em` is a
  best-effort baseline match, not a measured one) and Cyrillic body text rendering
  in Sofia Sans (not just Latin — this is the untested part with the highest
  blast radius if wrong).

### P2 — Disable the Brevo newsletter block + reCAPTCHA (homepage-only, single line) — ✅ DONE (2026-09-21)

**Investigation:** the Brevo newsletter isn't a plugin — it's raw HTML/JS hand-pasted
into [`theme/template-parts/front-page/newsletter.php`](../../theme/template-parts/front-page/newsletter.php):
a `<form>` posting straight to a `sibforms.com` endpoint (lines 11–16) plus Brevo's
`main.js` (line 32). reCAPTCHA (the invisible `g-recaptcha` div, line 26, and
`recaptcha/api.js`, line 33) exists solely to protect this one form — it's not shared
with Ninja Forms or used anywhere else in the theme. The whole partial is included from
exactly one place: [`theme/front-page.php:21`](../../theme/front-page.php#L21). So both
issues collapse to one template, only reachable from the homepage.

`third-party-summary` attributes 117 ms blocking / 756 KB to the recaptcha/gstatic
bucket and 42 ms / 128 KB to sibforms.com; `unused-javascript` shows 53% and 80% of
those scripts respectively going unused on a typical page load.

**Decision:** rather than lazy-loading (deferring the cost) or deleting (losing the
markup), turn the section off at the single include site and leave the partial file
untouched, so it's a one-line change to restore later:

```php
// theme/front-page.php:21
// Disabled 2026-09 for performance (P2, docs/plans/2026-09-21-003-fix-onsite-performance-plan.md):
// unconditional sibforms.com + recaptcha loads cost 117ms/756KB + 42ms/128KB blocking
// on every homepage view. Uncomment to restore the newsletter signup + reCAPTCHA.
// get_template_part('template-parts/front-page/newsletter');
```

Implemented as-is — the four lines above are the actual diff in `front-page.php:21-24`.

- Single line commented out in `front-page.php`; `newsletter.php` itself is left
  completely as-is (no partial edits inside it to reconcile later).
- Fully removes the form, both scripts, the DOM nodes, and the inline `AUTOHIDE` script
  from the rendered homepage — not deferred, not lazy, just off — so
  `third-party-summary`'s recaptcha/gstatic and sibforms.com buckets should disappear
  entirely from the next report, not merely shrink.
- Restoring later means removing product functionality (no newsletter signup capture)
  until/unless replaced — flag this as a product tradeoff to confirm with whoever owns
  the newsletter list before merging, not a purely technical call.

### P3 — Convert the 3 heaviest content photos to WebP/AVIF (437 KB potential)

`bbq-event-2026-sep-scaled.jpg`, `evn-165-scaled.jpg`, `evmasters-ebag-scaled.jpg` are
uploaded content images, not theme assets — handle through the media pipeline, not a
theme code change:

- Check whether WP Smush (already active per the Sep 5 plan's lazy-load findings) has
  WebP/AVIF conversion enabled — turn it on there first.
- If Smush's plan doesn't cover it, WP core 6.5+'s `image_editor_output_format` filter
  can auto-generate WebP/AVIF alongside the original JPEG with no plugin.
- Lower priority than P0–P2: capped, per-image value, and doesn't need a theme deploy
  at all.

### P4 — Defer, don't act yet: DOM size (864 elements) and long main-thread tasks (11)

Both read as downstream symptoms of P1/P2 rather than independent problems — third-party
script execution drives most of the 2.4s main-thread work, and the homepage's
card-grid markup drives DOM size. Re-measure after P0–P2 land before considering any
markup/DOM restructuring, which would need real template surgery for comparatively
small further gain.

### Bonus, zero-cost — homepage `<h1>` (not performance, from the same orphaned branch) — ✅ DONE (2026-09-21)

`front-page.php` had no `<h1>` at all — the hero section starts at `<h2>`, leaving the
site's highest-authority URL without a top-level heading. `aa6cab8` + `c47f935` already
wrote a visually-hidden fix (site name/tagline from Settings → General, so it stays
editable without a deploy). Cheap to re-implement alongside this work; not required for
the performance goal.

Re-implemented as a visually-hidden (`sr-only`) `<h1>` inserted in `front-page.php`
right after the header include, pulling `bloginfo('name')` / `get_bloginfo('description')`
so it stays editable from Settings → General without a deploy. Scoped to `front-page.php`
only (not the shared `header.php`, which every other template already gives its own
visible `<h1>`), so no visual change and no impact on other templates.

## Verification plan

- Before/after: re-run `mcp__gtmetrix__gtmetrix_start_test` against the new mobile
  page's exact settings (iPhone 15/16, Seattle, unthrottled) immediately after deploy,
  and again once Cloudflare caching has had time to warm.
- Success criteria against today's baseline (report `8AzXWOno`): TTFB drops well below
  1s once P0 lands; the "Google Fonts" third-party bucket drops from 1.155 MB toward
  ~50 KB once P1 lands; the recaptcha/gstatic and sibforms.com buckets drop out of `third-party-summary`
  entirely once P2 lands (the newsletter section is off, not deferred); GTmetrix
  score/grade moves off 57/E.
- The existing `seo-performance-report` skill already tracks GTmetrix score run-over-run
  in `reports/seo-performance/history.csv` — but note that CSV currently tracks the
  *desktop, unthrottled* page (`3k5t7XjE`), not the new mobile one (`odTFqgLq`). Decide
  whether to add the mobile page as a second tracked row or switch tracking to it,
  since mobile is the stricter and arguably more representative signal now that both
  exist.
- CrUX field data (real users) has passed cleanly on both form factors for months per
  `history.csv` — this plan is about lab/structural debt, not a live ranking emergency;
  don't let deploy urgency override the normal review process.

## Branch plan

1. This plan is committed on `feature/on-site-seo` (research/tooling branch — stays
   here, matching the Sep 5 plan's own convention).
2. Once approved, switch to `main`, create a new branch (e.g.
   `fix/onsite-performance-round-2`) off it.
3. Implement P1 (font chain + icon subsetting) and P2 (lazy reCAPTCHA/newsletter
   script) as the code changes — both self-contained, both grounded in real
   measurements above. Treat the recovered `seo-on-site-optimizations` commits as
   reference diffs to reimplement, not to merge directly.
4. P0 (Cloudflare/caching) and P3 (image format conversion) are dashboard/plugin
   config, not this PR — track them as follow-up tasks against whoever holds
   Cloudflare/hosting access, but don't block the code PR on them.
5. Optionally fold in the homepage `<h1>` bonus fix if it's not worth a separate PR.
6. Open for review; merge to `main` and deploy through the existing manual process
   (`docs/DEPLOYMENT.md`) once approved.
7. Re-run GTmetrix per the verification plan above; the next monthly
   `seo-performance-report` snapshot is the formal checkpoint either way.
