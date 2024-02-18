<?php

/**
 * The single (post) template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header');


$current_post = get_post();

$current_category = get_the_category()[0];

$cover_image = get_post_meta($current_post->ID, 'cover-image', true);

$tags = get_the_tags();

$bread_crumbs = [
    [
        'label' => 'Начало',
        'link' => '/',
    ],
    [
        'label' => $current_category->name,
        'link' => get_category_link($current_category->term_id),
    ],
    [
        'label' => $current_post->post_title,
        'link' => get_permalink(),
    ],
];

?>

<div class="relative">

    <?php if (!empty($cover_image)) { ?>
        <div class="absolute h-192 w-full bg-cover bg-center bg-no-repeat" style="background-image: url(<?php echo $cover_image; ?>);">
            <div class="h-2/5 bg-from-black-80-gradient opacity-75"></div>
            <div class="h-3/5 bg-to-black-gradient"></div>
        </div>
    <?php } else { ?>
        <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
            <div class="h-full bg-from-black-60-gradient"></div>
        </div>
    <?php } ?>

    <div class="wrapper py-8 relative">
        <?php get_template_part('template-parts/bread-crumbs', 'bread_crumbs', array('bread_crumbs' => $bread_crumbs)); ?>

        <h1 class="title text-3xl/8 mb-8 font-bold <?php echo !empty($cover_image) ? 'mt-112' : 'mt-6' ?>"><?php echo $current_post->post_title; ?></h1>
        <p class="mb-8 text-[1.0625rem]"><?php echo $current_post->post_excerpt ?></p>
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="col-span-2 post-content">
                <?php
                echo apply_filters('the_content', $current_post->post_content);
                ?>
            </div>
            <div class="hidden lg:flex lg:col-span-1 lg:flex-col lg:gap-12">
                <?php get_template_part('template-parts/single/sidebar', 'single-sidebar', array('post' => $current_post, 'category' => $current_category, 'tags' => $tags)); ?>
                <?php get_template_part('template-parts/sidebar'); ?>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/single/more-category-posts', 'more-category-posts', array('category_id' => $current_category->term_id)); ?>

    <?php get_template_part('template-parts/find-us'); ?>
    <?php get_template_part('template-parts/footer'); ?>