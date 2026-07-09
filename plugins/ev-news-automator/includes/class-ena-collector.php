<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Collector {

    // Spacing between OpenRouter calls so a full batch doesn't burst past the account's rate limit.
    private const REQUEST_DELAY_SECONDS = 2;

    private ENA_Sheets     $storage;
    private ENA_Scraper    $scraper;
    private ENA_OpenRouter $openrouter;
    private ENA_Logger     $logger;
    private ENA_Settings   $settings;

    public function __construct(
        ENA_Sheets     $storage,
        ENA_Scraper    $scraper,
        ENA_OpenRouter $openrouter,
        ENA_Logger     $logger,
        ENA_Settings   $settings
    ) {
        $this->storage    = $storage;
        $this->scraper    = $scraper;
        $this->openrouter = $openrouter;
        $this->logger     = $logger;
        $this->settings   = $settings;
    }

    public function run(): array {
        $sources       = $this->settings->sources();
        $existing_urls = $this->storage->existing_urls();
        $new_articles  = [];
        $batch_seen    = [];

        $cutoff   = $this->settings->article_age_cutoff();
        $html_cap = 5; // HTML pages carry no dates; top N items are assumed most recent.

        foreach ( $sources as $source ) {
            $items = $this->scraper->fetch_source( $source );
            $count = count( $items );

            if ( $source['method'] === 'html' ) {
                $items    = array_slice( $items, 0, $html_cap );
                $filtered = $count - count( $items );
                $msg      = "{$source['url']} — {$count} articles found";
                if ( $filtered > 0 ) $msg .= ", capped to {$html_cap} (HTML source, no pub dates)";
            } else {
                $items    = array_values( array_filter(
                    $items,
                    fn ( $i ) => $i['published_at'] > 0 && $i['published_at'] >= $cutoff
                ) );
                $filtered = $count - count( $items );
                $msg      = "{$source['url']} — {$count} articles found";
                if ( $filtered > 0 ) $msg .= ", {$filtered} before cutoff or undated skipped";
            }

            $this->logger->step( 'scrape_source', 'ok', $msg );

            foreach ( $items as $item ) {
                $url = $item['url'];
                if ( isset( $existing_urls[ $url ] ) || isset( $batch_seen[ $url ] ) ) continue;
                $batch_seen[ $url ] = true;
                $new_articles[]     = $item;
            }
        }

        $this->logger->step( 'dedupe', 'ok', count( $new_articles ) . ' new after deduplication' );

        // Sort by published_at DESC so articles are appended in recency order within each batch.
        usort( $new_articles, fn ( $a, $b ) => $b['published_at'] <=> $a['published_at'] );

        $rows  = [];
        $total = count( $new_articles );

        foreach ( $new_articles as $i => $article ) {
            $num = $i + 1;
            if ( $i > 0 ) {
                sleep( self::REQUEST_DELAY_SECONDS );
            }
            $summary = $this->openrouter->summarize( $article['title'], $article['excerpt'] ?? '' );

            if ( is_wp_error( $summary ) ) {
                $this->logger->step( 'openrouter_call', 'skip', "article {$num}/{$total} — skipped, will retry next run: " . $summary->get_error_message() );
                continue;
            }

            $this->logger->step( 'openrouter_call', 'ok', "article {$num}/{$total} — bg_title generated" );

            // upvote/downvote/clicks are real GA4-backed vote counters seeded at 0 on insert,
            // updated by the GA4 sync. added_date is written by the storage adapter automatically.
            $rows[] = [
                'title'       => $summary['bg_title'],
                'description' => $summary['bg_summary'],
                'link'        => $article['url'],
                'author'      => $article['source'],
                'upvote'      => 0,
                'downvote'    => 0,
                'clicks'      => 0,
                'pub_date'    => $article['published_at'] > 0 ? gmdate( 'Y-m-d', $article['published_at'] ) : '',
            ];
        }

        $added = 0;

        if ( ! empty( $rows ) ) {
            $result = $this->storage->append_rows( $rows );
            if ( ! is_wp_error( $result ) ) {
                $added = count( $rows );
                $this->logger->step( 'sheets_append', 'ok', "{$added} rows appended" );
            } else {
                $this->logger->log_error( 'sheets_append', $result->get_error_message() );
            }
        }

        // Sorting and trimming happen in ENA_Cron::run_pipeline() AFTER this returns,
        // so they operate on the full set (existing + newly appended) rows.
        return [ 'added' => $added ];
    }
}
