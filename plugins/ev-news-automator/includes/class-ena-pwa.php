<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Self-contained PWA layer: manifest, service worker, push subscription enqueue, REST count endpoint.
 * The theme needs no PWA awareness — activate this class and everything is wired.
 */
class ENA_PWA {

    public static function register(): void {
        add_action( 'init',               [ __CLASS__, 'register_rewrites' ] );
        add_filter( 'query_vars',         [ __CLASS__, 'add_query_vars' ] );
        add_action( 'template_redirect',  [ __CLASS__, 'serve_static_files' ] );
        add_action( 'after_switch_theme', [ __CLASS__, 'flush' ] );
        add_action( 'wp_head',            [ __CLASS__, 'inject_head_tags' ], 1 );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
        add_action( 'rest_api_init',      [ __CLASS__, 'register_rest_routes' ] );
    }

    // ── Rewrite rules ─────────────────────────────────────────────────────────

    public static function register_rewrites(): void {
        add_rewrite_rule( '^sw\.js$',       'index.php?ena_pwa_file=sw',       'top' );
        add_rewrite_rule( '^manifest\.json$', 'index.php?ena_pwa_file=manifest', 'top' );
    }

    public static function add_query_vars( array $vars ): array {
        $vars[] = 'ena_pwa_file';
        return $vars;
    }

    public static function serve_static_files(): void {
        $file = get_query_var( 'ena_pwa_file' );
        if ( ! $file ) return;

        if ( $file === 'sw' ) {
            header( 'Content-Type: application/javascript; charset=utf-8' );
            header( 'Service-Worker-Allowed: /' );
            header( 'Cache-Control: no-cache' );
            readfile( ENA_PLUGIN_DIR . 'assets/sw.js' );
            exit;
        }

        if ( $file === 'manifest' ) {
            header( 'Content-Type: application/manifest+json; charset=utf-8' );
            header( 'Cache-Control: no-cache' );
            $icon = get_stylesheet_directory_uri() . '/images/pwaicon.png';
            echo wp_json_encode( [
                'name'             => 'CLBD News Feed',
                'short_name'       => 'CLBD News Feed',
                'description'      => 'EV новини, ревюта и подкаст за електрически автомобили',
                'start_url'        => '/ev-news-feed/',
                'scope'            => '/',
                'display'          => 'standalone',
                'background_color' => '#0f172a',
                'theme_color'      => '#FE3652',
                'orientation'      => 'portrait-primary',
                'icons'            => [
                    [ 'src' => $icon, 'sizes' => '400x400', 'type' => 'image/png', 'purpose' => 'any maskable' ],
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            exit;
        }
    }

    public static function flush(): void {
        self::register_rewrites();
        flush_rewrite_rules();
    }

    // ── Head tags ─────────────────────────────────────────────────────────────

    public static function inject_head_tags(): void {
        $icon = get_stylesheet_directory_uri() . '/images/pwaicon.png';
        echo '<link rel="manifest" href="/manifest.json">' . "\n";
        echo '<link rel="apple-touch-icon" href="' . esc_url( $icon ) . '">' . "\n";
        echo '<meta name="theme-color" content="#FE3652">' . "\n";
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="CLBD News Feed">' . "\n";
    }

    // ── Scripts ───────────────────────────────────────────────────────────────

    public static function enqueue_scripts(): void {
        wp_enqueue_script( 'ena-pwa-init', ENA_PLUGIN_URL . 'assets/pwa-init.js', [], ENA_VERSION, true );
        wp_localize_script( 'ena-pwa-init', 'pwaConfig', [
            'vapidPublicKey' => ENA_Push::get_public_key_base64url(),
            'subscribeUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'          => wp_create_nonce( 'ena_push_sub' ),
            'swUrl'          => '/sw.js',
        ] );
    }

    // ── REST endpoint ─────────────────────────────────────────────────────────

    public static function register_rest_routes(): void {
        register_rest_route( 'carlifebydani/v1', '/daily-count', [
            'methods'             => 'GET',
            'callback'            => [ __CLASS__, 'rest_daily_count' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public static function rest_daily_count(): \WP_REST_Response {
        $articles = json_decode( get_option( ENA_OPT_LIVE_ARTICLES, '[]' ), true );
        $today    = gmdate( 'Y-m-d' );
        $count    = is_array( $articles )
            ? count( array_filter( $articles, fn( $a ) => ( $a['added_date'] ?? '' ) === $today ) )
            : 0;
        return rest_ensure_response( [ 'count' => $count ] );
    }
}
