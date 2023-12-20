<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header'); ?>

<div>
    <?php
    get_template_part('template-parts/front-page/featured-posts');
    get_template_part('template-parts/front-page/news');
    get_template_part('template-parts/front-page/share-with-us');
    get_template_part('template-parts/front-page/ev-news');
    get_template_part('template-parts/front-page/ev-reviews');
    get_template_part('template-parts/front-page/ev-masters');
    //Производители
    get_template_part('template-parts/front-page/find-us');
    ?>
</div>

<?php get_template_part('template-parts/footer'); ?>
