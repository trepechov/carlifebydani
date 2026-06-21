<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_OpenRouter {

    private const API_URL      = 'https://openrouter.ai/api/v1/chat/completions';
    private const KEY_INFO_URL = 'https://openrouter.ai/api/v1/auth/key';
    private const USAGE_OPTION = 'ena_openrouter_usage';

    private ENA_Settings $settings;

    public function __construct( ENA_Settings $settings ) {
        $this->settings = $settings;
    }

    public function summarize( string $original_title, string $excerpt_or_body ): array|WP_Error {
        $result = $this->chat(
            'Bulgarian automotive news editor. Reply ONLY with JSON: {"title":"...","summary":"..."}. Title = concise BG headline. Summary = 2-3 BG sentences. No markdown.',
            "Original title: {$original_title}\n\nArticle excerpt: {$excerpt_or_body}\n\nProduce JSON.",
            [ 'temperature' => 0.4 ],
            'summarize'
        );

        if ( is_wp_error( $result ) ) return $result;

        $parsed = json_decode( $result, true );
        if ( json_last_error() !== JSON_ERROR_NONE || empty( $parsed['title'] ) ) {
            return [
                'bg_title'   => $original_title,
                'bg_summary' => substr( $excerpt_or_body, 0, 300 ),
            ];
        }

        return [
            'bg_title'   => $parsed['title'],
            'bg_summary' => $parsed['summary'] ?? '',
        ];
    }

    public function podcast_script( string $bg_title, string $body_text ): string|WP_Error {
        $truncated = mb_substr( $body_text, 0, 6000 );
        return $this->chat(
            'Scriptwriter for Bulgarian EV podcast Car Life by Dani. Spoken-style Bulgarian, 1-2 paragraphs, no markdown.',
            "Заглавие: {$bg_title}\n\nПълен текст:\n{$truncated}\n\nНапиши разширен подкаст скрипт.",
            [],
            'podcast'
        );
    }

    /**
     * Generate a podcast summary for the hosts to read during the live session.
     * Uses the existing title + description from the sheet — no article scraping needed.
     * Returns 3-5 Bulgarian sentences with the most interesting facts.
     */
    public function podcast_summary( string $bg_title, string $description ): string|WP_Error {
        return $this->chat(
            'Редактор за български EV подкаст Car Life by Dani. Напиши 3-5 изречения на български с най-интересните факти и детайли от статията. Без markdown, само обикновен текст.',
            "Заглавие: {$bg_title}\n\nКратко описание: {$description}\n\nНапиши резюме за водещите на подкаста.",
            [ 'temperature' => 0.5 ],
            'podcast_summary'
        );
    }

    /**
     * Fetch OpenRouter account info for the configured API key.
     * Returns the `data` payload from /api/v1/auth/key.
     */
    public function get_key_info(): array|WP_Error {
        $api_key = $this->settings->get( 'openrouter_api_key' );
        if ( empty( $api_key ) ) {
            return new WP_Error( 'openrouter_key', 'OpenRouter API key not configured' );
        }

        $response = ENA_HTTP::get( self::KEY_INFO_URL, [
            'headers' => [ 'Authorization' => "Bearer {$api_key}" ],
        ] );

        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        return $data['data'] ?? $data;
    }

    /** Read accumulated local usage stats from the WP option. */
    public static function get_local_stats(): array {
        $defaults = [
            'total_calls'                            => 0,
            'summarize_calls'                        => 0,
            'podcast_calls'                          => 0,
            'podcast_summary_calls'                  => 0,
            'total_prompt_tokens'                    => 0,
            'total_completion_tokens'                => 0,
            'total_tokens'                           => 0,
            'summarize_completion_tokens'            => 0,
            'podcast_completion_tokens'              => 0,
            'podcast_summary_completion_tokens'      => 0,
            'first_call_at'                          => null,
            'last_call_at'                           => null,
        ];
        $stored = get_option( self::USAGE_OPTION, [] );
        return array_merge( $defaults, is_array( $stored ) ? $stored : [] );
    }

    /** Wipe accumulated local usage stats. */
    public static function reset_local_stats(): void {
        delete_option( self::USAGE_OPTION );
    }

    private function chat( string $system, string $user, array $opts = [], string $type = 'general' ): string|WP_Error {
        $api_key = $this->settings->get( 'openrouter_api_key' );
        $model   = $this->settings->get( 'openrouter_model', 'anthropic/claude-opus-4-8' );

        if ( empty( $api_key ) ) {
            return new WP_Error( 'openrouter_key', 'OpenRouter API key not configured' );
        }

        $body = array_merge( [
            'model'    => $model,
            'messages' => [
                [ 'role' => 'system', 'content' => $system ],
                [ 'role' => 'user',   'content' => $user ],
            ],
        ], $opts );

        $response = ENA_HTTP::post_json( self::API_URL, $body, [
            'Authorization' => "Bearer {$api_key}",
            'HTTP-Referer'  => get_site_url(),
        ] );

        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        $content = $data['choices'][0]['message']['content'] ?? null;
        if ( $content === null ) {
            return new WP_Error( 'openrouter_empty', 'No content in response', $data );
        }

        $this->record_usage( $data['usage'] ?? [], $type );

        return $content;
    }

    private function record_usage( array $usage, string $type ): void {
        $stats = get_option( self::USAGE_OPTION, [] );
        if ( ! is_array( $stats ) ) $stats = [];

        $prompt     = (int) ( $usage['prompt_tokens']     ?? 0 );
        $completion = (int) ( $usage['completion_tokens'] ?? 0 );
        $total      = (int) ( $usage['total_tokens']      ?? ( $prompt + $completion ) );

        $stats['total_calls']             = ( $stats['total_calls']             ?? 0 ) + 1;
        $stats["{$type}_calls"]           = ( $stats["{$type}_calls"]           ?? 0 ) + 1;
        $stats['total_prompt_tokens']     = ( $stats['total_prompt_tokens']     ?? 0 ) + $prompt;
        $stats['total_completion_tokens'] = ( $stats['total_completion_tokens'] ?? 0 ) + $completion;
        $stats['total_tokens']            = ( $stats['total_tokens']            ?? 0 ) + $total;
        $stats["{$type}_completion_tokens"] = ( $stats["{$type}_completion_tokens"] ?? 0 ) + $completion;

        if ( empty( $stats['first_call_at'] ) ) {
            $stats['first_call_at'] = current_time( 'mysql' );
        }
        $stats['last_call_at'] = current_time( 'mysql' );

        update_option( self::USAGE_OPTION, $stats, false );
    }
}
