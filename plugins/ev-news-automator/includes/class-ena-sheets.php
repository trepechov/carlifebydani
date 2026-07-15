<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// STORAGE ADAPTER for Google Sheets v4 REST API.
//
// Spreadsheet structure (actual):
//   One Google Spreadsheet with multiple sheets (tabs), one per podcast session.
//   Tab names use DD.MM.YYYY format (e.g. "16.06.2026").
//   Columns per tab: title | description | link | author | upvote | downvote | clicks | added_date
//   upvote (col E) / downvote (col F) — real GA4-synced vote counts; written as 0 on append,
//   updated by ENA_Analytics from the ev_news_upvote / ev_news_downvote GA4 events.
//   clicks (col G) — GA4 click count; written as 0 on append, updated daily by ENA_Analytics.
//   added_date (col H) — Y-m-d date the row was appended; written by the adapter, never changed.
//   The session date lives in the tab name, not a column.
//   "Active sheet" = the tab whose name is the most recent valid DD.MM.YYYY date.
//
// Backward compat: tabs created before the count/added_date columns are handled by
// read_data_rows() — missing upvote/downvote/clicks treated as 0, missing added_date=session_date.
//
// Interface contract (all callers use these):
//   read_data_rows(), append_rows(), delete_rows(), update_clicks(), existing_urls(), row_count()
//
// Additional session management:
//   list_sheets(), active_sheet_name(), active_sheet_url()
class ENA_Sheets {

    private const BASE                 = 'https://sheets.googleapis.com/v4/spreadsheets';
    private const SCOPES               = [ 'https://www.googleapis.com/auth/spreadsheets' ];
    private const COLUMNS              = [ 'title', 'description', 'link', 'author', 'upvote', 'downvote', 'clicks', 'added_date', 'pub_date' ];
    private const SESSION_DATE_PATTERN = '/^\d{2}\.\d{2}\.\d{4}$/';

    private ENA_Google_Auth $auth;
    private ENA_Settings    $settings;

    public function __construct( ENA_Google_Auth $auth, ENA_Settings $settings ) {
        $this->auth     = $auth;
        $this->settings = $settings;
    }

    // ── Public interface contract ────────────────────────────────────────────

    /**
     * Read data rows from the active session sheet.
     * Returns assoc arrays with keys: title, description, link, author, upvote, downvote,
     * clicks, added_date, session_date.
     * session_date is parsed from the tab name (DD.MM.YYYY → Y-m-d), not a real column.
     * upvote/downvote/clicks are real GA4-synced vote counts, cast to int.
     * Backward compat: missing upvote/downvote/clicks → 0; missing col H → added_date=session_date.
     */
    public function read_data_rows(): array|WP_Error {
        $sheet = $this->active_sheet_name();
        if ( is_wp_error( $sheet ) ) return $sheet;

        $all = $this->read_sheet_rows( $sheet );
        if ( is_wp_error( $all ) ) return $all;

        $date = $this->parse_session_date( $sheet );
        $rows = array_slice( $all, 1 ); // skip header row

        return array_map( function ( $row ) use ( $date ) {
            $padded = array_pad( $row, 9, '' );
            $assoc  = array_combine( self::COLUMNS, array_slice( $padded, 0, 9 ) );
            // Backward compat defaults for old tabs (A:F only)
            if ( (string) $assoc['upvote'] === '' )   $assoc['upvote'] = 0;
            if ( (string) $assoc['downvote'] === '' ) $assoc['downvote'] = 0;
            if ( (string) $assoc['clicks'] === '' )   $assoc['clicks'] = 0;
            if ( (string) $assoc['added_date'] === '' ) $assoc['added_date'] = $date;
            $assoc['upvote']       = (int) $assoc['upvote'];
            $assoc['downvote']     = (int) $assoc['downvote'];
            $assoc['clicks']       = (int) $assoc['clicks'];
            $assoc['session_date'] = $date;
            return $assoc;
        }, $rows );
    }

    /**
     * Returns [link => true] for deduplication within the active session sheet.
     */
    public function existing_urls(): array {
        $sheet = $this->active_sheet_name();
        if ( is_wp_error( $sheet ) ) return [];

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return [];

        $id    = $this->settings->get( 'spreadsheet_id' );
        $range = rawurlencode( "{$sheet}!C:C" ); // column C = link
        $url   = self::BASE . "/{$id}/values/{$range}";

        $response = ENA_HTTP::get( $url, [ 'headers' => [ 'Authorization' => "Bearer {$token}" ] ] );
        $data     = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return [];

        $map = [];
        foreach ( $data['values'] ?? [] as $i => $cell ) {
            if ( $i === 0 ) continue; // skip header
            $link = $cell[0] ?? '';
            if ( ! empty( $link ) ) $map[ $link ] = true;
        }
        return $map;
    }

    /**
     * Append rows to the active session sheet.
     * Each row must be an assoc array with keys matching COLUMNS (except session_date and added_date).
     * The adapter writes today's date into added_date automatically; clicks defaults to 0.
     */
    public function append_rows( array $rows ): bool|WP_Error {
        if ( empty( $rows ) ) return true;

        $sheet = $this->active_sheet_name();
        if ( is_wp_error( $sheet ) ) return $sheet;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $id    = $this->settings->get( 'spreadsheet_id' );
        $range = rawurlencode( "{$sheet}!A:I" );
        $url   = self::BASE . "/{$id}/values/{$range}:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS";

        $today  = gmdate( 'Y-m-d' );
        $values = array_map(
            function ( $row ) use ( $today ) {
                return array_map( function ( $k ) use ( $row, $today ) {
                    if ( $k === 'added_date' ) return $row['added_date'] ?? $today;
                    if ( $k === 'clicks' )     return $row['clicks'] ?? 0;
                    return $row[ $k ] ?? '';
                }, self::COLUMNS );
            },
            $rows
        );

        $response = ENA_HTTP::post_json( $url, [ 'values' => $values ], [
            'Authorization' => "Bearer {$token}",
        ] );
        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        return true;
    }

    /**
     * Delete rows by 0-based data-row index from the active session sheet.
     * Sorted descending internally to avoid index shifting.
     */
    public function delete_rows( array $row_indices ): bool|WP_Error {
        if ( empty( $row_indices ) ) return true;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $sheet_id = $this->active_sheet_id_numeric();
        if ( is_wp_error( $sheet_id ) ) return $sheet_id;

        rsort( $row_indices );

        $requests = array_map( function ( $idx ) use ( $sheet_id ) {
            $sheet_row = $idx + 1; // +1 for header offset
            return [
                'deleteDimension' => [
                    'range' => [
                        'sheetId'    => $sheet_id,
                        'dimension'  => 'ROWS',
                        'startIndex' => $sheet_row,
                        'endIndex'   => $sheet_row + 1,
                    ],
                ],
            ];
        }, $row_indices );

        $id  = $this->settings->get( 'spreadsheet_id' );
        $url = self::BASE . "/{$id}:batchUpdate";

        $response = ENA_HTTP::post_json( $url, [ 'requests' => $requests ], [
            'Authorization' => "Bearer {$token}",
        ] );
        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        return true;
    }

    /**
     * Update column G (clicks) for every row whose link URL matches the given map.
     * Rows whose URL is not in $url_to_clicks are left unchanged.
     */
    public function update_clicks( array $url_to_clicks ): bool|WP_Error {
        return $this->update_column( 'G', $url_to_clicks );
    }

    /**
     * Update column E (upvote) for every row whose link URL matches the given map.
     * Rows whose URL is not in $url_to_count are left unchanged.
     */
    public function update_upvotes( array $url_to_count ): bool|WP_Error {
        return $this->update_column( 'E', $url_to_count );
    }

    /**
     * Update column F (downvote) for every row whose link URL matches the given map.
     * Rows whose URL is not in $url_to_count are left unchanged.
     */
    public function update_downvotes( array $url_to_count ): bool|WP_Error {
        return $this->update_column( 'F', $url_to_count );
    }

    /**
     * Write an integer count into $column_letter for every row whose link (column C)
     * matches a key in $url_to_count. Rows not in the map are left unchanged.
     */
    private function update_column( string $column_letter, array $url_to_count ): bool|WP_Error {
        if ( empty( $url_to_count ) ) return true;

        $sheet = $this->active_sheet_name();
        if ( is_wp_error( $sheet ) ) return $sheet;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $id = $this->settings->get( 'spreadsheet_id' );

        // Read link column to find which sheet rows need updating
        $range    = rawurlencode( "{$sheet}!C:C" );
        $url      = self::BASE . "/{$id}/values/{$range}";
        $response = ENA_HTTP::get( $url, [ 'headers' => [ 'Authorization' => "Bearer {$token}" ] ] );
        $data     = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        $update_data = [];
        foreach ( $data['values'] ?? [] as $row_index => $cell ) {
            if ( $row_index === 0 ) continue; // skip header (sheet row 1)
            $link = $cell[0] ?? '';
            if ( ! empty( $link ) && array_key_exists( $link, $url_to_count ) ) {
                $sheet_row     = $row_index + 1; // 1-based sheet row
                $update_data[] = [
                    'range'  => "{$sheet}!{$column_letter}{$sheet_row}",
                    'values' => [ [ (int) $url_to_count[ $link ] ] ],
                ];
            }
        }

        if ( empty( $update_data ) ) return true;

        $batch_url = self::BASE . "/{$id}/values:batchUpdate";
        $response  = ENA_HTTP::post_json( $batch_url, [
            'valueInputOption' => 'RAW',
            'data'             => $update_data,
        ], [ 'Authorization' => "Bearer {$token}" ] );

        $result = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $result ) ) return $result;

        return true;
    }

    /**
     * Reorder all data rows in the active session sheet by upvote (column E) descending.
     * The header row (row 1) is preserved — sort starts at row index 1.
     * Called after update_upvotes() so the spreadsheet reflects engagement order.
     */
    public function sort_by_upvotes(): bool|WP_Error {
        $sheet_id = $this->active_sheet_id_numeric();
        if ( is_wp_error( $sheet_id ) ) return $sheet_id;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $id  = $this->settings->get( 'spreadsheet_id' );
        $url = self::BASE . "/{$id}:batchUpdate";

        $response = ENA_HTTP::post_json( $url, [
            'requests' => [
                [
                    'sortRange' => [
                        'range'     => [
                            'sheetId'          => $sheet_id,
                            'startRowIndex'    => 1, // skip header row
                            'startColumnIndex' => 0,
                            'endColumnIndex'   => 9, // columns A–I
                        ],
                        'sortSpecs' => [
                            [
                                'dimensionIndex' => 4, // column E = upvote
                                'sortOrder'      => 'DESCENDING',
                            ],
                            [
                                'dimensionIndex' => 8, // column I = pub_date
                                'sortOrder'      => 'DESCENDING',
                            ],
                            [
                                'dimensionIndex' => 7, // column H = added_date
                                'sortOrder'      => 'DESCENDING',
                            ],
                        ],
                    ],
                ],
            ],
        ], [ 'Authorization' => "Bearer {$token}" ] );

        $result = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $result ) ) return $result;

        return true;
    }

    /**
     * Trim the active sheet to at most $max data rows by deleting the bottom rows.
     *
     * Must be called AFTER sort_by_upvotes(): once the sheet is sorted
     * (upvote DESC → pub_date DESC → added_date DESC), the bottom rows are the
     * zero-upvote articles with the oldest pub_date — exactly the ones that should
     * age out. Deleting by position is therefore both correct and simple.
     *
     * Returns the number of rows removed.
     */
    public function trim_to_max( int $max ): int {
        if ( $max <= 0 ) return 0; // Invalid/unset limit — never treat this as "delete everything."

        $count = $this->row_count();
        if ( $count <= $max ) return 0;

        $excess  = $count - $max;
        $indices = range( $count - $excess, $count - 1 ); // bottom $excess data rows (0-based)

        $result = $this->delete_rows( $indices );
        if ( is_wp_error( $result ) ) return 0;

        return $excess;
    }

    public function row_count(): int {
        $rows = $this->read_data_rows();
        if ( is_wp_error( $rows ) ) return 0;
        return count( $rows );
    }

    // ── Session management ───────────────────────────────────────────────────

    /**
     * List all sheet tabs in the spreadsheet.
     * Returns [['title' => 'DD.MM.YYYY', 'id' => 12345], ...]
     * Result cached in a transient for 5 minutes.
     */
    public function list_sheets(): array|WP_Error {
        $id        = $this->settings->get( 'spreadsheet_id' );
        $cache_key = 'ena_sheets_list_' . md5( (string) $id );
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) return $cached;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $url      = self::BASE . "/{$id}?fields=sheets.properties(sheetId,title)";
        $response = ENA_HTTP::get( $url, [ 'headers' => [ 'Authorization' => "Bearer {$token}" ] ] );
        $data     = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        $sheets = array_map(
            fn ( $s ) => [
                'title' => $s['properties']['title'],
                'id'    => (int) $s['properties']['sheetId'],
            ],
            $data['sheets'] ?? []
        );

        set_transient( $cache_key, $sheets, 5 * MINUTE_IN_SECONDS );
        return $sheets;
    }

    /**
     * Force list_sheets() to hit the Sheets API on its next call instead of serving
     * the 5-minute cache. Call this before resolving the active sheet for a pipeline
     * run so a newly added weekly tab is picked up immediately rather than waiting
     * out a cache warmed by an earlier page load or run.
     */
    public function flush_sheets_cache(): void {
        $id = $this->settings->get( 'spreadsheet_id' );
        delete_transient( 'ena_sheets_list_' . md5( (string) $id ) );
    }

    /**
     * Return the tab title of the most recently dated session sheet (DD.MM.YYYY format).
     */
    public function active_sheet_name(): string|WP_Error {
        $sheets = $this->list_sheets();
        if ( is_wp_error( $sheets ) ) return $sheets;

        $dated = array_filter(
            $sheets,
            fn ( $s ) => preg_match( self::SESSION_DATE_PATTERN, $s['title'] )
        );

        if ( empty( $dated ) ) {
            return new WP_Error(
                'no_session_sheet',
                'No session sheet found. Expected tabs named DD.MM.YYYY (e.g. "16.06.2026").'
            );
        }

        usort( $dated, fn ( $a, $b ) =>
            $this->parse_session_timestamp( $b['title'] ) <=> $this->parse_session_timestamp( $a['title'] )
        );

        return $dated[0]['title'];
    }

    /**
     * Returns the direct URL to the active session tab including the #gid anchor.
     * Mirrors active_sheet_name() logic — filters, sorts, then reads 'id' from the
     * same array element to avoid a secondary title-match lookup that can fail silently.
     */
    public function active_sheet_url(): string|WP_Error {
        $sheets = $this->list_sheets();
        if ( is_wp_error( $sheets ) ) return $sheets;

        $dated = array_values( array_filter(
            $sheets,
            fn ( $s ) => preg_match( self::SESSION_DATE_PATTERN, $s['title'] )
        ) );

        if ( empty( $dated ) ) {
            return new WP_Error( 'no_session_sheet', 'No session sheet found — cannot build sheet URL.' );
        }

        usort( $dated, fn ( $a, $b ) =>
            $this->parse_session_timestamp( $b['title'] ) <=> $this->parse_session_timestamp( $a['title'] )
        );

        $active = $dated[0];
        $id     = $this->settings->get( 'spreadsheet_id' );
        return "https://docs.google.com/spreadsheets/d/{$id}/edit#gid={$active['id']}";
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function read_sheet_rows( string $sheet_name ): array|WP_Error {
        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        $id    = $this->settings->get( 'spreadsheet_id' );
        $range = rawurlencode( "{$sheet_name}!A:I" );
        $url   = self::BASE . "/{$id}/values/{$range}";

        $response = ENA_HTTP::get( $url, [ 'headers' => [ 'Authorization' => "Bearer {$token}" ] ] );
        $data     = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        return $data['values'] ?? [];
    }

    private function active_sheet_id_numeric(): int|WP_Error {
        $sheet_name = $this->active_sheet_name();
        if ( is_wp_error( $sheet_name ) ) return $sheet_name;

        $sheets = $this->list_sheets();
        if ( is_wp_error( $sheets ) ) return $sheets;

        foreach ( $sheets as $sheet ) {
            if ( $sheet['title'] === $sheet_name ) {
                return $sheet['id'];
            }
        }

        return new WP_Error( 'sheet_id_missing', "Could not find numeric ID for sheet: {$sheet_name}" );
    }

    /** DD.MM.YYYY → Y-m-d (falls back to the raw name on parse failure). */
    private function parse_session_date( string $tab_name ): string {
        if ( preg_match( '/^(\d{2})\.(\d{2})\.(\d{4})$/', $tab_name, $m ) ) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        return $tab_name;
    }

    /** DD.MM.YYYY → Unix timestamp for sorting (returns 0 on parse failure). */
    private function parse_session_timestamp( string $tab_name ): int {
        $dt = DateTime::createFromFormat( 'd.m.Y', $tab_name, new DateTimeZone( 'UTC' ) );
        return $dt ? $dt->getTimestamp() : 0;
    }
}
