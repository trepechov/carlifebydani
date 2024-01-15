<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="p-5 pt-4 pr-8 flex bg-black rounded-br-4xl shadow-card">
        <?php if (!empty($args['number'])) { ?>
            <span class="text-4xl mr-4 font-bold text-brand-grey"><?php echo str_pad($args['number'], 2, '0', STR_PAD_LEFT) ?></span>
        <?php } ?>

        <div>
            <?php get_template_part('template-parts/tags-category-date', 'tags', array('post' => $args['post'], 'with_category' => !empty($args['with_category']), 'small' => true)); ?>

            <h6 class="line-clamp-2 mt-3 group-hover:text-brand-red link-transition"><?php echo $args['post']->post_title ?></h6>
        </div>
    </article>
</a>