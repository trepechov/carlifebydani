<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
    <h1>EV News Automator — Settings</h1>

    <?php if ( isset( $_GET['updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'ena_save_settings', 'ena_settings_nonce' ); ?>
        <input type="hidden" name="action" value="ena_save_settings">

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="openrouter_api_key">OpenRouter API Key</label></th>
                <td>
                    <?php $has_key = ! empty( $settings->get( 'openrouter_api_key' ) ); ?>
                    <?php if ( $has_key ) : ?>
                        <div id="ena-key-set" style="display:flex;align-items:center;gap:10px;">
                            <input type="text" value="••••••••••••••••" disabled class="regular-text" style="color:#999;background:#f6f7f7;">
                            <a href="#" id="ena-key-change" style="white-space:nowrap;">Change key</a>
                        </div>
                        <div id="ena-key-input" style="display:none;">
                            <input type="password" name="openrouter_api_key" id="openrouter_api_key" value="" class="regular-text" autocomplete="new-password">
                            <a href="#" id="ena-key-cancel" style="margin-left:8px;">Cancel</a>
                        </div>
                    <?php else : ?>
                        <input type="password" name="openrouter_api_key" id="openrouter_api_key" value="" class="regular-text" autocomplete="new-password">
                        <p class="description">No API key set yet.</p>
                    <?php endif; ?>
                    <script>
                    (function(){
                        var changeBtn = document.getElementById('ena-key-change');
                        var cancelBtn = document.getElementById('ena-key-cancel');
                        if ( changeBtn ) {
                            changeBtn.addEventListener('click', function(e){
                                e.preventDefault();
                                document.getElementById('ena-key-set').style.display   = 'none';
                                document.getElementById('ena-key-input').style.display = 'block';
                                document.getElementById('openrouter_api_key').focus();
                            });
                        }
                        if ( cancelBtn ) {
                            cancelBtn.addEventListener('click', function(e){
                                e.preventDefault();
                                document.getElementById('ena-key-input').style.display = 'none';
                                document.getElementById('ena-key-set').style.display   = 'flex';
                            });
                        }
                    })();
                    </script>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="openrouter_model">OpenRouter Model</label></th>
                <td>
                    <input type="text" name="openrouter_model" id="openrouter_model"
                           value="<?php echo esc_attr( $settings->get( 'openrouter_model' ) ); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="spreadsheet_id">Google Spreadsheet ID</label></th>
                <td>
                    <input type="text" name="spreadsheet_id" id="spreadsheet_id"
                           value="<?php echo esc_attr( $settings->get( 'spreadsheet_id' ) ); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="podcast_doc_id">Podcast Script Document ID</label></th>
                <td>
                    <input type="text" name="podcast_doc_id" id="podcast_doc_id"
                           value="<?php echo esc_attr( $settings->get( 'podcast_doc_id' ) ); ?>" class="regular-text">
                    <p class="description">Before each recording session: create a new Google Doc inside your shared Drive folder, then paste its ID here.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="service_account_path">Service Account JSON Path</label></th>
                <td>
                    <input type="text" name="service_account_path" id="service_account_path"
                           value="<?php echo esc_attr( $settings->get( 'service_account_path' ) ); ?>" class="large-text">
                    <p class="description">Absolute server path. Must not be inside webroot.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ga4_property_id">GA4 Property ID</label></th>
                <td>
                    <input type="text" name="ga4_property_id" id="ga4_property_id"
                           value="<?php echo esc_attr( $settings->get( 'ga4_property_id' ) ); ?>" class="regular-text">
                    <p class="description">Numeric GA4 property ID (e.g. <code>123456789</code>). Found in Analytics → Admin → Property Settings. Leave blank to disable click sync.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="max_articles">Article limit</label></th>
                <td>
                    <input type="number" name="max_articles" id="max_articles" min="1" max="500"
                           value="<?php echo esc_attr( $settings->get( 'max_articles' ) ); ?>" class="small-text">
                    <p class="description">Maximum number of articles kept in the Google Sheet and displayed on the episode page. Oldest rows are removed automatically when this limit is exceeded.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="max_script_articles">Max articles on script</label></th>
                <td>
                    <input type="number" name="max_script_articles" id="max_script_articles" min="1" max="100"
                           value="<?php echo esc_attr( $settings->get( 'max_script_articles' ) ); ?>" class="small-text">
                    <p class="description">How many top articles to include when generating the podcast script.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="article_age_limit">Article Age Limit</label></th>
                <td>
                    <select name="article_age_limit" id="article_age_limit">
                        <?php
                        $age_options = [
                            '1d' => 'Last 24 hours',
                            '2d' => 'Last 2 days',
                            '3d' => 'Last 3 days',
                            '4d' => 'Last 4 days',
                            '5d' => 'Last 5 days',
                            '6d' => 'Last 6 days',
                            '1w' => 'Last 7 days (1 week)',
                        ];
                        $cur_age = $settings->get( 'article_age_limit' );
                        foreach ( $age_options as $val => $label ) :
                        ?>
                            <option value="<?php echo esc_attr( $val ); ?>"<?php selected( $cur_age, $val ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Only RSS articles published within this window are collected. HTML sources (no pub date) are always capped to the 5 most recent items regardless of this setting.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="collection_interval">Collection Interval</label></th>
                <td>
                    <select name="collection_interval" id="collection_interval">
                        <?php
                        $intervals = [
                            '15min'   => '15 minutes (dev)',
                            '30min'   => '30 minutes (dev)',
                            '1hour'   => '1 hour (dev)',
                            '6hours'  => '6 hours (staging)',
                            '12hours' => '12 hours',
                            'daily'   => 'Daily (production)',
                        ];
                        $cur_interval = $settings->get( 'collection_interval' );
                        foreach ( $intervals as $val => $label ) :
                        ?>
                            <option value="<?php echo esc_attr( $val ); ?>"<?php selected( $cur_interval, $val ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span style="margin-left:12px;">
                        at <input type="time" name="collection_time" id="collection_time"
                                  value="<?php echo esc_attr( $settings->get( 'collection_time', '09:00' ) ); ?>"
                                  <?php echo $cur_interval !== 'daily' ? 'disabled' : ''; ?>>
                        <span class="description" style="margin-left:6px;">
                            (site local time — currently <strong><?php echo esc_html( wp_date( 'H:i', null, wp_timezone() ) ); ?></strong> / <?php echo esc_html( wp_timezone_string() ); ?>).
                            If using a real server cron instead of WP-Cron, point it to <code>wp-cron.php</code> at this same time.
                        </span>
                    </span>
                    <script>
                    (function(){
                        var sel = document.getElementById('collection_interval');
                        var inp = document.getElementById('collection_time');
                        if ( sel && inp ) {
                            sel.addEventListener('change', function(){
                                inp.disabled = this.value !== 'daily';
                            });
                        }
                    })();
                    </script>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="sources">News Sources</label></th>
                <td>
                    <textarea name="sources" id="sources" rows="10" class="large-text"><?php echo esc_textarea( $settings->get( 'sources' ) ); ?></textarea>
                    <p class="description">One source per line: <code>https://example.com/feed rss</code> or <code>https://example.com html</code>. Method defaults to rss.</p>
                </td>
            </tr>
        </table>

        <?php submit_button( 'Save Settings' ); ?>
    </form>
</div>
