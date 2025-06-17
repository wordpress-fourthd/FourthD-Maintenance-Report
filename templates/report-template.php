<?php
/**
 * Maintenance Report Template
 */

defined('ABSPATH') || exit;

$core_info     = fd_get_wp_core_info();
$theme_info    = fd_get_active_theme_info();
$plugins       = fd_get_plugin_info();
$config        = fd_get_wp_config_info();
$disk          = fd_get_disk_usage_info();
$php_config    = fd_get_php_config_info();
$categorized   = fd_get_categorized_plugins();
$analytics     = fd_get_analytics_info();
$users         = fd_get_user_overview();
$recommendations = fd_get_general_recommendations();

$latest_wp_version = '6.5.3';
$wp_needs_update   = version_compare($core_info['wp_version'], $latest_wp_version, '<');
$wp_status_class   = $wp_needs_update ? 'status-warning' : 'status-ok';
$php_status_class  = version_compare($core_info['php_version'], '8.0', '>=') ? 'status-ok' : 'status-warning';
$ssl_status_class  = $core_info['ssl_enabled'] === 'Yes' ? 'status-ok' : 'status-warning';
$timezone_status_class = ($core_info['timezone'] === 'Not Set') ? 'status-warning' : 'status-ok';
$debug_status_class = $core_info['debug_mode'] ? 'status-warning' : 'status-ok';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance Report</title>
    <link rel="stylesheet" href="<?php echo plugins_url('assets/css/style.css', dirname(__FILE__)); ?>?ver=1.0.0" />
    <style>
        .status-ok { color: green; font-weight: bold; }
        .status-warning { color: red; font-weight: bold; }
    </style>
</head>
<body>
<h1 class="fd-maintenance-heading">
  <img src="<?php echo plugins_url('assets/images/fourthd.svg', dirname(__FILE__)); ?>" alt="FD Logo" class="fd-logo">
  Maintenance Report
</h1>

<div class="report-container">
    <div class="section">
        <div class="section-inner">
            <h2>⚙️ WordPress Info</h2>
            <ul>
                <li><strong>Site URL:</strong> <?php echo esc_html($core_info['site_url']); ?></li>
                <li><strong>WordPress Version:</strong> <span class="<?php echo $wp_status_class; ?>"><?php echo esc_html($core_info['wp_version']); ?> (<?php echo $wp_needs_update ? 'Needs update' : 'Updated'; ?>)</span></li>
                <li><strong>PHP Version:</strong> <span class="<?php echo $php_status_class; ?>"><?php echo esc_html($core_info['php_version']); ?></span></li>
                <li><strong>MySQL Version:</strong> <?php echo esc_html($core_info['mysql_version']); ?></li>
                <li><strong>Server Software:</strong> <?php echo esc_html($core_info['server_software']); ?></li>
                <li><strong>SSL Enabled:</strong> <span class="<?php echo $ssl_status_class; ?>"><?php echo esc_html($core_info['ssl_enabled']); ?></span></li>
                <li><strong>Language:</strong> <?php echo esc_html($core_info['language']); ?></li>
                <li><strong>Timezone:</strong> <span class="<?php echo $timezone_status_class; ?>"><?php echo esc_html($core_info['timezone']); ?></span></li>
                <li><strong>Debug Mode:</strong> <span class="<?php echo $debug_status_class; ?>"><?php echo $core_info['debug_mode'] ? 'Enabled' : 'Disabled'; ?></span></li>
                <li><strong>Multisite:</strong> <?php echo $core_info['multisite'] ? 'Yes' : 'No'; ?></li>
                <li><strong>Table Prefix:</strong> <?php echo esc_html($core_info['table_prefix']); ?></li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>🛠️ WordPress Configuration</h2>
            <ul>
                <?php foreach ($config as $key => $value): ?>
                    <li><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</strong> <?php echo esc_html($value); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>🎨 Active Theme</h2>
            <ul>
                <?php foreach ($theme_info as $key => $value): ?>
                    <li><strong><?php echo ucwords($key); ?>:</strong> <?php echo esc_html($value); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>🔌 Plugins</h2>
            <ul class="plugin-list">
                <?php foreach ($plugins as $plugin): ?>
                    <li>
                        <?php echo esc_html($plugin['name']); ?> (v<?php echo esc_html($plugin['version']); ?>) - 
                        <span class="<?php echo $plugin['active'] ? 'status-ok' : 'status-warning'; ?>">
                            <?php echo $plugin['active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>🧮 Disk Usage</h2>
            <ul>
                <?php foreach ($disk as $label => $size): ?>
                    <li><strong><?php echo ucwords(str_replace('_', ' ', $label)); ?>:</strong> <?php echo esc_html($size); ?></li>
                <?php endforeach; ?>
            </ul>
            <!-- Existing recommended size table kept -->
        </div>
    </div>

    <?php
    $sections = [
        'Backup' => 'backup',
        'Security' => 'security',
        'SEO' => 'seo',
        'Caching' => 'performance',
        'WooCommerce' => 'woocommerce'
    ];

    foreach ($sections as $label => $slug):
    ?>
    <div class="section">
        <div class="section-inner">
            <h2><?php echo $label === 'Caching' ? '🚀' : ($label === 'WooCommerce' ? '🛒' : ($label === 'SEO' ? '🔎' : ($label === 'Security' ? '🔐' : '♻️'))); ?> <?php echo $label; ?> Overview</h2>
            <ul>
                <?php if (!empty($categorized[$slug])): ?>
                    <?php foreach ($categorized[$slug] as $plugin): ?>
                        <li>
                            <?php echo esc_html($plugin['name']); ?>:
                            <span class="<?php echo $plugin['status'] === 'active' ? 'status-ok' : 'status-warning'; ?>">
                                <?php echo $plugin['status'] === 'active' ? '✅ Active' : 'Installed (Not Active)'; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="status-warning">❌ No plugin found</li>
                    <li><strong>Recommendation:</strong> <?php echo esc_html((['backup'=>'Install and activate a backup plugin.','security'=>'Use a firewall or security plugin.','seo'=>'Add an SEO plugin to improve search visibility.','performance'=>'Use a caching plugin for performance.','woocommerce'=>'Activate WooCommerce if needed.'])[$slug]); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="section">
        <div class="section-inner">
            <h2>📈 Analytics Overview</h2>
            <ul>
                <?php if (!$analytics['found']): ?>
                    <li class="status-warning"><?php echo esc_html($analytics['recommendation']); ?></li>
                <?php else: ?>
                    <?php foreach ($analytics['plugins'] as $plugin): ?>
                        <li>
                            <?php echo esc_html($plugin['name']); ?>:
                            <span class="<?php echo $plugin['status'] === 'active' ? 'status-ok' : 'status-warning'; ?>">
                                <?php echo $plugin['status'] === 'active' ? '✅ Active' : '⚠️ Installed (Not Active)'; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>👥 User Accounts Overview</h2>
            <ul>
                <li><strong>Total Users:</strong> <?php echo esc_html($users['total_users']); ?></li>
                <li><strong>User Roles:</strong>
                    <ul>
                        <?php foreach ($users['roles'] as $role => $count): ?>
                            <li><?php echo esc_html($role); ?>: <?php echo esc_html($count); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><strong>Recommendation:</strong> <?php echo esc_html($users['recommendation']); ?></li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-inner">
            <h2>🛠️ Recommendations</h2>
            <ul>
                <?php foreach ($recommendations as $item): ?>
                    <li><?php echo esc_html($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
	
	
	
<?php
// Get all PageSpeed scores (calculated fresh each time)
$pagespeed_data = get_pagespeed_all_scores();
$mobile_scores = $pagespeed_data['mobile'];
$desktop_scores = $pagespeed_data['desktop'];
?>

<div class="section">
    <div class="section-inner">
        <h2>🚀 PageSpeed Insights</h2>
        
        <div class="pagespeed-categories" style="display: flex; gap: 20px; padding:20px;">
            <!-- Desktop Scores -->
            <div style="flex: 1;">
                <h3>💻 Desktop</h3>
                <ul>
                    <li><strong>Performance:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($desktop_scores['performance']); ?>">
                            <?php echo $desktop_scores['performance']; ?>
                        </span>
                    </li>
                    <li><strong>Accessibility:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($desktop_scores['accessibility']); ?>">
                            <?php echo $desktop_scores['accessibility']; ?>
                        </span>
                    </li>
                    <li><strong>Best Practices:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($desktop_scores['best_practices']); ?>">
                            <?php echo $desktop_scores['best_practices']; ?>
                        </span>
                    </li>
                    <li><strong>SEO:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($desktop_scores['seo']); ?>">
                            <?php echo $desktop_scores['seo']; ?>
                        </span>
                    </li>
                </ul>
            </div>
            
            <!-- Mobile Scores -->
            <div style="flex: 1;">
                <h3>📱 Mobile</h3>
                <ul>
                    <li><strong>Performance:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($mobile_scores['performance']); ?>">
                            <?php echo $mobile_scores['performance']; ?>
                        </span>
                    </li>
                    <li><strong>Accessibility:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($mobile_scores['accessibility']); ?>">
                            <?php echo $mobile_scores['accessibility']; ?>
                        </span>
                    </li>
                    <li><strong>Best Practices:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($mobile_scores['best_practices']); ?>">
                            <?php echo $mobile_scores['best_practices']; ?>
                        </span>
                    </li>
                    <li><strong>SEO:</strong> 
                        <span class="<?php echo get_pagespeed_category_status_class($mobile_scores['seo']); ?>">
                            <?php echo $mobile_scores['seo']; ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        
        <p style="margin-top: 10px; font-size: 12px; color: #666; text-align:center;">
            <strong>Last Updated:</strong> <?php echo $pagespeed_data['last_updated']; ?>
        </p>
    </div>
</div>
	
	
	
</div>
	
	


</body>
</html>


