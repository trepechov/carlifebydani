<?php
if (!is_user_logged_in()) {
    wp_redirect('https://evtour.carlifebydani.com/');
    exit;
} ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.svg">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>

<body class="body">
    <header class="bg-black">
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
                <ul class="flex gap-4 items-center">
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
                    <li>
                        <a href='<?php echo $topMenuItems[$i]->url ?>' class="button" target="_blank">
                            <span class="material-symbols-outlined text-base -ml-1">favorite</span>
                            <?php echo $topMenuItems[$i]->title ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Full width separator -->
        <div class="border-b border-b-brand-red/50"></div>

        <!-- Main Navigation -->
        <div class="wrapper py-4 flex justify-between">

            <div class="w-full flex gap-6 justify-between items-center">
                <!-- Logo -->
                <a href="<?php echo get_home_url(); ?>">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="Carlife by Dani" />
                </a>

                <label class="cursor-pointer lg:w-full" for="mobile-menu">
                    <input class="peer hidden" type="checkbox" id="mobile-menu" />
                    <span class="material-symbols-outlined text-5xl lg:hidden">
                        menu
                    </span>
                    <div class="fixed inset-0 z-40 hidden h-full w-full bg-black/50 backdrop-blur-sm peer-checked:block lg:!hidden">
                        &nbsp;
                    </div>
                    <nav class="fixed top-0 right-0 z-40 h-full w-full md:w-1/2 lg:w-auto bg-black flex flex-col p-9
                        lg:p-0 lg:static lg:flex-row lg:flex-1 lg:justify-between lg:items-center
                        translate-x-full peer-checked:translate-x-0 lg:translate-x-0 transition duration-300
                    ">
                        <!-- flex flex-1 justify-between items-center -->
                        <!-- Main menu with categories -->
                        <ul class="flex flex-col gap-8 lg:flex-row">
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
                        <hr class="my-5 lg:hidden" />
                        <!-- Share with us menu -->
                        <div class="flex flex-col items-start gap-1">
                            <span class="text-xs uppercase text-brand-red">Сподели с нас...</span>

                            <div class="flex gap-8 flex-col lg:flex-row">
                                <?php
                                $share_menu = wp_get_nav_menu_object($locations['share-menu']);

                                $shareMenuItems = wp_get_nav_menu_items($share_menu->term_id);

                                foreach ($shareMenuItems as $key => $menuItem) {
                                    $current = ($menuItem->object_id == get_queried_object_id()) ? 'text-brand-red' : '';
                                ?>
                                    <a href="<?php echo $menuItem->url ?>" class="flex flex-col">
                                        <span class="text-lg font-bold link-transition hover:text-brand-red"><?php echo $menuItem->title; ?></span>
                                        <span class="text-xs italic text-brand-lightgrey"><?php echo get_post_meta($menuItem->object_id, 'post-subtitle', true); ?></span>
                                    </a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <hr class="my-5 lg:hidden" />

                    </nav>
                </label>
            </div>
        </div>
    </header>

    <main>