function fetchOgImage(url) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: '/wp-content/themes/carlifebydani/corsproxy.php?url=' + encodeURIComponent(url),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                const html = document.createElement('html');
                html.innerHTML = data.contents;

                const ogImage = jQuery(html).find('meta[property="og:image"]').attr('content');

                if (ogImage) {
                    resolve(ogImage);
                } else {
                    reject('No og:image meta tag found');
                }
            },
            error: function (err) {
                reject('Failed to fetch the page');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    jQuery('.js-external-article').each(function () {
        const url = jQuery(this).find('a').attr('href');

        fetchOgImage(url)
            .then((imageUrl) => {
                jQuery(this).find('.js-thumbnail').css('background-image', 'url(' + imageUrl + ')').addClass('opacity-100');
      
            })
            .catch((error) => {
                // console.error('Error:', error);
            });
    });
});
