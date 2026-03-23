<?php
// Add admin menu
add_action('admin_menu', 'wcp_add_admin_menu');
function wcp_add_admin_menu() {
    add_menu_page(
        'WhatsApp Chat Settings',
        'WhatsApp Chat',
        'manage_options',
        'whatsapp-chat-settings',
        'wcp_settings_page',
        'dashicons-format-chat',
        100
    );
    
    add_submenu_page(
        'whatsapp-chat-settings',
        'Group Settings',
        'Group Settings',
        'manage_options',
        'whatsapp-group-settings',
        'wcp_group_settings_page'
    );
}

// Register settings
add_action('admin_init', 'wcp_register_settings');
function wcp_register_settings() {
    register_setting('wcp_settings_group', 'wcp_settings');
    
    // Main Settings Section
    add_settings_section(
        'wcp_main_section',
        'Main Settings',
        'wcp_main_section_callback',
        'whatsapp-chat-settings'
    );
    
    add_settings_field(
        'phone_number',
        'Phone Number',
        'wcp_phone_number_callback',
        'whatsapp-chat-settings',
        'wcp_main_section'
    );
    
    add_settings_field(
        'button_color',
        'Button Color',
        'wcp_button_color_callback',
        'whatsapp-chat-settings',
        'wcp_main_section'
    );
    
    add_settings_field(
        'button_position',
        'Button Position',
        'wcp_button_position_callback',
        'whatsapp-chat-settings',
        'wcp_main_section'
    );
    
    add_settings_field(
        'message',
        'Default Message',
        'wcp_message_callback',
        'whatsapp-chat-settings',
        'wcp_main_section'
    );
    
    add_settings_field(
        'display_settings',
        'Display Settings',
        'wcp_display_settings_callback',
        'whatsapp-chat-settings',
        'wcp_main_section'
    );
}

// Section callback
function wcp_main_section_callback() {
    echo '<p>Configure your WhatsApp chat button settings below:</p>';
}

// Field callbacks
function wcp_phone_number_callback() {
    $options = get_option('wcp_settings');
    $phone_number = isset($options['phone_number']) ? $options['phone_number'] : '';
    ?>
    <input type="text" name="wcp_settings[phone_number]" 
           value="<?php echo esc_attr($phone_number); ?>" 
           class="regular-text" placeholder="+1234567890">
    <p class="description">Enter phone number with country code (e.g., +1234567890)</p>
    <?php
}

function wcp_button_color_callback() {
    $options = get_option('wcp_settings');
    $button_color = isset($options['button_color']) ? $options['button_color'] : '#25D366';
    ?>
    <input type="color" name="wcp_settings[button_color]" 
           value="<?php echo esc_attr($button_color); ?>">
    <?php
}

function wcp_button_position_callback() {
    $options = get_option('wcp_settings');
    $button_position = isset($options['button_position']) ? $options['button_position'] : 'right';
    ?>
    <select name="wcp_settings[button_position]">
        <option value="left" <?php selected($button_position, 'left'); ?>>Left</option>
        <option value="right" <?php selected($button_position, 'right'); ?>>Right</option>
    </select>
    <?php
}

function wcp_message_callback() {
    $options = get_option('wcp_settings');
    $message = isset($options['message']) ? $options['message'] : 'Hello, I need assistance!';
    ?>
    <textarea name="wcp_settings[message]" rows="3" class="large-text"><?php 
        echo esc_textarea($message); 
    ?></textarea>
    <p class="description">Default message that will appear in WhatsApp chat</p>
    <?php
}

function wcp_display_settings_callback() {
    $options = get_option('wcp_settings');
    $show_on_mobile = isset($options['show_on_mobile']) ? $options['show_on_mobile'] : 'yes';
    $show_on_desktop = isset($options['show_on_desktop']) ? $options['show_on_desktop'] : 'yes';
    ?>
    <label>
        <input type="checkbox" name="wcp_settings[show_on_mobile]" value="yes" 
            <?php checked($show_on_mobile, 'yes'); ?>>
        Show on Mobile
    </label>
    <br>
    <label>
        <input type="checkbox" name="wcp_settings[show_on_desktop]" value="yes" 
            <?php checked($show_on_desktop, 'yes'); ?>>
        Show on Desktop
    </label>
    <?php
}

// Group Settings Page
function wcp_group_settings_page() {
    $options = get_option('wcp_settings');
    $defaults = array(
        'group_enabled' => 'no',
        'group_link' => '',
        'group_name' => ''
    );
    $options = wp_parse_args($options, $defaults);
    
    if (isset($_POST['submit'])) {
        // Verify nonce for security
        if (!isset($_POST['wcp_group_nonce']) || !wp_verify_nonce($_POST['wcp_group_nonce'], 'wcp_group_settings')) {
            echo '<div class="notice notice-error"><p>Security check failed!</p></div>';
            return;
        }
        
        $options['group_enabled'] = isset($_POST['group_enabled']) ? 'yes' : 'no';
        $options['group_link'] = esc_url_raw($_POST['group_link']);
        $options['group_name'] = sanitize_text_field($_POST['group_name']);
        update_option('wcp_settings', $options);
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>WhatsApp Group Settings</h1>
        <form method="post">
            <?php wp_nonce_field('wcp_group_settings', 'wcp_group_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Group Chat</th>
                    <td>
                        <label>
                            <input type="checkbox" name="group_enabled" value="yes" 
                                <?php checked($options['group_enabled'], 'yes'); ?>>
                            Enable WhatsApp Group Link
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Group Name</th>
                    <td>
                        <input type="text" name="group_name" 
                               value="<?php echo esc_attr($options['group_name']); ?>" 
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Group Invite Link</th>
                    <td>
                        <input type="url" name="group_link" 
                               value="<?php echo esc_attr($options['group_link']); ?>" 
                               class="regular-text" 
                               placeholder="https://chat.whatsapp.com/...">
                        <p class="description">Enter your WhatsApp group invite link</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Main settings page
function wcp_settings_page() {
    ?>
    <div class="wrap">
        <h1>WhatsApp Chat Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wcp_settings_group');
            do_settings_sections('whatsapp-chat-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}