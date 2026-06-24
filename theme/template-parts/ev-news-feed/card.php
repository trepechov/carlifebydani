<?php
/**
 * EV News Feed — single article card.
 *
 * $args:
 *   article  array  { id, title, link, description, source, date, clicks }
 *
 * Mobile  : full-height (70 vh) card, image fills background, text overlaid at bottom.
 * Desktop : horizontal flex row, thumbnail left, content right.
 */

$article     = $args['article'] ?? [];
$index       = (int) ( $args['index'] ?? 0 );
$num         = $index ? str_pad( $index, 2, '0', STR_PAD_LEFT ) : '';
$title       = esc_html( $article['title']       ?? '' );
$link        = esc_url(  $article['link']        ?? '' );
$source      = esc_html( $article['source']      ?? '' );
$description = esc_html( $article['description'] ?? '' );
$date        = esc_html( $article['date']        ?? '' );
$clicks      = (int) ( $article['clicks'] ?? 0 );
$data_title  = esc_attr( $article['title'] ?? '' );
$data_url    = esc_attr( $article['link']  ?? '' );
?>
<article class="js-external-article group relative h-[70vh] rounded-br-4xl overflow-hidden bg-brand-solidgrey shadow-card lg:h-auto lg:flex lg:flex-row lg:items-stretch lg:bg-brand-grey lg:hover:bg-brand-solidgrey lg:transition-colors lg:duration-200">

    <?php /* ── IMAGE ── */ ?>
    <div class="absolute inset-0 lg:relative lg:w-44 lg:h-32 lg:flex-shrink-0 lg:overflow-hidden">
        <a href="<?php echo $link; ?>" target="_blank" rel="nofollow" class="block w-full h-full" data-ev-news data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>">
            <div class="relative w-full h-full overflow-hidden">
                <div class="absolute inset-0 bg-brand-grey"></div>
                <img src="" alt="<?php echo $title; ?>" class="js-thumbnail absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity delay-500 duration-1000">
            </div>
        </a>
    </div>

    <?php /* ── GRADIENT OVERLAY (mobile only) ── */ ?>
    <div class="absolute inset-0 bg-to-black-gradient-feed pointer-events-none lg:hidden"></div>

    <?php /* ── MOBILE NUMBER (top-left overlay, hidden on desktop) ── */ ?>
    <?php if ( $num ) : ?>
    <div class="absolute top-4 left-5 pointer-events-none lg:hidden">
        <span class="text-8xl font-bold text-white/20 leading-none"><?php echo $num; ?></span>
    </div>
    <?php endif; ?>

    <?php /* ── MOBILE TEXT (bottom overlay, hidden on desktop) ── */ ?>
    <div class="absolute bottom-0 left-0 right-0 p-5 lg:hidden">
        <?php if ( $clicks > 0 ) : ?>
        <div class="absolute top-0 right-5 -translate-y-1/2 flex items-center gap-1">
            <div class="relative rounded-full border-2 w-9 h-9 flex items-center justify-center bg-black/70 border-brand-green text-sm font-bold backdrop-blur-sm">
                <span><?php echo $clicks; ?></span>
                <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-green material-symbols-outlined">Check</div>
            </div>
        </div>
        <?php endif; ?>
        <span class="text-brand-red text-xs font-bold uppercase tracking-widest block mb-2"><?php echo $source; ?></span>
        <h3 class="mt-0 mb-2 text-xl font-bold leading-tight line-clamp-2">
            <a href="<?php echo $link; ?>" target="_blank" rel="nofollow" class="text-white hover:text-brand-red link-transition" data-ev-news-article data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>"><?php echo $title; ?></a>
        </h3>
        <?php if ( $description ) : ?>
        <p class="text-brand-lightgrey text-sm line-clamp-3 mt-0"><?php echo $description; ?></p>
        <?php endif; ?>
    </div>

    <?php /* ── DESKTOP CONTENT (right column, hidden on mobile) ── */ ?>
    <div class="hidden lg:flex lg:flex-col lg:justify-between lg:flex-1 lg:p-5">
        <div>
            <span class="text-brand-red text-xs font-bold uppercase tracking-widest block mb-2"><?php echo $source; ?></span>
            <h3 class="mt-0 mb-2 text-lg font-bold leading-snug line-clamp-2 group-hover:text-brand-red transition-colors duration-200">
                <a href="<?php echo $link; ?>" target="_blank" rel="nofollow" class="link-transition" data-ev-news-article data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>"><?php echo $title; ?></a>
            </h3>
            <?php if ( $description ) : ?>
            <p class="text-brand-lightgrey text-sm line-clamp-2 mt-0"><?php echo $description; ?></p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-3 mt-3">
            <div class="relative rounded-full border-2 w-10 h-10 flex items-center justify-center bg-brand-solidgrey border-brand-green border-opacity-40 flex-shrink-0">
                <span class="text-sm font-bold"><?php echo $clicks; ?></span>
                <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-green material-symbols-outlined">Check</div>
            </div>
            <?php if ( $date ) : ?>
            <span class="text-brand-lightgrey text-xs"><?php echo $date; ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
