(function () {
    const { url, nonce, jobState } = window.enaAjax || {};
    if (!url || !nonce) return;

    // ── State ─────────────────────────────────────────────────────────────────

    let pollTimer    = null;
    let elapsedTimer = null;
    let activeJobType = null;

    // ── Job bar ───────────────────────────────────────────────────────────────

    function setBar(cls, iconHtml, msg, timeText) {
        const bar = document.getElementById('ena-job-bar');
        if (!bar) return;
        bar.className = cls;
        document.getElementById('ena-job-icon').innerHTML    = iconHtml;
        document.getElementById('ena-job-msg').textContent   = msg;
        document.getElementById('ena-job-elapsed').textContent = timeText || '';
    }

    function clearBar() {
        const bar = document.getElementById('ena-job-bar');
        if (bar) bar.className = '';
    }

    // ── Button loading state ──────────────────────────────────────────────────

    function setButtonRunning(type) {
        activeJobType = type;
        const id  = type === 'collection' ? 'ena-btn-collection' : 'ena-btn-podcast';
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.dataset.originalText = btn.textContent;
        const label = type === 'collection' ? 'Collecting…' : 'Generating…';
        const spinnerClass = (id === 'ena-btn-collection') ? 'ena-btn-spinner' : 'ena-btn-spinner ena-btn-spinner--dark';
        btn.innerHTML = '<span class="' + spinnerClass + '"></span>' + label;
    }

    function restoreButtons() {
        ['ena-btn-collection', 'ena-btn-podcast'].forEach(id => {
            const btn = document.getElementById(id);
            if (btn && btn.dataset.originalText) {
                btn.textContent = btn.dataset.originalText;
                delete btn.dataset.originalText;
            }
        });
        activeJobType = null;
    }

    function setButtonsDisabled(disabled) {
        ['ena-btn-collection', 'ena-btn-podcast'].forEach(id => {
            const btn = document.getElementById(id);
            if (btn) btn.disabled = disabled;
        });
    }

    // ── Show states ───────────────────────────────────────────────────────────

    function showIdle() {
        stopTimers();
        clearBar();
        restoreButtons();
        setButtonsDisabled(false);
    }

    function showRunning(job) {
        const spinnerHtml = '<span class="ena-bar-spinner"></span>';
        const label = job.type === 'podcast' ? 'Generating podcast script' : 'Running collection';
        setBar('is-running', spinnerHtml, label + '…', '');
        setButtonRunning(job.type);
        setButtonsDisabled(true);
        if (job.started_at) startElapsedTimer(job.started_at);
    }

    function showDone(job) {
        stopTimers();
        restoreButtons();
        setButtonsDisabled(false);

        const result = job.result || {};
        let msg = job.type === 'podcast' ? 'Podcast script generated' : 'Collection complete';
        const parts = [];
        if (result.added   != null) parts.push(result.added   + ' added');
        if (result.removed != null) parts.push(result.removed + ' removed');
        if (result.synced  != null) parts.push(result.synced  + ' synced');
        if (parts.length) msg += ' · ' + parts.join(', ');

        const duration = (job.finished_at && job.started_at)
            ? 'took ' + (job.finished_at - job.started_at) + 's'
            : '';

        if (result.skipped) {
            msg += ' · ⚠ ' + result.skipped + ' skipped (' + (result.skip_summary || 'OpenRouter errors') + ')';
            setBar('is-warning', '⚠', msg, duration);
        } else {
            setBar('is-done', '✓', msg, duration);
        }
    }

    function showError(job) {
        stopTimers();
        restoreButtons();
        setButtonsDisabled(false);
        setBar('is-error', '✕', 'Error: ' + (job.error || 'unknown'), '');
    }

    // ── Timers ────────────────────────────────────────────────────────────────

    function stopTimers() {
        if (pollTimer)    { clearInterval(pollTimer);    pollTimer    = null; }
        if (elapsedTimer) { clearInterval(elapsedTimer); elapsedTimer = null; }
    }

    function startElapsedTimer(startedAt) {
        if (elapsedTimer) clearInterval(elapsedTimer);
        const tick = () => {
            const secs = Math.floor(Date.now() / 1000) - startedAt;
            document.getElementById('ena-job-elapsed').textContent = secs + 's';
        };
        tick();
        elapsedTimer = setInterval(tick, 1000);
    }

    // ── Polling ───────────────────────────────────────────────────────────────

    function applyJobState(job) {
        if      (job.status === 'idle')    showIdle();
        else if (job.status === 'running') showRunning(job);
        else if (job.status === 'done')    showDone(job);
        else if (job.status === 'error')   showError(job);
    }

    function startPolling() {
        if (pollTimer) return; // already polling
        pollTimer = setInterval(fetchStatus, 3000);
    }

    function fetchStatus() {
        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({ action: 'ena_job_status', nonce }),
        })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const job = data.data;
                applyJobState(job);
                if (job.status === 'running') {
                    startPolling();
                } else {
                    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
                }
            })
            .catch(() => {}); // silently ignore transient network errors during polling
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────

    function dispatch(job_type) {
        setButtonsDisabled(true);
        setBar('is-running', '<span class="ena-bar-spinner"></span>', 'Starting…', '');
        setButtonRunning(job_type);

        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({ action: 'ena_dispatch_job', nonce, job_type }),
        })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showError({ error: data.data || 'Dispatch failed' });
                    return;
                }
                const { dispatched, reason, job } = data.data;
                if (!dispatched && reason === 'already_running') {
                    applyJobState(job);
                } else {
                    applyJobState({ status: 'running', type: job_type, started_at: Math.floor(Date.now() / 1000) });
                }
                startPolling();
            })
            .catch(err => showError({ error: 'Network error: ' + err.message }));
    }

    // ── Button handlers ───────────────────────────────────────────────────────

    document.getElementById('ena-btn-collection')?.addEventListener('click', function () {
        if (!confirm('Run collection now?\n\nThis scrapes all sources, calls OpenRouter for each new article, and writes to Google Sheets. Running it multiple times in a row wastes API credits and may append duplicate articles.\n\nOnly run once unless you have a specific reason to re-run.')) return;
        dispatch('collection');
    });

    document.getElementById('ena-btn-podcast')?.addEventListener('click', function () {
        if (!confirm('Generate podcast script?\n\nBefore continuing, make sure:\n• You have created a new Google Doc for this session\n• The correct Document ID is saved in Settings → Podcast Script Document ID\n\nThe script will be appended to whatever doc ID is currently configured.')) return;
        dispatch('podcast');
    });

    // On page load: apply server-rendered state immediately (no async flash on refresh)
    if (jobState) {
        applyJobState(jobState);
        if (jobState.status === 'running') startPolling();
    }

    // Load OpenRouter account usage (credits used/remaining) immediately rather than
    // waiting for a manual "Refresh" click — it's the first thing worth seeing on load.
    if (document.getElementById('ena-account-card')) {
        fetchUsage();
    }

    // ── OpenRouter Usage ──────────────────────────────────────────────────────

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
