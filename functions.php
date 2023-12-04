<?php
	add_theme_support('post-thumbnails');

    add_action('wp_enqueue_scripts', function(){
        wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css', [], time());
        // ('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css');
    }); 

    function register_my_menus() {
        register_nav_menus(
            array(
                'top-menu' => __( 'Top Menu' ),
                'main-menu' => __( 'Main Menu' ),
            )
        );
    }
    add_action( 'init', 'register_my_menus' );
?>