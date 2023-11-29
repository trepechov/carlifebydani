<?php
  if  (!is_user_logged_in() ) {
    wp_redirect('https://evtour.carlifebydani.com/');
    exit;
  }?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body class="bg-black text-white">
    <header>
        <div class="border-b border-b-red-500">
            <div class="container max-w-7xl p-2 flexjustify-between">
                <div>Tags</div>
                <nav>
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'header-menu',
                            'fallback_cb' => false,
                            'menu_class' => 'list-none flex space-x-4',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        ) );
                    ?>
                </nav>
            </div> 
        </div>

        <div class="container max-w-7xl p-2 flex justify-between">
            <div class="flex">
                <a class="mr-4" href="<?php echo get_home_url(); ?>">
                    <h2>CarLife by Dani Logo</h2>
                </a>
                <nav>
                    <ul class="list-none flex space-x-4">
                        <?php wp_list_categories( array(
                            'title_li' => '',
                            'orderby' => 'id',
                            'hide_empty' => false,
                            'parent' => 0,
                        ) ); ?>
                    </ul>
                </nav>
            </div>
        </div>

    </header>

    <main class="site-main bg-gray-800 p-2">
        <div class="container max-w-7xl p-2">
