<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="h-full bg-black rounded-br-4xl overflow-hidden drop-shadow-card">
        <div class="relative">
            <div class="absolute z-10 bottom-4 flex items-center gap-1 pl-[7%]">
                <?php if (isset($args['with_category']) && $args['with_category']) { ?>
                    <span class=" font-bold"><?php echo get_the_category($args['post']->ID)[0]->name ?></span>

                    <span class="w-1 h-1 bg-brand-red rounded"></span>
                <?php } ?>
                <span class="text-sm"><?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?></span>
            </div>
            <div class="overlay bg-to-black-gradient-post group-hover:opacity-0"></div>
            <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100"></div>
            <?php echo get_the_post_thumbnail($args['post']->ID, 'medium', array('class' => 'w-full')); ?>
        </div>

        <div class="h-full p-[7%] pb-[12%] group-hover:text-brand-red group-hover:bg-brand-solidgrey">
            <?php if ($args['title_size'] === 'normal') { ?>
                <h3 class="line-clamp-4">
                    <?php echo $args['post']->post_title ?>
                </h3>
            <?php } else { ?>
                <h5 class="line-clamp-4">
                    <?php echo $args['post']->post_title ?>
                </h5>
            <?php } ?>
        </div>
    </article>
</a>