<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Logger {

    // Minimum gap between two alert emails, regardless of how many errors are
    // logged in between — a bad run can call log_error() once per failing
    // source, and every one of those is still captured in the run log/
    // transcript either way, so nothing is lost by not emailing each one.
    private const ALERT_COOLDOWN  = 15 * MINUTE_IN_SECONDS;
    private const ALERT_LOCK_KEY  = 'ena_error_alert_lock';

    private string $current_trigger = 'manual';
    private string $current_phase   = '';
    private ?ENA_Settings $settings;

    public function __construct( ?ENA_Settings $settings = null ) {
        $this->settings = $settings;
    }

    public function log( string $phase, string $level, string $message, array $context = [] ): void {
        $entry = [
            'time'    => ( new DateTimeImmutable() )->format( 'Y-m-d H:i:s' ),
            'trigger' => $this->current_trigger,
            'phase'   => $phase,
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];

        $log = get_option( ENA_OPT_RUN_LOG, [] );
        if ( ! is_array( $log ) ) $log = [];
        array_unshift( $log, $entry );
        $log = array_slice( $log, 0, 20 );
        update_option( ENA_OPT_RUN_LOG, $log );
    }

    public function step( string $step, string $status, string $detail = '' ): void {
        $entry = [
            'time'   => ( new DateTimeImmutable() )->format( 'Y-m-d H:i:s' ),
            'step'   => $step,
            'status' => $status,
            'detail' => $detail,
        ];

        $transcript = get_option( ENA_OPT_CRON_TRANSCRIPT, [] );
        if ( ! is_array( $transcript ) ) $transcript = [];
        $transcript[] = $entry;
        update_option( ENA_OPT_CRON_TRANSCRIPT, $transcript );
    }

    public function begin_run( string $trigger, string $phase ): void {
        $this->current_trigger = $trigger;
        $this->current_phase   = $phase;
        update_option( ENA_OPT_CRON_TRANSCRIPT, [] );
        $this->step( 'begin_run', 'ok', "trigger={$trigger} phase={$phase}" );
    }

    public function end_run( array $summary ): void {
        $detail = implode( ' ', array_map(
            fn( $k, $v ) => "{$k}={$v}",
            array_keys( $summary ),
            array_values( $summary )
        ) );
        $this->step( 'end_run', 'ok', $detail );
        $this->log(
            $this->current_phase,
            'info',
            "Run complete: {$detail}",
            $summary
        );
    }

    public function log_error( string $phase, string $message, array $context = [] ): void {
        $this->log( $phase, 'error', $message, $context );
        $this->step( $phase, 'error', $message );
        $this->maybe_email_alert( $phase, $message, $context );
    }

    /**
     * Emails the site admin on error, gated by the "Email error alerts"
     * setting (default on) and a cooldown so one bad run — e.g. every
     * configured source failing in the same collection — sends at most one
     * email rather than one per failure. Every error is captured in the run
     * log/transcript regardless of whether an email goes out.
     */
    private function maybe_email_alert( string $phase, string $message, array $context ): void {
        $enabled = $this->settings
            ? (bool) $this->settings->get( 'error_alert_enabled', true )
            : true;
        if ( ! $enabled ) return;

        if ( get_transient( self::ALERT_LOCK_KEY ) ) return;
        set_transient( self::ALERT_LOCK_KEY, 1, self::ALERT_COOLDOWN );

        $site   = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
        $to     = get_option( 'admin_email' );
        $when   = ( new DateTimeImmutable() )->format( 'Y-m-d H:i:s' );
        $subject = sprintf( '[%s] EV News Automator error: %s', $site, $phase );

        $lines = [
            "An error was logged by the EV News Automator plugin.",
            "",
            "Site:     " . home_url( '/' ),
            "Time:     {$when}",
            "Trigger:  {$this->current_trigger}",
            "Phase:    {$phase}",
            "Message:  {$message}",
        ];

        if ( ! empty( $context ) ) {
            $lines[] = "Context:";
            $lines[] = wp_json_encode( $context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        }

        $transcript = $this->transcript();
        if ( ! empty( $transcript ) ) {
            $lines[] = "";
            $lines[] = "--- Run transcript (" . count( $transcript ) . " steps) ---";
            foreach ( $transcript as $entry ) {
                $lines[] = sprintf(
                    '[%s] %-24s %-6s %s',
                    $entry['time']   ?? '',
                    $entry['step']   ?? '',
                    $entry['status'] ?? '',
                    $entry['detail'] ?? ''
                );
            }
        }

        $lines[] = "";
        $lines[] = "Full run log and controls: " . admin_url( 'admin.php?page=ev-news-automator' );
        $lines[] = "This alert can be disabled in EV News → Settings.";

        wp_mail( $to, $subject, implode( "\n", $lines ) );
    }

    public function all(): array {
        $log = get_option( ENA_OPT_RUN_LOG, [] );
        return is_array( $log ) ? $log : [];
    }

    public function transcript(): array {
        $t = get_option( ENA_OPT_CRON_TRANSCRIPT, [] );
        return is_array( $t ) ? $t : [];
    }

    public function clear_log(): void {
        update_option( ENA_OPT_RUN_LOG, [] );
    }

    public function clear_transcript(): void {
        update_option( ENA_OPT_CRON_TRANSCRIPT, [] );
    }

    public function set_status( string $key, array $data ): void {
        update_option( $key, $data );
    }

    public function get_status( string $key ): array {
        $v = get_option( $key, [] );
        return is_array( $v ) ? $v : [];
    }
}
