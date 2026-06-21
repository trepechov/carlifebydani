<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Ajax {

    public static function register(): void {
        add_action( 'wp_ajax_ena_run_collection',    [ __CLASS__, 'handle_run_collection' ] );
        add_action( 'wp_ajax_ena_run_sync',          [ __CLASS__, 'handle_run_sync' ] );
        add_action( 'wp_ajax_ena_run_podcast',       [ __CLASS__, 'handle_run_podcast' ] );
        add_action( 'wp_ajax_ena_openrouter_usage',  [ __CLASS__, 'handle_openrouter_usage' ] );
        add_action( 'wp_ajax_ena_reset_usage_stats', [ __CLASS__, 'handle_reset_usage_stats' ] );
    }

    public static function handle_run_collection(): void {
        check_ajax_referer( 'ena_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Forbidden', 403 );
        }

        $plugin = ENA_Plugin::instance();
        $plugin->logger->begin_run( 'manual', 'collection' );

        try {
            $rows   = $plugin->storage->read_data_rows();
            $urls   = is_wp_error( $rows ) ? [] : array_column( $rows, 'link' );
            $clicks = $plugin->analytics->fetch_clicks( $urls );

            if ( is_wp_error( $clicks ) ) {
                $plugin->logger->log_error( 'analytics_fetch', $clicks->get_error_message() );
            } else {
                $plugin->storage->update_clicks( $clicks );
                $with_clicks = count( array_filter( $clicks, fn ( $c ) => $c > 0 ) );
                $plugin->logger->step( 'analytics_fetch', 'ok', count( $urls ) . " URLs, {$with_clicks} with clicks" );

                $sort_result = $plugin->storage->sort_by_clicks();
                if ( is_wp_error( $sort_result ) ) {
                    $plugin->logger->step( 'sheets_sort', 'error', $sort_result->get_error_message() );
                } else {
                    $plugin->logger->step( 'sheets_sort', 'ok', 'rows sorted by clicks DESC' );
                }
            }

            $result = $plugin->collector->run();

            $sync_result = $plugin->sync->run();
            $result['synced'] = $sync_result['count'] ?? 0;

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
}
