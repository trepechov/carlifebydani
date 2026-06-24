<div class="p-5 pb-8 bg-brand-red rounded-br-4xl">
    <p class="text-black">Категория</p>
    <h5><a href="<?php echo get_category_link($args['category']->term_id) ?>"><?php echo $args['category']->name; ?></a></h5>
    <p class="text-black mt-2">Дата на публикуване</p>
    <h5><?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?></h5>
    <p class="text-black mt-2">Автор</p>
    <h5>
        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" class="hover:underline"><?php echo get_the_author(); ?></a>
    </h5>

    <?php
    if ([] != $args['tags']) {
    ?>
        <p class="text-black mt-2">Тагове</p>
        <div class="flex flex-wrap gap-x-2">
            <?php
            foreach ($args['tags'] as $tag) {
                echo '<a href="' . get_tag_link($tag->term_id) . '" class="hover:underline"><h5>#' . $tag->name . '</h5></a>';
            }
            ?>
        </div>

    <?php
    }
    ?>
</div>