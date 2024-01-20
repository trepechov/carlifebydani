<?php

/**
 * The tag template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

$tag = get_queried_object();

// $current_category = get_the_category()[0];

$bread_crumbs = [
    [
        'label' => 'Начало',
        'link' => '/',
    ],
    [
        'label' => $tag->name,
        'link' => get_category_link($tag->term_id),
    ],
];

get_template_part('template-parts/header');
?>


<div class="relative">
    <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
        <div class="h-full bg-from-black-60-gradient"></div>
    </div>
    <div class="container py-8 relative">

        <?php get_template_part('template-parts/bread-crumbs', 'bread_crumbs', array('bread_crumbs' => $bread_crumbs)); ?>

        <h2 class="title text-3xl/8 mt-6 mb-8">#<?php echo $tag->name ?></h2>
        <div class="mb-8"><?php echo tag_description() ?></div>

        <div class="grid grid-cols-3 gap-8">
            <div class="pb-8 col-span-2 border-b-2 border-brand-button">
                <?php
                get_template_part('template-parts/tag/main', 'tag_main', array('tag' => $tag));
                ?>
            </div>
            <div class="col-span-1">
                <?php
                get_template_part('template-parts/sidebar');
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/find-us'); ?>
<?php get_template_part('template-parts/footer'); ?>