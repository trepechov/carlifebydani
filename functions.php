<?php

define('EV_NEWS_CATEGORY_ID', 1);
define('EV_MASTERS_CATEGORY_ID', 2);
define('EV_REVIEWS_CATEGORY_ID', 3);
define('NEWS_CATEGORY_ID', 45);

add_theme_support('post-thumbnails');

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css', [], time());
    // ('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css');
});

function register_my_menus()
{
    register_nav_menus(
        array(
            'top-menu' => __('Top Menu'),
            'main-menu' => __('Main Menu'),
            'share-menu' => __('Share Menu'),
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
