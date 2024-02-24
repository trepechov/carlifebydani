<?php
$ev_news_posts = get_posts(array(
    'numberposts'   => 7,
    'category'      => EV_NEWS_CATEGORY_ID
));
?><div class="bg-carbon-stripe-white-20">
    <div class="wrapper py-12">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="title">EV News</h3>
            <a href="<?php echo get_category_link(EV_NEWS_CATEGORY_ID) ?>" class="button">Виж всички</a>
        </div>

        <div class="mb-8 grid gap-8 grid-cols-1 lg:grid-cols-3 items-stretch justify-items-stretch">
            <?php
            for ($i = 0; $i < 3; $i++) {
                get_template_part('template-parts/card-article', 'article',  [
                    'post' => $ev_news_posts[$i]
                ]);
            }
            ?>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-4 lg:place-items-stretch">
            <?php
            for ($i = 3; $i < 7; $i++) {
                get_template_part('template-parts/card-article', 'article',  [
                    'post' => $ev_news_posts[$i],
                    'small_title' => true
                ]);
            }
            ?>
        </div>
    </div>
</div>