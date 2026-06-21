<?php
/**
 * Test 03 — Google Docs integration
 *
 * Verifies: append_sections, podcast_summary.
 * Appends a test section to the manually pre-created doc and prints the URL.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-03-google-docs.php
 */

$plugin     = ENA_Plugin::instance();
$docs       = $plugin->docs;
$openrouter = $plugin->openrouter;

WP_CLI::log( str_repeat( '-', 60 ) );

// ── 1. Use existing doc ───────────────────────────────────────────────────
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

// ── 3. OpenRouter podcast script (optional smoke test) ────────────────────
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
