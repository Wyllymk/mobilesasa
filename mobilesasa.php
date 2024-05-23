<?php
/** 
 *  
 *  MobileSasa SMS
 * 
 * @package           MobileSasa
 * @author            Wilson Devops <wilsonkabatha@gmail.com>
 * @copyright         2024 Wilson Devops
 * @license           GPL-2.0-or-later
 * @link              https://github.com/Wyllymk/mobilesasa
 * 
 * @wordpress-plugin
 * 
 * Plugin Name:             MOBILESASA SMS
 * Plugin URI:              https://github.com/Wyllymk/mobilesasa 
 * Description:             A plugin to handle bulk SMS in MobileSasa. 
 * Version:                 1.0.0 
 * Requires at least:       6.0
 * Requires PHP:            7.2 
 * Tested up to:            6.5
 * WC requires at least:    8.9
 * Author:                  Wilson Devops 
 * Author URI:              https://wilsondevops.com
 * Text Domain:             mobilesasa
 * License:                 GPL v2 or later 
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt 
 * Update URI:              https://github.com/Wyllymk/mobilesasa 
 * Requires Plugins:        woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define Constants
define( 'MS_PLUGIN_VERSION', '1.0.0');
define( 'MS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MS_PLUGIN_NAME', plugin_basename(__FILE__) );

if(file_exists(MS_PLUGIN_PATH . 'vendor/autoload.php')){
    require_once (MS_PLUGIN_PATH . 'vendor/autoload.php');
}

/**
 * The function "activateMobileSasaExternally" activates the "Github_Actions_Activate" class externally.
 */
function activateMobileSasaExternally(){
    Wylly\MobileSasa\Base\MobileSasa_Activate::activate();
}

// The function is used to register a callback function that will be executed when the plugin is activated. 
register_activation_hook(__FILE__, 'activateMobileSasaExternally');

/**
 * The function "deactivateMobileSasaExternally" calls the "deactivate" method of the
 * "Github_Actions_Deactivate" class.
 */
function deactivateMobileSasaExternally(){
    Wylly\MobileSasa\Base\MobileSasa_Deactivate::deactivate();

}

// The function is used to register a callback function that will be executed when the plugin is deactivated. 
register_deactivation_hook(__FILE__, 'deactivateMobileSasaExternally');

/* Checking if the class exists and if it does, it will register the services. 
* This is a way to ensure that the class is loaded before calling its methods, preventing any errors or issues. 
*/
if(class_exists('Wylly\\MobileSasa\\MobileSasa_Init')){
    Wylly\MobileSasa\MobileSasa_Init::registerServices();
}