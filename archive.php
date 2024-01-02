<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


$current_category = get_the_category()[0];
$bread_crumbs = array(
    (object) [
        'label' => 'Начало',
        'link' => '/',
    ],
    (object) [
        'label' => $current_category->name,
        'link' => get_category_link($current_category->term_id),
    ],
);


get_template_part('template-parts/header');
?>


<div class="bg-carbon-stripe-white-20">
    <div class="bg-grey-stripe-gradient">
        <div class="bg-from-black-gradient">
            <div class="container py-8">

                <?php get_template_part('template-parts/bread-crumbs', 'bread_crumbs', array('bread_crumbs' => $bread_crumbs)); ?>

                <h3 class="title mt-6 mb-8"><?php echo $current_category->name ?></h2>
                    <?php echo tag_description() ?>

                    <div class="grid grid-cols-3 gap-8">
                        <div class="col-span-2 grid gap-8">
                            <?php
                            get_template_part('template-parts/archive/main', 'archive_main', array('category' => $current_category));
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
    </div>
</div>

<?php get_template_part('template-parts/find-us'); ?>
<?php get_template_part('template-parts/footer'); ?>