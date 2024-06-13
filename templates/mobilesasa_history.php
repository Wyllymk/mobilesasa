<?php
// Check if in admin area and load necessary files
if (!defined('ABSPATH') || !is_admin()) {
    exit;
}

// Check if settings were updated and set appropriate message
$is_update = isset($_GET['settings-updated']);
$message = $is_update ? __('Messages Updated', 'mobilesasa') : __('Settings Saved', 'mobilesasa');

if ($is_update) {
    add_settings_error('mobilesasa_messages', 'ga_message', $message, 'updated');
}

// Retrieve sent and scheduled messages from the database
global $wpdb;
$mobilesasa_messages = $wpdb->prefix . 'mobilesasa_messages';
$sent_messages = $wpdb->get_results("SELECT * FROM $mobilesasa_messages ORDER BY sent_at DESC", ARRAY_A);

$mobilesasa_scheduled_messages = $wpdb->prefix . 'mobilesasa_scheduled_messages';
$scheduled_messages = $wpdb->get_results("SELECT * FROM $mobilesasa_scheduled_messages ORDER BY id DESC", ARRAY_A);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('mobilesasa_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1"><?php esc_html_e('Scheduled Messages', 'mobilesasa'); ?></a></li>
        <li><a href="#tab-2"><?php esc_html_e('Sent Messages', 'mobilesasa'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="table-container">
                <?php if (!empty($scheduled_messages)) : ?>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Message Body', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Recipients', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Status', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Scheduled Time', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Actions', 'mobilesasa'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scheduled_messages as $message) : ?>
                        <tr data-message-id="<?php echo esc_attr($message['id']); ?>">
                            <td><?php echo esc_html($message['id']); ?></td>
                            <td><?php echo esc_html($message['message']); ?></td>
                            <td>
                                <span class="show-more" data-message-id="<?php echo $message['id']; ?>">
                                    <?php esc_html_e('Show Recipients', 'mobilesasa'); ?>
                                </span>
                                <div id="recipients-<?php echo $message['id']; ?>" style="display: none;">
                                    <?php echo esc_html($message['recipients']); ?>
                                </div>
                            </td>
                            <td><?php echo esc_html($message['status']); ?></td>
                            <td><?php echo esc_html($message['schedule_time']); ?></td>
                            <td>
                                <button type="button" class="button button-primary delete-btn"
                                    data-message-id="<?php echo esc_attr($message['id']); ?>">
                                    <?php esc_html_e('Delete', 'mobilesasa'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p><b><?php esc_html_e('No messages found.', 'mobilesasa'); ?></b></p>
                <?php endif; ?>
            </div>
            <!-- Modal for Scheduled Messages -->
            <div id="recipientsModalScheduled" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <table class="recipients-table custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php esc_html_e('Phone Number', 'mobilesasa'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="recipientListScheduled"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="tab-2" class="tab-pane">
            <div class="table-container">
                <?php if (!empty($sent_messages)) : ?>
                <table class="custom-table striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('ID', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Sent At', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Message Body', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Recipients', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Status', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Delivered Count', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Actions', 'mobilesasa'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sent_messages as $message) : ?>
                        <tr data-message-id="<?php echo esc_attr($message['message_id']); ?>">
                            <td><?php echo esc_html($message['id']); ?></td>
                            <td><?php echo esc_html($message['sent_at']); ?></td>
                            <td><?php echo esc_html($message['message_body']); ?></td>
                            <td>
                                <span class="show-more" data-message-id="<?php echo $message['id']; ?>">
                                    <?php esc_html_e('Show Recipients', 'mobilesasa'); ?>
                                </span>
                                <div id="recipients-<?php echo $message['id']; ?>-sent" style="display: none;">
                                    <?php echo esc_html($message['recipients']); ?>
                                </div>
                            </td>
                            <td><?php echo esc_html($message['status']); ?></td>
                            <td><?php echo esc_html($message['delivered_count']); ?></td>
                            <td>
                                <button type="button" class="button button-primary delivery-status-btn">
                                    <?php esc_html_e('Check Status', 'mobilesasa'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <p><b><?php esc_html_e('No messages found.', 'mobilesasa'); ?></b></p>
                <?php endif; ?>
            </div>
            <!-- Modal for Sent Messages -->
            <div id="recipientsModalSent" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <table class="recipients-table custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php esc_html_e('Phone Number', 'mobilesasa'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="recipientListSent"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>