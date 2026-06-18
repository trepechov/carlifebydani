<article class="p-5 pt-4 pr-8 flex bg-black rounded-br-4xl shadow-card group cursor-pointer" onclick="window.location='<?php echo get_permalink($args['post']->ID) ?>'">
    <div>
        <?php get_template_part('template-parts/tags-category-date', 'tags', array('post' => $args['post'], 'with_category' => !empty($args['with_category']), 'small' => true)); ?>

        <h6 class="mt-3 group-hover:text-brand-red link-transition"><a href="<?php echo get_permalink($args['post']->ID) ?>" class="line-clamp-2"><?php echo $args['post']->post_title ?></a></h6>
    </div>
</article>