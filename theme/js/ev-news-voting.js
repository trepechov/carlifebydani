// EV News Feed vote buttons — first-party cookie state + optimistic DOM math + GA4 events.
// Backend GA4→Sheets sync (ENA_Analytics) aggregates the ev_news_upvote / ev_news_downvote
// events keyed on article_url (truncated to 100 chars, matching the click-tracking pattern).
//
// Cookie structure per article (new format):
//   { current: 'up'|'down'|null, fired: { up: bool, down: bool } }
//
// GA4 events are fired at most once per direction per article, regardless of how many times
// the user switches between upvote and downvote. The `fired` map tracks this independently
// of `current` (the active UI selection).
//
// Backward compat: old entries stored as plain string 'up'|'down' are migrated on read.

(function () {
    var COOKIE_NAME = 'ev_news_votes';
    var COOKIE_DAYS = 365;

    function readVotes() {
        var match = document.cookie.match(/(?:^|;\s*)ev_news_votes=([^;]*)/);
        if (!match) return {};
        try {
            var parsed = JSON.parse(decodeURIComponent(match[1]));
            return (parsed && typeof parsed === 'object') ? parsed : {};
        } catch (err) {
            return {};
        }
    }

    function writeVotes(votes) {
        var expires = new Date(Date.now() + COOKIE_DAYS * 864e5).toUTCString();
        document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(votes)) +
            '; path=/; expires=' + expires + '; SameSite=Lax';
    }

    // Normalises old string entries ('up'|'down') to the new object shape.
    function getEntry(votes, articleId) {
        var raw = votes[articleId];
        if (!raw) return null;
        if (typeof raw === 'string') {
            var migrated = { current: raw, fired: {} };
            migrated.fired[raw] = true;
            return migrated;
        }
        if (!raw.fired) raw.fired = {};
        return raw;
    }

    function buttons(articleId, dir) {
        var attr = dir === 'up' ? 'data-ev-news-upvote' : 'data-ev-news-downvote';
        return document.querySelectorAll('[' + attr + '][data-article-id="' + cssEscape(articleId) + '"]');
    }

    function cssEscape(value) {
        if (window.CSS && CSS.escape) return CSS.escape(value);
        return String(value).replace(/["\\]/g, '\\$&');
    }

    function setDisabled(btn, disabled) {
        btn.disabled = disabled;
    }

    // Fills the button circle solid (voted) or restores the default grey background.
    function setVotedVisual(btn, voted) {
        var isUp = btn.hasAttribute('data-ev-news-upvote');
        var solidClass = isUp ? 'bg-brand-green' : 'bg-brand-red';
        if (voted) {
            btn.classList.remove('bg-brand-solidgrey');
            btn.classList.add(solidClass);
        } else {
            btn.classList.remove(solidClass);
            btn.classList.add('bg-brand-solidgrey');
        }
    }

    function countSpan(btn) {
        var dir = btn.hasAttribute('data-ev-news-upvote') ? 'up' : 'down';
        return btn.querySelector('[data-vote-count="' + dir + '"]');
    }

    function bump(btn, delta) {
        var span = countSpan(btn);
        if (!span) return;
        var current = parseInt(span.textContent, 10) || 0;
        span.textContent = Math.max(0, current + delta);
    }

    function pushEvent(eventName, btn) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: eventName,
            article_title: btn.dataset.title,
            article_url: (btn.dataset.articleUrl || '').slice(0, 100),
            article_id: btn.dataset.articleId
        });
    }

    // On load: disable and visually mark whichever button matches the stored current vote.
    function applyStoredState() {
        var votes = readVotes();
        Object.keys(votes).forEach(function (articleId) {
            var entry = getEntry(votes, articleId);
            if (!entry || !entry.current) return;
            var dir = entry.current;
            if (dir !== 'up' && dir !== 'down') return;
            buttons(articleId, dir).forEach(function (btn) {
                bump(btn, 1);
                setDisabled(btn, true);
                setVotedVisual(btn, true);
            });
        });
    }

    document.addEventListener('click', function (e) {
        var upBtn = e.target.closest('[data-ev-news-upvote]');
        var downBtn = e.target.closest('[data-ev-news-downvote]');
        var btn = upBtn || downBtn;
        if (!btn || btn.disabled) return;

        var dir = upBtn ? 'up' : 'down';
        var articleId = btn.dataset.articleId;
        var votes = readVotes();
        var entry = getEntry(votes, articleId) || { current: null, fired: {} };
        var prev = entry.current;

        if (prev === dir) return; // already selected this way (defensive; button should be disabled)

        // Undo the previously selected direction.
        if (prev === 'up' || prev === 'down') {
            buttons(articleId, prev).forEach(function (b) {
                bump(b, -1);
                setDisabled(b, false);
                setVotedVisual(b, false);
            });
        }

        // Apply the new direction.
        buttons(articleId, dir).forEach(function (b) {
            bump(b, 1);
            setDisabled(b, true);
            setVotedVisual(b, true);
        });

        entry.current = dir;

        // Fire GA4 only if this direction has never been sent before.
        if (!entry.fired[dir]) {
            entry.fired[dir] = true;
            pushEvent(dir === 'up' ? 'ev_news_upvote' : 'ev_news_downvote', btn);
        }

        votes[articleId] = entry;
        writeVotes(votes);
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyStoredState);
    } else {
        applyStoredState();
    }
})();
