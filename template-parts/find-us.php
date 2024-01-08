<?php
$socialMedias = [
	[
		'link' => 'https://www.youtube.com/@CarlifebyDani',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/youtube.svg',
		'title' => '@CarlifebyDani',
	],
	[
		'link' => 'https://instagram.com/carlifebydani',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/instagram.svg',
		'title' => '@carlifebydani',
	],
	[
		'link' => 'https://www.patreon.com/carlifebydani',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/patreon.svg',
		'title' => '@carlifebydani',
	],
	[
		'link' => 'https://open.spotify.com/show/2Bzp0LtBkArEZjV7D7V8HI',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/spotify.svg',
		'title' => '@CarlifebyDani',
	],
	[
		'link' => 'https://www.facebook.com/carlifebydani',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/fb.svg',
		'title' => '@carlifebydani',
	],
	[
		'link' => 'https://podcasts.apple.com/us/podcast/ev-masters-подкаст-за-електромобили/id1685415558',
		'image' => get_stylesheet_directory_uri() . '/images/icons/social/apple.svg',
		'title' => '@CarlifebyDani',
	]
];
?>
<div class="container py-12 bg-brand-grey">
	<div class="flex mb-8 justify-between items-center">
		<h3 class="title">Намерете ни в</h3>
	</div>

	<div class="grid gap-8 grid-cols-2 lg:grid-cols-6">
		<?php foreach ($socialMedias as $socialMedia) {
			get_template_part('template-parts/card-social', 'social-card', array('social_media' => $socialMedia));
		} ?>
	</div>
</div>