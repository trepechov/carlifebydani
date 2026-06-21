(function () {
    const { url, nonce } = window.enaAjax || {};
    if (!url || !nonce) return;

    let elapsed = 0;
    let timer   = null;

    function startSpinner() {
        elapsed = 0;
        document.getElementById('ena-elapsed').textContent = '0s';
        document.getElementById('ena-spinner').style.display = 'inline-flex';
        document.getElementById('ena-result').textContent = '';
        document.getElementById('ena-result').className = '';
        timer = setInterval(() => {
            elapsed++;
            document.getElementById('ena-elapsed').textContent = elapsed + 's';
        }, 1000);
    }

    function stopSpinner(message, isError) {
        clearInterval(timer);
        document.getElementById('ena-spinner').style.display = 'none';
        const el = document.getElementById('ena-result');
        el.textContent = message;
        el.className   = isError ? 'error' : 'success';
    }

    function trigger(action, button) {
        startSpinner();
        button.disabled = true;

        const body = new URLSearchParams({ action, nonce });

        fetch(url, { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const info = JSON.stringify(data.data);
                    stopSpinner('Done: ' + info, false);
                } else {
                    stopSpinner('Error: ' + (data.data || 'unknown'), true);
                }
            })
            .catch(err => stopSpinner('Network error: ' + err.message, true))
            .finally(() => {
                button.disabled = false;
            });
    }

    document.getElementById('ena-btn-collection')?.addEventListener('click', function () {
        if (!confirm('Run collection now?\n\nThis scrapes all sources, calls OpenRouter for each new article, and writes to Google Sheets. Running it multiple times in a row wastes API credits and may append duplicate articles.\n\nOnly run once unless you have a specific reason to re-run.')) return;
        trigger('ena_run_collection', this);
    });

    document.getElementById('ena-btn-podcast')?.addEventListener('click', function () {
        if (!confirm('Generate podcast script?\n\nBefore continuing, make sure:\n• You have created a new Google Doc for this session\n• The correct Document ID is saved in Settings → Podcast Script Document ID\n\nThe script will be appended to whatever doc ID is currently configured.')) return;
        trigger('ena_run_podcast', this);
    });

    // ── OpenRouter Usage ────────────────────────────────────────────────────

    function fmt(n) {
        return Number(n).toLocaleString();
    }

    function renderAccountCard(keyInfo) {
        const card = document.getElementById('ena-account-card');
        if (!card) return;

        if (keyInfo.error) {
            card.innerHTML = '<h3 style="margin:0 0 10px;font-size:14px;">OpenRouter Account</h3>'
                + '<p style="color:#dc3232;margin:0;">Error: ' + keyInfo.error + '</p>';
            return;
        }

        const usage     = keyInfo.usage     != null ? '$' + Number(keyInfo.usage).toFixed(4)          : '—';
        const limit     = keyInfo.limit     != null ? '$' + Number(keyInfo.limit).toFixed(2)           : 'No limit';
        const remaining = keyInfo.limit_remaining != null ? '$' + Number(keyInfo.limit_remaining).toFixed(4) : '—';
        const tier      = keyInfo.is_free_tier ? 'Free tier' : 'Paid';
        const label     = keyInfo.label || '—';

        card.innerHTML = '<h3 style="margin:0 0 10px;font-size:14px;">OpenRouter Account</h3>'
            + '<table class="ena-stat-table">'
            + '<tr><td>Key label</td><td>' + label + '</td></tr>'
            + '<tr><td>Tier</td><td>' + tier + '</td></tr>'
            + '<tr><td>Credits used</td><td><strong>' + usage + '</strong></td></tr>'
            + '<tr><td>Credit limit</td><td>' + limit + '</td></tr>'
            + (keyInfo.limit_remaining != null ? '<tr><td>Remaining</td><td>' + remaining + '</td></tr>' : '')
            + '</table>';
    }

    function fetchUsage(btn) {
        const status = document.getElementById('ena-usage-status');
        if (btn) btn.disabled = true;
        if (status) status.textContent = 'Loading…';

        fetch(url, { method: 'POST', body: new URLSearchParams({ action: 'ena_openrouter_usage', nonce }) })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    renderAccountCard(data.data.key_info || {});
                    if (status) status.textContent = 'Updated ' + new Date().toLocaleTimeString();
                } else {
                    if (status) status.textContent = 'Error: ' + (data.data || 'unknown');
                }
            })
            .catch(err => { if (status) status.textContent = 'Network error: ' + err.message; })
            .finally(() => { if (btn) btn.disabled = false; });
    }

    document.getElementById('ena-btn-usage-refresh')?.addEventListener('click', function () {
        fetchUsage(this);
    });

    document.getElementById('ena-btn-usage-reset')?.addEventListener('click', function () {
        const btn    = this;
        const status = document.getElementById('ena-usage-status');
        btn.disabled = true;
        if (status) status.textContent = 'Resetting…';

        fetch(url, { method: 'POST', body: new URLSearchParams({ action: 'ena_reset_usage_stats', nonce }) })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (status) status.textContent = 'Stats reset. Reload to see updated counters.';
                } else {
                    if (status) status.textContent = 'Error: ' + (data.data || 'unknown');
                }
            })
            .catch(err => { if (status) status.textContent = 'Network error: ' + err.message; })
            .finally(() => { btn.disabled = false; });
    });
})();
