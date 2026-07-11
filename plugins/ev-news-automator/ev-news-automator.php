<?php
/**
 * Plugin Name: EV News Automator
 * Description: Automated Bulgarian EV news collection, summarization, and podcast script generation.
 * Version: 1.1.3
 * Author: Car Life by Dani
 * Text Domain: ev-news-automator
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ENA_VERSION',          '1.1.3' );
define( 'ENA_PLUGIN_FILE',      __FILE__ );
define( 'ENA_PLUGIN_DIR',       plugin_dir_path( __FILE__ ) );
define( 'ENA_PLUGIN_URL',       plugin_dir_url( __FILE__ ) );

// wp_options keys — define here, reference everywhere else
define( 'ENA_OPT_SETTINGS',           'ena_settings' );
define( 'ENA_OPT_LIVE_ARTICLES',      'ev_news_live_articles' );
define( 'ENA_OPT_GOOGLE_TOKEN',       'ena_google_token' );
define( 'ENA_OPT_SHEET_META',         'ena_sheet_meta' );
define( 'ENA_OPT_RUN_LOG',            'ena_run_log' );
define( 'ENA_OPT_CRON_TRANSCRIPT',    'ena_cron_transcript' );
define( 'ENA_OPT_STATUS_COLLECTION',  'ena_status_last_collection' );
define( 'ENA_OPT_STATUS_SYNC',        'ena_status_last_sync' );
define( 'ENA_OPT_STATUS_PODCAST',     'ena_status_last_podcast' );
define( 'ENA_OPT_ACTIVE_JOB',         'ena_active_job' );

// Load all includes
require_once ENA_PLUGIN_DIR . 'includes/class-ena-http.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-settings.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-logger.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-google-auth.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-sheets.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-analytics.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-docs.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-openrouter.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-scraper.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-collector.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-sync.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-podcast.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-push.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-pwa.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-background.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-cron.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-ajax.php';
require_once ENA_PLUGIN_DIR . 'includes/class-ena-plugin.php';
require_once ENA_PLUGIN_DIR . 'admin/class-ena-admin.php';

register_activation_hook( __FILE__, [ 'ENA_Cron', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'ENA_Cron', 'deactivate' ] );

add_action( 'plugins_loaded', [ 'ENA_Plugin', 'instance' ] );
