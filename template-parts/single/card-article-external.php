<article class="bg-black p-5 flex items-start gap-5 rounded-br-2xl js-external-article">
    <div class="w-36 h-20 bg-cover" style="background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/noimage-gray.png">
        <div class="w-full h-full opacity-0 transition-opacity duration-300 bg-cover js-thumbnail"></div>
    </div>
    <div class="flex flex-col justify-between flex-1">
        <div class="h-20 flex flex-col justify-between">
            <a href="<?php echo $args['article']->link ?>">
                <h5 class="mt-0"><?php echo $args['article']->title ?></h5>
            </a>

            <a href="#">Виж още</a>
        </div>
        <p class="text-base"><?php echo $args['article']->description ?></p>
    </div>
    <div class="flex gap-3 items-center h-20">
        <div class="rounded-full border-2 w-12 h-12 flex justify-center items-center bg-brand-solidgrey border-brand-green border-opacity-40 relative">
            <span class="text-lg font-bold"><?php echo $args['article']->upvote ?></span>
            <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-green material-symbols-outlined">Check</div>
        </div>
        <div class="rounded-full border-2 w-12 h-12 flex justify-center items-center bg-brand-solidgrey border-brand-red border-opacity-40 relative">
            <span class="text-lg font-bold"><?php echo $args['article']->downvote ?></span>
            <div class="absolute -top-1 -right-1 w-4 h-4 flex items-center justify-center text-xs font-bold rounded-full bg-brand-red material-symbols-outlined">Close</div>
        </div>
    </div>
</article>