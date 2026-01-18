<?php

/* ------------------------------------------------ */
/* FRONTEND: DELIVERY & PICKUP SLOTS                */
/* ------------------------------------------------ */

// 1. Add Fields to Checkout
add_action( 'woocommerce_before_order_notes', 'mcs_add_delivery_checkout_fields' );
function mcs_add_delivery_checkout_fields( $checkout ) {
    global $has_valid_license;
    if ( !$has_valid_license || !get_option('mcs_enable_delivery_slots') ) return;

    echo '<div id="mcs_delivery_checkout_field"><h3>Delivery / Pickup Details</h3>';

    // A. Date Field
    woocommerce_form_field( 'mcs_delivery_date', array(
        'type'        => 'date',
        'class'       => array('form-row-wide'),
        'label'       => 'Preferred Date',
        'required'    => true,
        'placeholder' => 'Select Date',
    ), $checkout->get_value( 'mcs_delivery_date' ) );

    // B. Time Slot Field (Dropdown)
    $slots_data = get_option('mcs_delivery_slots_list', array());
    $options = array( '' => 'Choose a time slot...' );
    
    if ( !empty($slots_data) ) {
        foreach ( $slots_data as $slot ) {
            // Check if label exists
            if ( !empty($slot['label']) ) {
                $options[ $slot['label'] ] = $slot['label'];
            }
        }
    }

    woocommerce_form_field( 'mcs_delivery_time', array(
        'type'        => 'select',
        'class'       => array('form-row-wide'),
        'label'       => 'Preferred Time Slot',
        'required'    => true,
        'options'     => $options,
    ), $checkout->get_value( 'mcs_delivery_time' ) );

    echo '</div>';
}

// 2. Validate Fields
add_action( 'woocommerce_checkout_process', 'mcs_validate_delivery_checkout_fields' );
function mcs_validate_delivery_checkout_fields() {
    global $has_valid_license;
    if ( !$has_valid_license || !get_option('mcs_enable_delivery_slots') ) return;

    if ( empty( $_POST['mcs_delivery_date'] ) ) {
        wc_add_notice( 'Please select a preferred <strong>Date</strong> for delivery/pickup.', 'error' );
    }
    if ( empty( $_POST['mcs_delivery_time'] ) ) {
        wc_add_notice( 'Please select a preferred <strong>Time Slot</strong>.', 'error' );
    }
}

// 3. Save to Order Meta
add_action( 'woocommerce_checkout_update_order_meta', 'mcs_save_delivery_checkout_fields' );
function mcs_save_delivery_checkout_fields( $order_id ) {
    global $has_valid_license;
    if ( !$has_valid_license || !get_option('mcs_enable_delivery_slots') ) return;

    if ( ! empty( $_POST['mcs_delivery_date'] ) ) {
        update_post_meta( $order_id, '_mcs_delivery_date', sanitize_text_field( $_POST['mcs_delivery_date'] ) );
    }
    if ( ! empty( $_POST['mcs_delivery_time'] ) ) {
        update_post_meta( $order_id, '_mcs_delivery_time', sanitize_text_field( $_POST['mcs_delivery_time'] ) );
    }
}

// 4. Display in Admin Order View
add_action( 'woocommerce_admin_order_data_after_billing_address', 'mcs_show_delivery_info_admin' );
function mcs_show_delivery_info_admin( $order ) {
    global $has_valid_license;
    if ( !$has_valid_license ) return;
    
    $date = $order->get_meta( '_mcs_delivery_date' );
    $time = $order->get_meta( '_mcs_delivery_time' );

    if ( $date || $time ) {
        echo '<h3>Preferred Schedule</h3>';
        if($date) echo '<p><strong>Date:</strong> ' . esc_html($date) . '</p>';
        if($time) echo '<p><strong>Time:</strong> ' . esc_html($time) . '</p>';
    }
}

// 5. Display in Emails
add_action( 'woocommerce_email_after_order_table', 'mcs_show_delivery_info_email', 20, 4 );
function mcs_show_delivery_info_email( $order, $sent_to_admin, $plain_text, $email ) {
    global $has_valid_license;
    if ( !$has_valid_license ) return;

    $date = $order->get_meta( '_mcs_delivery_date' );
    $time = $order->get_meta( '_mcs_delivery_time' );

    if ( $date || $time ) {
        echo '<h3>Delivery / Pickup Preference</h3>';
        if($date) echo '<p><strong>Date:</strong> ' . esc_html($date) . '</p>';
        if($time) echo '<p><strong>Time:</strong> ' . esc_html($time) . '</p>';
    }
}

// 6. Add Custom Column to Order List (The Table View)
add_filter( 'manage_edit-shop_order_columns', 'mcs_add_order_column_header' );
function mcs_add_order_column_header( $columns ) {
    global $has_valid_license;
    if ( !$has_valid_license ) return $columns;
    // Add new column at the end
    $columns['mcs_timeslot'] = 'Preferred Time';
    return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'mcs_add_order_column_content' );
function mcs_add_order_column_content( $column ) {
    global $post;
    if ( 'mcs_timeslot' === $column ) {
        $order = wc_get_order( $post->ID );
        $date = $order->get_meta( '_mcs_delivery_date' );
        $time = $order->get_meta( '_mcs_delivery_time' );
        
        if ($date) echo '<strong>' . esc_html($date) . '</strong><br>';
        if ($time) echo esc_html($time);
    }
}
