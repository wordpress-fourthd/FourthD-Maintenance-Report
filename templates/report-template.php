<?php
/**
 * Maintenance Report Template
 */

defined('ABSPATH') || exit;

$core_info     = fd_get_wp_core_info();
$theme_info    = fd_get_active_theme_info();
$plugins       = fd_get_plugin_info();
$config        = fd_get_wp_config_info();
$disk_usage          = fd_get_disk_usage_info();
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

                <li><strong>WordPress Version:</strong>
                    <?php echo esc_html($core_info['wp_version']); ?>
                    <?php echo $wp_needs_update ? ' <span class="status-warning">⚠️ Needs update</span>' : ' <span class="status-ok">✅ Up-to-date</span>'; ?>
                </li>

                <li><strong>PHP Version:</strong>
                    <?php echo esc_html($core_info['php_version']); ?>
                    <?php echo version_compare($core_info['php_version'], '8.1', '>=') ? ' <span class="status-ok">✅ OK</span>' : ' <span class="status-warning">⚠️ Consider upgrading</span>'; ?>
                </li>

                <li><strong>MySQL Version:</strong>
                    <?php echo esc_html($core_info['mysql_version']); ?>
                    <?php echo version_compare($core_info['mysql_version'], '5.7', '>=') ? ' <span class="status-ok">✅ OK</span>' : ' <span class="status-warning">⚠️ Outdated</span>'; ?>
                </li>

                <li><strong>Server Software:</strong> <?php echo esc_html($core_info['server_software']); ?></li>

                <li><strong>SSL Enabled:</strong>
                    <?php echo esc_html($core_info['ssl_enabled']); ?>
                    <?php echo ($core_info['ssl_enabled'] === 'Yes') ? ' <span class="status-ok">✅</span>' : ' <span class="status-warning">⚠️ Should be enabled</span>'; ?>
                </li>

                <li><strong>Language:</strong> <?php echo esc_html($core_info['language']); ?></li>

                <li><strong>Timezone:</strong>
                    <?php echo esc_html($core_info['timezone']); ?>
                    <?php echo ($core_info['timezone'] === 'Not Set') ? ' <span class="status-warning">⚠️ Should be set</span>' : ' <span class="status-ok">✅</span>'; ?>
                </li>

                <li><strong>Debug Mode:</strong>
                    <?php echo $core_info['debug_mode'] ? 'Enabled <span class="status-warning">⚠️ Turn off on production</span>' : 'Disabled <span class="status-ok">✅</span>'; ?>
                </li>

                <li><strong>Multisite:</strong> <?php echo $core_info['multisite'] ? 'Yes' : 'No'; ?></li>

                <li><strong>Table Prefix:</strong> <?php echo esc_html($core_info['table_prefix']); ?></li>
            </ul>
        </div>
    </div>

   <div class="section">
  <div class="section-inner">
    <h2>🛠️ WordPress Configuration</h2>
    <ul>
      <li><strong>Permalink Structure:</strong> <?php echo esc_html($config['permalink_structure'] ?: 'Default'); ?></li>
      <li><strong>User Registration Enabled:</strong> <?php echo esc_html($config['membership']); ?></li>
      <li><strong>Discourage Search Engines:</strong> <?php echo esc_html($config['discourage_search']); ?></li>
      <li><strong>Default Comment Status:</strong> <?php echo esc_html($config['default_comment_status']); ?></li>
      <li><strong>Total Users:</strong> <?php echo esc_html($config['total_users']); ?></li>

      <li>
        <strong>Plugins Installed:</strong>
        <?php echo esc_html($config['total_plugins']); ?> total (<?php echo esc_html($config['active_plugins']); ?> active)
        <?php if ($config['total_plugins'] > 20): ?>
          <span class="status-warning">⚠️ Too many plugins</span>
        <?php else: ?>
          <span class="status-ok">✅ Good</span>
        <?php endif; ?>
        <br><em>Recommended: Only keep necessary plugins (typically < 20 total).</em>
      </li>

      <?php
        $theme = wp_get_theme();
        $all_themes = wp_get_themes();
        $theme_count = count($all_themes);
      ?>
      <li>
        <strong>Themes Installed:</strong>
        <?php echo esc_html($theme_count); ?> total (Active: <?php echo esc_html($theme->get('Name')); ?>)
        <?php if ($theme_count > 3): ?>
          <span class="status-warning">⚠️ Consider removing unused themes</span>
        <?php else: ?>
          <span class="status-ok">✅ Good</span>
        <?php endif; ?>
        <br><em>Recommended: Keep 1 active + 1 default fallback theme (e.g., Twenty Twenty-Four).</em>
      </li>
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
        <h2>💾 Disk Usage Overview</h2>

        <?php
        $status = [
            'wordpress' => fd_get_disk_status_label($disk_usage['wordpress_dir'], 500),
            'uploads'   => fd_get_disk_status_label($disk_usage['uploads_dir'], 2000),
            'themes'    => fd_get_disk_status_label($disk_usage['themes_dir'], 100),
            'plugins'   => fd_get_disk_status_label($disk_usage['plugins_dir'], 200),
            'database'  => fd_get_disk_status_label($disk_usage['database_size'], 500),
            'total'     => fd_get_disk_status_label($disk_usage['total_install'], 5000),
        ];
        ?>

    <ul>
    <li><strong>WordPress Directory Size:</strong> <?php echo $disk_usage['wordpress_dir']; ?> <?php echo $status['wordpress']; ?></li>
    <li><strong>Uploads Directory Size:</strong> <?php echo $disk_usage['uploads_dir']; ?> <?php echo $status['uploads']; ?></li>
    <li><strong>Themes Directory Size:</strong> <?php echo $disk_usage['themes_dir']; ?> <?php echo $status['themes']; ?></li>
    <li><strong>Plugins Directory Size:</strong> <?php echo $disk_usage['plugins_dir']; ?> <?php echo $status['plugins']; ?></li>
    <li><strong>Database Size:</strong> <?php echo $disk_usage['database_size']; ?> <?php echo $status['database']; ?></li>
    <li><strong>Total Installation Size:</strong> <?php echo $disk_usage['total_install']; ?> <?php echo $status['total']; ?></li>
</ul>

             <div style="padding: 30px;">

                <h3>📋 Recommended Size Limits</h3>
                <table class="fd-table">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Suggested Max</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>WordPress Directory</td>
                            <td>&lt; 500 MB</td>
                            <td>Core files + themes/plugins should stay lean</td>
                        </tr>
                        <tr>
                            <td>Uploads Directory</td>
                            <td>&lt; 2 GB</td>
                            <td>Clean periodically (especially for media-heavy sites)</td>
                        </tr>
                        <tr>
                            <td>Themes Directory</td>
                            <td>&lt; 100 MB</td>
                            <td>Remove unused themes to reduce clutter</td>
                        </tr>
                        <tr>
                            <td>Plugins Directory</td>
                            <td>&lt; 200 MB</td>
                            <td>Builders and feature-rich plugins can get large fast</td>
                        </tr>
                        <tr>
                            <td>Database Size</td>
                            <td>&lt; 500 MB</td>
                            <td>Clean transients/logs/spam regularly</td>
                        </tr>
                        <tr>
                            <td>Total Installation Size</td>
                            <td>&lt; 5 GB</td>
                            <td>Use CDN/offloading if consistently larger</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>

 <?php
$pagespeed = get_pagespeed_all_scores(); // Fetch once
$sections = [
    'Backup'      => 'backup',
    'Security'    => 'security',
    'SEO'         => 'seo',
    'Caching'     => 'performance',
    'WooCommerce' => 'woocommerce'
];

$icons = [
    'Backup'      => '♻️',
    'Security'    => '🔐',
    'SEO'         => '🔎',
    'Caching'     => '🚀',
    'WooCommerce' => '🛒'
];

$recommendations = [
    'backup'      => 'Install and activate a backup plugin.',
    'security'    => 'Use a firewall or security plugin.',
    'seo'         => 'Add an SEO plugin to improve search visibility.',
    'performance' => 'Use a caching plugin for performance.',
    'woocommerce' => 'Activate WooCommerce if needed.'
];

foreach ($sections as $label => $slug):
?>
<div class="section">
  <div class="section-inner">
    <h2><?php echo $icons[$label]; ?> <?php echo $label; ?> Overview</h2>

    <?php if ($slug === 'security'): ?>
      <h3>🔐 Internal Security Plugin Check</h3>
    <?php endif; ?>

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
        <li><strong>Recommendation:</strong> <?php echo esc_html($recommendations[$slug]); ?></li>
      <?php endif; ?>
    </ul>

    <?php if ($slug === 'performance' && !empty($pagespeed)): ?>
      <h3>🚀 PageSpeed Insights</h3>
      <div style="display: flex; gap: 20px;">
        <div style="flex:1;">
          <h4>💻 Desktop</h4>
          <ul>
            <li><strong>Performance:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['desktop']['performance']); ?>"><?php echo $pagespeed['desktop']['performance']; ?></span></li>
            <li><strong>Accessibility:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['desktop']['accessibility']); ?>"><?php echo $pagespeed['desktop']['accessibility']; ?></span></li>
            <li><strong>Best Practices:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['desktop']['best_practices']); ?>"><?php echo $pagespeed['desktop']['best_practices']; ?></span></li>
            <li><strong>SEO:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['desktop']['seo']); ?>"><?php echo $pagespeed['desktop']['seo']; ?></span></li>
          </ul>
        </div>
        <div style="flex:1;">
          <h4>📱 Mobile</h4>
          <ul>
            <li><strong>Performance:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['mobile']['performance']); ?>"><?php echo $pagespeed['mobile']['performance']; ?></span></li>
            <li><strong>Accessibility:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['mobile']['accessibility']); ?>"><?php echo $pagespeed['mobile']['accessibility']; ?></span></li>
            <li><strong>Best Practices:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['mobile']['best_practices']); ?>"><?php echo $pagespeed['mobile']['best_practices']; ?></span></li>
            <li><strong>SEO:</strong> <span class="<?php echo get_pagespeed_category_status_class($pagespeed['mobile']['seo']); ?>"><?php echo $pagespeed['mobile']['seo']; ?></span></li>
          </ul>
        </div>
      </div>
      <p style="margin-top:10px;font-size:12px;color:#666;"><strong>Last Updated:</strong> <?php echo esc_html($pagespeed['last_updated']); ?></p>
    <?php endif; ?>

    <?php if ($slug === 'security'): ?>
      <h3>🛡️ Vulnerability Check</h3>

      <h4>🔧 WordPress Core</h4>
      <?php if (!empty($core_vuln['vulnerabilities'])): ?>
        <ul>
          <?php foreach ($core_vuln['vulnerabilities'] as $v): ?>
            <li><strong><?php echo esc_html($v['title']); ?></strong><br>
            <em><?php echo esc_html(wp_strip_all_tags($v['description'])); ?></em><br>
            <small>Severity: <?php echo esc_html($v['severity']); ?></small></li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="fd-success">✅ No core vulnerabilities</p>
      <?php endif; ?>

      <h4>🔌 Plugins</h4>
      <?php if (!empty($plugin_vuln)): ?>
        <?php foreach ($plugin_vuln as $plugin => $vuls): ?>
          <h5><?php echo esc_html($plugin); ?></h5>
          <ul>
            <?php foreach ($vuls as $v): ?>
              <li><strong><?php echo esc_html($v['title']); ?></strong><br>
              <em><?php echo esc_html(wp_strip_all_tags($v['description'])); ?></em><br>
              <small>Severity: <?php echo esc_html($v['severity']); ?></small></li>
            <?php endforeach; ?>
          </ul>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="fd-success">✅ No plugin vulnerabilities</p>
      <?php endif; ?>

     
      <h4>🎨 Themes</h4>
      <?php if (!empty($theme_vuln)): ?>
        <?php foreach ($theme_vuln as $theme => $vuls): ?>
          <h5><?php echo esc_html($theme); ?></h5>
          <ul>
            <?php foreach ($vuls as $v): ?>
              <li><strong><?php echo esc_html($v['title']); ?></strong><br>
              <em><?php echo esc_html(wp_strip_all_tags($v['description'])); ?></em><br>
              <small>Severity: <?php echo esc_html($v['severity']); ?></small></li>
            <?php endforeach; ?> 
          </ul>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="fd-success">✅ No theme vulnerabilities</p>
      <?php endif; ?>
    <?php endif; ?>


    <?php if ($slug === 'woocommerce' && class_exists('WooCommerce')): ?>
  <h3>🛍️ WooCommerce Stats</h3>
  <ul>
    <?php
    // Product Counts
    $product_counts = [
        'total'     => wp_count_posts('product')->publish + wp_count_posts('product')->draft + wp_count_posts('product')->pending,
        'published' => wp_count_posts('product')->publish,
        'drafts'    => wp_count_posts('product')->draft,
        'pending'   => wp_count_posts('product')->pending,
    ];

    // Order Counts by Status
    $order_statuses = ['wc-completed', 'wc-processing', 'wc-pending', 'wc-on-hold'];
    $order_counts = [];

    foreach ($order_statuses as $status) {
        $order_counts[$status] = wc_orders_count(str_replace('wc-', '', $status));
    }

    $total_orders = array_sum($order_counts);
    ?>
    
    <li><strong>Total Products:</strong> <?php echo esc_html($product_counts['total']); ?></li>
    <li>
      <strong>Published:</strong> <?php echo esc_html($product_counts['published']); ?>,
      <strong>Drafts:</strong> <?php echo esc_html($product_counts['drafts']); ?>,
      <strong>Pending:</strong> <?php echo esc_html($product_counts['pending']); ?>
    </li>

    <li><strong>Total Orders:</strong> <?php echo esc_html($total_orders); ?></li>
    <li>
      <strong>Completed:</strong> <?php echo esc_html($order_counts['wc-completed']); ?>,
      <strong>Processing:</strong> <?php echo esc_html($order_counts['wc-processing']); ?>,
      <strong>Pending:</strong> <?php echo esc_html($order_counts['wc-pending']); ?>,
      <strong>On Hold:</strong> <?php echo esc_html($order_counts['wc-on-hold']); ?>
    </li>
  </ul>
<?php endif; ?>

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

                <?php if ($analytics['gtag']): ?>
                    <li>
                        gtag.js detected:
                        <span class="status-ok">✅ Custom Google Analytics code found</span>
                    </li>
                <?php endif; ?>

                <li><strong>Recommendation:</strong> Ensure you're tracking key events (e.g., goals, ecommerce, behavior).</li>
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
	

</div>
</body>
</html>
