// EV News Feed vote buttons — first-party cookie state + optimistic DOM math + GA4 events.
// Backend GA4→Sheets sync (ENA_Analytics) aggregates the ev_news_upvote / ev_news_downvote
// events keyed on article_url (truncated to 100 chars, matching the click-tracking pattern).
//
// The cookie only bridges the gap until the next GA4→Sheets sync, so it holds votes for the
// articles currently on the feed and nothing else: every write OVERWRITES it with just those
// entries, which drops articles that rotated off the feed and keeps it far below the 4 KB cap
// (browsers silently reject larger cookies — the old JSON format hit that after ~36 votes).
//
// Cookie format: concatenated fixed-width entries, no separators, no URL-encoding needed:
//   <first 8 hex chars of the article id><state char>
// State char: u/d = current up/down, GA4 fired for that direction only;
//             U/D = current up/down, GA4 already fired for both directions.
// GA4 events are fired at most once per direction per article, regardless of switching.
//
// Migration: the legacy JSON cookie (ev_news_votes) is converted on a feed page, then deleted.

(function () {
    var COOKIE_NAME = 'ev_news_v';
    var LEGACY_COOKIE_NAME = 'ev_news_votes';
    var COOKIE_DAYS = 30;
    var ID_LEN = 8;
    var ENTRY_RE = /([0-9a-f]{8})([uUdD])/g;

    function readCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
        return match ? match[1] : null;
    }

    function shortId(articleId) {
        return String(articleId).slice(0, ID_LEN);
    }

    // Returns { shortId: { current: 'up'|'down', fired: { up: bool, down: bool } } }.
    function readVotes() {
        var votes = {};
        var raw = readCookie(COOKIE_NAME) || '';
        var m;
        while ((m = ENTRY_RE.exec(raw)) !== null) {
            var code = m[2];
            var dir = code.toLowerCase() === 'u' ? 'up' : 'down';
            var fired = {};
            fired[dir] = true;
            if (code === 'U' || code === 'D') { fired.up = true; fired.down = true; }
            votes[m[1]] = { current: dir, fired: fired };
        }
        return votes;
    }

    // Legacy JSON cookie: { articleId: 'up'|'down' } or { articleId: { current, fired } }.
    function readLegacyVotes() {
        var raw = readCookie(LEGACY_COOKIE_NAME);
        if (!raw) return null;
        var parsed;
        try {
            parsed = JSON.parse(decodeURIComponent(raw));
        } catch (err) {
            return {};
        }
        var votes = {};
        if (!parsed || typeof parsed !== 'object') return votes;
        Object.keys(parsed).forEach(function (articleId) {
            var entry = parsed[articleId];
            if (typeof entry === 'string') {
                var fired = {};
                fired[entry] = true;
                entry = { current: entry, fired: fired };
            }
            if (!entry || (entry.current !== 'up' && entry.current !== 'down')) return;
            votes[shortId(articleId)] = { current: entry.current, fired: entry.fired || {} };
        });
        return votes;
    }

    // Short ids of every article rendered on this page.
    function pageIds() {
        var ids = {};
        document.querySelectorAll('[data-ev-news-upvote][data-article-id]').forEach(function (btn) {
            ids[shortId(btn.dataset.articleId)] = true;
        });
        return ids;
    }

    // Overwrites the cookie with only the votes for articles on this page.
    function writeVotes(votes, onPage) {
        var value = '';
        Object.keys(votes).forEach(function (id) {
            var entry = votes[id];
            if (!onPage[id] || (entry.current !== 'up' && entry.current !== 'down')) return;
            var code = entry.current === 'up' ? 'u' : 'd';
            if (entry.fired.up && entry.fired.down) code = code.toUpperCase();
            value += id + code;
        });
        if (!value) {
            document.cookie = COOKIE_NAME + '=; path=/; max-age=0; SameSite=Lax';
            return;
        }
        var expires = new Date(Date.now() + COOKIE_DAYS * 864e5).toUTCString();
        document.cookie = COOKIE_NAME + '=' + value + '; path=/; expires=' + expires + '; SameSite=Lax';
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

    // On load: migrate the legacy cookie, prune votes for articles no longer on the feed,
    // then disable and visually mark whichever button matches each stored vote.
    // Only runs on pages that render vote buttons — the script is enqueued site-wide, and
    // pruning against a page with no articles would wipe every stored vote.
    function applyStoredState() {
        var onPage = pageIds();
        if (!Object.keys(onPage).length) return;

        var votes = readVotes();
        var legacy = readLegacyVotes();
        if (legacy) {
            Object.keys(legacy).forEach(function (id) {
                if (!votes[id]) votes[id] = legacy[id];
            });
            document.cookie = LEGACY_COOKIE_NAME + '=; path=/; max-age=0; SameSite=Lax';
        }
        // Rewriting on every feed visit also refreshes the expiry (Safari caps script-set
        // cookies at 7 days from the last write).
        writeVotes(votes, onPage);

        document.querySelectorAll('[data-ev-news-upvote][data-article-id]').forEach(function (upBtn) {
            var articleId = upBtn.dataset.articleId;
            var entry = votes[shortId(articleId)];
            if (!entry) return;
            buttons(articleId, entry.current).forEach(function (btn) {
                if (btn.disabled) return; // same article rendered twice: mark each button once
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
        var id = shortId(articleId);
        var votes = readVotes();
        var entry = votes[id] || { current: null, fired: {} };
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

        votes[id] = entry;
        writeVotes(votes, pageIds());
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyStoredState);
    } else {
        applyStoredState();
    }
})();
