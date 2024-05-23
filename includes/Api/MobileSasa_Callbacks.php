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
            $default_repo_text = 'Click to activate Bulk SMS option';
        
            echo '<input type="checkbox" class="regular-text" name="mobilesasa_bulk_options[bulk_sms_enable]" value="1" ' . checked(1, $bulk_sms_enable, false) . '>';
            echo '<p class="description">' . esc_html($default_repo_text) . '</p>';

        }
        public static function bulkMessage($args) {
            $options = get_option('mobilesasa_bulk_options');
            $bulk_message = isset($options['bulk_message']) ? esc_textarea($options['bulk_message']) : '';
            $default_message_text = 'Enter your bulk message here.';
        
            $html = '<textarea class="large-text" rows="5" name="mobilesasa_bulk_options[bulk_message]" placeholder="' . esc_attr($default_message_text) . '">' . $bulk_message . '</textarea>';
        
            if (isset($args['desc'])) {
                $html .= '<p class="description">' . esc_html($args['desc']) . '</p>';
            }
        
            echo $html;
        }
        
        
    }
    
}