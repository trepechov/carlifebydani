<?php
$ev_masters_posts = get_posts(array(
    'numberposts'   => 4,
    'category'      => EV_MASTERS_CATEGORY_ID
));
?><div class="bg-carbon-stripe-white-20">
    <div class="wrapper py-12">
        <div class="flex mb-8 justify-between items-center">
            <h3 class="title">EV Masters</h3>
            <a href="<?php echo get_category_link(EV_MASTERS_CATEGORY_ID) ?>" class="button">Виж всички</a>
        </div>

        <div class="grid gap-8 grid-cols-1 lg:grid-cols-4 lg:place-items-stretch">
            <?php
            for ($i = 0; $i < 4; $i++) {
                get_template_part('template-parts/card-article', 'article',  [
                    'post' => $ev_masters_posts[$i],
                    'small_title' => true
                ]);
            }
            ?>
        </div>
    </div>
</div>