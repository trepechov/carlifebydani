<?php
// Get posts with query to use sticky prop
$args  = array(
    'posts_per_page'      => 6,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);


$top_10_post = get_post(TOP_10_PAGE_ID);

$meta_data = get_post_meta(TOP_10_PAGE_ID, 'top-10-order', true);

$top_10_posts = get_posts([
    'numberposts' => 10,
    'include' => explode(',', $meta_data),
]);

?>
<h4>Избрано за вас</h4>
<div class="grid gap-3 mt-5">
    <?php
    foreach ($query->posts as $post) {
        get_template_part('template-parts/card-article-sidebar', 'article', array('post' => $post, 'with_category' => true));
    }
    ?>
</div>

<h4 class="mt-12 flex gap-4 items-center">
    <?php echo get_the_post_thumbnail(TOP_10_PAGE_ID, 'full', array('class' => 'h-4 mb-0.5 w-auto')); ?>
    Top 10
</h4>
<div class="grid gap-3 mt-5">
    <?php
    foreach ($top_10_posts as $key => $post) {
        get_template_part('template-parts/card-article-sidebar', 'article', array('post' => $post, 'with_category' => true, 'number' => $key + 1));
    }
    ?>
</div>