<?php
/**
 * Trigger this file on plugin uninstall
 * 
 * @package MobileSasa
*/

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
delete_option( 'mobilesasa_transactional_options' );
delete_option( 'mobilesasa_defaults' );
delete_option( 'mobilesasa_messages_table_created' );
delete_option( 'mobilesasa_scheduled_messages_table_created' );


// Delete plugin tables
global $wpdb;
$messages_table_name = $wpdb->prefix . 'mobilesasa_messages';
$schedule_messages_table_name = $wpdb->prefix . 'mobilesasa_scheduled_messages';

$wpdb->query( "DROP TABLE IF EXISTS $messages_table_name" );
$wpdb->query( "DROP TABLE IF EXISTS $schedule_messages_table_name" );

// Delete plugin transients
delete_transient( 'mobilesasa_balance_response' );

// Remove scheduled cron jobs
wp_clear_scheduled_hook( 'send_draft_order_sms' );
wp_clear_scheduled_hook( 'mobilesasa_send_scheduled_sms' );
wp_clear_scheduled_hook( 'delete_custom_post_meta_event' );

// Additional cleanup if necessary
// For example, deleting custom post meta data created by the plugin
$meta_keys = array(
    '_admin_sms_sent',
    '_draft_duration_logged',
    '_sms_sent_logged'
    // Add other meta keys as needed
);

foreach ( $meta_keys as $meta_key ) {
    delete_metadata( 'post', 0, $meta_key, '', true );
}