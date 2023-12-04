<?php
  if  (!is_user_logged_in() ) {
    wp_redirect('https://evtour.carlifebydani.com/');
    exit;
  }?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link
	    href="https://fonts.googleapis.com/css2?family=Sofia+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap"
	    rel="stylesheet"
	/>
    <?php wp_head(); ?>
</head>

<body class="body">
    <header class="header">
        <!-- TOP -->
        <div class="top">
            <div class="top-container">
                <nav class="top-tags text-[13px]">
                    <span class="font-bold uppercase">Популярни теми</span>

                    <span class="mx-2.5">/</span>

                    <ul class="list-none flex gap-4">
                    <?php
                        $tags = get_tags([
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 5
                        ]);
                        foreach ( $tags as $tag ) :
                        $tag_link = get_tag_link( $tag->term_id );
                    ?>
                    <li>
                        <a href='<?php echo $tag_link; ?>' title='<?php echo $tag->name; ?>'>#<?php echo $tag->name ?></a>
                    </li>
                    <?php
                        endforeach;
                    ?>
                    </ul>
                </nav>

                <nav class="text-sm flex gap-5 items-center">
                    <?php
                        wp_nav_menu( array(
                            'theme_location' => 'top-menu',
                            'fallback_cb' => false,
                            'menu_class' => 'list-none flex gap-4',
                            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        ) );
                    ?>

                    <button>
                        <span></span>
                        <span>Подкрепи ни</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="main-nav">
            <div class="flex items-center gap-6">
                <a class="w-full h-full max-w-[224px]" href="<?php echo get_home_url(); ?>">
                    <img src="http://carlifebydani.local/wp-content/uploads/2023/12/logo-1.png" alt="Logo">
                </a>

                <nav>
                    <ul class="list-none flex gap-7">
                        <?php wp_list_categories( array(
                            'title_li' => '',
                            'orderby' => 'id',
                            'hide_empty' => false,
                            'parent' => 0,
                        ) ); ?>
                    </ul>
                </nav>
            </div>

            <div class="sidebar">
		        <span class="text-[13px] font-bold uppercase text-red-500">сподели с нас...</span>

		        <div class="flex gap-8">
			        <div class="sidebar-item">
				        <span class="item-title">Новини за EV NEWS</span>
				        <span class="item-sub-title text-gray">Теми, които те вълнуват</span>
                    </div>

			        <div class="sidebar-item">
				        <span class="item-title">Твоята EV Кола</span>
				        <span class="item-sub-title text-gray">Запиши колата си за ревю</span>
			        </div>
                </div>
		    </div>
        </div>
    </header>

    <main>
