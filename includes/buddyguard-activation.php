<?php
// Log plugin activation
function buddyguard_activate() {
    update_option('buddyguard_activation_time', time());
    buddyguard_log(__('Plugin Activated', 'buddyguard'), __('BuddyGuard plugin was activated.', 'buddyguard'));
}
register_activation_hook(__FILE__, 'buddyguard_activate');

// Log plugin deactivation
function buddyguard_deactivate() {
    delete_option('buddyguard_activation_time');
    buddyguard_log(__('Plugin Deactivated', 'buddyguard'), __('BuddyGuard plugin was deactivated.', 'buddyguard'));
}
register_deactivation_hook(__FILE__, 'buddyguard_deactivate');