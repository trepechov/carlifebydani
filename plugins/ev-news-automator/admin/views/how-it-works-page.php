<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Markdown renderer helpers (load once per request) ──────────────────────

if ( ! function_exists( 'ena_md_slug' ) ) {
    function ena_md_slug( string $text ): string {
        $s = preg_replace( '/\*\*(.+?)\*\*/', '$1', strip_tags( $text ) );
        $s = strtolower( $s );
        $s = str_replace( [ '/', '.', '\\', '—', '–', '`' ], '-', $s );
        $s = preg_replace( '/[^a-z0-9-]/', '', $s );
        return preg_replace( '/-+/', '-', trim( $s, '-' ) );
    }
}

if ( ! function_exists( 'ena_md_inline' ) ) {
    function ena_md_inline( string $text ): string {
        // Protect inline code with placeholders before other transforms
        $slots = [];
        $text  = preg_replace_callback( '/`([^`\n]+)`/', function ( $m ) use ( &$slots ) {
            $key          = "\x02" . count( $slots ) . "\x03";
            $slots[ $key ] = '<code class="ena-ic">' . esc_html( $m[1] ) . '</code>';
            return $key;
        }, $text );

        // Bold, then italic
        $text = preg_replace( '/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text );
        $text = preg_replace( '/(?<!\*)\*([^*\n]+)\*(?!\*)/', '<em>$1</em>', $text );

        // Links
        $text = preg_replace_callback( '/\[([^\]]+)\]\(([^)]+)\)/', function ( $m ) {
            $label  = esc_html( $m[1] );
            $href   = $m[2];
            $is_ext = (bool) preg_match( '/^https?:\/\//', $href );
            if ( $is_ext ) {
                return '<a href="' . esc_url( $href ) . '" target="_blank" rel="noopener">' . $label . '</a>';
            }
            return '<span class="ena-ref">' . $label . '</span>';
        }, $text );

        // Restore inline-code placeholders
        return strtr( $text, $slots );
    }
}

if ( ! function_exists( 'ena_md_to_html' ) ) {
    function ena_md_to_html( string $md ): string {
        $lines = explode( "\n", $md );
        $html  = '';

        $in_fence   = false;
        $fence_lang = '';
        $fence_buf  = [];

        $in_table = false;
        $tbl_rows = [];

        $list_type  = '';
        $list_items = [];

        $bq_lines   = [];
        $para_lines = [];

        // Flusher closures — each references outer vars by ref
        $flush_para = function () use ( &$para_lines, &$html ) {
            if ( ! $para_lines ) return;
            $out = '';
            foreach ( $para_lines as $i => $pline ) {
                if ( $i > 0 ) {
                    // Two trailing spaces = hard line break
                    $out .= ( substr( $para_lines[ $i - 1 ], -2 ) === '  ' ) ? '<br>' : ' ';
                }
                $out .= ena_md_inline( rtrim( $pline ) );
            }
            $html      .= '<p>' . $out . "</p>\n";
            $para_lines = [];
        };

        $flush_list = function () use ( &$list_items, &$list_type, &$html ) {
            if ( ! $list_items ) return;
            $tag   = $list_type;
            $html .= "<$tag class=\"ena-list\">";
            foreach ( $list_items as $item ) {
                $html .= '<li>' . ena_md_inline( $item ) . '</li>';
            }
            $html      .= "</$tag>\n";
            $list_items = [];
            $list_type  = '';
        };

        $flush_bq = function () use ( &$bq_lines, &$html ) {
            if ( ! $bq_lines ) return;
            $html    .= '<blockquote class="ena-bq">' . ena_md_inline( implode( ' ', $bq_lines ) ) . "</blockquote>\n";
            $bq_lines = [];
        };

        $flush_table = function () use ( &$tbl_rows, &$in_table, &$html ) {
            if ( ! $tbl_rows ) return;
            $html .= '<div class="ena-table-wrap"><table class="widefat ena-doc-table"><thead><tr>';
            foreach ( ( $tbl_rows[0] ?? [] ) as $cell ) {
                $html .= '<th>' . ena_md_inline( trim( $cell ) ) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ( array_slice( $tbl_rows, 2 ) as $row ) {
                if ( $row === null ) continue;
                $html .= '<tr>';
                foreach ( $row as $cell ) {
                    $html .= '<td>' . ena_md_inline( trim( $cell ) ) . '</td>';
                }
                $html .= '</tr>';
            }
            $html    .= "</tbody></table></div>\n";
            $tbl_rows = [];
            $in_table = false;
        };

        foreach ( $lines as $line ) {

            // ── Fenced code blocks ──────────────────────────────────────────
            if ( preg_match( '/^```(\w*)$/', $line, $m ) ) {
                if ( ! $in_fence ) {
                    $flush_para(); $flush_list(); $flush_bq();
                    $in_fence   = true;
                    $fence_lang = $m[1];
                    $fence_buf  = [];
                } else {
                    if ( $fence_lang === 'mermaid' ) {
                        // Render as Mermaid diagram
                        $html .= '<div class="mermaid ena-mermaid">'
                            . esc_html( implode( "\n", $fence_buf ) )
                            . "</div>\n";
                    } else {
                        $lang_cls = $fence_lang ? ' class="language-' . esc_attr( $fence_lang ) . '"' : '';
                        $html    .= '<pre class="ena-pre"><code' . $lang_cls . '>'
                            . esc_html( implode( "\n", $fence_buf ) )
                            . "</code></pre>\n";
                    }
                    $in_fence   = false;
                    $fence_lang = '';
                    $fence_buf  = [];
                }
                continue;
            }
            if ( $in_fence ) { $fence_buf[] = $line; continue; }

            // ── Tables ──────────────────────────────────────────────────────
            if ( preg_match( '/^\|/', $line ) ) {
                $flush_para(); $flush_list(); $flush_bq();
                $in_table = true;
                $cells    = array_map( 'trim', explode( '|', trim( $line, '| ' ) ) );
                $is_sep   = (bool) array_reduce( $cells, fn( $ok, $c ) => $ok && preg_match( '/^[-: ]+$/', $c ), true );
                $tbl_rows[] = $is_sep ? null : $cells;
                continue;
            }
            if ( $in_table ) { $flush_table(); }

            // ── ATX headers ─────────────────────────────────────────────────
            if ( preg_match( '/^(#{1,4}) (.+)$/', $line, $m ) ) {
                $flush_para(); $flush_list(); $flush_bq();
                $lvl  = min( strlen( $m[1] ), 4 );
                $text = $m[2];
                $id   = ena_md_slug( $text );
                $html .= "<h$lvl id=\"$id\">" . ena_md_inline( $text ) . "</h$lvl>\n";
                continue;
            }

            // ── Horizontal rules ────────────────────────────────────────────
            if ( preg_match( '/^-{3,}$/', trim( $line ) ) ) {
                $flush_para(); $flush_list(); $flush_bq();
                $html .= "<hr class=\"ena-hr\">\n";
                continue;
            }

            // ── Blockquotes ─────────────────────────────────────────────────
            if ( preg_match( '/^> ?(.*)$/', $line, $m ) ) {
                $flush_para(); $flush_list();
                $bq_lines[] = $m[1];
                continue;
            }
            if ( $bq_lines ) { $flush_bq(); }

            // ── Unordered list ───────────────────────────────────────────────
            if ( preg_match( '/^[-*] (.+)$/', $line, $m ) ) {
                $flush_para(); $flush_bq();
                if ( $list_type !== 'ul' ) { $flush_list(); $list_type = 'ul'; }
                $list_items[] = $m[1];
                continue;
            }

            // ── Ordered list ─────────────────────────────────────────────────
            if ( preg_match( '/^\d+\. (.+)$/', $line, $m ) ) {
                $flush_para(); $flush_bq();
                if ( $list_type !== 'ol' ) { $flush_list(); $list_type = 'ol'; }
                $list_items[] = $m[1];
                continue;
            }

            // ── Blank line ────────────────────────────────────────────────────
            if ( trim( $line ) === '' ) {
                $flush_para(); $flush_list(); $flush_bq();
                continue;
            }

            // ── Paragraph text ────────────────────────────────────────────────
            $flush_list(); $flush_bq();
            $para_lines[] = $line;
        }

        // EOF flushes
        $flush_para(); $flush_list(); $flush_bq();
        if ( $in_table ) { $flush_table(); }

        return $html;
    }
}

// ── Read plan file ──────────────────────────────────────────────────────────

$plan_file = ENA_PLUGIN_DIR . 'docs/EPISODE_WORKFLOW.md';

if ( ! file_exists( $plan_file ) ) {
    echo '<div class="wrap"><div class="notice notice-error inline"><p>'
        . 'Plan file not found: <code>' . esc_html( $plan_file ) . '</code>'
        . '</p></div></div>';
    return;
}

$md = file_get_contents( $plan_file );
if ( $md === false ) {
    echo '<div class="wrap"><div class="notice notice-error inline"><p>Could not read plan file.</p></div></div>';
    return;
}

// ── Build TOC (h1 + h2 only to keep sidebar lean) ─────────────────────────

$toc = [];
foreach ( explode( "\n", $md ) as $ln ) {
    if ( preg_match( '/^(#{1,2}) (.+)$/', $ln, $m ) ) {
        $level     = strlen( $m[1] );
        $raw_text  = $m[2];
        $clean     = preg_replace( '/\*\*(.+?)\*\*/', '$1', strip_tags( $raw_text ) );
        $clean     = preg_replace( '/`([^`]+)`/', '$1', $clean );
        $toc[]     = [ 'level' => $level, 'text' => $clean, 'id' => ena_md_slug( $raw_text ) ];
    }
}

// ── Render ──────────────────────────────────────────────────────────────────

$content_html = ena_md_to_html( $md );

?>
<div class="wrap" id="ena-how-it-works">
    <h1 class="wp-heading-inline">Работен процес на епизода</h1>
    <span class="title-count theme-count">EV News Automator</span>

    <!-- ── Phase overview bar ───────────────────────────────────────────── -->
    <div class="ena-phase-bar">

        <div class="ena-phase ena-phase--auto">
            <div class="ena-phase__icon">🤖</div>
            <div class="ena-phase__body">
                <strong>Ср → Вт 09:00</strong>
                <span>Автоматично · всеки ден</span>
                <ul>
                    <li>GA4 синхрон</li>
                    <li>Сортиране по ангажираност</li>
                    <li>Обхождане RSS/HTML</li>
                    <li>AI резюме на БГ</li>
                    <li>Добавяне в Sheet</li>
                </ul>
            </div>
        </div>

        <div class="ena-phase__arrow" aria-hidden="true">›</div>

        <div class="ena-phase ena-phase--manual">
            <div class="ena-phase__icon">📝</div>
            <div class="ena-phase__body">
                <strong>Вторник — преди запис</strong>
                <span>Ръчно · ~15 мин</span>
                <ul>
                    <li>Нов Google Doc</li>
                    <li>ID на Doc в Настройки</li>
                    <li>Генерирай подкаст скрипт</li>
                </ul>
            </div>
        </div>

        <div class="ena-phase__arrow" aria-hidden="true">›</div>

        <div class="ena-phase ena-phase--record">
            <div class="ena-phase__icon">🎙️</div>
            <div class="ena-phase__body">
                <strong>Вторник вечер</strong>
                <span>Запис</span>
                <ul>
                    <li>Водещите четат Google Doc</li>
                    <li>Записване на подкаст</li>
                </ul>
            </div>
        </div>

        <div class="ena-phase__arrow" aria-hidden="true">›</div>

        <div class="ena-phase ena-phase--after">
            <div class="ena-phase__icon">🚀</div>
            <div class="ena-phase__body">
                <strong>Вторник — след запис</strong>
                <span>Ръчно · ~10 мин</span>
                <ul>
                    <li>Публикувай епизода</li>
                    <li>Нов таб ДД.ММ.ГГГГ</li>
                    <li>Нова WP страница</li>
                    <li>news_csv мета</li>
                    <li>Стартирай колекция</li>
                </ul>
            </div>
        </div>

        <div class="ena-phase__loop" aria-hidden="true">↺</div>

    </div><!-- .ena-phase-bar -->

    <div class="ena-doc-layout">

        <!-- ── Sidebar TOC ──────────────────────────────────────────────── -->
        <nav class="ena-doc-nav" aria-label="Table of contents">
            <strong class="ena-toc-heading">Contents</strong>
            <ul>
                <?php foreach ( $toc as $item ) :
                    $is_sub = $item['level'] === 2;
                ?>
                <li class="<?php echo $is_sub ? 'ena-toc-sub' : 'ena-toc-top'; ?>">
                    <a href="#<?php echo esc_attr( $item['id'] ); ?>">
                        <?php echo esc_html( $item['text'] ); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- ── Main content ─────────────────────────────────────────────── -->
        <article class="ena-doc-content">
            <?php echo $content_html; // Escaped internally via esc_html / esc_url ?>
        </article>

    </div><!-- .ena-doc-layout -->
</div><!-- #ena-how-it-works -->

<script type="module">
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';
    mermaid.initialize({ startOnLoad: true, theme: 'default', securityLevel: 'loose' });
</script>
