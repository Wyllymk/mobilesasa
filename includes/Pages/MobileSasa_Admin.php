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
       
        /**
         * Register the necessary hooks and settings for the Mobile Sasa Admin class.
         */
        public static function register(): void {

            self::$callbacks = new MobileSasa_Callbacks();

            self::$admin_settings = new MobileSasa_Settings_Api();

            self::set_pages();

            self::set_sub_pages();

            self::set_admin_settings();

            self::set_admin_sections();

            self::set_admin_fields(); 

            self::$admin_settings->add_pages(self::$pages)->with_sub_page('Dashboard')->add_sub_pages(self::$sub_pages)->register();

        }
        
        /**
         * Set the pages for the Mobile Sasa Admin menu.
         */
        public static function set_pages(): void {
            self::$pages = [
                [
                'page_title'    => 'Mobile Sasa SMS',
                'menu_title'    => 'Mobile Sasa Sms',
                'capability'    => 'manage_options',
                'menu_slug'     => 'mobilesasa-sms',
                'callback'      => [self::$callbacks, 'admin_dashboard'],
                'icon_url'      => 'dashicons-email-alt',
                'position'      => 110
                ],
            ];
        }

        /**
         * Set the sub-pages for the Mobile Sasa Admin menu.
         */
        public static function set_sub_pages(): void {
            self::$sub_pages = [
                [
                    'parent_slug'   => 'mobilesasa-sms',
                    'page_title'    => 'Mobile Sasa Settings',
                    'menu_title'    => 'Settings',
                    'capability'    => 'manage_options',
                    'menu_slug'     => 'mobilesasa-settings',
                    'callback'      => [self::$callbacks, 'admin_settings'],
                ],
                [
                    'parent_slug'   => 'mobilesasa-sms',
                    'page_title'    => 'Mobile Sasa History',
                    'menu_title'    => 'History',
                    'capability'    => 'manage_options',
                    'menu_slug'     => 'mobilesasa-history',
                    'callback'      => [self::$callbacks, 'admin_history'],
                ]
            ];
        }

        /**
         * Set the admin settings for the Mobile Sasa plugin.
         */
        public static function set_admin_settings(): void {
            $args = array(
                array(
                    'option_group'  => 'mobilesasa_admin_group',
                    'option_name'   => 'mobilesasa_defaults',
                    'callback'      => [self::$callbacks, 'mobilesasa_options_group'],
                ),
                array(
                    'option_group'  => 'mobilesasa_bulk_group',
                    'option_name'   => 'mobilesasa_bulk_options',
                    'callback'      => [self::$callbacks, 'mobilesasa_options_group'],
                ),
                array(
                    'option_group'  => 'mobilesasa_transactional_group',
                    'option_name'   => 'mobilesasa_transactional_options',
                    'callback'      => [self::$callbacks, 'mobilesasa_options_group'],
                ),     

            );
            self::$admin_settings->set_settings($args);
        }

        /**
         * Set the admin sections for the Mobile Sasa plugin settings.
         */
        public static function set_admin_sections(): void {
            $args = array(
                array(
                    'id'            => 'mobilesasa_index_token',
                    'title'         => 'Mobile Sasa Credentials',
                    'callback'      => [self::$callbacks, 'mobilesasa_admin_section'],
                    'page'          => 'mobilesasa-sms'
                ),
               
                array(
                    'id'            => 'transactionalsms_index',
                    'title'         => 'Transactional SMS Settings',
                    'callback'      => [self::$callbacks, 'mobilesasa_admin_section'],
                    'page'          => 'mobilesasa_transactional_settings'
                ),
                
            );
            self::$admin_settings->set_sections($args);
        }

        /**
         * Set the admin fields for the Mobile Sasa plugin settings.
         */
        public static function set_admin_fields(): void {
            $args = array(
                    [
                        'id'            => 'mobilesasa_sender',
                        'title'         => 'Sender ID',
                        'callback'      => [self::$callbacks, 'mobilesasa_sender'],
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
                        'callback'      => [self::$callbacks, 'mobilesasa_token'],
                        'page'          => 'mobilesasa-sms',
                        'section'       => 'mobilesasa_index_token',
                        'args'          => array(
                            'label_for' => 'mobilesasa_token',
                            'type'      => 'text',
                            'class'     => 'example-text',
                        )
                    ],
                    
                   
                    array(
                        'id'            => 'transactional_sms_enable',
                        'title'         => 'Enable/ Disable',
                        'callback'      => [self::$callbacks, 'transactional_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'admin_number'],
                        'page'          => 'mobilesasa_transactional_settings',
                        'section'       => 'transactionalsms_index',
                        'args'          => array(
                            'label_for' => 'transactional_admin_number',
                            'type'      => 'text',
                            'class'     => 'example-text',
                            'desc'      => __('Please separate multiple numbers using a comma.','mobilesasa'),
                        )
                    ),
                    array(
                        'id'            => 'admin_sms_enable',
                        'title'         => 'Receive Admin SMS',
                        'callback'      => [self::$callbacks, 'admin_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'admin_sms_message'],
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
                        'callback'      => [self::$callbacks, 'draft_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'draft_sms_message'],
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
                        'callback'      => [self::$callbacks, 'pending_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'pending_sms_message'],
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
                        'callback'      => [self::$callbacks, 'onhold_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'onhold_sms_message'],
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
                        'callback'      => [self::$callbacks, 'processing_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'processing_sms_message'],
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
                        'callback'      => [self::$callbacks, 'completed_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'completed_sms_message'],
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
                        'callback'      => [self::$callbacks, 'cancelled_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'cancelled_sms_message'],
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
                        'callback'      => [self::$callbacks, 'failed_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'failed_sms_message'],
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
                        'callback'      => [self::$callbacks, 'pickup_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'pickup_sms_message'],
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
                        'callback'      => [self::$callbacks, 'failed_delivery_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'failed_delivery_sms_message'],
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
                        'callback'      => [self::$callbacks, 'returned_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'returned_sms_message'],
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
                        'callback'      => [self::$callbacks, 'refunded_sms_enable'],
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
                        'callback'      => [self::$callbacks, 'refunded_sms_message'],
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
            self::$admin_settings->set_fields($args);
        }

    }
    
}