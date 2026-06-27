# PWA Push Badges — Daily Article Count on iOS Home Screen

## What this does

When a user adds the EV News Feed page to their iPhone home screen (iOS 16.4+), the home screen icon shows a red badge number reflecting how many new articles were collected today. Tapping the icon opens the feed and the badge clears.

This works via Web Push: after every daily collection run, the plugin sends a silent push to all subscribed devices. The service worker wakes up, fetches today's count from the REST API, sets the badge, and shows a notification.

---

## Architecture

```
ENA_Cron::run_pipeline()
    → ENA_Sync::run()           returns published_today count
    → ENA_Push::send_all(N)     VAPID HTTP POST to each stored endpoint

iOS Push Delivery
    → sw.js: push event fires
    → fetch /wp-json/carlifebydani/v1/daily-count
    → self.registration.setAppBadge(N)   ← red badge on icon
    → self.registration.showNotification(…)

User taps icon → app opens
    → pwa-init.js: navigator.clearAppBadge()   ← badge clears
```

---

## Key files

| File | Purpose |
|---|---|
| `includes/class-ena-pwa.php` | Registers all PWA hooks: manifest + SW rewrites, `wp_head` tags, script enqueue, REST endpoint |
| `includes/class-ena-push.php` | VAPID key generation + storage, push subscription management, Web Push delivery |
| `assets/sw.js` | Service worker: handles push event, sets badge, shows notification, clears badge on tap |
| `assets/pwa-init.js` | Client JS: registers SW, requests notification permission, saves subscription, clears badge on open |

The theme has **zero PWA awareness**. All logic lives in the plugin.

---

## URL endpoints served by the plugin

| URL | Content-Type | Purpose |
|---|---|---|
| `/sw.js` | `application/javascript` | Service worker (served from `assets/sw.js` via WP rewrite) |
| `/manifest.json` | `application/manifest+json` | Web app manifest (generated dynamically by `ENA_PWA`) |
| `/wp-json/carlifebydani/v1/daily-count` | `application/json` | Today's article count for the SW to badge with |

Both `/sw.js` and `/manifest.json` are served via WordPress rewrite rules registered in `ENA_PWA::register_rewrites()`.

---

## New `wp_options` keys

| Key | What's stored |
|---|---|
| `ena_vapid_keys` | P-256 ECDH key pair (PEM) + base64url-encoded public key. Auto-generated on first use. |
| `ena_push_subscriptions` | JSON array of push subscription objects `{endpoint, keys:{p256dh, auth}}`. One entry per subscribed device. |

---

## VAPID implementation

No Composer dependency. VAPID JWT signing (ES256) is implemented in `class-ena-push.php` using PHP's built-in OpenSSL:

1. `generate_keys()` — creates a P-256 EC key pair via `openssl_pkey_new`
2. `build_vapid_jwt()` — builds and signs the JWT with `openssl_sign(…, OPENSSL_ALGO_SHA256)`
3. `der_to_p1363()` — converts the DER-encoded ECDSA signature to IEEE P1363 (`r‖s`) required by JWT ES256
4. `send_all()` — POSTs to each subscription endpoint with `Authorization: vapid t={jwt},k={pubkey}`; removes stale endpoints (HTTP 404/410)

Push messages carry **no payload** — the service worker fetches the count from the REST endpoint after waking. This avoids the payload encryption complexity (RFC 8291 / AES-128-GCM).

---

## One-time setup after deploy

1. **Flush rewrite rules** — visit any WP Admin page once after activating/updating the plugin. The `after_switch_theme` hook does this automatically on theme switches; for plugin updates trigger it manually via **Settings → Permalinks → Save**.

2. **Verify SW is reachable** — open `https://carlifebydani.com/sw.js` in a browser; it should return JavaScript, not a 404.

3. **Verify manifest** — open `https://carlifebydani.com/manifest.json`; it should return JSON with `Content-Type: application/manifest+json`.

4. **Subscribe from iPhone** — on iOS 16.4+, open `/ev-news-feed/` in Safari → Share → Add to Home Screen. Then open the icon and grant notification permission when prompted by `pwa-init.js`.

5. **Test the badge** — trigger a manual collection from the plugin admin dashboard. After the sync completes, `ENA_Push::send_all()` fires automatically. The badge should appear on the home screen icon within seconds.

---

## iOS requirements

| Requirement | Detail |
|---|---|
| iOS version | 16.4 or later |
| Install method | Must be added via Safari → Share → Add to Home Screen (not just bookmarked) |
| Notification permission | User must grant permission when prompted on first open |
| Browser | Safari only (Chrome/Firefox on iOS do not support Web Push for PWAs) |

---

## Stale subscription cleanup

`ENA_Push::send_all()` removes any subscription endpoint that returns HTTP 404 or 410 (device unsubscribed or app removed from home screen). Cleanup happens automatically on each push send — no manual maintenance required.
