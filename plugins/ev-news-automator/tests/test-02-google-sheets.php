<?php
/**
 * Test 2a — Google Sheets integration
 *
 * Verifies: auth token, sheet listing, active sheet detection,
 * read_data_rows (column structure), append_rows (test row), update_clicks,
 * and delete_rows cleanup. Leaves the sheet in its original state.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-02-google-sheets.php
 */

$plugin   = ENA_Plugin::instance();
$storage  = $plugin->storage;
$settings = $plugin->settings;

$spreadsheet_id = $settings->get( 'spreadsheet_id' );
WP_CLI::log( 'Spreadsheet ID: ' . ( $spreadsheet_id ?: '(not set)' ) );
WP_CLI::log( str_repeat( '-', 60 ) );

// ── 0. Auth token smoke test ──────────────────────────────────────────────
WP_CLI::log( "\n[0] Obtaining Google access token..." );
$auth  = $plugin->auth;
$token = $auth->get_access_token( [ 'https://www.googleapis.com/auth/spreadsheets' ] );
if ( is_wp_error( $token ) ) {
    WP_CLI::error( 'get_access_token() failed: ' . $token->get_error_message() . "\nData: " . print_r( $token->get_error_data(), true ) );
}
WP_CLI::success( 'Token obtained (first 20 chars): ' . substr( $token, 0, 20 ) . '...' );

// ── 1. List all sheet tabs ────────────────────────────────────────────────
WP_CLI::log( "\n[1] Listing sheet tabs..." );
$sheets = $storage->list_sheets();
if ( is_wp_error( $sheets ) ) {
    $data = $sheets->get_error_data();
    WP_CLI::error( 'list_sheets() failed: ' . $sheets->get_error_message() . "\nGoogle response body: " . ( $data['body'] ?? print_r( $data, true ) ) );
}
foreach ( $sheets as $s ) {
    WP_CLI::log( "  · \"{$s['title']}\"  (sheetId={$s['id']})" );
}
WP_CLI::success( count( $sheets ) . ' tab(s) found.' );

// ── 2. Active sheet ───────────────────────────────────────────────────────
WP_CLI::log( "\n[2] Detecting active sheet..." );
$active = $storage->active_sheet_name();
if ( is_wp_error( $active ) ) {
    WP_CLI::error(
        $active->get_error_message() . "\n\n"
        . "Action required: open the Google Sheet and create a tab named in DD.MM.YYYY format\n"
        . "(e.g. '" . gmdate( 'd.m.Y' ) . "') with this header row:\n"
        . "title | description | link | author | upvote | downvote | clicks | added_date | summary"
    );
}
WP_CLI::success( "Active sheet: \"{$active}\"" );

// ── 3. Read current rows and inspect column structure ─────────────────────
WP_CLI::log( "\n[3] Reading data rows from active sheet..." );
$rows = $storage->read_data_rows();
if ( is_wp_error( $rows ) ) {
    WP_CLI::error( 'read_data_rows() failed: ' . $rows->get_error_message() );
}

$expected_keys = [ 'title', 'description', 'link', 'author', 'upvote', 'downvote', 'clicks', 'added_date', 'session_date' ];

if ( empty( $rows ) ) {
    WP_CLI::warning( 'Sheet is empty — column structure cannot be verified from data rows.' );
} else {
    $first = $rows[0];
    $got   = array_keys( $first );
    $missing = array_diff( $expected_keys, $got );
    $extra   = array_diff( $got, $expected_keys );

    if ( empty( $missing ) ) {
        WP_CLI::success( 'Column structure OK: ' . implode( ', ', $got ) );
    } else {
        WP_CLI::warning( 'Missing keys: ' . implode( ', ', $missing ) );
        WP_CLI::warning( 'Got keys: ' . implode( ', ', $got ) );
    }
    if ( ! empty( $extra ) ) {
        WP_CLI::warning( 'Extra unexpected keys: ' . implode( ', ', $extra ) );
    }

    WP_CLI::log( '  Row count: ' . count( $rows ) );
    WP_CLI::log( '  First row:' );
    foreach ( $first as $k => $v ) {
        WP_CLI::log( "    {$k}: " . mb_substr( (string) $v, 0, 80 ) );
    }
}

// ── 4. Append a test row ──────────────────────────────────────────────────
WP_CLI::log( "\n[4] Appending a test row..." );
$test_url = 'https://example.com/ena-test-row-' . time();
$test_row = [
    'title'       => '[ENA TEST] Тестово заглавие',
    'description' => '[ENA TEST] Тестово описание на статия.',
    'link'        => $test_url,
    'author'      => 'ENA Test',
    'upvote'      => '',
    'downvote'    => '',
    'clicks'      => 0,
];

$append_result = $storage->append_rows( [ $test_row ] );
if ( is_wp_error( $append_result ) ) {
    WP_CLI::error( 'append_rows() failed: ' . $append_result->get_error_message() );
}
WP_CLI::success( 'Test row appended. URL: ' . $test_url );

// ── 5. Read back and verify the test row ──────────────────────────────────
WP_CLI::log( "\n[5] Reading back rows to verify..." );
$rows_after = $storage->read_data_rows();
if ( is_wp_error( $rows_after ) ) {
    WP_CLI::error( 'read_data_rows() after append failed: ' . $rows_after->get_error_message() );
}

$test_row_index = null;
foreach ( $rows_after as $i => $row ) {
    if ( $row['link'] === $test_url ) {
        $test_row_index = $i;
        WP_CLI::success( "Test row found at data index {$i}:" );
        WP_CLI::log( "  title:      {$row['title']}" );
        WP_CLI::log( "  link:       {$row['link']}" );
        WP_CLI::log( "  clicks:     {$row['clicks']}" );
        WP_CLI::log( "  added_date: {$row['added_date']}" );
        break;
    }
}

if ( $test_row_index === null ) {
    WP_CLI::error( 'Test row not found after append — something is wrong with append_rows().' );
}

// ── 6. update_clicks on the test row ──────────────────────────────────────
WP_CLI::log( "\n[6] Testing update_clicks() on test row..." );
$click_result = $storage->update_clicks( [ $test_url => 42 ] );
if ( is_wp_error( $click_result ) ) {
    WP_CLI::error( 'update_clicks() failed: ' . $click_result->get_error_message() );
}

// Read back to verify
$rows_clicked = $storage->read_data_rows();
$verified_clicks = null;
foreach ( $rows_clicked as $row ) {
    if ( $row['link'] === $test_url ) {
        $verified_clicks = (int) $row['clicks'];
        break;
    }
}

if ( $verified_clicks === 42 ) {
    WP_CLI::success( 'update_clicks() OK — clicks set to 42 and verified.' );
} else {
    WP_CLI::warning( "update_clicks() mismatch — expected 42, got {$verified_clicks}." );
}

// ── 7. Delete the test row (cleanup) ──────────────────────────────────────
WP_CLI::log( "\n[7] Cleaning up — deleting test row at index {$test_row_index}..." );
$delete_result = $storage->delete_rows( [ $test_row_index ] );
if ( is_wp_error( $delete_result ) ) {
    WP_CLI::error( 'delete_rows() failed: ' . $delete_result->get_error_message() );
}

// Verify deletion
$rows_final = $storage->read_data_rows();
$still_there = false;
foreach ( $rows_final as $row ) {
    if ( $row['link'] === $test_url ) {
        $still_there = true;
        break;
    }
}
if ( $still_there ) {
    WP_CLI::warning( 'Test row still present after delete — check delete_rows().' );
} else {
    WP_CLI::success( 'Test row deleted. Sheet restored to original state.' );
}

WP_CLI::log( str_repeat( '-', 60 ) );
WP_CLI::success( 'Google Sheets test complete.' );
