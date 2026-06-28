'use strict';

const DAILY_COUNT_URL = '/wp-json/carlifebydani/v1/daily-count';
const FEED_URL        = '/ev-news-feed/';
const ICON_URL        = '/wp-content/themes/carlifebydani/images/pwaicon.png';

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(self.clients.claim()));

self.addEventListener('push', event => {
    event.waitUntil(
        fetch(DAILY_COUNT_URL)
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(({ count }) => Promise.all([
                self.registration.setAppBadge(count),
                self.registration.showNotification('CLBD News Feed', {
                    body: `${count} нов${count === 1 ? 'а статия' : 'и статии'} днес`,
                    icon: ICON_URL,
                    badge: ICON_URL,
                    tag: 'ev-daily',
                    renotify: true,
                    data: { url: FEED_URL },
                }),
            ]))
            .catch(() => self.registration.showNotification('CLBD News Feed', {
                body: 'Нови статии са достъпни',
                icon: ICON_URL,
                badge: ICON_URL,
                tag: 'ev-daily',
                data: { url: FEED_URL },
            }))
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const target = event.notification.data?.url || FEED_URL;

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clients => {
            for (const client of clients) {
                if (client.url.includes(FEED_URL) && 'focus' in client) {
                    self.registration.clearAppBadge();
                    return client.focus();
                }
            }
            return self.clients.openWindow(target).then(() => self.registration.clearAppBadge());
        })
    );
});
