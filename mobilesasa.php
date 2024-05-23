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
 * Plugin Name:             MobileSasa SMS
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
define( 'MS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MS_PLUGIN_NAME', plugin_basename(__FILE__) );