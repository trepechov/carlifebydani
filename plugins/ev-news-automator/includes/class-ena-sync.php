<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Runs after every collection — both the daily cron and the manual "Run collection now" trigger.
 * Reads all rows, orders them, and writes a JSON snapshot to ev_news_live_articles.
 *
 * Article ordering (applied before the JSON snapshot is written):
 *
 *   Group 1 — articles collected within the last 24 hours (added_date >= yesterday UTC)
 *             Shown first, sorted by upvote DESC → pub_date DESC → added_date DESC.
 *
 *   Group 2 — all older articles
 *             Shown after Group 1, same sort.
 *
 * Both groups are explicitly re-sorted here rather than trusting the physical
 * spreadsheet order left by sort_by_upvotes() — that sort runs as a separate
 * Sheets API call earlier in the pipeline, and display order must not depend
 * on it having landed correctly by the time this reads the rows back.
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
        $cutoff = gmdate( 'Y-m-d', time() - DAY_IN_SECONDS );
        $recent = array_values( array_filter( $rows, fn ( $r ) => ( $r['added_date'] ?? '' ) >= $cutoff ) );
        $older  = array_values( array_filter( $rows, fn ( $r ) => ( $r['added_date'] ?? '' ) < $cutoff ) );

        // upvote DESC → pub_date DESC → added_date DESC within each group.
        $pub_date_of = fn ( $r ) => ! empty( $r['pub_date'] ) ? $r['pub_date'] : ( $r['added_date'] ?? '' );
        $by_engagement = function ( $a, $b ) use ( $pub_date_of ) {
            $cmp = (int) $b['upvote'] <=> (int) $a['upvote'];
            if ( $cmp !== 0 ) return $cmp;
            $cmp = strcmp( $pub_date_of( $b ), $pub_date_of( $a ) );
            if ( $cmp !== 0 ) return $cmp;
            return strcmp( $b['added_date'] ?? '', $a['added_date'] ?? '' );
        };
        usort( $recent, $by_engagement );
        usort( $older, $by_engagement );

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
