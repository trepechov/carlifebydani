<article onclick="windows.location='<?php echo get_permalink($args['post']->ID) ?>'" class="auto-rows-max group grid grid-cols-1 cursor-pointer bg-black rounded-br-4xl overflow-hidden shadow-card hover:bg-brand-solidgrey sm:grid-cols-2 sm:rounded-br-5xl <?php echo empty($args['horizontal']) ? 'lg:grid-cols-1 lg:rounded-br-4xl' : '' ?>">
    <div class="relative">
        <div class="sm:hidden <?php echo empty($args['horizontal']) ? 'lg:block' : '' ?>">
            <div class="absolute z-10 bottom-4 pl-[7%] text-brand-lightgrey">
                <?php get_template_part('template-parts/tags-category-date', 'tags', [
                    'post' => $args['post'],
                    'with_category' => !empty($args['with_category'])
                ]); ?>
            </div>
            <div class="overlay bg-to-black-gradient-post group-hover:opacity-0"></div>
            <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100"></div>
        </div>
        <?php get_template_part('template-parts/featured-image', 'featured-image', [
            'post_id' => $args['post']->ID,
            'size' => 'medium',
            'class' => 'object-cover h-full'
        ]); ?>
    </div>

    <div class="px-[7%] pb-[12%] group-hover:text-brand-red ">
        <div class="hidden mt-6 mb-2 sm:block <?php echo empty($args['horizontal']) ? 'lg:hidden' : '' ?>">
            <?php get_template_part('template-parts/tags-category-date', 'tags', [
                'post' => $args['post'],
                'with_category' => !empty($args['with_category'])
            ]); ?>
        </div>
        <h3>
            <a href="<?php echo get_permalink($args['post']->ID) ?>" class="line-clamp-4 <?php echo !empty($args['small_title']) ? 'lg:text-xl/6' : '' ?>">
                <?php echo $args['post']->post_title ?>
            </a>
        </h3>
    </div>
</article>