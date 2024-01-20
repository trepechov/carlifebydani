<?php
$catetgory_posts = get_posts([
    'numberposts' => 999,       //todo: pagination
    'category' => $args['category']->term_id,
]);
?>
<div class="flex flex-col gap-8">
    <?php
    if (count($catetgory_posts) > 0) {
        foreach ($catetgory_posts as $post) {
            get_template_part('template-parts/card-article-horizontal', 'article', [
                'post' => $post,
            ]);
        }
    } else {
    ?>
        <p>Няма намерни публикации. :(</p>
    <?php
    }
    ?>
</div>