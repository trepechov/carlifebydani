'use strict';

/* global pwaConfig */

(function () {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

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

    // Must be called directly from a user tap — iOS silently ignores
    // Notification.requestPermission() when called without a user gesture.
    async function subscribeFromGesture(reg, banner) {
        const permission = await Notification.requestPermission();
        banner.remove();
        if (permission !== 'granted') return;

        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(pwaConfig.vapidPublicKey),
        });
        await saveSub(sub.toJSON());
    }

    function showOptInBanner(reg) {
        const banner = document.createElement('div');
        banner.style.cssText = [
            'position:fixed', 'bottom:1.5rem', 'left:50%',
            'transform:translateX(-50%)',
            'background:#1e293b', 'color:#f8fafc',
            'border:1px solid #FE3652',
            'border-radius:1rem', 'padding:0.875rem 1.25rem',
            'display:flex', 'align-items:center', 'gap:0.875rem',
            'font-size:0.875rem', 'z-index:9999',
            'box-shadow:0 4px 24px rgba(0,0,0,0.5)',
            'max-width:calc(100vw - 2rem)',
        ].join(';');

        banner.innerHTML = `
            <span>🔔 Получавай известия за нови статии</span>
            <button id="ena-allow-btn" style="background:#FE3652;color:#fff;border:none;border-radius:0.5rem;padding:0.4rem 0.9rem;font-size:0.8125rem;font-weight:600;cursor:pointer;white-space:nowrap;">Разреши</button>
            <button id="ena-dismiss-btn" style="background:transparent;color:#94a3b8;border:none;font-size:1.1rem;cursor:pointer;padding:0 0.2rem;line-height:1;">✕</button>
        `;

        document.body.appendChild(banner);

        // Allow button — user gesture triggers the permission prompt
        document.getElementById('ena-allow-btn').addEventListener('click', () => {
            subscribeFromGesture(reg, banner);
        });

        document.getElementById('ena-dismiss-btn').addEventListener('click', () => {
            banner.remove();
            // Remember dismissal for 7 days so we don't pester on every visit
            localStorage.setItem('ena-push-dismissed', Date.now());
        });
    }

    navigator.serviceWorker.register(pwaConfig.swUrl, { scope: '/' })
        .then(async reg => {
            // Clear badge whenever the app is opened
            if ('clearAppBadge' in navigator) navigator.clearAppBadge();

            if (!pwaConfig.vapidPublicKey || !window.location.pathname.startsWith('/ev-news-feed')) return;

            // Already subscribed — just re-save the endpoint (handles key rotation)
            const existing = await reg.pushManager.getSubscription();
            if (existing) {
                await saveSub(existing.toJSON());
                return;
            }

            // Already granted — subscribe silently (e.g. after SW update cleared the sub)
            if (Notification.permission === 'granted') {
                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(pwaConfig.vapidPublicKey),
                });
                await saveSub(sub.toJSON());
                return;
            }

            // Permission not yet requested — show opt-in banner unless recently dismissed
            if (Notification.permission === 'default') {
                const dismissed = parseInt(localStorage.getItem('ena-push-dismissed') || '0', 10);
                const sevenDays = 7 * 24 * 60 * 60 * 1000;
                if (Date.now() - dismissed > sevenDays) {
                    showOptInBanner(reg);
                }
            }
        });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && 'clearAppBadge' in navigator) {
            navigator.clearAppBadge();
        }
    });
}());
