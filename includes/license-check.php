<?php

// Function to check if the user has a valid license (DEMO VERSION)
function has_valid_license() {
    // Get the license key from the WordPress options
    $license_key = get_option('my_plugin_license_key');

    // If there is no license key, return false
    if (empty($license_key)) {
        update_option('my_plugin_license_status', 'invalid');
        return false;
    }

    // --- DEMO LOGIC ---
    // In a real plugin, you would make a request to your license server here.
    // For this example, we'll just check if the key is 'valid-key'.
    if ($license_key === 'valid-key') {
        update_option('my_plugin_license_status', 'valid');
        return true;
    }
    // --- END DEMO LOGIC ---

    // If the license is not valid, return false
    update_option('my_plugin_license_status', 'invalid');
    return false;
}
