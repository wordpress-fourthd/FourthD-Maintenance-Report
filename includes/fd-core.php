<?php
defined('ABSPATH') || exit;

/**
 * Get WordPress Core Information.
 */
function fd_get_wp_core_info() {
    global $wpdb;

    return [
        'site_url'       => get_site_url(),
        'wp_version'     => get_bloginfo('version'),
        'php_version'    => phpversion(),
        'mysql_version'  => $wpdb->db_version(),
        'server_software'=> $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'ssl_enabled'    => is_ssl() ? 'Yes' : 'No',
        'language'       => get_locale(),
        'timezone'       => get_option('timezone_string') ?: 'Not Set',
        'debug_mode'     => defined('WP_DEBUG') && WP_DEBUG,
        'multisite'      => is_multisite(),
        'table_prefix'   => $wpdb->prefix,
    ];
}

function fd_get_wp_config_info() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $user_count     = count_users();
    $all_plugins    = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    return [
        'permalink_structure'     => get_option('permalink_structure'),
        'membership'              => get_option('users_can_register') ? 'Yes' : 'No',
        'discourage_search'       => get_option('blog_public') ? 'No' : 'Yes',
        'default_comment_status'  => get_option('default_comment_status'),
        'total_users'             => $user_count['total_users'] ?? 0,
        'total_plugins'           => count($all_plugins),
        'active_plugins'          => count($active_plugins),
    ];
}


/**
 * Get Active Theme Info.
 */
function fd_get_active_theme_info() {
    $theme = wp_get_theme();
    return [
        'name'       => $theme->get('Name'),
        'version'    => $theme->get('Version'),
        'author'     => $theme->get('Author'),
        'template'   => $theme->get_template(),
        'stylesheet' => $theme->get_stylesheet(),
    ];
}

/**
 * Get Plugin Info.
 */
function fd_get_plugin_info() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active = get_option('active_plugins', []);

    return array_map(function ($file, $plugin) use ($active) {
        return [
            'name'    => $plugin['Name'],
            'version' => $plugin['Version'],
            'active'  => in_array($file, $active),
            'author'  => $plugin['Author'],
        ];
    }, array_keys($plugins), $plugins);
}

/**
 * Get Folder Size Recursively.
 */
function fd_get_folder_size($path) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

/**
 * Convert bytes to human-readable format.
 */
function fd_format_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Get disk usage data for various WP directories and DB.
 */
function fd_get_disk_usage_info() {
    global $wpdb;

    $wp_path     = ABSPATH;
    $uploads_dir = wp_get_upload_dir()['basedir'];
    $themes_dir  = get_theme_root();
    $plugins_dir = WP_PLUGIN_DIR;

    return [
        'wordpress_dir' => fd_format_size(fd_get_folder_size($wp_path)),
        'uploads_dir'   => fd_format_size(fd_get_folder_size($uploads_dir)),
        'themes_dir'    => fd_format_size(fd_get_folder_size($themes_dir)),
        'plugins_dir'   => fd_format_size(fd_get_folder_size($plugins_dir)),
        'database_size' => $wpdb->get_var("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2)
            FROM information_schema.tables WHERE table_schema = DATABASE()
        ") . ' MB',
        'total_install' => fd_format_size(fd_get_folder_size($wp_path)),
    ];
}

function fd_get_disk_status_label($value, $threshold_mb) {
    $num = 0;

    if (strpos($value, 'GB') !== false) {
        $num = (float) $value * 1024;
    } elseif (strpos($value, 'MB') !== false) {
        $num = (float) $value;
    } elseif (strpos($value, 'KB') !== false) {
        $num = (float) $value / 1024;
    } elseif (strpos($value, 'B') !== false) {
        $num = (float) $value / 1024 / 1024;
    }

    if ($num > $threshold_mb) {
        return '<span class="status-warning">⚠️ High</span>';
    } else {
        return '<span class="status-ok">✅ OK</span>';
    }
}



function fd_get_php_config_info() {
    return [
        'max_input_vars'      => ini_get('max_input_vars'),
        'max_execution_time'  => ini_get('max_execution_time'),
        'memory_limit'        => ini_get('memory_limit'),
        'max_input_time'      => ini_get('max_input_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size'       => ini_get('post_max_size'),
    ];
}

/**
 * Get Categorized Plugin Statuses.
 */
function fd_get_categorized_plugins() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    $categories = [
        'backup' => ['updraftplus', 'backwpup', 'vaultpress', 'duplicator', 'backupbuddy'],
        'security' => ['wordfence', 'sucuri-scanner', 'wp-cerber', 'ithemes-security-pro'],
        'seo' => ['wordpress-seo', 'all-in-one-seo-pack', 'seo-by-rank-math', 'seopress'],
        'performance' => ['wp-rocket', 'litespeed-cache', 'w3-total-cache', 'autoptimize'],
        'woocommerce' => ['woocommerce'],
    ];

    $result = [];

    foreach ($categories as $type => $slugs) {
        $result[$type] = [];
        foreach ($plugins as $plugin_file => $plugin) {
            $slug = explode('/', $plugin_file)[0];
            if (in_array($slug, $slugs)) {
                $result[$type][] = [
                    'name'   => $plugin['Name'],
                    'status' => in_array($plugin_file, $active_plugins) ? 'active' : 'installed',
                ];
            }
        }
    }

    return $result;
}

/**
 * Detect Manual gtag.js Integration.
 */
function fd_check_manual_gtag() {
    $response = wp_remote_get(home_url('/'));

    if (is_wp_error($response)) return false;

    $html = wp_remote_retrieve_body($response);

    return (
        strpos($html, 'https://www.googletagmanager.com/gtag/js') !== false ||
        preg_match('/G-[A-Z0-9]+/', $html)
    );
}

function fd_get_analytics_info() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active  = get_option('active_plugins', []);

    $known = [
        'google-site-kit/google-site-kit.php',
        'monsterinsights/google-analytics-for-wordpress.php',
        'exactmetrics/google-analytics-dashboard-for-wp.php',
    ];

    $found_plugins = [];

    foreach ($known as $file) {
        foreach ($plugins as $installed => $info) {
            if (stripos($installed, dirname($file)) === 0) {
                $found_plugins[] = [
                    'name' => $info['Name'],
                    'status' => in_array($installed, $active) ? 'active' : 'installed',
                ];
            }
        }
    }

    // Check for inline gtag or analytics.js output (in <head>)
    $gtag_found = false;
    ob_start();
    wp_head(); // capture the head content
    $head_output = ob_get_clean();

    if (
        stripos($head_output, 'gtag(') !== false ||
        stripos($head_output, 'www.googletagmanager.com/gtag/js') !== false ||
        stripos($head_output, 'www.google-analytics.com/analytics.js') !== false
    ) {
        $gtag_found = true;
    }

    return [
        'found' => (count($found_plugins) > 0 || $gtag_found),
        'plugins' => $found_plugins,
        'gtag' => $gtag_found,
        'recommendation' => 'Consider installing Google Site Kit, MonsterInsights, or embedding gtag.js to track analytics effectively.',
    ];
}


function fd_get_user_overview() {
    $users = count_users();
    $roles = [];

    foreach ($users['avail_roles'] as $role => $count) {
        $roles[ucfirst(str_replace('_', ' ', $role))] = $count;
    }

    return [
        'total_users'   => $users['total_users'] ?? 0,
        'roles'         => $roles,
        'recommendation'=> 'It is recommended to enable 2FA for all users.',
    ];
}

function fd_get_general_recommendations() {
    return [
        '🖼️ Optimize large images for faster loading.',
        '🔍 Add ALT tags to important images.',
        '🧹 Remove unused plugins and themes.',
        '🔄 Setup scheduled backups.',
        '📈 Improve SEO meta across key pages.',
        '⚙️ Update WordPress, plugins, and PHP.',
        '🔐 Enable security plugins (e.g., Wordfence).',
        '⏱️ Estimated Time: 7–10 hours.',
    ];

}

/**
 * PageSpeed Insights API Score Fetch.
 */

function get_pagespeed_all_scores() {
    $api_key = get_option('fd_api_key');
    $site_url = home_url();
    
    $scores = array(
        'mobile' => get_all_categories_score($site_url, 'mobile', $api_key),
        'desktop' => get_all_categories_score($site_url, 'desktop', $api_key),
        'last_updated' => current_time('mysql')
    );
    
    return $scores;
}


function get_all_categories_score($url, $strategy, $api_key) {
    $api_url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' . http_build_query(array(
        'url' => $url,
        'key' => $api_key,
        'strategy' => $strategy
    )) . '&category=performance&category=accessibility&category=best-practices&category=seo';
    
    $response = wp_remote_get($api_url, array('timeout' => 45));
    
    if (is_wp_error($response)) {
        return array(
            'performance' => 'Error',
            'accessibility' => 'Error',
            'best_practices' => 'Error',
            'seo' => 'Error'
        );
    }
    echo '<!-- ' . esc_url($api_url) . ' -->';

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || isset($data['error'])) {
        return array(
            'performance' => 'Error',
            'accessibility' => 'Error',
            'best_practices' => 'Error',
            'seo' => 'Error'
        );
    }
    
    $categories = $data['lighthouseResult']['categories'] ?? array();
    
    return array(
        'performance' => isset($categories['performance']['score']) ? round($categories['performance']['score'] * 100) : 'Error',
        'accessibility' => isset($categories['accessibility']['score']) ? round($categories['accessibility']['score'] * 100) : 'Error',
        'best_practices' => isset($categories['best-practices']['score']) ? round($categories['best-practices']['score'] * 100) : 'Error',
        'seo' => isset($categories['seo']['score']) ? round($categories['seo']['score'] * 100) : 'Error'
    );
}


function get_pagespeed_category_status_class($score) {
    if ($score === 'Error') return 'status-error';
    if ($score >= 90) return 'status-ok';
    if ($score >= 50) return 'status-warning';
    return 'status-error';
}
/**
 * Core Vulnerability Check.
 */
function fd_check_core_vulnerabilities() {
    global $wp_version;
    $url = "https://www.wpvulnerability.com/api/core/{$wp_version}";

    $response = wp_remote_get($url);
    $data = json_decode(wp_remote_retrieve_body($response), true);

    return $data ?: ['note' => 'No vulnerabilities found.'];
}


function fd_check_plugin_vulnerabilities() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $vulns = [];

    foreach ($plugins as $slug => $info) {
        $url = "https://www.wpvulnerability.com/api/plugin/" . dirname($slug);
        $response = wp_remote_get($url);
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($data['vulnerabilities'])) {
            $vulns[$info['Name']] = $data['vulnerabilities'];
        }
    }

    return $vulns;
}


function fd_check_theme_vulnerabilities() {
    $themes = wp_get_themes();
    $vulns = [];

    foreach ($themes as $theme) {
        $slug = $theme->get_stylesheet();
        $url = "https://www.wpvulnerability.com/api/theme/{$slug}";
        $response = wp_remote_get($url);
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!empty($data['vulnerabilities'])) {
            $vulns[$theme->get('Name')] = $data['vulnerabilities'];
        }
    }

    return $vulns;
}