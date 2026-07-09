<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Reads GA4 event counts via the Analytics Data API v1.
// The site fires dataLayer events with custom parameter 'article_url':
//   'ev_news_click'    when a visitor clicks an EV news article card (theme/js/ev-news-tracking.js)
//   'ev_news_upvote'   / 'ev_news_downvote' when a visitor votes on a card (theme/js/ev-news-voting.js)
class ENA_Analytics {

    private const API_BASE = 'https://analyticsdata.googleapis.com/v1beta/properties';
    private const SCOPES   = [ 'https://www.googleapis.com/auth/analytics.readonly' ];

    private ENA_Google_Auth $auth;
    private ENA_Settings    $settings;

    public function __construct( ENA_Google_Auth $auth, ENA_Settings $settings ) {
        $this->auth     = $auth;
        $this->settings = $settings;
    }

    /**
     * Fetch ev_news_click event counts for the given URLs over the past $days_back days.
     * Returns [url => int]. Every URL in $urls appears in the result; missing URLs default to 0.
     * Returns WP_Error('ga4_not_configured') if ga4_property_id is not set.
     */
    public function fetch_clicks( array $urls, int $days_back = 7 ): array|WP_Error {
        return $this->fetch_event_counts( 'ev_news_click', $urls, $days_back );
    }

    /**
     * Fetch ev_news_upvote event counts for the given URLs. Same contract as fetch_clicks().
     */
    public function fetch_upvotes( array $urls, int $days_back = 7 ): array|WP_Error {
        return $this->fetch_event_counts( 'ev_news_upvote', $urls, $days_back );
    }

    /**
     * Fetch ev_news_downvote event counts for the given URLs. Same contract as fetch_clicks().
     */
    public function fetch_downvotes( array $urls, int $days_back = 7 ): array|WP_Error {
        return $this->fetch_event_counts( 'ev_news_downvote', $urls, $days_back );
    }

    /**
     * Fetch $event_name counts (dimension customEvent:article_url) for the given URLs
     * over the past $days_back days. Returns [url => int], every URL seeded at 0.
     */
    private function fetch_event_counts( string $event_name, array $urls, int $days_back ): array|WP_Error {
        $property_id = $this->settings->ga4_property_id();
        if ( empty( $property_id ) ) {
            return new WP_Error( 'ga4_not_configured', 'ga4_property_id not set' );
        }

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $report = $this->run_report( $token, $property_id, $event_name, $days_back );
        if ( is_wp_error( $report ) ) {
            // Attach the GA4 response body to the error so callers can log the full detail.
            $data = $report->get_error_data();
            if ( ! empty( $data['body'] ) ) {
                return new WP_Error(
                    $report->get_error_code(),
                    $report->get_error_message() . ' — ' . substr( $data['body'], 0, 300 ),
                    $data
                );
            }
            return $report;
        }

        // Seed all requested URLs at 0, then overlay GA4 counts.
        // GA4 truncates custom dimension values at 100 chars, so build a
        // prefix → full-url index as a fallback for long URLs.
        $counts = array_fill_keys( $urls, 0 );
        $prefix_to_full = [];
        foreach ( $urls as $u ) {
            $prefix_to_full[ substr( $u, 0, 100 ) ] = $u;
        }

        foreach ( $report as $ga4_url => $count ) {
            if ( array_key_exists( $ga4_url, $counts ) ) {
                $counts[ $ga4_url ] = $count;
            } elseif ( isset( $prefix_to_full[ $ga4_url ] ) ) {
                $counts[ $prefix_to_full[ $ga4_url ] ] = $count;
            }
        }

        return $counts;
    }

    private function run_report( string $token, string $property_id, string $event_name, int $days_back ): array|WP_Error {
        $url  = self::API_BASE . "/{$property_id}:runReport";
        $body = [
            'dimensions'      => [ [ 'name' => 'customEvent:article_url' ] ],
            'metrics'         => [ [ 'name' => 'eventCount' ] ],
            'dateRanges'      => [ [ 'startDate' => "{$days_back}daysAgo", 'endDate' => 'today' ] ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName'    => 'eventName',
                    'stringFilter' => [ 'matchType' => 'EXACT', 'value' => $event_name ],
                ],
            ],
            'limit' => 10000,
        ];

        $response = ENA_HTTP::post_json( $url, $body, [ 'Authorization' => "Bearer {$token}" ] );
        $data     = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        $result = [];
        foreach ( $data['rows'] ?? [] as $row ) {
            $article_url = $row['dimensionValues'][0]['value'] ?? '';
            $count       = (int) ( $row['metricValues'][0]['value'] ?? 0 );
            if ( $article_url ) {
                $result[ $article_url ] = $count;
            }
        }

        return $result;
    }
}
