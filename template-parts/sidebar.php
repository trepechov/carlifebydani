<?php
// Get posts with query to use sticky prop
$args  = array(
    'posts_per_page'      => 6,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);

$locations = get_nav_menu_locations();
$top_10_menu = wp_get_nav_menu_object($locations['top-10-menu']);
$top_10_menu_items = wp_get_nav_menu_items($top_10_menu->term_id);
$meta_data = get_post_meta(TOP_10_PAGE_ID, 'top-10-order', true);

?>
<div>
    <h4>Избрано за вас</h4>
    <div class="flex flex-col gap-3 mt-5">
        <?php
        foreach ($query->posts as $post) {
            get_template_part('template-parts/card-article-sidebar', 'article', [
                'post' => $post,
                'with_category' => true
            ]);
        }
        ?>
    </div>
</div>

<div>
    <h4 class="flex gap-4 items-center">
        <?php echo get_the_post_thumbnail(TOP_10_PAGE_ID, 'full', [
            'class' => 'h-4 mb-0.5 w-auto'
        ]); ?>
        <?php echo get_the_title(TOP_10_PAGE_ID); ?>
    </h4>
    <div class="flex flex-col gap-3 mt-5">
        <?php foreach ($top_10_menu_items as $key => $menu_item) {
            get_template_part('template-parts/card-article-top-10', 'article', [
                'menu_item' => $menu_item,
                'number' => $key + 1,
                'is_sidebar' => true
            ]);
        } ?>
    </div>
</div>