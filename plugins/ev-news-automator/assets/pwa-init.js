'use strict';

/* global pwaConfig */

(function () {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    // Convert base64url VAPID key to Uint8Array for PushManager.subscribe
    function urlBase64ToUint8Array(base64url) {
        const padded = base64url.replace(/-/g, '+').replace(/_/g, '/');
        const padLen = (4 - (padded.length % 4)) % 4;
        const b64 = padded + '='.repeat(padLen);
        const raw = atob(b64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    async function saveSub(sub) {
        await fetch(pwaConfig.subscribeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'ena_save_push_sub',
                nonce: pwaConfig.nonce,
                subscription: JSON.stringify(sub),
            }),
        });
    }

    async function subscribe(reg) {
        const existing = await reg.pushManager.getSubscription();
        if (existing) {
            await saveSub(existing.toJSON());
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(pwaConfig.vapidPublicKey),
        });
        await saveSub(sub.toJSON());
    }

    navigator.serviceWorker.register(pwaConfig.swUrl, { scope: '/' })
        .then(reg => {
            // Request push permission only on the EV news feed page
            if (window.location.pathname.startsWith('/ev-news-feed')) {
                subscribe(reg);
            }
        });

    // Clear badge when the user opens the app
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && 'clearAppBadge' in navigator) {
            navigator.clearAppBadge();
        }
    });
    if (document.visibilityState === 'visible' && 'clearAppBadge' in navigator) {
        navigator.clearAppBadge();
    }
}());
