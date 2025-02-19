<?php
$locations = get_nav_menu_locations();
$share_menu = wp_get_nav_menu_object($locations['share-menu']);
$shareMenu = wp_get_nav_menu_items($share_menu->term_id);
?>
<div class="bg-black">
	<div class="wrapper py-20 grid gap-8 lg:grid-cols-3">
		<div class="relative lg:pt-12 lg:pr-14">
			<div class="mb-8 uppercase">
				<h3 class="title"><?php echo get_the_title(SHARE_WITH_US_PAGE_ID); ?></h3>
			</div>

			<div class="text-base text-brand-lightgrey">
				<?php echo get_post_meta(SHARE_WITH_US_PAGE_ID, 'post-subtitle', true); ?></p>
			</div>

			<div class="hidden lg:block absolute top-[50px] -right-8 w-12 h-5/6 bg-carbon-stripe-white"></div>
		</div>

		<?php
		foreach ($shareMenu as $key => $menuItem) {
		?>
			<a href="<?php echo $menuItem->url ?>" class="group">
				<div class="items-center flex justify-end relative bg-size-1/3 bg-no-repeat bg-left lg:h-[600px] lg:items-start lg:bg-cover lg:bg-center lg:rounded-br-4xl" style="background-image: url('<?php echo get_the_post_thumbnail_url($menuItem->object_id, 'full') ?>)">
					<div class="overlay bg-from-black-80-gradient group-hover:bg-from-red-gradient"></div>

					<div class="z-10 w-2/3 py-16 px-8 lg:w-full lg:py-10 lg:text-center">
						<h4 class="mb-3"><?php echo $menuItem->title ?></h4>
						<p class="text-lg italic text-brand-lightgrey group-hover:text-white">
							<?php echo get_post_meta($menuItem->object_id, 'post-subtitle', true); ?></p>
					</div>
				</div>
			</a>
		<?php
		}
		?>

	</div>
</div>