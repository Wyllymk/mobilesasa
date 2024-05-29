<?php
/**
 * The file that defines the WooCommerce Bulk SMS class
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

if (!class_exists('MobileSasa_BulkSMS')) {

    class MobileSasa_BulkSMS {
        /**
         * Register the necessary hooks for the WooCommerce Bulk SMS functionality.
         */
        public static function register(): void {
            add_action('admin_post_send_bulk_sms', [self::class, 'handle_send_bulk_sms']);
            add_action('wp_ajax_delete_scheduled_message', [self::class, 'delete_scheduled_message']);
            add_action('wp_ajax_delivery_status_sms', [self::class, 'delivery_status_sms_handler']);
            add_action('mobilesasa_send_scheduled_sms', [self::class, 'send_scheduled_sms']);
            add_action('woocommerce_after_order_notes', [self::class, 'sms_opt_in_checkout']);
            add_action('woocommerce_checkout_update_order_meta', [self::class, 'save_sms_opt_in']);
        }

        /**
         * Handle the submission of the bulk SMS form.
         *
         * This function checks if the user has the required permissions, and if the bulk SMS is enabled.
         * If enabled, it sends the bulk SMS to the provided phone numbers using the MobileSasa_SendSMS class.
         */
        public static function handle_send_bulk_sms(): void {
            if (!current_user_can('manage_options') || !check_admin_referer('send_bulk_sms_nonce')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'mobilesasa'));
            }
        
            $options = get_option('mobilesasa_bulk_options');
            $message = $options['bulk_message'] ?? '';
            $is_enabled = $options['bulk_sms_enable'] ?? '0';
        
            error_log(print_r($_POST, true));
        
            $schedule_sms = isset($_POST['schedule_sms']) ? sanitize_text_field($_POST['schedule_sms']) : '';
            $schedule_date = isset($_POST['schedule_date']) ? sanitize_text_field($_POST['schedule_date']) : null;
        
            // Check if scheduling SMS
            if ($schedule_sms == 'on' && $schedule_date) {
                // Save scheduled message to the database
                global $wpdb;
                $table_name = $wpdb->prefix . 'mobilesasa_scheduled_messages';
                
                // Sanitize recipients
                $recipients = isset($_POST['send_sms']) ? array_map('sanitize_text_field', $_POST['send_sms']) : [];
                
                $wpdb->insert(
                    $table_name,
                    array(
                        'message' => $message,
                        'recipients' => json_encode($recipients),
                        'schedule_time' => $schedule_date,
                        'status' => 'pending'
                    ),
                    array('%s', '%s', '%s', '%s')
                );
        
                // Schedule WP-Cron event
                $timestamp = strtotime($schedule_date);
                if (!wp_next_scheduled('mobilesasa_send_scheduled_sms', array($wpdb->insert_id))) {
                    wp_schedule_single_event($timestamp, 'mobilesasa_send_scheduled_sms', array($wpdb->insert_id));
                }
        
                // Add a success message
                set_transient('wcbulksms_message_scheduled', true, 30);
        
                // Redirect to the same page with a success parameter
                wp_redirect(add_query_arg('scheduled', 'true', wp_get_referer()));
                exit;
            } else {
                if ($is_enabled == '1' && !empty($message)) {
                    // Get the default sender ID and API token for the SMS service
                    $default_options = get_option('mobilesasa_defaults');
                    $senderid = $default_options['mobilesasa_sender'];
                    $apitoken = $default_options['mobilesasa_token'];
        
                    // Initialize the SMS sending class with the sender ID and API token
                    MobileSasa_SendSMS::init($senderid, $apitoken);
        
                    if (!empty($_POST['send_sms'])) {
                        
                        error_log(print_r($_POST, true));
                        
                        $phones = array_map('sanitize_text_field', $_POST['send_sms']);
                        $cleaned_phones = MobileSasa_SendSMS::clean_phone($phones);
        
                        $message_id = MobileSasa_SendSMS::send_sms($cleaned_phones, $message);
                        
                        if ($message_id) {
                            self::save_message($message, $cleaned_phones, 'Sent', 0, $message_id);
                        }
        
                        // Add a success message
                        set_transient('wcbulksms_message_sent', true, 30);
                    }
        
                    // Redirect to the same page with a success parameter
                    wp_redirect(add_query_arg('sent', 'true', wp_get_referer()));
                    exit;
                }
            }
        }
        
        
        public static function send_scheduled_sms($scheduled_message_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mobilesasa_scheduled_messages';
            $scheduled_message = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $scheduled_message_id), ARRAY_A);
        
            if ($scheduled_message) {
                $message = $scheduled_message['message'];
                $recipients = json_decode($scheduled_message['recipients'], true);
        
                $default_options = get_option('mobilesasa_defaults');
                $senderid = $default_options['mobilesasa_sender'];
                $apitoken = $default_options['mobilesasa_token'];
        
                // Initialize the SMS sending class with the sender ID and API token
                MobileSasa_SendSMS::init($senderid, $apitoken);
        
                $cleaned_phones = MobileSasa_SendSMS::clean_phone($recipients);
                $message_id = MobileSasa_SendSMS::send_sms($cleaned_phones, $message);
        
                if ($message_id) {
                    self::save_message($message, $cleaned_phones, 'Sent', 0, $message_id);
                    // Update the status of the scheduled message
                    $wpdb->update(
                        $table_name,
                        array('status' => 'sent'),
                        array('id' => $scheduled_message_id),
                        array('%s'),
                        array('%d')
                    );
                }
            }
        }
        

        /**
         * Handle the deleting scheduled message.
         *
         * This function checks if the user has the required permissions, and if the bulk SMS is enabled.
         * If enabled, it sends the bulk SMS to the provided phone numbers using the MobileSasa_SendSMS class.
         */
        public static function delete_scheduled_message() {
            // Verify nonce
            if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'delete_message_nonce')) {
                wp_send_json_error(array('message' => 'Nonce verification failed.'));
            }
            
            // Sanitize and validate the message ID
            $message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
        
            if ($message_id <= 0) {
                wp_send_json_error(array('message' => 'Invalid message ID.'));
            }
        
            // Delete scheduled message from the database
            global $wpdb;
            $table_name = $wpdb->prefix . 'mobilesasa_scheduled_messages';
        
            $deleted = $wpdb->delete(
                $table_name,
                array('id' => $message_id),
                array('%d')
            );
        
            if ($deleted === false) {
                wp_send_json_error(array('message' => 'Failed to delete the message.'));
            } else {
                wp_send_json_success(array('message' => 'Message deleted successfully.'));
            }
        }
        
        
        

        /**
         * Save the message details to the database.
         *
         * @param string $message_body The message content.
         * @param array $recipients The list of recipients.
         * @param string $status The status of the message.
         * @param int $delivered_count The number of delivered messages.
         * @param string $message_id The message ID from the SMS API.
         */
        public static function save_message(string $message_body, array $recipients, string $status, int $delivered_count, string $message_id): void {
            global $wpdb;
            $table_name = $wpdb->prefix . 'mobilesasa_messages';

            $wpdb->insert(
                $table_name,
                [
                    'sent_at' => current_time('mysql'),
                    'message_body' => $message_body,
                    'recipients' => json_encode($recipients),
                    'status' => $status,
                    'delivered_count' => $delivered_count,
                    'message_id' => $message_id,
                ]
            );
        }

        /**
         * Check the delivery status of messages.
         */
        /**
         * Check the delivery status of messages.
         */
        public static function delivery_status_sms_handler(): void {
            if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'delivery_status_sms_nonce')) {
                wp_send_json_error(array('message' => 'Nonce verification failed.'));
            }
            
            $message_id = isset($_POST['message_id']) ? sanitize_text_field($_POST['message_id']) : '';
            if (empty($message_id)) {
                wp_send_json_error(['message' => 'Invalid message ID']);
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'mobilesasa_messages';
            $default_options = get_option('mobilesasa_defaults');
            $api_token = $default_options['mobilesasa_token'];
            $url = 'https://api.mobilesasa.com/v1/dlr';

            $response = self::get_delivery_status($message_id, $api_token, $url);

            if ($response && isset($response['status']) && $response['status'] == true) {
                $delivered_count = 0;
                foreach ($response['messages'] as $msg) {
                    if ($msg['deliveryStatus']['status'] == 'Delivered') {
                        $delivered_count++;
                    }
                }

                $status = ($delivered_count == count($response['messages'])) ? 'Delivered' : 'Partially Delivered';
                $wpdb->update(
                    $table_name,
                    [
                        'status' => $status,
                        'delivered_count' => $delivered_count,
                    ],
                    ['message_id' => $message_id]
                );

                wp_send_json_success(['message' => 'Delivery status updated successfully.']);
            } else {
                wp_send_json_error(['message' => 'Failed to check delivery status.']);
            }
        }

         /**
         * Get the delivery status from the MobileSasa API.
         *
         * @param string $message_id
         * @param string $api_token
         * @param string $url
         * @return array|false
         */
        private static function get_delivery_status(string $message_id, string $api_token, string $url) {
            $args = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['messageId' => $message_id]),
                'timeout' => 45,
            ];

            $response = wp_remote_post($url, $args);
            if (is_wp_error($response)) {
                return false;
            }

            $body = wp_remote_retrieve_body($response);
            return json_decode($body, true);
        }

        /**
         * Add an SMS opt-in checkbox to the checkout page.
         *
         * @param \WC_Checkout $checkout The WooCommerce checkout object.
         */
        public static function sms_opt_in_checkout(\WC_Checkout $checkout): void {
            woocommerce_form_field('sms_opt_in', [
                'type' => 'checkbox',
                'class' => ['form-row-wide'],
                'label' => __('Yes, I would like to receive updates via SMS.', 'mobilesasa')
            ], $checkout->get_value('sms_opt_in'));
        }

        /**
         * Save the SMS opt-in checkbox value to the order meta.
         *
         * @param int $order_id The ID of the WooCommerce order.
         */
        public static function save_sms_opt_in(int $order_id): void {
            if (isset($_POST['sms_opt_in'])) {
                update_post_meta($order_id, '_sms_opt_in', sanitize_text_field($_POST['sms_opt_in']));
            }
        }
    }
}