<?php
// Check if the user has submitted the settings
$is_update = isset($_GET['settings-updated']);
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab-1'; // Default to tab-1

// Determine the message based on the update status and current tab
if ($is_update) {
    switch ($current_tab) {
        case 'tab-1':
            $message = __('Settings Updated', 'mobilesasa');
            break;
        case 'tab-2':
            $message = __('Settings Updated', 'mobilesasa');
            break;
        case 'tab-3':
            $message = __('Updated', 'mobilesasa');
            break;
        default:
            $message = __('Settings Saved', 'mobilesasa');
            break;
    }
    // Add a settings updated message with the class of "updated"
    add_settings_error('mobilesasa_messages', 'ms_message', $message, 'updated');
}

function get_all_customers() {
    // Fetch all orders
    $orders = wc_get_orders([
        'limit' => -1,
        'status' => 'all'
    ]);

    $customers = [];

    // Loop through each order
    foreach ($orders as $order) {
        // Ensure the order is not a refund
        if ($order instanceof WC_Order_Refund) {
            continue;
        }

        $phone = $order->get_billing_phone();

        // Skip customers without a phone number
        if (empty($phone)) {
            continue;
        }

        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $email = $order->get_billing_email();
        $order_total = $order->get_total();

        // Check if customer already exists
        if (isset($customers[$phone])) {
            // Increment the order count and add to the total amount spent
            $customers[$phone]['orders_count']++;
            $customers[$phone]['total_spent'] += $order_total;
        } else {
            // Initialize a new customer entry
            $customers[$phone] = [
                'name'        => $name,
                'phone'       => $phone,
                'email'       => $email,
                'orders_count'=> 1,
                'total_spent' => $order_total
            ];
        }
    }

    return array_values($customers);
}

$customers = get_all_customers();

// Check if the message was sent successfully
$message_empty = get_transient('wcbulksms_message_empty');
$message_sent = get_transient('wcbulksms_message_sent');
$scheduled_messages = get_transient('wcbulksms_message_scheduled');

if($message_empty){
    delete_transient('wcbulksms_message_empty');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><b><?php esc_html_e('Please provide a message to send.', 'mobilesasa'); ?></b></p>
</div>
<?php
}elseif ($message_sent) {
    delete_transient('wcbulksms_message_sent');
    ?>
<div class="notice notice-mobilesasa notice-success is-dismissible">
    <p><b><?php esc_html_e('SMS messages sent successfully.', 'mobilesasa'); ?></b></p>
</div>
<?php
} elseif ($scheduled_messages) {
    delete_transient('wcbulksms_message_scheduled');
    ?>
<div class="notice notice-mobilesasa notice-info is-dismissible">
    <p><b><?php esc_html_e('SMS messages scheduled for sending.', 'mobilesasa'); ?></b></p>
</div>
<?php
}
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab-1'; // Initialize $current_tab variable
?>



<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('mobilesasa_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="<?php echo ($current_tab === 'tab-1') ? 'active' : ''; ?>">
            <a href="#tab-1" id="tab-link-1">Bulk SMS</a>
        </li>
        <li class="<?php echo ($current_tab === 'tab-2') ? 'active' : ''; ?>">
            <a href="#tab-2" id="tab-link-2">Transactional SMS</a>
        </li>
        <li class="<?php echo ($current_tab === 'tab-3') ? 'active' : ''; ?>">
            <a href="#tab-3" id="tab-link-3">OTP Login</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane <?php echo $current_tab === 'tab-1' ? 'active' : ''; ?>">
            <form action="<?php echo admin_url('options.php'); ?>?tab=tab-1" method="post">
                <?php
                // Output nonce, action, and option_page fields for a settings page
                settings_fields('mobilesasa_bulk_group');

                // Output sections and fields for a settings page
                do_settings_sections('mobilesasa_bulk_settings');

                // Output Save Settings button
                submit_button('Save Settings');
                ?>
            </form>
            <hr>

            <h3><?php esc_html_e('Available Customers', 'mobilesasa'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="send_bulk_sms">
                <?php wp_nonce_field('send_bulk_sms_nonce'); ?>
                <table class="wp-list-table widefat fixed striped custom-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Phone', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Email', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Orders Placed', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Amount Spent', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Send SMS', 'mobilesasa'); ?></th>
                        </tr>
                    </thead>
                    <tbody style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td><?php echo esc_html($customer['name']); ?></td>
                            <td><?php echo esc_html($customer['phone']); ?></td>
                            <td><?php echo esc_html($customer['email']); ?></td>
                            <td><?php echo esc_html($customer['orders_count']); ?></td>
                            <td><?php echo wc_price($customer['total_spent']); ?></td>
                            <td>
                                <input type="checkbox" name="send_sms[]"
                                    value="<?php echo esc_attr($customer['phone']); ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <br>
                <label><input type="checkbox" id="select_all">
                    <?php esc_html_e('Select All Customers', 'mobilesasa'); ?>
                </label>
                <br><br>
                <label class="switch">
                    <input type="checkbox" id="schedule_sms_toggle" name="schedule_sms">
                    <span class="slider round"></span>
                </label>
                <?php esc_html_e('Schedule SMS to be sent later', 'mobilesasa'); ?>
                <br><br>
                <label for="schedule_date" id="schedule_date_label" style="display: none;">
                    <?php esc_html_e('Schedule SMS for', 'mobilesasa'); ?>:
                </label>
                <input type="datetime-local" id="schedule_date" name="schedule_date"
                    min="<?php echo date('Y-m-d\TH:i'); ?>" style="display: none;">
                <br><br>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Send SMS', 'mobilesasa'); ?>
                </button>
                <br><br>
            </form>

        </div>
        <div id="tab-2" class="tab-pane <?php echo $current_tab === 'tab-2' ? 'active' : ''; ?>">
            <form action="<?php echo admin_url('options.php'); ?>?tab=tab-2" method="post">
                <?php
                // Output nonce, action, and option_page fields for a settings page
                settings_fields('mobilesasa_transactional_group');

                // Output sections and fields for a settings page
                do_settings_sections('mobilesasa_transactional_settings');

                // Output Save Settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <div id="tab-3" class="tab-pane <?php echo $current_tab === 'tab-3' ? 'active' : ''; ?>">
            <h3>OTP Login</h3>
            <p><b>Coming Soon!</b></p>

        </div>

    </div>



    <div class="gat-footer">
        <hr>
        <div>
            <p>Copyright &copy; <?php echo date('Y'); ?> <a class="" target="_blank"
                    href="https://mobilesasa.com">Mobile Sasa</a></p>
        </div>
    </div>

</div>