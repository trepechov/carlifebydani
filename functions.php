<?php

require_once 'constants.php';

add_theme_support('post-thumbnails');

function wpdocs_carlifebydani_scripts()
{
    wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css');
    wp_enqueue_style('glightbox-css', get_stylesheet_directory_uri() . '/css/glightbox.min.css');
    wp_enqueue_style('cookieconsent-css', get_stylesheet_directory_uri() . '/css/cookieconsent.min.css');
    wp_enqueue_script('gtag', get_stylesheet_directory_uri() . '/js/gtag.js');
    wp_enqueue_script('glightbox', get_stylesheet_directory_uri() . '/js/glightbox.min.js');
    wp_enqueue_script('glightbox-init', get_stylesheet_directory_uri() . '/js/glightbox.init.js', ['glightbox', 'jquery']);
    wp_enqueue_script('cookieconsent', get_stylesheet_directory_uri() . '/js/cookieconsent.min.js', [], '', true);
    wp_enqueue_script('cookieconsent-init', get_stylesheet_directory_uri() . '/js/cookieconsent.init.js', ['cookieconsent'], '', true);
    wp_enqueue_script('ogimageloader-init', get_stylesheet_directory_uri() . '/js/ogimageloader.init.js', ['jquery']);
};
add_action('wp_enqueue_scripts', 'wpdocs_carlifebydani_scripts');


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
            '/<a\s+href\s*=\s*["\'](https?:\/\/(?!' . preg_quote($_SERVER['SERVER_NAME'], '/') . ')[^"\']+)["\'](?![^>]*\srel=)([^>]*)>/iu', '<a href="$1" target="_blank" rel="nofollow"$3>',
            $content
        );
    }

    return $content;
}
add_filter('the_content', 'add_blank_to_links');

//Ninja Forms Custom Background Image
function custom_header_code()
{
    $current_post = get_post();
    $cover_image = get_post_meta($current_post->ID, 'form-image', true);
    if ($cover_image) {
        echo "<style id='ninja_forms_custom_bg'>.post-content .nf-form-wrap {
            @media (width >= 64rem) { background-image: url('$cover_image');} }
        </style>";
    }
}
add_action('wp_head', 'custom_header_code');
