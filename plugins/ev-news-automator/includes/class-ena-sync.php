<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Runs after every collection — both the daily cron and the manual "Run collection now" trigger.
 * Reads all rows, engagement-sorts them, and writes a JSON snapshot to ev_news_live_articles.
 *
 * Article ordering (applied before the JSON snapshot is written):
 *
 *   Group 1 — any article with at least one click (clicks > 0)
 *             Shown first. Sorted by clicks DESC; ties broken by pub_date DESC.
 *
 *   Group 2 — zero-click articles (clicks = 0)
 *             Shown after Group 1. Sorted by pub_date DESC then added_date DESC,
 *             so articles collected today naturally appear above older ones.
 *             The bottom of this group (oldest pub_date) are the trim candidates.
 *
 * Date resolution: pub_date (Y-m-d, sourced from the RSS <pubDate> field) is the
 * primary date. If pub_date is missing for a row, added_date (the date the row was
 * scraped into the Sheet) is used as a fallback.
 */
class ENA_Sync {

    private ENA_Sheets $storage;
    private ENA_Logger $logger;

    public function __construct( ENA_Sheets $storage, ENA_Logger $logger ) {
        $this->storage = $storage;
        $this->logger  = $logger;
    }

    public function run(): array {
        $rows = $this->storage->read_data_rows();

        if ( is_wp_error( $rows ) ) {
            $this->logger->log_error( 'sync', $rows->get_error_message() );
            return [ 'count' => 0 ];
        }

        // pub_date (Y-m-d) is preferred; falls back to added_date for rows where pub_date is missing.
        $pub_date_of = fn ( $r ) => ! empty( $r['pub_date'] ) ? $r['pub_date'] : ( $r['added_date'] ?? '' );

        $with_clicks = array_values( array_filter( $rows, fn ( $r ) => (int) $r['clicks'] > 0 ) );
        $zero_clicks = array_values( array_filter( $rows, fn ( $r ) => (int) $r['clicks'] === 0 ) );

        // Group 1: clicks DESC → pub_date DESC for ties.
        usort( $with_clicks, function ( $a, $b ) use ( $pub_date_of ) {
            $cmp = (int) $b['clicks'] <=> (int) $a['clicks'];
            if ( $cmp !== 0 ) return $cmp;
            return strcmp( $pub_date_of( $b ), $pub_date_of( $a ) );
        } );

        // Group 2: pub_date DESC → added_date DESC so articles collected today float above older ones.
        usort( $zero_clicks, function ( $a, $b ) use ( $pub_date_of ) {
            $cmp = strcmp( $pub_date_of( $b ), $pub_date_of( $a ) );
            if ( $cmp !== 0 ) return $cmp;
            return strcmp( $b['added_date'] ?? '', $a['added_date'] ?? '' );
        } );

        $sorted = array_merge( $with_clicks, $zero_clicks );

        $articles = array_map( fn ( $r ) => [
            'id'          => md5( $r['link'] ),
            'title'       => $r['title'],
            'link'        => $r['link'],
            'description' => $r['description'],
            'source'      => $r['author'],
            'pub_date'    => $r['pub_date'] ?? '',
            'date'        => $r['session_date'],
            'clicks'      => (int) $r['clicks'],
            'added_date'  => $r['added_date'],
        ], $sorted );

        update_option( ENA_OPT_LIVE_ARTICLES, wp_json_encode( $articles ) );
        $count = count( $articles );

        // Count articles added today — uses gmdate to match the UTC date written by append_rows().
        $today           = gmdate( 'Y-m-d' );
        $published_today = count( array_filter( $rows, fn ( $r ) => ( $r['added_date'] ?? '' ) === $today ) );

        $this->logger->step( 'sync', 'ok', "{$count} articles written to ev_news_live_articles" );
        $sheet_name = $this->storage->active_sheet_name();
        $sheet_url  = $this->storage->active_sheet_url();

        $this->logger->set_status( ENA_OPT_STATUS_SYNC, [
            'timestamp'       => ( new DateTimeImmutable() )->format( 'c' ),
            'count'           => $count,
            'published_today' => $published_today,
            'with_clicks'     => count( $with_clicks ),
            'zero_clicks'     => count( $zero_clicks ),
            'sheet_name'      => is_wp_error( $sheet_name ) ? '' : $sheet_name,
            'sheet_url'       => is_wp_error( $sheet_url ) ? '' : $sheet_url,
        ] );

        return [ 'count' => $count ];
    }
}
