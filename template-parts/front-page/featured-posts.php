<?php
// Get posts with query to use sticky prop
$args  = array(
    'posts_per_page'      => 4,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);
?>

<div class="bg-carbon-stripe-white-20">
    <div class="bg-grey-stripe-gradient">
        <div class="bg-from-black-gradient">
            <div class="container py-8">
                <!-- Hero article -->
                <article class="relative">
                    <div class="w-full lg:w-3/4 lg:rounded-br-8xl lg:border-b-20 lg:border-r-20 lg:pb-5 lg:pr-5 lg:border-white/10">
                        <div class="relative lg:rounded-br-6xl overflow-hidden">
                            <div class="overlay bg-to-black-gradient"></div>
                            <?php echo get_the_post_thumbnail($query->posts[0]->ID, 'large', array('class' => '')); ?>
                        </div>
                    </div>

                    <div class="mb-12 p-8 pb-16 bg-brand-red rounded-br-4xl lg:absolute lg:right-0 lg:top-5 lg:w-2/5">
                        <div class="flex items-center text-black gap-2 mb-5">
                            <span class="text-xl font-bold uppercase"><?php echo get_the_category($query->posts[0]->ID)[0]->name ?></span>

                            <span class="w-1.5 h-1.5 bg-black/50 rounded"></span>

                            <span><?php echo date('d.m.Y', strtotime($query->posts[0]->post_date)) ?></span>
                        </div>
                        <h2>
                            <a href='<?php echo get_permalink($query->posts[0]->ID) ?>' class="link-transition hover:text-black line-clamp-4"><?php echo $query->posts[0]->post_title ?></a>
                        </h2>
                    </div>
                </article>

                <!-- Featured articles -->
                <div class="mt-12">
                    <div class="flex mb-8 justify-between items-center">
                        <h3 class="title">Избрано за вас</h3>
                    </div>
                    <div class="lg:grid lg:grid-cols-3 lg:gap-8 items-stretch justify-items-stretch">
                        <?php
                        for ($i = 1; $i < 4; $i++) {
                            // Reemove this once have enought posts
                            if (!isset($query->posts[$i])) {
                                continue;
                            }
                            get_template_part('template-parts/card-article', 'article',  array('post' => $query->posts[$i], 'title_size' => 'normal', 'with_category' => true));
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>