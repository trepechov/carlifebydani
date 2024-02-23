<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header');
?>
<div class="wrapper">
    <?php
    if (have_posts()) {
        /* Start the Loop */
        while (have_posts()) {
            the_post();
            if (!is_home() && !is_front_page()) {
    ?>
                <h3 class="mt-16 mb-4"><?php the_title(); ?></h3>
                <p class="italic">Публикувано на <?php echo get_the_date('d.m.y'); ?> в категория
                    <?php
                    $categories = get_the_category();
                    if ($categories) {
                        foreach ($categories as $category) {
                            echo '<a href="' . get_category_link($category->term_id) . '" class="hover:text-brand-red">' . $category->name . '</a> ';
                        }
                    }
                    ?> от <?php echo get_the_author(); ?> <!-- Added this line -->
                </p>

                </p>
        <?php
            }
            the_content();
        }
    } else {
        ?>

        <p>No posts found. 404 :(</p>

    <?php
    }
    ?>
</div>

<?php get_template_part('template-parts/footer'); ?>