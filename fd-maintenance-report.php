<?php
/*
Plugin Name: FD Maintenance Report
Description: Displays WordPress core, plugin, SEO, performance, and security checks on a dedicated page.
Version: 1.0
Author: FourthD
*/

defined('ABSPATH') || exit;

// Auto-load includes
require_once plugin_dir_path(__FILE__) . 'includes/fd-core.php';
require_once plugin_dir_path(__FILE__) . 'includes/fd-api.php';

// Register report page
add_action('init', 'fd_register_report_page');
function fd_register_report_page() {
    add_rewrite_rule('^fd-maintenance-report/?$', 'index.php?fd_report_page=1', 'top');
}

add_filter('query_vars', function($vars) {
    $vars[] = 'fd_report_page';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('fd_report_page')) {
        include plugin_dir_path(__FILE__) . 'templates/report-template.php';
        exit;
    }
});
