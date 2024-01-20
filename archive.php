<?php

/**
 * The category/autors template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


$current_category = get_the_category()[0];

$bread_crumbs = [
    [
        'label' => 'Начало',
        'link' => '/',
    ],
    [
        'label' => $current_category->name,
        'link' => get_category_link($current_category->term_id),
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

        <h2 class="title text-3xl/8 mt-6 mb-8"><?php echo $current_category->name ?></h2>
        <?php echo tag_description() ?>

        <div class="grid grid-cols-3 gap-8">
            <div class="pb-8 col-span-2 border-b-2 border-brand-button">
                <?php
                get_template_part('template-parts/archive/main', 'archive_main', array('category' => $current_category));
                ?>
            </div>
            <div class="col-span-1 flex flex-col gap-12">
                <?php
                get_template_part('template-parts/sidebar');
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/find-us'); ?>
<?php get_template_part('template-parts/footer'); ?>