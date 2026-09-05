<?php
$class = isset($args['class']) ? $args['class'] : '';
$attr  = array('class' => $class);

// Pass 'eager' => true for the single above-the-fold hero image (currently only
// the front-page hero in featured-posts.php). `skip-lazy` stops WP Smush's JS
// lazy-loader from swapping src→data-src on it — GTmetrix (2026-09-05, report
// 7ksv4vEw) traced 1.4s of the homepage LCP time to exactly that swap being
// gated behind smush-lazy-load.min.js executing before the real image request
// could start. fetchpriority hints the browser's preload scanner the same way
// a manual <link rel=preload> would, without a second, easy-to-drift URL to
// keep in sync in <head>. See docs/plans/2026-09-05-002-fix-onsite-seo-performance-plan.md.
if (!empty($args['eager'])) {
    $attr['class']        = trim($class . ' skip-lazy');
    $attr['loading']      = 'eager';
    $attr['fetchpriority'] = 'high';
}

if (has_post_thumbnail($args['post_id'])) {
    echo get_the_post_thumbnail($args['post_id'], $args['size'], $attr);
} else {
    $eager_attrs = !empty($args['eager']) ? ' loading="eager" fetchpriority="high"' : '';
    echo '<img src="' . get_stylesheet_directory_uri() . '/images/noimage' . (isset($args['size']) && $args['size'] == 'medium' ? '-640x360' : '') . '.jpg" alt="" class="' . esc_attr($attr['class']) . '"' . $eager_attrs . ' />';
}
