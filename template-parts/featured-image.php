<?php
if (has_post_thumbnail($args['post_id'])) {
    echo get_the_post_thumbnail($args['post_id'], $args['size'], array('class' => (isset($args['class']) ? $args['class'] : '')));
} else {
    echo '<img src="' . get_stylesheet_directory_uri() . '/images/noimage' . (isset($args['size']) && $args['size'] == 'medium' ? '-640x360' : '') . '.jpg" alt="" class="' . (isset($args['class']) ? $args['class'] : '') . '" />';
}
