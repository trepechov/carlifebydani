<?php
/**
 * Test 5 — AEO prompt A/B
 *
 * Runs the same articles through the pre-1.3.0 prompt (`legacy`), the production prompt built from
 * prompts/analysis-system.md (`production`) and every tests/prompts/*.txt variant, then writes a
 * side-by-side markdown report. Nothing is written to Sheets/Docs. Add a variant = drop a .txt file.
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-05-aeo-prompt-ab.php [source=20.10.2026|rss] [limit=0] [input=body|excerpt]
 *
 *   source  a sheet tab name, or "rss" to pull fresh items from the configured sources
 *   input   "body" = scraped article body (production since 1.3.0); "excerpt" = RSS excerpt only
 *           (on a sheet tab, which stores no excerpts, "excerpt" uses the sheet description)
 */

require_once __DIR__ . '/lib-aeo.php';

$plugin = ENA_Plugin::instance();
$source = $args[0] ?? '20.10.2026';
$limit  = isset( $args[1] ) ? (int) $args[1] : 0; // 0 = all
$input  = ( $args[2] ?? 'body' ) === 'excerpt' ? 'excerpt' : 'body';

$build = new ReflectionMethod( $plugin->openrouter, 'build_analysis_system_prompt' );
$build->setAccessible( true );

$variants = [
    'legacy'     => aeo_legacy_prompt( $plugin ),
    'production' => $build->invoke( $plugin->openrouter ),
];
foreach ( glob( ENA_PLUGIN_DIR . 'tests/prompts/*.txt' ) as $file ) {
    $variants[ basename( $file, '.txt' ) ] = trim( file_get_contents( $file ) );
}
WP_CLI::log( 'Variants: ' . implode( ', ', array_keys( $variants ) ) );

$items = aeo_test_articles( $plugin, $source, $limit, $input );
WP_CLI::log( "Source {$source}, input {$input}: " . count( $items ) . ' articles' );

$md = "# AEO prompt A/B — {$source} ({$input}) — " . gmdate( 'Y-m-d H:i' ) . " UTC\n\nModel: " . $plugin->settings->get( 'openrouter_model' )
    . "\n\n`current` = what the sheet holds today (sheet tabs only).\n\n";

$dir  = ENA_PLUGIN_DIR . 'tests/output/';
@mkdir( $dir, 0755, true );
$file = $dir . 'test-05-' . gmdate( 'Ymd-His' ) . '.md';

foreach ( $items as $i => $item ) {
    WP_CLI::log( sprintf( '[%d/%d] %s', $i + 1, count( $items ), $item['title'] ) );

    $md .= "---\n\n## " . ( $i + 1 ) . ". {$item['title']}\n\n{$item['link']}\n\nSource: {$item['text_source']}\n\n";
    if ( $item['current_desc'] !== '' ) {
        $md .= "**current** — {$item['current_title']}\n\n> {$item['current_desc']}\n\n";
    }

    foreach ( $variants as $name => $system ) {
        $out = aeo_chat_json( $plugin, $system, aeo_user_message( $item ) );
        $md .= "### {$name}\n\n```json\n"
             . ( is_wp_error( $out ) ? 'ERROR: ' . $out->get_error_message() : json_encode( $out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) )
             . "\n```\n\n";
        if ( is_wp_error( $out ) && ENA_OpenRouter::is_fatal_batch_error( $out ) ) break 2;
    }
    file_put_contents( $file, $md ); // incremental: survive a killed run
}

file_put_contents( $file, $md );
WP_CLI::success( "Report: {$file}" );
