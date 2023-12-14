<?php
if (!is_user_logged_in()) {
    wp_redirect('https://evtour.carlifebydani.com/');
    exit;
} ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body class="body">
    <header class="bg-black">
        <div class="container py-4 flex justify-between items-center">
            <!-- Popular tags menu -->
            <nav class="flex gap-4 text-xs">
                <span class="font-bold uppercase">Популярни теми</span>

                <span>/</span>

                <ul class="list-none flex gap-4">
                    <?php
                    $tags = get_tags([
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'number' => 5
                    ]);

                    foreach ($tags as $tag) {
                        $tag_link = get_tag_link($tag->term_id);
                    ?>
                        <li>
                            <a href='<?php echo $tag_link; ?>' title='<?php echo $tag->name; ?>' class="hover:text-brand-red">#<?php echo $tag->name ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </nav>

            <!-- Top menu -->
            <nav class="flex gap-5 text-sm">
                <ul class="list-none flex gap-4 items-center">
                    <?php
                    $locations = get_nav_menu_locations();
                    $menu = wp_get_nav_menu_object($locations['top-menu']);
                    $topMenuItems = wp_get_nav_menu_items($menu->term_id);

                    for ($i = 0; $i < count($topMenuItems) - 1; $i++) { ?>
                        <li class=" hover:text-brand-red">
                            <a href='<?php echo $topMenuItems[$i]->url ?>'><?php echo $topMenuItems[$i]->title ?></a>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <a href='<?php echo $topMenuItems[$i]->url ?>' class="button button-icon-heart">
                            <?php echo $topMenuItems[$i]->title ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Full width separator -->
        <div class="border-b border-b-brand-red"></div>

        <!-- Main Navigation -->
        <div class="container py-4 flex justify-between">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="<?php echo get_home_url(); ?>">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="Carlife by Dani" />
                </a>

                <!-- Main menu with categories -->
                <nav>
                    <ul class="list-none flex gap-8">
                        <?php
                        $categories = get_categories(array(
                            'title_li' => '',
                            'orderby' => 'id',
                            'hide_empty' => false,
                            'parent' => 0,
                        ));

                        foreach ($categories as $key => $category) { ?>
                            <li class="text-xl font-bold hover:text-brand-red">
                                <a href="<?php echo get_category_link($category->term_id) ?>">
                                    <span class="block text-sm leading-4 text-brand-red"><?php echo str_pad($key + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                    <?php echo $category->name ?>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </nav>
            </div>

            <!-- Share with us menu -->
            <div class="flex flex-col items-start gap-1">
                <span class="text-xs font-bold uppercase text-brand-red">сподели с нас...</span>

                <div class="flex gap-8">
                    <a href="#" class="flex flex-col">
                        <span class="text-lg font-bold">Новини за EV NEWS</span>
                        <span class="text-xs italic text-brand-lightgrey">Теми, които те вълнуват</span>
                    </a>

                    <a href="#" class="flex flex-col">
                        <span class="text-lg font-bold">Твоята EV Кола</span>
                        <span class="text-xs italic text-brand-lightgrey">запиши колата си за ревю</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>