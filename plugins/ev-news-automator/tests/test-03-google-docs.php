<?php
/**
 * Test 03 — Google Docs integration
 *
 * Verifies: append_sections, and the AEO «Резюме» brief (podcast_summary) incl. its Doc formatting.
 * Appends a test section to the manually pre-created doc and prints the URL.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-03-google-docs.php [tab=20.10.2026]
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

// ── 3. AEO «Резюме» briefs (podcast_summary) for real rows ───────────────
// Pass a tab name as the first argument (default 20.10.2026). Uses the same path as ENA_Podcast::run():
// scrape the article body, generate the brief, render it into the doc so the bolding can be checked by eye.
$tab = $args[0] ?? '20.10.2026';
WP_CLI::log( "\n[3] Generating «Резюме» briefs for 3 rows of tab {$tab}..." );
$rows = $plugin->storage->read_data_rows( $tab );
if ( is_wp_error( $rows ) ) WP_CLI::error( $rows->get_error_message() );

$brief_sections = [];
$brief_errors   = 0;
foreach ( array_slice( $rows, 0, 3 ) as $i => $row ) {
    $body  = $plugin->scraper->extract_body( $row['link'] );
    $brief = $openrouter->podcast_summary( $row['title'], $row['description'], is_wp_error( $body ) ? '' : $body, (string) ( $row['pub_date'] ?? '' ) );
    if ( is_wp_error( $brief ) ) {
        WP_CLI::warning( '  podcast_summary() error: ' . $brief->get_error_message() );
        $brief_errors++;
        continue;
    }
    [ $q_line, $a_line ] = array_pad( explode( "\n", $brief, 2 ), 2, '' );
    $checks = [
        'question line starts with label'  => str_starts_with( $q_line, ENA_OpenRouter::BRIEF_QUESTION_LABEL ),
        'question ends with "?"'           => str_ends_with( $q_line, '?' ),
        'answer line starts with label'    => str_starts_with( $a_line, ENA_OpenRouter::BRIEF_ANSWER_LABEL ),
        'answer is one paragraph, no list' => ! preg_match( '/\R|•/u', $a_line ),
    ];
    foreach ( $checks as $label => $ok ) {
        if ( ! $ok ) { WP_CLI::warning( "  row " . ( $i + 1 ) . ": {$label} — FAILED" ); $brief_errors++; }
    }
    WP_CLI::log( '  ' . ( is_wp_error( $body ) ? '[no body] ' : '' ) . $brief );
    $brief_sections[] = [
        'bg_title'    => '[TEST] ' . $row['title'],
        'url'         => $row['link'],
        'description' => $row['description'],
        'summary'     => $brief,
    ];
}
if ( $brief_sections ) {
    $r = $docs->append_sections( $doc_id, $brief_sections );
    if ( is_wp_error( $r ) ) WP_CLI::error( 'append_sections() with briefs failed: ' . $r->get_error_message() );
    WP_CLI::success( count( $brief_sections ) . ' brief(s) written — check that the Въпрос line and the Отговор label are bold.' );
}
if ( $brief_errors ) WP_CLI::error( "{$brief_errors} brief check(s) failed." );

WP_CLI::log( str_repeat( '-', 60 ) );
WP_CLI::success( 'Google Docs test complete.' );
WP_CLI::log( 'Test doc URL (open and inspect, then delete manually):' );
WP_CLI::log( $docs->doc_url( $doc_id ) );
