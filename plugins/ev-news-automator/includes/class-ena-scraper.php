<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Scraper {

    private ENA_Logger $logger;

    public function __construct( ENA_Logger $logger ) {
        $this->logger = $logger;
    }

    public function fetch_source( array $source ): array {
        $method = $source['method'] ?? 'rss';
        $url    = $source['url'];

        if ( ! ENA_HTTP::is_safe_url( $url ) ) {
            $this->logger->log_error( 'scraper', "Unsafe URL skipped: {$url}" );
            return [];
        }

        return $method === 'html'
            ? $this->fetch_html( $url, wp_parse_url( $url, PHP_URL_HOST ) )
            : $this->fetch_rss( $url );
    }

    public function fetch_rss( string $url ): array {
        $response = ENA_HTTP::get( $url );
        if ( is_wp_error( $response ) ) {
            $this->logger->log_error( 'scraper', "HTTP error fetching {$url}: " . $response->get_error_message() );
            return [];
        }

        $body = wp_remote_retrieve_body( $response );
        libxml_use_internal_errors( true );
        $dom = new DOMDocument();
        if ( ! $dom->loadXML( $body ) ) {
            $this->logger->log_error( 'scraper', "XML parse failed for {$url}" );
            return [];
        }

        $articles = [];
        $host     = wp_parse_url( $url, PHP_URL_HOST );

        // Support both RSS <item> and Atom <entry>
        $items = $dom->getElementsByTagName( 'item' );
        if ( $items->length === 0 ) {
            $items = $dom->getElementsByTagName( 'entry' );
        }

        foreach ( $items as $item ) {
            $title   = $this->first_text( $item, [ 'title' ] );
            $link    = $this->first_text( $item, [ 'link', 'id' ] );
            $excerpt = $this->first_text( $item, [ 'description', 'summary', 'content' ] );

            if ( empty( $title ) || empty( $link ) ) continue;
            if ( ! ENA_HTTP::is_safe_url( $link ) ) continue;

            $articles[] = [
                'title'        => wp_strip_all_tags( $title ),
                'url'          => $link,
                'excerpt'      => wp_strip_all_tags( substr( $excerpt, 0, 500 ) ),
                'source'       => $host,
                'published_at' => $this->parse_published_date( $item ),
            ];
        }

        return $articles;
    }

    public function fetch_html( string $url, string $base ): array {
        $response = ENA_HTTP::get( $url );
        if ( is_wp_error( $response ) ) {
            $this->logger->log_error( 'scraper', "HTTP error fetching {$url}: " . $response->get_error_message() );
            return [];
        }

        $body = wp_remote_retrieve_body( $response );
        libxml_use_internal_errors( true );
        $dom = new DOMDocument();
        $dom->loadHTML( mb_convert_encoding( $body, 'HTML-ENTITIES', 'UTF-8' ) );

        $xpath    = new DOMXPath( $dom );
        $anchors  = $xpath->query( '//h1/a | //h2/a | //h3/a | //article//a[string-length(text()) > 20]' );
        $articles = [];
        $seen     = [];

        foreach ( $anchors as $a ) {
            $title = trim( $a->textContent );
            $href  = $a->getAttribute( 'href' );

            if ( empty( $title ) || strlen( $title ) < 10 ) continue;
            if ( strpos( $href, 'http' ) !== 0 ) {
                $href = 'https://' . rtrim( $base, '/' ) . '/' . ltrim( $href, '/' );
            }
            if ( ! ENA_HTTP::is_safe_url( $href ) || isset( $seen[ $href ] ) ) continue;

            $seen[ $href ]  = true;
            $articles[] = [
                'title'        => $title,
                'url'          => $href,
                'excerpt'      => '',
                'source'       => $base,
                'published_at' => 0, // HTML sources carry no publication date
            ];
        }

        return $articles;
    }

    public function extract_body( string $url ): string|WP_Error {
        if ( ! ENA_HTTP::is_safe_url( $url ) ) {
            return new WP_Error( 'unsafe_url', 'URL failed safety check' );
        }

        $response = ENA_HTTP::get( $url );
        if ( is_wp_error( $response ) ) return $response;

        $body = wp_remote_retrieve_body( $response );
        libxml_use_internal_errors( true );
        $dom = new DOMDocument();
        $dom->loadHTML( mb_convert_encoding( $body, 'HTML-ENTITIES', 'UTF-8' ) );

        // Remove noise elements
        $remove_tags = [ 'script', 'style', 'nav', 'header', 'footer', 'aside', 'form', 'iframe' ];
        foreach ( $remove_tags as $tag ) {
            $nodes = $dom->getElementsByTagName( $tag );
            while ( $nodes->length > 0 ) {
                $nodes->item(0)->parentNode->removeChild( $nodes->item(0) );
            }
        }

        return $this->clean_text( $dom );
    }

    private function clean_text( DOMDocument $dom ): string {
        $text = $dom->textContent;
        $text = preg_replace( '/\s+/', ' ', $text );
        return trim( $text );
    }

    /** Parse pubDate / published / updated from an RSS or Atom item. Returns Unix timestamp or 0. */
    private function parse_published_date( DOMElement $item ): int {
        $raw = $this->first_text( $item, [ 'pubDate', 'published', 'updated' ] );
        if ( empty( $raw ) ) return 0;
        $ts = strtotime( $raw );
        return ( $ts !== false && $ts > 0 ) ? $ts : 0;
    }

    private function first_text( DOMElement $parent, array $tags ): string {
        foreach ( $tags as $tag ) {
            $nodes = $parent->getElementsByTagName( $tag );
            if ( $nodes->length > 0 ) {
                return $nodes->item(0)->textContent;
            }
        }
        return '';
    }
}
