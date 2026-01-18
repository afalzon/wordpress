<?php
/**
 * Plugin Name: Falz.com.au WP Theme
 * Description: A custom settings panel for the theme with Front-end and Back-end configurations.
 * Version: 2.00
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

// Clear PUC cache when the update branch setting is changed to ensure immediate refresh.
add_action('update_option_mcs_update_branch', function() use ($myUpdateChecker) {
    $myUpdateChecker->getUpdateState()->delete();
});

// Allow switching to a branch with a different version (including downgrades).
add_filter('site_transient_update_plugins', function($transient) use ($myUpdateChecker) {
    if ( ! is_object($transient) ) return $transient;

    $state = $myUpdateChecker->getUpdateState();
    $update = $state->getUpdate();

    if ( $update ) {
        $plugin_file = plugin_basename(__FILE__);
        $installed_version = $myUpdateChecker->getInstalledVersion();

        // If the remote version differs from installed, inject it to allow update/downgrade.
        if ( $installed_version && $update->version !== $installed_version ) {
            if ( ! isset($transient->response[$plugin_file]) ) {
                $wp_update = $update->toWpFormat();
                $wp_update->plugin = $plugin_file;
                $transient->response[$plugin_file] = $wp_update;
            }
        }
    }
    return $transient;
}, 11);

// Include the license check file.
require_once plugin_dir_path( __FILE__ ) . 'includes/license-check.php';

// Check for a valid license.
$has_valid_license = has_valid_license();
