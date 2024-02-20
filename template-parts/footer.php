</main>

<footer class="bg-black">
    <div class="wrapper pt-12 pb-8">
        <div class="flex gap-14 items-stretch">
            <a href="<?php echo get_home_url(); ?>" class="my-3">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.svg" alt="Carlife by Dani" class="w-48" />
            </a>
            <div class="flex-1 bg-carbon-stripe-white"></div>
        </div>
        <div class="flex mt-12">
            <div class="flex-1 mx-2">
                <nav>
                    <ul class="flex gap-4 text-md sm:gap-8 sm:text-xl font-bold ">
                        <?php
                        $locations = get_nav_menu_locations();
                        $footer_menu = wp_get_nav_menu_object($locations['footer-menu']);
                        $footerMenuItems = wp_get_nav_menu_items($footer_menu->term_id);

                        foreach ($footerMenuItems as $key => $menuItem) {
                            $current = ($menuItem->object_id == get_queried_object_id()) ? 'text-brand-red' : '';
                        ?>
                            <li>
                                <a class="hover:text-brand-red <?php echo $current ?>" href="<?php echo  $menuItem->url ?>">
                                    <?php echo $menuItem->title ?>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </nav>
                <nav class="mt-8">
                    <ul class="flex gap-4 items-center">
                        <?php
                        $bottom_menu = wp_get_nav_menu_object($locations['bottom-menu']);
                        $bottomMenuItems = wp_get_nav_menu_items($bottom_menu->term_id);

                        for ($i = 0; $i < count($bottomMenuItems) - 1; $i++) { ?>
                            <li>
                                <a class="hover:text-brand-red" href='<?php echo $bottomMenuItems[$i]->url ?>'><?php echo $bottomMenuItems[$i]->title ?></a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </nav>
                <nav class="mt-2">
                    <ul class="flex gap-4 items-center">
                        <?php
                        $share_menu = wp_get_nav_menu_object($locations['share-menu']);
                        $shareMenu = wp_get_nav_menu_items($share_menu->term_id);

                        foreach ($shareMenu as $key => $menuItem) { ?>
                            <li>
                                <a class="hover:text-brand-red" href="<?php echo  $menuItem->url ?>">
                                    <?php echo $menuItem->title ?>
                                </a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </nav>
            </div>
            <div class="hidden flex-1 lg:flex gap-16 justify-end items-center">
                <div class="flex gap-8 items-end">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mail.svg" alt="mai" class="h-14" />
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-brand-red">Контакти</span>
                        <a class="text-3xl" href="mailto:info@carlifebydani.com">info@carlifebydani.com</a>
                        <span class="text-xs text-brand-lightgrey">Имате нужда от повече информация или връзка с нас.</span>
                    </div>
                </div>
                <a class="w-24 h-24 flex items-center justify-center rounded-full bg-brand-red text-lg hover:bg-brand-solidgrey" href="#">
                    <span class="material-symbols-outlined text-7xl mb-1">
                        keyboard_arrow_up
                    </span>
                </a>

            </div>
        </div>

        <?php
        $videos_and_pictures_post = get_post(VIDEOS_AND_PICTURES);
        $videos_and_pictures_link = $videos_and_pictures_post ? get_permalink($videos_and_pictures_post->ID) : get_home_url();
        ?>

        <div class="mt-12 pt-8 text-xs text-center border-t border-brand-darkgrey">
            <p>В сайта се използват авторски текстове, <a href="<?php echo $videos_and_pictures_link; ?>" class="hover:text-brand-red">видео и снимки*</a>. Цитирането на източник Car Life by Dani е задължително!</p>
            &copy;<?php echo date("Y"); ?> <a href="<?php echo get_home_url(); ?>" class="hover:text-brand-red">carlifebydani.com</a> Всички права запазени!
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>