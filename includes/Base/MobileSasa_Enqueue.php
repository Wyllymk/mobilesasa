<?php
/**
 * @package MobileSasa
*/

namespace Wylly\MobileSasa\Base;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;
 
if( ! class_exists('MobileSasa_Enqueue')){

    class MobileSasa_Enqueue{

        public static function register(){
            add_action( 'admin_enqueue_scripts', array(self::class, 'enqueue_admin_scripts') );
        }

        public static function enqueue_admin_scripts(){
            wp_enqueue_style('ms-style', MS_PLUGIN_URL . 'assets/css/mobilesasa.css', array(), wp_get_theme()->get( 'Version' ), 'all');
            wp_enqueue_script('ms-script', MS_PLUGIN_URL . 'assets/js/mobilesasa.js', array( 'jquery' ), wp_get_theme()->get( 'Version' ), true);
            
            // Pass AJAX URL and nonces to the script
            wp_localize_script('ms-script', 'mobilesasa', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce_check_balance' => wp_create_nonce('check_balance_nonce'),
                'nonce_delete_message' => wp_create_nonce('delete_message_nonce'),
                'nonce_delivery_status' => wp_create_nonce('delivery_status_sms_nonce'),
            ));
        }
        
    }
    
}