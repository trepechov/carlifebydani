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

    <div class="container py-8 relative">

        <h2 class="title text-3xl/8 mb-8 mt-6' ?>"><?php echo $current_post->post_title; ?></h2>
        <p class="mb-8 text-[1.0625rem]"><?php echo $current_post->post_excerpt ?></p>
        <div class="post-content">
            <?php
            echo apply_filters('the_content', $current_post->post_content);
            ?>
        </div>
    </div>

    <?php get_template_part('template-parts/find-us'); ?>
    <?php get_template_part('template-parts/footer'); ?>