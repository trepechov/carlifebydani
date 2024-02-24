<?php
$category_posts = get_posts(array(
    'numberposts'   => 3,
    'orderby'       => 'rand',
    'category'      => $args['category_id']
));
?>
<div class="wrapper">
    <div class="flex mb-8 justify-between items-center">
        <h3 class="title">Още от тази категория</h3>
    </div>

    <div class="mb-8 grid gap-8 grid-cols-1 lg:grid-cols-3 items-stretch justify-items-stretch">
        <?php
        for ($i = 0; $i < 3; $i++) {
            get_template_part('template-parts/card-article', 'article',  [
                'post' => $category_posts[$i]
            ]);
        }
        ?>
    </div>
</div>