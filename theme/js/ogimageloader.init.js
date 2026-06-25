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
    var PRELOAD_AHEAD = 3;

    function loadCard(index) {
        if (loaded.has(index) || index >= cards.length) return;
        loaded.add(index);

        var card = cards[index];
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
        cards.forEach(function (_, i) { loadCard(i); });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var index = cards.indexOf(entry.target);
            var end = Math.min(index + PRELOAD_AHEAD, cards.length - 1);
            for (var i = index; i <= end; i++) {
                loadCard(i);
            }
        });
    }, {
        rootMargin: '0px 0px 400px 0px'
    });

    cards.forEach(function (card) { observer.observe(card); });
});
