</main>

<footer>
    <div class="container max-w-7xl p-2 space-y-8">
        <div class="text-2xl">CarLife by Dani</div>
        <div class="flex justify-between">
            <nav>
                <ul class="list-none flex space-x-4">
                    <?php wp_list_categories(array(
                        'title_li' => '',
                        'orderby' => 'id',
                        'hide_empty' => false,
                        'parent' => 0,
                    )); ?>
                </ul>
            </nav>
            <div class="text-2xl">info@carlife.com</div>
        </div>
        <div class="py-2 text-xs text-center border-t border-gray-800">&copy;2023 carlifebydani.com</div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>