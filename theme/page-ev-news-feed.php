<?php
/**
 * Template Name: EV News Feed
 *
 * Layout mirrors index.php (category/archive template) exactly:
 * all content lives inside the same relative wrapper as the diagonal
 * carbon stripe, so the background treatment is identical to /ev-masters/.
 *
 * Data: ev_news_live_articles wp_option written by EV News Automator (ENA_Sync).
 */

$raw      = get_option( 'ev_news_live_articles', '[]' );
$articles = json_decode( $raw, true );
if ( ! is_array( $articles ) ) {
    $articles = [];
}

$sync_status = get_option( 'ena_status_last_sync', [] );
$sheet_name  = $sync_status['sheet_name'] ?? '';
if ( ! $sheet_name && ! empty( $articles[0]['date'] ) ) {
    $dt         = DateTime::createFromFormat( 'Y-m-d', $articles[0]['date'] );
    $sheet_name = $dt ? $dt->format( 'd.m.Y' ) : '';
}
$session_label = $sheet_name ? "Подкаст — {$sheet_name}" : 'Новини за подкаста тази седмица';

get_template_part( 'template-parts/header' );
?>

<div class="relative">

    <?php /* Diagonal carbon stripe — same as index.php / ev-masters */ ?>
    <div class="absolute h-80 w-full bg-carbon-stripe-white-20">
        <div class="h-full bg-from-black-60-gradient"></div>
    </div>

    <div class="wrapper py-8 relative">

        <h1 class="title text-3xl/8 font-bold mt-6 mb-2">EV News Feed</h1>
        <p class="mb-8 text-brand-lightgrey"><?php echo esc_html( $session_label ); ?></p>

        <?php if ( empty( $articles ) ) : ?>

        <div class="py-20 text-center">
            <span class="material-symbols-outlined text-5xl text-brand-lightgrey block mb-4">newspaper</span>
            <p class="text-brand-lightgrey text-lg">Няма налични новини. Проверете по-късно.</p>
        </div>

        <?php else : ?>

        <div class="lg:grid lg:grid-cols-3 lg:gap-8">

            <div class="pb-8 col-span-2 border-b-2 border-brand-button">
                <div class="flex flex-col gap-8">
                    <?php foreach ( $articles as $i => $article ) :
                        get_template_part( 'template-parts/ev-news-feed/card-classic', null, [
                            'article' => $article,
                            'index'   => $i + 1,
                        ] );
                    endforeach; ?>
                </div>
            </div>

            <div class="hidden lg:flex lg:col-span-1 lg:flex-col lg:gap-12">
                <?php get_template_part( 'template-parts/sidebar' ); ?>
            </div>

        </div>

        <?php endif; ?>

    </div>

</div>

<?php get_template_part( 'template-parts/find-us' ); ?>
<?php get_template_part( 'template-parts/footer' ); ?>
