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
            add_action( 'admin_enqueue_scripts', array(__CLASS__, 'enqueueAdminScripts') );
        }

        public static function enqueueAdminScripts(){
            wp_enqueue_style('ms-style', MS_PLUGIN_URL . 'assets/css/mobilesasa.css', array(), wp_get_theme()->get( 'Version' ), 'all');
            wp_enqueue_script('ms-script', MS_PLUGIN_URL . 'assets/js/mobilesasa.js', array( 'jquery' ), wp_get_theme()->get( 'Version' ), true);
           // Pass AJAX URL to the script
        //    $title_nonce = wp_create_nonce( 'github_actions_theme_nonce' );
        //     wp_localize_script('ms-script', 'workflowAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => $title_nonce,));
        }
        
    }
    
}