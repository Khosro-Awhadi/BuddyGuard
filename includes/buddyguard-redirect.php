<?php
// Redirect users based on the selected options
function buddyguard_redirect() {
    $components = [
        'members' => ['is_page' => 'members', 'bp_is' => 'bp_is_members_component'],
        'activity' => ['bp_is' => 'bp_is_activity_directory'],
        'groups' => ['bp_is' => 'bp_is_groups_component'], // Added groups component
        'forums' => ['bp_is' => 'bp_is_forums_component'], // Ensure this is correct
        'profile' => ['bp_is' => 'bp_is_user_profile'],
    ];

    foreach ($components as $key => $conditions) {
        if (get_option('block_' . $key) == 1 && buddyguard_check_conditions($conditions)) {
            // Get exempt roles for this component
            $exempt_roles = (array) get_option($key . '_exempt_roles', []);
            $user = wp_get_current_user();

            // Check if the user has an exempt role
            if (array_intersect($exempt_roles, $user->roles)) {
                buddyguard_log(__('Exempt Role Bypass', 'buddyguard'), sprintf(__('User "%s" bypassed blocking for component "%s".', 'buddyguard'), $user->user_login, $key));
                return; // Skip blocking and redirection
            }

            // Log the redirect action
            buddyguard_log(__('Redirect Triggered', 'buddyguard'), sprintf(__('User "%s" was redirected from component "%s".', 'buddyguard'), $user->user_login, $key));

            // Existing redirect logic...
            if (is_user_logged_in()) {
                $redirect_option = get_option($key . '_logged_in_redirect_option');
                $custom_redirect_url = get_option($key . '_logged_in_custom_redirect_url');
                $no_redirect_message = get_option($key . '_logged_in_no_redirect_message', __('Access Denied. You do not have permission to view this page.', 'buddyguard'));
            } else {
                $redirect_option = get_option($key . '_not_logged_in_redirect_option');
                $custom_redirect_url = get_option($key . '_not_logged_in_custom_redirect_url');
                $no_redirect_message = get_option($key . '_not_logged_in_no_redirect_message', __('Access Denied. You do not have permission to view this page.', 'buddyguard'));
            }

            // Handle "No Redirect" option
            if ($redirect_option === 'no_redirect') {
                // Block access without redirecting and show the custom message
                wp_die($no_redirect_message, __('Access Denied', 'buddyguard'), ['response' => 403]);
            } elseif ($redirect_option === 'allow_access') {
                // Allow access without redirecting
                return;
            } else {
                // Redirect based on the selected option
                buddyguard_handle_redirect($redirect_option, $custom_redirect_url);
            }
        }
    }
}
add_action('template_redirect', 'buddyguard_redirect');

// Check conditions for redirection
function buddyguard_check_conditions($conditions) {
    if (isset($conditions['is_page']) && is_page($conditions['is_page'])) {
        return true;
    }
    if (isset($conditions['bp_is']) && function_exists($conditions['bp_is']) && call_user_func($conditions['bp_is'])) {
        return true;
    }
    return false;
}

// Handle redirection based on the selected option
function buddyguard_handle_redirect($redirect_option, $custom_redirect_url) {
    switch ($redirect_option) {
        case 'homepage':
            $redirect_url = home_url();
            break;
        case 'login':
            $redirect_url = wp_login_url();
            break;
        case 'registration':
            $redirect_url = wp_registration_url();
            break;
        case 'custom':
            if (!empty($custom_redirect_url)) {
                $redirect_url = esc_url_raw($custom_redirect_url);
            } else {
                $redirect_url = home_url(); // Fallback to homepage if custom URL is empty
            }
            break;
        case '404':
            global $wp_query;
            $wp_query->set_404(); // Set the query to 404
            status_header(404); // Set the HTTP status to 404
            get_template_part(404); // Load the 404 template
            exit; // Stop further execution
        default:
            $redirect_url = home_url(); // Default to homepage
            break;
    }

    if (isset($redirect_url)) {
        wp_redirect($redirect_url);
        exit;
    }
}