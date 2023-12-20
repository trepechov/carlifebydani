<?php
// Get posts with query to use sticky prop
$args  = array(
    'posts_per_page'      => 4,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);
?>

<div class="bg-pattern">
    <div class="bg-from-black-gradien">
        <div class="container py-6">
            <!-- Hero article -->
            <article class="relative">
                <div class="w-full lg:w-3/4 lg:rounded-br-8xl lg:border-b-16 lg:border-r-16 lg:pb-4 lg:pr-4 lg:border-white/10">
                    <div class="relative lg:rounded-br-6xl overflow-hidden">
                        <div class="overlay bg-to-black-gradient"></div>
                        <?php echo get_the_post_thumbnail($query->posts[0]->ID, 'large', array('class' => '')); ?>
                    </div>
                </div>

                <div class="mb-12 p-8 bg-brand-red rounded-br-4xl lg:absolute lg:right-0 lg:top-5 lg:w-1/3">
                    <div class="flex items-center text-black gap-2 mb-5">
                        <span class="text-xl font-bold"><?php echo date('d.m.Y', strtotime($query->posts[0]->post_date)) ?></span>

                        <span class="text-2xl opacity-50">&#x2022;</span>

                        <span><?php echo get_the_category($query->posts[0]->ID)[0]->name ?></span>
                    </div>
                    <h2>
                        <a href='<?php echo get_permalink($query->posts[0]->ID) ?>' class="link-transition hover:text-black"><?php echo $query->posts[0]->post_title ?></a>
                    </h2>
                </div>
            </article>

            <div class="mt-8">
                <h3 class="border-l-8 p-3 mb-8 border-brand-red uppercase">Избрано за вас</h3>
                <div class="lg:grid lg:grid-cols-3 lg:gap-8 items-stretch justify-items-stretch">
                    <?php
                    for ($i = 1; $i < 4; $i++) {
                        // Reemove this once have enought posts
                        if (!isset($query->posts[$i])) {
                            continue;
                        }
                        get_template_part('template-parts/card-article', 'article',  array('post' => $query->posts[$i], 'title_size' => 'normal'));
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>