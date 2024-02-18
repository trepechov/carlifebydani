<?php

$hero_args  = array(
    'posts_per_page' => 1,
    'ignore_sticky_posts' => 1,
);
$hero_query = new WP_Query($hero_args);

// Get posts with query to use sticky prop
// Get 3 post for 3 featured articles and one additional in case hero post matches
$feaured_args  = array(
    'posts_per_page'      => 4,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$featured_query = new WP_Query($feaured_args);
?>

<div class="relative">
    <div class="absolute h-192 w-full bg-carbon-stripe-white-20">
        <div class="h-full bg-from-black-60-gradient"></div>
    </div>
    <div class="wrapper py-8 relative">
        <!-- Hero article -->
        <article class="relative">
            <div class="w-full lg:w-3/4 lg:rounded-br-8xl lg:border-b-20 lg:border-r-20 lg:pb-5 lg:pr-5 lg:border-white/10">
                <div class="relative lg:rounded-br-6xl cursor-pointer overflow-hidden" onclick="window.location.href='<?php echo get_permalink($hero_query->posts[0]->ID) ?>'">
                    <div class="overlay bg-to-black-80-gradient"></div>
                    <?php get_template_part('template-parts/featured-image', 'featured-image', array('post_id' => $hero_query->posts[0]->ID, 'size' => 'large')); ?>
                </div>
            </div>

            <div class="mb-12 p-8 pb-16 bg-brand-red rounded-br-4xl lg:absolute lg:right-0 lg:top-5 lg:w-2/5">
                <div class="flex items-center text-black gap-2 mb-5">
                    <span class="text-xl font-bold uppercase"><?php echo get_the_category($hero_query->posts[0]->ID)[0]->name ?></span>

                    <span class="w-1.5 h-1.5 bg-black/50 rounded"></span>

                    <span><?php echo date('d.m.Y', strtotime($hero_query->posts[0]->post_date)) ?></span>
                </div>
                <h2>
                    <a href='<?php echo get_permalink($hero_query->posts[0]->ID) ?>' class="link-transition hover:text-black line-clamp-4"><?php echo $hero_query->posts[0]->post_title ?></a>
                </h2>
            </div>
        </article>

        <!-- Featured articles -->
        <div class="my-12">
            <div class="flex mb-8 justify-between items-center">
                <h3 class="title">Избрано за вас</h3>
            </div>
            <div class="grid gap-8 grid-cols-1 lg:grid-cols-3 items-stretch justify-items-stretch">
                <?php
                $i = 0;
                foreach ($featured_query->posts as $post) {
                    if ($i == 3) {
                        break;
                    }
                    if ($post->ID == $hero_query->posts[0]->ID) {
                        continue;
                    }
                    get_template_part('template-parts/card-article', 'article',  array('post' => $post, 'title_size' => 'normal', 'with_category' => true));
                    $i++;
                }
                ?>
            </div>
        </div>
    </div>
</div>