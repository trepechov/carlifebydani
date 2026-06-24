<div class="flex items-center <?php echo !empty($args['small']) ? 'text-xs gap-1' : 'gap-2' ?>">
    <?php if (!empty($args['with_category'])) { ?>
        <span class="font-bold uppercase text-brand-lightgrey"><?php echo get_the_category($args['post']->ID)[0]->name ?></span>

        <span class="w-1.5 h-1.5 bg-brand-red rounded"></span>
    <?php } ?>
    <span class="text-sm text-brand-lightgrey"><?php echo date("d.m.Y", strtotime($args['post']->post_date)) ?></span>
</div>
