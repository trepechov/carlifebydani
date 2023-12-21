<a href="<?php echo $args['social_media']
    ->link; ?>" target="_blank" class="p-9 lg:p-5 xl:p-9 flex flex-col gap-6 items-center justify-center rounded-br-4xl link-transition bg-black hover:bg-brand-solidgrey">
		<img class="w-2/6 h-8 lg:w-10/12" src="<?php echo $args['social_media']->image; ?>" alt="Logo" />

		<div class="text-xs font-bold text-brand-lightgrey">
			<?php echo $args['social_media']->title; ?>
		</div>
</a>