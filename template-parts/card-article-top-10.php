<article class="pb-2 pt-4 px-6 flex bg-black rounded-br-4xl shadow-card cursor-pointer group" onclick="window.location.href='<?php echo get_permalink($args['post']->ID) ?>'">
    <span class="mr-6 text-8xl  font-bold text-brand-grey"><?php echo str_pad($args['number'], 2, '0', STR_PAD_LEFT) ?></span>

    <div class=" pt-2">
        <?php get_template_part('template-parts/tags-category-date', 'tags', array('post' => $args['post'], 'with_category' => true, 'small' => true)); ?>

        <h6 class=" mt-2">
            <a href=" <?php echo get_permalink($args['post']->ID) ?>" class="line-clamp-2 group-hover:text-brand-red link-transition"><?php echo $args['post']->post_title ?>
            </a>
        </h6>
    </div>
</article>
</a>