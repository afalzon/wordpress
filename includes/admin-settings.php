<?php

// 1. Register the Admin Menu
function mcs_add_admin_menu() {
    add_menu_page(
        'Theme Customisation', 'Site Settings', 'manage_options', 'my-custom-settings', 'mcs_options_page_html', 'dashicons-admin-generic', 99
    );
}
add_action('admin_menu', 'mcs_add_admin_menu');

// 2. Initialize Settings
function mcs_settings_init() {
    // --- GENERAL ---
    register_setting('mcs_general_group', 'mcs_abn');
    register_setting('mcs_general_group', 'mcs_bank_acc_name');
    register_setting('mcs_general_group', 'mcs_bank_bsb');
    register_setting('mcs_general_group', 'mcs_bank_acc_num');
    register_setting('mcs_general_group', 'mcs_update_branch');

    add_settings_section('mcs_general_section', 'Business Details', 'mcs_general_section_callback', 'mcs_general_group');
    add_settings_field('mcs_abn', 'Australian Business Number (ABN)', 'mcs_abn_render', 'mcs_general_group', 'mcs_general_section');
    add_settings_field('mcs_update_branch', 'Update Branch', 'mcs_update_branch_render', 'mcs_general_group', 'mcs_general_section');
    add_settings_section('mcs_bank_section', 'Bank Details', 'mcs_bank_section_callback', 'mcs_general_group');
    add_settings_field('mcs_bank_acc_name', 'Account Name', 'mcs_bank_acc_name_render', 'mcs_general_group', 'mcs_bank_section');
    add_settings_field('mcs_bank_bsb', 'BSB', 'mcs_bank_bsb_render', 'mcs_general_group', 'mcs_bank_section');
    add_settings_field('mcs_bank_acc_num', 'Account Number', 'mcs_bank_acc_num_render', 'mcs_general_group', 'mcs_bank_section');

    // --- FRONT END ---
    register_setting('mcs_frontend_group', 'mcs_show_abn_footer');
    add_settings_section('mcs_frontend_section', 'Footer Options', 'mcs_frontend_section_callback', 'mcs_frontend_group');
    add_settings_field('mcs_show_abn_footer', 'Show ABN in Footer', 'mcs_show_abn_render', 'mcs_frontend_group', 'mcs_frontend_section');

    // --- BACK END ---
    register_setting('mcs_backend_group', 'mcs_enable_bank_action');
    register_setting('mcs_backend_group', 'mcs_enable_min_order');
    register_setting('mcs_backend_group', 'mcs_min_order_amount');
    register_setting('mcs_backend_group', 'mcs_min_order_ignore_virtual');
    add_settings_section('mcs_backend_section', 'WooCommerce Options', 'mcs_backend_section_callback', 'mcs_backend_group');
    add_settings_field('mcs_enable_bank_action', 'Order Actions', 'mcs_enable_bank_action_render', 'mcs_backend_group', 'mcs_backend_section');
    add_settings_field('mcs_min_order_amount', 'Minimum Order Amount ($)', 'mcs_min_order_amount_render', 'mcs_backend_group', 'mcs_backend_section');

    // --- HOLIDAYS ---
    register_setting('mcs_holidays_group', 'mcs_default_holiday_msg');
    register_setting('mcs_holidays_group', 'mcs_holidays_list', 'mcs_sanitize_array_callback');
    add_settings_section('mcs_holidays_section', 'Store Holiday Scheduler', 'mcs_holidays_section_callback', 'mcs_holidays_group');
    add_settings_field('mcs_default_holiday_msg', 'Default Holiday Message', 'mcs_default_holiday_msg_render', 'mcs_holidays_group', 'mcs_holidays_section');
    add_settings_field('mcs_holidays_list', 'Scheduled Holidays', 'mcs_holidays_list_render', 'mcs_holidays_group', 'mcs_holidays_section');

    // --- DELIVERY / PICKUP (New) ---
    register_setting('mcs_delivery_group', 'mcs_enable_delivery_slots');
    register_setting('mcs_delivery_group', 'mcs_delivery_slots_list', 'mcs_sanitize_array_callback');
    add_settings_section('mcs_delivery_section', 'Delivery & Pickup Options', 'mcs_delivery_section_callback', 'mcs_delivery_group');
    add_settings_field('mcs_enable_delivery_slots', 'Enable Feature', 'mcs_enable_delivery_slots_render', 'mcs_delivery_group', 'mcs_delivery_section');
    add_settings_field('mcs_delivery_slots_list', 'Time Slots', 'mcs_delivery_slots_list_render', 'mcs_delivery_group', 'mcs_delivery_section');
}
add_action('admin_init', 'mcs_settings_init');

// 3. Render the Admin Page HTML
function mcs_options_page_html() {
    if (!current_user_can('manage_options')) return;
    $default_tab = 'general';
    $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <nav class="nav-tab-wrapper">
            <a href="?page=my-custom-settings&tab=general" class="nav-tab <?php echo $tab === 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
            <a href="?page=my-custom-settings&tab=frontend" class="nav-tab <?php echo $tab === 'frontend' ? 'nav-tab-active' : ''; ?>">Front End</a>
            <a href="?page=my-custom-settings&tab=backend" class="nav-tab <?php echo $tab === 'backend' ? 'nav-tab-active' : ''; ?>">Back End</a>
            <a href="?page=my-custom-settings&tab=holidays" class="nav-tab <?php echo $tab === 'holidays' ? 'nav-tab-active' : ''; ?>">Holidays</a>
            <a href="?page=my-custom-settings&tab=delivery" class="nav-tab <?php echo $tab === 'delivery' ? 'nav-tab-active' : ''; ?>">Delivery/Pickup</a>
        </nav>
        <form action="options.php" method="post">
            <?php
            if ($tab === 'general') { settings_fields('mcs_general_group'); do_settings_sections('mcs_general_group'); }
            elseif ($tab === 'frontend') { settings_fields('mcs_frontend_group'); do_settings_sections('mcs_frontend_group'); }
            elseif ($tab === 'backend') { settings_fields('mcs_backend_group'); do_settings_sections('mcs_backend_group'); }
            elseif ($tab === 'holidays') { settings_fields('mcs_holidays_group'); do_settings_sections('mcs_holidays_group'); }
            elseif ($tab === 'delivery') { settings_fields('mcs_delivery_group'); do_settings_sections('mcs_delivery_group'); }
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// --- RENDER CALLBACKS ---
function mcs_general_section_callback() { echo '<p>Enter your core business details here.</p>'; }
function mcs_bank_section_callback() { echo '<p>Enter your bank details below.</p>'; }
function mcs_frontend_section_callback() { echo '<p>Control how elements appear on the public facing site.</p>'; }
function mcs_backend_section_callback() { echo '<p>Tools to assist with Order Management.</p>'; }
function mcs_holidays_section_callback() { echo '<p>Manage dates when the store will be closed.</p>'; }
function mcs_delivery_section_callback() { echo '<p>Manage the dates and time slots customers can choose at checkout.</p>'; }

// Simple Fields
function mcs_abn_render() { echo '<input type="text" name="mcs_abn" value="' . esc_attr(get_option('mcs_abn')) . '" class="regular-text">'; }

function mcs_update_branch_render() {
    $current_branch = get_option('mcs_update_branch', 'main'); // Default to 'main'
    ?>
    <select name="mcs_update_branch">
        <option value="main" <?php selected($current_branch, 'main'); ?>>Main (Stable)</option>
        <option value="dev" <?php selected($current_branch, 'dev'); ?>>Development</option>
    </select>
    <p class="description">Select the GitHub branch to check for updates. 'Development' may be unstable.</p>
    <?php
}

function mcs_bank_acc_name_render() { echo '<input type="text" name="mcs_bank_acc_name" value="' . esc_attr(get_option('mcs_bank_acc_name')) . '" class="regular-text">'; }
function mcs_bank_bsb_render() { echo '<input type="text" name="mcs_bank_bsb" value="' . esc_attr(get_option('mcs_bank_bsb')) . '" class="regular-text">'; }
function mcs_bank_acc_num_render() { echo '<input type="text" name="mcs_bank_acc_num" value="' . esc_attr(get_option('mcs_bank_acc_num')) . '" class="regular-text">'; }
function mcs_show_abn_render() { echo '<label><input type="checkbox" name="mcs_show_abn_footer" value="1" ' . checked(1, get_option('mcs_show_abn_footer'), false) . ' /> Display ABN in footer.</label>'; }
function mcs_enable_bank_action_render() { echo '<label><input type="checkbox" name="mcs_enable_bank_action" value="1" ' . checked(1, get_option('mcs_enable_bank_action'), false) . ' /> Enable "Send Bank Details" action.</label>'; }

function mcs_min_order_amount_render() {
    $enabled = get_option('mcs_enable_min_order');
    $amount = get_option('mcs_min_order_amount');
    $ignore_virtual = get_option('mcs_min_order_ignore_virtual');
    ?>
    <fieldset>
        <label><input type="checkbox" name="mcs_enable_min_order" value="1" <?php checked(1, $enabled, true); ?> /> Enable Minimum Order</label><br><br>
        <label>Minimum Amount ($): <input type="number" step="0.01" name="mcs_min_order_amount" value="<?php echo esc_attr($amount); ?>" class="small-text"></label><br><br>
        <label><input type="checkbox" name="mcs_min_order_ignore_virtual" value="1" <?php checked(1, $ignore_virtual, true); ?> /> Ignore for virtual products.</label>
    </fieldset>
    <?php
}

// Holiday Fields
function mcs_default_holiday_msg_render() {
    $msg = get_option('mcs_default_holiday_msg', 'Our store is currently closed for holidays.');
    echo '<textarea name="mcs_default_holiday_msg" rows="3" class="large-text code">' . esc_textarea($msg) . '</textarea>';
}

function mcs_holidays_list_render() {
    $holidays = get_option('mcs_holidays_list', array());
    // (Re-using the repeater logic, simplified for brevity in this step but fully functional)
    ?>
    <div id="mcs-holiday-wrapper">
        <?php if (!empty($holidays)) : foreach ($holidays as $key => $h) : ?>
            <div class="holiday-row" style="background:#f9f9f9; padding:10px; margin-bottom:10px; border:1px solid #ddd;">
                Start: <input type="date" name="mcs_holidays_list[<?php echo $key; ?>][start]" value="<?php echo esc_attr($h['start']); ?>">
                End: <input type="date" name="mcs_holidays_list[<?php echo $key; ?>][end]" value="<?php echo esc_attr($h['end']); ?>">
                <label><input type="checkbox" name="mcs_holidays_list[<?php echo $key; ?>][use_default]" value="1" <?php checked(1, isset($h['use_default'])?$h['use_default']:0, true); ?>> Use Default</label>
                <br><input type="text" name="mcs_holidays_list[<?php echo $key; ?>][msg]" value="<?php echo esc_attr(isset($h['msg'])?$h['msg']:''); ?>" class="widefat" placeholder="Custom Message">
                <button type="button" class="button remove-row" style="margin-top:5px;">Remove</button>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <button type="button" class="button" id="mcs-add-holiday">Add New Holiday</button>
    <script>
        jQuery(document).ready(function($) {
            $('#mcs-add-holiday').click(function() {
                var idx = Date.now();
                var html = '<div class="holiday-row" style="background:#f9f9f9; padding:10px; margin-bottom:10px; border:1px solid #ddd;">Start: <input type="date" name="mcs_holidays_list['+idx+'][start]"> End: <input type="date" name="mcs_holidays_list['+idx+'][end]"> <label><input type="checkbox" name="mcs_holidays_list['+idx+'][use_default]" value="1"> Use Default</label><br><input type="text" name="mcs_holidays_list['+idx+'][msg]" class="widefat" placeholder="Custom Message"><button type="button" class="button remove-row" style="margin-top:5px;">Remove</button></div>';
                $('#mcs-holiday-wrapper').append(html);
            });
            $(document).on('click', '.remove-row', function() { $(this).parent().remove(); });
        });
    </script>
    <?php
}

// --- DELIVERY FIELDS ---

function mcs_enable_delivery_slots_render() {
    echo '<label><input type="checkbox" name="mcs_enable_delivery_slots" value="1" ' . checked(1, get_option('mcs_enable_delivery_slots'), false) . ' /> Enable Date & Time Slot selection at checkout.</label>';
}

function mcs_delivery_slots_list_render() {
    $slots = get_option('mcs_delivery_slots_list', array());
    ?>
    <p>Add your available time slots (e.g., "Morning (9am - 12pm)", "Afternoon").</p>
    <div id="mcs-slots-wrapper">
        <?php if (!empty($slots)) : foreach ($slots as $key => $slot) : ?>
            <div class="slot-row" style="margin-bottom:10px;">
                <input type="text" name="mcs_delivery_slots_list[<?php echo $key; ?>][label]" value="<?php echo esc_attr($slot['label']); ?>" class="regular-text" placeholder="Slot Label">
                <button type="button" class="button remove-row">Remove</button>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <button type="button" class="button" id="mcs-add-slot">Add New Time Slot</button>
    <script>
        jQuery(document).ready(function($) {
            $('#mcs-add-slot').click(function() {
                var idx = Date.now();
                var html = '<div class="slot-row" style="margin-bottom:10px;"><input type="text" name="mcs_delivery_slots_list['+idx+'][label]" class="regular-text" placeholder="Slot Label"> <button type="button" class="button remove-row">Remove</button></div>';
                $('#mcs-slots-wrapper').append(html);
            });
            // Re-use remove-row class
        });
    </script>
    <?php
}

function mcs_sanitize_array_callback($input) {
    // Basic sanitization to ensure we return an array
    if( is_array($input) ) return $input;
    return array();
}
