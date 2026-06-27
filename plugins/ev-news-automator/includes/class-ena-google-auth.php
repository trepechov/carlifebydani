<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Google_Auth {

    private const TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';

    private ENA_Settings $settings;

    public function __construct( ENA_Settings $settings ) {
        $this->settings = $settings;
    }

    public function get_access_token( array $scopes ): string|WP_Error {
        $cache_key = md5( implode( ',', $scopes ) );
        $cached    = $this->cached_token( $cache_key );
        if ( $cached ) return $cached;

        $sa = $this->load_service_account();
        if ( is_wp_error( $sa ) ) return $sa;

        $jwt = $this->build_jwt( $sa, $scopes );
        if ( is_wp_error( $jwt ) ) return $jwt;

        $response = wp_remote_post( self::TOKEN_URL, [
            'timeout' => 30,
            'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ],
            'body'    => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ],
        ] );

        if ( is_wp_error( $response ) ) return $response;

        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        if ( empty( $data['access_token'] ) ) {
            return new WP_Error( 'auth_error', 'No access_token in response', $data );
        }

        $expires_in = (int) ( $data['expires_in'] ?? 3600 );
        $this->store_token( $cache_key, $data['access_token'], $expires_in );

        return $data['access_token'];
    }

    private function build_jwt( array $sa, array $scopes ): string|WP_Error {
        $now = time();
        $header  = $this->base64url( wp_json_encode( [ 'alg' => 'RS256', 'typ' => 'JWT' ] ) );
        $payload = $this->base64url( wp_json_encode( [
            'iss'   => $sa['client_email'],
            'scope' => implode( ' ', $scopes ),
            'aud'   => self::TOKEN_URL,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ] ) );

        $signing_input = "{$header}.{$payload}";
        $private_key   = openssl_pkey_get_private( $sa['private_key'] );
        if ( ! $private_key ) {
            return new WP_Error( 'jwt_error', 'Failed to load private key' );
        }

        $signature = '';
        if ( ! openssl_sign( $signing_input, $signature, $private_key, OPENSSL_ALGO_SHA256 ) ) {
            return new WP_Error( 'jwt_error', 'openssl_sign failed' );
        }

        return $signing_input . '.' . $this->base64url( $signature );
    }

    private function base64url( string $data ): string {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
    }

    private function cached_token( string $cache_key ): ?string {
        $tokens = get_option( ENA_OPT_GOOGLE_TOKEN, [] );
        if ( ! is_array( $tokens ) ) return null;
        $entry = $tokens[ $cache_key ] ?? null;
        if ( ! $entry ) return null;
        if ( time() >= ( $entry['expires_at'] - 60 ) ) return null;
        return $entry['token'];
    }

    private function store_token( string $cache_key, string $token, int $expires_in ): void {
        $tokens = get_option( ENA_OPT_GOOGLE_TOKEN, [] );
        if ( ! is_array( $tokens ) ) $tokens = [];
        $tokens[ $cache_key ] = [
            'token'      => $token,
            'expires_at' => time() + $expires_in,
        ];
        update_option( ENA_OPT_GOOGLE_TOKEN, $tokens );
    }

    private function load_service_account(): array|WP_Error {
        $path = $this->settings->service_account_path();
        if ( empty( $path ) ) {
            return new WP_Error( 'sa_missing', 'Service account path not configured' );
        }

        // Reject paths inside webroot
        $real = realpath( $path );
        if ( $real && strpos( $real, realpath( ABSPATH ) ) === 0 ) {
            return new WP_Error( 'sa_webroot', 'Service account file must not be inside webroot' );
        }

        if ( ! file_exists( $path ) ) {
            return new WP_Error( 'sa_missing', "Service account file not found: {$path}" );
        }

        $json = file_get_contents( $path );
        $data = json_decode( $json, true );

        if ( json_last_error() !== JSON_ERROR_NONE || empty( $data['client_email'] ) || empty( $data['private_key'] ) ) {
            return new WP_Error( 'sa_invalid', 'Service account JSON is invalid or missing required fields' );
        }

        return $data;
    }
}
