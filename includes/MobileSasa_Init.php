<?php
/**
 * The file that defines the Init class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/

namespace Wylly\MobileSasa;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists('MobileSasa_Init')){

    final class MobileSasa_Init{
        /**
         * Store all classes in an array
         * @return array Full list of classes
         */
        public static function getServices(){
            return [
                Pages\MobileSasa_Admin::class,
                Pages\MobileSasa_SendSMS::class,
                Pages\MobileSasa_BulkSMS::class,
                Pages\MobileSasa_TransactionalSMS::class,
                Pages\MobileSasa_CustomOrderStatus::class,
                Pages\MobileSasa_GetBalance::class,
                Pages\MobileSasa_Database::class,
                Base\MobileSasa_Settings::class,
                Base\MobileSasa_Enqueue::class
                
            ];
        }
        /**
         * Loop through the classes, initialize them, and call the register() method if it exists
         * @return  
         */        
        public static function registerServices(){
            foreach(self::getServices() as $class){
                $service = self::instantiate($class);
                if(method_exists($service, 'register')){
                    $service->register();
                }
            }
        }
        /**
         * Initialize the class 
         * @param class $class class from the services array
         * @return class instance of the class 
         */
        private static function instantiate($class){
            $service = new $class();
            return $service;
        }

    }
    
}