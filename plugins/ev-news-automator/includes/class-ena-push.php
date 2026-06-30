<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Manages VAPID keys, push subscriptions, and Web Push delivery.
 * No Composer required — VAPID JWT signing is implemented with PHP's OpenSSL.
 */
class ENA_Push {

    private const OPT_KEYS  = 'ena_vapid_keys';
    private const OPT_SUBS  = 'ena_push_subscriptions';

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Returns the VAPID public key as a base64url string for JS PushManager.subscribe().
     * Keys are auto-generated on first call and persisted in wp_options.
     */
    public static function get_public_key_base64url(): string {
        return self::get_keys()['public_key_base64url'] ?? '';
    }

    /**
     * Counts today's articles from the live articles wp_option.
     */
    public static function get_today_count(): int {
        $articles = json_decode( get_option( ENA_OPT_LIVE_ARTICLES, '[]' ), true );
        if ( ! is_array( $articles ) ) return 0;
        $today = gmdate( 'Y-m-d' );
        return count( array_filter( $articles, fn( $a ) => ( $a['added_date'] ?? '' ) === $today ) );
    }

    /**
     * Stores a push subscription JSON (from JS sub.toJSON()).
     * Deduplicates by endpoint so re-subscribing is idempotent.
     */
    public static function save_subscription( string $json ): bool {
        $sub = json_decode( $json, true );
        if ( empty( $sub['endpoint'] ) ) return false;

        $subs = get_option( self::OPT_SUBS, [] );
        foreach ( $subs as $existing ) {
            if ( ( $existing['endpoint'] ?? '' ) === $sub['endpoint'] ) return true;
        }
        $subs[] = $sub;
        return update_option( self::OPT_SUBS, $subs, false );
    }

    /**
     * Sends a VAPID push (no payload) to every stored subscription.
     * Removes endpoints that respond with 404/410 (expired or unsubscribed).
     */
    public static function send_all( int $count ): void {
        $subs = get_option( self::OPT_SUBS, [] );
        if ( empty( $subs ) ) return;

        $subject = 'mailto:' . get_option( 'admin_email' );
        $pub_b64 = self::get_keys()['public_key_base64url'];
        $stale   = [];

        foreach ( $subs as $i => $sub ) {
            $endpoint = $sub['endpoint'] ?? '';
            if ( ! $endpoint ) continue;

            $jwt = self::build_vapid_jwt( $endpoint, $subject );

            $response = wp_remote_post( $endpoint, [
                'timeout' => 10,
                'headers' => [
                    'Authorization' => "vapid t={$jwt},k={$pub_b64}",
                    'TTL'           => '86400',
                    'Urgency'       => 'normal',
                    'Content-Type'  => 'application/octet-stream',
                ],
                'body' => '',
            ] );

            if ( ! is_wp_error( $response ) ) {
                $code = (int) wp_remote_retrieve_response_code( $response );
                if ( $code === 404 || $code === 410 ) {
                    $stale[] = $i;
                }
            }
        }

        if ( $stale ) {
            foreach ( array_reverse( $stale ) as $i ) {
                array_splice( $subs, $i, 1 );
            }
            update_option( self::OPT_SUBS, $subs, false );
        }
    }

    // ── VAPID key management ─────────────────────────────────────────────────

    private static function get_keys(): array {
        $keys = get_option( self::OPT_KEYS );
        if ( is_array( $keys ) && ! empty( $keys['public_key_base64url'] ) ) {
            return $keys;
        }
        try {
            $keys = self::generate_keys();
        } catch ( \RuntimeException $e ) {
            // EC key generation unavailable in this environment (e.g. LibreSSL on macOS).
            // Push notifications are disabled; the site continues to work normally.
            return [];
        }
        update_option( self::OPT_KEYS, $keys, false );
        return $keys;
    }

    private static function generate_keys(): array {
        $res = openssl_pkey_new( [
            'curve_name'       => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ] );

        if ( $res === false ) {
            throw new \RuntimeException( 'OpenSSL EC key generation failed: ' . openssl_error_string() );
        }

        openssl_pkey_export( $res, $private_pem );
        $details   = openssl_pkey_get_details( $res );
        $public_pem = $details['key'];

        // Uncompressed EC point: 0x04 || x (32 bytes) || y (32 bytes)
        $x = str_pad( $details['ec']['x'], 32, "\x00", STR_PAD_LEFT );
        $y = str_pad( $details['ec']['y'], 32, "\x00", STR_PAD_LEFT );

        return [
            'private_key_pem'      => $private_pem,
            'public_key_pem'       => $public_pem,
            'public_key_base64url' => self::base64url( "\x04" . $x . $y ),
        ];
    }

    // ── VAPID JWT (ES256) ────────────────────────────────────────────────────

    private static function build_vapid_jwt( string $endpoint, string $subject ): string {
        $parsed  = wp_parse_url( $endpoint );
        $aud     = $parsed['scheme'] . '://' . $parsed['host'];

        $header  = self::base64url( (string) wp_json_encode( [ 'typ' => 'JWT', 'alg' => 'ES256' ] ) );
        $payload = self::base64url( (string) wp_json_encode( [
            'aud' => $aud,
            'exp' => time() + 43200,
            'sub' => $subject,
        ] ) );

        $input = $header . '.' . $payload;
        $pkey  = openssl_pkey_get_private( self::get_keys()['private_key_pem'] );
        openssl_sign( $input, $der_sig, $pkey, OPENSSL_ALGO_SHA256 );

        return $input . '.' . self::base64url( self::der_to_p1363( $der_sig ) );
    }

    /**
     * Converts an OpenSSL DER-encoded ECDSA signature to IEEE P1363 (r||s).
     * JWT ES256 requires raw 32-byte r and 32-byte s concatenated.
     *
     * DER layout:  30 [total] 02 [r_len] [r…] 02 [s_len] [s…]
     */
    private static function der_to_p1363( string $der ): string {
        $offset = 2; // skip 0x30 and total_len

        $offset++; // skip 0x02 (r tag)
        $r_len   = ord( $der[ $offset++ ] );
        $r       = substr( $der, $offset, $r_len );
        $offset += $r_len;

        $offset++; // skip 0x02 (s tag)
        $s_len   = ord( $der[ $offset++ ] );
        $s       = substr( $der, $offset, $s_len );

        // DER may prepend a 0x00 byte to mark positive; strip it, then pad to 32 bytes.
        $r = str_pad( ltrim( $r, "\x00" ), 32, "\x00", STR_PAD_LEFT );
        $s = str_pad( ltrim( $s, "\x00" ), 32, "\x00", STR_PAD_LEFT );

        return $r . $s;
    }

    private static function base64url( string $data ): string {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
    }
}
