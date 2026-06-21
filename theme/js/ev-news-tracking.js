document.addEventListener('click', function (e) {
    var link = e.target.closest('[data-ev-news-article]');
    if (!link) return;

    var allLinks = document.querySelectorAll('[data-ev-news-article]');
    var position = Array.prototype.indexOf.call(allLinks, link) + 1;

    var url = (link.dataset.url || '').slice(0, 100);
    var source = '';
    try { source = new URL(url).hostname; } catch (err) {}

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        event: 'ev_news_click',
        article_title: link.dataset.title,
        article_url: url,
        article_source: source,
        article_position: position
    });
});
