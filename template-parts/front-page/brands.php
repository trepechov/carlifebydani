<?php
$brandImages = [];
$brandDirectory = get_template_directory() . '/images/brands';
$brandFiles = scandir($brandDirectory);
foreach ($brandFiles as $file) {
    if ($file !== '.' && $file !== '..') {
        $brandImages[] = pathinfo($file, PATHINFO_FILENAME);;
    }
}

$tags = get_tags([
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 999,
    'hide_empty' => false
]);

?>

<div class="container py-12">
    <div class="flex mb-8 justify-between items-center">
        <h3 class="title">Пpоизводители</h3>
    </div>

    <div class="w-full lg:rounded-br-8xl lg:border-b-20 lg:border-r-20 lg:pb-5 lg:pr-5 lg:border-white/10">
        <div class="bg-black p-8 pr-10 lg:rounded-br-6xl grid grid-cols-2 lg:grid-cols-6">
            <?php
            foreach ($tags as $tag) {
                if (in_array($tag->slug, $brandImages)) {
            ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="hover:bg-brand-solidgrey">
                        <img src="<?php echo get_template_directory_uri() ?>/images/brands/<?php echo $tag->slug ?>.png" alt="<?php echo $tag->name ?>">
                    </a>
            <?php
                }
            }
            ?>
        </div>
    </div>
</div>