<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Admin {

    private ENA_Settings $settings;
    private ENA_Logger   $logger;

    public function __construct( ENA_Settings $settings, ENA_Logger $logger ) {
        $this->settings = $settings;
        $this->logger   = $logger;
    }

    public function add_menu(): void {
        add_menu_page(
            'EV News Automator',
            'EV News',
            'manage_options',
            'ev-news-automator',
            [ $this, 'render_dashboard' ],
            'dashicons-rss',
            30
        );

        add_submenu_page(
            'ev-news-automator',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'ev-news-automator',
            [ $this, 'render_dashboard' ]
        );

        add_submenu_page(
            'ev-news-automator',
            'Settings',
            'Settings',
            'manage_options',
            'ev-news-automator-settings',
            [ $this, 'render_settings' ]
        );

        add_submenu_page(
            'ev-news-automator',
            'Work Process',
            'Work Process',
            'manage_options',
            'ev-news-automator-plan',
            [ $this, 'render_how_it_works' ]
        );
    }

    public function enqueue( string $hook ): void {
        if ( strpos( $hook, 'ev-news-automator' ) === false ) return;

        wp_enqueue_style(
            'ena-admin',
            ENA_PLUGIN_URL . 'assets/admin.css',
            [],
            ENA_VERSION
        );

        wp_enqueue_script(
            'ena-admin',
            ENA_PLUGIN_URL . 'assets/admin.js',
            [],
            ENA_VERSION,
            true
        );

        wp_localize_script( 'ena-admin', 'enaAjax', [
            'url'   => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'ena_admin' ),
        ] );
    }

    public function handle_settings_save(): void {
        check_admin_referer( 'ena_save_settings', 'ena_settings_nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Forbidden', 403 );
        }

        $allowed_intervals = [ '15min', '30min', '1hour', '6hours', '12hours', 'daily' ];
        $allowed_ages      = [ '1d', '2d', '3d', '4d', '5d', '6d', '1w' ];

        $values = [];

        // API key: only update if a non-empty value was submitted
        $submitted_key = sanitize_text_field( $_POST['openrouter_api_key'] ?? '' );
        if ( ! empty( $submitted_key ) ) {
            $values['openrouter_api_key'] = $submitted_key;
        }

        $values['openrouter_model']     = sanitize_text_field( $_POST['openrouter_model'] ?? 'anthropic/claude-opus-4-8' );
        $values['spreadsheet_id']       = sanitize_text_field( $_POST['spreadsheet_id'] ?? '' );
        $values['service_account_path'] = sanitize_text_field( $_POST['service_account_path'] ?? '' );
        $values['ga4_property_id']      = sanitize_text_field( $_POST['ga4_property_id'] ?? '' );
        $values['podcast_doc_id']       = sanitize_text_field( $_POST['podcast_doc_id'] ?? '' );
        $values['max_articles']         = absint( $_POST['max_articles'] ?? 50 );
        $values['max_script_articles']  = absint( $_POST['max_script_articles'] ?? 10 );

        $age = sanitize_text_field( $_POST['article_age_limit'] ?? '1d' );
        $values['article_age_limit'] = in_array( $age, $allowed_ages, true ) ? $age : '1d';

        $interval = sanitize_text_field( $_POST['collection_interval'] ?? 'daily' );
        $values['collection_interval'] = in_array( $interval, $allowed_intervals, true ) ? $interval : 'daily';

        $time = sanitize_text_field( $_POST['collection_time'] ?? '09:00' );
        $values['collection_time'] = preg_match( '/^\d{2}:\d{2}$/', $time ) ? $time : '09:00';

        // Sources: parse each line
        $raw_sources    = sanitize_textarea_field( $_POST['sources'] ?? '' );
        $values['sources'] = $raw_sources;

        $this->settings->update( $values );
        ENA_Cron::reschedule();

        wp_safe_redirect( add_query_arg( 'updated', '1', wp_get_referer() ) );
        exit;
    }

    public function render_settings(): void {
        $settings = $this->settings;
        include ENA_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public function render_dashboard(): void {
        $settings = $this->settings;
        $logger   = $this->logger;
        include ENA_PLUGIN_DIR . 'admin/views/dashboard-page.php';
    }

    public function render_how_it_works(): void {
        include ENA_PLUGIN_DIR . 'admin/views/how-it-works-page.php';
    }
}
