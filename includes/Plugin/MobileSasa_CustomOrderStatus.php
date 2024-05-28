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
            self::registerCustomOrderStatusHooks();
        }

        /**
         * Register the hooks for adding custom order statuses.
         */
        private static function registerCustomOrderStatusHooks(): void {
            add_action('init', [self::class, 'addCustomOrderStatusReadyForPickup']);
            add_action('init', [self::class, 'addCustomOrderStatusFailedDelivery']);
            add_action('init', [self::class, 'addCustomOrderStatusReturned']);
            add_action('wc_order_statuses', [self::class, 'addCustomOrderStatusesToDropdown'], 10, 1);
        }

        /**
         * Add the custom order status "Ready for Pickup" to WooCommerce.
         */
        public static function addCustomOrderStatusReadyForPickup(): void {
            self::registerCustomOrderStatus('wc-ready-for-pickup', 'Ready for Pickup');
        }

        /**
         * Add the custom order status "Failed Delivery" to WooCommerce.
         */
        public static function addCustomOrderStatusFailedDelivery(): void {
            self::registerCustomOrderStatus('wc-failed-delivery', 'Failed Delivery');
        }

        /**
         * Add the custom order status "Returned" to WooCommerce.
         */
        public static function addCustomOrderStatusReturned(): void {
            self::registerCustomOrderStatus('wc-returned', 'Returned');
        }

        /**
         * Register a custom order status with WooCommerce.
         *
         * @param string $slug   The slug for the custom order status.
         * @param string $label  The label for the custom order status.
         */
        private static function registerCustomOrderStatus(string $slug, string $label): void {
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
         * @param array $orderStatuses The existing order statuses.
         * @return array The updated order statuses, including the custom order statuses.
         */
        public static function addCustomOrderStatusesToDropdown(array $orderStatuses): array {
            $updatedOrderStatuses = [];

            foreach ($orderStatuses as $key => $status) {
                $updatedOrderStatuses[$key] = $status;

                if ('wc-completed' === $key) {
                    $updatedOrderStatuses['wc-ready-for-pickup'] = 'Ready for Pickup';
                    $updatedOrderStatuses['wc-failed-delivery'] = 'Failed Delivery';
                    $updatedOrderStatuses['wc-returned'] = 'Returned';
                }
            }

            return $updatedOrderStatuses;
        }
    }
}