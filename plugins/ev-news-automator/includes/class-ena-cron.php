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
     *   3. Sort the FULL sheet: upvote DESC → pub_date DESC → added_date DESC.
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

        // Auto-create this week's session tab if it's due and missing, copying the
        // header row from whatever's currently active. Self-heals the case where
        // the admin forgets the manual "new tab DD.MM.YYYY" step after Tuesday's
        // podcast — without this, active_sheet_name() would keep resolving to an
        // increasingly stale tab. create_session_tab() no-ops if the tab already
        // exists, so this is safe to run on every pipeline call.
        $target_tab = self::current_session_tab_name();
        $outgoing   = $plugin->storage->active_sheet_name();
        if ( is_wp_error( $outgoing ) || $outgoing !== $target_tab ) {
            $header_source = is_wp_error( $outgoing ) ? '' : $outgoing;
            $create        = $plugin->storage->create_session_tab( $target_tab, $header_source );
            $plugin->logger->step(
                'session_tab_create',
                is_wp_error( $create ) ? 'error' : 'ok',
                is_wp_error( $create )
                    ? $create->get_error_message()
                    : "ensured tab {$target_tab} exists" . ( $header_source ? " (header copied from {$header_source})" : ' (default header — no prior tab found)' )
            );
            $plugin->storage->flush_sheets_cache();
        }

        // Log which tab this run resolved as "active" before touching any data —
        // the single most useful line for diagnosing a run that targets the wrong
        // sheet or wipes an unexpectedly-empty one.
        $active_name = $plugin->storage->active_sheet_name();
        $plugin->logger->step(
            'active_sheet',
            is_wp_error( $active_name ) ? 'error' : 'ok',
            is_wp_error( $active_name ) ? $active_name->get_error_message() : "resolved active tab: {$active_name}"
        );

        // Session tabs are created manually (e.g. weekly), and refresh_analytics()/
        // ENA_Sync only ever touch the active (newest) tab. The run that first
        // notices the active tab changed gives the OUTGOING tab one last GA4
        // refresh before it's abandoned — otherwise any votes/clicks that landed
        // on it after rotation are frozen forever.
        $previous_active = get_option( ENA_OPT_LAST_ACTIVE_SHEET, '' );
        if ( ! is_wp_error( $active_name ) && $previous_active && $previous_active !== $active_name ) {
            $plugin->logger->step( 'tab_rotation', 'ok',
                "active tab changed {$previous_active} -> {$active_name}; refreshing outgoing tab one last time" );
            self::refresh_analytics( $plugin->storage, $plugin->analytics, $plugin->logger, $previous_active );
        }

        // 1. Refresh clicks + votes on existing rows. Shared with the podcast script
        //    generator (ENA_Podcast::run()) so both always sort off the same data.
        self::refresh_analytics( $plugin->storage, $plugin->analytics, $plugin->logger );

        // 2. Collect & append new articles at the bottom.
        $result = $plugin->collector->run();
        $plugin->logger->step( 'row_count_after_collect', 'ok', $plugin->storage->row_count() . ' rows in active sheet after append' );

        // 3. Sort the full sheet (always — independent of the vote fetch above).
        //    Also shared with the podcast script generator.
        self::sort_sheet( $plugin->storage, $plugin->logger );

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
            $push = ENA_Push::send_all( $today_count );
            $plugin->logger->step( 'push', $push['failed'] === 0 ? 'ok' : 'warn',
                "subs:{$push['subs']} sent:{$push['sent']} failed:{$push['failed']} stale:{$push['stale']} count:{$today_count}" );
        } else {
            $plugin->logger->step( 'push', 'skip', "published_today=0, no push sent" );
        }

        // 7. Persist the run timestamp so the next cutoff starts from here
        //    (minus the 1-hour buffer applied in ENA_Settings::article_age_cutoff).
        update_option( 'ena_last_collection_at', $run_started_at );

        // 8. Remember this run's active tab so the next run can detect rotation.
        if ( ! is_wp_error( $active_name ) ) {
            update_option( ENA_OPT_LAST_ACTIVE_SHEET, $active_name );
        }

        return $result;
    }

    /**
     * Add a single admin-submitted article to the active sheet, sharing every step
     * with the automatic pipeline after the fetch/summarize stage: append, sort,
     * trim, resync the live feed. Deliberately skips refresh_analytics() (no GA4
     * data exists yet for a brand-new URL) and the push-notification step (a single
     * manual add is a low-key admin action, not a "today's batch is ready" event).
     *
     * @return array{added:int,removed:int,synced:int,row:array}|WP_Error
     */
    public static function run_manual_add( ENA_Plugin $plugin, string $url ): array|WP_Error {
        $plugin->storage->flush_sheets_cache();

        $active_name = $plugin->storage->active_sheet_name();
        $plugin->logger->step(
            'active_sheet',
            is_wp_error( $active_name ) ? 'error' : 'ok',
            is_wp_error( $active_name ) ? $active_name->get_error_message() : "resolved active tab: {$active_name}"
        );

        $row = $plugin->collector->add_manual( $url );
        if ( is_wp_error( $row ) ) {
            $plugin->logger->log_error( 'manual_add', $row->get_error_message() );
            return $row;
        }

        $append = $plugin->storage->append_rows( [ $row ] );
        if ( is_wp_error( $append ) ) {
            $plugin->logger->log_error( 'sheets_append', $append->get_error_message() );
            return $append;
        }
        $plugin->logger->step( 'sheets_append', 'ok', '1 row appended (manual add)' );

        self::sort_sheet( $plugin->storage, $plugin->logger );

        $max     = (int) $plugin->settings->get( 'max_articles', 50 );
        $removed = $plugin->storage->trim_to_max( $max );
        $plugin->logger->step( 'sheets_trim', 'ok', "{$removed} rows removed (max={$max})" );

        $sync_result = $plugin->sync->run();

        return [
            'added'   => 1,
            'removed' => $removed,
            'synced'  => $sync_result['count'] ?? 0,
            'row'     => $row,
        ];
    }

    /**
     * Fetch GA4 clicks/upvotes/downvotes for a sheet's existing rows and write them
     * back to that sheet. Targets the active sheet by default; pass $sheet_name to
     * refresh a specific tab instead (used to give a just-rotated-out tab one final
     * refresh before it's abandoned — see run_pipeline()'s rotation check).
     * Each fetch is logged independently so one failing fetch never blocks the
     * others. Shared by run_pipeline() and ENA_Podcast::run() so both refresh the
     * exact same data before sorting.
     */
    public static function refresh_analytics( ENA_Sheets $storage, ENA_Analytics $analytics, ENA_Logger $logger, ?string $sheet_name = null ): void {
        $tag  = $sheet_name ? " [{$sheet_name}]" : '';
        $rows = $storage->read_data_rows( $sheet_name );
        $urls = is_wp_error( $rows ) ? [] : array_column( $rows, 'link' );
        $logger->step( 'read_data_rows', is_wp_error( $rows ) ? 'error' : 'ok',
            ( is_wp_error( $rows ) ? $rows->get_error_message() : count( $urls ) . ' existing rows read' ) . $tag );

        $clicks = $analytics->fetch_clicks( $urls );
        if ( is_wp_error( $clicks ) ) {
            $logger->log_error( 'analytics_fetch', $clicks->get_error_message() . $tag );
        } else {
            $storage->update_clicks( $clicks, $sheet_name );
            $with_clicks = count( array_filter( $clicks, fn ( $c ) => $c > 0 ) );
            $logger->step( 'analytics_fetch', 'ok', count( $urls ) . " URLs, {$with_clicks} with clicks{$tag}" );
        }

        $upvotes = $analytics->fetch_upvotes( $urls );
        if ( is_wp_error( $upvotes ) ) {
            $logger->log_error( 'analytics_fetch_upvotes', $upvotes->get_error_message() . $tag );
        } else {
            $storage->update_upvotes( $upvotes, $sheet_name );
            $with_upvotes = count( array_filter( $upvotes, fn ( $c ) => $c > 0 ) );
            $logger->step( 'analytics_fetch_upvotes', 'ok', count( $urls ) . " URLs, {$with_upvotes} with upvotes{$tag}" );
        }

        $downvotes = $analytics->fetch_downvotes( $urls );
        if ( is_wp_error( $downvotes ) ) {
            $logger->log_error( 'analytics_fetch_downvotes', $downvotes->get_error_message() . $tag );
        } else {
            $storage->update_downvotes( $downvotes, $sheet_name );
            $with_downvotes = count( array_filter( $downvotes, fn ( $c ) => $c > 0 ) );
            $logger->step( 'analytics_fetch_downvotes', 'ok', count( $urls ) . " URLs, {$with_downvotes} with downvotes{$tag}" );
        }
    }

    /**
     * Physically re-sort the active sheet: upvote DESC → pub_date DESC → added_date DESC.
     * Shared by run_pipeline() and ENA_Podcast::run() so the spreadsheet and the
     * generated script always end up in the exact same order.
     */
    public static function sort_sheet( ENA_Sheets $storage, ENA_Logger $logger ): bool|WP_Error {
        $sort_result = $storage->sort_by_upvotes();
        if ( is_wp_error( $sort_result ) ) {
            $logger->step( 'sheets_sort', 'error', $sort_result->get_error_message() );
            return $sort_result;
        }
        $logger->step( 'sheets_sort', 'ok', 'rows sorted: upvote DESC → pub_date DESC → added_date DESC' );
        return true;
    }

    /**
     * The DD.MM.YYYY name of the session tab that should be active right now.
     * Sessions run Tuesday-to-Tuesday: the podcast records Tuesday evening and a
     * fresh tab is expected once that's wrapped up — approximated as 19:00 in the
     * site's configured timezone (expected to be Europe/Sofia / EET-EEST, matching
     * the podcast's actual recording schedule). Before that cutoff on a Tuesday,
     * the previous week's Tuesday is still the current session.
     */
    private static function current_session_tab_name(): string {
        $now = new DateTimeImmutable( 'now', wp_timezone() );

        $dow                 = (int) $now->format( 'N' ); // 1=Mon .. 7=Sun, Tuesday=2
        $days_since_tuesday   = ( $dow - 2 + 7 ) % 7;
        $this_weeks_tuesday   = $days_since_tuesday > 0
            ? $now->modify( "-{$days_since_tuesday} days" )->setTime( 0, 0, 0 )
            : $now->setTime( 0, 0, 0 );

        $rollover_passed = $days_since_tuesday > 0 || $now->format( 'H:i' ) >= '19:00';
        $target          = $rollover_passed ? $this_weeks_tuesday : $this_weeks_tuesday->modify( '-7 days' );

        return $target->format( 'd.m.Y' );
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
