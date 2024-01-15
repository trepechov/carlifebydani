<?php

/**
 * The main template file
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
            <div class="h-2/5 bg-from-black-gradient opacity-60"></div>
            <div class="h-3/5 bg-to-black-gradient"></div>
        </div>
    <?php } else { ?>
        <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
            <div class="h-full bg-from-black-60-gradient"></div>
        </div>
    <?php } ?>

    <div class="container py-8 relative">

        <?php get_template_part('template-parts/bread-crumbs', 'bread_crumbs', array('bread_crumbs' => $bread_crumbs)); ?>

        <h2 class="title text-3xl/8 mb-8 <?php echo !empty($cover_image) ? 'mt-112' : 'mt-6' ?>"><?php echo $current_post->post_title; ?></h2>
        <p class="mb-8 text-[1.0625rem"><?php echo $current_post->post_excerpt ?></p>
        <div class="grid grid-cols-3 gap-8">
            <div class="col-span-2 post-content">
                <?php
                echo apply_filters('the_content', $current_post->post_content);
                ?>

            </div>
            <div class="col-span-1">
                <div class="mb-12 p-5 pb-8 bg-brand-red rounded-br-4xl">
                    <p class="text-black">Категория</p>
                    <h5><a href="<?php echo get_category_link($current_category->term_id) ?>"><?php echo $current_category->name; ?></a></h5>
                    <p class="text-black mt-2">Дата на публикуване</p>
                    <h5><?php echo date("d.m.Y", strtotime($current_post->post_date)) ?></h5>
                    <p class="text-black mt-2">Автор</p>
                    <h5>
                        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID'))  ?>"><?php echo get_the_author(); ?></a>
                    </h5>

                    <?php
                    if ([] != $tags) {
                    ?>
                        <p class="text-black mt-2">Тагове</p>
                        <div class="flex flex-wrap gap-x-2">
                            <?php
                            foreach ($tags as $tag) {
                                echo '<a href="' . get_tag_link($tag->term_id) . '"><h5>#' . $tag->name . '</h5></a>';
                            }
                            ?>
                        </div>

                    <?php
                    }
                    ?>
                </div>
                <?php get_template_part('template-parts/sidebar'); ?>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/find-us'); ?>
    <?php get_template_part('template-parts/footer'); ?>