<?php
function buddyguard_enqueue_styles() {
    // Enqueue the CSS file
    wp_enqueue_style(
        'buddyguard-css', // Handle
        plugins_url('/assets/css/buddyguard.css', dirname(__FILE__) . '/../buddy-guard.php'), // Correct path
        array(), // Dependencies
        '1.0', // Version
        'all' // Media
    );
}
add_action('admin_enqueue_scripts', 'buddyguard_enqueue_styles');