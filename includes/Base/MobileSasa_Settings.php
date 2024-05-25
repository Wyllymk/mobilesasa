<?php
/**
 * @package MobileSasa
*/

namespace Wylly\MobileSasa\Base;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;
 
if( ! class_exists('MobileSasa_Settings')){

    class MobileSasa_Settings{

        /**
         * Register the necessary hooks for the MobileSasa_Settings class.
         */
        public static function register(): void {
            add_filter( 'plugin_action_links_'.MS_PLUGIN_NAME, array(self::class, 'settingsLink') );
        }
        
        /**
         * Add a settings link to the plugin's entry in the plugins list.
         *
         * @param array $links An array of existing links for the plugin.
         * @return array The updated array of links with the settings link added.
         */
        public static function settingsLink(array $links): array {
            $settings_link = '<a href="admin.php?page=mobilesasa-sms">Settings</a>';
            array_push( $links, $settings_link);
            return $links;
        }    
        
    }
    
}