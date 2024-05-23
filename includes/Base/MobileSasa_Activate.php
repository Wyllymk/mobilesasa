<?php
/**
 * @package MobileSasa
*/

namespace Wylly\MobileSasa\Base;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;
 
if( ! class_exists('MobileSasa_Activate')){

    class MobileSasa_Activate{

        public static function activate(){
            flush_rewrite_rules();
        }

    }
    
}