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
                    'option_name'   => 'mobilesasa_transactional_options',
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
                    'page'          => 'mobilesasa_bulk_settings'
                ),
                array(
                    'id'            => 'transactionalsms_index',
                    'title'         => 'Transactional SMS Settings',
                    'callback'      => [self::$callbacks, 'mobilesasaAdminSection'],
                    'page'          => 'mobilesasa_transactional_settings'
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
                        'page'          => 'mobilesasa_bulk_settings',
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
                        'page'          => 'mobilesasa_bulk_settings',
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
                        'page'          => 'mobilesasa_bulk_settings',
                        'section'       => 'bulksms_index',
                        'args'          => array(
                            'label_for' => 'bulk_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'bulk_message',
                        'title'         => 'Message',
                        'callback'      => [self::$callbacks, 'bulkMessage'],
                        'page'          => 'mobilesasa_bulk_settings',
                        'section'       => 'bulksms_index',
                        'args'          => array(
                            'label_for' => 'bulk_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __('This message will be sent as a bulk SMS to your customers.','mobilesasa'),
                        )
                    ),
                    array(
                        'id'            => 'transactional_sms_enable',
                        'title'         => 'Enable/ Disable',
                        'callback'      => [self::$callbacks, 'transactionalSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'transactional_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'transactional_admin_number',
                        'title'         => 'Admin Number',
                        'callback'      => [self::$callbacks, 'adminNumber'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'transactional_admin_number',
                            'type'      => 'text',
                            'class'     => 'example-text',
                            'desc'      => __('Admin Number will receive a text on every order placed.','mobilesasa'),
                        )
                    ),
                    array(
                        'id'            => 'admin_sms_enable',
                        'title'         => 'Receive Admin SMS',
                        'callback'      => [self::$callbacks, 'adminSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'admin_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'admin_sms_message',
                        'title'         => 'Admin Placed Order SMS',
                        'callback'      => [self::$callbacks, 'adminSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'admin_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'draft_sms_enable',
                        'title'         => 'Order Draft',
                        'callback'      => [self::$callbacks, 'draftSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'draft_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'draft_sms_message',
                        'title'         => 'Order Draft SMS',
                        'callback'      => [self::$callbacks, 'draftSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'draft_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'pending_sms_enable',
                        'title'         => 'Order Pending',
                        'callback'      => [self::$callbacks, 'pendingSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'pending_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'pending_sms_message',
                        'title'         => 'Order Pending Payment SMS',
                        'callback'      => [self::$callbacks, 'pendingSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'pending_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'onhold_sms_enable',
                        'title'         => 'Order On Hold',
                        'callback'      => [self::$callbacks, 'onholdSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'onhold_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'onhold_sms_message',
                        'title'         => 'Order On Hold SMS',
                        'callback'      => [self::$callbacks, 'onholdSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'onhold_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'processing_sms_enable',
                        'title'         => 'Order Processing',
                        'callback'      => [self::$callbacks, 'processingSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'processing_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'processing_sms_message',
                        'title'         => 'Order Processing SMS',
                        'callback'      => [self::$callbacks, 'processingSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'processing_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'completed_sms_enable',
                        'title'         => 'Order Completed',
                        'callback'      => [self::$callbacks, 'completedSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'completed_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'completed_sms_message',
                        'title'         => 'Order Completed SMS',
                        'callback'      => [self::$callbacks, 'completedSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'completed_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'cancelled_sms_enable',
                        'title'         => 'Order Cancelled',
                        'callback'      => [self::$callbacks, 'cancelledSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'cancelled_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'cancelled_sms_message',
                        'title'         => 'Order Cancelled SMS',
                        'callback'      => [self::$callbacks, 'cancelledSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'cancelled_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'failed_sms_enable',
                        'title'         => 'Order Failed',
                        'callback'      => [self::$callbacks, 'failedSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'failed_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'failed_sms_message',
                        'title'         => 'Order Failed SMS',
                        'callback'      => [self::$callbacks, 'failedSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'failed_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'pickup_sms_enable',
                        'title'         => 'Order Ready for Pickup',
                        'callback'      => [self::$callbacks, 'pickupSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'pickup_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'pickup_sms_message',
                        'title'         => 'Order Ready for Pickup SMS',
                        'callback'      => [self::$callbacks, 'pickupSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'pickup_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'failed_delivery_sms_enable',
                        'title'         => 'Order Failed Delivery',
                        'callback'      => [self::$callbacks, 'failedDeliverySmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'failed_delivery_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'failed_delivery_sms_message',
                        'title'         => 'Order Failed Delivery SMS',
                        'callback'      => [self::$callbacks, 'failedDeliverySmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'failed_delivery_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'returned_sms_enable',
                        'title'         => 'Order Returned',
                        'callback'      => [self::$callbacks, 'returnedSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'returned_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'returned_sms_message',
                        'title'         => 'Order Returned SMS',
                        'callback'      => [self::$callbacks, 'returnedSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'returned_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    array(
                        'id'            => 'refunded_sms_enable',
                        'title'         => 'Order Refunded',
                        'callback'      => [self::$callbacks, 'refundedSmsEnable'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'refunded_sms_enable',
                            'type'      => 'checkbox',
                            'class'     => 'example-text',
                        )
                    ),
                    array(
                        'id'            => 'refunded_sms_message',
                        'title'         => 'Order Refunded SMS',
                        'callback'      => [self::$callbacks, 'refundedSmsMessage'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'refunded_sms_message',
                            'type'      => 'textarea',
                            'class'     => 'example-text',
                            'desc'      => __( 'Order shortcodes: {name} {orderid} {total} {phone}','mobilesasa' ),
                            'desc_tip'  => __( 'Please use ONLY the provided shortcodes.', 'mobilesasa' ),

                        )
                    ),
                    
            );
            self::$admin_settings->setFields($args);
        }

    }
    
}