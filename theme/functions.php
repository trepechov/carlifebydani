<?php

require_once 'constants.php';

add_theme_support('post-thumbnails');

function wpdocs_carlifebydani_scripts()
{
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Sofia+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,200,1,200&display=swap', [], null);
    wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css');
    wp_enqueue_style('glightbox-css', get_stylesheet_directory_uri() . '/css/glightbox.min.css');
    wp_enqueue_style('cookieconsent-css', get_stylesheet_directory_uri() . '/css/cookieconsent.min.css');
    wp_enqueue_script('gtag', get_stylesheet_directory_uri() . '/js/gtag.js', [], '', true);
    wp_enqueue_script('glightbox', get_stylesheet_directory_uri() . '/js/glightbox.min.js', [], '', true);
    wp_enqueue_script('glightbox-init', get_stylesheet_directory_uri() . '/js/glightbox.init.js', ['glightbox', 'jquery'], '', true);
    wp_enqueue_script('cookieconsent', get_stylesheet_directory_uri() . '/js/cookieconsent.min.js', [], '', true);
    wp_enqueue_script('cookieconsent-init', get_stylesheet_directory_uri() . '/js/cookieconsent.init.js', ['cookieconsent'], '', true);
    wp_enqueue_script('ev-news-tracking', get_stylesheet_directory_uri() . '/js/ev-news-tracking.js', [], '', true);
    wp_enqueue_script('ev-news-voting', get_stylesheet_directory_uri() . '/js/ev-news-voting.js', [], '', true);
    wp_enqueue_script('ogimageloader-init', get_stylesheet_directory_uri() . '/js/ogimageloader.init.js', ['jquery'], '', true);
    wp_localize_script('ogimageloader-init', 'ogProxy', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('fetch_og_image_nonce'),
    ]);
};
add_action('wp_enqueue_scripts', 'wpdocs_carlifebydani_scripts');

/*
 * Preconnect to third-party origins the page always talks to, so the
 * DNS/TLS handshake happens in parallel with HTML parsing instead of
 * only starting once the browser reaches the script/link tag.
 */
add_filter('wp_resource_hints', function ($hints, $relation_type) {
    if ($relation_type === 'preconnect') {
        $hints[] = ['href' => 'https://fonts.googleapis.com'];
        $hints[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin'];
        $hints[] = ['href' => 'https://www.googletagmanager.com'];
    }
    return $hints;
}, 10, 2);

/*
 * Move theme scripts to load with `defer` so they never block HTML
 * parsing. Scripts are already enqueued in dependency order and `defer`
 * preserves execution order, so glightbox-init/ogimageloader-init still
 * run after their jquery/glightbox dependencies.
 *
 * jquery-core/jquery-migrate are deliberately excluded: they're shared
 * WP handles that any plugin (e.g. Ninja Forms) may depend on, and
 * plugin scripts aren't deferred by this filter. Deferring jQuery itself
 * would make it load after those non-deferred plugin scripts run,
 * breaking anything that expects the jQuery global to already exist.
 * This holds regardless of which specific plugins are installed.
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    $theme_handles = [
        'gtag',
        'glightbox',
        'glightbox-init',
        'ogimageloader-init',
        'cookieconsent',
        'cookieconsent-init',
        'ev-news-tracking',
        'ev-news-voting',
    ];
    if (in_array($handle, $theme_handles, true) && strpos($tag, ' defer') === false) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);


function register_my_menus()
{
    register_nav_menus(
        array(
            'top-menu' => __('Top Menu'),
            'top-tags-menu' => __('Top Tags Menu'),
            'main-menu' => __('Main Menu'),
            'share-menu' => __('Share Menu'),
            'top-10-menu' => __('Top 10'),
            'footer-menu' => __('Footer Menu'),
            'bottom-menu' => __('Bottom Menu'),
        )
    );
}
add_action('init', 'register_my_menus');


/*
 * WordPress: Remove unwonted image sizes.
 * In this code I remove the three sizes medium_large, 1536x1536, 2048x2048
 * See full article: https://bloggerpilot.com/en/disable-wordpress-image-sizes/
 */

add_filter('intermediate_image_sizes', function ($sizes) {
    return array_diff($sizes, ['medium_large']);  // Medium Large (768 x 0)
});

add_action('init', 'j0e_remove_large_image_sizes');
function j0e_remove_large_image_sizes()
{
    remove_image_size('1536x1536');             // 2 x Medium Large (1536 x 1536)
    remove_image_size('2048x2048');             // 2 x Large (2048 x 2048)
}

// Use jquery selectroin glightbox.initjs instead of this
// function glightbox_class($content)
// {
//     // global $post;
//     $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
//     $replacement = '<a$1 class="glightbox" href=$2$3.$4$5$6>';
//     $content = preg_replace($pattern, $replacement, $content);
//     return $content;
// }
// add_filter('the_content', 'glightbox_class');



function add_tag_links_to_content($content)
{

    if (is_single()) {
        $post_tags = get_the_tags();

        if ($post_tags) {
            while ($tag = array_pop($post_tags)) {      // Reverse loop, when have tags like #Renault #Renault 5, link the extended tag first
                $tag_link = get_tag_link($tag->term_id);
                $tag_link_html = '<a href="' . esc_url($tag_link) . '">' . esc_html($tag->name) . '</a>';
                $content = preg_replace(
                    '/(<((?!a|td|strong|h2|h3|figcaption)[^>]*)>[^<]*?\b)' . preg_quote($tag->name, '/') . '(\b.*?<\/[^>]*>)/iu',
                    '$1' . $tag_link_html . '$3',
                    $content,
                    5
                );
            }
        }
    }

    return $content;
}
add_filter('the_content', 'add_tag_links_to_content');


function add_blank_to_links($content)
{

    if (is_single() || is_page()) {
        $content = preg_replace(
            '/<a\s+href\s*=\s*["\'](https?:\/\/(?!' . preg_quote($_SERVER['SERVER_NAME'], '/') . ')[^"\']+)["\'](?![^>]*\srel=)([^>]*)>/iu',
            '<a href="$1" target="_blank" rel="nofollow"$3>',
            $content
        );
    }

    return $content;
}
add_filter('the_content', 'add_blank_to_links');

/**
 * Returns true when $url is safe to fetch server-side:
 * HTTPS-only, resolves to a public IPv4 (blocks private/reserved ranges and IPv6).
 */
function carlifebydani_is_safe_url( string $url ): bool {
    if ( wp_parse_url( $url, PHP_URL_SCHEME ) !== 'https' ) {
        return false;
    }
    $host = wp_parse_url( $url, PHP_URL_HOST );
    if ( ! $host ) {
        return false;
    }
    $ip = gethostbyname( $host );
    if (
        $ip === $host ||
        filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === false ||
        filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false
    ) {
        return false;
    }
    return true;
}

add_action('wp_ajax_nopriv_fetch_og_image', 'fetch_og_image_proxy');
add_action('wp_ajax_fetch_og_image',         'fetch_og_image_proxy');

function fetch_og_image_proxy() {
    check_ajax_referer('fetch_og_image_nonce', 'nonce');

    $url    = isset($_GET['url']) ? esc_url_raw(wp_unslash($_GET['url'])) : '';
    $scheme = wp_parse_url($url, PHP_URL_SCHEME);
    $host   = wp_parse_url($url, PHP_URL_HOST);

    // Only HTTPS — blocks file://, php://, gopher://, dict://, http:// etc.
    if ($scheme !== 'https' || !$host) {
        wp_send_json_error('Only HTTPS URLs allowed', 400);
    }

    if ( ! carlifebydani_is_safe_url( $url ) ) {
        wp_send_json_error('Disallowed target', 403);
    }

    // Fetch with wp_remote_get — no cookies, no auth headers.
    // Allow up to 3 redirects (canonical URLs, trailing-slash redirects, etc.).
    // SSRF is checked on the initial URL; standard public-web redirects are safe.
    $response = wp_remote_get($url, [
        'timeout'             => 5,
        'redirection'         => 3,
        'limit_response_size' => 102400,
        'user-agent'          => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        'cookies'             => [],
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('Fetch failed', 502);
    }

    $status = wp_remote_retrieve_response_code($response);
    if ($status < 200 || $status >= 300) {
        wp_send_json_error('Upstream error: ' . (int) $status, 502);
    }

    wp_send_json(['contents' => wp_remote_retrieve_body($response)]);
}

//Ninja Forms Custom Background Image
function custom_header_code()
{
    $current_post = get_post();

    if ( ! $current_post || ($current_post->post_type !== 'page' && $current_post->post_type !== 'post') ) {
        return;
    }
    $cover_image = get_post_meta($current_post->ID, 'form-image', true);
    if ($cover_image) {
        echo "<style id='ninja_forms_custom_bg'>.post-content .nf-form-wrap {
            @media (width >= 64rem) { background-image: url('" . esc_url($cover_image) . "');} }
        </style>";
    }
}
add_action('wp_head', 'custom_header_code');
