<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_HTTP {

    // Trusted fixed endpoints — bypass is_safe_url
    private const ALLOWLISTED_HOSTS = [
        'accounts.google.com',
        'sheets.googleapis.com',
        'docs.googleapis.com',
        'www.googleapis.com',
        'analyticsdata.googleapis.com',
        'openrouter.ai',
    ];

    public static function is_safe_url( string $url ): bool {
        if ( wp_parse_url( $url, PHP_URL_SCHEME ) !== 'https' ) {
            return false;
        }
        $host = wp_parse_url( $url, PHP_URL_HOST );
        if ( ! $host ) {
            return false;
        }
        if ( in_array( $host, self::ALLOWLISTED_HOSTS, true ) ) {
            return true;
        }
        $ip = gethostbyname( $host );
        if (
            $ip === $host ||
            filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === false ||
            filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false
        ) {
            return false;
        }
        return true;
    }

    public static function get( string $url, array $args = [] ): array|WP_Error {
        $defaults = [ 'timeout' => 15, 'redirection' => 3 ];
        $response = wp_remote_get( $url, array_merge( $defaults, $args ) );
        if ( is_wp_error( $response ) ) return $response;
        $code = wp_remote_retrieve_response_code( $response );
        if ( $code < 200 || $code >= 300 ) {
            return new WP_Error( 'http_error', "HTTP {$code}", [ 'url' => $url, 'body' => wp_remote_retrieve_body( $response ) ] );
        }
        return $response;
    }

    public static function post_json( string $url, array $body, array $headers = [] ): array|WP_Error {
        $response = wp_remote_post( $url, [
            'timeout' => 60,
            'headers' => array_merge( [ 'Content-Type' => 'application/json' ], $headers ),
            'body'    => wp_json_encode( $body ),
        ] );
        if ( is_wp_error( $response ) ) return $response;
        $code = wp_remote_retrieve_response_code( $response );
        if ( $code < 200 || $code >= 300 ) {
            return new WP_Error( 'http_error', "HTTP {$code}", [ 'url' => $url, 'body' => wp_remote_retrieve_body( $response ) ] );
        }
        return $response;
    }

    public static function retrieve_json( array|WP_Error $response ): array|WP_Error {
        if ( is_wp_error( $response ) ) return $response;
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'json_parse', 'JSON decode failed', [ 'body' => substr( $body, 0, 500 ) ] );
        }
        return $data;
    }
}
