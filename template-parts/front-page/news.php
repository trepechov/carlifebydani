<?php
$news_posts = get_posts([
    'numberposts' => 11,
    'category' => NEWS_CATEGORY_ID,
]); ?><div class="bg-carbon-stripe-white-20">
    <div class="wrapper py-12">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="title">Публикации</h3>
            <a href="<?php echo get_category_link(NEWS_CATEGORY_ID); ?>" class="button">Виж всички</a>
        </div>

        <div class="mb-8 grid gap-8 grid-cols-1 lg:grid-cols-3 items-stretch justify-items-stretch">
            <?php for ($i = 0; $i < 3; $i++) {
                // Reemove this once have enought posts
                if (!isset($news_posts[$i])) {
                    continue;
                }
                get_template_part('template-parts/card-article', 'article', [
                    'post' => $news_posts[$i],
                    'title_size' => 'normal',
                ]);
            } ?>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-4 lg:place-items-stretch">
            <?php for ($i = 3; $i < 11; $i++) {
                // Reemove this once have enought posts
                if (!isset($news_posts[$i])) {
                    continue;
                }
                get_template_part('template-parts/card-article', 'article', [
                    'post' => $news_posts[$i],
                    'title_size' => 'small',
                ]);
            } ?>
        </div>
    </div>
</div>
</div>