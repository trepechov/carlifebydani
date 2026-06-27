<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Podcast {

    private ENA_Sheets     $storage;
    private ENA_Analytics  $analytics;
    private ENA_OpenRouter $openrouter;
    private ENA_Docs       $docs;
    private ENA_Logger     $logger;
    private ENA_Settings   $settings;

    public function __construct(
        ENA_Sheets     $storage,
        ENA_Analytics  $analytics,
        ENA_OpenRouter $openrouter,
        ENA_Docs       $docs,
        ENA_Logger     $logger,
        ENA_Settings   $settings
    ) {
        $this->storage    = $storage;
        $this->analytics  = $analytics;
        $this->openrouter = $openrouter;
        $this->docs       = $docs;
        $this->logger     = $logger;
        $this->settings   = $settings;
    }

    public function run(): array {
        $rows = $this->storage->read_data_rows();
        if ( is_wp_error( $rows ) ) {
            $this->logger->log( 'podcast', 'error', $rows->get_error_message() );
            return [ 'doc_url' => '', 'count' => 0 ];
        }

        // Step 1: refresh GA4 clicks so ordering reflects the most recent engagement.
        $urls   = array_column( $rows, 'link' );
        $clicks = $this->analytics->fetch_clicks( $urls );

        if ( is_wp_error( $clicks ) ) {
            $this->logger->step( 'podcast_analytics', 'skip', $clicks->get_error_message() );
        } else {
            $this->storage->update_clicks( $clicks );
            // Overlay fresh click counts onto our in-memory rows before sorting.
            foreach ( $rows as &$row ) {
                if ( isset( $clicks[ $row['link'] ] ) ) {
                    $row['clicks'] = $clicks[ $row['link'] ];
                }
            }
            unset( $row );
            $with_clicks = count( array_filter( $clicks, fn ( $c ) => $c > 0 ) );
            $this->logger->step( 'podcast_analytics', 'ok', count( $urls ) . " URLs, {$with_clicks} with clicks" );
        }

        // Step 2: order by clicks descending and take the top N.
        $top_n = max( 1, (int) $this->settings->get( 'max_script_articles', 10 ) );
        usort( $rows, fn ( $a, $b ) => $b['clicks'] <=> $a['clicks'] );
        $top = array_slice( $rows, 0, $top_n );

        // Step 3: generate summaries from existing title + description — no scraping needed.
        $sections = [];

        foreach ( $top as $row ) {
            $generated = $this->openrouter->podcast_summary( $row['title'], $row['description'] );
            if ( is_wp_error( $generated ) ) {
                $this->logger->step( 'podcast_summary', 'error', $generated->get_error_message() );
                $summary = $row['description']; // fall back to existing description
            } else {
                $summary = $generated;
                $this->logger->step( 'podcast_summary', 'ok', "generated for: {$row['title']}" );
            }

            $sections[] = [
                'bg_title'    => $row['title'],
                'url'         => $row['link'],
                'description' => $row['description'],
                'summary'     => $summary,
            ];
        }

        // Step 4: write the script document.
        // WHY MANUAL DOC ID: Google service accounts have no Drive storage quota of their own.
        // Calling ENA_Docs::create_doc() via the Docs API returns PERMISSION_DENIED, and
        // calling Drive Files.create returns storageQuotaExceeded — even for zero-byte
        // Google Docs format files. This is a Google API limitation for non-Workspace accounts:
        // service accounts cannot create new Drive files on a personal Google account without
        // domain-wide delegation (Workspace only). Writing to an EXISTING document works fine.
        // Workaround: the user creates a Google Doc manually before each recording session
        // and pastes its ID in Settings → Podcast Script Document ID. The plugin then appends
        // the generated script to that document via ENA_Docs::append_sections().
        $doc_id = $this->settings->podcast_doc_id();
        if ( empty( $doc_id ) ) {
            $this->logger->log( 'podcast', 'error', 'podcast_doc_id not configured — create a Google Doc, paste its ID in plugin settings.' );
            return [ 'doc_url' => '', 'count' => 0 ];
        }

        $this->docs->append_sections( $doc_id, $sections );

        $url        = $this->docs->doc_url( $doc_id );
        $count      = count( $sections );
        $top_clicks = array_sum( array_column( $top, 'clicks' ) );

        $this->logger->set_status( ENA_OPT_STATUS_PODCAST, [
            'timestamp'  => ( new DateTimeImmutable() )->format( 'c' ),
            'doc_url'    => $url,
            'count'      => $count,
            'top_clicks' => $top_clicks,
        ] );

        return [ 'doc_url' => $url, 'count' => $count ];
    }
}
