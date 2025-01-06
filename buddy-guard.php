<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Plugin Name: BuddyGuard
 * Plugin URI: https://plugins.awhadi.online/buddyguard
 * Description: Take full control of your BuddyBoss platform with BuddyGuard! Easily restrict access to BuddyBoss components (Members, Activity, Groups, Forums, and Profile) based on user roles, login status, and custom rules. Redirect users, display custom messages, and log all access attempts for enhanced security and customization. Perfect for creating exclusive communities, managing member access, and protecting sensitive content. Simplify BuddyBoss management with BuddyGuard today!
 * Version: 1.0.0
 * Author: Amir Khosro Awhadi
 * Author URI: https://plugins.awhadi.online/buddyguard
 * Text Domain: buddyguard
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Tested up to: 6.7
 * Update URI: https://plugins.awhadi.online/buddyguard
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Define constants for plugin settings
define('BUDDYGUARD_SETTINGS_GROUP', 'buddyguard-settings');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-enqueue.php';
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-logging.php';
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-redirect.php';
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/buddyguard-menu.php';

// Add settings link to the plugin on the plugins page
function buddyguard_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=buddyguard-settings') . '">' . __('Settings', 'buddyguard') . '</a>';
    array_unshift($links, $settings_link); // Add the link to the beginning of the array
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'buddyguard_add_settings_link');

// Add about link to the plugin meta row
function buddyguard_add_about_link($links, $file) {
    if (plugin_basename(__FILE__) === $file) {
        $about_link = '<a href="' . admin_url('options-general.php?page=buddyguard-settings&tab=about') . '">' . __('About', 'buddyguard') . '</a>';
        $links[] = $about_link; // Add the link to the end of the array
    }
    return $links;
}
add_filter('plugin_row_meta', 'buddyguard_add_about_link', 10, 2);