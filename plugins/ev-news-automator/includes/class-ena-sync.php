<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Runs after every collection — both the daily cron and the manual "Run collection now" trigger.
 * Reads all rows, orders them, and writes a JSON snapshot to ev_news_live_articles.
 *
 * Article ordering (applied before the JSON snapshot is written):
 *
 *   Group 1 — articles collected within the last 24 hours (added_date >= yesterday UTC)
 *             Shown first, in the order they appear in the spreadsheet.
 *
 *   Group 2 — all older articles
 *             Shown after Group 1, in the order they appear in the spreadsheet.
 *
 * Within each group the spreadsheet order is preserved as-is
 * (upvote DESC → pub_date DESC → added_date DESC, maintained by sort_by_upvotes()).
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

        // Group 1: collected within the last 24 hours. Group 2: everything older.
        // Both groups keep their spreadsheet order (no re-sort here).
        $cutoff = gmdate( 'Y-m-d', time() - DAY_IN_SECONDS );
        $recent = array_values( array_filter( $rows, fn ( $r ) => ( $r['added_date'] ?? '' ) >= $cutoff ) );
        $older  = array_values( array_filter( $rows, fn ( $r ) => ( $r['added_date'] ?? '' ) < $cutoff ) );

        $sorted = array_merge( $recent, $older );

        $articles = array_map( fn ( $r ) => [
            'id'          => md5( $r['link'] ),
            'title'       => $r['title'],
            'link'        => $r['link'],
            'description' => $r['description'],
            'source'      => $r['author'],
            'pub_date'    => $r['pub_date'] ?? '',
            'date'        => $r['session_date'],
            'clicks'      => (int) $r['clicks'],
            'upvote'      => (int) $r['upvote'],
            'downvote'    => (int) $r['downvote'],
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
            'recent_24h'      => count( $recent ),
            'older'           => count( $older ),
            'sheet_name'      => is_wp_error( $sheet_name ) ? '' : $sheet_name,
            'sheet_url'       => is_wp_error( $sheet_url ) ? '' : $sheet_url,
        ] );

        return [ 'count' => $count, 'published_today' => $published_today ];
    }
}
