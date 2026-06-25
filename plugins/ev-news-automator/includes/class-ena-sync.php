<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Runs after every collection — both the daily cron and the manual "Run collection now" trigger.
 * Reads all rows, engagement-sorts them, and writes a JSON snapshot to ev_news_live_articles.
 *
 * Article ordering (applied before the JSON snapshot is written):
 *
 *   Group 1 — published today (pub_date === today)
 *             Shown first. Sheet insertion order preserved within the group.
 *
 *   Group 2 — older articles with at least one click (pub_date < today, clicks > 0)
 *             Sorted by clicks DESC. Ties keep Sheet insertion order (PHP 8 stable sort).
 *
 *   Group 3 — older articles with zero clicks (pub_date < today, clicks = 0)
 *             Shown last. Sheet insertion order preserved within the group.
 *
 * Date resolution: pub_date (Y-m-d, sourced from the RSS <pubDate> field) is the
 * primary date. If pub_date is missing for a row, added_date (the date the row was
 * scraped into the Sheet) is used as a fallback so those rows still land in a group.
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

        $today = gmdate( 'Y-m-d' );

        // Group 1 — published today: shown first, Sheet insertion order preserved.
        // Group 2 — older, clicks > 0: sorted by clicks DESC; ties keep Sheet insertion order (PHP 8 stable sort).
        // Group 3 — older, clicks = 0: shown last, Sheet insertion order preserved.
        // pub_date (Y-m-d) is preferred; falls back to added_date for rows where pub_date is missing.
        $pub_date_of  = fn ( $r ) => ! empty( $r['pub_date'] ) ? $r['pub_date'] : $r['added_date'];
        $new_today    = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) === $today ) );
        $with_clicks  = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $today && (int) $r['clicks'] > 0 ) );
        $zero_clicks  = array_values( array_filter( $rows, fn ( $r ) => $pub_date_of( $r ) < $today && (int) $r['clicks'] === 0 ) );
        usort( $with_clicks, fn ( $a, $b ) => (int) $b['clicks'] <=> (int) $a['clicks'] );
        $sorted = array_merge( $new_today, $with_clicks, $zero_clicks );

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

        $this->logger->step( 'sync', 'ok', "{$count} articles written to ev_news_live_articles" );
        $sheet_name = $this->storage->active_sheet_name();
        $sheet_url  = $this->storage->active_sheet_url();

        $this->logger->set_status( ENA_OPT_STATUS_SYNC, [
            'timestamp'   => ( new DateTimeImmutable() )->format( 'c' ),
            'count'       => $count,
            'published_today' => count( $new_today ),
            'with_clicks' => count( $with_clicks ),
            'zero_clicks' => count( $zero_clicks ),
            'sheet_name'  => is_wp_error( $sheet_name ) ? '' : $sheet_name,
            'sheet_url'   => is_wp_error( $sheet_url ) ? '' : $sheet_url,
        ] );

        return [ 'count' => $count ];
    }
}
