<?php
/**
 * Test 6 — analyze() contract + AEO regression gate
 *
 * Calls the REAL ENA_OpenRouter::analyze() (production prompt from prompts/) on N articles and, for
 * the same input, the pre-1.3.0 prompt — then checks that nothing downstream can break:
 *
 *   HARD (fails the run)    contract: 6 keys with the right types, tags ≤ 8, region format;
 *                           format:   summary is 2-3 sentences, one paragraph, no list markers.
 *   REVIEW (listed, warns)  on_topic agreement with the legacy prompt (target ≥ 97% — a stability
 *                           check, not ground truth); numbers not found in the source text;
 *                           single-market region not named in the title / sentence 1.
 *   SOFT (reported only)    title / summary / sentence-1 lengths next to today's sheet numbers.
 *
 * Writes a markdown report to tests/output/test-06-*.md. Nothing is written to Sheets/Docs.
 *
 *   wp eval-file wp-content/plugins/ev-news-automator/tests/test-06-analyze-contract.php [source=20.10.2026|rss] [limit=20] [input=body|excerpt]
 */

require_once __DIR__ . '/lib-aeo.php';

$plugin = ENA_Plugin::instance();
$source = $args[0] ?? '20.10.2026';
$limit  = isset( $args[1] ) ? (int) $args[1] : 20;
$input  = ( $args[2] ?? 'body' ) === 'excerpt' ? 'excerpt' : 'body';

// Today's sheet lengths (tab 20.10.2026, 100 rows) — the soft targets are anchored to these.
const AEO_BASELINE = [ 'title' => [ 59, 77 ], 'summary' => [ 216, 254 ], 's1' => [ 114, 0 ] ];
const AEO_TARGET   = [ 'title' => 75, 'summary' => 250, 's1' => 120 ];

// Bulgarian market words expected in title/sentence 1 when region is a single market code.
const AEO_MARKET_WORDS = [
    'AU' => 'Австрал', 'US' => 'САЩ|Америк', 'CN' => 'Кита', 'DE' => 'Герман|Берлин|Мюнхен', 'EU' => 'Европ|ЕС',
    'GB' => 'Великобритан|Обединеното кралство|британ|Лондон', 'UK' => 'Великобритан|Обединеното кралство|британ',
    'JP' => 'Япон', 'KR' => 'Коре', 'IN' => 'Инди', 'FR' => 'Франц|Париж', 'NO' => 'Норвег', 'BG' => 'Българ',
    'CA' => 'Канад', 'IT' => 'Итал', 'ES' => 'Испан', 'NL' => 'Нидерланд|Холанд', 'SE' => 'Швец',
    'CZ' => 'Чехи|Чешк', 'PL' => 'Полш', 'AT' => 'Австри', 'CH' => 'Швейцар', 'TR' => 'Турц', 'RO' => 'Румън',
    'NZ' => 'Нова Зеландия', 'BR' => 'Бразил', 'MX' => 'Мексик', 'TH' => 'Тайланд', 'VN' => 'Виетнам',
];

$legacy_prompt = aeo_legacy_prompt( $plugin );
$items         = aeo_test_articles( $plugin, $source, $limit, $input );
WP_CLI::log( "Source {$source}, input {$input}: " . count( $items ) . ' articles' );

$hard = [];   // [ article#, message ]
$review = [ 'on_topic' => [], 'numbers' => [], 'market' => [] ];
$len = [ 'title' => [], 'summary' => [], 's1' => [] ];
$agree = 0; $compared = 0; $results = [];

foreach ( $items as $i => $item ) {
    $n = $i + 1;
    WP_CLI::log( sprintf( '[%d/%d] %s (%s)', $n, count( $items ), $item['title'], $item['text_source'] ) );

    $new = $plugin->openrouter->analyze( $item['title'], $item['text'], $item['pub_date'] );
    if ( is_wp_error( $new ) ) {
        if ( ENA_OpenRouter::is_fatal_batch_error( $new ) ) WP_CLI::error( 'OpenRouter: ' . $new->get_error_message() );
        $hard[] = [ $n, 'analyze() returned an error: ' . $new->get_error_message() ];
        continue;
    }

    // ── Contract (hard) ─────────────────────────────────────────────────────
    $types = [ 'bg_title' => 'is_string', 'bg_summary' => 'is_string', 'on_topic' => 'is_bool',
               'topic_reason' => 'is_string', 'tags' => 'is_array', 'region' => 'is_string' ];
    foreach ( $types as $key => $check ) {
        if ( ! array_key_exists( $key, $new ) || ! $check( $new[ $key ] ) ) $hard[] = [ $n, "key {$key} missing or wrong type" ];
    }
    if ( count( $new['tags'] ?? [] ) > 8 ) $hard[] = [ $n, 'more than 8 tags' ];
    if ( ! preg_match( '/^([A-Z]{2,6}(,[A-Z]{2,6}){0,2})?$/', (string) ( $new['region'] ?? '' ) ) ) $hard[] = [ $n, "bad region '{$new['region']}'" ];
    if ( trim( (string) $new['bg_title'] ) === '' ) $hard[] = [ $n, 'empty title' ];

    // ── Format (hard) ───────────────────────────────────────────────────────
    $summary   = (string) $new['bg_summary'];
    $sentences = aeo_sentences( $summary );
    if ( count( $sentences ) < 2 || count( $sentences ) > 3 ) $hard[] = [ $n, count( $sentences ) . ' sentences (want 2-3): ' . $summary ];
    // A spaced hyphen mid-sentence ("… автомобила - RCZ Vision …") is a dash, not a list marker.
    if ( preg_match( '/\R|•|^\s*[-*·]\s/u', $summary ) ) $hard[] = [ $n, 'line break or list marker in summary' ];

    // ── Lengths (soft) ──────────────────────────────────────────────────────
    $s1 = $sentences[0] ?? $summary;
    $len['title'][]   = mb_strlen( $new['bg_title'] );
    $len['summary'][] = mb_strlen( $summary );
    $len['s1'][]      = mb_strlen( $s1 );

    // ── on_topic stability vs legacy prompt (review) ────────────────────────
    $old = aeo_chat_json( $plugin, $legacy_prompt, aeo_user_message( $item ) );
    $old_topic = null;
    if ( ! is_wp_error( $old ) && array_key_exists( 'on_topic', $old ) ) {
        $v = $old['on_topic']; // same coercion as ENA_OpenRouter::parse_bool()
        $old_topic = is_bool( $v ) ? $v : ! in_array( strtolower( trim( (string) $v ) ), [ 'false', '0', 'no', '' ], true );
        $compared++;
        if ( $old_topic === $new['on_topic'] ) {
            $agree++;
        } else {
            $review['on_topic'][] = "#{$n} {$item['title']} — legacy " . var_export( $old_topic, true ) . ', new ' . var_export( $new['on_topic'], true ) . " ({$new['topic_reason']})";
        }
    }

    // ── Numbers not present in the source (review) ──────────────────────────
    $source_digits = [];
    // Join thousands groups ("1,000" / "41 900" → 1000 / 41900) on both sides before comparing.
    $join = fn ( string $t ) => preg_replace( '/(?<=\d)[,\x{00A0}\x{202F} ](?=\d{3}(?!\d))/u', '', $t );
    preg_match_all( '/\d+/', $join( $item['title'] . ' ' . $item['text'] ), $m );
    foreach ( $m[0] as $d ) $source_digits[ ltrim( $d, '0' ) ?: '0' ] = true;
    if ( $item['pub_date'] !== '' ) {
        foreach ( explode( '-', $item['pub_date'] ) as $d ) $source_digits[ ltrim( $d, '0' ) ?: '0' ] = true;
    }
    preg_match_all( '/\d+/', $join( $new['bg_title'] . ' ' . $summary ), $m );
    $unknown = array_unique( array_filter( $m[0], fn ( $d ) => ! isset( $source_digits[ ltrim( $d, '0' ) ?: '0' ] ) ) );
    if ( $unknown ) $review['numbers'][] = "#{$n} {$item['title']} — not in source: " . implode( ', ', $unknown ) . " — \"{$summary}\"";

    // ── Market named early (review) ─────────────────────────────────────────
    $region = (string) $new['region'];
    if ( isset( AEO_MARKET_WORDS[ $region ] ) && ! preg_match( '/' . AEO_MARKET_WORDS[ $region ] . '/iu', $new['bg_title'] . ' ' . $s1 ) ) {
        $review['market'][] = "#{$n} region {$region} not named in title/sentence 1 — {$new['bg_title']} / {$s1}";
    }

    $results[] = [ $n, $item, $new ];
}

// ── Report ───────────────────────────────────────────────────────────────────
$agree_pct = $compared ? round( 100 * $agree / $compared, 1 ) : 0;
$stat = function ( string $k ) use ( $len ) {
    $v = $len[ $k ];
    $within = $v ? round( 100 * count( array_filter( $v, fn ( $x ) => $x <= AEO_TARGET[ $k ] ) ) / count( $v ) ) : 0;
    $med = $v ? aeo_pct( $v, 0.5 ) : 0;
    return [ $med, aeo_pct( $v, 0.9 ), $v ? max( $v ) : 0, $within ];
};

$md  = "# analyze() contract — {$source} ({$input}) — " . gmdate( 'Y-m-d H:i' ) . " UTC\n\n";
$md .= 'Model: ' . $plugin->settings->get( 'openrouter_model' ) . ' · articles: ' . count( $items ) . "\n\n";
$md .= '**Hard failures: ' . count( $hard ) . "** · on_topic agreement with legacy: **{$agree_pct}%** ({$agree}/{$compared}, target ≥ 97%)\n\n";
$md .= "## Lengths (soft targets)\n\n| | median | p90 | max | ≤ target | today median / p90 |\n|---|---|---|---|---|---|\n";
foreach ( [ 'title' => 'Title', 'summary' => 'Summary', 's1' => 'Sentence 1' ] as $k => $label ) {
    [ $med, $p90, $max, $within ] = $stat( $k );
    $md .= "| {$label} | {$med} | {$p90} | {$max} | {$within}% ≤ " . AEO_TARGET[ $k ] . ' | ' . AEO_BASELINE[ $k ][0] . ( AEO_BASELINE[ $k ][1] ? ' / ' . AEO_BASELINE[ $k ][1] : '' ) . " |\n";
}
$md .= "\nExpected: summary median ≈ 220–240 and ≥ 80% of rows within each target. If well above, tighten prompts/aeo-rules.md / analysis-system.md — don't add code caps.\n\n";

$md .= "## Hard failures\n\n" . ( $hard ? implode( "\n", array_map( fn ( $h ) => "- #{$h[0]} {$h[1]}", $hard ) ) : '_none_' ) . "\n\n";
foreach ( [ 'on_topic' => 'on_topic disagreements (review by hand)', 'numbers' => 'Numbers not found in the source (review: unit conversions are expected here)', 'market' => 'Single-market region not named early (review)' ] as $k => $label ) {
    $md .= "## {$label}\n\n" . ( $review[ $k ] ? '- ' . implode( "\n- ", $review[ $k ] ) : '_none_' ) . "\n\n";
}

$md .= "## Outputs\n\n";
foreach ( $results as [ $n, $item, $new ] ) {
    $md .= "**{$n}. {$new['bg_title']}** (" . mb_strlen( $new['bg_title'] ) . ")  \n{$new['bg_summary']} (" . mb_strlen( $new['bg_summary'] ) . ")  \n"
         . '_region ' . ( $new['region'] ?: '—' ) . ' · ' . ( $new['on_topic'] ? 'on-topic' : 'OFF-TOPIC' ) . ' · ' . implode( ', ', $new['tags'] ) . " · {$item['text_source']}_  \n{$item['link']}\n\n";
}

$dir = ENA_PLUGIN_DIR . 'tests/output/';
@mkdir( $dir, 0755, true );
$file = $dir . 'test-06-' . gmdate( 'Ymd-His' ) . '.md';
file_put_contents( $file, $md );

WP_CLI::log( str_repeat( '-', 60 ) );
foreach ( [ 'title', 'summary', 's1' ] as $k ) {
    [ $med, $p90, $max, $within ] = $stat( $k );
    WP_CLI::log( sprintf( '%-8s median %3d  p90 %3d  max %3d  %3d%% ≤ %d', $k, $med, $p90, $max, $within, AEO_TARGET[ $k ] ) );
}
WP_CLI::log( "on_topic agreement {$agree_pct}% · numbers to review " . count( $review['numbers'] ) . ' · market misses ' . count( $review['market'] ) );
WP_CLI::log( "Report: {$file}" );
if ( $agree_pct < 97 ) WP_CLI::warning( 'on_topic agreement below 97% — review the disagreements in the report.' );
if ( $hard ) WP_CLI::error( count( $hard ) . ' hard failure(s) — see report.' );
WP_CLI::success( 'Contract holds.' );
