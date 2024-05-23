<?php
/**
 * The file that defines the Mobile Sasa Admin class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Pages
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/

namespace Wylly\MobileSasa\Pages;

use \Wylly\MobileSasa\Api\MobileSasa_Callbacks;
use \Wylly\MobileSasa\Api\MobileSasa_Settings_Api;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists('MobileSasa_Admin')){

    class MobileSasa_Admin{

        public static $callbacks;

        public static $admin_settings;

        public static $pages = array();

        public static $sub_pages = array();
       
        public static function register(){

            self::$callbacks = new MobileSasa_Callbacks();

            self::$admin_settings = new MobileSasa_Settings_Api();

            self::setPages();

            self::setSubPages();

            self::setAdminSettings();

            self::setAdminSections();

            self::setAdminFields(); 

            self::$admin_settings->addPages(self::$pages)->withSubPage('Dashboard')->addSubPages(self::$sub_pages)->register();

        }
        
        public static function setPages(){
            self::$pages = [
                [
                'page_title'    => 'Mobile Sasa SMS',
                'menu_title'    => 'Mobile Sasa Sms',
                'capability'    => 'manage_options',
                'menu_slug'     => 'mobilesasa-sms',
                'callback'      => [self::$callbacks, 'adminDashboard'],
                'icon_url'      => 'dashicons-email-alt',
                'position'      => 110
                ],
            ];
        }

        public static function setSubPages(){
            self::$sub_pages = [
                [
                    'parent_slug'   => 'mobilesasa-sms',
                    'page_title'    => 'Mobile Sasa Settings',
                    'menu_title'    => 'Settings',
                    'capability'    => 'manage_options',
                    'menu_slug'     => 'mobilesasa-settings',
                    'callback'      => [self::$callbacks, 'adminSettings'],
                ]
            ];
        }

        public static function setAdminSettings(){
            $args = array(
                array(
                    'option_group'  => 'mobilesasa_admin_group',
                    'option_name'   => 'mobilesasa_defaults'
                ),
                array(
                    'option_group'  => 'mobilesasa_bulk_group',
                    'option_name'   => 'mobilesasa_bulk_options',
                    'callback'      => [self::$callbacks, 'mobilesasaOptionsGroup'],
                ),
                array(
                    'option_group'  => 'mobilesasa_transactional_group',
                    'option_name'   => 'mobilesasa_bulk_options',
                    'callback'      => [self::$callbacks, 'mobilesasaOptionsGroup'],
                ),     

            );
            self::$admin_settings->setSettings($args);
        }

        public static function setAdminSections(){
            $args = array(
                array(
                    'id'            => 'mobilesasa_index_token',
                    'title'         => 'Mobile Sasa Credentials',
                    'callback'      => [self::$callbacks, 'mobilesasaAdminSection'],
                    'page'          => 'mobilesasa-sms'
                ),
                array(
                    'id'            => 'bulksms_index',
                    'title'         => 'Bulk SMS Settings',
                    'callback'      => [self::$callbacks, 'mobilesasaAdminSection'],
                    'page'          => 'mobilesasa-settings'
                ),
                
            );
            self::$admin_settings->setSections($args);
        }

        public static function setAdminFields(){
            $args = array(
                    [
                        'id'            => 'mobilesasa_sender',
                        'title'         => 'Sender ID',
                        'callback'      => [self::$callbacks, 'mobilesasaSender'],
                        'page'          => 'mobilesasa-sms',
                        'section'       => 'mobilesasa_index_token',
                        'args'          => array(
                            'label_for' => 'mobilesasa_sender',
                            'type'      => 'text',
                            'class'     => 'example-text',
                            'desc'      => __( 'e.g MOBILESASA', 'mobilesasa' ),
                        )
                    ],
                    [
                        'id'            => 'mobilesasa_token',
                        'title'         => 'API Token',
                        'callback'      => [self::$callbacks, 'mobilesasaToken'],
                        'page'          => 'mobilesasa-sms',
                        'section'       => 'mobilesasa_index_token',
                        'args'          => array(
                            'label_for' => 'mobilesasa_token',
                            'type'      => 'text',
                            'class'     => 'example-text',
                        )
                    ],
                    
                    array(
                        'id'            => 'bulk_sms_enable',
                        'title'         => 'Enable/ Disable',
                        'callback'      => [self::$callbacks, 'bulkSmsEnable'],
                        'page'          => 'mobilesasa-settings',
                        'section'       => 'bulksms_index',
                        'args'          => array(
                            'label_for' => 'bulk_sms_enable',
                            'type'      => 'text',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'bulk_message',
                        'title'         => 'Message',
                        'callback'      => [self::$callbacks, 'bulkMessage'],
                        'page'          => 'mobilesasa-settings',
                        'section'       => 'bulksms_index',
                        'args'          => array(
                            'label_for' => 'bulk_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => 'This message will be sent as a bulk SMS to your customers.',
                        )
                    ),
                    
                    
            );
            self::$admin_settings->setFields($args);
        }

    }
    
}