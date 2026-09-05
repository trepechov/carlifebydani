<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Cron {

    private const HOOK_COLLECTION = 'ena_daily_collection';

    // Session/podcast cadence, all in the site's timezone (Europe/Sofia, EET/EEST).
    // Sessions run Tuesday-to-Tuesday; the podcast records Tuesday evening (~19:00).
    private const SESSION_DOW         = 2;  // Tuesday (ISO: 1=Mon .. 7=Sun)
    private const PODCAST_RECORD_HOUR = 19; // approx recording time on the session day
    // Every article must be live on the feed at least this many hours before the
    // episode that covers it. The collection tab therefore rolls over to the next
    // episode FEED_LEAD_HOURS before recording (19:00 − 18h = 01:00): anything
    // collected inside that final window lands in the next episode's fresh tab
    // instead of being rushed into the one recorded that same evening.
    private const FEED_LEAD_HOURS     = 18;
    // The feed AND podcast source keep showing an episode until the day AFTER recording
    // (session day + 1 = Wednesday) at this hour, then advance together to the new week's
    // tab — so the just-aired episode's articles stay visible overnight for listeners
    // rather than vanishing at 19:00 the moment the episode drops. NOTE: unlike the other
    // constants above, this Wednesday switch is evaluated in SERVER time (UTC), not the
    // site timezone — see current_episode_tab_name().
    private const FEED_SWITCH_HOUR    = 0;

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
            $plugin->logger->end_run( $result );
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
     * @return array{added:int,removed:int,synced:int,duration:string}
     */
    public static function run_pipeline( ENA_Plugin $plugin ): array {
        // Wall-clock start used only to report how long the run took. Kept
        // separate from $run_started_at below (which drives the age cutoff) so
        // its sub-second precision isn't conflated with the cutoff timestamp.
        $started = microtime( true );

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

        // The feed AND the podcast read the current live-episode tab (they share one
        // source, flipping together at Wed 00:00). From the 01:00 collection rollover until
        // that Wednesday it's a DIFFERENT tab from the one the collector is filling
        // ($active_name), so track it and keep it refreshed. Refreshing $active_name plus
        // $feed_tab therefore covers every tab in play (collection + live episode).
        $feed_tab = self::feed_display_tab_name();

        // When the collection tab rotates (weekly, at the 01:00 freeze boundary) give the
        // OUTGOING tab one last GA4 refresh before it's abandoned — unless it's still the
        // live feed/episode tab, which the feed refresh below keeps current every run.
        $previous_active = get_option( ENA_OPT_LAST_ACTIVE_SHEET, '' );
        if ( ! is_wp_error( $active_name ) && $previous_active
            && $previous_active !== $active_name && $previous_active !== $feed_tab ) {
            $plugin->logger->step( 'tab_rotation', 'ok',
                "active tab changed {$previous_active} -> {$active_name}; refreshing outgoing tab one last time" );
            self::refresh_analytics( $plugin->storage, $plugin->analytics, $plugin->logger, $previous_active );
        }

        // 1. Refresh clicks + votes on existing rows. Shared with the podcast script
        //    generator (ENA_Podcast::run()) so both always sort off the same data.
        self::refresh_analytics( $plugin->storage, $plugin->analytics, $plugin->logger );

        // 1b. During the freeze window the feed/episode tab differs from the active tab
        //     refreshed above; refresh it too so votes/clicks readers cast on the feed
        //     don't stay frozen at their rollover-time values until the podcast runs.
        if ( ! is_wp_error( $active_name ) && $feed_tab !== $active_name ) {
            $plugin->logger->step( 'feed_tab_refresh', 'ok',
                "refreshing feed/episode tab {$feed_tab} (freeze window; collector on {$active_name})" );
            self::refresh_analytics( $plugin->storage, $plugin->analytics, $plugin->logger, $feed_tab );
        }

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

        $result['duration'] = round( microtime( true ) - $started, 1 ) . 's';

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
    public static function sort_sheet( ENA_Sheets $storage, ENA_Logger $logger, ?string $sheet_name = null ): bool|WP_Error {
        $sort_result = $storage->sort_by_upvotes( $sheet_name );
        if ( is_wp_error( $sort_result ) ) {
            $logger->step( 'sheets_sort', 'error', $sort_result->get_error_message() );
            return $sort_result;
        }
        $logger->step( 'sheets_sort', 'ok', 'rows sorted: upvote DESC → pub_date DESC → added_date DESC' );
        return true;
    }

    /**
     * The DD.MM.YYYY name of the session tab the COLLECTOR should be writing to now.
     * Tabs are named after the Tuesday the episode RECORDS (its episode date). Collection
     * for episode T runs from the previous Tuesday 01:00 up to Tuesday T at 01:00 — i.e.
     * it closes FEED_LEAD_HOURS before T's recording. At that 01:00 rollover the collector
     * advances to the NEXT episode (T+7), so an article arriving in the final window before
     * a recording lands in next week's episode instead of the one recorded that evening.
     *
     * @see podcast_source_tab_name() — the upcoming, not-yet-recorded episode the podcast
     *      reads. It differs from this only during the freeze window: the collector has
     *      already advanced to next week while the podcast still reads tonight's episode.
     */
    private static function current_session_tab_name(): string {
        return self::collection_recording_tuesday( new DateTimeImmutable( 'now', wp_timezone() ) )
            ->format( 'd.m.Y' );
    }

    /**
     * The DD.MM.YYYY tab of the episode that is currently LIVE — the one shown on the feed
     * AND read by the podcast script generator. It stays on the current episode until the
     * day after recording (Wednesday FEED_SWITCH_HOUR), then advances to the episode the
     * collector is now filling. Feed and podcast deliberately share this single source so
     * they always flip together on Wednesday — NOT at the Tue 19:00 recording. So a script
     * (re)generated Tuesday night still targets that evening's episode.
     *
     * The Wednesday switch is evaluated in SERVER time (UTC) per the operator's choice,
     * even though the recording/collection are anchored to the site timezone. We find the
     * most recent switch INSTANT in UTC, then resolve which episode the collector was
     * filling at that instant using the site-timezone Tuesday math (so 01:00/19:00 keep
     * their Europe/Sofia meaning). UTC Wed 00:00 ≈ 02:00–03:00 Sofia depending on DST.
     */
    public static function current_episode_tab_name(): string {
        $now_utc    = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
        $switch_dow = ( self::SESSION_DOW % 7 ) + 1; // day after the session day (Wednesday)
        $days_since = ( (int) $now_utc->format( 'N' ) - $switch_dow + 7 ) % 7;
        $switch     = $now_utc->modify( "-{$days_since} days" )->setTime( self::FEED_SWITCH_HOUR, 0, 0 );
        if ( $switch > $now_utc ) {
            $switch = $switch->modify( '-7 days' ); // today is the switch day but before the switch hour (UTC)
        }
        // Same instant, re-expressed in the site timezone, so the Tuesday/01:00 math below is correct.
        return self::collection_recording_tuesday( $switch->setTimezone( wp_timezone() ) )->format( 'd.m.Y' );
    }

    /** Tab the podcast script reads — the current live episode (see current_episode_tab_name()). */
    public static function podcast_source_tab_name(): string {
        return self::current_episode_tab_name();
    }

    /** Tab the public feed displays — the current live episode (see current_episode_tab_name()). */
    public static function feed_display_tab_name(): string {
        return self::current_episode_tab_name();
    }

    /**
     * The recording Tuesday (00:00) of the episode being COLLECTED INTO at $now. Collection
     * for an episode closes at its own Tuesday 01:00 (FEED_LEAD_HOURS before recording):
     * before that rollover we're still filling this week's episode, at/after it next week's.
     * Shared by the collection- and feed-tab helpers.
     */
    private static function collection_recording_tuesday( DateTimeImmutable $now ): DateTimeImmutable {
        $this_weeks_tue = self::this_weeks_session_day( $now );
        $rollover       = $this_weeks_tue->setTime( self::PODCAST_RECORD_HOUR - self::FEED_LEAD_HOURS, 0, 0 );
        return $now >= $rollover ? $this_weeks_tue->modify( '+7 days' ) : $this_weeks_tue;
    }

    /** Midnight of the most recent SESSION_DOW (Tuesday) at or before $now, in $now's timezone. */
    private static function this_weeks_session_day( DateTimeImmutable $now ): DateTimeImmutable {
        $days_since = ( (int) $now->format( 'N' ) - self::SESSION_DOW + 7 ) % 7;
        return $now->modify( "-{$days_since} days" )->setTime( 0, 0, 0 );
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
