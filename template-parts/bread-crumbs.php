<div class="flex items-center gap-2">
    <?php foreach ($args['bread_crumbs'] as $key => $bread_crumb) {
        if ($key > 0) {
    ?>
            <span class="w-1.5 h-1.5 bg-brand-red rounded"></span>
        <?php } ?>
        <span class="text-xs font-bold uppercase">
            <a href="<?php echo $bread_crumb->link ?>" class="<?php echo $key == count($args['bread_crumbs']) - 1 ? 'text-brand-lightgrey' : '' ?>"><?php echo $bread_crumb->label  ?></a></span>
    <?php } ?>
</div>