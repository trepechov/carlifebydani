  <!-- Main menu with categories -->
  <ul class="flex flex-col gap-4">
      <?php
        $locations = get_nav_menu_locations();
        $main_menu = wp_get_nav_menu_object($locations['main-menu']);
        $main_menu_items = wp_get_nav_menu_items($main_menu->term_id);

        foreach ($main_menu_items as $key => $menuItem) {
            $current = ($menuItem->object_id == get_queried_object_id()) ? 'text-brand-red' : '';
        ?>
          <li class="text-xl font-bold hover:text-brand-red <?php echo $current ?>">
              <a href="<?php echo $menuItem->url ?>">
                  <span class="block text-sm leading-4 text-brand-red font-normal"><?php echo str_pad($key + 1, 2, '0', STR_PAD_LEFT) ?></span>
                  <?php echo $menuItem->title ?>
              </a>
          </li>
      <?php
        }
        ?>
  </ul>

  <hr class="my-8 border-2 border-brand-button/50" />

  <!-- Share with us menu -->
  <div class="flex gap-4 flex-col">
      <span class="text-xs uppercase text-brand-red">Сподели с нас...</span>
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

  <hr class="my-8 border-2 border-brand-button/50" />

  <ul class="flex flex-col gap-4 items-start">
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