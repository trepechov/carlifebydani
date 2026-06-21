<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Logger {

    private string $current_trigger = 'manual';
    private string $current_phase   = '';

    public function log( string $phase, string $level, string $message, array $context = [] ): void {
        $entry = [
            'time'    => ( new DateTimeImmutable() )->format( 'c' ),
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
            'time'   => ( new DateTimeImmutable() )->format( 'H:i:s' ),
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
