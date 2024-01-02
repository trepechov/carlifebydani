<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="p-5 flex bg-black rounded-br-4xl shadow-card hover:bg-brand-solidgrey link-transition">
        <?php if (isset($args['index'])) { ?>
            <span class="text-4xl mr-4 font-bold text-brand-grey"><?php echo str_pad($args['index'] + 1, 2, '0', STR_PAD_LEFT) ?></span>
        <?php } ?>

        <div>
            <?php get_template_part('template-parts/tags-category-date', 'tags', array('post' => $args['post'], 'with_category' => isset($args['with_category'])  && $args['with_category'])); ?>

            <h5 class="line-clamp-2"><?php echo $args['post']->post_title ?></h5>
        </div>
    </article>
</a>