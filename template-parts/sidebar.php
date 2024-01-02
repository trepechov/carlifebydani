<?php
// Get posts with query to use sticky prop
$args  = array(
    'posts_per_page'      => 6,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);
?>
<h4>Избрано за вас</h4>
<div class="grid gap-3 mt-5">
    <?php
    foreach ($query->posts as $post) {
        get_template_part('template-parts/card-article-sidebar', 'article',  array('post' => $post, 'with_category' => true));
    }
    ?>
</div>

<h4 class="mt-12">Най-четени публикации</h4>
<div class="grid gap-3 mt-5">
    <?php
    foreach ($query->posts as $key => $post) {
        get_template_part('template-parts/card-article-sidebar', 'article',  array('post' => $post, 'with_category' => true, 'index' => $key));
    }
    ?>
</div>