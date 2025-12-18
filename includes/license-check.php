<?php

// Function to check if the user has a valid license
function has_valid_license() {
    // Get the license key from the WordPress options
    $license_key = get_option('my_plugin_license_key');

    // If there is no license key, return false
    if (empty($license_key)) {
        return false;
    }

    // The URL of the license server
    $license_server_url = 'https://your-license-server.com/wp-json/license/v1/validate';

    // The data to send to the license server
    $data = [
        'license_key' => $license_key,
        'site_url' => get_site_url(),
    ];

    // Make a request to the license server
    $response = wp_remote_post($license_server_url, [
        'body' => $data,
    ]);

    // If the request failed, return false
    if (is_wp_error($response)) {
        return false;
    }

    // Get the body of the response
    $body = wp_remote_retrieve_body($response);

    // Decode the JSON response
    $data = json_decode($body, true);

    // If the license is valid, return true
    if ($data['valid']) {
        // Store the license status in the database
        update_option('my_plugin_license_status', 'valid');
        return true;
    }

    // If the license is not valid, return false
    update_option('my_plugin_license_status', 'invalid');
    return false;
}
