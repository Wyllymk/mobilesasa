<?php
/**
 * The file that defines the MobileSasa Api Test class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Plugin
 *
 * Author: Wilson Devops <wilsonkabatha@gmail.com>
 */
namespace Wylly\MobileSasa\Plugin;

// if direct access than exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_Api')) {

    class MobileSasa_Api {
        /**
         * Register the necessary hooks for the MobileSasa Api Test functionality.
         */
        public static function register(): void {
            add_action('admin_post_save_credentials', [self::class, 'handle_save_credentials']);
            
        }

        /**
         * Handle the submission of the credentials form.
         *
         * This function checks if the user has the required permissions and verifies the nonce for security.
         * It then retrieves and sanitizes the sender IDs and API token from the submitted form data,
         * updates the options in the database, calls an external API to retrieve sender details,
         * and updates the sender type in the options.
         */
        public static function handle_save_credentials(): void {
            // Check if the current user has permission to manage options and verify the nonce for security
            if (!current_user_can('manage_options') || !check_admin_referer('save_credentials_nonce')) {
                // If the user lacks permission or the nonce is invalid, stop execution and display an error message
                wp_die(__('You do not have sufficient permissions to access this page.', 'mobilesasa'));
            }

            // Get the current default options for the SMS service
            $default_options = get_option('mobilesasa_defaults', array());

            // Retrieve the bulk sender ID from the form submission and sanitize it
            $bulk_sender_id = sanitize_text_field($_POST['bulk_sender_id']);
            // Retrieve the transactional sender ID from the form submission and sanitize it
            $transactional_sender_id = sanitize_text_field($_POST['transactional_sender_id']);
            // Retrieve the API token from the form submission and sanitize it
            $api_token = sanitize_text_field($_POST['mobilesasa_token']);

            // Handle the bulk sender ID
            self::update_sender_id($bulk_sender_id, 'bulk_sender_id', $default_options, 'Promotional');

            // Handle the transactional sender ID
            self::update_sender_id($transactional_sender_id, 'transactional_sender_id', $default_options, 'Transactional');

            // Check if the API token is empty and display an error message if it is
            if (empty($api_token)) {
                // Set transient for empty message
                set_transient('wc_mobilesasa_token_empty', true, 30);
            } else {
                // Update the API token in the default options array
                $default_options['mobilesasa_token'] = $api_token;
            }

            // Save the updated options back to the database
            update_option('mobilesasa_defaults', $default_options);

            // Add a success message
            set_transient('wc_credentials_saved', true, 30);

            // Redirect to the same page with a success parameter
            wp_redirect(add_query_arg('saved', 'true', wp_get_referer()));
            exit;
        }

        /**
         * Update sender ID and handle API request to retrieve sender details.
         *
         * @param string $sender_id The sender ID to be processed.
         * @param string $option_key The key for the sender ID and sender type in the options array.
         * @param array $default_options The array of default options.
         * @param string $expected_type The expected sender type ('promotional' or 'transactional').
         */
        private static function update_sender_id($sender_id, $option_key, &$default_options, $expected_type) {
            if (empty($sender_id)) {
                // Set transient for empty message
                set_transient("wc_mobilesasa_{$option_key}_empty", true, 30);
            } else {
                // Make an API request to retrieve sender details
                $response = self::fetch_sender_details($sender_id);

                // Check for errors in the API response
                if (is_wp_error($response)) {
                    wp_die(__('Failed to fetch sender details. Please try again later.', 'mobilesasa'));
                }

                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                // Check if the response status is true and response code is 0200
                if ($data['status'] === true && $data['responseCode'] === '0200') {
                    // Extract the senderType from the payload
                    $sender_type = sanitize_text_field($data['payload']['senderType']);

                    // Check if the sender type matches the expected type
                    if ($sender_type !== $expected_type) {
                        // Set transient for incorrect sender type
                        set_transient("wc_mobilesasa_{$option_key}_incorrect_type", true, 30);
                        // Redirect to the same page with an error parameter
                        wp_redirect(add_query_arg('error', "{$option_key}_incorrect_type", wp_get_referer()));
                        exit;
                    }

                    // Update the sender ID and sender type in the default options array
                    $default_options[$option_key] = $sender_id;
                    $default_options["mobilesasa_{$option_key}_type"] = $sender_type;

                    // Save the updated options back to the database
                    update_option('mobilesasa_defaults', $default_options);
                } else {
                    // Set transient for API error
                    set_transient("wc_mobilesasa_{$option_key}_error", true, 30);
                    // Redirect to the same page with an error parameter
                    wp_redirect(add_query_arg('error', "{$option_key}_error", wp_get_referer()));
                    exit;
                }
            }
        }

        /**
         * Fetch sender details from the API.
         *
         * @param string $sender_id The sender ID to be checked.
         * @return array|WP_Error The response from the API or WP_Error on failure.
         */
        private static function fetch_sender_details($sender_id) {
            return wp_remote_post('https://api.mobilesasa.com/open/v1/senders/load-details', array(
                'body' => json_encode(array('senderID' => $sender_id)),
                'headers' => array(
                    'Content-Type' => 'application/json'
                )
            ));
        }





    }
}