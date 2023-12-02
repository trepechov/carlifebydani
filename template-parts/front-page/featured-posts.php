<?php
    $args  = array(
        'posts_per_page'      => 10,
        'post__in'            => get_option( 'sticky_posts' ),
        'ignore_sticky_posts' => 1,
    );
    $query = new WP_Query( $args );
?>
<div class="featured-posts">
    <div class="featured-posts-container">
        <article class="relative">
            <?php echo get_the_post_thumbnail( $query->posts[0]->ID, array( 1024, 1024 ), array( 'class' => 'w-4/5' ) ); ?>
            <div class="absolute right-0 top-5 p-8 bg-red-600 rounded-br-3xl w-2/5">
                <span><?php echo date('d.m.Y', strtotime($query->posts[0]->post_date)) ?></span>
                <span><?php echo get_the_category( $query->posts[0]->ID )[0]->name ?></span>
                <h2 class="text-5xl"><?php echo $query->posts[0]->post_title ?></h2>
            </div>
        </article>
        <div>
            <h3 class="border-l-4 p-1 border-red-500">Избрано за вас<h3>
            <div class="posts-4">
                <?php for ($i = 1; $i < 5; $i++) { 
                    if (!empty($query->posts[$i])) {
                ?>
                    <article class='post'>
                        <?php echo get_the_post_thumbnail( $query->posts[$i]->ID, array( 300, 300 ), array( 'class' => 'w-full' ) ); ?>
                        <h3><?php echo $query->posts[$i]->post_title ?></h3>
                        <p><?php echo date('d.m.Y', strtotime($query->posts[$i]->post_date)) ?></p>
                    </article>
                <?php }
                    } ?>
            </div> 
        </div>
    </div>
</div>
