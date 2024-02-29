<?php
$locations = get_nav_menu_locations();
$top_10_menu = wp_get_nav_menu_object($locations['top-10-menu']);
$top_10_menu_items = wp_get_nav_menu_items($top_10_menu->term_id);
?>

<div class="wrapper py-12">
    <div class="flex flex-col mb-8 gap-y-2 justify-between sm:flex-row sm:items-center">
        <h3 class="title flex gap-4 items-center">
            <?php echo get_the_post_thumbnail(TOP_10_PAGE_ID, 'full', [
                'class' => 'h-5 mb-1 w-auto'
            ]); ?>
            Top 10
        </h3>
        <div class="text-xs text-brand-lightgrey sm:text-right">
            Car Life Top 10 е месечна класация.<br />
            Период на класацията <?php echo date('01.m.') . " - "  . date('t.m.Y'); ?>
        </div>
    </div>

    <div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <?php foreach ($top_10_menu_items as $key => $menu_item) {
                get_template_part('template-parts/card-article-top-10', 'article', [
                    'menu_item' => $menu_item,
                    'number' => $key + 1
                ]);
            } ?>
        </div>
    </div>
</div>