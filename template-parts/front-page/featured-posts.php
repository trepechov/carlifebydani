<?php
$args  = array(
    'posts_per_page'      => 10,
    'post__in'            => get_option('sticky_posts'),
    'ignore_sticky_posts' => 1,
);
$query = new WP_Query($args);
?>
<div class="featured-posts">
    <div class="gradient"></div>

    <div class="featured-posts-container">
        <article class="relative my-6">
            <div class="relative w-full lg:w-3/4">
                <div class="gradient-overlay"></div>
                <?php echo get_the_post_thumbnail($query->posts[0]->ID, array(1024, 1024), array('class' => 'lg:rounded-br-6xl')); ?>
            </div>


            <div class="title-container">
                <span class="text-black text-xl font-bold"><?php echo date('d.m.Y', strtotime($query->posts[0]->post_date)) ?></span>

                <span class="text-black mx-1 opacity-50">&#x2022;</span>

                <span class="text-black text-base"><?php echo get_the_category($query->posts[0]->ID)[0]->name ?></span>

                <h2>
                    <a href='<?php echo get_permalink($query->posts[0]->ID) ?>'><?php echo $query->posts[0]->post_title ?></a>
                </h2>
            </div>
        </article>

        <div>
            <h3 class="border-l-4 p-1 border-red-500">Избрано за вас<h3>
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