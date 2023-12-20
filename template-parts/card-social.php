<div class="p-9 flex flex-col gap-6 items-center justify-center rounded-br-4xl link-transition bg-black hover:bg-brand-solidgrey">
	<a href="<?php echo $args['social_media']->link; ?>" target="_blank">
		<img src="<?php echo $args['social_media']->image; ?>" alt="Logo" />
	</a>

	<div class="text-xs font-bold text-brand-lightgrey">
		<?php echo $args['social_media']->title; ?>
	</div>
</div>