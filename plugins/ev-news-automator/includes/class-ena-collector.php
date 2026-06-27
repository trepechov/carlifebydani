<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Collector {

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
                if ( $filtered > 0 ) $msg .= ", {$filtered} older than 24h or undated skipped";
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
            $num     = $i + 1;
            $summary = $this->openrouter->summarize( $article['title'], $article['excerpt'] ?? '' );

            if ( is_wp_error( $summary ) ) {
                $this->logger->step( 'openrouter_call', 'skip', "article {$num}/{$total} — skipped, will retry next run: " . $summary->get_error_message() );
                continue;
            }

            $this->logger->step( 'openrouter_call', 'ok', "article {$num}/{$total} — bg_title generated" );

            // upvote/downvote deprecated; clicks=0 on insert (updated daily by GA4 sync).
            // added_date is written by the storage adapter automatically.
            $rows[] = [
                'title'       => $summary['bg_title'],
                'description' => $summary['bg_summary'],
                'link'        => $article['url'],
                'author'      => $article['source'],
                'upvote'      => '',
                'downvote'    => '',
                'clicks'      => 0,
                'pub_date'    => $article['published_at'] > 0 ? gmdate( 'Y-m-d', $article['published_at'] ) : '',
            ];
        }

        $added   = 0;
        $removed = 0;

        if ( ! empty( $rows ) ) {
            $result = $this->storage->append_rows( $rows );
            if ( ! is_wp_error( $result ) ) {
                $added = count( $rows );
                $this->logger->step( 'sheets_append', 'ok', "{$added} rows appended" );
            } else {
                $this->logger->log_error( 'sheets_append', $result->get_error_message() );
            }
        }

        $max     = (int) $this->settings->get( 'max_articles', 50 );
        $removed = $this->trim_to_max( $max );

        $this->logger->set_status( ENA_OPT_STATUS_COLLECTION, [
            'timestamp' => ( new DateTimeImmutable() )->format( 'c' ),
            'added'     => $added,
            'removed'   => $removed,
        ] );

        return [ 'added' => $added, 'removed' => $removed ];
    }

    private function trim_to_max( int $max ): int {
        $rows = $this->storage->read_data_rows();
        if ( is_wp_error( $rows ) ) return 0;

        $count = count( $rows );
        if ( $count <= $max ) return 0;

        $excess = $count - $max;

        // Build a deletion priority list: zero-click rows first (oldest pub_date/added_date first),
        // then clicked rows as a last resort. Sheet position is NOT used — sort_by_clicks() may
        // have placed high-click articles at the top, so deleting by index 0..n would remove them.
        $candidates = [];
        foreach ( $rows as $idx => $row ) {
            $candidates[] = [
                'index'    => $idx,
                'clicks'   => (int) $row['clicks'],
                'date_key' => $row['pub_date'] ?: ( $row['added_date'] ?? '' ),
            ];
        }

        usort( $candidates, function ( $a, $b ) {
            // Prefer to delete zero-click rows before clicked rows.
            if ( $a['clicks'] === 0 && $b['clicks'] > 0 ) return -1;
            if ( $a['clicks'] > 0 && $b['clicks'] === 0 ) return  1;
            // Within same group: delete oldest first.
            return strcmp( $a['date_key'], $b['date_key'] );
        } );

        $to_delete = array_column( array_slice( $candidates, 0, $excess ), 'index' );
        $result    = $this->storage->delete_rows( $to_delete );

        if ( is_wp_error( $result ) ) {
            $this->logger->step( 'sheets_trim', 'error', $result->get_error_message() );
            return 0;
        }

        $this->logger->step( 'sheets_trim', 'ok', "{$excess} oldest zero-click rows removed (max={$max})" );
        return $excess;
    }
}
