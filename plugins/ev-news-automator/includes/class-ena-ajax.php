<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Ajax {

    public static function register(): void {
        add_action( 'wp_ajax_ena_run_collection',    [ __CLASS__, 'handle_run_collection' ] );
        add_action( 'wp_ajax_ena_run_sync',          [ __CLASS__, 'handle_run_sync' ] );
        add_action( 'wp_ajax_ena_run_podcast',       [ __CLASS__, 'handle_run_podcast' ] );
        add_action( 'wp_ajax_ena_openrouter_usage',  [ __CLASS__, 'handle_openrouter_usage' ] );
        add_action( 'wp_ajax_ena_reset_usage_stats', [ __CLASS__, 'handle_reset_usage_stats' ] );

        // Background job system
        add_action( 'wp_ajax_ena_dispatch_job',         [ __CLASS__, 'handle_dispatch_job' ] );
        add_action( 'wp_ajax_ena_job_status',            [ __CLASS__, 'handle_job_status' ] );
        add_action( 'wp_ajax_nopriv_ena_bg_worker',      [ __CLASS__, 'handle_bg_worker' ] );
    }

    public static function handle_run_collection(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'manual', 'collection' );

        try {
            $result = ENA_Cron::run_pipeline( $plugin );
            $plugin->logger->end_run( $result );
            wp_send_json_success( $result );
        } catch ( \Throwable $e ) {
            $plugin->logger->log_error( 'collection', 'Uncaught exception: ' . $e->getMessage() );
            wp_send_json_error( 'Exception: ' . $e->getMessage() );
        }
    }

    public static function handle_run_sync(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'manual', 'sync' );

        try {
            $result = $plugin->sync->run();
            $plugin->logger->end_run( $result );
            wp_send_json_success( $result );
        } catch ( \Throwable $e ) {
            $plugin->logger->log_error( 'sync', 'Uncaught exception: ' . $e->getMessage() );
            wp_send_json_error( 'Exception: ' . $e->getMessage() );
        }
    }

    public static function handle_run_podcast(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'manual', 'podcast' );

        try {
            $result = $plugin->podcast->run();
            $plugin->logger->end_run( $result );
            wp_send_json_success( $result );
        } catch ( \Throwable $e ) {
            $plugin->logger->log_error( 'podcast', 'Uncaught exception: ' . $e->getMessage() );
            wp_send_json_error( 'Exception: ' . $e->getMessage() );
        }
    }

    public static function handle_openrouter_usage(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $plugin   = ENA_Plugin::instance();
        $key_info = $plugin->openrouter->get_key_info();

        wp_send_json_success( [
            'key_info' => is_wp_error( $key_info )
                ? [ 'error' => $key_info->get_error_message() ]
                : $key_info,
            'local'    => ENA_OpenRouter::get_local_stats(),
        ] );
    }

    public static function handle_reset_usage_stats(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        ENA_OpenRouter::reset_local_stats();
        wp_send_json_success( [ 'local' => ENA_OpenRouter::get_local_stats() ] );
    }

    // ── Background job system ─────────────────────────────────────────────────

    public static function handle_dispatch_job(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $job_type = sanitize_key( $_POST['job_type'] ?? '' );
        if ( ! in_array( $job_type, [ 'collection', 'podcast' ], true ) ) {
            wp_send_json_error( 'Invalid job type' );
        }

        wp_send_json_success( ENA_Background::dispatch( $job_type ) );
    }

    public static function handle_job_status(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        wp_send_json_success( ENA_Background::status_for_client() );
    }

    public static function handle_bg_worker(): void {
        $job_id = sanitize_text_field( $_POST['job_id']   ?? '' );
        $token  = sanitize_text_field( $_POST['token']    ?? '' );
        $type   = sanitize_key(        $_POST['job_type'] ?? '' );

        if ( ! ENA_Background::validate_token( $job_id, $token ) ) {
            wp_die( 'Invalid token', '', [ 'response' => 403 ] );
        }

        ignore_user_abort( true );
        set_time_limit( 0 );

        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'background', $type );

        try {
            if ( $type === 'collection' ) {
                $result = ENA_Cron::run_pipeline( $plugin );
                $plugin->logger->end_run( $result );
                ENA_Background::finish( $job_id, $result );

            } elseif ( $type === 'podcast' ) {
                $result = $plugin->podcast->run();
                $plugin->logger->end_run( $result );
                ENA_Background::finish( $job_id, $result );

            } else {
                ENA_Background::fail( $job_id, 'Unknown job type: ' . $type );
            }
        } catch ( \Throwable $e ) {
            $plugin->logger->log_error( $type, 'Uncaught exception: ' . $e->getMessage() );
            ENA_Background::fail( $job_id, 'Exception: ' . $e->getMessage() );
        }

        wp_die();
    }
}
