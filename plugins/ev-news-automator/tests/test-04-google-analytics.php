<?php
/**
 * Test 4 — Google Analytics (GA4) smoke test
 *
 * Verifies that the service account has read access to the configured GA4
 * property and that the ev_news_click event data can be retrieved.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-04-google-analytics.php
 *
 * Optional: change the look-back window (default 7 days):
 *   wp eval-file ... --days=30
 */

$days_back = isset( $args['days'] ) ? (int) $args['days'] : 7;

$plugin      = ENA_Plugin::instance();
$auth        = $plugin->auth;
$analytics   = $plugin->analytics;
$settings    = $plugin->settings;

$property_id = $settings->ga4_property_id();

WP_CLI::log( 'GA4 Property ID : ' . ( $property_id ?: '(not set)' ) );
WP_CLI::log( 'Look-back       : ' . $days_back . ' days' );
WP_CLI::log( str_repeat( '-', 60 ) );

// ── 0. Pre-flight checks ──────────────────────────────────────────────────
if ( empty( $property_id ) ) {
    WP_CLI::error( 'ga4_property_id is not configured. Set it in EV News Automator → Settings.' );
}

if ( empty( $settings->service_account_path() ) ) {
    WP_CLI::error( 'service_account_path is not configured. Set it in EV News Automator → Settings.' );
}

// ── 1. Auth token ─────────────────────────────────────────────────────────
WP_CLI::log( "\n[1] Obtaining Google access token (analytics.readonly scope)..." );
$token = $auth->get_access_token( [ 'https://www.googleapis.com/auth/analytics.readonly' ] );
if ( is_wp_error( $token ) ) {
    WP_CLI::error( 'get_access_token() failed: ' . $token->get_error_message() . "\nData: " . print_r( $token->get_error_data(), true ) );
}
WP_CLI::success( 'Token obtained (first 20 chars): ' . substr( $token, 0, 20 ) . '...' );

// ── 2. Fetch click data via ENA_Analytics ─────────────────────────────────
WP_CLI::log( "\n[2] Calling fetch_clicks() with no URL filter (all ev_news_click events)..." );

// We pass an empty array so every URL returned by GA4 is included.
// The method seeds the result map from $urls, so passing [] means we get
// whatever GA4 returns without filtering — perfect for a smoke test.
$result = $analytics->fetch_clicks( [], $days_back );

if ( is_wp_error( $result ) ) {
    WP_CLI::error( 'fetch_clicks() failed: ' . $result->get_error_message() . "\nData: " . print_r( $result->get_error_data(), true ) );
}

$total_urls   = count( $result );
$total_clicks = array_sum( $result );

WP_CLI::success( "GA4 responded successfully." );
WP_CLI::log( "  Unique URLs with ev_news_click events : {$total_urls}" );
WP_CLI::log( "  Total click events in last {$days_back} days : {$total_clicks}" );

if ( $total_urls === 0 ) {
    WP_CLI::warning( 'No ev_news_click events found. This is expected if the site has not received traffic yet, or if GA4 event tracking is not configured on the frontend.' );
} else {
    // Show top 10 most-clicked URLs
    arsort( $result );
    $top = array_slice( $result, 0, 10, true );
    WP_CLI::log( "\n  Top " . count( $top ) . " URLs by clicks:" );
    $rank = 1;
    foreach ( $top as $url => $clicks ) {
        WP_CLI::log( sprintf( "  %2d. (%3d clicks)  %s", $rank++, $clicks, $url ) );
    }
}

// ── 3. Write output log ───────────────────────────────────────────────────
$log_dir  = ENA_PLUGIN_DIR . 'tests/output/';
$log_file = $log_dir . 'test-04-' . gmdate( 'Ymd-His' ) . '.json';
@mkdir( $log_dir, 0755, true );
file_put_contents( $log_file, json_encode( [
    'property_id' => $property_id,
    'days_back'   => $days_back,
    'total_urls'  => $total_urls,
    'total_clicks'=> $total_clicks,
    'results'     => $result,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );

WP_CLI::log( str_repeat( '-', 60 ) );
WP_CLI::success( 'GA4 smoke test complete. Log written to:' );
WP_CLI::log( $log_file );
