<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="h-full bg-black rounded-br-4xl overflow-hidden shadow-card">
        <div class="relative">
            <div class="absolute z-10 bottom-4 pl-[7%]">
                <?php get_template_part('template-parts/tags-category-date', 'tags', array('post' => $args['post'], 'with_category' => !empty($args['with_category']))); ?>
            </div>
            <div class="overlay bg-to-black-gradient-post group-hover:opacity-0"></div>
            <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100"></div>
            <?php get_template_part('template-parts/featured-image', 'featured-image', array('post_id' => $args['post']->ID, 'size' => 'medium', 'class' => 'w-full')); ?>
        </div>

        <div class="h-full px-[7%] pb-[12%] group-hover:text-brand-red group-hover:bg-brand-solidgrey">
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