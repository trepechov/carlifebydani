<?php
$args  = array(
    'posts_per_page'      => 10,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);
?>

<div class="bg-pattern ">
    <div class="bg-from-black-gradien">
        <div class="container py-6">
            <!-- Hero article -->
            <article class="relative">
                <div class="w-full lg:w-3/4 lg:rounded-br-8xl lg:border-b-16 lg:border-r-16 lg:pb-4 lg:pr-4 lg:border-white/10">
                    <div class="relative lg:rounded-br-6xl overflow-hidden">
                        <div class="overlay bg-to-black-gradient"></div>
                        <?php echo get_the_post_thumbnail($query->posts[0]->ID, array(1024, 1024), array('class' => '')); ?>
                    </div>
                </div>


                <div class="mb-12 p-8 bg-brand-red rounded-br-4xl lg:absolute lg:right-0 lg:top-5 lg:w-1/3">
                    <div class="flex items-center text-black gap-2 mb-5">
                        <span class="text-xl font-bold"><?php echo date('d.m.Y', strtotime($query->posts[0]->post_date)) ?></span>

                        <span class="text-2xl opacity-50">&#x2022;</span>

                        <span><?php echo get_the_category($query->posts[0]->ID)[0]->name ?></span>
                    </div>
                    <h2>
                        <a href='<?php echo get_permalink($query->posts[0]->ID) ?>' class="hover:text-black"><?php echo $query->posts[0]->post_title ?></a>
                    </h2>
                </div>
            </article>

            <div>
                <h3 class="border-l-4 p-1 border-red-500">Избрано за вас</h3>
                <div class="posts-4">
                    <?php for ($i = 1; $i < 5; $i++) {
                        if (!empty($query->posts[$i])) {
                    ?>
                            <article class='post'>
                                <?php echo get_the_post_thumbnail($query->posts[$i]->ID, array(300, 300), array('class' => 'w-full')); ?>
                                <h3><a href="<?php echo get_permalink($query->posts[$i]->ID) ?>"><?php echo $query->posts[$i]->post_title ?></a></h3>
                                <p><?php echo date('d.m.Y', strtotime($query->posts[$i]->post_date)) ?></p>
                            </article>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>