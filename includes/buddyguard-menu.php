<?php
// Add the plugin settings page to the WordPress admin menu
function buddyguard_menu() {
    add_options_page(
        __('BuddyGuard Settings', 'buddyguard'),
        __('BuddyGuard', 'buddyguard'),
        'manage_options',
        'buddyguard-settings',
        'buddyguard_settings_page'
    );
}
add_action('admin_menu', 'buddyguard_menu');

// Create a settings page in the WordPress admin dashboard
function buddyguard_settings_page() {
    $components = [
        'members' => __('Members Page', 'buddyguard'),
        'activity' => __('Activity Component', 'buddyguard'),
        'groups' => __('Groups Component', 'buddyguard'), // Added groups component
        'forums' => __('Forums Component', 'buddyguard'),
        'profile' => __('Profile Component', 'buddyguard'),
        'about' => __('About', 'buddyguard'), // Add About tab
    ];
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'members'; // Default to the first tab

    ?>
    <div class="buddyguard-wrap">
        <h2><?php _e('BuddyGuard Settings', 'buddyguard'); ?></h2>

        <!-- Tab Navigation -->
        <nav class="buddyguard-nav-tab-wrapper">
            <?php foreach ($components as $key => $label) : ?>
                <?php
                $tab_url = add_query_arg('tab', $key, admin_url('options-general.php?page=buddyguard-settings'));
                $active_class = ($active_tab === $key) ? 'nav-tab-active' : '';
                ?>
                <a href="<?php echo esc_url($tab_url); ?>" class="buddyguard-nav-tab <?php echo esc_attr($active_class); ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Tab Content -->
        <div class="buddyguard-tab-content">
            <?php if ($active_tab === 'about') : ?>
                <?php buddyguard_render_about_tab(); ?>
            <?php else : ?>
                <form method="post" action="options.php">
                    <?php
                    settings_fields(BUDDYGUARD_SETTINGS_GROUP);
                    do_settings_sections(BUDDYGUARD_SETTINGS_GROUP);

                    // Render settings for all tabs, but only show the active tab
                    foreach ($components as $key => $label) {
                        if ($key === 'about') continue; // Skip About tab
                        $display_style = ($active_tab === $key) ? 'block' : 'none';
                        echo '<div id="' . esc_attr($key) . '-settings" style="display: ' . esc_attr($display_style) . ';">';
                        buddyguard_render_settings_section($key, $label);
                        echo '</div>';
                    }
                    ?>

                    <div class="buddyguard-button-container">
                        <?php submit_button(__('Save Current Settings', 'buddyguard'), 'primary', 'submit', false); ?>
                        <button type="button" id="buddyguard-reset"><?php _e('Reset All Settings', 'buddyguard'); ?></button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Plugin Uptime Counter -->
        <div class="buddyguard-plugin-uptime">
            <?php
            $activation_time = get_option('buddyguard_activation_time', time());
            $uptime = human_time_diff($activation_time, time());
            echo sprintf(__('Buddyguard is serving since: <strong>%s</strong>', 'buddyguard'), $uptime);
            ?>
        </div>

        <!-- Created with Love Message -->
        <div class="buddyguard-created-with-love">
            <?php _e('Created with ❤️ by', 'buddyguard'); ?> <a href="https://awhadi.online" target="_blank" rel="noopener noreferrer">Amir Khosro Awhadi</a>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            <?php
            // Render JavaScript toggles for the active tab
            if (isset($components[$active_tab]) && $active_tab !== 'about') {
                buddyguard_render_js_toggle($active_tab);
            }
            ?>

            // Reset button confirmation
            $('#buddyguard-reset').click(function() {
                if (confirm('<?php _e('Are you sure you want to reset all settings? This cannot be undone.', 'buddyguard'); ?>')) {
                    window.location.href = '<?php echo admin_url('options-general.php?page=buddyguard-settings&reset=1'); ?>';
                }
            });
        });
    </script>
    <?php

    // Handle reset action
    if (isset($_GET['reset']) && $_GET['reset'] == 1) {
        buddyguard_reset_settings();
        echo '<div class="notice notice-success"><p>' . __('All settings have been reset.', 'buddyguard') . '</p></div>';
    }
}

// Render About tab content
function buddyguard_render_about_tab() {
    global $wpdb;
    ?>
    <div class="buddyguard-about-tab">
        <!-- Left Column: Plugin Information and Debug Info -->
        <div class="buddyguard-left-column">
            <div class="buddyguard-info-box">
                <h3><?php _e('About BuddyGuard', 'buddyguard'); ?></h3>

                <!-- Plugin Information -->
                <div>
                    <ul>
                        <li><?php _e('Take full control of your BuddyBoss platform with BuddyGuard! Easily restrict access to BuddyBoss components (Members, Activity, Groups, Forums, and Profile) based on user roles, login status, and custom rules. Redirect users, display custom messages, and log all access attempts for enhanced security and customization. Perfect for creating exclusive communities, managing member access, and protecting sensitive content. Simplify BuddyBoss management with BuddyGuard today!', 'buddyguard'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Donation Button and Ads -->
        <div class="buddyguard-right-column">
            <!-- Donation Button -->
            <div class="buddyguard-donation-box">
                <h3><?php _e('Support BuddyGuard', 'buddyguard'); ?></h3>
                <p><?php _e('If you find BuddyGuard useful, consider supporting its development with a donation. Thank you!', 'buddyguard'); ?></p>
                <a href="https://www.paypal.com/donate/?hosted_button_id=ZNM9Q9LHFK8UE"" target="_blank" rel="noopener noreferrer">
                    <?php _e('Donate Now', 'buddyguard'); ?>
                </a>
            </div>

            <!-- Further WordPress Plugins -->
            <div class="buddyguard-info-box">
                <h3><?php _e('Further WordPress Plugins', 'buddyguard'); ?></h3>
                <ul>
                    <li><a href="https://wordpress.org/plugins/buddyboss-platform/" target="_blank" rel="noopener noreferrer">BuddyBoss Platform</a></li>
                    <li><a href="https://wordpress.org/plugins/woocommerce/" target="_blank" rel="noopener noreferrer">WooCommerce</a></li>
                    <li><a href="https://wordpress.org/plugins/yoast-seo/" target="_blank" rel="noopener noreferrer">Yoast SEO</a></li>
                    <li><a href="https://wordpress.org/plugins/elementor/" target="_blank" rel="noopener noreferrer">Elementor</a></li>
                    <li><a href="https://wordpress.org/plugins/akismet/" target="_blank" rel="noopener noreferrer">Akismet</a></li>
                </ul>
            </div>

            <!-- Ads -->
            <div class="buddyguard-ads-box">
                <h3><?php _e('Ads', 'buddyguard'); ?></h3>
                <div>
                    <a href="https://awhadi.online" target="_blank" rel="noopener noreferrer">
                        <img src="https://via.placeholder.com/300x250" alt="Ad Placeholder">
                    </a>
                    <p><?php _e('Sponsored by', 'buddyguard'); ?> <a href="https://awhadi.online" target="_blank" rel="noopener noreferrer">Awhadi Studio</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Reset all plugin settings
function buddyguard_reset_settings() {
    $components = ['members', 'activity', 'groups', 'forums', 'profile'];
    foreach ($components as $key) {
        delete_option('block_' . $key);
        delete_option($key . '_exempt_roles');
        delete_option($key . '_logged_in_redirect_option');
        delete_option($key . '_logged_in_custom_redirect_url');
        delete_option($key . '_not_logged_in_redirect_option');
        delete_option($key . '_not_logged_in_custom_redirect_url');
        delete_option($key . '_logged_in_no_redirect_message');
        delete_option($key . '_not_logged_in_no_redirect_message');
    }

    // Log the reset action
    buddyguard_log(__('All Settings Reset', 'buddyguard'), __('All plugin settings were reset.', 'buddyguard'));
}

// Render settings section for each component
function buddyguard_render_settings_section($key, $label) {
    $roles = get_editable_roles(); // Get all editable roles
    ?>
    <div class="buddyguard-settings-section">
        <h3><?php echo esc_html($label); ?></h3>
        <table class="buddyguard-form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Block', 'buddyguard'); ?> <?php echo esc_html($label); ?></th>
                <td>
                    <label for="block_<?php echo esc_attr($key); ?>">
                        <input type="checkbox" id="block_<?php echo esc_attr($key); ?>" name="block_<?php echo esc_attr($key); ?>" value="1" <?php checked(get_option('block_' . $key), 1); ?> />
                        <?php _e('Block access to the BuddyBoss', 'buddyguard'); ?> <?php echo esc_html(strtolower($label)); ?>
                    </label>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_exempt_roles" style="display: none;">
                <th scope="row"><?php _e('Exempt User Roles', 'buddyguard'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($key); ?>_exempt_roles[]" id="<?php echo esc_attr($key); ?>_exempt_roles" multiple>
                        <?php foreach ($roles as $role => $details) : ?>
                            <option value="<?php echo esc_attr($role); ?>" <?php selected(in_array($role, (array) get_option($key . '_exempt_roles', [])), true); ?>>
                                <?php echo esc_html($details['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p><?php _e('Selected roles will bypass blocking and redirection.', 'buddyguard'); ?></p>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_logged_in_redirect_options" style="display: none;">
                <th scope="row"><?php _e('Logged-In Users Redirect', 'buddyguard'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($key); ?>_logged_in_redirect_option" id="<?php echo esc_attr($key); ?>_logged_in_redirect_option">
                        <option value="no_redirect" <?php selected(get_option($key . '_logged_in_redirect_option'), 'no_redirect'); ?>><?php _e('No Redirect (Block Access)', 'buddyguard'); ?></option>
                        <option value="homepage" <?php selected(get_option($key . '_logged_in_redirect_option'), 'homepage'); ?>><?php _e('Redirect to Homepage', 'buddyguard'); ?></option>
                        <option value="login" <?php selected(get_option($key . '_logged_in_redirect_option'), 'login'); ?>><?php _e('Redirect to Login Page', 'buddyguard'); ?></option>
                        <option value="registration" <?php selected(get_option($key . '_logged_in_redirect_option'), 'registration'); ?>><?php _e('Redirect to Registration Page', 'buddyguard'); ?></option>
                        <option value="allow_access" <?php selected(get_option($key . '_logged_in_redirect_option'), 'allow_access'); ?>><?php _e('Allow Access', 'buddyguard'); ?></option>
                        <option value="404" <?php selected(get_option($key . '_logged_in_redirect_option'), '404'); ?>><?php _e('404 Page Not Found', 'buddyguard'); ?></option>
                        <option value="custom" <?php selected(get_option($key . '_logged_in_redirect_option'), 'custom'); ?>><?php _e('Custom URL', 'buddyguard'); ?></option>
                    </select>
                    <p><?php _e('Select "Custom URL" to redirect users to a specific page. Enter the full URL (e.g., https://example.com/custom-page).', 'buddyguard'); ?></p>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_logged_in_custom_url_input" style="display: none;">
                <th scope="row"><?php _e('Logged-In Users Custom Redirect URL', 'buddyguard'); ?></th>
                <td>
                    <input type="text" name="<?php echo esc_attr($key); ?>_logged_in_custom_redirect_url" id="<?php echo esc_attr($key); ?>_logged_in_custom_redirect_url" value="<?php echo esc_attr(get_option($key . '_logged_in_custom_redirect_url', '')); ?>" />
                    <p><?php _e('Enter the full URL (e.g., https://example.com/custom-page).', 'buddyguard'); ?></p>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_logged_in_no_redirect_message" style="display: none;">
                <th scope="row"><?php _e('Logged-In Users Block Message', 'buddyguard'); ?></th>
                <td>
                    <textarea name="<?php echo esc_attr($key); ?>_logged_in_no_redirect_message" id="<?php echo esc_attr($key); ?>_logged_in_no_redirect_message"><?php echo esc_textarea(get_option($key . '_logged_in_no_redirect_message', __('Access Denied. You do not have permission to view this page.', 'buddyguard'))); ?></textarea>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_not_logged_in_redirect_options" style="display: none;">
                <th scope="row"><?php _e('Non-Logged-In Users Redirect', 'buddyguard'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($key); ?>_not_logged_in_redirect_option" id="<?php echo esc_attr($key); ?>_not_logged_in_redirect_option">
                        <option value="no_redirect" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'no_redirect'); ?>><?php _e('No Redirect (Block Access)', 'buddyguard'); ?></option>
                        <option value="homepage" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'homepage'); ?>><?php _e('Redirect to Homepage', 'buddyguard'); ?></option>
                        <option value="login" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'login'); ?>><?php _e('Redirect to Login Page', 'buddyguard'); ?></option>
                        <option value="registration" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'registration'); ?>><?php _e('Redirect to Registration Page', 'buddyguard'); ?></option>
                        <option value="allow_access" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'allow_access'); ?>><?php _e('Allow Access', 'buddyguard'); ?></option>
                        <option value="404" <?php selected(get_option($key . '_not_logged_in_redirect_option'), '404'); ?>><?php _e('404 Page Not Found', 'buddyguard'); ?></option>
                        <option value="custom" <?php selected(get_option($key . '_not_logged_in_redirect_option'), 'custom'); ?>><?php _e('Custom URL', 'buddyguard'); ?></option>
                    </select>
                    <p><?php _e('Select "Custom URL" to redirect users to a specific page. Enter the full URL (e.g., https://example.com/custom-page).', 'buddyguard'); ?></p>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_not_logged_in_custom_url_input" style="display: none;">
                <th scope="row"><?php _e('Non-Logged-In Users Custom Redirect URL', 'buddyguard'); ?></th>
                <td>
                    <input type="text" name="<?php echo esc_attr($key); ?>_not_logged_in_custom_redirect_url" id="<?php echo esc_attr($key); ?>_not_logged_in_custom_redirect_url" value="<?php echo esc_attr(get_option($key . '_not_logged_in_custom_redirect_url', '')); ?>" />
                    <p><?php _e('Enter the full URL (e.g., https://example.com/custom-page).', 'buddyguard'); ?></p>
                </td>
            </tr>
            <tr valign="top" id="<?php echo esc_attr($key); ?>_not_logged_in_no_redirect_message" style="display: none;">
                <th scope="row"><?php _e('Non-Logged-In Users Block Message', 'buddyguard'); ?></th>
                <td>
                    <textarea name="<?php echo esc_attr($key); ?>_not_logged_in_no_redirect_message" id="<?php echo esc_attr($key); ?>_not_logged_in_no_redirect_message"><?php echo esc_textarea(get_option($key . '_not_logged_in_no_redirect_message', __('Access Denied. You do not have permission to view this page.', 'buddyguard'))); ?></textarea>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

// Render JavaScript to toggle visibility of redirect options
function buddyguard_render_js_toggle($key) {
    ?>
    // Function to toggle visibility based on block checkbox and redirect options
    function toggleVisibility_<?php echo esc_js($key); ?>() {
        if ($('#block_<?php echo esc_js($key); ?>').is(':checked')) {
            $('#<?php echo esc_js($key); ?>_logged_in_redirect_options').show();
            $('#<?php echo esc_js($key); ?>_not_logged_in_redirect_options').show();
            $('#<?php echo esc_js($key); ?>_exempt_roles').show();

            // Show or hide custom URL inputs based on selected redirect options
            if ($('#<?php echo esc_js($key); ?>_logged_in_redirect_option').val() === 'custom') {
                $('#<?php echo esc_js($key); ?>_logged_in_custom_url_input').show();
            } else {
                $('#<?php echo esc_js($key); ?>_logged_in_custom_url_input').hide();
            }
            if ($('#<?php echo esc_js($key); ?>_not_logged_in_redirect_option').val() === 'custom') {
                $('#<?php echo esc_js($key); ?>_not_logged_in_custom_url_input').show();
            } else {
                $('#<?php echo esc_js($key); ?>_not_logged_in_custom_url_input').hide();
            }

            // Show or hide message text areas based on "No Redirect" selection
            if ($('#<?php echo esc_js($key); ?>_logged_in_redirect_option').val() === 'no_redirect') {
                $('#<?php echo esc_js($key); ?>_logged_in_no_redirect_message').show();
            } else {
                $('#<?php echo esc_js($key); ?>_logged_in_no_redirect_message').hide();
            }
            if ($('#<?php echo esc_js($key); ?>_not_logged_in_redirect_option').val() === 'no_redirect') {
                $('#<?php echo esc_js($key); ?>_not_logged_in_no_redirect_message').show();
            } else {
                $('#<?php echo esc_js($key); ?>_not_logged_in_no_redirect_message').hide();
            }
        } else {
            // Hide all related fields if the block checkbox is unchecked
            $('#<?php echo esc_js($key); ?>_logged_in_redirect_options').hide();
            $('#<?php echo esc_js($key); ?>_logged_in_custom_url_input').hide();
            $('#<?php echo esc_js($key); ?>_not_logged_in_redirect_options').hide();
            $('#<?php echo esc_js($key); ?>_not_logged_in_custom_url_input').hide();
            $('#<?php echo esc_js($key); ?>_exempt_roles').hide();
            $('#<?php echo esc_js($key); ?>_logged_in_no_redirect_message').hide();
            $('#<?php echo esc_js($key); ?>_not_logged_in_no_redirect_message').hide();
        }
    }

    // Bind change events to the block checkbox and redirect options
    $('#block_<?php echo esc_js($key); ?>').change(function() {
        toggleVisibility_<?php echo esc_js($key); ?>();
    });

    $('#<?php echo esc_js($key); ?>_logged_in_redirect_option').change(function() {
        if ($(this).val() === 'custom') {
            $('#<?php echo esc_js($key); ?>_logged_in_custom_url_input').show();
        } else {
            $('#<?php echo esc_js($key); ?>_logged_in_custom_url_input').hide();
        }
        if ($(this).val() === 'no_redirect') {
            $('#<?php echo esc_js($key); ?>_logged_in_no_redirect_message').show();
        } else {
            $('#<?php echo esc_js($key); ?>_logged_in_no_redirect_message').hide();
        }
    });

    $('#<?php echo esc_js($key); ?>_not_logged_in_redirect_option').change(function() {
        if ($(this).val() === 'custom') {
            $('#<?php echo esc_js($key); ?>_not_logged_in_custom_url_input').show();
        } else {
            $('#<?php echo esc_js($key); ?>_not_logged_in_custom_url_input').hide();
        }
        if ($(this).val() === 'no_redirect') {
            $('#<?php echo esc_js($key); ?>_not_logged_in_no_redirect_message').show();
        } else {
            $('#<?php echo esc_js($key); ?>_not_logged_in_no_redirect_message').hide();
        }
    });

    // Trigger initial state on page load
    toggleVisibility_<?php echo esc_js($key); ?>();
    <?php
}