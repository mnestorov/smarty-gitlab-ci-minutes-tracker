<?php
/**
 * Plugin Name:  SM - GitLab CI/CD Minutes Tracker
 * Description:  Tracks Compute Usage quotas for GitLab namespaces and groups (matches Usage Quotas dashboard).
 * Version:      3.1.0
 * Author:       Smarty Studio | Martin Nestorov
 * Author URI:   https://github.com/mnestorov
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  smarty-gitlab-ci-minutes-tracker
 * Domain Path:  /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'SMARTY_GL_SETTINGS', 'smarty_gl_settings' );
define( 'SMARTY_GL_VERSION', '3.1.0' );
define( 'SMARTY_GL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMARTY_GL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load plugin text domain for translations.
 */
function smarty_gl_load_textdomain() {
    load_plugin_textdomain( 'smarty-gitlab-ci-minutes-tracker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'smarty_gl_load_textdomain' );

/**
 * Adds the main menu and settings pages to the WordPress admin menu.
 */
function smarty_gl_register_menu_pages() {
    add_menu_page(
        __( 'GitLab CI Tracker', 'smarty-gitlab-ci-minutes-tracker' ),
        __( 'GitLab CI', 'smarty-gitlab-ci-minutes-tracker' ),
        'manage_options',
        'smarty-gitlab-settings',
        'smarty_gl_settings_page',
        'dashicons-chart-line',
        30
    );
}
add_action( 'admin_menu', 'smarty_gl_register_menu_pages' );

/**
 * Initialize admin settings on admin_init.
 */
add_action( 'admin_init', 'smarty_gl_admin_init' );

/**
 * Enqueue admin styles and scripts on admin pages.
 */
add_action( 'admin_enqueue_scripts', 'smarty_gl_enqueue_admin_styles' );

/**
 * Initializes admin settings.
 */
function smarty_gl_admin_init() {
    // Register settings with proper option group
    register_setting( 'smarty_gl_settings_group', SMARTY_GL_SETTINGS, 'smarty_gl_sanitize_settings' );
    
    add_settings_section( 'smarty_gl_api_section', __('API Configuration', 'smarty-gitlab-ci-minutes-tracker'), 'smarty_gl_api_section_callback', 'smarty-gitlab-settings' );
    add_settings_field( 'gitlab_token', __( 'GitLab Personal Access Token', 'smarty-gitlab-ci-minutes-tracker' ), 'smarty_gl_token_callback', 'smarty-gitlab-settings', 'smarty_gl_api_section' );
    
    add_settings_section( 'smarty_gl_sources_section', __('Tracked Sources', 'smarty-gitlab-ci-minutes-tracker'), 'smarty_gl_sources_section_callback', 'smarty-gitlab-settings' );
    add_settings_field( 'gitlab_sources', __('Namespaces/Groups', 'smarty-gitlab-ci-minutes-tracker'), 'smarty_gl_sources_repeater_callback', 'smarty-gitlab-settings', 'smarty_gl_sources_section' );
    
    add_settings_section( 'smarty_gl_notifications_section', __('Notifications', 'smarty-gitlab-ci-minutes-tracker'), 'smarty_gl_notifications_section_callback', 'smarty-gitlab-settings' );
    add_settings_field( 'slack_webhook_url', __( 'Slack Webhook URL', 'smarty-gitlab-ci-minutes-tracker' ), 'smarty_gl_slack_webhook_callback', 'smarty-gitlab-settings', 'smarty_gl_notifications_section');
}

/**
 * Enqueue admin styles and scripts.
 */
function smarty_gl_enqueue_admin_styles( $hook ) {
    // Only load on our plugin pages and dashboard
    if ( $hook === 'toplevel_page_smarty-gitlab-settings' || $hook === 'index.php' ) {
        wp_enqueue_style( 'smarty-gl-admin', SMARTY_GL_PLUGIN_URL . 'css/smarty-gl-admin.css', [], SMARTY_GL_VERSION );
        wp_enqueue_script( 'smarty-gl-admin', SMARTY_GL_PLUGIN_URL . 'js/smarty-gl-admin.js', ['jquery'], SMARTY_GL_VERSION, true );
    }
}

/**
 * Section callbacks for settings.
 */
function smarty_gl_api_section_callback() {
    echo '<div class="smarty-gl-description">';
    echo '<div class="smarty-gl-alert smarty-gl-alert-info">';
    echo '<strong>📋 ' . esc_html__('Setup Instructions:', 'smarty-gitlab-ci-minutes-tracker') . '</strong><br>';
    echo esc_html__('1. Go to GitLab → User Settings → Access Tokens', 'smarty-gitlab-ci-minutes-tracker') . '<br>';
    echo esc_html__('2. Create a token with "read_api" scope', 'smarty-gitlab-ci-minutes-tracker') . '<br>';
    echo esc_html__('3. Copy and paste the token below', 'smarty-gitlab-ci-minutes-tracker');
    echo '</div></div>';
}

function smarty_gl_sources_section_callback() {
    echo '<div class="smarty-gl-description">';
    echo esc_html__('Add GitLab groups or namespaces to track compute usage. This data matches what you see in GitLab\'s Usage Quotas dashboard.', 'smarty-gitlab-ci-minutes-tracker');
    echo '</div>';
}

function smarty_gl_notifications_section_callback() {
    echo '<div class="smarty-gl-description">';
    echo esc_html__('Configure notifications for usage alerts and reports.', 'smarty-gitlab-ci-minutes-tracker');
    echo '</div>';
}

/**
 * Field callbacks for settings.
 */
function smarty_gl_token_callback() {
    $options = get_option( SMARTY_GL_SETTINGS );
    $token = $options['gitlab_token'] ?? '';
    
    echo '<div class="smarty-gl-form-group">';
    printf( 
        '<input type="password" name="%s[gitlab_token]" value="%s" placeholder="%s" required class="code" />',
        esc_attr(SMARTY_GL_SETTINGS),
        esc_attr($token),
        esc_attr__('glpat-xxxxxxxxxxxxxxxxxxxx', 'smarty-gitlab-ci-minutes-tracker')
    );
    echo '</div>';
}

function smarty_gl_sources_repeater_callback() {
    $options = get_option( SMARTY_GL_SETTINGS );
    $sources = $options['sources'] ?? [];
    
    echo '<div class="smarty-gl-sources-container">';
    
    if (empty($sources)) {
        $sources = [['name' => '', 'type' => 'group', 'identifier' => '']];
    }
    
    foreach ($sources as $index => $source) {
        echo '<div class="smarty-gl-source-row">';
        echo '<div class="smarty-gl-source-fields">';
        
        echo '<div class="smarty-gl-source-field">';
        echo '<label>' . esc_html__('Display Name', 'smarty-gitlab-ci-minutes-tracker') . '</label>';
        printf(
            '<input type="text" name="%s[sources][%d][name]" value="%s" placeholder="%s" required />',
            esc_attr(SMARTY_GL_SETTINGS),
            $index,
            esc_attr($source['name'] ?? ''),
            esc_attr__('My Project', 'smarty-gitlab-ci-minutes-tracker')
        );
        echo '</div>';
        
        echo '<div class="smarty-gl-source-field">';
        echo '<label>' . esc_html__('Type', 'smarty-gitlab-ci-minutes-tracker') . '</label>';
        printf(
            '<select name="%s[sources][%d][type]" required>',
            esc_attr(SMARTY_GL_SETTINGS),
            $index
        );
        echo '<option value="group"' . selected($source['type'] ?? '', 'group', false) . '>' . esc_html__('Group', 'smarty-gitlab-ci-minutes-tracker') . '</option>';
        echo '<option value="namespace"' . selected($source['type'] ?? '', 'namespace', false) . '>' . esc_html__('Namespace', 'smarty-gitlab-ci-minutes-tracker') . '</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="smarty-gl-source-field">';
        echo '<label>' . esc_html__('GitLab ID/Path', 'smarty-gitlab-ci-minutes-tracker') . '</label>';
        printf(
            '<input type="text" name="%s[sources][%d][identifier]" value="%s" placeholder="%s" required />',
            esc_attr(SMARTY_GL_SETTINGS),
            $index,
            esc_attr($source['identifier'] ?? ''),
            esc_attr__('group-name or 12345', 'smarty-gitlab-ci-minutes-tracker')
        );
        echo '</div>';
        
        echo '<div class="smarty-gl-source-field">';
        if (count($sources) > 1) {
            echo '<button type="button" class="smarty-gl-btn smarty-gl-btn-danger smarty-gl-remove-source">' . esc_html__('Remove', 'smarty-gitlab-ci-minutes-tracker') . '</button>';
        }
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '<button type="button" class="smarty-gl-btn smarty-gl-btn-secondary smarty-gl-add-source">+ ' . esc_html__('Add Source', 'smarty-gitlab-ci-minutes-tracker') . '</button>';
}

function smarty_gl_slack_webhook_callback() {
    $options = get_option( SMARTY_GL_SETTINGS );
    $url = $options['slack_webhook_url'] ?? '';
    
    echo '<div class="smarty-gl-form-group">';
    printf( 
        '<input type="url" name="%s[slack_webhook_url]" value="%s" placeholder="%s" />',
        esc_attr(SMARTY_GL_SETTINGS),
        esc_attr($url),
        esc_attr__('https://hooks.slack.com/services/...', 'smarty-gitlab-ci-minutes-tracker')
    );
    echo '</div>';
}

/**
 * Sanitize settings input.
 */
function smarty_gl_sanitize_settings( $input ) {
    $new_input = [];
    
    $new_input['gitlab_token'] = isset($input['gitlab_token']) ? sanitize_text_field( $input['gitlab_token'] ) : '';
    
    if (isset($input['sources']) && is_array($input['sources'])) {
        $new_input['sources'] = [];
        foreach ($input['sources'] as $source) {
            if (!empty($source['name']) && !empty($source['identifier'])) {
                $new_input['sources'][] = [
                    'name' => sanitize_text_field($source['name']),
                    'type' => sanitize_text_field($source['type']),
                    'identifier' => sanitize_text_field($source['identifier'])
                ];
            }
        }
    }
    
    $new_input['slack_webhook_url'] = isset($input['slack_webhook_url']) ? esc_url_raw( $input['slack_webhook_url'] ) : '';
    
    return $new_input;
}

/**
 * Settings page HTML.
 */
function smarty_gl_settings_page() {
    if (isset($_GET['settings-updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully!', 'smarty-gitlab-ci-minutes-tracker') . '</p></div>';
    }
?>
    <div class="smarty-gl-container">
        <div class="smarty-gl-header">
            <h1><?php esc_html_e('GitLab CI/CD Minutes Tracker | Settings', 'smarty-gitlab-ci-minutes-tracker'); ?></h1>
            <p><?php esc_html_e('Monitor your GitLab compute usage quotas with real-time pipeline analytics', 'smarty-gitlab-ci-minutes-tracker'); ?></p>
        </div>
        
        <div class="smarty-gl-content">
            <form method="post" action="options.php">
                <?php 
                settings_fields( 'smarty_gl_settings_group' );
                
                $sections = [
                    'smarty_gl_api_section' => __('API Configuration', 'smarty-gitlab-ci-minutes-tracker'),
                    'smarty_gl_sources_section' => __('Tracked Sources', 'smarty-gitlab-ci-minutes-tracker'),
                    'smarty_gl_notifications_section' => __('Notifications', 'smarty-gitlab-ci-minutes-tracker')
                ];
                
                foreach ($sections as $section_id => $section_title) {
                    echo '<div class="smarty-gl-section">';
                    echo '<h2>' . esc_html($section_title) . '</h2>';
                    do_settings_fields( 'smarty-gitlab-settings', $section_id );
                    echo '</div>';
                }
                ?>
                
                <div class="smarty-gl-section">
                    <?php submit_button(__('Save Settings', 'smarty-gitlab-ci-minutes-tracker'), 'primary', 'submit', false, ['class' => 'smarty-gl-btn smarty-gl-btn-primary']); ?>
                </div>
            </form>
        </div>
    </div>
<?php
}

/**
 * Fetch GitLab data for a specific source.
 */
function smarty_gl_fetch_gitlab_data( $type, $identifier, $token ) {
    $cache_key = 'smarty_gl_data_' . md5($type . '_' . $identifier);
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $result_data = [
        'compute_used' => 0,
        'compute_limit' => 0,
        'last_updated' => current_time('mysql'),
        'error' => null,
        'status' => 'unknown'
    ];
    
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json'
        ],
        'timeout' => 30
    ];
    
    try {
        if ($type === 'group') {
            // Use Groups API for group-level quotas
            $api_url = sprintf('https://gitlab.com/api/v4/groups/%s?statistics=true', urlencode($identifier));
            $response = wp_remote_get( $api_url, $args );
            
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                
                if (isset($body['shared_runners_minutes_limit'])) {
                    $result_data['compute_limit'] = (int)$body['shared_runners_minutes_limit'];
                    
                    // Calculate current usage from group projects
                    $total_usage = smarty_gl_calculate_pipeline_usage($identifier, $token, $args);
                    $result_data['compute_used'] = $total_usage;
                    $result_data['status'] = 'success';
                    
                    error_log('GitLab Group Usage Calculated: ' . $total_usage . ' minutes for group: ' . $identifier);
                } else {
                    $result_data['error'] = __('Group data not found or insufficient permissions', 'smarty-gitlab-ci-minutes-tracker');
                    $result_data['status'] = 'error';
                }
            } else {
                $result_data['error'] = $response->get_error_message();
                $result_data['status'] = 'error';
            }
        } else {
            // Handle namespace type
            $result_data['error'] = __('Namespace tracking not fully implemented yet', 'smarty-gitlab-ci-minutes-tracker');
            $result_data['status'] = 'warning';
        }
        
    } catch (Exception $e) {
        $result_data['error'] = $e->getMessage();
        $result_data['status'] = 'error';
        error_log('GitLab API Error: ' . $e->getMessage());
    }
    
    set_transient($cache_key, $result_data, 300); // Cache for 5 minutes
    return $result_data;
}

/**
 * Calculate pipeline usage for current month.
 */
function smarty_gl_calculate_pipeline_usage($group_identifier, $token, $args) {
    $projects_url = sprintf('https://gitlab.com/api/v4/groups/%s/projects?include_subgroups=true&per_page=100', urlencode($group_identifier));
    $projects_response = wp_remote_get( $projects_url, $args );
    
    if (is_wp_error($projects_response)) {
        return 0;
    }
    
    $projects = json_decode(wp_remote_retrieve_body($projects_response), true);
    if (!is_array($projects)) {
        return 0;
    }
    
    $total_minutes = 0;
    $current_month_start = date('Y-m-01T00:00:00Z');
    
    foreach ($projects as $project) {
        $project_id = $project['id'];
        
        // Get pipelines for current month
        $pipelines_url = sprintf(
            'https://gitlab.com/api/v4/projects/%s/pipelines?updated_after=%s&per_page=100', 
            $project_id,
            urlencode($current_month_start)
        );
        
        $pipelines_response = wp_remote_get( $pipelines_url, $args );
        if (is_wp_error($pipelines_response)) {
            continue;
        }
        
        $pipelines = json_decode(wp_remote_retrieve_body($pipelines_response), true);
        if (!is_array($pipelines)) {
            continue;
        }
        
        foreach ($pipelines as $pipeline) {
            $pipeline_id = $pipeline['id'];
            
            // Get jobs for this pipeline
            $jobs_url = sprintf('https://gitlab.com/api/v4/projects/%s/pipelines/%s/jobs', $project_id, $pipeline_id);
            $jobs_response = wp_remote_get( $jobs_url, $args );
            
            if (is_wp_error($jobs_response)) {
                continue;
            }
            
            $jobs = json_decode(wp_remote_retrieve_body($jobs_response), true);
            if (!is_array($jobs)) {
                continue;
            }
            
            foreach ($jobs as $job) {
                // Only count shared runner jobs
                if (isset($job['runner']['is_shared']) && $job['runner']['is_shared'] === true) {
                    if (isset($job['duration']) && is_numeric($job['duration'])) {
                        $total_minutes += ceil($job['duration'] / 60); // Convert seconds to minutes
                    }
                }
            }
        }
    }
    
    error_log('Calculated usage from pipelines: ' . $total_minutes . ' minutes from ' . count($projects) . ' projects');
    return $total_minutes;
}

/**
 * Add dashboard widget.
 */
function smarty_gl_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'smarty_gl_dashboard_widget',
        __('GitLab CI Usage', 'smarty-gitlab-ci-minutes-tracker'),
        'smarty_gl_dashboard_widget_content'
    );
}
add_action( 'wp_dashboard_setup', 'smarty_gl_add_dashboard_widget' );

/**
 * Dashboard widget content.
 */
function smarty_gl_dashboard_widget_content() {
    $options = get_option( SMARTY_GL_SETTINGS );
    $token = $options['gitlab_token'] ?? '';
    $sources = $options['sources'] ?? [];
    
    echo '<div class="smarty-gl-widget">';
    
    if (empty($token)) {
        echo '<div class="smarty-gl-setup-card">';
        echo '<div class="smarty-gl-setup-icon">⚙️</div>';
        echo '<h3 class="smarty-gl-setup-title">' . esc_html__('Setup Required', 'smarty-gitlab-ci-minutes-tracker') . '</h3>';
        echo '<p class="smarty-gl-setup-description">' . esc_html__('Please configure your GitLab Personal Access Token to start tracking compute usage.', 'smarty-gitlab-ci-minutes-tracker') . '</p>';
        echo '<a href="' . admin_url('admin.php?page=smarty-gitlab-settings') . '" class="smarty-gl-setup-button">' . esc_html__('Configure Settings', 'smarty-gitlab-ci-minutes-tracker') . '</a>';
        echo '</div>';
        echo '</div>';
        return;
    }
    
    if (empty($sources)) {
        echo '<div class="smarty-gl-setup-card">';
        echo '<div class="smarty-gl-setup-icon">📊</div>';
        echo '<h3 class="smarty-gl-setup-title">' . esc_html__('No Sources Configured', 'smarty-gitlab-ci-minutes-tracker') . '</h3>';
        echo '<p class="smarty-gl-setup-description">' . esc_html__('Add GitLab groups or namespaces to start tracking their compute usage quotas.', 'smarty-gitlab-ci-minutes-tracker') . '</p>';
        echo '<a href="' . admin_url('admin.php?page=smarty-gitlab-settings') . '" class="smarty-gl-setup-button">' . esc_html__('Add Sources', 'smarty-gitlab-ci-minutes-tracker') . '</a>';
        echo '</div>';
        echo '</div>';
        return;
    }
    
    // Data table for detailed view
    echo '<table class="smarty-gl-data-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . esc_html__('Source', 'smarty-gitlab-ci-minutes-tracker') . '</th>';
    echo '<th>' . esc_html__('Usage', 'smarty-gitlab-ci-minutes-tracker') . '</th>';
    echo '<th>' . esc_html__('Limit', 'smarty-gitlab-ci-minutes-tracker') . '</th>';
    echo '<th>' . esc_html__('Progress', 'smarty-gitlab-ci-minutes-tracker') . '</th>';
    echo '<th>' . esc_html__('Status', 'smarty-gitlab-ci-minutes-tracker') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($sources as $source) {
        $data = smarty_gl_fetch_gitlab_data($source['type'], $source['identifier'], $token);
        
        $usage_percent = $data['compute_limit'] > 0 ? ($data['compute_used'] / $data['compute_limit']) * 100 : 0;
        $usage_percent = min(100, $usage_percent);
        
        echo '<tr>';
        echo '<td>';
        echo '<strong>' . esc_html($source['name']) . '</strong><br>';
        echo '<small style="color: #646970;">' . esc_html(ucfirst($source['type'])) . '</small>';
        echo '</td>';
        echo '<td>' . esc_html(number_format($data['compute_used'])) . '</td>';
        echo '<td>' . esc_html(number_format($data['compute_limit'])) . '</td>';
        echo '<td>';
        echo '<div style="background: #f0f0f1; border-radius: 10px; height: 8px; overflow: hidden;">';
        echo '<div style="background: #0073aa; height: 100%; width: ' . esc_attr($usage_percent) . '%; transition: width 0.3s ease;"></div>';
        echo '</div>';
        echo '<small style="color: #646970;">' . esc_html(round($usage_percent)) . '%</small>';
        echo '</td>';
        echo '<td>';
        
        if ($data['error']) {
            echo '<span class="smarty-gl-status-badge smarty-gl-status-error">' . esc_html__('Error', 'smarty-gitlab-ci-minutes-tracker') . '</span>';
        } elseif ($usage_percent >= 90) {
            echo '<span class="smarty-gl-status-badge smarty-gl-status-warning">' . esc_html__('High', 'smarty-gitlab-ci-minutes-tracker') . '</span>';
        } else {
            echo '<span class="smarty-gl-status-badge smarty-gl-status-success">' . esc_html__('Normal', 'smarty-gitlab-ci-minutes-tracker') . '</span>';
        }
        
        echo '</td>';
        echo '</tr>';
        
        if ($data['error']) {
            echo '<tr>';
            echo '<td colspan="5" style="padding: 8px 16px; background: #fcf0f1; border-left: 3px solid #d63638; font-size: 12px; color: #b32d2e;">';
            echo esc_html($data['error']);
            echo '</td>';
            echo '</tr>';
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    
    echo '<p style="text-align: center; margin-top: 16px; color: #646970; font-size: 12px;">';
    echo esc_html__('Last updated:', 'smarty-gitlab-ci-minutes-tracker') . ' ' . esc_html(current_time('M j, Y g:i A'));
    echo ' • <a href="' . admin_url('admin.php?page=smarty-gitlab-settings') . '" style="color: #0073aa;">' . esc_html__('Settings', 'smarty-gitlab-ci-minutes-tracker') . '</a>';
    echo '</p>';
    
    echo '</div>';
}

