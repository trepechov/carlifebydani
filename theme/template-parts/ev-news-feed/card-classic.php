<?php
/**
 * EV News Feed Classic — single article card.
 *
 * Replicates the exact shape and grid structure of card-article.php
 * (used on category/archive pages like /ev-news/).
 *
 * Mobile  : vertical — 16:9 image on top, content below. rounded-br-4xl.
 * sm+     : horizontal 2-col — image left, content right. rounded-br-5xl.
 *
 * Δ vs card-article: source/date eyebrow instead of category, smaller title
 * (text-base/lg instead of 2xl/3xl), description paragraph added, position
 * number in a low-contrast dark block anchored bottom-right of the card.
 *
 * $args:
 *   article  array  { title, link, description, source, date, clicks }
 *   index    int    1-based position in the feed
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
<article class="js-external-article group grid grid-cols-1 bg-brand-solidgrey rounded-br-4xl overflow-hidden shadow-card hover:bg-brand-grey transition-colors duration-200 sm:grid-cols-2 sm:rounded-br-5xl">

    <?php /* ── IMAGE ──
           aspect-video sets 16:9 on mobile; on sm+ the grid row stretches
           the cell to match the content column height (object-cover fills it). */ ?>
    <a href="<?php echo $link; ?>" target="_blank" rel="nofollow"
       class="relative block aspect-video overflow-hidden bg-brand-grey sm:aspect-auto"
       data-ev-news data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>">
        <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100 sm:hidden"></div>
        <img src="" alt="<?php echo $title; ?>"
             class="js-thumbnail absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity delay-500 duration-1000">
    </a>

    <?php /* ── CONTENT ── */ ?>
    <div class="relative px-[7%] pb-[12%]">

        <?php /* Eyebrow: source · date · clicks badge */ ?>
        <div class="mt-5 mb-3 flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-red"><?php echo $source; ?></span>
            <?php if ( $date ) : ?>
            <span class="w-1.5 h-1.5 rounded-full bg-brand-red flex-shrink-0"></span>
            <span class="text-xs text-brand-lightgrey"><?php echo $date; ?></span>
            <?php endif; ?>
            <?php if ( $clicks > 0 ) : ?>
            <div class="relative ml-auto rounded-full border w-7 h-7 flex items-center justify-center bg-brand-solidgrey border-brand-green flex-shrink-0">
                <span class="text-xs font-bold"><?php echo $clicks; ?></span>
                <div class="absolute -top-1 -right-1 w-3.5 h-3.5 flex items-center justify-center rounded-full bg-brand-green material-symbols-outlined" style="font-size:9px">Check</div>
            </div>
            <?php endif; ?>
        </div>

        <?php /* Title — smaller than category cards to leave room for description */ ?>
        <h3 class="mt-0 mb-2">
            <a href="<?php echo $link; ?>" target="_blank" rel="nofollow"
               class="text-base/6 font-bold line-clamp-2 group-hover:text-brand-red link-transition sm:text-lg/6"
               data-ev-news-article data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>"><?php echo $title; ?></a>
        </h3>

        <?php /* Description */ ?>
        <?php if ( $description ) : ?>
        <p class="text-brand-lightgrey text-sm line-clamp-3 mt-0 mb-0"><?php echo $description; ?></p>
        <?php endif; ?>

        <?php /* Position number — low-contrast dark block, bottom-right corner */ ?>
        <?php if ( $num ) : ?>
        <div class="absolute bottom-0 right-0 px-4 py-3 select-none pointer-events-none">
            <span class="text-5xl font-bold leading-none" style="color:rgba(255,255,255,.09)"><?php echo $num; ?></span>
        </div>
        <?php endif; ?>

    </div>

</article>
