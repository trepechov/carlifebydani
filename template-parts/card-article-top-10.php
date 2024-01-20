<article class="pb-2 pt-4 px-6 flex bg-black rounded-br-4xl shadow-card cursor-pointer group" onclick="window.location.href='<?php echo $args['menu_item']->url ?>'">
    <span class="mr-6 text-8xl font-bold text-brand-grey"><?php echo str_pad($args['number'], 2, '0', STR_PAD_LEFT) ?></span>

    <div class="pt-2">
        <div class="flex items-center">
            <span class="text-sm uppercase text-brand-red"><?php echo get_the_category($args['menu_item']->object_id)[0]->name ?></span>
        </div>

        <h5 class="mt-2">
            <a href="<?php echo $args['menu_item']->url ?>" class="line-clamp-2 group-hover:text-brand-red link-transition"><?php echo $args['menu_item']->title ?></a>
        </h5>
    </div>
</article>