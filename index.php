<?php

/**
 * The category/tags/authors template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

$bread_crumbs = [
    [
        'label' => 'Начало',
        'link' => '/',
    ],
];

$args = array(
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => (get_query_var('paged') ? get_query_var('paged') : 1),
);

switch (true) {
    case is_tag():
        $tag = get_queried_object();

        // $bread_crumbs[] = [
        //     'label' => '#' . $tag->name,
        //     'link' => get_category_link($tag->term_id),
        // ];

        $title = '#' . $tag->name;
        $description = $tag->description;

        $args['tag'] = $tag->slug;

        break;

    case is_author():
        $author = get_queried_object();

        // $bread_crumbs[] = [
        //     'label' => $author->display_name,
        //     'link' => get_category_link($author->term_id),
        // ];

        $title = $author->display_name;
        $description = get_the_author_meta('description');

        $args['author'] = $author->ID;

        break;

    case is_search():
        // $bread_crumbs[] = [
        //     'label' => 'Търсене',
        //     'link' => get_search_link(),
        // ];

        $title = 'Резултати: ' . get_search_query();
        $args['s'] = get_search_query();

        break;

    case is_404():
        $title = 'Страницата не е намерена';
        break;

    default:
        $current_category = get_the_category()[0];

        // $bread_crumbs[] = [
        //     'label' => $current_category->name,
        //     'link' => get_category_link($current_category->term_id),
        // ];

        $title = $current_category->name;
        $description = $current_category->description;

        $args['category'] = $current_category->term_id;
        break;
};

$archive_posts = get_posts($args);

get_template_part('template-parts/header');
?>


<div class="relative">
    <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
        <div class="h-full bg-from-black-60-gradient"></div>
    </div>
    <div class="wrapper py-8 relative">

        <?php get_template_part('template-parts/bread-crumbs', 'bread_crumbs', array('bread_crumbs' => $bread_crumbs)); ?>

        <h1 class="title text-3xl/8 font-bold mt-6 mb-8"><?php echo $title ?></h1>
        <?php if (!empty($description)) { ?>
            <p class="mb-8"><?php echo $description ?></p>
        <?php } ?>

        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <div class="pb-8 col-span-2 border-b-2 border-brand-button">
                <div class="flex flex-col gap-8">
                    <?php
                    if (count($archive_posts) > 0) {
                        foreach ($archive_posts as $post) {
                            get_template_part('template-parts/card-article', 'article', [
                                'post' => $post,
                                'horizontal' => true,
                            ]);
                        }
                    ?>
                        <div class="my-8">
                            <?php
                            the_posts_pagination(array(
                                'mid_size' => 1,
                                'type' => 'plain',
                                'before_page_number' => '<div class="flex w-14 h-14 text-2xl rounded-br-lg justify-center items-center bg-black hover:bg-brand-solidgrey">',
                                'after_page_number' => '</div>',
                                'mid_size'  => 2,
                                'prev_text' => '<div class="text-4xl hover:text-brand-lightgrey">&lsaquo;</div>',
                                'next_text' => '<div class="text-4xl">&rsaquo;</div>',
                            ));
                            ?>
                        </div>
                    <?php
                    } else {
                    ?>
                        <p>Няма намерни публикации</p>

                        <ul class="list-none flex flex-wrap gap-2 text-sm">
                            <?php
                            $locations = get_nav_menu_locations();
                            $top_tags_menu = wp_get_nav_menu_object($locations['top-tags-menu']);
                            $top_tag_menu_items = wp_get_nav_menu_items($top_tags_menu->term_id);

                            foreach ($top_tag_menu_items as $menuItem) { ?>
                                <li>
                                    <a href="<?php echo home_url('/') . '?s=' . $menuItem->post_name; ?>" class="hover:text-brand-red"><?php echo substr($menuItem->title, 1); ?></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="hidden lg:flex lg:col-span-1 lg:flex-col lg:gap-12">
                <?php
                get_template_part('template-parts/sidebar');
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/find-us'); ?>
<?php get_template_part('template-parts/footer'); ?>