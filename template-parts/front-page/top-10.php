<?php
$meta_data = get_post_meta(TOP_10_PAGE_ID, 'top-10-order', true);

$top_10_posts = get_posts([
    'numberposts' => 10,
    'include' => explode(',', $meta_data),
]);
?>

<div class="container py-12">
    <div class="flex mb-8 justify-between items-center">
        <h3 class="title flex gap-4 items-center">
            <?php echo get_the_post_thumbnail(TOP_10_PAGE_ID, 'full', array('class' => 'h-5 mb-1 w-auto')); ?>
            Top 10
        </h3>
    </div>

    <div class="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($top_10_posts as $key => $post) {
                get_template_part('template-parts/card-article-top-10', 'article', array('post' => $post, 'number' => $key + 1));
            } ?>
        </div>
    </div>
</div>