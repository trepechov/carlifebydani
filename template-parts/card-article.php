<a href="<?php echo get_permalink($args['post']->ID) ?>" class="group">
    <article class="h-full bg-black rounded-br-4xl overflow-hidden">
        <div class="relative">
            <p class="absolute z-10 text-sm bottom-4 <?php echo $args['title_size'] === 'normal' ? 'left-8' : 'left-4' ?>">
                <?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?>
            </p>
            <div class="overlay bg-to-black-gradient-post group-hover:opacity-0"></div>
            <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100"></div>
            <?php echo get_the_post_thumbnail($args['post']->ID, 'medium', array('class' => 'w-full')); ?>
        </div>

        <?php if ($args['title_size'] === 'normal') { ?>
            <h4 class="pb-8 px-8 h-full group-hover:text-brand-red group-hover:bg-brand-solidgrey">
                <?php echo $args['post']->post_title ?>
            </h4>
        <?php } else { ?>
            <h5 class="pb-4 px-4 h-full group-hover:text-brand-red group-hover:bg-brand-solidgrey">
                <?php echo $args['post']->post_title ?>
            </h5>
        <?php } ?>
    </article>
</a>