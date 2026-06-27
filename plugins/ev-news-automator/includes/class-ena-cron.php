<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Cron {

    private const HOOK_COLLECTION = 'ena_daily_collection';

    public static function activate(): void {
        add_filter( 'cron_schedules', [ __CLASS__, 'add_intervals' ] );
        self::reschedule();
    }

    public static function deactivate(): void {
        wp_clear_scheduled_hook( self::HOOK_COLLECTION );
    }

    public static function reschedule(): void {
        wp_clear_scheduled_hook( self::HOOK_COLLECTION );

        $settings = new ENA_Settings();
        $interval = self::get_interval( $settings->get( 'collection_interval', 'daily' ) );

        // For the production daily schedule, fire at the configured collection_time
        // so the team can rely on a predictable morning run. For shorter dev intervals,
        // fire within 60 seconds so iteration is immediate.
        $start = $interval === 'daily'
            ? self::next_collection_timestamp( $settings )
            : time() + 60;

        wp_schedule_event( $start, $interval, self::HOOK_COLLECTION );
    }

    public static function register_hooks(): void {
        add_filter( 'cron_schedules', [ __CLASS__, 'add_intervals' ] );
        add_action( self::HOOK_COLLECTION, [ __CLASS__, 'run_daily_collection' ] );
    }

    public static function run_daily_collection(): void {
        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'cron', 'collection' );

        try {
            $result = self::run_pipeline( $plugin );
            $plugin->logger->end_run( array_merge( $result, [ 'duration' => '?' ] ) );
        } catch ( \Throwable $e ) {
            $plugin->logger->log_error( 'collection', 'Uncaught exception: ' . $e->getMessage() );
        }
    }

    /**
     * The full collection pipeline, shared by the daily cron, the manual
     * "Run collection now" admin trigger, and the background worker so the
     * ordering is identical everywhere:
     *
     *   1. Fetch GA4 clicks for existing rows and write them back to the sheet.
     *   2. Collect & append newly scraped articles at the bottom.
     *   3. Sort the FULL sheet: clicks DESC → pub_date DESC → added_date DESC.
     *   4. Trim to max_articles by deleting the bottom (oldest zero-click) rows.
     *   5. Rebuild the live JSON snapshot the feed page reads from.
     *
     * Steps 3 and 4 run on every collection regardless of whether the GA4 fetch
     * in step 1 succeeded. A failed clicks fetch must never skip the sort, or
     * freshly appended articles are stranded, unsorted, at the bottom of the sheet.
     *
     * @return array{added:int,removed:int,synced:int}
     */
    public static function run_pipeline( ENA_Plugin $plugin ): array {
        // 1. Refresh clicks on existing rows.
        $rows   = $plugin->storage->read_data_rows();
        $urls   = is_wp_error( $rows ) ? [] : array_column( $rows, 'link' );
        $clicks = $plugin->analytics->fetch_clicks( $urls );

        if ( is_wp_error( $clicks ) ) {
            $plugin->logger->log_error( 'analytics_fetch', $clicks->get_error_message() );
        } else {
            $plugin->storage->update_clicks( $clicks );
            $with_clicks = count( array_filter( $clicks, fn ( $c ) => $c > 0 ) );
            $plugin->logger->step( 'analytics_fetch', 'ok', count( $urls ) . " URLs, {$with_clicks} with clicks" );
        }

        // 2. Collect & append new articles at the bottom.
        $result = $plugin->collector->run();

        // 3. Sort the full sheet (always — independent of the clicks fetch above).
        $sort_result = $plugin->storage->sort_by_clicks();
        if ( is_wp_error( $sort_result ) ) {
            $plugin->logger->step( 'sheets_sort', 'error', $sort_result->get_error_message() );
        } else {
            $plugin->logger->step( 'sheets_sort', 'ok', 'rows sorted: clicks DESC → pub_date DESC' );
        }

        // 4. Trim to max by deleting the bottom (oldest zero-click) rows.
        $max     = (int) $plugin->settings->get( 'max_articles', 50 );
        $removed = $plugin->storage->trim_to_max( $max );
        $result['removed'] = $removed;
        if ( $removed > 0 ) {
            $plugin->logger->step( 'sheets_trim', 'ok', "{$removed} oldest zero-click rows removed (max={$max})" );
        }

        // Collection status reflects this run's append + trim counts.
        $plugin->logger->set_status( ENA_OPT_STATUS_COLLECTION, [
            'timestamp' => ( new DateTimeImmutable() )->format( 'c' ),
            'added'     => $result['added'] ?? 0,
            'removed'   => $removed,
        ] );

        // 5. Rebuild the live snapshot for the feed page.
        $sync_result      = $plugin->sync->run();
        $result['synced'] = $sync_result['count'] ?? 0;

        // 6. Push badge update to all subscribed PWA users.
        $today_count = $sync_result['published_today'] ?? 0;
        if ( $today_count > 0 ) {
            ENA_Push::send_all( $today_count );
        }

        return $result;
    }

    public static function add_intervals( array $schedules ): array {
        $schedules['ena_15min']  = [ 'interval' => 900,   'display' => '15 Minutes' ];
        $schedules['ena_30min']  = [ 'interval' => 1800,  'display' => '30 Minutes' ];
        $schedules['ena_6hours'] = [ 'interval' => 21600, 'display' => '6 Hours' ];
        return $schedules;
    }

    private static function get_interval( string $setting ): string {
        $map = [
            '15min'   => 'ena_15min',
            '30min'   => 'ena_30min',
            '1hour'   => 'hourly',
            '6hours'  => 'ena_6hours',
            '12hours' => 'twicedaily',
            'daily'   => 'daily',
        ];
        return $map[ $setting ] ?? 'daily';
    }

    // Returns the Unix timestamp of the next occurrence of collection_time in the site's timezone.
    private static function next_collection_timestamp( ENA_Settings $settings ): int {
        $time = $settings->get( 'collection_time', '09:00' );
        [ $hour, $minute ] = array_map( 'intval', explode( ':', $time ) );

        $tz  = wp_timezone();
        $now = new DateTimeImmutable( 'now', $tz );

        $today_target = $now->setTime( $hour, $minute );

        return ( $now < $today_target )
            ? $today_target->getTimestamp()
            : $today_target->modify( '+1 day' )->getTimestamp();
    }
}
