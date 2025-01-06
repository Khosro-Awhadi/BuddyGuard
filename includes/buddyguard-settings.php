<?php
// Load the plugin text domain for translations
function buddyguard_load_textdomain() {
    load_plugin_textdomain('buddyguard', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'buddyguard_load_textdomain');


// Initialize plugin settings
function buddyguard_init() {
    $components = ['members', 'activity', 'groups', 'forums', 'profile'];
    foreach ($components as $key) {
        register_setting(BUDDYGUARD_SETTINGS_GROUP, 'block_' . $key);
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_exempt_roles');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_logged_in_redirect_option');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_logged_in_custom_redirect_url', 'sanitize_custom_url');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_not_logged_in_redirect_option');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_not_logged_in_custom_redirect_url', 'sanitize_custom_url');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_logged_in_no_redirect_message', 'wp_kses_post');
        register_setting(BUDDYGUARD_SETTINGS_GROUP, $key . '_not_logged_in_no_redirect_message', 'wp_kses_post');
    }

    // Set activation time if not already set
    if (!get_option('buddyguard_activation_time')) {
        update_option('buddyguard_activation_time', time());
    }
}
add_action('admin_init', 'buddyguard_init');

// Sanitize custom URL
function sanitize_custom_url($input) {
    return esc_url_raw($input);
}