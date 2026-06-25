# EV News Feed — Migration & Enhancement Plan

## Why This Document Exists

The EV News Feed is currently split across two separate codebases — the **EV News Automator plugin** and the **carlifebydani theme** — with tight, implicit coupling between them:

- `theme/page-ev-news-feed.php` reads `ev_news_live_articles` directly (hardcoded option key)
- `theme/template-parts/ev-news-feed/card.php` assumes a specific article array shape
- The OG image AJAX proxy (`fetch_og_image_proxy`) lives in the theme's `functions.php`
- Feed JS files (`ev-news-tracking.js`, `ogimageloader.init.js`) are globally enqueued by the theme

**Risk:** Any plugin refactor (option key rename, article schema change) silently breaks the theme. Swapping the theme loses the feed entirely.

This document describes how to decouple them using the **WooCommerce template override pattern**, and also specifies a new **"Boost recent articles" admin setting**.

---

## Target Architecture

The plugin owns all default rendering. The theme may optionally override individual templates by placing files in a conventional folder.

```
plugins/ev-news-automator/
  templates/
    feed.php              ← default feed body layout
    parts/
      card.php            ← default article card
  assets/
    ev-news-feed.css      ← feed-specific styles (extracted from theme)
    ev-news-feed.js       ← GA4 click tracking (moved from theme)
    ogimageloader.js      ← OG thumbnail loader (moved from theme)
  includes/
    class-ena-frontend.php  ← enqueues, AJAX proxy, evna_get_template() helper

theme/
  ev-news-automator/       ← theme overrides (optional, matches plugin templates/ tree)
    feed.php
    parts/
      card.php
  page-ev-news-feed.php   ← slimmed down: calls get_template_part for header/footer,
                             then evna_get_template('feed.php') for the body
```

**Plugin public API surface:**

| Hook | Type | Purpose |
|------|------|---------|
| `evna_live_articles` | filter | Modify the articles array before rendering |
| `evna_before_feed` | action | Inject markup before the article list |
| `evna_after_feed` | action | Inject markup after the article list |
| `evna_get_template( $name, $args )` | function | Load theme override or plugin default template |

---

## Part 1 — Plugin-Theme Decoupling

### Migration Checklist

Work through these in order. Each step is backwards-compatible with the current theme until the final two "Theme side" steps.

#### Plugin side — build the public API

- [ ] **Create `includes/class-ena-frontend.php`** containing:
  - `evna_get_template( string $name, array $args = [] ): void`  
    Checks `get_stylesheet_directory() . '/ev-news-automator/' . $name` first; falls back to `ENA_PLUGIN_DIR . 'templates/' . $name`. Extracts `$args` into local variables before including.
  - `enqueue_frontend_assets(): void`  
    Hooked to `wp_enqueue_scripts`. Enqueues `ev-news-feed.js` and `ogimageloader.js` (with `wp_localize_script` for AJAX URL + nonce) and `ev-news-feed.css` (after the theme stylesheet). Conditionally load only on the EV news feed page — use `is_page_template( 'page-ev-news-feed.php' )` or check the global `$post` template.
  - `ajax_og_image_proxy(): void`  
    Exact copy of `fetch_og_image_proxy()` from `theme/functions.php`. Registers on `wp_ajax_fetch_og_image` and `wp_ajax_nopriv_fetch_og_image`.

- [ ] **Register `ENA_Frontend` in `includes/class-ena-plugin.php`**  
  After the existing hook registrations in `ENA_Plugin::__construct()`:
  ```php
  $frontend = new ENA_Frontend( $this->http );
  add_action( 'wp_enqueue_scripts',          [ $frontend, 'enqueue_frontend_assets' ] );
  add_action( 'wp_ajax_fetch_og_image',      [ $frontend, 'ajax_og_image_proxy' ] );
  add_action( 'wp_ajax_nopriv_fetch_og_image', [ $frontend, 'ajax_og_image_proxy' ] );
  ```
  `ENA_Frontend` needs `ENA_HTTP` for the `is_safe_url` SSRF check used in the proxy.

- [ ] **Create `templates/feed.php`** (plugin default feed body)  
  Copy lines 12–76 of `theme/page-ev-news-feed.php` (from `$raw = get_option(...)` through the closing `</div>`). Replace:
  ```php
  // old
  get_template_part( 'template-parts/ev-news-feed/card', null, [...] );
  // new
  evna_get_template( 'parts/card.php', [...] );
  ```
  Add filter and action hooks:
  ```php
  $articles = apply_filters( 'evna_live_articles', $articles );
  // ...
  do_action( 'evna_before_feed', $articles );
  // ... article loop ...
  do_action( 'evna_after_feed', $articles );
  ```

- [ ] **Create `templates/parts/card.php`** (plugin default card)  
  Copy `theme/template-parts/ev-news-feed/card.php` verbatim. This is the plugin's canonical default; the theme copy remains as an override until it is cleaned up.

- [ ] **Move JS assets**  
  - `theme/js/ev-news-tracking.js` → `assets/ev-news-feed.js`
  - `theme/js/ogimageloader.init.js` → `assets/ogimageloader.js`  
  Remove `ogProxy` jQuery dependency; the script can use `fetch` directly (IntersectionObserver is already used, so modern browsers are assumed).

- [ ] **Create `assets/ev-news-feed.css`**  
  Initially empty or a stub — populated during the CSS extraction step (Part 3).

#### Theme side — remove coupled code

- [ ] **Remove from `theme/functions.php`:**
  - `fetch_og_image_proxy()` and its `add_action( 'wp_ajax_*', ... )` calls
  - `wp_enqueue_script( 'ev-news-tracking', ... )` and `wp_enqueue_script( 'ogimageloader-init', ... )`
  - The `wp_localize_script( 'ogimageloader-init', 'ogProxy', [...] )` call

- [ ] **Update `theme/page-ev-news-feed.php`:**  
  Replace the entire data-fetch + article loop block (lines 12–69) with a single call:
  ```php
  evna_get_template( 'feed.php' );
  ```
  Keep `get_template_part( 'template-parts/header' )` at the top and `get_template_part( 'template-parts/find-us' )` / `get_template_part( 'template-parts/footer' )` at the bottom — these are theme-owned.

- [ ] **(Optional) Relocate theme card template**  
  Move `theme/template-parts/ev-news-feed/card.php` to `theme/ev-news-automator/parts/card.php` to align with the override convention, OR delete it if its content is identical to the new plugin default.

#### Verification

- [ ] Visit `/ev-news-feed/` — articles render correctly
- [ ] Click an article — GA4 `ev_news_click` event fires in GTM Preview
- [ ] Open DevTools Network tab — OG thumbnails load (AJAX proxy responds)
- [ ] Deactivate the plugin — page shows the graceful empty-state markup, no PHP fatal
- [ ] Activate a default theme (e.g. Twenty Twenty-Four) — feed body renders from plugin defaults without fatal errors (layout will be unstyled, which is acceptable)

---

## Part 2 — Boost Recent Articles Feature

### Feature Spec

**Admin setting:** "Boost articles from the last 24 hours to the top"  
**Option key inside `ena_settings`:** `boost_recent_24h` (boolean, default `false`)

When **enabled**, articles whose `pub_date` falls within the last 24 hours (approximated as `pub_date >= yesterday's calendar date`, since `pub_date` is stored as `Y-m-d` only) are moved to the top of the feed. All other articles follow the existing three-group sort below them.

When **disabled**, the existing sort is unchanged:
- Group 1: published today
- Group 2: older, clicks > 0 (sorted by clicks DESC)
- Group 3: older, zero clicks

### Files to Change

#### 1. `includes/class-ena-settings.php` — add default

In `defaults()`, add:
```php
'boost_recent_24h' => false,
```

#### 2. `admin/views/settings-page.php` — add checkbox

After the closing `</tr>` of the "Article Age Limit" row (currently around line 130), insert:

```php
<tr>
    <th scope="row">Boost recent articles</th>
    <td>
        <label for="boost_recent_24h">
            <input type="checkbox" name="boost_recent_24h" id="boost_recent_24h" value="1"
                   <?php checked( (bool) $settings->get( 'boost_recent_24h' ) ); ?>>
            Pin articles published in the last 24 hours to the top of the feed
        </label>
        <p class="description">When enabled, articles whose publication date falls within the last 24 hours
        are shown first, regardless of click count. Because publication dates are stored as calendar dates
        only, "last 24 hours" means today and yesterday.</p>
    </td>
</tr>
```

#### 3. `admin/class-ena-admin.php` — save handler

In `handle_settings_save()` (line 78), add alongside the other `$values[...]` assignments:
```php
$values['boost_recent_24h'] = ! empty( $_POST['boost_recent_24h'] );
```

#### 4. `includes/class-ena-sync.php` — sort logic + settings dependency

`ENA_Sync` currently receives `ENA_Sheets` and `ENA_Logger` only. Add `ENA_Settings` as a third constructor parameter:

```php
// class-ena-sync.php
private ENA_Settings $settings;

public function __construct( ENA_Sheets $storage, ENA_Logger $logger, ENA_Settings $settings ) {
    $this->storage  = $storage;
    $this->logger   = $logger;
    $this->settings = $settings;
}
```

Update the instantiation in `includes/class-ena-plugin.php` (line 42):
```php
$this->sync = new ENA_Sync( $this->storage, $this->logger, $this->settings );
```

Replace the sorting block in `ENA_Sync::run()` (currently lines 41–52) with:

```php
$boost     = (bool) $this->settings->get( 'boost_recent_24h' );
$today     = gmdate( 'Y-m-d' );
$yesterday = gmdate( 'Y-m-d', time() - DAY_IN_SECONDS );

$pub_date_of = fn ( $r ) => ! empty( $r['pub_date'] ) ? $r['pub_date'] : $r['added_date'];

if ( $boost ) {
    // Group 0 (boost): published today or yesterday (≈ last 24 h, date resolution)
    $recent     = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) >= $yesterday ) );
    // Group 1: older with clicks, sorted DESC
    $old_clicks = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $yesterday && (int) $r['clicks'] > 0 ) );
    // Group 2: older, zero clicks
    $old_zero   = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $yesterday && (int) $r['clicks'] === 0 ) );
    usort( $old_clicks, fn ( $a, $b ) => (int) $b['clicks'] <=> (int) $a['clicks'] );
    $sorted = array_merge( $recent, $old_clicks, $old_zero );
} else {
    // Existing three-group sort — unchanged
    $new_today   = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) === $today ) );
    $with_clicks = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $today && (int) $r['clicks'] > 0 ) );
    $zero_clicks = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $today && (int) $r['clicks'] === 0 ) );
    usort( $with_clicks, fn ( $a, $b ) => (int) $b['clicks'] <=> (int) $a['clicks'] );
    $sorted = array_merge( $new_today, $with_clicks, $zero_clicks );
}
```

Also update the `set_status` call's stat keys to reflect the new grouping when boost is on (add `'boost_mode' => $boost` to the status array for observability).

### Verification

- [ ] Enable the toggle in Settings → Save
- [ ] Trigger "Run sync now" from the Dashboard
- [ ] Visit `/ev-news-feed/` — articles with pub_date = today and yesterday appear before older articles
- [ ] Disable the toggle → Re-sync → Confirm the feed reverts to the original 3-group order
- [ ] Edge case: all articles are older than yesterday — feed renders without empty sections or errors
- [ ] Edge case: no articles at all — the existing empty-state message appears

---

## Part 3 — Template & CSS Organization

### CSS Extraction

The feed-specific visual styles currently live inside the theme's compiled `style.min.css`. The goal is to move only the **feed-exclusive** declarations into `plugin/assets/ev-news-feed.css` so the plugin's card renders acceptably in any theme.

Candidates for extraction:
- `.shadow-card` custom shadow definition
- `bg-from-black-60-gradient`, `bg-to-solidgray-gradient-post` gradient utilities
- `bg-carbon-stripe-white-20` stripe pattern
- `rounded-br-4xl`, `rounded-br-5xl` if not used elsewhere in the theme

Shared Tailwind utilities (`grid`, `flex`, `gap-*`, `text-*`, brand colour definitions) stay in the theme stylesheet. The plugin stylesheet is enqueued **after** the theme stylesheet (`'after'` dependency or explicit handle dependency) so it can assume Tailwind base layers are available.

Until CSS extraction is done the feed continues to rely on the theme stylesheet — this is acceptable and should not block the migration.

### Template Override Convention

The plugin's loader (`evna_get_template`) resolves names relative to:
1. `get_stylesheet_directory() . '/ev-news-automator/' . $name` (theme override)
2. `ENA_PLUGIN_DIR . 'templates/' . $name` (plugin default)

The theme override directory mirrors the plugin `templates/` tree exactly:

```
theme/ev-news-automator/
  feed.php          → overrides plugin/templates/feed.php
  parts/
    card.php        → overrides plugin/templates/parts/card.php
```

Theme developers should **never** call `get_template_part( 'template-parts/ev-news-feed/...' )` directly for EV news content — always use `evna_get_template()` so the resolution chain is respected.

---

## Recommended Implementation Order

Tackle these in separate focused sessions to keep each step reviewable:

| Session | Scope | Files touched |
|---------|-------|--------------|
| 1 | Boost feature | `class-ena-settings.php`, `settings-page.php`, `class-ena-admin.php`, `class-ena-sync.php`, `class-ena-plugin.php` |
| 2 | Plugin frontend class | `class-ena-frontend.php` (new), `class-ena-plugin.php` |
| 3 | Plugin default templates | `templates/feed.php` (new), `templates/parts/card.php` (new) |
| 4 | Theme cleanup | `theme/functions.php`, `theme/page-ev-news-feed.php`, optionally remove old card |
| 5 | Filters & actions | `templates/feed.php` (add hooks) |
| 6 | CSS extraction | `assets/ev-news-feed.css`, theme `style.css` / Tailwind config |

Sessions 1–3 add new code without removing anything, so they carry zero regression risk. Sessions 4–5 remove the coupling; verify the feed after each one before moving to the next.
