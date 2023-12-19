<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header');
?>
<div class="container my-16">
    <?php
    if (have_posts()) { ?>

        <h2 class="border-l-8 mb-8 p-3 border-brand-red uppercase"><?php single_cat_title(); ?></h2>
        <?php echo tag_description() ?>
        <?php
        /* Start the Loop */
        while (have_posts()) {
            the_post();
            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
        ?>
            <a href="<?php echo get_permalink(); ?>">
                <article class="mb-8">
                    <h4 class="mb-4"><?php the_title(); ?></h4>
                    <div class="flex gap-8">
                        <?php if ($featured_image_url) { ?>
                            <img src="<?php echo $featured_image_url; ?>" alt="Featured Image">
                        <?php
                        } ?>
                        <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                </article>
            </a>
        <?php
        }
    } else {
        ?>

        <p>Няма намерни новини. :(</p>

    <?php
    }
    ?>
</div>

<?php get_template_part('template-parts/footer'); ?>