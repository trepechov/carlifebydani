<?php
$locations = get_nav_menu_locations();
$share_menu = wp_get_nav_menu_object($locations['share-menu']);
$shareMenu = wp_get_nav_menu_items($share_menu->term_id);
?>
<div class="bg-black">
	<div class="container py-20 grid gap-8 lg:grid-cols-3">
		<div class="relative lg:pt-12 lg:pr-14">
			<div class="mb-8 uppercase">
				<h3 class="title">Сподели с нас</h3>
			</div>

			<div class="text-base text-brand-lightgrey">
				Lorem Ipsum е елементарен примерен текст, използван в печатарската и типографската индустрия.
			</div>

			<div class="hidden lg:block absolute top-[50px] -right-8 w-12 h-5/6 bg-carbon-stripe-white"></div>
		</div>

		<?php
		foreach ($shareMenu as $key => $menuItem) {
		?>
			<a href="<?php echo $menuItem->url ?>" class="group">
				<div class="items-center flex justify-end relative bg-size-1/3 bg-no-repeat bg-left lg:h-[600px] lg:items-start lg:bg-cover lg:bg-center lg:rounded-br-4xl" style="background-image: url('<?php echo get_the_post_thumbnail_url($menuItem->object_id, 'full') ?>)">
					<div class="overlay bg-from-black-gradient group-hover:bg-from-red-gradient"></div>

					<div class="z-10 w-2/3 py-16 px-8 lg:w-full lg:py-10 lg:text-center">
						<h4 class="mb-6"><?php echo $menuItem->title ?></h4>
						<p class="text-lg italic text-brand-lightgrey group-hover:text-brand-solidgrey link-transition">
							<?php
							switch ($key) {
								case 0:
									echo 'Теми, които ме вълнуват';
									break;
								case 1:
									echo 'Запиши колата си за ревю';
									break;
								default:
									break;
							}
							?></p>
					</div>
				</div>
			</a>
		<?php
		}
		?>

	</div>
</div>

</div>