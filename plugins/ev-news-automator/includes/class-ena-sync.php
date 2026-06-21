<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Runs after every collection — both the daily cron and the manual "Run collection now" trigger.
// Reads all rows, engagement-sorts them, and writes a JSON snapshot to ev_news_live_articles.
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

        $today = gmdate( 'Y-m-d' );

        // Group 1 — added today: shown first, Sheet insertion order preserved.
        // Group 2 — older, clicks > 0: sorted by clicks DESC; ties keep Sheet insertion order (PHP 8 stable sort).
        // Group 3 — older, clicks = 0: shown last, Sheet insertion order preserved.
        $new_today   = array_values( array_filter( $rows, fn ( $r ) => $r['added_date'] === $today ) );
        $with_clicks = array_values( array_filter( $rows, fn ( $r ) => $r['added_date'] < $today && (int) $r['clicks'] > 0 ) );
        $zero_clicks = array_values( array_filter( $rows, fn ( $r ) => $r['added_date'] < $today && (int) $r['clicks'] === 0 ) );
        usort( $with_clicks, fn ( $a, $b ) => (int) $b['clicks'] <=> (int) $a['clicks'] );
        $sorted = array_merge( $new_today, $with_clicks, $zero_clicks );

        $articles = array_map( fn ( $r ) => [
            'id'          => md5( $r['link'] ),
            'title'       => $r['title'],
            'link'        => $r['link'],
            'description' => $r['description'],
            'source'      => $r['author'],
            'date'        => $r['session_date'],
            'clicks'      => (int) $r['clicks'],
            'added_date'  => $r['added_date'],
        ], $sorted );

        update_option( ENA_OPT_LIVE_ARTICLES, wp_json_encode( $articles ) );
        $count = count( $articles );

        $this->logger->step( 'sync', 'ok', "{$count} articles written to ev_news_live_articles" );
        $sheet_url = $this->storage->active_sheet_url();

        $this->logger->set_status( ENA_OPT_STATUS_SYNC, [
            'timestamp'   => ( new DateTimeImmutable() )->format( 'c' ),
            'count'       => $count,
            'new_today'   => count( $new_today ),
            'with_clicks' => count( $with_clicks ),
            'zero_clicks' => count( $zero_clicks ),
            'sheet_url'   => is_wp_error( $sheet_url ) ? '' : $sheet_url,
        ] );

        return [ 'count' => $count ];
    }
}
