<?php
$catetgory_posts = get_posts([
    'numberposts' => 999,       //todo: pagination
    'tag' => $args['tag']->slug,
]);
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
<div class="h-1 mt-12 bg-brand-button"></div>