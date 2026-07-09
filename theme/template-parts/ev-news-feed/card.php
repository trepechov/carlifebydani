<?php
/**
 * EV News Feed — single article card.
 *
 * Mobile  : vertical — 16:9 image on top, content below. rounded-br-4xl.
 * sm+     : horizontal 2-col — image left, content right. rounded-br-5xl.
 *
 * $args:
 *   article  array  { title, link, description, source, pub_date, upvote, downvote, added_date }
 *   index    int    1-based position in the feed
 */

$article      = $args['article'] ?? [];
$index        = (int) ( $args['index'] ?? 0 );
$num          = $index ? str_pad( $index, 2, '0', STR_PAD_LEFT ) : '';
$title        = esc_html( $article['title']       ?? '' );
$link         = esc_url(  $article['link']        ?? '' );
$source       = esc_html( $article['source']      ?? '' );
$description  = esc_html( $article['description'] ?? '' );
$pub_date_raw = $article['pub_date'] ?? '';
$pub_date     = $pub_date_raw ? esc_html( date_i18n( 'j M Y', strtotime( $pub_date_raw ) ) ) : '';
$upvote       = (int) ( $article['upvote']    ?? 0 );
$downvote     = (int) ( $article['downvote']  ?? 0 );
$is_new       = ( $article['added_date'] ?? '' ) === gmdate( 'Y-m-d' );
$data_title   = esc_attr( $article['title'] ?? '' );
$data_url     = esc_attr( $article['link']  ?? '' );
$data_id      = esc_attr( $article['id'] ?? md5( $article['link'] ?? '' ) );
?>
<article class="js-external-article group grid grid-cols-1 bg-black rounded-br-4xl overflow-hidden shadow-card hover:bg-brand-solidgrey transition-colors duration-300 sm:grid-cols-2 sm:rounded-br-5xl">

    <?php /* ── IMAGE ──
           aspect-video sets 16:9 on mobile; on sm+ the grid row stretches
           the cell to match the content column height (object-cover fills it). */ ?>
    <a href="<?php echo $link; ?>" target="_blank" rel="nofollow"
       class="relative block aspect-video overflow-hidden bg-black sm:aspect-auto"
       data-ev-news data-title="<?php echo $data_title; ?>" data-url="<?php echo $data_url; ?>">
        <div class="overlay bg-to-solidgray-gradient-post opacity-0 group-hover:opacity-100 sm:hidden"></div>
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/noimage-640x360.jpg" alt=""
             class="absolute inset-0 w-full h-full object-cover">
        <img src="" alt="<?php echo $title; ?>"
             class="js-thumbnail absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity delay-500 duration-1000">
        <?php if ( $num ) : ?>
        <span class="absolute bottom-3 left-6 z-10 text-6xl font-bold leading-none select-none pointer-events-none sm:bottom-auto sm:top-3 sm:left-3" style="color:rgba(255,255,255,0.25);text-shadow:1px 1px rgba(0,0,0,0.25)"><?php echo $num; ?></span>
        <?php endif; ?>
    </a>

    <?php /* ── CONTENT ── */ ?>
    <div class="relative px-[7%] pb-[12%] min-h-[13rem]">

        <?php /* Eyebrow: source · date · new badge · vote buttons */ ?>
        <div class="mt-5 mb-3 flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-red"><?php echo $source; ?></span>
            <?php if ( $pub_date ) : ?>
            <span class="w-1.5 h-1.5 rounded-full bg-brand-red flex-shrink-0"></span>
            <span class="text-xs text-brand-lightgrey"><?php echo $pub_date; ?></span>
            <?php endif; ?>
            <?php if ( $is_new ) : ?>
            <span class="material-symbols-outlined text-brand-red flex-shrink-0" style="font-size:20px" title="Нова статия">fiber_new</span>
            <?php endif; ?>

            <?php /* Vote buttons — always rendered (clickable at 0); cookie state applied by ev-news-voting.js */ ?>
            <button type="button"
                    class="relative rounded-full border w-7 h-7 flex items-center justify-center bg-brand-solidgrey border-brand-green flex-shrink-0 transition-colors duration-300 disabled:cursor-not-allowed ml-auto"
                    data-ev-news-upvote data-article-id="<?php echo $data_id; ?>" data-article-url="<?php echo $data_url; ?>" data-title="<?php echo $data_title; ?>"
                    aria-label="Upvote">
                <span class="text-xs font-bold" data-vote-count="up"><?php echo $upvote; ?></span>
                <div class="absolute -top-1 -right-1 w-3.5 h-3.5 flex items-center justify-center rounded-full bg-brand-green material-symbols-outlined" style="font-size:9px;box-shadow:0 0 0 1.5px #000">thumb_up</div>
            </button>
            <button type="button"
                    class="relative rounded-full border w-7 h-7 flex items-center justify-center bg-brand-solidgrey border-brand-red flex-shrink-0 transition-colors duration-300 disabled:cursor-not-allowed"
                    data-ev-news-downvote data-article-id="<?php echo $data_id; ?>" data-article-url="<?php echo $data_url; ?>" data-title="<?php echo $data_title; ?>"
                    aria-label="Downvote">
                <span class="text-xs font-bold" data-vote-count="down"><?php echo $downvote; ?></span>
                <div class="absolute -top-1 -right-1 w-3.5 h-3.5 flex items-center justify-center rounded-full bg-brand-red material-symbols-outlined" style="font-size:9px;box-shadow:0 0 0 1.5px #000">thumb_down</div>
            </button>
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

    </div>

</article>
