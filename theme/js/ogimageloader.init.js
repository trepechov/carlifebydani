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

document.addEventListener('DOMContentLoaded', function () {
    var cards = Array.from(document.querySelectorAll('.js-external-article'));
    var loaded = new Set();

    function loadCard(card, index) {
        if (loaded.has(index)) return;
        loaded.add(index);

        var anchor = card.querySelector('a');
        var url = anchor ? anchor.getAttribute('href') : null;
        if (!url) return;

        fetchOgImage(url)
            .then(function (imageUrl) {
                var thumb = card.querySelector('.js-thumbnail');
                if (thumb) {
                    thumb.src = imageUrl;
                    thumb.classList.add('opacity-100');
                }
            })
            .catch(function () {});
    }

    if (!('IntersectionObserver' in window)) {
        cards.forEach(function (card, i) { loadCard(card, i); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var index = cards.indexOf(entry.target);
            loadCard(entry.target, index);
        });
    }, {
        rootMargin: '0px 0px 400px 0px'
    });

    cards.forEach(function (card) { observer.observe(card); });
});
