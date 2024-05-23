<?php
/**
 * @package MobileSasa
*/

namespace Wylly\MobileSasa\Base;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;
 
if( ! class_exists('MobileSasa_Settings')){

    class MobileSasa_Settings{

        public static function register(){
            add_filter( 'plugin_action_links_'.MS_PLUGIN_NAME, array(__CLASS__, 'settingsLink') );
        }
        
        public static function settingsLink($links){
            $settings_link = '<a href="admin.php?page=mobilesasa-sms">Settings</a>';
            array_push( $links, $settings_link);
            return $links;
        }    
        
    }
    
}