<?php
/**
 * Template Name: EV News Feed
 *
 * Standalone page for browsing this week's curated EV news articles.
 * Mobile: Instagram-style vertical snap feed (70 vh cards).
 * Desktop: 2/3 + sidebar grid matching other news pages.
 *
 * Data: ev_news_live_articles wp_option written by EV News Automator plugin (ENA_Sync).
 */

$raw      = get_option( 'ev_news_live_articles', '[]' );
$articles = json_decode( $raw, true );
if ( ! is_array( $articles ) ) {
    $articles = [];
}

// Derive the podcast session label from the sync status (set by ENA_Sync on each run).
// Falls back to the first article's date if the status isn't populated yet.
$sync_status   = get_option( 'ena_status_last_sync', [] );
$sheet_name    = $sync_status['sheet_name'] ?? '';
if ( ! $sheet_name && ! empty( $articles[0]['date'] ) ) {
    $dt = DateTime::createFromFormat( 'Y-m-d', $articles[0]['date'] );
    $sheet_name = $dt ? $dt->format( 'd.m.Y' ) : '';
}
$session_label = $sheet_name ? "Подкаст — {$sheet_name}" : 'Новини за подкаста тази седмица';

get_template_part( 'template-parts/header' );
?>

<div class="bg-black min-h-screen">

    <?php /* ── PAGE HERO ────────────────────────────────────────────────── */ ?>
    <div class="relative">
        <div class="absolute h-60 w-full bg-carbon-stripe-white-20">
            <div class="h-full bg-from-black-60-gradient"></div>
        </div>

        <?php /* Mobile title */ ?>
        <div class="wrapper py-6 relative lg:hidden">
            <h1 class="title text-2xl font-bold mt-4 mb-1">EV News Feed</h1>
            <p class="text-brand-lightgrey text-sm"><?php echo esc_html( $session_label ); ?></p>
        </div>

        <?php /* Desktop title */ ?>
        <div class="hidden lg:block wrapper py-8 relative">
            <h1 class="title text-3xl/8 font-bold mt-6 mb-2">EV News Feed</h1>
            <p class="mb-8 text-brand-lightgrey"><?php echo esc_html( $session_label ); ?></p>
        </div>
    </div>

    <?php if ( empty( $articles ) ) : ?>

    <div class="wrapper py-20 text-center">
        <span class="material-symbols-outlined text-5xl text-brand-lightgrey block mb-4">newspaper</span>
        <p class="text-brand-lightgrey text-lg">Няма налични новини. Проверете по-късно.</p>
    </div>

    <?php else : ?>

    <?php /* ══ MOBILE FEED (< lg) ════════════════════════════════════════ */ ?>
    <div class="lg:hidden px-3 pb-6 flex flex-col gap-3">
        <?php foreach ( $articles as $index => $article ) :
            get_template_part( 'template-parts/ev-news-feed/card', null, [ 'article' => $article, 'index' => $index + 1 ] );
        endforeach; ?>
    </div>

    <?php /* ══ DESKTOP LAYOUT (lg+) ════════════════════════════════════ */ ?>
    <div class="hidden lg:block">
        <div class="wrapper py-8">
            <div class="lg:grid lg:grid-cols-3 lg:gap-8">

                <?php /* Main articles (2/3) */ ?>
                <div class="col-span-2 flex flex-col gap-6">
                    <?php foreach ( $articles as $index => $article ) :
                        get_template_part( 'template-parts/ev-news-feed/card', null, [ 'article' => $article, 'index' => $index + 1 ] );
                    endforeach; ?>
                </div>

                <?php /* Sidebar (1/3) */ ?>
                <div class="col-span-1 flex flex-col gap-12">
                    <?php get_template_part( 'template-parts/sidebar' ); ?>
                </div>

            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<?php get_template_part( 'template-parts/find-us' ); ?>
<?php get_template_part( 'template-parts/footer' ); ?>
