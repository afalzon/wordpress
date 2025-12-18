<?php

/* ------------------------------------------------ */
/* OTHER LOGIC (Bank, Min Order)          */
/* ------------------------------------------------ */

// (Existing Bank Logic)
add_filter( 'woocommerce_order_actions', 'mcs_add_bank_details_order_action' );
function mcs_add_bank_details_order_action( $actions ) {
    if ( get_option('mcs_enable_bank_action') ) $actions['mcs_send_bank_details'] = 'Send Bank Details (Set to Pending)';
    return $actions;
}
add_action( 'woocommerce_order_action_mcs_send_bank_details', 'mcs_process_bank_details_order_action' );
function mcs_process_bank_details_order_action( $order ) {
    $note = sprintf("Here are our bank details for payment:\n\nAccount Name: %s\nBSB: %s\nAccount Number: %s", get_option('mcs_bank_acc_name'), get_option('mcs_bank_bsb'), get_option('mcs_bank_acc_num'));
    $order->add_order_note( $note, 1 ); 
    $order->update_meta_data( '_mcs_bank_details_sent_timestamp', current_time('mysql') );
    $order->update_status( 'pending', 'Status changed automatically after sending Bank Details.' );
    $order->save();
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'mcs_display_bank_details_sent_flag' );
function mcs_display_bank_details_sent_flag( $order ) {
    $sent_time = $order->get_meta( '_mcs_bank_details_sent_timestamp' );
    if ( $sent_time ) echo '<p class="form-field form-field-wide"><span style="color:#46b450;font-weight:600;"><span class="dashicons dashicons-yes"></span> Bank Details Sent</span><br><span class="description">Sent on: ' . esc_html(date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($sent_time))) . '</span></p>';
}

// (Existing Min Order Logic)
add_action('woocommerce_checkout_process', 'mcs_minimum_order_amount');
add_action('woocommerce_check_cart_items', 'mcs_minimum_order_amount');
function mcs_minimum_order_amount() {
    if ( ! get_option('mcs_enable_min_order') ) return;
    $minimum = (float) get_option('mcs_min_order_amount'); 
    if ( empty($minimum) || $minimum == 0 ) return;
    if ( get_option('mcs_min_order_ignore_virtual') ) {
        $all_virtual = true;
        foreach ( WC()->cart->get_cart() as $cart_item ) { if ( ! $cart_item['data']->is_virtual() ) { $all_virtual = false; break; } }
        if ( $all_virtual ) return;
    }
    if ( WC()->cart->has_discount() ) return; 
    $current_total = (float) WC()->cart->get_total( 'edit' );
    if ( $current_total < $minimum ) {
        $message = sprintf('Your current order total is %s — you must have an order with a minimum of %s to place your order.', wc_price( $current_total ), wc_price( $minimum ));
        if ( is_cart() ) wc_print_notice( $message, 'error' );
        else wc_add_notice( $message, 'error' );
    }
}

