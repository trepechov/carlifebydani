<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_OpenRouter {

    private const API_URL           = 'https://openrouter.ai/api/v1/chat/completions';
    private const KEY_INFO_URL      = 'https://openrouter.ai/api/v1/auth/key';
    private const USAGE_OPTION      = 'ena_openrouter_usage';
    private const DAILY_REQ_OPTION  = 'ena_openrouter_daily_requests';
    private const RATE_LIMIT_OPTION = 'ena_openrouter_rate_limit';

    // Error codes where retrying the next item in a batch just re-hits the same wall —
    // callers looping over multiple OpenRouter calls should stop instead of continuing.
    private const FATAL_BATCH_ERROR_CODES = [ 'http_429', 'http_401' ];

    /** True when a WP_Error from chat()/summarize()/podcast_summary() means "stop the batch now". */
    public static function is_fatal_batch_error( WP_Error $error ): bool {
        return in_array( $error->get_error_code(), self::FATAL_BATCH_ERROR_CODES, true );
    }

    /** Human-readable reason for a fatal batch-stopping error, for logging. */
    public static function fatal_batch_reason( WP_Error $error ): string {
        return $error->get_error_code() === 'http_429'
            ? 'rate limited (429), stopping early instead of hitting the same limit repeatedly'
            : 'unauthorized (401) — API key looks invalid/expired, stopping early instead of repeating the same failure';
    }

    private ENA_Settings $settings;
    private ENA_Logger   $logger;

    public function __construct( ENA_Settings $settings, ENA_Logger $logger ) {
        $this->settings = $settings;
        $this->logger   = $logger;
    }

    /**
     * Single reusable analysis pass over an article: produces the Bulgarian title + summary AND the
     * on_topic / tags / region signals in ONE OpenRouter call (see docs/OFF_TOPIC_TAGS_PLAN.md).
     * Both the automatic collector (ENA_Collector::run) and the manual-add path
     * (ENA_Collector::add_manual) go through here so all rows carry the same signals.
     *
     * Returns:
     *   bg_title     string  — concise Bulgarian headline
     *   bg_summary   string  — 2-3 Bulgarian sentences
     *   on_topic     bool    — EV/EV-industry related (dictionary-steered LLM judgment)
     *   topic_reason string  — short model explanation of the on_topic call (for tuning), or ''
     *   tags         array   — Bulgarian tags: brand + model (proper nouns) + event descriptors
     *   region       string  — ISO 3166-1 alpha-2 region the article is about ("US", "CN,EU"), or ''
     */
    public function analyze( string $original_title, string $excerpt_or_body ): array|WP_Error {
        $result = $this->chat(
            $this->build_analysis_system_prompt(),
            "Original title: {$original_title}\n\nArticle excerpt: {$excerpt_or_body}\n\nProduce JSON.",
            [ 'temperature' => 0.4 ],
            'summarize' // keep the usage-stat bucket name stable across the rename
        );

        if ( is_wp_error( $result ) ) return $result;

        $parsed = json_decode( self::strip_json_fences( $result ), true );
        if ( json_last_error() !== JSON_ERROR_NONE || empty( $parsed['title'] ) ) {
            return new WP_Error( 'openrouter_parse', 'Invalid or empty JSON response from OpenRouter' );
        }

        return [
            'bg_title'     => $parsed['title'],
            'bg_summary'   => $parsed['summary'] ?? '',
            'on_topic'     => self::parse_bool( $parsed['on_topic'] ?? true ),
            'topic_reason' => is_scalar( $parsed['topic_reason'] ?? '' ) ? trim( (string) ( $parsed['topic_reason'] ?? '' ) ) : '',
            'tags'         => self::normalize_tags( $parsed['tags'] ?? [] ),
            'region'       => self::normalize_region( $parsed['region'] ?? '' ),
        ];
    }

    /**
     * Build the analyze() system prompt, folding in the editable off-topic whitelist/blacklist and
     * the Bulgarian event-descriptor vocabulary from settings. The dictionaries are injected as
     * guidance ("strongly on/off topic"), not a literal keyword filter — the judgment stays semantic.
     */
    private function build_analysis_system_prompt(): string {
        $whitelist   = self::flatten_list( (string) $this->settings->get( 'offtopic_whitelist', '' ) );
        $blacklist   = self::flatten_list( (string) $this->settings->get( 'offtopic_blacklist', '' ) );
        $descriptors = self::flatten_list( (string) $this->settings->get( 'tag_descriptors', '' ) );

        $lines = [
            'You are a Bulgarian automotive/EV news editor.',
            'Reply ONLY with a single JSON object — no markdown, no code fences:',
            '{"title":"...","summary":"...","on_topic":true,"topic_reason":"...","tags":["..."],"region":"..."}',
            '',
            'Fields:',
            '- title: concise Bulgarian headline.',
            '- summary: 2-3 Bulgarian sentences.',
            '- on_topic: true if the article relates to electric vehicles or the EV industry in ANY way. This explicitly includes news about a specific EV brand or model and its business — deliveries, production, sales, recalls, incidents/accidents, pricing, launches, and company or market news — NOT only articles about EV technology itself. A story about an EV maker (e.g. Rivian, Tesla, BYD) or an EV model is on-topic even when it is about a delivery, customer, legal or business dispute rather than the technology. Mark false ONLY when the article is not about EVs at all — e.g. purely internal-combustion (petrol/diesel) vehicles, or an unrelated subject. When unsure, prefer true. Use judgment, not just keyword matching.',
            '- topic_reason: one short phrase (Bulgarian or English) explaining the on_topic decision, for auditing.',
            '- tags: array of short tags IN BULGARIAN. Include the car brand and model as their proper-noun spelling when present (e.g. "Tesla", "Model Y" — do not transliterate), plus zero or more Bulgarian event descriptors.',
            '- region: ISO 3166-1 alpha-2 code of the region the article is ABOUT (e.g. "US", "CN", "DE"), or "EU"/"GLOBAL". Use "" if not region-specific. Comma-separate if several (e.g. "US,EU").',
        ];

        if ( $descriptors !== '' ) {
            $lines[] = '';
            $lines[] = "Prefer these Bulgarian event descriptors for tags when they apply: {$descriptors}.";
        }
        if ( $whitelist !== '' ) {
            $lines[] = '';
            $lines[] = "Treat these topics as strongly ON-topic: {$whitelist}.";
        }
        if ( $blacklist !== '' ) {
            $lines[] = "Treat these topics as strongly OFF-topic: {$blacklist}.";
        }

        return implode( "\n", $lines );
    }

    /** Collapse a newline/comma-separated settings list into a single comma-joined line for the prompt. */
    private static function flatten_list( string $raw ): string {
        $parts = preg_split( '/[\r\n,]+/', $raw );
        $parts = array_filter( array_map( 'trim', $parts ), fn ( $p ) => $p !== '' );
        return implode( ', ', $parts );
    }

    /** Strip a ```json … ``` (or bare ```) fence some models wrap JSON in, so json_decode succeeds. */
    private static function strip_json_fences( string $raw ): string {
        $trimmed = trim( $raw );
        if ( ! str_starts_with( $trimmed, '```' ) ) return $trimmed;
        $trimmed = preg_replace( '/^```[a-zA-Z]*\s*/', '', $trimmed );
        $trimmed = preg_replace( '/\s*```$/', '', $trimmed );
        return trim( (string) $trimmed );
    }

    /** Coerce the model's on_topic value (bool / "true"/"false" / 1/0) to a real bool; default true. */
    private static function parse_bool( $value ): bool {
        if ( is_bool( $value ) ) return $value;
        if ( is_int( $value ) ) return $value !== 0;
        if ( is_string( $value ) ) return ! in_array( strtolower( trim( $value ) ), [ 'false', '0', 'no', '' ], true );
        return true;
    }

    /** Trim/dedupe tag strings, drop empties, cap the count so a runaway response can't bloat a cell. */
    private static function normalize_tags( $tags ): array {
        if ( ! is_array( $tags ) ) return [];
        $clean = [];
        foreach ( $tags as $tag ) {
            if ( ! is_scalar( $tag ) ) continue;
            $tag = trim( (string) $tag );
            if ( $tag !== '' && ! in_array( $tag, $clean, true ) ) $clean[] = $tag;
        }
        return array_slice( $clean, 0, 8 );
    }

    /** Normalize region to uppercase alpha-2 code(s), comma-joined; '' when not region-specific. */
    private static function normalize_region( $region ): string {
        if ( ! is_scalar( $region ) ) return '';
        $parts = preg_split( '/[\s,]+/', strtoupper( trim( (string) $region ) ) );
        $codes = [];
        foreach ( $parts as $p ) {
            if ( preg_match( '/^[A-Z]{2,6}$/', $p ) && ! in_array( $p, $codes, true ) ) $codes[] = $p;
        }
        return implode( ',', array_slice( $codes, 0, 3 ) );
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
     * Generate an extended summary for the hosts to read during the live session —
     * longer than the sheet's short description, so it adds material rather than
     * just restating it. Uses the existing title + description from the sheet —
     * no article scraping needed.
     */
    public function podcast_summary( string $bg_title, string $description ): string|WP_Error {
        return $this->chat(
            'Пишеш разширено фактическо резюме на новинарска статия на български, което да разгъне и допълни кратко описание с повече детайли и контекст. Съдържай само фактите от статията — без упоменаване на подкаст, водещи, слушатели или епизоди. Без markdown.',
            "Заглавие: {$bg_title}\n\nКратко описание: {$description}\n\nНапиши разширено резюме от 8-10 изречения с повече детайли и контекст, без да повтаряш описанието дословно.",
            [ 'temperature' => 0.3 ],
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

    /**
     * Requests made by this plugin today (UTC), regardless of outcome — counts against the
     * OpenRouter request-based rate limits, unlike get_local_stats() which only counts successes.
     * Not authoritative if the same API key is used elsewhere.
     */
    public static function get_daily_request_count(): array {
        $today  = gmdate( 'Y-m-d' );
        $stored = get_option( self::DAILY_REQ_OPTION, [] );
        if ( ! is_array( $stored ) || ( $stored['date'] ?? '' ) !== $today ) {
            return [ 'date' => $today, 'count' => 0 ];
        }
        return $stored;
    }

    /** Most recent X-RateLimit-* snapshot observed on a 429 response, or null if none yet. */
    public static function get_last_rate_limit(): ?array {
        $stored = get_option( self::RATE_LIMIT_OPTION, null );
        return is_array( $stored ) ? $stored : null;
    }

    private static function bump_daily_request_count(): void {
        $count = self::get_daily_request_count();
        $count['count']++;
        update_option( self::DAILY_REQ_OPTION, $count, false );
    }

    /** Persist the X-RateLimit-* headers from a 429 so the dashboard can show it without waiting for another 429. */
    private static function record_rate_limit_snapshot( array $error_data ): void {
        $remaining = $error_data['rate_limit_remaining'] ?? null;
        $limit     = $error_data['rate_limit_limit'] ?? null;
        if ( $remaining === null && $limit === null ) return;

        $reset = $error_data['rate_limit_reset'] ?? null;
        $reset_secs = null;
        if ( is_numeric( $reset ) ) {
            $reset_secs = $reset > 10_000_000_000 ? intdiv( (int) $reset, 1000 ) : (int) $reset; // ms vs s epoch
        }

        update_option( self::RATE_LIMIT_OPTION, [
            'remaining'    => $remaining,
            'limit'        => $limit,
            'reset_at_utc' => $reset_secs ? gmdate( 'Y-m-d H:i:s', $reset_secs ) : null,
            'observed_at'  => current_time( 'mysql' ),
        ], false );
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

        $headers = [
            'Authorization' => "Bearer {$api_key}",
            'HTTP-Referer'  => get_site_url(),
        ];

        $response = ENA_HTTP::post_json( self::API_URL, $body, $headers );
        self::bump_daily_request_count();
        $data     = ENA_HTTP::retrieve_json( $response );

        if ( is_wp_error( $data ) ) {
            // No retry on 429: retrying (and continuing to the next article) just re-hits the
            // same wall repeatedly, wasting minutes on a run that's already going to fail.
            // Fail fast instead — the caller stops the batch and the next trigger picks it up.
            if ( $data->get_error_code() === 'http_429' ) self::record_rate_limit_snapshot( $data->get_error_data() );
            return self::with_upstream_message( $data );
        }

        $content = $data['choices'][0]['message']['content'] ?? null;
        if ( $content === null ) {
            return new WP_Error( 'openrouter_empty', 'No content in response', $data );
        }

        $usage = $data['usage'] ?? [];
        $this->record_usage( $usage, $type );
        $this->log_usage( $type, $usage );

        return $content;
    }

    /**
     * Re-wrap a WP_Error from ENA_HTTP with OpenRouter's own error message (if present in the
     * response body), so logs show the actual upstream reason instead of a generic HTTP status.
     */
    private static function with_upstream_message( WP_Error $error ): WP_Error {
        $body = $error->get_error_data()['body'] ?? null;
        if ( ! is_string( $body ) ) return $error;

        $parsed = json_decode( $body, true );
        $upstream_message = $parsed['error']['message'] ?? null;
        $message = $error->get_error_message();
        if ( ! empty( $upstream_message ) ) $message .= ' — ' . $upstream_message;
        $message .= self::rate_limit_suffix( $error->get_error_data() );

        if ( $message === $error->get_error_message() ) return $error;

        return new WP_Error( $error->get_error_code(), $message, $error->get_error_data() );
    }

    /**
     * Format the X-RateLimit-* headers OpenRouter attaches to 429 responses into a log suffix,
     * e.g. " [quota: 0/20 remaining, resets 22:05:00]". These headers are only sent on failures —
     * OpenRouter does not expose remaining free-tier request quota on successful calls.
     */
    private static function rate_limit_suffix( array $error_data ): string {
        $remaining = $error_data['rate_limit_remaining'] ?? null;
        $limit     = $error_data['rate_limit_limit'] ?? null;
        $reset     = $error_data['rate_limit_reset'] ?? null;
        if ( $remaining === null && $limit === null ) return '';

        $quota = trim( ( $remaining ?? '?' ) . '/' . ( $limit ?? '?' ) );
        $reset_str = '';
        if ( is_numeric( $reset ) ) {
            $reset_secs = $reset > 10_000_000_000 ? intdiv( (int) $reset, 1000 ) : (int) $reset; // ms vs s epoch
            $reset_str  = ', resets ' . gmdate( 'H:i:s', $reset_secs ) . ' UTC';
        }

        return " [quota: {$quota} remaining{$reset_str}]";
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

    /** Record per-request token usage as a transcript step, so token burn is visible next to the call it came from. */
    private function log_usage( string $type, array $usage ): void {
        $prompt     = (int) ( $usage['prompt_tokens']     ?? 0 );
        $completion = (int) ( $usage['completion_tokens'] ?? 0 );
        $total      = (int) ( $usage['total_tokens']      ?? ( $prompt + $completion ) );
        $session    = (int) ( get_option( self::USAGE_OPTION, [] )['total_tokens'] ?? $total );

        $this->logger->step(
            'openrouter_usage',
            'ok',
            "{$type}: {$total} tokens ({$prompt} prompt + {$completion} completion) — session total: {$session} tokens"
        );
    }
}
