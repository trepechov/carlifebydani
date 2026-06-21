<?php
/**
 * Test 2b — Google Docs + Drive integration
 *
 * Verifies: create_doc, move_to_folder, append_sections.
 * Creates a test document with one section, moves it to the configured Drive folder,
 * and prints the doc URL. You will need to delete the test doc manually from Drive.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-03-google-docs.php
 */

$plugin    = ENA_Plugin::instance();
$docs      = $plugin->docs;
$settings  = $plugin->settings;
$openrouter = $plugin->openrouter;

$folder_id = $settings->get( 'drive_folder_id' );
WP_CLI::log( 'Drive folder ID: ' . ( $folder_id ?: '(not set — doc will not be moved)' ) );
WP_CLI::log( str_repeat( '-', 60 ) );

// ── 0. Scope diagnostic — try creating via Drive API first ────────────────
WP_CLI::log( "\n[0] Drive API smoke test (create file via Drive, not Docs API)..." );
$auth        = $plugin->auth;
$drive_scope = [ 'https://www.googleapis.com/auth/drive' ];
$drive_token = $auth->get_access_token( $drive_scope );
if ( is_wp_error( $drive_token ) ) {
    WP_CLI::warning( 'Could not get drive token: ' . $drive_token->get_error_message() );
} else {
    // Create directly in the shared folder — avoids service account storage attribution
    $drive_resp = ENA_HTTP::post_json(
        'https://www.googleapis.com/drive/v3/files',
        [
            'name'     => 'ENA Drive Test',
            'mimeType' => 'application/vnd.google-apps.document',
            'parents'  => [ $folder_id ],
        ],
        [ 'Authorization' => "Bearer {$drive_token}" ]
    );
    $drive_data = ENA_HTTP::retrieve_json( $drive_resp );
    if ( is_wp_error( $drive_data ) ) {
        WP_CLI::warning( 'Drive create failed: ' . $drive_data->get_error_message() );
        $err_data = $drive_data->get_error_data();
        WP_CLI::warning( 'Body: ' . ( $err_data['body'] ?? print_r( $err_data, true ) ) );
    } elseif ( ! empty( $drive_data['id'] ) ) {
        WP_CLI::success( 'Drive API works! Created file ID: ' . $drive_data['id'] . ' — delete it manually from Drive.' );
    } else {
        WP_CLI::warning( 'Unexpected Drive response: ' . print_r( $drive_data, true ) );
    }
}

// ── 1. Use existing doc (bypasses create_doc storage issue) ───────────────
// Document pre-created manually and shared with the service account as Editor.
$doc_id = '1_bLGgYLyHIJkjZQ9oDi9RLfCXMAiFEQDyMRm_W_VMOQ';
WP_CLI::log( "\n[1] Using existing doc: {$doc_id}" );
WP_CLI::log( '  URL: ' . $docs->doc_url( $doc_id ) );

// ── 2. Append a test section ──────────────────────────────────────────────
WP_CLI::log( "\n[2] Appending test content section..." );
$sections = [
    [
        'bg_title' => 'Tesla представи нов модел с обсег от 700 км',
        'url'      => 'https://electrek.co/example-article',
        'script'   => 'В тази седмица Tesla обяви нов електрически автомобил, '
                    . 'чийто обсег надхвърля 700 километра на едно зареждане. '
                    . 'Моделът се очаква да навлезе на европейския пазар до края на годината.',
    ],
];

$append_result = $docs->append_sections( $doc_id, $sections );
if ( is_wp_error( $append_result ) ) {
    WP_CLI::error( 'append_sections() failed: ' . $append_result->get_error_message() );
}
WP_CLI::success( '1 section appended.' );

// ── 3. Move to Drive folder (if configured) ───────────────────────────────
if ( $folder_id ) {
    WP_CLI::log( "\n[3] Moving doc to configured Drive folder ({$folder_id})..." );
    $move_result = $docs->move_to_folder( $doc_id, $folder_id );
    if ( is_wp_error( $move_result ) ) {
        WP_CLI::warning( 'move_to_folder() failed: ' . $move_result->get_error_message() );
    } else {
        WP_CLI::success( 'Doc moved to folder.' );
    }
} else {
    WP_CLI::warning( "\n[3] Skipped move_to_folder — drive_folder_id not configured in settings." );
}

// ── 4. OpenRouter podcast script (optional smoke test) ────────────────────
WP_CLI::log( "\n[4] Smoke-testing OpenRouter podcast_script()..." );
$script = $openrouter->podcast_script(
    'Tesla представи нов модел с обсег от 700 км',
    'Tesla обяви нов електрически автомобил с обсег от 700 км. Моделът ще бъде наличен в Европа до края на годината.'
);
if ( is_wp_error( $script ) ) {
    WP_CLI::warning( 'podcast_script() error: ' . $script->get_error_message() );
} else {
    WP_CLI::success( 'podcast_script() OK:' );
    WP_CLI::log( mb_substr( $script, 0, 300 ) . '...' );
}

WP_CLI::log( str_repeat( '-', 60 ) );
WP_CLI::success( 'Google Docs test complete.' );
WP_CLI::log( 'Test doc URL (open and inspect, then delete manually):' );
WP_CLI::log( $docs->doc_url( $doc_id ) );
