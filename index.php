<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header');
?>

    <div class="main-content">
        <?php  
            if (have_posts()) {
                /* Start the Loop */
                while (have_posts()) {
                    the_post();
                    if (! is_home() && ! is_front_page()) {
                        ?>
                <h1><?php the_title(); ?></h1>
                <?php
                    }
                    the_content(); 
                }
            } else {
                ?>

                <p>No posts found. :(</p>

                <?php

            }
        ?>
    </div>

<?php get_template_part('template-parts/footer'); ?>