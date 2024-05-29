<?php
/**
 * The file that defines the functionality for adding custom order statuses.
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Plugin
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
 */

namespace Wylly\MobileSasa\Plugin;

// If direct access, then exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_CustomOrderStatus')) {
    /**
     * MobileSasa_CustomOrderStatus class
     */
    class MobileSasa_CustomOrderStatus {

        /**
         * Registers the necessary hooks and initializes the class.
         */
        public static function register(): void {
            self::register_custom_order_status_hooks();
        }

        /**
         * Register the hooks for adding custom order statuses.
         */
        private static function register_custom_order_status_hooks(): void {
            add_action('init', [self::class, 'add_custom_order_status_ready_for_pickup']);
            add_action('init', [self::class, 'add_custom_order_status_failed_delivery']);
            add_action('init', [self::class, 'add_custom_order_status_returned']);
            add_action('wc_order_statuses', [self::class, 'add_custom_order_statuses_to_dropdown'], 10, 1);
        }

        /**
         * Add the custom order status "Ready for Pickup" to WooCommerce.
         */
        public static function add_custom_order_status_ready_for_pickup(): void {
            self::register_custom_order_status('wc-ready-for-pickup', 'Ready for Pickup');
        }

        /**
         * Add the custom order status "Failed Delivery" to WooCommerce.
         */
        public static function add_custom_order_status_failed_delivery(): void {
            self::register_custom_order_status('wc-failed-delivery', 'Failed Delivery');
        }

        /**
         * Add the custom order status "Returned" to WooCommerce.
         */
        public static function add_custom_order_status_returned(): void {
            self::register_custom_order_status('wc-returned', 'Returned');
        }

        /**
         * Register a custom order status with WooCommerce.
         *
         * @param string $slug   The slug for the custom order status.
         * @param string $label  The label for the custom order status.
         */
        private static function register_custom_order_status(string $slug, string $label): void {
            register_post_status($slug, [
                'label'                     => $label,
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop("{$label} <span class='count'>(%s)</span>", "{$label} <span class='count'>(%s)</span>"),
            ]);
        }

        /**
         * Add custom order statuses to the WooCommerce order status dropdown.
         *
         * @param array $order_statuses The existing order statuses.
         * @return array The updated order statuses, including the custom order statuses.
         */
        public static function add_custom_order_statuses_to_dropdown(array $order_statuses): array {
            $updated_order_statuses = [];

            foreach ($order_statuses as $key => $status) {
                $updated_order_statuses[$key] = $status;

                if ('wc-completed' === $key) {
                    $updated_order_statuses['wc-ready-for-pickup'] = 'Ready for Pickup';
                    $updated_order_statuses['wc-failed-delivery'] = 'Failed Delivery';
                    $updated_order_statuses['wc-returned'] = 'Returned';
                }
            }

            return $updated_order_statuses;
        }
    }
}