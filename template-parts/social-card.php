<div class="p-9 flex flex-col gap-6 items-center justify-center rounded-br-4xl cursor-pointer bg-black hover:bg-brand-solidgrey">
		<a href="<?php echo get_permalink($args['card']->link); ?>" target="_blank">
				<img src="<?php echo $args['card']->image; ?>" alt="Logo" />
		</a>

		<div class="text-xs font-bold text-brand-lightgrey">
				<?php echo $args['card']->title; ?>
		</div>
</div>