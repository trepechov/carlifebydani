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
        <div class="absolute h-192 w-full bg-size-7/8 bg-top sm:bg-size-5/4 lg:bg-cover lg:bg-center bg-no-repeat" style="background-image: url(<?php echo $cover_image; ?>);">
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

        <h1 class="title text-3xl/8 mb-8 font-bold <?php echo !empty($cover_image) ? 'mt-80 lg:mt-96' : 'mt-6' ?>"><?php echo $current_post->post_title; ?></h1>

        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="col-span-3">
                <div class="mb-4 flex flex-wrap md:flex-nowrap gap-4 md:gap-8 lg:hidden">
                    <div>
                        <p class="text-sm text-brand-lightgrey">Категория</p>
                        <h5 class="text-[1.0625rem]"><a href="<?php echo get_category_link($current_category->term_id) ?>"><?php echo $current_category->name; ?></a></h5>
                    </div>

                    <div>
                        <p class="text-sm text-brand-lightgrey whitespace-nowrap">Дата на публикуване</p>
                        <h5 class="text-[1.0625rem]"><?php echo date("d.m.Y", strtotime($current_post->post_date)) ?></h5>
                    </div>

                    <div>
                        <p class="text-sm text-brand-lightgrey">Автор</p>
                        <h5 class="text-[1.0625rem]"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" class="hover:underline"><?php echo get_the_author(); ?></a></h5>
                    </div>

                    <?php
                    if ([] != $tags) {
                    ?>
                        <div>
                            <p class="text-sm text-brand-lightgrey">Тагове</p>
                            <div class="flex flex-wrap gap-x-2">
                                <?php
                                foreach ($tags as $tag) {
                                    echo '<a href="' . get_tag_link($tag->term_id) . '" class="hover:underline"><h5 class="text-[1.0625rem]">#' . $tag->name . '</h5></a>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="mb-4 lg:mb-0 post-content"><?php echo apply_filters('the_content', $current_post->post_excerpt); ?></div>
            </div>
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