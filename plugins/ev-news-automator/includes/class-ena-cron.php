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
        $start    = self::next_collection_timestamp( $settings );

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
     * The full collection pipeline, shared by the scheduled cron, the manual
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
        // Record start time before fetching so the next run's cutoff covers
        // any articles published during this run's execution window.
        $run_started_at = time();

        // Bypass the 5-minute sheets-list cache so a weekly tab added just before
        // this run (e.g. right before clicking "Run collection now") is picked up
        // as the active sheet instead of whatever was cached by an earlier page
        // load or run.
        $plugin->storage->flush_sheets_cache();

        // Log which tab this run resolved as "active" before touching any data —
        // the single most useful line for diagnosing a run that targets the wrong
        // sheet or wipes an unexpectedly-empty one.
        $active_name = $plugin->storage->active_sheet_name();
        $plugin->logger->step(
            'active_sheet',
            is_wp_error( $active_name ) ? 'error' : 'ok',
            is_wp_error( $active_name ) ? $active_name->get_error_message() : "resolved active tab: {$active_name}"
        );

        // 1. Refresh clicks + votes on existing rows. Each GA4 fetch is logged
        //    independently so one failing fetch never blocks the others.
        $rows   = $plugin->storage->read_data_rows();
        $urls   = is_wp_error( $rows ) ? [] : array_column( $rows, 'link' );
        $plugin->logger->step( 'read_data_rows', is_wp_error( $rows ) ? 'error' : 'ok',
            is_wp_error( $rows ) ? $rows->get_error_message() : count( $rows ) . ' existing rows read' );
        $clicks = $plugin->analytics->fetch_clicks( $urls );

        if ( is_wp_error( $clicks ) ) {
            $plugin->logger->log_error( 'analytics_fetch', $clicks->get_error_message() );
        } else {
            $plugin->storage->update_clicks( $clicks );
            $with_clicks = count( array_filter( $clicks, fn ( $c ) => $c > 0 ) );
            $plugin->logger->step( 'analytics_fetch', 'ok', count( $urls ) . " URLs, {$with_clicks} with clicks" );
        }

        $upvotes = $plugin->analytics->fetch_upvotes( $urls );
        if ( is_wp_error( $upvotes ) ) {
            $plugin->logger->log_error( 'analytics_fetch_upvotes', $upvotes->get_error_message() );
        } else {
            $plugin->storage->update_upvotes( $upvotes );
            $with_upvotes = count( array_filter( $upvotes, fn ( $c ) => $c > 0 ) );
            $plugin->logger->step( 'analytics_fetch_upvotes', 'ok', count( $urls ) . " URLs, {$with_upvotes} with upvotes" );
        }

        $downvotes = $plugin->analytics->fetch_downvotes( $urls );
        if ( is_wp_error( $downvotes ) ) {
            $plugin->logger->log_error( 'analytics_fetch_downvotes', $downvotes->get_error_message() );
        } else {
            $plugin->storage->update_downvotes( $downvotes );
            $with_downvotes = count( array_filter( $downvotes, fn ( $c ) => $c > 0 ) );
            $plugin->logger->step( 'analytics_fetch_downvotes', 'ok', count( $urls ) . " URLs, {$with_downvotes} with downvotes" );
        }

        // 2. Collect & append new articles at the bottom.
        $result = $plugin->collector->run();
        $plugin->logger->step( 'row_count_after_collect', 'ok', $plugin->storage->row_count() . ' rows in active sheet after append' );

        // 3. Sort the full sheet (always — independent of the vote fetch above).
        $sort_result = $plugin->storage->sort_by_upvotes();
        if ( is_wp_error( $sort_result ) ) {
            $plugin->logger->step( 'sheets_sort', 'error', $sort_result->get_error_message() );
        } else {
            $plugin->logger->step( 'sheets_sort', 'ok', 'rows sorted: upvote DESC → pub_date DESC' );
        }

        // 4. Trim to max by deleting the bottom (oldest zero-upvote) rows.
        $max        = (int) $plugin->settings->get( 'max_articles', 50 );
        $pre_trim   = $plugin->storage->row_count();
        $plugin->logger->step( 'sheets_trim_start', 'ok', "max_articles={$max}, rows before trim={$pre_trim}" );
        $removed    = $plugin->storage->trim_to_max( $max );
        $result['removed'] = $removed;
        $plugin->logger->step( 'sheets_trim', 'ok',
            "{$removed} rows removed (max={$max}), rows after trim=" . $plugin->storage->row_count() );

        // Collection status reflects this run's append + trim counts.
        $plugin->logger->set_status( ENA_OPT_STATUS_COLLECTION, [
            'timestamp'    => ( new DateTimeImmutable() )->format( 'c' ),
            'added'        => $result['added'] ?? 0,
            'removed'      => $removed,
            'skipped'      => $result['skipped'] ?? 0,
            'skip_summary' => $result['skip_summary'] ?? '',
        ] );

        // 5. Rebuild the live snapshot for the feed page.
        $sync_result      = $plugin->sync->run();
        $result['synced'] = $sync_result['count'] ?? 0;

        // 6. Push badge update to all subscribed PWA users.
        $today_count = $sync_result['published_today'] ?? 0;
        if ( $today_count > 0 ) {
            $push = ENA_Push::send_all( $today_count );
            $plugin->logger->step( 'push', $push['failed'] === 0 ? 'ok' : 'warn',
                "subs:{$push['subs']} sent:{$push['sent']} failed:{$push['failed']} stale:{$push['stale']} count:{$today_count}" );
        } else {
            $plugin->logger->step( 'push', 'skip', "published_today=0, no push sent" );
        }

        // 7. Persist the run timestamp so the next cutoff starts from here
        //    (minus the 1-hour buffer applied in ENA_Settings::article_age_cutoff).
        update_option( 'ena_last_collection_at', $run_started_at );

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

    /**
     * Returns the Unix timestamp of the next run slot, anchored to collection_time.
     *
     * For any interval, collection_time is always one of the evenly-distributed slots.
     * Example: interval=6h, time=08:00 → slots at 02:00, 08:00, 14:00, 20:00 every day.
     * The first event fires at the next upcoming slot from now.
     */
    private static function next_collection_timestamp( ENA_Settings $settings ): int {
        $time = $settings->get( 'collection_time', '09:00' );
        [ $hour, $minute ] = array_map( 'intval', explode( ':', $time ) );

        $interval_secs = self::get_interval_seconds(
            $settings->get( 'collection_interval', 'daily' )
        );

        $tz     = wp_timezone();
        $now    = new DateTimeImmutable( 'now', $tz );
        $anchor = $now->setTime( $hour, $minute, 0 );

        $diff = $now->getTimestamp() - $anchor->getTimestamp();

        if ( $diff < 0 ) {
            // Anchor is still upcoming today — use it as the first slot.
            return $anchor->getTimestamp();
        }

        // Find how many full intervals have elapsed since the anchor,
        // then schedule the very next slot after now.
        $n = (int) floor( $diff / $interval_secs );
        return $anchor->getTimestamp() + ( $n + 1 ) * $interval_secs;
    }

    private static function get_interval_seconds( string $setting ): int {
        $map = [
            '15min'   => 15 * MINUTE_IN_SECONDS,
            '30min'   => 30 * MINUTE_IN_SECONDS,
            '1hour'   => HOUR_IN_SECONDS,
            '6hours'  => 6 * HOUR_IN_SECONDS,
            '12hours' => 12 * HOUR_IN_SECONDS,
            'daily'   => DAY_IN_SECONDS,
        ];
        return $map[ $setting ] ?? DAY_IN_SECONDS;
    }
}
