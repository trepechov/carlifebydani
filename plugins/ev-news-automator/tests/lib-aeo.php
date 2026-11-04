<?php
/**
 * Shared helpers for the AEO tests (test-05 A/B, test-06 contract). Loaded with require_once
 * from inside `wp eval-file`, so WordPress and the plugin classes are available.
 */

/**
 * Articles to run through analyze(), in the same shape the collector would see them.
 *
 *   $source = a sheet tab name (e.g. "20.10.2026")  → rows of that tab; no excerpt is stored, so
 *             'excerpt' mode falls back to the sheet description
 *           = "rss"                                  → fresh items from the configured sources
 *   $mode   = "body"    → scraped article body (first 1500 chars, = ENA_Collector::ARTICLE_BODY_CAP),
 *                         falling back to the excerpt/description like production does
 *           = "excerpt" → RSS excerpt only (the pre-1.3.0 production input)
 *
 * Returns a list of [ title, link, text, text_source, pub_date, current_title, current_description ].
 */
function aeo_test_articles( ENA_Plugin $plugin, string $source, int $limit, string $mode ): array {
    $items = [];

    if ( $source === 'rss' ) {
        foreach ( $plugin->settings->sources() as $src ) {
            foreach ( $plugin->scraper->fetch_source( $src ) as $item ) {
                $items[] = [
                    'title'    => $item['title'],
                    'link'     => $item['url'],
                    'fallback' => (string) ( $item['excerpt'] ?? '' ),
                    'pub_date' => ( $item['published_at'] ?? 0 ) > 0 ? gmdate( 'Y-m-d', $item['published_at'] ) : '',
                    'current_title' => '',
                    'current_desc'  => '',
                ];
                if ( $limit > 0 && count( $items ) >= $limit ) break 2;
            }
        }
    } else {
        $rows = $plugin->storage->read_data_rows( $source );
        if ( is_wp_error( $rows ) ) WP_CLI::error( $rows->get_error_message() );
        foreach ( $rows as $row ) {
            if ( empty( $row['link'] ) ) continue;
            $items[] = [
                'title'    => $row['title'],
                'link'     => $row['link'],
                'fallback' => (string) $row['description'],
                'pub_date' => (string) ( $row['pub_date'] ?? '' ),
                'current_title' => $row['title'],
                'current_desc'  => (string) $row['description'],
            ];
            if ( $limit > 0 && count( $items ) >= $limit ) break;
        }
    }

    foreach ( $items as &$item ) {
        $item['text']        = $item['fallback'];
        $item['text_source'] = $source === 'rss' ? 'rss excerpt' : 'sheet description';
        if ( $mode === 'body' ) {
            $body = $plugin->scraper->extract_body( $item['link'] );
            if ( ! is_wp_error( $body ) && trim( $body ) !== '' ) {
                $item['text']        = mb_substr( $body, 0, 1500 );
                $item['text_source'] = 'article body';
            }
        }
    }
    unset( $item );

    return $items;
}

/** The pre-1.3.0 analyze() system prompt (settings dictionaries included), for old-vs-new comparisons. */
function aeo_legacy_prompt( ENA_Plugin $plugin ): string {
    $openrouter = $plugin->openrouter;
    $flatten    = new ReflectionMethod( $openrouter, 'flatten_list' );
    $flatten->setAccessible( true );
    $legacy = new ReflectionMethod( $openrouter, 'build_legacy_analysis_prompt' );
    $legacy->setAccessible( true );

    $get = fn ( $key ) => $flatten->invoke( null, (string) $plugin->settings->get( $key, '' ) );
    return $legacy->invoke( $openrouter, $get( 'tag_descriptors' ), $get( 'offtopic_whitelist' ), $get( 'offtopic_blacklist' ) );
}

/** Same user message analyze() sends, so variant prompts are compared on identical input. */
function aeo_user_message( array $item ): string {
    $date = 'Publication date: ' . ( $item['pub_date'] !== '' ? $item['pub_date'] : 'unknown' );
    return "Original title: {$item['title']}\n{$date}\n\nArticle text: {$item['text']}\n\nProduce JSON.";
}

/** Call the private ENA_OpenRouter::chat() with a given system prompt and decode the JSON reply. */
function aeo_chat_json( ENA_Plugin $plugin, string $system, string $user ): array|WP_Error {
    $chat = new ReflectionMethod( $plugin->openrouter, 'chat' );
    $chat->setAccessible( true );
    $raw = $chat->invoke( $plugin->openrouter, $system, $user, [ 'temperature' => 0.4 ], 'summarize' );
    if ( is_wp_error( $raw ) ) return $raw;
    $decoded = json_decode( trim( preg_replace( '/^```[a-z]*\s*|\s*```$/i', '', trim( $raw ) ) ), true );
    return is_array( $decoded ) ? $decoded : new WP_Error( 'parse', 'Invalid JSON: ' . mb_substr( $raw, 0, 200 ) );
}

/**
 * Count sentences in Bulgarian text. A period inside a number ("436.5") or after an abbreviation that
 * precedes a capitalised name ("гр. София", "д-р Иванов") does not end a sentence. "г.", "км.", "млн."
 * are NOT treated as abbreviations: in news copy they usually end the sentence ("…през 2026 г. Компанията …").
 */
function aeo_sentences( string $text ): array {
    $parts  = preg_split( '/(?<=[.!?…])\s+(?=[\p{Lu}\d„"«(])/u', trim( $text ) );
    $abbrev = '/(?:(?<!\p{L})(?:гр|т\.е|напр|вкл|прибл|ок|ул|бул|д-р|инж|св|проф)\.)$/iu';
    // A leading date phrase ending in a year ("На 14 октомври 2026 г.") opens the sentence, not ends it.
    $date_lead = '/^(?:На|От|До|През|Към|След|Преди|Във?|Още на)\s[^.!?]*\d{4}\s?г\.$/u';
    $out    = [];
    foreach ( $parts as $p ) {
        if ( $out && ( preg_match( $abbrev, end( $out ) ) || preg_match( $date_lead, end( $out ) ) ) ) {
            $out[ count( $out ) - 1 ] .= ' ' . $p;
        } else {
            $out[] = $p;
        }
    }
    return array_values( array_filter( $out, fn ( $s ) => trim( $s ) !== '' ) );
}

function aeo_pct( array $values, float $q ): int {
    if ( ! $values ) return 0;
    sort( $values );
    return (int) $values[ min( count( $values ) - 1, (int) floor( $q * count( $values ) ) ) ];
}
