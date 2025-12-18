<?php
/**
 * Plugin Name: Aaron Falzon - Custom Theme Settings
 * Description: A custom settings panel for the theme with Front-end and Back-end configurations.
 * Version: 1.9.a
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

require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-update-checker-5.6/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/afalzon/wordpress/',
	__FILE__,
	'custom-settings'
);

//Set the branch that contains the stable release.
$update_branch = get_option('mcs_update_branch', 'main'); // Default to 'main'
$myUpdateChecker->setBranch($update_branch);
