<?php

require_once 'constants.php';

add_theme_support('post-thumbnails');

function wpdocs_carlifebydani_scripts()
{
    wp_enqueue_style('theme-css', get_stylesheet_directory_uri() . '/css/style.min.css');
    wp_enqueue_style('glightbox-css', get_stylesheet_directory_uri() . '/css/glightbox.min.css');
    wp_enqueue_style('cookieconsent-css', get_stylesheet_directory_uri() . '/css/cookieconsent.min.css');
    wp_enqueue_script('gtag', get_stylesheet_directory_uri() . '/js/gtag.js');
    wp_enqueue_script('glightbox', get_stylesheet_directory_uri() . '/js/glightbox.min.js');
    wp_enqueue_script('glightbox-init', get_stylesheet_directory_uri() . '/js/glightbox.init.js', [
        'glightbox',
        'jquery',
    ]);
    wp_enqueue_script('cookieconsent', get_stylesheet_directory_uri() . '/js/cookieconsent.min.js', [], '', true);
    wp_enqueue_script(
        'cookieconsent-init',
        get_stylesheet_directory_uri() . '/js/cookieconsent.init.js',
        ['cookieconsent'],
        '',
        true
    );
    wp_enqueue_script('ogimageloader-init', get_stylesheet_directory_uri() . '/js/ogimageloader.init.js', ['jquery']);
}
add_action('wp_enqueue_scripts', 'wpdocs_carlifebydani_scripts');

function register_my_menus()
{
    register_nav_menus([
        'top-menu' => __('Top Menu'),
        'top-tags-menu' => __('Top Tags Menu'),
        'main-menu' => __('Main Menu'),
        'share-menu' => __('Share Menu'),
        'top-10-menu' => __('Top 10'),
        'footer-menu' => __('Footer Menu'),
        'bottom-menu' => __('Bottom Menu'),
    ]);
}
add_action('init', 'register_my_menus');

/*
 * WordPress: Remove unwonted image sizes.
 * In this code I remove the three sizes medium_large, 1536x1536, 2048x2048
 * See full article: https://bloggerpilot.com/en/disable-wordpress-image-sizes/
 */

add_filter('intermediate_image_sizes', function ($sizes) {
    return array_diff($sizes, ['medium_large']); // Medium Large (768 x 0)
});

add_action('init', 'j0e_remove_large_image_sizes');
function j0e_remove_large_image_sizes()
{
    remove_image_size('1536x1536'); // 2 x Medium Large (1536 x 1536)
    remove_image_size('2048x2048'); // 2 x Large (2048 x 2048)
}

// Use jquery selectroin glightbox.initjs instead of this
// function glightbox_class($content)
// {
//     // global $post;
//     $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
//     $replacement = '<a$1 class="glightbox" href=$2$3.$4$5$6>';
//     $content = preg_replace($pattern, $replacement, $content);
//     return $content;
// }
// add_filter('the_content', 'glightbox_class');

function add_tag_links_to_content($content)
{
    if (is_single()) {
        $post_tags = get_the_tags();

        if ($post_tags) {
            while ($tag = array_pop($post_tags)) {
                // Reverse loop, when have tags like #Renault #Renault 5, link the extended tag first
                $tag_link = get_tag_link($tag->term_id);
                $tag_link_html = '<a href="' . esc_url($tag_link) . '">' . esc_html($tag->name) . '</a>';
                $content = preg_replace(
                    '/(<((?!a|td|strong|h2|h3|figcaption)[^>]*)>[^<]*?\b)' .
                        preg_quote($tag->name, '/') .
                        '(\b.*?<\/[^>]*>)/iu',
                    '$1' . $tag_link_html . '$3',
                    $content,
                    5
                );
            }
        }
    }

    return $content;
}
add_filter('the_content', 'add_tag_links_to_content');

function add_blank_to_links($content)
{
    if (is_single() || is_page()) {
        $content = preg_replace(
            '/<a\s+href\s*=\s*["\'](https?:\/\/(?!' .
                preg_quote($_SERVER['SERVER_NAME'], '/') .
                ')[^"\']+)["\'](?![^>]*\srel=)([^>]*)>/iu',
            '<a href="$1" target="_blank" rel="nofollow"$3>',
            $content
        );
    }

    return $content;
}
add_filter('the_content', 'add_blank_to_links');

function parts_request_form_shortcode()
{
    ob_start();

    if (isset($_GET['message'])) {
        switch ($_GET['message']) {
            case 'success':
                echo '<p style="color: green;">Твоята заявка е успешна!</p>';
                break;
            case 'missing_fields':
                echo '<p style="color: red;">Попълни всички задължителни полета.</p>';
                break;
            case 'invalid_email':
                echo '<p style="color: red;">Невалиден имейл адрес.</p>';
                break;
            case 'db_error':
                echo '<p style="color: red;">Грешка. Опитай отново.</p>';
                break;
            case 'nonce_error':
                echo '<p style="color: red;">Security verification failed.</p>';
                break;
            case 'spam_detected':
                echo '<p style="color: red;">Spam detected. Submission blocked.</p>';
                break;
        }
    }
    ?>
    <form method="post" action="" class="flex flex-col gap-4">
        <?php wp_nonce_field('parts_request_action', 'parts_request_nonce'); ?>

        <input type="hidden" name="hidden_field" value=""> <!-- Honeypot -->

        <div>
            <label for="chassis">Шаси:</label>
            <select name="chassis" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
                <option value="">Избери шаси</option>
                <option value="A">Шаси A</option>
                <option value="B">Шаси B</option>
                <option value="C">Шаси C</option>
            </select>
        </div>

        <div>
            <label for="part">Част:</label>
            <input type="text" name="part" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
        </div>

        <div>
            <label for="description">Описание:</label>
            <textarea name="description" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none"></textarea>
        </div>

        <div>
            <label for="name">Име:</label>
            <input type="text" name="name" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
        </div>

        <div>
            <label for="phone">Телефон:</label>
            <input type="tel" name="phone" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
        </div>

        <div>
            <label for="email">Имейл:</label>
            <input type="email" name="email" required class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
        </div>

        <button type="submit" name="parts_request_submit" class="bg-black text-lg p-4">Изпрати твоята заявка</button>
    </form>
    <?php return ob_get_clean();
}
add_shortcode('parts_request_form', 'parts_request_form_shortcode');

function handle_parts_request_submission()
{
    if (isset($_POST['parts_request_submit'])) {
        // Check nonce for security
        if (
            !isset($_POST['parts_request_nonce']) ||
            !wp_verify_nonce($_POST['parts_request_nonce'], 'parts_request_action')
        ) {
            wp_redirect(add_query_arg('message', 'nonce_error', wp_get_referer()));
            exit();
        }

        // Honeypot check
        if (!empty($_POST['hidden_field'])) {
            wp_redirect(add_query_arg('message', 'spam_detected', wp_get_referer()));
            exit();
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'parts_req';

        // Sanitize input data
        $chassis = sanitize_text_field($_POST['chassis']);
        $part = sanitize_text_field($_POST['part']);
        $description = sanitize_textarea_field($_POST['description']);
        $name = sanitize_text_field($_POST['name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);

        if (empty($chassis) || empty($part) || empty($description) || empty($name) || empty($phone) || empty($email)) {
            wp_redirect(add_query_arg('message', 'missing_fields', wp_get_referer()));
            exit();
        }

        if (!is_email($email)) {
            wp_redirect(add_query_arg('message', 'invalid_email', wp_get_referer()));
            exit();
        }

        $inserted = $wpdb->insert(
            $table_name,
            [
                'chassis' => $chassis,
                'part' => $part,
                'description' => $description,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($inserted) {
            wp_redirect(add_query_arg('message', 'success', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('message', 'db_error', wp_get_referer()));
        }
        exit();
    }
}
add_action('init', 'handle_parts_request_submission');

function create_parts_req_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parts_req';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        chassis VARCHAR(255) NOT NULL,
        part VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

add_action('after_setup_theme', 'create_parts_req_table');
