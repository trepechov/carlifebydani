<div class="flex items-center gap-2">
    <?php if (isset($args['with_category']) && $args['with_category']) { ?>
        <span class="font-bold uppercase"><?php echo get_the_category($args['post']->ID)[0]->name ?></span>

        <span class="w-1.5 h-1.5 bg-brand-red rounded"></span>
    <?php } ?>
    <span class="text-sm"><?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?></span>
</div>