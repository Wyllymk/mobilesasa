<?php
/**
 * @package MobileSasa
*/

namespace Wylly\MobileSasa\Base;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;


if( ! class_exists('MobileSasa_Deactivate')){

    class MobileSasa_Deactivate{

        public static function deactivate(){
            flush_rewrite_rules();
        }

    }
    
}