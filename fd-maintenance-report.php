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

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/fd-core.php';
require_once plugin_dir_path(__FILE__) . 'includes/fd-api.php';

/**
 * Register custom rewrite rule for the maintenance report page.
 */
function fd_register_report_page() {
    add_rewrite_rule('^fd-maintenance-report/?$', 'index.php?fd_report_page=1', 'top');
}
add_action('init', 'fd_register_report_page');

/**
 * Add custom query var for our report page.
 *
 * @param array $vars
 * @return array
 */
function fd_add_query_vars($vars) {
    $vars[] = 'fd_report_page';
    return $vars;
}
add_filter('query_vars', 'fd_add_query_vars');

/**
 * Load the report template when the report page is requested.
 */
function fd_load_report_template() {
    if (get_query_var('fd_report_page')) {
        include plugin_dir_path(__FILE__) . 'templates/report-template.php';
        exit;
    }
}
add_action('template_redirect', 'fd_load_report_template');  
 