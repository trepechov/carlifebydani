# Cloudflare handoff: enable HTML edge caching for carlifebydani.com

**Status:** Open — needs Cloudflare dashboard access, not a code change
**Owner needed:** whoever administers the Cloudflare account/zone for carlifebydani.com
**Raised by:** on-site performance audit, 2026-09-21 (`docs/plans/2026-09-21-003-fix-onsite-performance-plan.md`, section "P0")
**Repo:** this repo (`carlifebydani`) — if you have Claude Code, you can `cd` into it and ask Claude to dig into any of the background below; see "Using Claude Code on this" at the bottom.

---

## The problem, in one line

The homepage HTML is not being cached at Cloudflare's edge. Every single visitor triggers a full WordPress PHP execution + database round-trip at origin. This is the #1 flagged performance issue on the site right now.

## Evidence

```
$ curl -sD- -o /dev/null https://www.carlifebydani.com/
server: cloudflare
cf-cache-status: DYNAMIC
```

`cf-cache-status: DYNAMIC` means Cloudflare is passing every request straight through to origin — no edge cache hit is possible for this response type today.

This lines up with GTmetrix/Lighthouse data from report `8AzXWOno` (2026-09-15, mobile-simulated, iPhone 15/16, Seattle):

| Metric | Value |
|---|---|
| Root document TTFB | 1,540–1,686 ms |
| GTmetrix grade | 57 / E |
| Overall Performance score | 40% |

TTFB is the single highest-leverage metric on the whole report — every other timing metric (LCP, TTI, chained requests) starts its clock only after TTFB resolves. Fixing this one thing moves more than any of the theme-code fixes already shipped (font-loading chain, icon font, disabling unused third-party scripts — all done 2026-09-21, see the plan doc).

## What's been ruled out

I checked `theme/functions.php` and the rest of the theme for any existing cache-control logic (headers, `Cache-Control`, `Expires`, any Cloudflare-specific code) — there is none. The theme is not fighting a cache, doing something that would break one, or emitting no-cache headers on purpose. This is a pure infrastructure gap, not a code conflict. Nothing in the app needs to change to turn caching on.

There's also no caching plugin installed in WordPress today that we're aware of (no `W3TC`/`WP Rocket`/`LiteSpeed Cache`/Cloudflare plugin found in the repo's plugin references) — `docs/DEPLOYMENT.md` doesn't mention a caching layer at all, which matches what the `DYNAMIC` header shows.

## What to do, in priority order

### 1. Check whether Cloudflare APO (Automatic Platform Optimization) for WordPress is available on our plan

This is the purpose-built solution for exactly this stack (WordPress + Cloudflare). It caches full HTML pages at the edge and auto-purges on publish/update via the official WordPress plugin — no custom purge logic to build or maintain.

- Dashboard → **Speed → Optimization → Automatic Platform Optimization**
- Usually a paid add-on (~$5/mo) unless already bundled into the current plan
- If available, this is the preferred fix — do this instead of #2, not in addition to it

### 2. If APO isn't available or isn't worth the cost, add a Cache Rule

- Dashboard → **Caching → Cache Rules** → create rule
- **Match:** hostname is `www.carlifebydani.com` (and the apex domain too, if it also serves live traffic) — but exclude `/wp-admin/*`, `/wp-login.php`, `/wp-json/*`, and any request carrying a preview/`wp_logout` query param
- **Cache eligibility:** Cache Everything, for the matched HTML responses
- **Edge TTL:** start conservative, 60–300 seconds. Even a short TTL eliminates "every visitor re-executes PHP" for the vast majority of traffic — this site isn't highly personalized/dynamic per-visitor
- **Must bypass cache on the WordPress logged-in cookie** (`wordpress_logged_in_*`). Without this, editors/admins browsing while logged in will see cached pages and think their edits aren't saving. This is standard practice for any WP + Cloudflare cache setup.
- **Purge-on-publish is not optional.** Without it, published edits won't go live until the edge TTL naturally expires. Two ways to get it:
  - Install the official **Cloudflare** WordPress plugin, connect it with an API token scoped to this zone (needs Zone → Cache Purge permission), enable "Automatic Cache Purge" — it hooks WordPress's `save_post` action automatically. This is the low-effort path and should be tried first.
  - Or, if the plugin route isn't viable for some reason, a small custom `save_post` webhook that calls Cloudflare's Purge Cache API for the affected URL. This would need a few lines added to the theme (`functions.php`) or a small custom plugin — flag it back to the dev side if it comes to this, since it's the one path that touches code.

### 3. While you're in there — check for server-side caching at the hosting layer too

Independent of Cloudflare: does the host have an object cache, OPcache, or something like LiteSpeed Cache active? `docs/DEPLOYMENT.md` doesn't reference any caching layer, which suggests probably not. Not a blocker for #1/#2, just worth confirming since it stacks with edge caching rather than replacing it.

## How to verify it worked

```
curl -sD- -o /dev/null https://www.carlifebydani.com/
```

Look for `cf-cache-status: HIT` (it may show `MISS` or briefly `DYNAMIC` right after a purge, then flip to `HIT` on the next request within the TTL window). Once that's confirmed, ping the dev side back — a GTmetrix re-run against the monitored page (`odTFqgLq`, weekly-monitored mobile) will confirm the TTFB drop.

## Scope note

This is purely a Cloudflare-dashboard / WordPress-plugin task. It does not require touching the theme repo, does not need a code deploy, and can happen fully in parallel with any pending theme PRs — nothing here is blocked on the other, and nothing else is blocked on this either (though it's the single biggest win available on the whole performance list).

---

## Using Claude Code on this

If you have Claude Code available and want to go deeper than this doc (e.g. verify the "no caching plugin/code" claim yourself, check exact file paths, or re-derive the GTmetrix numbers), you can run it directly against this repo:

```
cd carlifebydani   # wherever this repo is checked out
claude
```

Useful things to ask it:
- `"Read docs/plans/2026-09-21-003-fix-onsite-performance-plan.md and summarize the P0 section and the evidence behind it"`
- `"grep the theme for any Cloudflare, cache-control, or Expires header logic — confirm nothing there would conflict with edge caching"`
- `"Show me docs/DEPLOYMENT.md so I understand the hosting/deploy setup before I touch Cloudflare"`

The full performance audit — including the other fixes already shipped and the ones still pending (P1–P4) — lives at `docs/plans/2026-09-21-003-fix-onsite-performance-plan.md` in this repo, on branch `feature/on-site-seo`.
