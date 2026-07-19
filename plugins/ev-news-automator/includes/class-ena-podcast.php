<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Podcast {

    // Spacing between OpenRouter calls so a full batch doesn't burst past the account's rate limit.
    private const REQUEST_DELAY_SECONDS = 2;

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
        // Steps 1–2: refresh GA4 clicks/votes and physically re-sort the sheet —
        // identical to the collection pipeline (ENA_Cron::run_pipeline()) — so the
        // script is always built from the exact same order the spreadsheet ends up in.
        ENA_Cron::refresh_analytics( $this->storage, $this->analytics, $this->logger );
        $sort_result = ENA_Cron::sort_sheet( $this->storage, $this->logger );
        if ( is_wp_error( $sort_result ) ) {
            $this->logger->log( 'podcast', 'error', $sort_result->get_error_message() );
            return [ 'doc_url' => '', 'count' => 0 ];
        }

        // Step 3: read the now-sorted sheet and take the top N off the top —
        // same order the spreadsheet shows.
        $rows = $this->storage->read_data_rows();
        if ( is_wp_error( $rows ) ) {
            $this->logger->log( 'podcast', 'error', $rows->get_error_message() );
            return [ 'doc_url' => '', 'count' => 0 ];
        }
        $top_n = max( 1, (int) $this->settings->get( 'max_script_articles', 10 ) );
        $top   = array_slice( $rows, 0, $top_n );

        // Step 4: generate summaries from existing title + description — no scraping needed.
        $sections      = [];
        $total         = count( $top );
        $stopped_early = false; // set once a 429/401 hits, so remaining articles skip the API call too.

        foreach ( $top as $i => $row ) {
            // Placeholder shown in the doc itself whenever no AI summary was generated, so
            // reading the script later makes it obvious this isn't "nothing extra to add" —
            // it's a failure the next run should be expected to fill in.
            $summary = '[AI резюме не е генерирано — генерирането спря по-рано в тази партида]';

            if ( ! $stopped_early ) {
                if ( $i > 0 ) {
                    sleep( self::REQUEST_DELAY_SECONDS );
                }
                $generated = $this->openrouter->podcast_summary( $row['title'], $row['description'] );

                if ( is_wp_error( $generated ) ) {
                    if ( ENA_OpenRouter::is_fatal_batch_error( $generated ) ) {
                        $stopped_early = true;
                        $left          = $total - ( $i + 1 );
                        $reason        = ENA_OpenRouter::fatal_batch_reason( $generated );
                        $this->logger->step(
                            'podcast_summary',
                            'skip',
                            "article " . ( $i + 1 ) . "/{$total} — {$reason} — {$left} article(s) will be written with description only: " . $generated->get_error_message()
                        );
                        $summary = "[AI резюме не е генерирано — {$reason}]";
                    } else {
                        $this->logger->step( 'podcast_summary', 'error', $generated->get_error_message() );
                        $summary = "[AI резюме неуспешно: {$generated->get_error_message()}]";
                    }
                } else {
                    $summary = $generated;
                    $this->logger->step( 'podcast_summary', 'ok', "generated for: {$row['title']}" );
                }
            }

            $sections[] = [
                'bg_title'    => $row['title'],
                'url'         => $row['link'],
                'off_topic'   => $row['off_topic'] ?? '', // "yes"/"no" off-topic flag (yes = NOT about EVs)
                'tags'        => $row['tags'] ?? '',    // comma-separated Bulgarian tags from analyze()
                'region'      => $row['region'] ?? '',  // ISO region code(s) the article is about
                'description' => $row['description'], // copied verbatim from the sheet
                'summary'     => $summary,             // longer AI-generated write-up
            ];
        }

        // Step 5: write the script document.
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
