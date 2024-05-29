<?php
/**
 * The file that defines the functionality plugin class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Plugin
 *
 * @autor Wilson Devops <wilsonkabatha@gmail.com>
 */

namespace Wylly\MobileSasa\Plugin;

// If direct access, then exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_TransactionalSMS')) {
    /**
     * MobileSasa_TransactionalSMS class
     */
    class MobileSasa_TransactionalSMS {
        
        /**
         * Options Array
         *
         * @var array
         */
        private static $options = [];

        /**
         * Registers the necessary hooks and initializes the class.
         */
        public static function register(): void {

            self::load_options();
            add_action('woocommerce_order_status_changed', [self::class, 'order_status'], 10, 3);
            // Hook into order status changes
			add_action( 'woocommerce_store_api_checkout_update_order_meta', [self::class, 'track_order_draft_duration'] );
            // Hook into the cron event to send the SMS
			add_action( 'send_draft_order_sms', [self::class,'send_draft_order_sms_callback'], 10, 1 );
			// Hook the function to run when WordPress initializes
			add_action('init', [self::class, 'schedule_delete_custom_postMeta']);
			// Hook the function to the scheduled event
			add_action('delete_custom_post_meta_event', [self::class, 'delete_custom_postMeta']);
        }

        /**
         * Load options from the database
         */
        private static function load_options(): void {

            // Retrieve the entire serialized array from the database
            $serialized_options = get_option('mobilesasa_transactional_options');

            // Unserialize the options array
            $options_array = maybe_unserialize($serialized_options);

            // If the options array is valid, store it in the static property
            if (is_array($options_array)) {
                self::$options = $options_array;
            } else {
                // Handle the case where the options array is not valid
                self::$options = [];
            }
        }

        /**
         * Handles the order status change and sends SMS notifications.
         *
         * @param int $order_id The ID of the order.
         * @param string $old_status The previous order status.
         * @param string $new_status The new order status.
         */
        public static function order_status(int $order_id, string $old_status, string $new_status): void {

            $transactional_sms_enable = self::$options['transactional_sms_enable'] ?? '';
            if ($transactional_sms_enable && $transactional_sms_enable === '1') {
                // Get the default sender ID and API token for the SMS service
                $default_options = get_option('mobilesasa_defaults');
                $sender_id = $default_options['mobilesasa_sender'] ?? '';
                $api_token = $default_options['mobilesasa_token'] ?? '';

                // Initialize the SMS sending class with the sender ID and API token
                MobileSasa_SendSMS::init($sender_id, $api_token);

                // Order details
                $order = wc_get_order($order_id);
                $phone = $order->get_billing_phone();
                $name = $order->get_billing_first_name();
                $total = $order->get_total();


                // List of statuses to check
                $statuses = [
                    'pending' => 'pending_sms',
                    'on-hold' => 'onhold_sms',
                    'processing' => 'processing_sms',
                    'completed' => 'completed_sms',
                    'cancelled' => 'cancelled_sms',
                    'refunded' => 'refunded_sms',
                    'failed' => 'failed_sms',
                    'ready-for-pickup' => 'ready_for_pickup_sms',
                    'failed-delivery' => 'failed_delivery_sms',
                    'returned' => 'returned_sms',
                ];

                // Check each status and send SMS if enabled
                foreach ($statuses as $status => $option_prefix) {
                    if ($new_status === $status && self::$options["{$option_prefix}_enable"] === '1' && !empty(self::$options["{$option_prefix}_message"])) {
                        $message = str_replace(
                            ['{name}', '{orderid}', '{total}', '{phone}'],
                            [$name, $order_id, $total, $phone],
                            self::$options["{$option_prefix}_message"]
                        );
                        MobileSasa_SendSMS::wc_send_sms(MobileSasa_SendSMS::wc_clean_phone($phone), $message);
                    }
                }

                $$admin_statuses = ['pending', 'on-hold', 'processing'];
                $admin_phone_number = self::$options['transactional_admin_number'] ?? '';
                $has_admin_logged = get_post_meta($order->get_id(), '_admin_sms_sent', true);
                
                if (!$has_admin_logged) {
                    // Check each status and send SMS if enabled
                    foreach ($admin_statuses as $status) {
                        if ($new_status === $status && self::$options["admin_sms_enable"] === '1' && !empty(self::$options["admin_sms_message"])) {
                            $message = str_replace(
                                ['{name}', '{order_id}', '{total}', '{phone}'],
                                [$name, $order_id, $total, $phone],
                                self::$options["admin_sms_message"]
                            );
                            MobileSasa_SendSMS::wc_send_sms(MobileSasa_SendSMS::wc_clean_phone($admin_phone_number), $message);
                        }
                    }
                }
                
            } else {
                // error_log("Transactional SMS is not enabled.");
            }
        }

        /**
         * Schedules a WP-Cron event to track the duration of the order draft status.
         *
         * @param \WC_Order $order The WooCommerce order object.
         */
        public static function track_order_draft_duration(\WC_Order  $order ): void {
			// Check for flag already set or not.
			$has_draft_logged = get_post_meta( $order->get_id(), '_draft_duration_logged', true );
			
			if ( $order->has_status( 'checkout-draft' ) && ! $has_draft_logged ) {
				// Schedule a cron job to send the SMS after 10 minutes
				$timestamp = time() + 300; //300 seconds
				wp_schedule_single_event( $timestamp  , 'send_draft_order_sms', array( $order->get_id() ) );
		
				// Set flag to prevent duplicate logging
				update_post_meta( $order->get_id(), '_draft_duration_logged', true );
			}
		}
        
        /**
         * Sends SMS notification for draft orders after a specified duration.
         *
         * @param int $order_id The ID of the order.
         */
        public static function send_draft_order_sms_callback(int $order_id): void {

			$has_sms_logged = get_post_meta( $order_id, '_sms_sent_logged', true );
			
			$order = wc_get_order( $order_id );
			
			if($order->has_status( 'checkout-draft' ) && ! $has_sms_logged ){

                $transactional_sms_enable = self::$options['transactional_sms_enable'] ?? '';
                
                if ($transactional_sms_enable && $transactional_sms_enable === '1') {
                    // Get the default sender ID and API token for the SMS service
                    $default_options = get_option('mobilesasa_defaults');
                    $sender_id = $default_options['mobilesasa_sender'] ?? '';
                    $api_token = $default_options['mobilesasa_token'] ?? '';

                    // Initialize the SMS sending class with the sender ID and API token
                    MobileSasa_SendSMS::init($sender_id, $api_token);

                    // Order details
                    $order = wc_get_order($order_id);
                    $phone = $order->get_billing_phone();
                    $name = $order->get_billing_first_name();
                    $total = $order->get_total();

                    if (self::$options["draft_sms_enable"] === '1' && !empty(self::$options["draft_sms_message"])) {
                        $message = str_replace(
                            ['{name}', '{order_id}', '{total}', '{phone}'],
                            [$name, $order_id, $total, $phone],
                            self::$options["draft_sms_message"]
                        );
                        MobileSasa_SendSMS::wc_send_sms(MobileSasa_SendSMS::wc_clean_phone($phone), $message);
                    }
                    // Set flag to prevent duplicate logging
					update_post_meta( $order->get_id(), '_sms_sent_logged', true );
					// Delete the meta entry
					delete_post_meta( $order->get_id(), '_draft_duration_logged' );

                } else {
                    // error_log("Transactional SMS is not enabled.");
                }
			
			}

		}

        /**
         * Schedules a periodic event to delete custom post meta.
         */
        public static function schedule_delete_custom_postMeta(): void {
			// Check if the scheduled event already exists
			if (!wp_next_scheduled('delete_custom_post_meta_event')) {
				// Schedule the event to run once every 2 hours
				wp_schedule_event(time(), 'daily', 'delete_custom_post_meta_event');
			}
		}
        
        /**
         * Deletes specific custom post meta data created by the plugin.
         */
        private static function delete_custom_postMeta(): void {
			// Define the post meta keys created by your plugin
			$meta_keys = array(
				'_admin_sms_sent',
				'_draft_duration_logged',
				'_sms_sent_logged'
				// Add other meta keys as needed
			);

			// Loop through each meta key and delete the post meta for all posts
			foreach ($meta_keys as $meta_key) {
				// Delete the post meta for all posts
				delete_metadata('post', 0, $meta_key, '', true);
			}
		}
        
    }
}