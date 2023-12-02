<?php
$news_posts = get_posts( array(
    'numberposts'	=> 7,
	'category'		=> 1   //category ev-news
) );
?><div class="category-posts">
    <div class="featured-posts-container">
        <h3 class="border-l-4 p-1 border-red-500">Ev News</h3>

        <!-- TODO Replace foreach loop with for to ierate over the array and print the posts in the correct order -->
        <div class="posts-3">
            <?php
                foreach($news_posts as $post) {
                    echo "<article class='post'>";
                    echo get_the_post_thumbnail( $post->ID, array( 300, 300 ), array( 'class' => 'w-full' ) );
                    echo "<h3>" . $post->post_title . "</h3>";
                    echo "<p>" . date('d.m.Y', strtotime($post->post_date)) . "</p></article>";
                }
            ?>
            <article class='post'>1</article>
            <article class='post'>2</article>
        </div>
        <div class="posts-4">
            <?php
                foreach($news_posts as $post) {
                    echo "<article class='post'>";
                    echo get_the_post_thumbnail( $post->ID, array( 300, 300 ), array( 'class' => 'w-full' ) );
                    echo "<h3>" . $post->post_title . "</h3>";
                    echo "<p>" . date('d.m.Y', strtotime($post->post_date)) . "</p></article>";
                }
            ?>
            <article class='post'>1</article>
            <article class='post'>2</article>
            <article class='post'>4</article>
        </div> 
    </div>
</div>
