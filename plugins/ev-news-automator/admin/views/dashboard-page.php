<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap" id="ena-dashboard">
    <h1>EV News Automator — Dashboard</h1>

    <div class="postbox" style="margin-bottom:24px;padding:16px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
            <h2 style="margin:0;font-size:16px;">Pipeline Status</h2>
            <div style="display:flex;gap:8px;align-items:center;">
                <button class="button button-primary" id="ena-btn-collection">Run Collection Now</button>
                <button class="button" id="ena-btn-podcast">Generate Podcast Script</button>
            </div>
        </div>

        <div id="ena-job-bar">
            <span id="ena-job-icon"></span>
            <span id="ena-job-msg"></span>
            <span id="ena-job-elapsed"></span>
        </div>

        <div style="display:flex;gap:20px;flex-wrap:wrap;">
        <?php
        $sheet_id       = $settings->get( 'spreadsheet_id' );
        $sheet_url      = $sheet_id ? 'https://docs.google.com/spreadsheets/d/' . $sheet_id . '/edit' : '';
        // Overridden with the exact tab URL (#gid) stored during the last sync run.
        $sync_status    = $logger->get_status( ENA_OPT_STATUS_SYNC );
        if ( ! empty( $sync_status['sheet_url'] ) ) {
            $sheet_url = $sync_status['sheet_url'];
        }
        $podcast_doc_id = $settings->get( 'podcast_doc_id' );
        $podcast_doc_url = $podcast_doc_id ? 'https://docs.google.com/document/d/' . $podcast_doc_id . '/edit' : '';

        $field_labels = [
            'Last Sync' => [
                'added'   => 'Added',
                'removed' => 'Removed',
            ],
            'Collection' => [
                'count'       => 'Count',
                'with_clicks' => 'With clicks',
                'zero_clicks' => 'Zero clicks',
            ],
            'Podcast Script' => [
                'count'      => 'Limit',
                'top_clicks' => 'Click events',
            ],
        ];

        $statuses = [
            'Last Sync'      => [ ENA_OPT_STATUS_COLLECTION, [ 'added', 'removed' ] ],
            'Collection'     => [ ENA_OPT_STATUS_SYNC,       [ 'count', 'with_clicks', 'zero_clicks' ] ],
            'Podcast Script' => [ ENA_OPT_STATUS_PODCAST,    [ 'count', 'top_clicks' ] ],
        ];
        foreach ( $statuses as $label => [ $key, $fields ] ) :
            $status = $logger->get_status( $key );
            $has_data = ! empty( $status['timestamp'] );
        ?>
        <div class="postbox" style="min-width:200px;flex:1;padding:12px 16px;">
            <h3 style="margin:0 0 8px;font-size:14px;"><?php echo esc_html( $label ); ?></h3>
            <?php if ( $has_data ) : ?>
                <?php foreach ( $fields as $f ) : if ( isset( $status[ $f ] ) ) : ?>
                    <p style="margin:4px 0;">
                        <strong><?php echo esc_html( $field_labels[ $label ][ $f ] ?? $f ); ?>:</strong>
                        <?php echo esc_html( $status[ $f ] ); ?>
                    </p>
                <?php endif; endforeach; ?>
                <?php if ( $label !== 'Collection' && ! empty( $status['timestamp'] ) ) : ?>
                    <p style="margin:4px 0;">
                        <strong>Run at:</strong>
                        <?php echo esc_html( date_i18n( 'j M Y · H:i', strtotime( $status['timestamp'] ) ) ); ?>
                    </p>
                <?php endif; ?>
                <?php if ( $label === 'Last Sync' && ! empty( $status['skipped'] ) ) : ?>
                    <p style="margin:8px 0 0;padding:6px 8px;background:#fef8ee;border-left:3px solid #dba617;color:#8a5a00;font-size:12px;">
                        ⚠ <?php echo esc_html( $status['skipped'] ); ?> article(s) skipped —
                        <?php echo esc_html( $status['skip_summary'] ?? '' ); ?>.
                        <a href="#ena-account-card">Check OpenRouter account</a>.
                    </p>
                <?php endif; ?>
                <?php if ( $label === 'Collection' && $sheet_url ) : ?>
                    <p style="margin:4px 0;">
                        <strong>Sheet URL:</strong>
                        <a href="<?php echo esc_url( $sheet_url ); ?>" target="_blank">Open Sheet</a>
                    </p>
                <?php endif; ?>
                <?php if ( $label === 'Podcast Script' && $podcast_doc_url ) : ?>
                    <p style="margin:4px 0;">
                        <strong>Script:</strong>
                        <a href="<?php echo esc_url( $podcast_doc_url ); ?>" target="_blank">Open Doc</a>
                    </p>
                <?php endif; ?>
            <?php else : ?>
                <p style="color:#999;">No runs yet.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>
    </div>

    <?php
    $ai_stats     = ENA_OpenRouter::get_local_stats();
    $total_calls  = (int) $ai_stats['total_calls'];
    $summ_calls   = (int) $ai_stats['summarize_calls'];
    $pod_calls    = (int) $ai_stats['podcast_calls'];
    $prompt_tok   = (int) $ai_stats['total_prompt_tokens'];
    $compl_tok    = (int) $ai_stats['total_completion_tokens'];
    $total_tok    = (int) $ai_stats['total_tokens'];
    $words_gen    = (int) round( $compl_tok * 0.75 );
    $pages_gen    = $words_gen > 0 ? round( $words_gen / 250, 1 ) : 0;
    $reading_min  = $words_gen > 0 ? (int) ceil( $words_gen / 200 ) : 0;
    $avg_summ_tok = $summ_calls > 0 ? (int) round( (int) $ai_stats['summarize_completion_tokens'] / $summ_calls ) : 0;
    $avg_pod_tok  = $pod_calls  > 0 ? (int) round( (int) $ai_stats['podcast_completion_tokens']   / $pod_calls  ) : 0;
    ?>

    <div class="postbox ena-usage-box" style="margin-bottom:24px;padding:16px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <h2 style="margin:0;font-size:16px;">AI Usage Stats</h2>
            <div style="display:flex;gap:8px;align-items:center;">
                <button id="ena-btn-usage-refresh" class="button">&#8635; Refresh OpenRouter data</button>
                <button id="ena-btn-usage-reset" class="button" onclick="return confirm('Reset all local token counters?');">Reset local stats</button>
                <span id="ena-usage-status" style="font-size:12px;color:#999;"></span>
            </div>
        </div>

        <div style="display:flex;gap:20px;flex-wrap:wrap;">

            <!-- Account card — populated by JS on refresh -->
            <div class="postbox" id="ena-account-card" style="min-width:200px;flex:1;padding:12px 16px;">
                <h3 style="margin:0 0 10px;font-size:14px;">OpenRouter Account</h3>
                <p style="color:#999;font-size:12px;margin:0;">Click "Refresh" to load live account data.</p>
            </div>

            <!-- Plugin token counters — rendered from WP option on page load -->
            <div class="postbox" style="min-width:200px;flex:1;padding:12px 16px;">
                <h3 style="margin:0 0 10px;font-size:14px;">Plugin Activity</h3>
                <?php if ( $total_calls === 0 ) : ?>
                    <p style="color:#999;margin:0;">No AI calls recorded yet.</p>
                <?php else : ?>
                    <table class="ena-stat-table">
                        <tr><td>Total AI calls</td><td><?php echo esc_html( number_format( $total_calls ) ); ?></td></tr>
                        <tr><td>&nbsp;&nbsp;↳ Summaries</td><td><?php echo esc_html( number_format( $summ_calls ) ); ?></td></tr>
                        <tr><td>&nbsp;&nbsp;↳ Podcast scripts</td><td><?php echo esc_html( number_format( $pod_calls ) ); ?></td></tr>
                        <tr><td colspan="2"><hr style="margin:6px 0;border:0;border-top:1px solid #ddd;"></td></tr>
                        <tr><td>Prompt tokens</td><td><?php echo esc_html( number_format( $prompt_tok ) ); ?></td></tr>
                        <tr><td>Generated tokens</td><td><?php echo esc_html( number_format( $compl_tok ) ); ?></td></tr>
                        <tr><td>Total tokens</td><td><strong><?php echo esc_html( number_format( $total_tok ) ); ?></strong></td></tr>
                    </table>
                    <p style="margin:8px 0 0;font-size:11px;color:#999;">
                        <?php if ( $ai_stats['first_call_at'] ) : ?>
                            Since <?php echo esc_html( date_i18n( 'j M Y', strtotime( $ai_stats['first_call_at'] ) ) ); ?>
                            &nbsp;&middot;&nbsp;
                            Last: <?php echo esc_html( date_i18n( 'j M H:i', strtotime( $ai_stats['last_call_at'] ) ) ); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Fun facts -->
            <?php if ( $total_calls > 0 ) : ?>
            <div class="postbox" style="min-width:200px;flex:1;padding:12px 16px;background:linear-gradient(135deg,#f0f9ff 0%,#e8f5e9 100%);">
                <h3 style="margin:0 0 10px;font-size:14px;">&#127881; AI Fun Facts</h3>
                <ul style="margin:0;padding-left:18px;font-size:13px;line-height:1.8;">
                    <li>~<strong><?php echo esc_html( number_format( $words_gen ) ); ?> words</strong> generated in Bulgarian</li>
                    <?php if ( $pages_gen >= 1 ) : ?>
                    <li>That&rsquo;s about <strong><?php echo esc_html( $pages_gen ); ?> pages</strong> of text</li>
                    <?php endif; ?>
                    <?php if ( $reading_min >= 1 ) : ?>
                    <li><strong><?php echo esc_html( $reading_min ); ?> min</strong> of reading material created</li>
                    <?php endif; ?>
                    <?php if ( $summ_calls > 0 ) : ?>
                    <li><strong><?php echo esc_html( number_format( $summ_calls ) ); ?></strong> EV articles auto-translated &amp; summarized</li>
                    <?php if ( $avg_summ_tok > 0 ) : ?>
                    <li>~<?php echo esc_html( number_format( $avg_summ_tok ) ); ?> tokens per summary on average</li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if ( $pod_calls > 0 ) : ?>
                    <li><strong><?php echo esc_html( number_format( $pod_calls ) ); ?></strong> podcast script<?php echo $pod_calls > 1 ? 's' : ''; ?> drafted for Car Life by Dani</li>
                    <?php if ( $avg_pod_tok > 0 ) : ?>
                    <li>~<?php echo esc_html( number_format( $avg_pod_tok ) ); ?> tokens per podcast script</li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <h2>Run Log</h2>
    <?php $log = $logger->all(); ?>
    <?php if ( empty( $log ) ) : ?>
        <p>No log entries yet.</p>
    <?php else : ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Time</th><th>Trigger</th><th>Phase</th><th>Level</th><th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $log as $entry ) : ?>
                    <tr>
                        <td><?php echo esc_html( $entry['time'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['trigger'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['phase'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['level'] ?? '' ); ?></td>
                        <td><?php echo esc_html( $entry['message'] ?? '' ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2 style="margin-top:24px;">Last Run Transcript</h2>
    <?php $transcript = $logger->transcript(); ?>
    <?php if ( empty( $transcript ) ) : ?>
        <p>No transcript yet.</p>
    <?php else : ?>
        <details>
            <summary>Show transcript (<?php echo count( $transcript ); ?> steps)</summary>
            <table class="widefat striped" style="margin-top:8px;">
                <thead>
                    <tr><th>Time</th><th>Step</th><th>Status</th><th>Detail</th></tr>
                </thead>
                <tbody>
                    <?php foreach ( $transcript as $entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( $entry['time'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['step'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['status'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['detail'] ?? '' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </details>
    <?php endif; ?>
</div>
