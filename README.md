# Sendcloud Webhook Handler

## Overview

This is a WordPress plugin designed to handle Sendcloud webhooks. Sendcloud is a shipping and logistics platform, and this plugin facilitates the integration of Sendcloud webhook data with a WordPress website.

## Installation

1. **Download the Plugin:**
   - Download the `sendcloud-webhook-handler` directory and its contents.
   - Place the entire directory in the WordPress plugins directory (`wp-content/plugins/`).

2. **Activate the Plugin:**
   - Log in to your WordPress admin dashboard.
   - Navigate to the "Plugins" section.
   - Locate "Sendcloud Webhook Handler" and click "Activate."

## Usage

Once the plugin is activated, it automatically hooks into the WordPress initialization process (`init`). It listens for incoming Sendcloud webhook data and processes it accordingly.

### Webhook Handling Process

1. **Receiving Webhook Data:**
   - The plugin listens for incoming webhook data on the `php://input` stream.

2. **Processing Webhook Data:**
   - If data is received, it is logged and then decoded from JSON format.
   - The plugin checks for the existence of the "action" field in the decoded data.

3. **Validating and Saving Data to the Database:**
   - If the "action" field exists, the plugin extracts parcel details and validates the required data.
   - If the required parcel data is present, it is sanitized and validated before being saved to the WordPress database.

4. **Updating Post and Parcel Status:**
   - The plugin maps Sendcloud status messages to corresponding WordPress post statuses.
   - It updates the post status in the WordPress posts table and the custom Sendcloud parcels table.

### Database Tables

- The plugin uses two database tables:
  1. The WordPress posts table to update the post status.
  2. A custom table (`cu_sendcloud_parcels`) to store Sendcloud parcel information.

## Configuration

- The plugin does not require specific configuration. However, ensure that the Sendcloud webhook is configured to send data to the correct endpoint on your WordPress site.

## Notes

- The plugin assumes a specific structure in the Sendcloud webhook payload, including the presence of the "action" field and required parcel data.

## Customization

- If needed, you can customize the plugin by modifying the code to suit your specific requirements.

## Troubleshooting

- In case of issues, check the error logs for relevant information. The error logs capture details about webhook data processing and database operations.

## License

This plugin is released under the [GPL-2.0](https://www.gnu.org/licenses/gpl-2.0.html) license. Feel free to modify and distribute it according to your needs.

---

**Note:** Make sure to replace `'cu_sendcloud_webhook'` and `'cu_sendcloud_parcels'` with your actual table names as needed.