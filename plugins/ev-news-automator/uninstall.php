<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

$keys = [
    'ena_settings',
    'ev_news_live_articles',
    'ena_google_token',
    'ena_sheet_meta',
    'ena_run_log',
    'ena_cron_transcript',
    'ena_status_last_collection',
    'ena_status_last_sync',
    'ena_status_last_podcast',
];

foreach ( $keys as $key ) {
    delete_option( $key );
}
