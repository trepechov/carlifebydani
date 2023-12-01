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

<body class="body">
    <header class="header">
        <!-- TOP -->
        <div class="top">
            <div class="top-container">
                <nav class="top-tags">Популярни теми /
                    <ul class="list-none flex">
                    <?php
                        $tags = get_tags([
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 5
                        ]);
                        foreach ( $tags as $tag ) :
                        $tag_link = get_tag_link( $tag->term_id );
                    ?>
                    <li class="m-2">
                        <a href='<?php echo $tag_link; ?>' title='<?php echo $tag->name; ?>'>#<?php echo $tag->name ?></a>
                    </li>
                    <?php
                        endforeach;
                    ?>
                    </ul>
                </nav>
                <nav class="top-nav">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'top-menu',
                            'fallback_cb' => false,
                            'menu_class' => 'list-none flex space-x-4',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        ) );
                    ?>
                </nav>
            </div> 
        </div>

        <!-- Main Navigation -->
        <div class="main-nav">
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
            <div>Новини за EV News | Твоята EV Кола</div>
        </div>
    </header>
                
    <main>
