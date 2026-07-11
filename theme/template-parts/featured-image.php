<?php
/*
 * WP's default `sizes` attribute assumes the image displays at up to
 * its intrinsic width (e.g. 300px for 'medium'), which is far narrower
 * than these cards/hero actually render at. That mismatch is what lets
 * mobile browsers pick the full-resolution srcset candidate instead of
 * a properly downscaled one, so we pass an accurate `sizes` per layout.
 */
$image_attr = ['class' => (isset($args['class']) ? $args['class'] : '')];
$image_attr['sizes'] = $args['sizes'] ?? '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw';

if (has_post_thumbnail($args['post_id'])) {
    echo get_the_post_thumbnail($args['post_id'], $args['size'], $image_attr);
} else {
    echo '<img src="' . get_stylesheet_directory_uri() . '/images/noimage' . (isset($args['size']) && $args['size'] == 'medium' ? '-640x360' : '') . '.jpg" alt="" class="' . (isset($args['class']) ? $args['class'] : '') . '" />';
}
