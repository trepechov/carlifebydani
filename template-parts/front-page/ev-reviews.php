<?php
$ev_reviews_posts = get_posts(array(
    'numberposts'   => 8,
    'category'      => EV_REVIEWS_CATEGORY_ID
));
?><div>
    <div class="wrapper py-12">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="title">EV Reviews</h3>
            <a href="<?php echo get_category_link(EV_REVIEWS_CATEGORY_ID) ?>" class="button">Виж всички</a>
        </div>

        <div class="grid gap-8 grid-cols-1 lg:grid-cols-4 lg:place-items-stretch">
            <?php
            for ($i = 0; $i < 8; $i++) {
                // Reemove this once have enought posts
                if (!isset($ev_reviews_posts[$i])) {
                    continue;
                }
                get_template_part('template-parts/card-article', 'article',  ['post' => $ev_reviews_posts[$i], 'title_size' => 'small']);
            }
            ?>
        </div>
    </div>
</div>