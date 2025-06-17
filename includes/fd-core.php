<?php
defined('ABSPATH') || exit;

// function fd_get_wp_core_info() {
//     return [
//         'wp_version'       => get_bloginfo('version'),
//         'site_url'         => site_url(),
//         'admin_email'      => get_option('admin_email'),
//         'multisite'        => is_multisite(),
//         'debug_mode'       => defined('WP_DEBUG') && WP_DEBUG,
//     ];
// }

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
    global $wpdb;

    $user_count_data = count_users();
    $total_users     = $user_count_data['total_users'] ?? 0;

    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);
    $active_count = count($active_plugins);
    $total_plugins = count($all_plugins);

    return [
        'permalink_structure' => get_option('permalink_structure'),
        'membership'          => get_option('users_can_register') ? 'Yes' : 'No',
        'discourage_search'   => get_option('blog_public') ? 'No' : 'Yes',
        'default_comment_status' => get_option('default_comment_status'),
        'total_users'         => $total_users,
        'total_plugins'       => $total_plugins,
        'active_plugins'      => $active_count,
    ];
}


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

function fd_get_plugin_info() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    $plugin_data = [];
    foreach ($plugins as $plugin_file => $plugin) {
        $plugin_data[] = [
            'name'     => $plugin['Name'],
            'version'  => $plugin['Version'],
            'active'   => in_array($plugin_file, $active_plugins),
            'author'   => $plugin['Author'],
        ];
    }

    return $plugin_data;
}


function fd_get_disk_usage_info() {
    global $wpdb;

    $wp_path      = ABSPATH;
    $uploads_dir  = wp_get_upload_dir()['basedir'];
    $themes_dir   = get_theme_root();
    $plugins_dir  = WP_PLUGIN_DIR;
    $db_size_mb   = $wpdb->get_var("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) FROM information_schema.tables WHERE table_schema = DATABASE()");

    $total_size = fd_get_folder_size($wp_path);

    return [
        'wordpress_dir' => fd_format_size(fd_get_folder_size($wp_path)),
        'uploads_dir'   => fd_format_size(fd_get_folder_size($uploads_dir)),
        'themes_dir'    => fd_format_size(fd_get_folder_size($themes_dir)),
        'plugins_dir'   => fd_format_size(fd_get_folder_size($plugins_dir)),
        'database_size' => $db_size_mb . ' MB',
        'total_install' => fd_format_size($total_size),
    ];
}

function fd_get_folder_size($path) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function fd_format_size($bytes) {
    $units = ['B','KB','MB','GB','TB'];
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
function fd_get_php_config_info() {
    return [
        'max_input_vars' => ini_get('max_input_vars'),
        'max_execution_time' => ini_get('max_execution_time'),
        'memory_limit' => ini_get('memory_limit'),
        'max_input_time' => ini_get('max_input_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ];
}


function fd_get_categorized_plugins() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    // Plugin slug groups
    $categories = [
'backup' => [
    'updraftplus',
    'backwpup',
    'vaultpress',
    'blogvault-real-time-backup',
    'wpvivid-backuprestore',
    'backupwordpress',
    'xcloner-backup-and-restore',
    'duplicator',
    'backwpup',
    'wp-db-backup',
    'backupbuddy',
    'total-upkeep',
    'snapshots',
],
'security' => [
    'wordfence',
    'sucuri-scanner',
    'all-in-one-wp-security-and-firewall',
    'wps-hide-login',
    'shield-security',
    'wp-cerber',
    'malcare-security',
    'ithemes-security-pro',
    'jetpack',
    'bbq-firewall',
    'ninja-firewall',
    'wp-defender',
],
'seo' => [
    'wordpress-seo', // Yoast
    'all-in-one-seo-pack',
    'seo-by-rank-math',
    'the-seo-framework',
    'platinum-seo-pack',
    'squirrly-seo',
    'smartcrawl-seo',
    'seopress',
    'wp-meta-seo',
    'slim-seo',
    'aioseo-lite',
],
'performance' => [
    'wp-rocket',
    'litespeed-cache',
    'w3-total-cache',
    'autoptimize',
    'cache-enabler',
    'comet-cache',
    'hyper-cache',
    'swift-performance-lite',
    'hummingbird-performance',
    'nitropack',
    'siteground-optimizer',
    'asset-cleanup',
    'wp-optimize',
    'fast-velocity-minify',
    'breeze',
    'cloudflare',
    'gzip-compression',
],
'woocommerce' => [
    'woocommerce',
    
],
    ];

    $categorized = [];

    foreach ($categories as $type => $slugs) {
        $categorized[$type] = [];
        foreach ($plugins as $plugin_file => $plugin_data) {
            $slug = explode('/', $plugin_file)[0];
            if (in_array($slug, $slugs)) {
                $categorized[$type][] = [
                    'name'   => $plugin_data['Name'],
                    'status' => in_array($plugin_file, $active_plugins) ? 'active' : 'installed',
                ];
            }
        }
    }

    return $categorized;
}


function fd_check_manual_gtag() {
    // Get the site's homepage HTML (cached version for performance)
    $homepage_url = home_url('/');

    // Use wp_remote_get to fetch HTML content of homepage
    $response = wp_remote_get($homepage_url);

    if (is_wp_error($response)) {
        return false; // Could not fetch site HTML
    }

    $html = wp_remote_retrieve_body($response);

    // Look for gtag.js snippet or Google Analytics tracking ID (e.g. 'G-XXXXXXX')
    if (strpos($html, 'https://www.googletagmanager.com/gtag/js') !== false) {
        return true;
    }
    if (preg_match('/G-[A-Z0-9]+/', $html)) {
        return true;
    }

    return false;
}

function fd_get_analytics_info() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', []);

    $analytics_plugins = [
        'google-site-kit/google-site-kit.php',
        'monsterinsights/google-analytics-for-wordpress.php',
        'exactmetrics/google-analytics-dashboard-for-wp.php',
        'analyticstracking/analyticstracking.php',
        'ga-google-analytics/ga-google-analytics.php',
    ];

    $found_plugins = [];

    foreach ($analytics_plugins as $plugin_file) {
        foreach ($plugins as $installed_file => $plugin_data) {
            if (stripos($installed_file, dirname($plugin_file)) === 0) {
                $is_active = in_array($installed_file, $active_plugins);
                $found_plugins[] = [
                    'name' => $plugin_data['Name'],
                    'status' => $is_active ? 'active' : 'installed',
                ];
                break;
            }
        }
    }

    $manual_gtag = fd_check_manual_gtag();

    if (empty($found_plugins) && !$manual_gtag) {
        return [
            'found' => false,
            'recommendation' => '⚠️ No Google Analytics integration detected. We recommend installing Google Site Kit or MonsterInsights to monitor traffic and behavior.',
        ];
    }

    if ($manual_gtag) {
        $found_plugins[] = [
            'name' => 'Manual gtag.js Integration',
            'status' => 'active',
        ];
    }

    return [
        'found' => true,
        'plugins' => $found_plugins,
        'recommendation' => '',
    ];
}


function fd_get_user_overview() {
    $user_count_data = count_users();
    $total_users = $user_count_data['total_users'] ?? 0;
    $roles_summary = [];

    foreach ($user_count_data['avail_roles'] as $role => $count) {
        $roles_summary[ucfirst(str_replace('_', ' ', $role))] = $count;
    }

    if (!isset($roles_summary['None'])) {
        $roles_summary['None'] = 0;
    }

    return [
        'total_users' => $total_users,
        'roles' => $roles_summary,
        'recommendation' => 'It is recommended that 2FA be added for all accounts.',
    ];
}

	function fd_fetch_pagespeed_data($url) {
    $api_key = 'AIzaSyAQLl9psH3hYJSDDccXZUYK9R5A9vpYZhU';

    $api_base = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
    $strategies = ['mobile', 'desktop'];
    $results = [];

    foreach ($strategies as $strategy) {
        $request_url = add_query_arg([
            'url' => $url,
            'key' => $api_key,
            'strategy' => $strategy,
        ], $api_base);

        $response = wp_remote_get($request_url);

        if (is_wp_error($response)) {
            $results[$strategy] = ['error' => 'Request failed'];
            continue;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['lighthouseResult'])) {
            $results[$strategy] = ['error' => 'No results found'];
            continue;
        }

        $lh = $data['lighthouseResult'];

        $results[$strategy] = [
            'performance' => round($lh['categories']['performance']['score'] * 100),
            'fcp' => $lh['audits']['first-contentful-paint']['displayValue'],
            'lcp' => $lh['audits']['largest-contentful-paint']['displayValue'],
            'tbt' => $lh['audits']['total-blocking-time']['displayValue'],
            'cls' => $lh['audits']['cumulative-layout-shift']['displayValue'],
            'link' => $data['id'], // Original test URL
        ];
    }

    return $results;
}





function fd_get_general_recommendations() {
    return [
        '🖼️ Optimize large image files to improve page load speed.',
        '🔍 Add missing ALT tags to important images for better SEO and accessibility.',
        '🧹 Remove or deactivate unused plugins and themes to reduce security risk.',
        '🔄 Set up automated daily/weekly backups (if not already configured).',
        '📈 Review and update SEO meta titles/descriptions across key pages.',
        '⚙️ Update WordPress core, plugins, and PHP version (if not on latest).',
        '🔐 Install or review security plugin settings (Wordfence, Sucuri, etc.).',
        '⏱️ Total Estimated Time: 7–10 hours',
        'Need help implementing any of these improvements? We’d be happy to assist.',
    ];
}




function get_pagespeed_all_scores() {
    $api_key = 'AIzaSyAQLl9psH3hYJSDDccXZUYK9R5A9vpYZhU';
    $site_url = home_url();
    
    $scores = array(
        'mobile' => get_all_categories_score($site_url, 'mobile', $api_key),
        'desktop' => get_all_categories_score($site_url, 'desktop', $api_key),
        'last_updated' => current_time('mysql')
    );
    
    return $scores;
}


function get_all_categories_score($url, $strategy, $api_key) {
    // Build URL with all categories
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