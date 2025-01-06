<?php
// Log actions to the WordPress debug log
function buddyguard_log($action, $details = '') {
    $timestamp = current_time('mysql'); // Get current timestamp
    $user = wp_get_current_user();
    $username = $user->exists() ? $user->user_login : __('Guest', 'buddyguard'); // Get username or "Guest"
    $ip_address = $_SERVER['REMOTE_ADDR']; // Get user IP address

    // Log entry format: [Timestamp] [Username] [IP Address] [Action] [Details]
    $log_entry = sprintf(
        "[%s] [%s] [%s] %s: %s",
        $timestamp,
        $username,
        $ip_address,
        $action,
        $details
    );

    // Write to the WordPress debug log
    error_log($log_entry);
}