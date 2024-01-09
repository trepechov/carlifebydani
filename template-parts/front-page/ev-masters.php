<?php
$ev_masters_posts = get_posts(array(
    'numberposts'   => 4,
    'category'      => EV_MASTERS_CATEGORY_ID
));
?><div class="bg-carbon-stripe-white-20">
    <div class="container py-12">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="title">EV Masters</h3>
            <a href="<?php echo get_category_link(EV_MASTERS_CATEGORY_ID) ?>" class="button">Виж всички</a>
        </div>


        <div class="lg:grid lg:grid-cols-4 lg:gap-8 lg:place-items-stretch">
            <?php
            for ($i = 0; $i < 4; $i++) {
                // Reemove this once have enought posts
                if (!isset($ev_masters_posts[$i])) {
                    continue;
                }
                get_template_part('template-parts/card-article', 'article',  ['post' => $ev_masters_posts[$i], 'title_size' => 'small']);
            }
            ?>
        </div>
    </div>
</div>