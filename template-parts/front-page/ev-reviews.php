<?php
$ev_reviews_posts = get_posts(array(
    'numberposts'   => 8,
    'category'      => EV_REVIEWS_CATEGORY_ID
));
?><div class="bg-pattern">
    <div class="container py-6">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="border-l-8 p-3 border-brand-red">EV Reviews</h3>
            <a href="<?php echo get_category_link(EV_REVIEWS_CATEGORY_ID) ?>" class="button">Виж всички</a>
        </div>

        <div class="mb-8 lg:grid lg:grid-cols-4 lg:gap-8 lg:place-items-stretch">
            <?php
            for ($i = 0; $i < 7; $i++) {
                // Reemove this once have enought posts
                if (!isset($ev_reviews_posts[$i])) {
                    continue;
                }
                get_template_part('template-parts/cart-article', 'article',  array('post' => $ev_reviews_posts[$i], 'title_size' => 'small'));
            }
            ?>
        </div>
    </div>
</div>