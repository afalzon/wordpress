<?php

/* ------------------------------------------------ */
/* OTHER LOGIC (Holidays)          */
/* ------------------------------------------------ */

// (Existing Holiday Frontend Logic)
function mcs_get_active_holiday() {
    $holidays = get_option('mcs_holidays_list', array());
    if (empty($holidays)) return false;
    $today = current_time('Y-m-d');
    foreach ($holidays as $holiday) {
        if ($today >= $holiday['start'] && $today <= $holiday['end']) return $holiday;
    }
    return false;
}
add_filter('woocommerce_is_purchasable', 'mcs_disable_purchasing_holidays');
function mcs_disable_purchasing_holidays($purchasable) {
    global $has_valid_license;
    if (!$has_valid_license) return $purchasable;
    if (mcs_get_active_holiday()) return false;
    return $purchasable;
}
add_action('wp_footer', 'mcs_show_holiday_banner');
function mcs_show_holiday_banner() {
    global $has_valid_license;
    if (!$has_valid_license) return;
    $active = mcs_get_active_holiday();
    if (!$active) return;
    $message = (isset($active['use_default']) && $active['use_default'] == 1) ? get_option('mcs_default_holiday_msg') : (isset($active['msg']) ? $active['msg'] : '');
    if (empty($message)) $message = "Our store is currently closed.";
    echo '<div style="position:fixed; bottom:0; left:0; width:100%; background-color:#ffcc00; color:black; text-align:center; padding:15px; z-index:99999; font-weight:bold;">' . wp_kses_post($message) . '</div>';
    echo '<style>body { padding-bottom: 50px !important; }</style>';
}
