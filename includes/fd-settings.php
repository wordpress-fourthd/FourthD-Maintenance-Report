<?php
if (!defined('ABSPATH')) exit;

add_action('admin_init', 'fd_register_plugin_settings');

function fd_register_plugin_settings() {
    register_setting('fd_settings_group', 'fd_api_key');

    add_settings_section(
        'fd_main_section',
        'Main Settings',
        null,
        'fd-settings'
    );

    add_settings_field(
        'fd_api_key_field',
        'Enter Pagespeed insights API key',
        'fd_api_key_field_callback',
        'fd-settings',
        'fd_main_section'
    );
}

function fd_api_key_field_callback() {
    $value = esc_attr(get_option('fd_api_key'));
    echo '<input type="text" name="fd_api_key" value="' . $value . '" class="regular-text">';
}
