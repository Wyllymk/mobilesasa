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

        private static $table_name;

        /**
         * Initialize the class and set the table name.
         * Register the necessary hooks for the database setup.
         */
        public static function register(): void {
            global $wpdb;
            self::$table_name = $wpdb->prefix . 'mobilesasa';

            add_action('init', [self::class, 'create_table']);
        }

        /**
         * Create the database table for storing the balance.
         */
        public static function create_table(): void {
            global $wpdb;

            $table_name = self::$table_name;
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                balance decimal(10,2) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        /**
         * Save the balance to the database.
         *
         * @param float $balance The balance value to save.
         */
        public static function save_balance(float $balance): void {
            global $wpdb;

            $table_name = self::$table_name;
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