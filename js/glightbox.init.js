document.addEventListener('DOMContentLoaded', () => {
    const lightbox = GLightbox({
        touchNavigation: true,
        // loop: true,
        // autoplayVideos: true,
        zoomable: true,
        selector: '.wp-block-gallery a, .wp-block-image a[href*=carlifebydani]'
    });

    // Add the lightbox class to the gallery and image blocks. Use for custume styling
    jQuery('.wp-block-gallery a, .wp-block-image a[href*=carlifebydani]').addClass('glightbox');
});
