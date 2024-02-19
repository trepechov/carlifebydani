<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="bg-black grid grid-cols-2 rounded-br-5xl shadow-card relative overflow-hidden">
        <!-- <div class="overlay bg-to-black-gradient-post"></div>
        <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100 link-transition"></div> -->
        <div>
            <?php get_template_part('template-parts/featured-image', 'featured-image', array('post_id' => $args['post']->ID, 'size' => 'medium', 'class' => 'w-full')); ?>
        </div>
        <div class="px-8 p-4 mr-12">
            <span class="text-sm text-brand-lightgrey"><?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?></span>
            <h3 class="mt-4 line-clamp-4 group-hover:text-brand-red link-transition"><?php echo $args['post']->post_title ?></h3>
        </div>
    </article>
</a>
