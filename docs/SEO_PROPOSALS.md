# On-Site SEO Improvement Proposals — carlifebydani.com

**Analysis date:** 2026-06-18  
**Overall readiness score:** 6.5 / 10  
**Language:** Bulgarian / bg-BG  
**Live data source:** Google Analytics 4 + Search Console (last 28 days: May 21 – Jun 17, 2026)

---

## Live Analytics Snapshot

> Data pulled directly from GA4 / Search Console on 2026-06-18.

| Metric | Value |
|--------|-------|
| Organic clicks (28d) | **1,315** |
| Organic impressions (28d) | **44,509** |
| Average CTR | **2.95%** |
| Average position | **6.52** |
| Active users (28d) | **1,201** |
| Avg. engagement time / user | **1m 05s** |
| Engaged sessions / user | **0.65** |
| Primary audience | **Bulgaria — Sofia (1.2K), Varna (234), Plovdiv (131)** |

### Top 10 Organic Landing Pages

| # | Page | Clicks | Impressions | CTR | Avg. Position | Engagement Rate |
|---|------|--------|-------------|-----|---------------|-----------------|
| 1 | `/` (homepage) | 260 | 1,026 | 25.3% | 4.55 | 84.1% |
| 2 | `/publications/mg4-priemlivo-kachestvo-na-razumna-cena/` | 111 | 3,840 | 2.9% | 5.79 | 54.0% |
| 3 | `/publications/mg-zs-ev-kompakten-gradski-suv/` | 73 | 2,538 | 2.9% | 6.06 | 47.7% |
| 4 | `/ev-review/toyota-bz4x-tromava-iznenada-izvan-putya/` | 65 | 2,815 | 2.3% | 7.02 | 61.8% |
| 5 | `/publications/stanciite-na-tesla-v-blgariya-veche-sa-plateni/` | 63 | 1,710 | 3.7% | 6.18 | 66.1% |
| 6 | `/publications/domashno-zarezhdane-na-elektromobili/` | 60 | 2,649 | 2.3% | 7.97 | 50.0% |
| 7 | `/publications/eldrive-fest-2026-nie-shhe-sme-tam/` | 46 | 549 | 8.4% | 5.94 | 36.3% |
| 8 | `/ev-review/kia-e-niro-pokupkata-parvite-2-godini-elektromobil/` | 44 | 570 | 7.7% | 6.12 | 74.5% |
| 9 | `/clbd-parts/` | 43 | 395 | 10.9% | 4.05 | 58.8% |
| 10 | `/publications/noviyat-tesla-model-y-juniper-2025-be-predstaven-v-evropa/` | 27 | 1,562 | 1.7% | 5.48 | 69.4% |

### Top Non-Branded Search Queries

| # | Query (Bulgarian) | Clicks | Impressions | CTR | Position |
|---|-------------------|--------|-------------|-----|----------|
| 1 | mg zs мнения | 32 | 365 | 8.8% | 4.24 |
| 2 | toyota bz4x мнения | 24 | 113 | 21.2% | 2.52 |
| 3 | киа ниро електрик мнения | 21 | 136 | 15.4% | 5.39 |
| 4 | mg4 | 15 | 378 | 4.0% | 5.24 |
| 5 | mg 4 electric | 8 | 102 | 7.8% | 6.10 |
| 6 | mg4 electric | 8 | 241 | 3.3% | 6.16 |
| 7 | mg4 electric цена | 8 | 114 | 7.0% | 6.52 |
| 8 | mg4 мнения | 6 | 21 | 28.6% | 2.00 |
| 9 | toyota bz4x | 6 | 484 | 1.2% | 5.68 |
| 10 | зарядни станции на тесла в българия | 6 | 35 | 17.1% | 3.80 |
| 11 | рено 5 | 5 | 377 | 1.3% | 5.97 |
| 12 | mg zs | 4 | 271 | 1.5% | 8.61 |
| 13 | baw pony цена | 4 | 133 | 3.0% | 6.63 |

**Key findings:**
- **34% of organic clicks are branded** (clbd / car life by dani / carlife by dani) — non-branded growth is the core opportunity
- **"mg4" cluster has 835+ combined impressions** across 5 keyword variants but avg CTR of only 4% — adding structured data and better meta descriptions could 2–3× clicks
- **"toyota bz4x"** alone has 484 impressions at position 5.68 with just 1.24% CTR — a compelling meta description could add ~30–50 clicks/month
- **EV Reviews earn the highest CTR** when they rank in top 3 ("toyota bz4x мнения" 21.2%, "mg4 мнения" 28.6%) — more review content is the highest-leverage content investment
- **Branded homepage CTR is 25.3%** — strong brand recognition, but the homepage has no H1 (proposal 2.1)

---

## Yoast SEO Pro — What It Covers

**Active plugin:** Yoast SEO Premium (Pro). This changes the implementation strategy for several proposals below.

| Proposal | Status with Yoast Pro |
|----------|-----------------------|
| 1.1 Meta descriptions, OG tags, Twitter Cards | **Yoast handles** — configure in Yoast UI, do NOT add custom PHP |
| 1.2 Article + Organization JSON-LD | **Yoast handles** — outputs automatically on posts/pages |
| 1.2 BreadcrumbList JSON-LD | **Yoast handles** — enable Yoast Breadcrumbs in Search Appearance settings |
| 1.3 Title tag | **Needs 1 code change** — remove `wp_title()` from header.php + add `add_theme_support('title-tag')` in functions.php; Yoast then controls titles from the admin |
| 2.2 BreadcrumbList JSON-LD | **Yoast handles** — see 1.2 |
| 3.6 Canonical tags | **Yoast handles** — automatic |
| B1 Review schema for EV Reviews | **NOT covered** — requires custom code or ACF + Yoast schema extension |
| A1 CollectionPage schema for EV News | **NOT covered** — requires custom code |
| C1 VideoObject schema for EV Masters | **NOT covered by default** — Yoast Video SEO is a separate paid add-on; custom code is the alternative |

> **Conflict warning:** Do NOT also implement the custom `carlife_seo_meta_tags()`, `carlife_schema_jsonld()`, or `carlife_canonical_tag()` functions from the original proposals — they will duplicate Yoast's output and cause double tags.

---

## Summary

The theme has a solid semantic HTML foundation — proper use of `<header>`, `<nav>`, `<article>`, `<footer>`, `<main>`, mobile-responsive layout, and clean internal-linking practices. With Yoast SEO Pro now active, the meta tag and base structured data layers are handled by the plugin. The remaining gaps are: **the title tag hook** (one code change needed to let Yoast take over), **article-type-specific schema** (Review, CollectionPage, VideoObject — Yoast does not output these), **image optimisation** (no lazy loading, missing alt text), and a few **heading hierarchy** issues.

**Live data confirms the priority order:** EV Reviews are the strongest organic performers — "toyota bz4x мнения" ranks at position 2.52 with 21.2% CTR, "mg4 мнения" at position 2.00 with 28.6% CTR. Adding `Review` schema to every EV Review post is the single action most likely to unlock rich results (star ratings, reviewer byline) and increase CTR across the whole review category. The MG4 cluster (835 impressions, 4% avg CTR) will also benefit directly from Yoast-managed meta descriptions once editors fill in the Yoast metabox on those posts.

---

## Priority 1 — Critical (Immediate impact on crawling and SERP appearance)

### 1.1 Meta descriptions and Open Graph / Twitter Card tags

**Status: Handled by Yoast SEO Pro — no custom PHP needed.**

Yoast outputs `<meta name="description">`, all `og:*` tags, and Twitter Card tags automatically. The custom `carlife_seo_meta_tags()` function from the earlier draft must NOT be added — it would create duplicate tags.

**Yoast configuration checklist:**

1. **Enable OG tags:** Yoast SEO → Social → Facebook → "Add Open Graph meta data" → On
2. **Enable Twitter Cards:** Yoast SEO → Social → Twitter → "Add Twitter card meta data" → On
3. **Upload default OG image** (1200×630 px): Yoast SEO → Social → Facebook → Default image → upload `/images/og-default.jpg`
4. **Set homepage description:** Yoast SEO → Search Appearance → General → Site tagline, or set it directly in the Yoast metabox on the front page
5. **Per-post meta descriptions:** open each high-traffic post and fill in the Yoast "SEO description" field in the Yoast metabox. Priority order based on impressions:
   - `/publications/mg4-priemlivo-kachestvo-na-razumna-cena/` (3,840 impressions, 2.9% CTR)
   - `/publications/domashno-zarezhdane-na-elektromobili/` (2,649 impressions, 2.3% CTR)
   - `/publications/mg-zs-ev-kompakten-gradski-suv/` (2,538 impressions, 2.9% CTR)
   - `/ev-review/toyota-bz4x-tromava-iznenada-izvan-putya/` (2,815 impressions, 2.3% CTR)
   - `/publications/noviyat-tesla-model-y-juniper-2025-be-predstaven-v-evropa/` (1,562 impressions, 1.7% CTR)

---

### 1.2 JSON-LD structured data

**Status: Base schemas handled by Yoast SEO Pro. Article-type-specific schemas require custom code.**

Yoast automatically outputs `Organization`, `WebSite`, `WebPage`, and `Article` JSON-LD on every page. The custom `carlife_schema_jsonld()` function must NOT be added — it would duplicate Yoast's output.

**What Yoast outputs automatically:**
- `Organization` (site-wide)
- `WebSite` with `SearchAction` (sitelinks search box — unlocked automatically)
- `WebPage` on every page
- `Article` on single posts (with `datePublished`, `dateModified`, `author`, `image`, `headline`)
- `BreadcrumbList` when Yoast breadcrumbs are enabled (see 2.2)

**What still requires custom code (Yoast does not output these):**
- `Review` + `Car` schema for EV Reviews → see proposal B1
- `CollectionPage` + `ItemList` schema for EV News → see proposal A1
- `VideoObject` schema for EV Masters → see proposal C1

**Yoast configuration checklist:**
1. Yoast SEO → Search Appearance → Content Types → Posts → "Show in search results" → Yes, and "Show SEO settings" → On
2. Yoast SEO → Search Appearance → General → Knowledge Graph → set site name, logo, organisation type
3. Verify schema output at any post URL using Google's [Rich Results Test](https://search.google.com/test/rich-results)

---

### 1.3 Fix `<title>` tag — REQUIRED for Yoast to function

**Problem:** `template-parts/header.php:11` uses the deprecated `wp_title()` function. Yoast SEO Pro **cannot manage the title tag** until `add_theme_support('title-tag')` is declared and the hardcoded `<title>` is removed. This is the single most critical code change needed right now — without it Yoast's title templates have no effect.

**File:** [template-parts/header.php](theme/template-parts/header.php) line 11 — remove:
```html
<title><?php wp_title(); ?></title>
```

**File:** [functions.php](theme/functions.php) — add inside the theme setup function (near line 5 where `post-thumbnails` is declared):
```php
add_theme_support('title-tag');
```

After this change, configure title templates in Yoast SEO → Search Appearance → Content Types to match the Bulgarian format the site uses.

---

## Priority 2 — High (Noticeable ranking and usability impact)

### 2.1 Add H1 to the homepage

**Problem:** `front-page.php` has no `<h1>` tag. Every page should have exactly one H1. The hero image area or site tagline is the natural place.

**File:** `front-page.php` — add before the featured-posts partial:

```php
<h1 class="sr-only"><?php bloginfo('description'); ?></h1>
```

Using `sr-only` (Tailwind's screen-reader-only utility) keeps the visual design unchanged while satisfying crawlers and accessibility.

---

### 2.2 BreadcrumbList schema for existing breadcrumbs

**Status: Handled by Yoast SEO Pro — no custom JSON-LD needed.**

Yoast outputs `BreadcrumbList` JSON-LD automatically when its breadcrumbs feature is enabled. Do NOT add the custom JSON-LD block from the earlier draft — it would duplicate Yoast's output.

**Yoast configuration:**
1. Yoast SEO → Search Appearance → Breadcrumbs → "Enable breadcrumbs for your theme" → On
2. Set separator, home label ("Начало"), and prefix to match the site's Bulgarian UI
3. Optionally replace `template-parts/bread-crumbs.php` visual output with:
   ```php
   <?php if (function_exists('yoast_breadcrumb')) {
       yoast_breadcrumb('<nav class="breadcrumbs" aria-label="Навигационна пътека">', '</nav>');
   } ?>
   ```
   This uses Yoast's breadcrumb HTML so the visual and schema outputs stay in sync. The existing custom `bread-crumbs.php` can remain for now if restyling is not planned.
```

---

### 2.3 Add `loading="lazy"` and `decoding="async"` to images

**Problem:** No lazy loading is applied. All images are eagerly fetched, increasing initial page weight and slowing LCP on long pages. `template-parts/featured-image.php:5` fallback images also have empty `alt` attributes.

**File:** `template-parts/featured-image.php` — wrap `get_the_post_thumbnail()` with additional attributes:

```php
// Replace the existing get_the_post_thumbnail call
echo get_the_post_thumbnail($post_id, $size, [
    'loading' => 'lazy',
    'decoding' => 'async',
    'alt' => esc_attr(get_the_title($post_id)),
]);
```

For the fallback image, add descriptive alt:
```php
// Was: alt=""
<img src="..." loading="lazy" decoding="async" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
```

WordPress 5.5+ sets `loading="lazy"` on images in post content automatically, but theme-level calls need it explicitly.

---

### 2.4 Defer non-critical JavaScript

**Problem:** `functions.php:12-14` enqueues `gtag.js`, `glightbox.min.js`, and `glightbox.init.js` without deferral. These block HTML parsing and delay LCP.

**File:** `functions.php` — update the enqueue calls:

```php
// Before (blocking)
wp_enqueue_script('gtag', get_stylesheet_directory_uri() . '/js/gtag.js');
wp_enqueue_script('glightbox', get_stylesheet_directory_uri() . '/js/glightbox.min.js');
wp_enqueue_script('glightbox-init', get_stylesheet_directory_uri() . '/js/glightbox.init.js', ['glightbox', 'jquery']);
wp_enqueue_script('ogimageloader-init', get_stylesheet_directory_uri() . '/js/ogimageloader.init.js', ['jquery']);

// After (non-blocking)
wp_enqueue_script('gtag', get_stylesheet_directory_uri() . '/js/gtag.js', [], null, ['strategy' => 'async']);
wp_enqueue_script('glightbox', get_stylesheet_directory_uri() . '/js/glightbox.min.js', [], null, ['strategy' => 'defer', 'in_footer' => true]);
wp_enqueue_script('glightbox-init', get_stylesheet_directory_uri() . '/js/glightbox.init.js', ['glightbox', 'jquery'], null, ['strategy' => 'defer', 'in_footer' => true]);
wp_enqueue_script('ogimageloader-init', get_stylesheet_directory_uri() . '/js/ogimageloader.init.js', ['jquery'], null, ['strategy' => 'defer', 'in_footer' => true]);
```

> Note: `strategy` key requires WordPress 6.3+. For older WordPress use the `$in_footer = true` boolean (5th parameter) as a fallback.

---

## Priority 3 — Medium (Quality and content-structure improvements)

### 3.1 Fix heading misuse (h5 for metadata)

**Problem:** `single.php` uses `<h5>` and `<h6>` for the category link and publication date — these are not headings, they are metadata. Search engines read heading tags as content signals; putting dates in them adds noise.

**Files:** `single.php` and related single partials.

Replace heading tags used for metadata (author, date, category on article pages) with `<span>` or `<p>` tags styled to match.

---

### 3.2 Add `<section>` tags to front-page content blocks

**Problem:** `front-page.php` content areas (featured posts, brands, newsletter, etc.) are wrapped in generic `<div>` containers. Wrapping each logical block in a `<section>` with an `aria-label` helps both accessibility and crawlers understand content zones.

```html
<section aria-label="Избрано за вас">
    <?php get_template_part('template-parts/front-page/featured-posts'); ?>
</section>

<section aria-label="Марки">
    <?php get_template_part('template-parts/front-page/brands'); ?>
</section>
```

---

### 3.3 Add `<figure>` and `<figcaption>` for post images

**Problem:** Featured images and inline post images are not wrapped in `<figure>` elements. This misses semantic context for image search indexing and accessibility.

**File:** `template-parts/featured-image.php`:

```php
<figure class="featured-image">
    <?php echo get_the_post_thumbnail($post_id, $size, ['loading' => 'lazy', 'decoding' => 'async']); ?>
    <?php $caption = get_the_post_thumbnail_caption($post_id);
    if ($caption) : ?>
        <figcaption><?php echo esc_html($caption); ?></figcaption>
    <?php endif; ?>
</figure>
```

---

### 3.4 Add `<aside>` for sidebar areas

**Problem:** Sidebar/related-content containers use generic `<div>` wrappers. Using `<aside>` conveys that content is complementary rather than primary, which helps crawlers assign appropriate weight.

In `single.php` and `index.php` sidebar regions, replace the outermost `<div>` wrapper with `<aside aria-label="Свързани статии">`.

---

### 3.5 Add preconnect hints for third-party origins

**Problem:** Google Fonts (loaded via CSS) and Google Analytics are external domains. Without preconnect hints, each connection starts cold on first request.

**File:** `functions.php` — add to `wp_head`:

```php
add_action('wp_head', 'carlife_resource_hints', 0);
function carlife_resource_hints() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://www.google-analytics.com">' . "\n";
    echo '<link rel="dns-prefetch" href="https://www.googletagmanager.com">' . "\n";
}
```

---

### 3.6 Canonical tags for paginated archives

**Status: Handled by Yoast SEO Pro — no custom PHP needed.**

Yoast outputs `<link rel="canonical">` on all page types including paginated archives, singular posts, and category pages. The custom `carlife_canonical_tag()` function from the earlier draft must NOT be added.

**Verify:** Check any paginated archive (`/publications/page/2/`) in browser devtools to confirm Yoast is outputting the canonical tag. Yoast SEO → Tools → File editor should show no overriding robots.txt rules.

---

## Priority 4 — Low (Future enhancements)

### 4.1 WebP / AVIF image support

Consider enabling WordPress WebP output and `<picture>` elements with AVIF/WebP sources for modern browsers. This requires server support (Imagick with WebP enabled) and is best added once the critical fixes above are done.

### 4.2 Sitelinks search box schema

Add `WebSite` schema with `SearchAction` so Google can display an inline search box in brand SERPs.

```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Car Life by Dani",
  "url": "https://carlifebydani.com/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://carlifebydani.com/?s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
```

### 4.3 hreflang tags (if multilingual expansion)

If content is ever published in other languages, add `<link rel="alternate" hreflang="bg" href="...">` tags for each language variant.

### 4.4 Author schema and author archive pages

Each author archive page could output a `Person` schema block linking to any social profiles. This improves E-E-A-T (Experience, Expertise, Authoritativeness, Trust) signals.

### 4.5 FAQ / HowTo schema for applicable posts

For car-buying guides, checklists, or Q&A posts, add `FAQPage` or `HowTo` schema to unlock accordion-style rich results in Google.

---

---

## Article-Type-Specific SEO

The site has three distinct post categories that share `single.php` but have fundamentally different content structures. Each needs its own schema strategy and thin-content posture.

| Category | ID | Content structure | SEO risk |
|----------|-----|------------------|----------|
| EV News | `EV_NEWS_CATEGORY_ID` (1) | Authored excerpt + curated external links from CSV | Thin content if excerpt is short |
| EV Reviews | `EV_REVIEWS_CATEGORY_ID` (3) | Full authored review text | Low — strongest content type |
| EV Masters | `EV_MASTERS_CATEGORY_ID` (45) | YouTube video embed, minimal text | High — embed-only pages are invisible to Google |

---

### A. EV News — Curated link roundups

**How it works:** `single.php:110-142` fetches a remote CSV via `news_csv` post meta and renders each external article as a `card-article-external.php` card. The `post_excerpt` is the only original authored text on the page. All external links already carry `rel="nofollow"` via the `add_blank_to_links()` filter.

**SEO risks:**
- If the excerpt is fewer than ~120 words Google treats the page as thin/shallow.
- A page that is mostly outbound links to other domains signals low editorial value without clear framing.

**Proposals:**

**A1. Use `CollectionPage` + `ItemList` schema**

This signals to Google that the page is intentional editorial curation, not scraped content.

```php
// In the carlife_schema_jsonld() function, add a branch:
if (is_singular('post') && in_category(EV_NEWS_CATEGORY_ID)) {
    global $post;

    // Build item list from the CSV (same fetch logic as single.php)
    $news_csv_url = get_post_meta($post->ID, 'news_csv', true);
    $items = [];
    if ($news_csv_url && carlifebydani_is_safe_url($news_csv_url)) {
        $response = wp_remote_get($news_csv_url, ['timeout' => 5]);
        if (!is_wp_error($response)) {
            $rows = array_map('str_getcsv', explode("\n", wp_remote_retrieve_body($response)));
            array_shift($rows); // remove CSV header
            foreach (array_values(array_filter($rows)) as $i => $row) {
                if (empty($row[2])) continue;
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $i + 1,
                    'url'      => $row[2],
                    'name'     => $row[0],
                ];
            }
        }
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'CollectionPage',
        'name'            => get_the_title($post),
        'description'     => wp_strip_all_tags(get_the_excerpt($post)),
        'url'             => get_permalink($post),
        'datePublished'   => get_the_date('c', $post),
        'dateModified'    => get_the_modified_date('c', $post),
        'author'          => ['@type' => 'Person', 'name' => get_the_author_meta('display_name', $post->post_author)],
        'mainEntity'      => ['@type' => 'ItemList', 'itemListElement' => $items],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
```

**A2. Editorial excerpt minimum — editorial guideline**

Add a note to the content guidelines: every EV News post must have a minimum of 100–150 words of original Bulgarian commentary in the excerpt field. This is the primary signal of editorial value and is what Google uses as the SERP snippet.

**A3. Meta description from excerpt**

The `carlife_seo_meta_tags()` function (proposal 1.1) already pulls from the excerpt — EV News pages benefit directly from that fix, since the excerpt IS the authored content.

---

### B. EV Reviews — Original authored content

**How it works:** `single.php:103-108` renders `post_content` in full. This is the strongest content type on the site — long-form authored reviews of electric vehicles.

**Current performance (top queries):**

| Query | Clicks | Impressions | CTR | Position |
|-------|--------|-------------|-----|----------|
| toyota bz4x мнения | 24 | 113 | 21.2% | 2.52 |
| киа ниро електрик мнения | 21 | 136 | 15.4% | 5.39 |
| mg zs мнения | 32 | 365 | 8.8% | 4.24 |
| mg4 мнения | 6 | 21 | 28.6% | 2.00 |

EV Reviews have the highest CTR on the site when they rank in the top 3. The gap is volume: there are very few review pages and limited impressions. More reviews and better schema will compound quickly.

**SEO risks:** Yoast outputs generic `Article` schema on these pages. Leaving `Review` schema unimplemented means no rich result eligibility (star ratings, reviewer byline) — a significant missed opportunity for this content type.

**Proposals:**

**B1. Add `Review` schema with `itemReviewed` as a `Car` — NOT covered by Yoast, custom code required**

Add to `functions.php`. This runs alongside Yoast's `Article` schema — both can coexist in `wp_head`:

```php
add_action('wp_head', 'carlife_ev_review_schema', 5);
function carlife_ev_review_schema() {
    if (!is_singular('post') || !in_category(EV_REVIEWS_CATEGORY_ID)) return;
    global $post;

    $car_model = get_post_meta($post->ID, 'car-model', true) ?: get_the_title($post);
    $rating    = get_post_meta($post->ID, 'car-rating', true); // numeric 1–10, optional

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Review',
        'name'          => get_the_title($post),
        'description'   => wp_strip_all_tags(get_the_excerpt($post)),
        'url'           => get_permalink($post),
        'datePublished' => get_the_date('c', $post),
        'dateModified'  => get_the_modified_date('c', $post),
        'inLanguage'    => 'bg',
        'author'        => [
            '@type' => 'Person',
            'name'  => get_the_author_meta('display_name', $post->post_author),
            'url'   => get_author_posts_url($post->post_author),
        ],
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'url'   => home_url('/'),
        ],
        'itemReviewed'  => [
            '@type' => 'Car',
            'name'  => $car_model,
        ],
        'image' => has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'large') : null,
    ];

    if ($rating) {
        $schema['reviewRating'] = [
            '@type'       => 'Rating',
            'ratingValue' => (float) $rating,
            'bestRating'  => 10,
            'worstRating' => 1,
        ];
    }

    echo '<script type="application/ld+json">' . wp_json_encode(array_filter($schema), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
```

**B2. Register `car-model` and `car-rating` custom fields**

Register via `register_post_meta` (or ACF if already in use) so editors can fill them in the post editor:

```php
add_action('init', function () {
    register_post_meta('post', 'car-model', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'string',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    register_post_meta('post', 'car-rating', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'number',
        'sanitize_callback' => 'floatval',
    ]);
});
```

`car-model` example values: `Toyota bZ4X`, `MG4 Electric`, `Kia e-Niro`. These feed `itemReviewed.name` precisely without parsing the post title.

**B3. Add numeric ratings for star snippets — highest CTR unlock available**

Google displays star ratings in SERPs from `Review.reviewRating`. Even appearing at position 5 with a star rating badge will significantly lift CTR over undecorated blue links. Once `car-rating` meta is populated on existing review posts, the schema in B1 above will emit star ratings automatically.

**Editorial action for existing EV Review posts — fill in immediately:**

| Post | `car-model` value | `car-rating` suggestion |
|------|-------------------|------------------------|
| Toyota bZ4X review | `Toyota bZ4X` | 7.5 |
| Kia e-Niro review | `Kia e-Niro` | 8.5 |
| MG ZS EV review | `MG ZS EV` | 7.0 |
| MG4 Electric review | `MG4 Electric` | 8.0 |

**B4. Yoast content optimisation for EV Reviews**

With Yoast active, use its per-post SEO panel:
- **Focus keyphrase:** set to the Bulgarian query format — e.g. `Toyota bZ4X мнения` for the Toyota review
- **Meta description:** write a compelling 150-character Bulgarian description that includes the focus keyphrase and a differentiator (e.g. "Честен личен опит след 6 месеца с Toyota bZ4X — зареждане, разход и изненади извън пътя.")
- **Cornerstone content:** mark all EV Review posts as cornerstone content in Yoast to signal editorial priority

---

### C. EV Masters — YouTube video embeds

**How it works:** `post_content` is primarily a YouTube `<iframe>` embed. There may be minimal surrounding text. The excerpt may be empty. From Google's perspective these pages are nearly invisible — the video content itself is on YouTube, and there is no text or structured signal on the page to rank against.

**SEO risks:**
- Pages with only an embed and no original text are the most likely to be de-prioritised by Google.
- Missing `VideoObject` schema means no eligibility for Google's **video carousel** rich results — the single biggest SERP opportunity for this content type.

**Proposals:**

**C1. Add `VideoObject` schema (highest-impact fix for EV Masters)**

Extract the YouTube video ID from the embed and output full `VideoObject` schema. This is what enables Google to index these pages as video results and show them in the video carousel.

```php
if (is_singular('post') && in_category(EV_MASTERS_CATEGORY_ID)) {
    global $post;

    // Extract YouTube video ID from the iframe embed in post_content
    preg_match(
        '/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i',
        $post->post_content,
        $yt_match
    );
    $yt_id = $yt_match[1] ?? null;

    if ($yt_id) {
        $schema = [
            '@context'     => 'https://schema.org',
            '@type'        => 'VideoObject',
            'name'         => get_the_title($post),
            'description'  => wp_strip_all_tags(get_the_excerpt($post)) ?: get_the_title($post),
            'thumbnailUrl' => 'https://img.youtube.com/vi/' . $yt_id . '/maxresdefault.jpg',
            'uploadDate'   => get_the_date('c', $post),
            'embedUrl'     => 'https://www.youtube.com/embed/' . $yt_id,
            'contentUrl'   => 'https://www.youtube.com/watch?v=' . $yt_id,
            'publisher'    => [
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'logo'  => ['@type' => 'ImageObject', 'url' => get_stylesheet_directory_uri() . '/images/logo.svg'],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
```

**C2. Require a description excerpt on every EV Masters post — editorial guideline**

Without text Google has nothing to rank. Even 2–3 sentences summarising what the video covers (car model, key topics, why it matters) written in Bulgarian dramatically improves the page's standalone ranking value. Recommended minimum: 80 words.

**C3. Lazy-load YouTube iframes**

YouTube iframes block the page's LCP even when far below the fold. Replace the plain `<iframe src="https://www.youtube.com/embed/...">` with a poster-image facade that loads the iframe only on click, or at minimum add `loading="lazy"`:

```html
<iframe src="https://www.youtube.com/embed/VIDEO_ID"
        loading="lazy"
        title="<?php echo esc_attr(get_the_title()); ?>"
        allowfullscreen>
</iframe>
```

Adding a `title` attribute to the iframe is also a Lighthouse accessibility requirement.

**C4. Use the YouTube thumbnail as the post featured image**

If an EV Masters post has no WordPress featured image, fall back to the YouTube `maxresdefault.jpg` for OG image and structured data. This can be handled in the `carlife_seo_meta_tags()` hook:

```php
// In the $image derivation inside carlife_seo_meta_tags():
if (is_singular('post') && !has_post_thumbnail() && in_category(EV_MASTERS_CATEGORY_ID)) {
    preg_match('/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i', get_the_content(), $m);
    if (!empty($m[1])) {
        $image = 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg';
    }
}
```

---

## Implementation Order

Tasks now split between **code changes**, **Yoast configuration**, and **editorial actions**.

### Code changes (theme)

| # | Proposal | Est. effort | SEO impact |
|---|----------|-------------|------------|
| 1 | Fix `<title>` — remove `wp_title()`, add `add_theme_support('title-tag')` (1.3) | 20m | **Critical** — Yoast cannot function without this |
| 2 | Register `car-model` + `car-rating` post meta fields (B2) | 30m | High — unlocks star ratings |
| 3 | Add `Review` + `Car` schema for EV Reviews (B1) | 1h | High — unlocks review rich results |
| 4 | Add `VideoObject` schema for EV Masters (C1) | 1h | Very high — unlocks video carousel |
| 5 | Add `CollectionPage` + `ItemList` schema for EV News (A1) | 1.5h | High — thin content signal |
| 6 | Homepage H1 (2.1) | 15m | High |
| 7 | Lazy loading + alt text on featured images (2.3) | 1h | High |
| 8 | Defer JS (2.4) | 30m | Medium |
| 9 | Lazy-load YouTube iframes + title attr (C3) | 30m | Medium |
| 10 | Preconnect hints (3.5) | 15m | Medium |
| 11 | Semantic improvements — section, aside, figure (3.2–3.4) | 2h | Low–Medium |
| 12 | Fix heading misuse (3.1) | 1h | Low |
| 13 | WebP support, hreflang (4.x) | 4h+ | Low–Medium |

### Yoast SEO Pro configuration (admin UI, no code)

| # | Task | Est. effort | SEO impact |
|---|------|-------------|------------|
| Y1 | Enable OG + Twitter Card, upload default OG image (1200×630 px) | 15m | Very high |
| Y2 | Search Appearance → Knowledge Graph: set org name, logo, type | 10m | High |
| Y3 | Search Appearance → Breadcrumbs: enable, set Bulgarian labels | 10m | High |
| Y4 | Content Types → Posts: enable SEO settings, set title templates | 15m | High |
| Y5 | Verify canonical output on a paginated archive page | 5m | High — confirm no gaps |

### Editorial actions (content team)

| # | Task | Priority |
|---|------|----------|
| E1 | Fill `car-model` and `car-rating` custom fields on all EV Review posts | Immediate — needed for B1 schema to output star ratings |
| E2 | Set Yoast focus keyphrase on every EV Review post (format: `[Model] мнения`) | Immediate |
| E3 | Write Yoast meta descriptions for top-5 high-impression posts (see 1.1) | This week |
| E4 | Mark all EV Review posts as Cornerstone Content in Yoast | This week |
| E5 | EV News posts: minimum 100–150 words original Bulgarian text in excerpt field (A2) | Ongoing guideline |
| E6 | EV Masters posts: minimum 80 words describing video content in excerpt field (C2) | Ongoing guideline |

**Total code work (top 7 items):** ~5 hours  
**Total all code:** ~10 hours  
**Yoast config:** ~1 hour  
**Editorial (per post):** ~10 min/post

---

## Notes

- **Validate after each change:** use Google's [Rich Results Test](https://search.google.com/test/rich-results) for schema, and [Open Graph Debugger](https://developers.facebook.com/tools/debug/) for OG tags. Run both after Y1 and after B1 code goes live.
- **Title tag fix (1.3) must go first** — Yoast cannot output its title until `wp_title()` is removed from header.php and `add_theme_support('title-tag')` is declared.
- Core Web Vitals (LCP, CLS, INP) should be measured in Google Search Console after the JS deferral (2.4) and lazy-loading (2.3) changes go live.
- The custom `carlife_ev_review_schema()` function (B1) is safe to add alongside Yoast — it emits a separate `Review` block, while Yoast emits `Article`. Both appear in `<head>` without conflict. Verify in Rich Results Test that both are detected.
