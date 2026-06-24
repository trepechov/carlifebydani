<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Settings {

    public function get( string $key, $default = null ) {
        $all = $this->all();
        return $all[ $key ] ?? ( $default ?? ( $this->defaults()[ $key ] ?? null ) );
    }

    public function all(): array {
        $saved = get_option( ENA_OPT_SETTINGS, [] );
        return array_merge( $this->defaults(), is_array( $saved ) ? $saved : [] );
    }

    public function update( array $values ): void {
        $current = $this->all();
        update_option( ENA_OPT_SETTINGS, array_merge( $current, $values ) );
    }

    public function defaults(): array {
        return [
            'openrouter_api_key'    => '',
            'openrouter_model'      => 'anthropic/claude-opus-4-8',
            'spreadsheet_id'        => '',
            'service_account_path'  => '',
            'ga4_property_id'       => '',
            'podcast_doc_id'        => '',
            'max_articles'          => 50,
            'max_script_articles'   => 10,
            'collection_interval'   => 'daily',
            'collection_time'       => '09:00',
            'article_age_limit'     => '1d',
            'sources'               => '',
        ];
    }

    public function sources(): array {
        $raw = $this->get( 'sources', '' );
        $lines = array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
        $result = [];
        foreach ( $lines as $line ) {
            $parts = preg_split( '/\s+/', $line, 2 );
            $url = $parts[0] ?? '';
            $method = strtolower( trim( $parts[1] ?? 'rss' ) );
            if ( ! in_array( $method, [ 'rss', 'html' ], true ) ) {
                $method = 'rss';
            }
            if ( ENA_HTTP::is_safe_url( $url ) ) {
                $result[] = [ 'url' => $url, 'method' => $method ];
            }
        }
        return $result;
    }

    public function article_age_cutoff(): int {
        $map = [
            '1d' => DAY_IN_SECONDS,
            '2d' => 2 * DAY_IN_SECONDS,
            '3d' => 3 * DAY_IN_SECONDS,
            '4d' => 4 * DAY_IN_SECONDS,
            '5d' => 5 * DAY_IN_SECONDS,
            '6d' => 6 * DAY_IN_SECONDS,
            '1w' => WEEK_IN_SECONDS,
        ];
        $val = $this->get( 'article_age_limit', '1d' );
        return time() - ( $map[ $val ] ?? DAY_IN_SECONDS );
    }

    public function service_account_path(): string {
        return $this->get( 'service_account_path', '' );
    }

    public function podcast_doc_id(): string {
        return (string) $this->get( 'podcast_doc_id', '' );
    }

    public function ga4_property_id(): string {
        return (string) $this->get( 'ga4_property_id', '' );
    }
}
