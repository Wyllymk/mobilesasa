<?php
/**
 * The file that defines the Mobile Sasa Database class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Plugin
 *
 * Author: Wilson Devops <wilsonkabatha@gmail.com>
 */

namespace Wylly\MobileSasa\Plugin;

// If direct access, then exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_Database')) {

    class MobileSasa_Database {

        private static $messages_table_name;
        private static $schedule_messages_table_name;

        /**
         * Initialize the class and set the table names.
         * Register the necessary hooks for the database setup.
         */
        public static function register(): void {
            global $wpdb;
            self::$messages_table_name = $wpdb->prefix . 'mobilesasa_messages';
            self::$schedule_messages_table_name = $wpdb->prefix . 'mobilesasa_scheduled_messages';

            add_action('admin_init', [self::class, 'create_messages_table']);
            add_action('admin_init', [self::class, 'create_scheduled_messages_table']);
        }

        /**
         * Create the database table for storing the messages.
         */
        public static function create_messages_table(): void {
            global $wpdb;

            $table_name = self::$messages_table_name;
            // Check if the table has been created before
            $table_created = get_option('mobilesasa_messages_table_created');
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            
            if (!$table_created || !$table_exists) {
                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    sent_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    message_body text NOT NULL,
                    recipients longtext NOT NULL,
                    status varchar(20) NOT NULL,
                    delivered_count int NOT NULL,
                    message_id varchar(50) NOT NULL,
                    PRIMARY KEY (id)
                ) $charset_collate;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                // Set the option to indicate the table has been created
                update_option('mobilesasa_messages_table_created', true);
            }
        }

        /**
         * Create the database table for storing the scheduled messages.
         */
        public static function create_scheduled_messages_table() {
            global $wpdb;
            $table_name = self::$schedule_messages_table_name;
        
            // Check if the table has been created before
            $table_created = get_option('mobilesasa_scheduled_messages_table_created');
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            
            if (!$table_created || !$table_exists) {
                $charset_collate = $wpdb->get_charset_collate();
        
                $sql = "CREATE TABLE $table_name (
                    id bigint(20) NOT NULL AUTO_INCREMENT,
                    message text NOT NULL,
                    recipients text NOT NULL,
                    schedule_time datetime NOT NULL,
                    status varchar(20) DEFAULT 'pending' NOT NULL,
                    PRIMARY KEY  (id)
                ) $charset_collate;";
            
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

                // Set the option to indicate the table has been created
                update_option('mobilesasa_scheduled_messages_table_created', true);
            }
            
        }        

        /**
         * Save the balance to the database.
         *
         * @param float $balance The balance value to save.
         */
        public static function save_balance(float $balance): void {
            global $wpdb;

            $table_name = self::$balance_table_name;
            $wpdb->insert(
                $table_name,
                [
                    'balance' => $balance,
                    'created_at' => current_time('mysql')
                ]
            );
        }
    }
}