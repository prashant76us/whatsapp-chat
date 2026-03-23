<?php
/**
 * Plugin Name: WhatsApp Chat Plugin
 * Plugin URI: https://hotsweb.in/
 * Description: Add a floating WhatsApp chat button to your website with customizable settings
 * Version: 1.0.0
 * Author: Prashant J.
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCP_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once WCP_PLUGIN_PATH . 'includes/admin-settings.php';
require_once WCP_PLUGIN_PATH . 'includes/frontend-display.php';

// Activation hook
register_activation_hook(__FILE__, 'wcp_activate_plugin');
function wcp_activate_plugin() {
    $default_settings = array(
        'phone_number' => '',
        'button_color' => '#25D366',
        'button_position' => 'right',
        'message' => 'Hello, I need assistance!',
        'show_on_mobile' => 'yes',
        'show_on_desktop' => 'yes',
        'group_enabled' => 'no',
        'group_link' => '',
        'group_name' => ''
    );
    
    // Check if settings already exist
    $existing_settings = get_option('wcp_settings');
    
    if ($existing_settings === false) {
        // No settings exist, add new ones
        add_option('wcp_settings', $default_settings);
    } else {
        // Settings exist, merge with defaults to ensure all keys are present
        $updated_settings = wp_parse_args($existing_settings, $default_settings);
        update_option('wcp_settings', $updated_settings);
    }
}

// Add settings link on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wcp_add_settings_link');
function wcp_add_settings_link($links) {
    $settings_link = '<a href="admin.php?page=whatsapp-chat-settings">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

// Initialize plugin with default values for existing installations
add_action('init', 'wcp_initialize_settings');
function wcp_initialize_settings() {
    $options = get_option('wcp_settings');
    $defaults = array(
        'phone_number' => '',
        'button_color' => '#25D366',
        'button_position' => 'right',
        'message' => 'Hello, I need assistance!',
        'show_on_mobile' => 'yes',
        'show_on_desktop' => 'yes',
        'group_enabled' => 'no',
        'group_link' => '',
        'group_name' => ''
    );
    
    if (is_array($options)) {
        $options = wp_parse_args($options, $defaults);
        update_option('wcp_settings', $options);
    }
}