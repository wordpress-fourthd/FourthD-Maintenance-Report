<?php
/**
 * Plugin Name: FD Maintenance Report
 * Plugin URI: https://fourthd.io/fd-maintenance-report
 * Description: Displays WordPress core, plugin, SEO, performance, and security checks on a dedicated page.
 * Version: 1.0.0
 * Author: FourthD
 * Author URI: https://fourthd.io
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fd-maintenance-report
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

// Includes
require_once plugin_dir_path(__FILE__) . 'includes/fd-core.php';
require_once plugin_dir_path(__FILE__) . 'includes/fd-api.php';


function fd_register_report_page() {
    add_rewrite_rule('^fd-maintenance-report/?$', 'index.php?fd_report_page=1', 'top');
}
add_action('init', 'fd_register_report_page');

function fd_add_query_vars($vars) {
    $vars[] = 'fd_report_page';
    return $vars;
}
add_filter('query_vars', 'fd_add_query_vars');

function fd_load_report_template() {
    if (get_query_var('fd_report_page')) {
        include plugin_dir_path(__FILE__) . 'templates/report-template.php';
        exit;
    }
}
add_action('template_redirect', 'fd_load_report_template');

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/fd-admin-menu.php';
    require_once plugin_dir_path(__FILE__) . 'includes/fd-settings.php';
}
