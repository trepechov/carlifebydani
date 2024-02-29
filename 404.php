<?php

/**
 * The page template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header');


$current_post = get_post();


?>

<div class="relative">


    <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
        <div class="h-full bg-from-black-60-gradient"></div>
    </div>

    <div class="wrapper py-8 relative">

        <!-- <h1 class="title text-3xl/8 font-bold mb-8 mt-6' ?>">404</h1> -->
        <div class="post-content">
            <h1 class="mt-16 text-7xl text-center text-brand-lightgrey">404</h1>
            <h3 class="text-center !mt-0">Страницата не е намерена</h3>


        </div>
        <ul class="mt-8 list-none flex flex-wrap gap-2 text-sm justify-center">
            <?php
            $locations = get_nav_menu_locations();
            $top_tags_menu = wp_get_nav_menu_object($locations['top-tags-menu']);
            $top_tag_menu_items = wp_get_nav_menu_items($top_tags_menu->term_id);

            foreach ($top_tag_menu_items as $menuItem) { ?>
                <li>
                    <a href="<?php echo home_url('/') . '?s=' . $menuItem->post_name; ?>" class="text-brand-lightgrey hover:text-brand-red"><?php echo substr($menuItem->title, 1); ?></a>
                </li>
            <?php
            }
            ?>
        </ul>
    </div>

    <?php get_template_part('template-parts/find-us'); ?>
    <?php get_template_part('template-parts/footer'); ?>