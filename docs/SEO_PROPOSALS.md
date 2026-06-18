# On-Site SEO Improvement Proposals — carlifebydani.com

**Analysis date:** 2026-06-18  
**Overall readiness score:** 6.5 / 10  
**Language:** Bulgarian / bg-BG

---

## Summary

The theme has a solid semantic HTML foundation — proper use of `<header>`, `<nav>`, `<article>`, `<footer>`, `<main>`, mobile-responsive layout, and clean internal-linking practices. The critical gaps are in the **meta tag layer** (no Open Graph, no meta descriptions), **structured data** (no JSON-LD schema), **image optimisation** (no lazy loading, missing alt text on fallback images), and a few **heading hierarchy** issues.

---

## Priority 1 — Critical (Immediate impact on crawling and SERP appearance)

### 1.1 Add meta descriptions and Open Graph / Twitter Card tags

**Problem:** `template-parts/header.php` has only `<title>`, charset, and viewport. There are no `<meta name="description">`, `og:*`, or `twitter:*` tags. Without these, Google writes its own snippets and social shares show blank previews.

**Proposed fix — `functions.php`:** add a `wp_head` hook that generates tags from the post excerpt / site description.

```php
add_action('wp_head', 'carlife_seo_meta_tags', 1);
function carlife_seo_meta_tags() {
    global $post;

    $title       = is_singular() ? get_the_title() : get_bloginfo('name');
    $description = is_singular() && has_excerpt($post)
        ? wp_strip_all_tags(get_the_excerpt())
        : get_bloginfo('description');
    $image       = is_singular() && has_post_thumbnail($post)
        ? get_the_post_thumbnail_url($post, 'large')
        : get_stylesheet_directory_uri() . '/images/og-default.jpg';
    $url         = is_singular() ? get_permalink($post) : home_url('/');
    $type        = is_singular('post') ? 'article' : 'website';

    // Meta description
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";

    // Open Graph
    echo '<meta property="og:type"        content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title"       content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url"         content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:image"       content="' . esc_url($image) . '">' . "\n";
    echo '<meta property="og:site_name"   content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale"      content="bg_BG">' . "\n";

    // Twitter Card
    echo '<meta name="twitter:card"        content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="twitter:image"       content="' . esc_url($image) . '">' . "\n";
}
```

**Also needed:** create `/images/og-default.jpg` (1200×630 px) as the fallback share image.

---

### 1.2 Add JSON-LD structured data

**Problem:** No schema.org markup exists anywhere. This means no rich snippets (article dates, breadcrumbs, author bylines) in Google Search results.

**Proposed fix — `functions.php`:** output JSON-LD via `wp_head`.

```php
add_action('wp_head', 'carlife_schema_jsonld', 2);
function carlife_schema_jsonld() {
    // Organization schema on every page
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_bloginfo('name'),
        'url'      => home_url('/'),
        'logo'     => [
            '@type' => 'ImageObject',
            'url'   => get_stylesheet_directory_uri() . '/images/logo.svg',
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

    // Article schema on single posts
    if (is_singular('post')) {
        global $post;
        $article = [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => get_the_title($post),
            'description'      => wp_strip_all_tags(get_the_excerpt($post)),
            'datePublished'    => get_the_date('c', $post),
            'dateModified'     => get_the_modified_date('c', $post),
            'url'              => get_permalink($post),
            'image'            => has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'large') : null,
            'author'           => [
                '@type' => 'Person',
                'name'  => get_the_author_meta('display_name', $post->post_author),
                'url'   => get_author_posts_url($post->post_author),
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => get_bloginfo('name'),
                'logo'  => ['@type' => 'ImageObject', 'url' => get_stylesheet_directory_uri() . '/images/logo.svg'],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode(array_filter($article), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
```

**Additional schemas to add later:**
- `BreadcrumbList` (see proposal 2.2)
- `FAQPage` on applicable pages
- `WebSite` with `SearchAction` for sitelinks search box

---

### 1.3 Fix `<title>` tag — replace deprecated `wp_title()`

**Problem:** `template-parts/header.php:11` uses the deprecated `wp_title()` function. Since WordPress 4.1 the recommended approach is `add_theme_support('title-tag')`, which lets WordPress (and SEO plugins) control the title.

**File:** `template-parts/header.php`

Remove:
```html
<title><?php wp_title(); ?></title>
```

**File:** `functions.php` — add inside the theme setup function:
```php
add_theme_support('title-tag');
```

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

### 2.2 Add BreadcrumbList schema to existing breadcrumbs

**Problem:** `template-parts/bread-crumbs.php` renders visual breadcrumbs but has no structured data. Google cannot display breadcrumb rich results without JSON-LD.

**File:** `template-parts/bread-crumbs.php` — append after existing markup:

```php
<?php if (is_singular('post') || is_archive() || is_category()) :
    global $post;
    $items = [];
    $items[] = ['@type' => 'ListItem', 'position' => 1, 'name' => 'Начало', 'item' => home_url('/')];

    if (is_singular('post')) {
        $cats = get_the_category($post->ID);
        if ($cats) {
            $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $cats[0]->name, 'item' => get_category_link($cats[0]->term_id)];
        }
        $items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => get_the_title($post)];
    } elseif (is_category()) {
        $items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => single_cat_title('', false)];
    }

    $breadcrumb_schema = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    echo '<script type="application/ld+json">' . wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
endif; ?>
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

### 3.6 Add canonical tags for paginated archives

**Problem:** Paginated archive pages (`?paged=2`, etc.) may be indexed as duplicate content without explicit canonical or `rel="next"` / `rel="prev"` signals.

**File:** `functions.php`:

```php
add_action('wp_head', 'carlife_canonical_tag', 1);
function carlife_canonical_tag() {
    // WordPress core adds canonical via wp_head for singular pages.
    // This adds it for archives/paginated contexts.
    if (is_singular()) return; // core handles these

    $canonical = get_pagenum_link();
    echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
}
```

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

**SEO risks:** Minimal, but leaving `Review` structured data on the table means missing rich result eligibility (star ratings, reviewer byline) in SERPs.

**Proposals:**

**B1. Add `Review` schema with `itemReviewed` as a `Car`**

```php
if (is_singular('post') && in_category(EV_REVIEWS_CATEGORY_ID)) {
    global $post;

    // Attempt to derive car name from the post title — editors should use
    // "Review: [Brand] [Model]" or "[Brand] [Model] — Review" format for best extraction.
    $car_name = get_the_title($post);

    // If a 'car-model' custom field is set, use it for precision
    $car_model_meta = get_post_meta($post->ID, 'car-model', true);

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Review',
        'name'          => get_the_title($post),
        'description'   => wp_strip_all_tags(get_the_excerpt($post)),
        'url'           => get_permalink($post),
        'datePublished' => get_the_date('c', $post),
        'dateModified'  => get_the_modified_date('c', $post),
        'author'        => [
            '@type' => 'Person',
            'name'  => get_the_author_meta('display_name', $post->post_author),
            'url'   => get_author_posts_url($post->post_author),
        ],
        'itemReviewed'  => [
            '@type' => 'Car',
            'name'  => $car_model_meta ?: $car_name,
        ],
        'image' => has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'large') : null,
        // Uncomment and wire to a custom field once a numeric rating system is added:
        // 'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $rating, 'bestRating' => 10],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode(array_filter($schema), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
```

**B2. Add a `car-model` custom field (optional but recommended)**

Register a simple custom meta field (via ACF or `register_post_meta`) so editors can enter the exact car model string (e.g. `Volkswagen ID.4 Pro`). This feeds the `itemReviewed.name` cleanly without relying on title parsing.

**B3. Consider a numeric rating system for star snippets**

Google displays star ratings from `Review` schema in SERPs. Even a simple 1–10 custom field stored in post meta and output as `reviewRating` would unlock this rich result type for every review page.

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

| # | Proposal | Est. effort | SEO impact |
|---|----------|-------------|------------|
| 1 | Meta descriptions + OG + Twitter Card (1.1) | 2h | Very high |
| 2 | JSON-LD Article + Organization schema (1.2) | 2h | Very high |
| 3 | VideoObject schema for EV Masters (C1) | 1h | Very high — unlocks video carousel |
| 4 | Fix `<title>` — use `add_theme_support('title-tag')` (1.3) | 30m | High |
| 5 | Homepage H1 (2.1) | 15m | High |
| 6 | BreadcrumbList JSON-LD (2.2) | 1h | High |
| 7 | Review schema for EV Reviews (B1) | 1h | High — unlocks review rich results |
| 8 | CollectionPage + ItemList schema for EV News (A1) | 1.5h | High — thin content signal |
| 9 | Lazy loading + alt text (2.3) | 1h | High |
| 10 | YouTube thumbnail as OG fallback for EV Masters (C4) | 30m | Medium |
| 11 | Defer JS (2.4) | 30m | Medium |
| 12 | Lazy-load YouTube iframes + title attr (C3) | 30m | Medium |
| 13 | Preconnect hints (3.5) | 15m | Medium |
| 14 | Canonical tags for archives (3.6) | 30m | Medium |
| 15 | Semantic improvements — section, aside, figure (3.2–3.4) | 2h | Low–Medium |
| 16 | Fix heading misuse (3.1) | 1h | Low |
| 17 | `car-model` meta field + rating system for Reviews (B2–B3) | 3h | Medium (unlocks stars) |
| 18 | WebP support, sitelinks schema, hreflang (4.x) | 4h+ | Low–Medium |

**Editorial guidelines (no code required):**
- EV News posts: minimum 100–150 words of original Bulgarian text in the excerpt field (A2)
- EV Masters posts: minimum 80 words describing the video content in the excerpt field (C2)

**Total for top 9 (schema + core meta):** ~10 hours  
**Total for all proposals:** ~20 hours

---

## Notes

- Items 1.1–1.3 and 2.1–2.2 can be validated immediately in Google's [Rich Results Test](https://search.google.com/test/rich-results) and [Open Graph Debugger](https://developers.facebook.com/tools/debug/).
- Core Web Vitals (LCP, CLS, INP) should be measured in Google Search Console after the JS deferral and lazy-loading changes go live.
- A full SEO plugin (Yoast, Rank Math) can replace proposals 1.1, 1.3, 3.6, and parts of 1.2 if editorial meta-description control via the admin UI is preferred over hardcoded theme functions.
