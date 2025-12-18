<?php
/**
 * Plugin Name: Aaron Falzon - Custom Theme Settings
 * Description: A custom settings panel for the theme with Front-end and Back-end configurations.
 * Version: 1.7
 * Author: Aaron Falzon
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include the plugin files.
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend-delivery.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend-logic.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/common-logic.php';
