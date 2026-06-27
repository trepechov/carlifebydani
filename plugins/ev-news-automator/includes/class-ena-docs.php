<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Docs {

    private const DOCS_BASE  = 'https://docs.googleapis.com/v1/documents';
    private const DRIVE_BASE = 'https://www.googleapis.com/drive/v3/files';
    private const SCOPES     = [
        'https://www.googleapis.com/auth/documents',
        'https://www.googleapis.com/auth/drive.file',
    ];

    private ENA_Google_Auth $auth;

    public function __construct( ENA_Google_Auth $auth ) {
        $this->auth = $auth;
    }

    /**
     * Create a Google Doc. When $folder_id is provided the file is created directly
     * in that folder via the Drive Files API — the SA's personal Drive is never used,
     * avoiding storageQuotaExceeded errors on the service account.
     */
    public function create_doc( string $title, string $folder_id = '' ): string|WP_Error {
        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        if ( ! empty( $folder_id ) ) {
            $body = [
                'name'     => $title,
                'mimeType' => 'application/vnd.google-apps.document',
                'parents'  => [ $folder_id ],
            ];
            $response = ENA_HTTP::post_json( self::DRIVE_BASE, $body, [
                'Authorization' => "Bearer {$token}",
            ] );
            $data = ENA_HTTP::retrieve_json( $response );
            if ( is_wp_error( $data ) ) return $data;

            if ( empty( $data['id'] ) ) {
                return new WP_Error( 'drive_create', 'No file id returned from Drive', $data );
            }
            return $data['id'];
        }

        // Fallback: create via Docs API (file lands in SA's My Drive)
        $response = ENA_HTTP::post_json( self::DOCS_BASE, [ 'title' => $title ], [
            'Authorization' => "Bearer {$token}",
        ] );
        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        if ( empty( $data['documentId'] ) ) {
            return new WP_Error( 'docs_create', 'No documentId returned', $data );
        }
        return $data['documentId'];
    }

    public function move_to_folder( string $doc_id, string $folder_id ): bool|WP_Error {
        if ( empty( $folder_id ) ) return true;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        // Get current parents first
        $meta_url  = self::DRIVE_BASE . "/{$doc_id}?fields=parents";
        $meta_resp = ENA_HTTP::get( $meta_url, [
            'headers' => [ 'Authorization' => "Bearer {$token}" ],
        ] );
        $meta = ENA_HTTP::retrieve_json( $meta_resp );
        $remove = is_array( $meta ) && ! empty( $meta['parents'] )
            ? implode( ',', $meta['parents'] )
            : '';

        $url = self::DRIVE_BASE . "/{$doc_id}?addParents=" . rawurlencode( $folder_id )
             . ( $remove ? '&removeParents=' . rawurlencode( $remove ) : '' );

        $response = wp_remote_request( $url, [
            'method'  => 'PATCH',
            'timeout' => 30,
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'body'    => '{}',
        ] );

        if ( is_wp_error( $response ) ) return $response;
        return true;
    }

    public function append_sections( string $doc_id, array $sections ): bool|WP_Error {
        if ( empty( $sections ) ) return true;

        $token = $this->auth->get_access_token( self::SCOPES );
        if ( is_wp_error( $token ) ) return $token;

        // Fetch the document to find where existing content ends so we can
        // use absolute indices for both insertText and style requests.
        $doc_resp = ENA_HTTP::get( self::DOCS_BASE . "/{$doc_id}", [
            'headers' => [ 'Authorization' => "Bearer {$token}" ],
        ] );
        $doc = ENA_HTTP::retrieve_json( $doc_resp );
        if ( is_wp_error( $doc ) ) return $doc;

        $content = $doc['body']['content'] ?? [];
        $last    = end( $content );
        // Insert before the implicit final body newline (endIndex is exclusive).
        $cursor  = ( $last['endIndex'] ?? 2 ) - 1;

        $requests = [];

        // ── Episode header ───────────────────────────────────────────────────
        $header = 'EV News Roundup — ' . wp_date( 'F j, Y' ) . "\n";
        $hlen   = $this->utf16_len( $header );
        $requests[] = $this->req_insert( $header, $cursor );
        $requests[] = $this->req_para_style( $cursor, $cursor + $hlen, 'HEADING_1' );
        $cursor += $hlen;

        $requests[] = $this->req_insert( "\n", $cursor );
        $cursor += 1;

        foreach ( $sections as $i => $s ) {
            $num = $i + 1;

            // ── Article title (numbered, HEADING_2) ──────────────────────────
            $title = "{$num}. " . ( $s['bg_title'] ?? 'Untitled' ) . "\n";
            $tlen  = $this->utf16_len( $title );
            $requests[] = $this->req_insert( $title, $cursor );
            $requests[] = $this->req_para_style( $cursor, $cursor + $tlen, 'HEADING_2' );
            $cursor += $tlen;

            // ── Link to original article (italic + blue hyperlink) ───────────
            $link_text = "Read the original article\n";
            $llen      = $this->utf16_len( $link_text );
            $requests[] = $this->req_insert( $link_text, $cursor );
            if ( ! empty( $s['url'] ) ) {
                $requests[] = $this->req_text_style( $cursor, $cursor + $llen - 1, [
                    'link'            => [ 'url' => $s['url'] ],
                    'italic'          => true,
                    'foregroundColor' => [ 'color' => [ 'rgbColor' => [
                        'red' => 0.11, 'green' => 0.44, 'blue' => 0.73,
                    ] ] ],
                ], 'link,italic,foregroundColor' );
            }
            $cursor += $llen;

            // blank line before script
            $requests[] = $this->req_insert( "\n", $cursor );
            $cursor += 1;

            // ── Script / AI summary ──────────────────────────────────────────
            $summary = trim( $s['summary'] ?? '' ) . "\n\n";
            $slen    = $this->utf16_len( $summary );
            $requests[] = $this->req_insert( $summary, $cursor );
            $cursor += $slen;

            // ── Background description (gray italic) ─────────────────────────
            if ( ! empty( $s['description'] ) ) {
                $desc = trim( $s['description'] ) . "\n\n";
                $dlen = $this->utf16_len( $desc );
                $requests[] = $this->req_insert( $desc, $cursor );
                // Apply italic + gray to everything except the trailing \n\n
                $requests[] = $this->req_text_style( $cursor, $cursor + $dlen - 2, [
                    'italic'          => true,
                    'foregroundColor' => [ 'color' => [ 'rgbColor' => [
                        'red' => 0.4, 'green' => 0.4, 'blue' => 0.4,
                    ] ] ],
                ], 'italic,foregroundColor' );
                $cursor += $dlen;
            }

            // ── Counterpoint: "Другата гледна точка" + sources ───────────────
            $counterpoint = trim( $s['counterpoint'] ?? '' );
            if ( $counterpoint !== '' ) {
                $cp_head = "Другата гледна точка\n";
                $chlen   = $this->utf16_len( $cp_head );
                $requests[] = $this->req_insert( $cp_head, $cursor );
                $requests[] = $this->req_para_style( $cursor, $cursor + $chlen, 'HEADING_3' );
                $cursor += $chlen;

                $cp_body = $counterpoint . "\n\n";
                $cblen   = $this->utf16_len( $cp_body );
                $requests[] = $this->req_insert( $cp_body, $cursor );
                $cursor += $cblen;

                $sources = $s['sources'] ?? [];
                if ( ! empty( $sources ) ) {
                    $lbl    = "Източници:\n";
                    $lbllen = $this->utf16_len( $lbl );
                    $requests[] = $this->req_insert( $lbl, $cursor );
                    $requests[] = $this->req_text_style( $cursor, $cursor + $lbllen - 1, [
                        'bold' => true,
                    ], 'bold' );
                    $cursor += $lbllen;

                    $bullet     = '• ';
                    $bullet_len = $this->utf16_len( $bullet );
                    foreach ( $sources as $src ) {
                        $label = trim( $src['title'] ?? '' ) ?: ( $src['url'] ?? '' );
                        $line  = $bullet . $label . "\n";
                        $linelen = $this->utf16_len( $line );
                        $requests[] = $this->req_insert( $line, $cursor );
                        if ( ! empty( $src['url'] ) ) {
                            // Hyperlink the label only (skip the bullet prefix and trailing newline).
                            $requests[] = $this->req_text_style(
                                $cursor + $bullet_len,
                                $cursor + $linelen - 1,
                                [
                                    'link'            => [ 'url' => $src['url'] ],
                                    'italic'          => true,
                                    'foregroundColor' => [ 'color' => [ 'rgbColor' => [
                                        'red' => 0.11, 'green' => 0.44, 'blue' => 0.73,
                                    ] ] ],
                                ],
                                'link,italic,foregroundColor'
                            );
                        }
                        $cursor += $linelen;
                    }

                    // blank line after the source list
                    $requests[] = $this->req_insert( "\n", $cursor );
                    $cursor += 1;
                }
            }

            // ── Separator ────────────────────────────────────────────────────
            $sep     = str_repeat( '─', 48 ) . "\n\n";
            $seplen  = $this->utf16_len( $sep );
            $requests[] = $this->req_insert( $sep, $cursor );
            $requests[] = $this->req_text_style( $cursor, $cursor + $seplen - 2, [
                'foregroundColor' => [ 'color' => [ 'rgbColor' => [
                    'red' => 0.75, 'green' => 0.75, 'blue' => 0.75,
                ] ] ],
            ], 'foregroundColor' );
            $cursor += $seplen;
        }

        $url      = self::DOCS_BASE . "/{$doc_id}:batchUpdate";
        $response = ENA_HTTP::post_json( $url, [ 'requests' => $requests ], [
            'Authorization' => "Bearer {$token}",
        ] );
        $data = ENA_HTTP::retrieve_json( $response );
        if ( is_wp_error( $data ) ) return $data;

        return true;
    }

    private function utf16_len( string $text ): int {
        return strlen( mb_convert_encoding( $text, 'UTF-16LE', 'UTF-8' ) ) / 2;
    }

    private function req_insert( string $text, int $index ): array {
        return [ 'insertText' => [ 'text' => $text, 'location' => [ 'index' => $index ] ] ];
    }

    private function req_para_style( int $start, int $end, string $style ): array {
        return [
            'updateParagraphStyle' => [
                'range'          => [ 'startIndex' => $start, 'endIndex' => $end ],
                'paragraphStyle' => [ 'namedStyleType' => $style ],
                'fields'         => 'namedStyleType',
            ],
        ];
    }

    private function req_text_style( int $start, int $end, array $style, string $fields ): array {
        return [
            'updateTextStyle' => [
                'range'     => [ 'startIndex' => $start, 'endIndex' => $end ],
                'textStyle' => $style,
                'fields'    => $fields,
            ],
        ];
    }

    public function doc_url( string $doc_id ): string {
        return "https://docs.google.com/document/d/{$doc_id}/edit";
    }
}
