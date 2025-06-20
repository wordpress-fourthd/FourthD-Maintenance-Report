<?php
if (!defined('ABSPATH')) exit;

/**
 * Register top-level menu and submenu
 */
add_action('admin_menu', 'fd_register_admin_menu');

function fd_register_admin_menu() {
    add_menu_page(
        'FD Maintenance Report',
        'FD Maintenance',
        'manage_options',
        'fd-maintenance',
        'fd_render_report_page',
        'dashicons-shield-alt',
        80
    );

    add_submenu_page(
        'fd-maintenance',
        'Settings',
        'Settings',
        'manage_options',
        'fd-settings',
        'fd_render_settings_page'
    );
}


function fd_render_report_page() {
    echo '<div class="wrap"><h1>FourthD Maintenance Report</h1><p>Report can be viewed at: <code>' . home_url('/fd-maintenance-report') . '</code></p></div>';
}


function fd_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>FourthD Maintenance Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('fd_settings_group');
            do_settings_sections('fd-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
