<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon-16x16.png">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>

<body class="body">
    <header class="bg-black sticky top-0 z-999 sm:static">
        <div class="hidden wrapper py-2 justify-between items-center lg:flex">
            <!-- Popular tags menu -->
            <nav class="flex items-center gap-2 text-xs">
                <span class="font-bold uppercase">Популярни теми</span>

                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/slash.svg" alt="slash" class="h-6" />

                <ul class="list-none flex gap-4">
                    <?php
                    $locations = get_nav_menu_locations();
                    $top_tags_menu = wp_get_nav_menu_object($locations['top-tags-menu']);
                    $top_tag_menu_items = wp_get_nav_menu_items($top_tags_menu->term_id);

                    foreach ($top_tag_menu_items as $menuItem) { ?>
                        <li>
                            <a href='<?php echo $menuItem->url ?>' class="hover:text-brand-red"><?php echo $menuItem->title ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </nav>

            <!-- Top menu -->
            <nav class="flex gap-5 text-sm">
                <ul class="hidden xl:flex items-center gap-4">
                    <?php
                    $top_menu = wp_get_nav_menu_object($locations['top-menu']);
                    $topMenuItems = wp_get_nav_menu_items($top_menu->term_id);

                    for ($i = 0; $i < count($topMenuItems) - 1; $i++) { ?>
                        <li class=" hover:text-brand-red">
                            <a href='<?php echo $topMenuItems[$i]->url ?>'><?php echo $topMenuItems[$i]->title ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <a href='<?php echo $topMenuItems[$i]->url ?>' class="button" target="_blank">
                    <span class="material-symbols-outlined -ml-1">favorite</span>
                    <?php echo $topMenuItems[$i]->title ?>
                </a>
            </nav>
        </div>

        <!-- Full width separator -->
        <div class="border-b border-b-brand-red/50"></div>

        <!-- Main Navigation -->
        <div class="wrapper py-4 flex justify-between">

            <div class="w-full flex gap-6 justify-between ">

                <!-- Left section -->
                <div class="flex gap-6 items-center">
                    <!-- Logo -->
                    <a href="<?php echo get_home_url(); ?>">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="Car Life by Dani" class="h-16 sm:h-20" />
                    </a>

                    <!-- Main menu with categories -->
                    <ul class="hidden lg:flex flex-row gap-8">
                        <?php
                        $main_menu = wp_get_nav_menu_object($locations['main-menu']);
                        $main_menu_items = wp_get_nav_menu_items($main_menu->term_id);

                        foreach ($main_menu_items as $key => $menuItem) {
                            $current = ($menuItem->object_id == get_queried_object_id()) ? 'text-brand-red' : '';
                        ?>
                            <li class="text-xl font-bold hover:text-brand-red <?php echo $current ?>">
                                <a href="<?php echo  $menuItem->url ?>">
                                    <span class="block text-sm leading-4 text-brand-red font-normal"><?php echo str_pad($key + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                    <?php echo $menuItem->title ?>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>

                <!-- Right section -->
                <div class="flex items-center">
                    <!-- Support us button (mobile) -->
                    <a href='<?php echo $topMenuItems[$i]->url ?>' class="button mr-4 !hidden sm:!flex lg:!hidden" target="_blank">
                        <span class="material-symbols-outlined -ml-1">favorite</span>
                        <?php echo $topMenuItems[$i]->title ?>
                    </a>

                    <!-- Seach menu with button -->
                    <label class="relative cursor-pointer p-1 mt-2 2xl:mt-6" for="search-menu">
                        <input class="peer hidden" type="checkbox" id="search-menu" />
                        <span class="material-symbols-outlined text-4xl z-999 peer-checked:z-999 hover:text-brand-red link-transition">
                            search
                        </span>

                        <div class="fixed inset-0 peer-checked:z-999 hidden h-full w-full bg-brand-solidgrey/80 backdrop-blur-sm peer-checked:block">
                            &nbsp;
                        </div>

                        <div class="fixed top-0 left-0 z-999 h-2/5 w-full flex justify-center items-center -translate-y-full overflow-y-auto overscroll-y-none transition duration-300 peer-checked:translate-y-0">
                            <div class="relative h-full w-full bg-black shadow-2xl">

                                <div class="wrapper h-full">
                                    <div class="relative h-full flex justify-center items-center">

                                        <span class="material-symbols-outlined text-4xl absolute right-4 top-4 hover:text-brand-red link-transition cursor-pointer" for="mobile-menu-top">
                                            close
                                        </span>

                                        <?php get_template_part('template-parts/menus/search'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <!-- Seach menu with button -->

                    <!-- Mobile menu with button-->
                    <label class="relative cursor-pointer p-1 mt-2 2xl:mt-6 2xl:hidden" for="mobile-menu">
                        <input class="peer hidden" type="checkbox" id="mobile-menu" />
                        <span class="material-symbols-outlined text-4xl hover:text-brand-red link-transition">
                            menu
                        </span>
                        <div class="fixed inset-0 z-999 hidden h-full w-full bg-brand-solidgrey/80 backdrop-blur-sm peer-checked:block">
                            &nbsp;
                        </div>
                        <div class="fixed top-0 right-0 z-999 h-full w-full flex justify-end translate-x-full overflow-y-auto overscroll-y-none transition duration-300 peer-checked:translate-x-0">
                            <div class="relative h-full w-full px-8 py-12 bg-black/80 shadow-2xl sm:w-1/2 overflow-y-auto overscroll-auto">
                                <span class="material-symbols-outlined text-4xl absolute right-4 top-4 hover:text-brand-red link-transition">
                                    close
                                </span>

                                <?php get_template_part('template-parts/menus/mobile-menu'); ?>
                            </div>
                        </div>
                    </label>
                    <!-- Mobile menu with button-->

                    <div class="hidden mt-4 mr-4 ml-2 2xl:block"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/slash.svg" alt="slash" class="h-9" /></div>

                    <!-- Share with us menu -->
                    <div class="hidden 2xl:flex flex-col items-start gap-1">
                        <span class="text-xs uppercase text-brand-red">Сподели с нас...</span>

                        <div class="flex gap-8 flex-row">
                            <?php
                            $share_menu = wp_get_nav_menu_object($locations['share-menu']);
                            $shareMenuItems = wp_get_nav_menu_items($share_menu->term_id);

                            foreach ($shareMenuItems as $key => $menuItem) {
                                $current = ($menuItem->object_id == get_queried_object_id()) ? 'text-brand-red' : '';
                            ?>
                                <a href="<?php echo $menuItem->url ?>" class="flex flex-col">
                                    <span class="text-xl font-bold link-transition hover:text-brand-red"><?php echo $menuItem->title; ?></span>
                                    <span class="text-xs italic text-brand-lightgrey"><?php echo get_post_meta($menuItem->object_id, 'post-subtitle', true); ?></span>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
