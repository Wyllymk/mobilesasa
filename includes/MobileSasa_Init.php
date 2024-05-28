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
                Base\MobileSasa_Settings::class,
                Base\MobileSasa_Enqueue::class,
                Plugin\MobileSasa_SendSMS::class,
                Plugin\MobileSasa_BulkSMS::class,
                Plugin\MobileSasa_TransactionalSMS::class,
                Plugin\MobileSasa_CustomOrderStatus::class,
                Plugin\MobileSasa_GetBalance::class,
                Plugin\MobileSasa_Database::class,
                
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