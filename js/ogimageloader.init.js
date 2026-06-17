function fetchOgImage(url) {
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            url: ogProxy.ajaxUrl + '?action=fetch_og_image&nonce=' + ogProxy.nonce + '&url=' + encodeURIComponent(url),
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                const html = document.createElement('html');
                html.innerHTML = data.contents;

                const ogImage = jQuery(html).find('meta[property="og:image"]').attr('content');

                if (ogImage) {
                    if (!/^https:\/\//i.test(ogImage)) {
                        reject('Invalid og:image URL scheme');
                        return;
                    }
                    resolve(ogImage);
                } else {
                    reject('No og:image meta tag found');
                }
            },
            error: function (err) {
                reject(err);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    jQuery('.js-external-article').each(function () {
        const url = jQuery(this).find('a').attr('href');

        fetchOgImage(url)
            .then((imageUrl) => {
                jQuery(this).find('.js-thumbnail').attr('src', imageUrl).addClass('opacity-100');
            })
            .catch((error) => {
                // console.error('Error:', error);
            });
    });
});
