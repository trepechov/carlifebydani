<article class="p-5 bg-black flex items-start gap-5 rounded-br-2xl js-external-article">

    <div class="hidden sm:block w-36 h-20 bg-cover" style="background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/noimage-gray.png">
        <a href="<?php echo $args['article']->link ?>" target="_blank" rel="nofollow">
            <div class="aspect-[36/20] overflow-hidden relative">
                <img src="" alt="<?php echo $args['article']->title ?>" class="absolute top-1/2 -translate-y-1/2 opacity-0 transition-opacity delay-1000 duration-1000 js-thumbnail">
            </div>
        </a>
    </div>

    <div class="flex flex-col justify-between flex-1">
        <div class="peer min-h-[80px] flex flex-col justify-between">
            <h5 class="mt-0">
                <a href="<?php echo $args['article']->link ?>" target="_blank" rel="nofollow" class="hover:text-brand-red link-transition"><?php echo $args['article']->title ?></a>
            </h5>

            <?php if (!empty($args['article']->description)) { ?>
                <label class="mt-3 flex items-center gap-1 cursor-pointer" for="news-article-<?php echo $args['article']->id ?>">
                    <input class="peer hidden" type="checkbox" id="news-article-<?php echo $args['article']->id ?>">
                    <span class="w-[18px] h-[18px] text-md flex justify-center items-center bg-brand-solidgrey delay-75 duration-150 peer-checked:rotate-180">
                        <span class="material-symbols-outlined">keyboard_arrow_down</span>
                    </span>

                    <span class="text-brand-red">Виж още</span>
                </label>
            <?php } ?>
        </div>
        <p class="mt-2 text-base overflow-hidden transition-all ease-in-out duration-300 opacity-0 max-h-0 peer-has-[:checked]:max-h-60 peer-has-[:checked]:opacity-100"><?php echo $args['article']->description ?></p>
    </div>

    <div class="flex gap-3 items-center h-20">
        <div class="rounded-full border-2 w-12 h-12 flex justify-center items-center bg-brand-solidgrey border-brand-green border-opacity-40 relative">
            <span class="text-lg font-bold"><?php echo !empty($args['article']->upvote) ? $args['article']->upvote : '0' ?></span>
            <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-green material-symbols-outlined">Check</div>
        </div>
        <div class="rounded-full border-2 w-12 h-12 flex justify-center items-center bg-brand-solidgrey border-brand-red border-opacity-40 relative">
            <span class="text-lg font-bold"><?php echo !empty($args['article']->downvote) ? $args['article']->downvote : '0' ?></span>
            <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-red material-symbols-outlined">Close</div>
        </div>
    </div>
</article>