<?php
$news_posts = get_posts( array(
    'numberposts'	=> 4,
	'category'		=> 2   //category ev-masters
) );
?><div class="category-posts">
    <div class="featured-posts-container">
        <h3 class="border-l-4 p-1 border-red-500">EV Masters</h3>

        <div class="posts-4">
            <?php
                foreach($news_posts as $post) {
                    echo "<article class='post'>";
                    echo get_the_post_thumbnail( $post->ID, array( 300, 300 ), array( 'class' => 'w-full' ) );
                    echo "<h3><a href='" . get_permalink($post->ID)  . "'>" . $post->post_title . "</a></h3>";
                    echo "<p>" . date('d.m.Y', strtotime($post->post_date)) . "</p></article>";
                }
            ?>
            <article class='post'>1</article>
            <article class='post'>2</article>
            <article class='post'>4</article>
        </div> 
    </div>
</div>
