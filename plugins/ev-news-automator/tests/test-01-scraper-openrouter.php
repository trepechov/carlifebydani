<?php
/**
 * Test 1 — Scraper + OpenRouter
 *
 * Reads the configured news sources, scrapes up to 3 articles per source,
 * runs each through OpenRouter to produce a Bulgarian title + summary,
 * and writes the results to a JSON log. Nothing is written to Google Sheets.
 *
 * Run from Local's shell (Site → Open Shell), from the WordPress root:
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-01-scraper-openrouter.php
 *
 * Optional: limit articles per source
 *   wp eval-file ... --limit=1
 */

$limit = 3; // Change this to test fewer articles per source (e.g. 1 for a quick run)

$plugin     = ENA_Plugin::instance();
$settings   = $plugin->settings;
$scraper    = $plugin->scraper;
$openrouter = $plugin->openrouter;

$sources = $settings->sources();

if ( empty( $sources ) ) {
    WP_CLI::error( 'No sources configured. Add sources in EV News Automator → Settings.' );
}

WP_CLI::log( 'Model: ' . $settings->get( 'openrouter_model' ) );
WP_CLI::log( 'Sources: ' . count( $sources ) . ', limit per source: ' . $limit );
WP_CLI::log( str_repeat( '-', 60 ) );

$results = [];

foreach ( $sources as $source ) {
    WP_CLI::log( "\nSource: {$source['url']} [{$source['method']}]" );

    $items = $scraper->fetch_source( $source );

    if ( empty( $items ) ) {
        WP_CLI::warning( '  0 articles found — source may be down or malformed.' );
        $results[] = [ 'source' => $source['url'], 'status' => 'no_items', 'articles' => [] ];
        continue;
    }

    $slice = array_slice( $items, 0, $limit );
    WP_CLI::log( '  Found ' . count( $items ) . ' articles, summarising ' . count( $slice ) . '...' );

    $source_results = [];

    foreach ( $slice as $item ) {
        WP_CLI::log( '  · ' . $item['title'] );

        $summary = $openrouter->summarize( $item['title'], $item['excerpt'] ?? '' );

        $entry = [
            'original_title' => $item['title'],
            'original_url'   => $item['url'],
            'excerpt_snippet' => mb_substr( $item['excerpt'] ?? '', 0, 150 ),
        ];

        if ( is_wp_error( $summary ) ) {
            $entry['status']  = 'error';
            $entry['message'] = $summary->get_error_message();
            WP_CLI::warning( '    ✗ ' . $entry['message'] );
        } else {
            $entry['status']     = 'ok';
            $entry['bg_title']   = $summary['bg_title'];
            $entry['bg_summary'] = $summary['bg_summary'];
            WP_CLI::success( '    ✓ ' . $summary['bg_title'] );
            WP_CLI::log( '      ' . mb_substr( $summary['bg_summary'], 0, 120 ) . '...' );
        }

        $source_results[] = $entry;
    }

    $results[] = [
        'source'       => $source['url'],
        'total_found'  => count( $items ),
        'tested'       => count( $source_results ),
        'articles'     => $source_results,
    ];
}

// Write log
$log_dir  = ENA_PLUGIN_DIR . 'tests/output/';
$log_file = $log_dir . 'test-01-' . gmdate( 'Ymd-His' ) . '.json';
@mkdir( $log_dir, 0755, true );
file_put_contents( $log_file, json_encode( $results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

WP_CLI::log( str_repeat( '-', 60 ) );
WP_CLI::success( 'Done. Log written to:' );
WP_CLI::log( $log_file );
