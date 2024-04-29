<form action="<?php echo home_url('/'); ?>" method="get" class="w-11/12 sm:w-4/5">
    <div class="relative">
        <div class="pointer-events-none absolute left-0">
            <span class="material-symbols-outlined text-5xl hover:text-brand-red link-transition">
                search
            </span>
        </div>
        <input type="text" name="s" value="<?php the_search_query(); ?>" placeholder="Търсене в сайта..." class="bg-black text-white w-full text-2xl block pl-20 pt-2 pb-8 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none" />
    </div>

    <ul class="mt-8 list-none flex flex-wrap gap-2 text-sm">
        <li>например:</li>
        <?php
        $locations = get_nav_menu_locations();
        $top_tags_menu = wp_get_nav_menu_object($locations['top-tags-menu']);
        $top_tag_menu_items = wp_get_nav_menu_items($top_tags_menu->term_id);

        foreach ($top_tag_menu_items as $menuItem) { ?>
            <li>
                <a href="<?php echo home_url('/') . '?s=' . substr($menuItem->title, 1); ?>" class="hover:text-brand-red"><?php echo substr($menuItem->title, 1); ?></a>
            </li>
        <?php
        }
        ?>
    </ul>
</form>