<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Plugin {

    private static ?ENA_Plugin $instance = null;

    public ENA_Settings    $settings;
    public ENA_Logger      $logger;
    public ENA_HTTP        $http;
    public ENA_Google_Auth $auth;
    public ENA_Sheets      $storage;
    public ENA_Analytics   $analytics;
    public ENA_Docs        $docs;
    public ENA_OpenRouter  $openrouter;
    public ENA_Scraper     $scraper;
    public ENA_Collector   $collector;
    public ENA_Sync        $sync;
    public ENA_Podcast     $podcast;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->settings   = new ENA_Settings();
        $this->logger     = new ENA_Logger();
        $this->http       = new ENA_HTTP();
        $this->auth       = new ENA_Google_Auth( $this->settings );

        // STORAGE ADAPTER BINDING — swap ENA_Sheets for a different class to change the backend
        $this->storage    = new ENA_Sheets( $this->auth, $this->settings );

        $this->analytics  = new ENA_Analytics( $this->auth, $this->settings );
        $this->docs       = new ENA_Docs( $this->auth );
        $this->openrouter = new ENA_OpenRouter( $this->settings, $this->logger );
        $this->scraper    = new ENA_Scraper( $this->logger );
        $this->collector  = new ENA_Collector( $this->storage, $this->scraper, $this->openrouter, $this->logger, $this->settings );
        $this->sync       = new ENA_Sync( $this->storage, $this->logger );
        $this->podcast    = new ENA_Podcast( $this->storage, $this->analytics, $this->openrouter, $this->docs, $this->logger, $this->settings );

        ENA_Cron::register_hooks();
        ENA_Ajax::register();
        ENA_PWA::register();

        $admin = new ENA_Admin( $this->settings, $this->logger );
        add_action( 'admin_menu', [ $admin, 'add_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $admin, 'enqueue' ] );
        add_action( 'admin_post_ena_save_settings', [ $admin, 'handle_settings_save' ] );
    }
}
