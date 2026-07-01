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
            'position:fixed', 'bottom:0', 'left:0', 'right:0',
            'background:#111827', 'color:#f8fafc',
            'border-radius:1.25rem 1.25rem 0 0',
            'padding:1.5rem 1.25rem 2rem',
            'z-index:9999',
            'box-shadow:0 -4px 32px rgba(0,0,0,0.6)',
        ].join(';');

        banner.innerHTML = `
            <p style="font-size:1.125rem;font-weight:700;margin:0 0 0.625rem;color:#f8fafc;">Получавай известия за нови статии</p>
            <p style="font-size:0.875rem;color:#94a3b8;margin:0 0 1.25rem;line-height:1.5;">Уведомявай ме, когато са публикувани нови статии и подкаст епизоди.</p>
            <button id="ena-allow-btn" style="display:block;width:100%;background:#FE3652;color:#fff;border:none;border-radius:0.75rem;padding:0.875rem;font-size:1rem;font-weight:600;cursor:pointer;margin-bottom:0.75rem;">Разреши</button>
            <button id="ena-dismiss-btn" style="display:block;width:100%;background:#1f2937;color:#94a3b8;border:none;border-radius:0.75rem;padding:0.875rem;font-size:1rem;cursor:pointer;">Отхвърляне</button>
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

            if (!pwaConfig.vapidPublicKey) return;

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
