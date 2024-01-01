<?php
/*
Plugin Name: Sendcloud Webhook Handler
*/

add_action('init', 'handle_sendcloud_webhook');
function handle_sendcloud_webhook() {
    // Get the raw POST data
    $postData = file_get_contents("php://input");

    // Check if there is any data
    if (!empty($postData)) {
        error_log("Webhook data saved successfully.");
        error_log($postData);

        // Decode JSON data
        $decodedData = json_decode($postData, true);

        // Check if "action" field exists in the decoded data
        if (isset($decodedData['action'])) {
            // Extract parcel details
            $parcel = !empty($decodedData['parcel']) ? $decodedData['parcel'] : [];

            // Check if required parcel data is present
            if (!empty($parcel['order_number']) && !empty($parcel['status']['id']) && !empty($parcel['status']['message'])) {
                // Extract parcel data
                $orderNumber = $parcel['order_number'];
                $statusId = $parcel['status']['id'];
                $statusMessage = $parcel['status']['message'];

                // Save data to the database
                save_to_database($decodedData['action'], $orderNumber, $statusId, $statusMessage);
            } else {
                // Log an error if required parcel data is missing
                error_log("Required parcel data is missing in the Sendcloud webhook.");
            }
        } else {
            // Log an error if "action" field is not present
            error_log("No 'action' field found in the Sendcloud webhook data.");
        }
    } else {
        // Log an error if no data is received
        error_log("No data received in the Sendcloud webhook.");
    }
}

function save_to_database($action, $orderNumber, $statusId, $statusMessage) {
    global $wpdb;

    // Replace 'your_table_name' with the actual table name you want to use
    $table_name = 'cu_sendcloud_webhook';

    // Sanitize and validate data before inserting into the database
    $action = sanitize_text_field($action);
    $orderNumber = sanitize_text_field($orderNumber);
    $statusId = absint($statusId); // Ensure that status ID is an integer
    $statusMessage = sanitize_text_field($statusMessage);

    // Map status messages to corresponding WordPress post statuses
    $statusMapping = [
        'Cancellation requested' => ['Pending Cancel', 1],
        'Ready to send' => ['wc-processing', 0],
        'En route to sorting center' => ['wc-shipped-done', 0],
        'Delivered' => ['wc-completed', 0],
    ];

    // Default values if status message is not found in the mapping
    $defaultStatus = [$statusMessage, 0];

    // Get status data and new status from the mapping or use defaults
    list($statusData, $newStatus) = $statusMapping[$statusMessage] ?? $defaultStatus;

    // Update post status in the WordPress posts table
    if (in_array($statusMessage, ['Cancellation requested', 'Ready to send', 'En route to sorting center', 'Delivered'])) {
        $wpdb->update(
            $wpdb->posts,
            ['post_status' => $statusData],
            ['ID' => $orderNumber],
            ['%s'],
            ['%d']
        );
    }

    // Update status in the custom Sendcloud parcels table
    $table_name_parcel = $wpdb->prefix . 'cu_sendcloud_parcels';
    $wpdb->update(
        $table_name_parcel,
        ['status' => $newStatus, 'status_message' => $statusData],
        ['ID' => $orderNumber],
        ['%s', '%s'],
        ['%d']
    );

    // Database insert/update successful
    error_log('Webhook data saved to the database successfully.');
}
