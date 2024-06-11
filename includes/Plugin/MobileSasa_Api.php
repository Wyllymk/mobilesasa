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
         * It then retrieves and sanitizes the sender ID and API token from the submitted form data,
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
            $default_options = get_option('mobilesasa_defaults');

            // Retrieve the sender ID from the form submission and sanitize it
            $sender_id = sanitize_text_field($_POST['mobilesasa_sender']);
            // Retrieve the API token from the form submission and sanitize it
            $api_token = sanitize_text_field($_POST['mobilesasa_token']);

            // Check if the sender ID is empty and display an error message if it is
            if (empty($sender_id)) {
                 // Set transient for empty message
                 set_transient('wc_mobilesasa_sender_id_empty', true, 30);
            } else {
                // Update the sender ID in the default options array
                $default_options['mobilesasa_sender'] = $sender_id;

                // Make an API request to retrieve sender details
                $response = wp_remote_post('https://api.mobilesasa.com/open/v1/senders/load-details', array(
                    'body' => json_encode(array('senderID' => $sender_id)),
                    'headers' => array(
                        'Content-Type' => 'application/json'
                    )
                ));

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
                    // Update the senderType in the default options array
                    $default_options['mobilesasa_sender_type'] = $sender_type;

                    // Save the updated options back to the database
                    update_option('mobilesasa_defaults', $default_options);
                } else {
                    // Set transient for empty message
                    set_transient('wc_mobilesasa_sender_error', true, 30);
                    // Redirect to the same page with an error parameter
                    wp_redirect(add_query_arg('error', 'sender_error', wp_get_referer()));
                    exit;
                }

            }

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



    }
}