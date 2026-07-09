<?php

/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_template_part('template-parts/header'); ?>

<!--
    Homepage is a hub (news, top 10, EV news/reviews/masters, brands), not a
    single article, so the H1 is a static site identity statement rather than
    the hero post's headline — which stays an H2 and would otherwise leave
    the page with no H1 at all. Visually hidden since the hero section already
    carries the visual weight; sourced from Settings > General so it stays
    editable without a deploy.
-->
<h1 class="sr-only"><?php
    echo esc_html(get_bloginfo('name'));
    $tagline = get_bloginfo('description');
    if ($tagline) {
        echo ' — ' . esc_html($tagline);
    }
?></h1>

<!-- <div> -->
<?php
get_template_part('template-parts/front-page/featured-posts');
get_template_part('template-parts/front-page/news');
get_template_part('template-parts/front-page/share-with-us');
get_template_part('template-parts/front-page/top-10');
get_template_part('template-parts/front-page/ev-news');
get_template_part('template-parts/front-page/ev-reviews');
get_template_part('template-parts/front-page/ev-masters');
get_template_part('template-parts/front-page/brands');
get_template_part('template-parts/front-page/newsletter');
get_template_part('template-parts/find-us');
?>
<!-- </div> -->

<?php get_template_part('template-parts/footer'); ?>