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

        public static function register(): void {
            
            add_action('admin_post_send_bulk_sms', [self::class, 'handle_send_bulk_sms']);
            add_action('woocommerce_after_order_notes', [self::class, 'sms_opt_in_checkout']);
            add_action('woocommerce_checkout_update_order_meta', [self::class, 'save_sms_opt_in']);
        }

 
        public static function handle_send_bulk_sms(): void {
            if (!current_user_can('manage_options') || !check_admin_referer('send_bulk_sms_nonce')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'mobilesasa'));
            }
    
            $options = get_option('mobilesasa_bulk_options');
            $message = $options['bulk_message'] ?? '';
            $is_enabled = $options['bulk_sms_enable'] ?? '0';

            // $message = sanitize_text_field($_POST['bulk_sms_message']);
            // update_option('bulk_message', $message);

    
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


        // Display custom checkout field
        public static function sms_opt_in_checkout($checkout) {
            woocommerce_form_field('sms_opt_in', [
                'type' => 'checkbox',
                'class' => ['form-row-wide'],
                'label' => __('Opt-in for SMS notifications for new products', 'woocommerce'),
            ], $checkout->get_value('sms_opt_in'));
        }

        public static function save_sms_opt_in($order_id) {
            if (!empty($_POST['sms_opt_in'])) {
                update_post_meta($order_id, '_sms_opt_in', 'yes');
            } else {
                update_post_meta($order_id, '_sms_opt_in', 'no');
            }
        }
    }
}