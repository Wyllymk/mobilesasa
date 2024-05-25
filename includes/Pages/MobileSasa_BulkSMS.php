<?php
/**
 * The file that defines the WooCommerce Bulk SMS class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/pages
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/

namespace Wylly\MobileSasa\Pages;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists('MobileSasa_BulkSMS')){

    class MobileSasa_BulkSMS {
        /**
         * Register the necessary hooks for the WooCommerce Bulk SMS functionality.
         */
        public static function register(): void {
            
            add_action('admin_post_send_bulk_sms', [self::class, 'handleSendBulkSms']);
            add_action('woocommerce_after_order_notes', [self::class, 'smsOptInCheckout']);
            add_action('woocommerce_checkout_update_order_meta', [self::class, 'saveSmsOptIn']);
        }

 
        /**
         * Handle the submission of the bulk SMS form.
         *
         * This function checks if the user has the required permissions, and if the bulk SMS is enabled.
         * If enabled, it sends the bulk SMS to the provided phone numbers using the MobileSasa_SendSMS class.
         */
        public static function handleSendBulkSms(): void {
            
            if (!current_user_can('manage_options') || !check_admin_referer('send_bulk_sms_nonce')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'mobilesasa'));
            }
    
            $options = get_option('mobilesasa_bulk_options');
            $message = $options['bulk_message'] ?? '';
            $is_enabled = $options['bulk_sms_enable'] ?? '0';
    
            if ($is_enabled == '1' && !empty($message)) {
                // Get the default sender ID and API token for the SMS service
                $default_options = get_option('mobilesasa_defaults');
                $senderid = $default_options['mobilesasa_sender'];
                $apitoken = $default_options['mobilesasa_token'];
    
                // Initialize the SMS sending class with the sender ID and API token
                MobileSasa_SendSMS::init($senderid, $apitoken);
    
                if (!empty($_POST['send_sms'])) {
                    $phones = array_map('sanitize_text_field', $_POST['send_sms']);
                    foreach ($phones as $phone) {
                        MobileSasa_SendSMS::wcSendExpressPostSMS(MobileSasa_SendSMS::wcCleanPhone($phone), $message);
                    }
    
                    // Add a success message
                    set_transient('wcbulksms_message_sent', true, 30);
                }
    
                // Redirect to the same page with a success parameter
                wp_redirect(add_query_arg('sent', 'true', wp_get_referer()));
                exit;
            }
        }


        /**
         * Display the SMS opt-in checkbox field on the WooCommerce checkout page.
         *
         * @param WC_Checkout $checkout The WooCommerce checkout object.
         */
        public static function smsOptInCheckout(WC_Checkout $checkout): void {
            woocommerce_form_field('sms_opt_in', [
                'type' => 'checkbox',
                'class' => ['form-row-wide'],
                'label' => __('Opt-in for SMS notifications for new products', 'woocommerce'),
            ], $checkout->get_value('sms_opt_in'));
        }

        /**
         * Save the SMS opt-in preference for the current order.
         *
         * @param int $order_id The ID of the order.
         */
        public static function saveSmsOptIn(int $order_id): void {
            if (!empty($_POST['sms_opt_in'])) {
                update_post_meta($order_id, '_sms_opt_in', 'yes');
            } else {
                update_post_meta($order_id, '_sms_opt_in', 'no');
            }
        }
    }
}