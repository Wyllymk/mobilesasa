<?php
/**
 * The file that defines the Mobile Sasa Get Balance class
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

// If direct access, then exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_GetBalance')) {

    class MobileSasa_GetBalance {

        private static $api_token;

        /**
         * Register the necessary hooks for the WooCommerce Bulk SMS functionality.
         */
        public static function register(): void {
            add_action('admin_post_get_balance', [self::class, 'handle_get_balance']);
        }

        /**
         * Initialize the class with the API token.
         *
         * @param string $api_token The API token for the SMS service.
         */
        public static function init(string $api_token): void {
            self::$api_token = $api_token;
        }

        /**
         * Handle the get balance request.
         */
        public static function handle_get_balance(): void {

            if (!current_user_can('manage_options') || !check_admin_referer('get_balance_nonce')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'mobilesasa'));
            }

            // Retrieve and set the API token
            $default_options = get_option('mobilesasa_defaults');
            $api_token = $default_options['mobilesasa_token'];
            self::init($api_token);

            // Get the balance
            $status = self::get_balance();

            // Redirect to the same page with a status parameter
            wp_redirect(add_query_arg('status', $status ? 'success' : 'failed', wp_get_referer()));
            exit;
        }

        /**
         * Get the balance from the Mobile Sasa API.
         *
         * @return int A status code indicating the success (1) or failure (0) of the operation.
         */
        public static function get_balance(): int {

            $status = 0;
        
            $url = 'https://api.mobilesasa.com/v1/get-balance';
        
            // Assuming the API token is sent as a query parameter for a GET request
            $url = add_query_arg([
                'api_token' => self::$api_token
            ], $url);
        
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 400
            ]);
        
            $response = curl_exec($curl);
        
            if ($response === false) {
                // Handle curl error
                $error = curl_error($curl);
                error_log('cURL Error: ' . $error);
            } else {
                // error_log('cURL Response: ' . $response); // Log the response for debugging
            }
        
            curl_close($curl);
        
            if ($response) {
                $responseVals = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log('JSON Decode Error: ' . json_last_error_msg());
                } else {
                    if (isset($responseVals['responseCode']) && $responseVals['responseCode'] === '0200') {
                        $status = 1;
                        // Save balance to the database
                        $balance = $responseVals['balance']; // Assuming 'balance' is the key for balance value in the response
                        Mobilesasa_Database::save_balance(floatval($balance));

                        // Add a success message
                        set_transient('mobilesasa_balance_response', true, 30);
                    } else {
                        error_log('API Response Error: ' . print_r($responseVals, true));
                    }
                }
            }
        
            return $status;
        }
        
        
    }
}