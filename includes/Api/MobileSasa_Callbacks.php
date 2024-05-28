<?php
/**
 * The file that defines the Mobile Sasa Callback class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Api
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/

namespace Wylly\MobileSasa\Api;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;
 
if( ! class_exists('MobileSasa_Callbacks')){

    class MobileSasa_Callbacks{

        public static function adminDashboard(){
            if(file_exists(MS_PLUGIN_PATH . 'templates/mobilesasa_dashboard.php')){
                require_once (MS_PLUGIN_PATH . 'templates/mobilesasa_dashboard.php');
            }
        }
        public static function adminSettings(){
            if(file_exists(MS_PLUGIN_PATH . 'templates/mobilesasa_settings.php')){
                require_once (MS_PLUGIN_PATH . 'templates/mobilesasa_settings.php');
            }
        }
        public static function adminHistory(){
            if(file_exists(MS_PLUGIN_PATH . 'templates/mobilesasa_history.php')){
                require_once (MS_PLUGIN_PATH . 'templates/mobilesasa_history.php');
            }
        }

        public static function mobilesasaOptionsGroup($input){
            return $input;
        }
        
        public static function mobilesasaAdminSection(){
            echo 'Please fill in the fields below!';
        }

        public static function mobilesasaSender($args) {
            $options = get_option('mobilesasa_defaults');
            $mobilesasa_sender = isset($options['mobilesasa_sender']) ? esc_attr($options['mobilesasa_sender']) : '';
        
            $html = '<input type="text" class="regular-text" name="mobilesasa_defaults[mobilesasa_sender]" value="' . $mobilesasa_sender . '" placeholder="Enter Mobile Sasa Sender ID">';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function mobilesasaToken() {
            $options = get_option('mobilesasa_defaults');
        
            // Check if the 'mobilesasa_token' key exists in the $options array
            $mobilesasa_token = isset($options['mobilesasa_token']) ? esc_attr($options['mobilesasa_token']) : '';
        
            echo '<input type="text" class="regular-text" name="mobilesasa_defaults[mobilesasa_token]" value="' . $mobilesasa_token . '" placeholder="Enter Mobile Sasa API Token">';
        }

        public static function bulkSmsEnable(){
            $options = get_option('mobilesasa_bulk_options');
            $bulk_sms_enable = isset($options['bulk_sms_enable']) ? esc_attr($options['bulk_sms_enable']) : '0';
            $default_repo_text = 'Click to activate Mobile Sasa Bulk SMS';
        
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_bulk_options[bulk_sms_enable]" value="1" ' . checked(1, $bulk_sms_enable, false) . '>';
            echo '<p class="description">' . esc_html($default_repo_text) . '</p>';

        }
        public static function bulkMessage($args) {
            $options = get_option('mobilesasa_bulk_options');
            $bulk_message = isset($options['bulk_message']) ? esc_textarea($options['bulk_message']) : '';
            $default_message_text = 'Enter your bulk message here.';
        
            $html = '<textarea class="large-text" rows="5" style="width:30em" name="mobilesasa_bulk_options[bulk_message]" placeholder="' . esc_attr($default_message_text) . '">' . $bulk_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function transactionalSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $transactional_sms_enable = isset($options['transactional_sms_enable']) ? esc_attr($options['transactional_sms_enable']) : '0';
            $default_repo_text = 'Click to activate Mobile Sasa Transactional SMS';
        
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[transactional_sms_enable]" value="1" ' . checked(1, $transactional_sms_enable, false) . '>';
            echo '<p class="description">' . esc_html($default_repo_text) . '</p>';

        }
        public static function adminNumber($args) {
            $options = get_option('mobilesasa_transactional_options');
            $transactional_admin_number = isset($options['transactional_admin_number']) ? esc_attr($options['transactional_admin_number']) : '';
            $default_message_text = 'e.g 0729123456, 0728654321';

            $html = '<input type="text" style="width:30em" class="regular-text" name="mobilesasa_transactional_options[transactional_admin_number]" value="' . esc_attr($transactional_admin_number) . '" placeholder="' . esc_attr($default_message_text) . '">';

            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }

            echo $html;
        }
        public static function adminSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $admin_sms_enable = isset($options['admin_sms_enable']) ? esc_attr($options['admin_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[admin_sms_enable]" value="1" ' . checked(1, $admin_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function adminSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $admin_sms_message = isset($options['admin_sms_message']) ? esc_textarea($options['admin_sms_message']) : '';
            $default_message_text = __('e.g Hello Admin, {name} has placed an order #{orderid}','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[admin_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $admin_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function draftSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $draft_sms_enable = isset($options['draft_sms_enable']) ? esc_attr($options['draft_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[draft_sms_enable]" value="1" ' . checked(1, $draft_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function draftSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $draft_sms_message = isset($options['draft_sms_message']) ? esc_textarea($options['draft_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, please continue with your order #{orderid}','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[draft_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $draft_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function pendingSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $pending_sms_enable = isset($options['pending_sms_enable']) ? esc_attr($options['pending_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[pending_sms_enable]" value="1" ' . checked(1, $pending_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function pendingSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $pending_sms_message = isset($options['pending_sms_message']) ? esc_textarea($options['pending_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, we have received your order #{orderid} please finish payment','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[pending_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $pending_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function onholdSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $onhold_sms_enable = isset($options['onhold_sms_enable']) ? esc_attr($options['onhold_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[onhold_sms_enable]" value="1" ' . checked(1, $onhold_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function onholdSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $onhold_sms_message = isset($options['onhold_sms_message']) ? esc_textarea($options['onhold_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} is on hold pending payment confirmation','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[onhold_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $onhold_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function processingSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $processing_sms_enable = isset($options['processing_sms_enable']) ? esc_attr($options['processing_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[processing_sms_enable]" value="1" ' . checked(1, $processing_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function processingSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $processing_sms_message = isset($options['processing_sms_message']) ? esc_textarea($options['processing_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, we have received your order #{orderid}','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[processing_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $processing_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function completedSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $completed_sms_enable = isset($options['completed_sms_enable']) ? esc_attr($options['completed_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[completed_sms_enable]" value="1" ' . checked(1, $completed_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function completedSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $completed_sms_message = isset($options['completed_sms_message']) ? esc_textarea($options['completed_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, we have shipped your order #{orderid}','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[completed_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $completed_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function cancelledSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $cancelled_sms_enable = isset($options['cancelled_sms_enable']) ? esc_attr($options['cancelled_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[cancelled_sms_enable]" value="1" ' . checked(1, $cancelled_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function cancelledSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $cancelled_sms_message = isset($options['cancelled_sms_message']) ? esc_textarea($options['cancelled_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, we have cancelled your order #{orderid}','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[cancelled_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $cancelled_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function failedSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $failed_sms_enable = isset($options['failed_sms_enable']) ? esc_attr($options['failed_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[failed_sms_enable]" value="1" ' . checked(1, $failed_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function failedSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $failed_sms_message = isset($options['failed_sms_message']) ? esc_textarea($options['failed_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} has failed payment','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[failed_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $failed_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function pickupSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $pickup_sms_enable = isset($options['pickup_sms_enable']) ? esc_attr($options['pickup_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[pickup_sms_enable]" value="1" ' . checked(1, $pickup_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function pickupSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $pickup_sms_message = isset($options['pickup_sms_message']) ? esc_textarea($options['pickup_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} is ready for pickup','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[pickup_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $pickup_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function failedDeliverySmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $failed_delivery_sms_enable = isset($options['failed_delivery_sms_enable']) ? esc_attr($options['failed_delivery_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[failed_delivery_sms_enable]" value="1" ' . checked(1, $failed_delivery_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function failedDeliverySmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $failed_delivery_sms_message = isset($options['failed_delivery_sms_message']) ? esc_textarea($options['failed_delivery_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} has failed delivery','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[failed_delivery_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $failed_delivery_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function returnedSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $returned_sms_enable = isset($options['returned_sms_enable']) ? esc_attr($options['returned_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[returned_sms_enable]" value="1" ' . checked(1, $returned_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function returnedSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $returned_sms_message = isset($options['returned_sms_message']) ? esc_textarea($options['returned_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} has been returned','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[returned_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $returned_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        public static function refundedSmsEnable(){
            $options = get_option('mobilesasa_transactional_options');
            $refunded_sms_enable = isset($options['refunded_sms_enable']) ? esc_attr($options['refunded_sms_enable']) : '0';
            $default_repo_text = 'Send SMS';

            echo '<label>';
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_transactional_options[refunded_sms_enable]" value="1" ' . checked(1, $refunded_sms_enable, false) . '>';
            echo '<span>' . esc_html($default_repo_text) . '</span>';
            echo '</label>';

        }
        public static function refundedSmsMessage($args) {
            $options = get_option('mobilesasa_transactional_options');
            $refunded_sms_message = isset($options['refunded_sms_message']) ? esc_textarea($options['refunded_sms_message']) : '';
            $default_message_text = __('e.g Hello {name}, your order #{orderid} has been refunded','mobilesasa');
        
            $html = '<textarea class="large-text" rows="3" style="width:30em" name="mobilesasa_transactional_options[refunded_sms_message]" placeholder="' . esc_attr($default_message_text) . '">' . $refunded_sms_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        
    }
    
}