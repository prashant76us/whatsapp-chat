<?php
// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'wcp_enqueue_scripts');
function wcp_enqueue_scripts() {
    wp_enqueue_style('wcp-styles', WCP_PLUGIN_URL . 'assets/css/whatsapp-chat.css');
    wp_enqueue_script('wcp-scripts', WCP_PLUGIN_URL . 'assets/js/whatsapp-chat.js', array('jquery'), '1.0', true);
    
    $options = get_option('wcp_settings');
    wp_localize_script('wcp-scripts', 'wcp_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

// Display floating button
add_action('wp_footer', 'wcp_display_floating_button');
function wcp_display_floating_button() {
    $options = get_option('wcp_settings');
    
    // Set default values if options don't exist
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
    
    // Merge options with defaults
    $options = wp_parse_args($options, $defaults);
    
    // Check if phone number is empty, if so don't display the button
    if (empty($options['phone_number'])) {
        return;
    }
    
    // Check display settings with proper isset checks
    $is_mobile = wp_is_mobile();
    
    // Safely check display preferences
    $show_on_mobile = isset($options['show_on_mobile']) ? $options['show_on_mobile'] : 'yes';
    $show_on_desktop = isset($options['show_on_desktop']) ? $options['show_on_desktop'] : 'yes';
    
    if (($is_mobile && $show_on_mobile !== 'yes') || 
        (!$is_mobile && $show_on_desktop !== 'yes')) {
        return;
    }
    
    $position = isset($options['button_position']) ? $options['button_position'] : 'right';
    $color = !empty($options['button_color']) ? $options['button_color'] : '#25D366';
    $phone = $options['phone_number'];
    $message = isset($options['message']) ? urlencode($options['message']) : urlencode('Hello, I need assistance!');
    $whatsapp_url = "https://wa.me/{$phone}?text={$message}";
    
    // Safely check group settings
    $group_enabled = isset($options['group_enabled']) ? $options['group_enabled'] : 'no';
    $group_link = isset($options['group_link']) ? $options['group_link'] : '';
    $group_name = isset($options['group_name']) ? $options['group_name'] : '';
    
    ?>
    <div class="wcp-floating-button <?php echo esc_attr($position); ?>" 
         style="--wcp-color: <?php echo esc_attr($color); ?>">
        
        <!-- Main Chat Button -->
        <div class="wcp-chat-button" id="wcp-main-button">
            <svg viewBox="0 0 24 24" width="30" height="30">
                <path fill="white" d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01zm-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23z"/>
            </svg>
        </div>
        
        <!-- Popup Box -->
        <div class="wcp-popup" id="wcp-popup">
            <div class="wcp-popup-header">
                <h3>Chat with us</h3>
                <span class="wcp-close">&times;</span>
            </div>
            <div class="wcp-popup-body">
                <?php if ($group_enabled === 'yes' && !empty($group_link)): ?>
                <div class="wcp-group-section">
                    <h4>Join our WhatsApp Group</h4>
                    <?php if (!empty($group_name)): ?>
                        <p><?php echo esc_html($group_name); ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($group_link); ?>" 
                       target="_blank" rel="noopener noreferrer" class="wcp-group-link">
                        Join Group
                    </a>
                </div>
                <div class="wcp-divider">
                    <span>or</span>
                </div>
                <?php endif; ?>
                
                <div class="wcp-chat-section">
                    <h4>Chat with us directly</h4>
                    <a href="<?php echo esc_url($whatsapp_url); ?>" 
                       target="_blank" rel="noopener noreferrer" class="wcp-chat-link">
                        Start Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Add admin styles
add_action('admin_head', 'wcp_admin_styles');
function wcp_admin_styles() {
    echo '<style>
        .wcp-color-picker {
            width: 100px;
            height: 40px;
            padding: 5px;
        }
    </style>';
}