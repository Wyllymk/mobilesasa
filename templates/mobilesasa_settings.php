<?php
// Check if the user has submitted the settings
// WordPress adds the "settings-updated" $_GET parameter to the URL

$is_update = isset($_GET['settings-updated']);

// Determine the message based on the update status
$message = $is_update ? __('Bulk SMS Settings Updated', 'mobilesasa') : __('Settings Saved', 'mobilesasa');

if ($is_update) {
    // Add a settings updated message with the class of "updated"
    add_settings_error('github_actions_messages', 'ga_message', $message, 'updated');
}

function get_all_customers() {
    $orders = wc_get_orders([
        'limit' => -1,
        'status' => 'all'
    ]);

    $customers = [];

    foreach ($orders as $order) {
        $phone = $order->get_billing_phone();
        $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $opt_in = get_post_meta($order->get_id(), '_sms_opt_in', true);
        if (!empty($phone) && !isset($customers[$phone])) {
            $customers[$phone] = [
                'name'   => $name,
                'phone'  => $phone,
                'opt_in' => $opt_in
            ];
        }
    }

    return array_values($customers);
}

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('github_actions_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Bulk SMS</a></li>
        <li class=""><a href="#tab-2">Transactional SMS</a></li>
        <li class=""><a href="#tab-3">OTP Login</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <form action="<?php echo admin_url(); ?>options.php" method="post">
                <?php
                // Output nonce, action, and option_page fields for a settings page
                settings_fields('mobilesasa_bulk_group');

                // Output sections and fields for a settings page
                do_settings_sections('mobilesasa-settings');

                // Output Save Settings button
                submit_button('Save Settings');
                ?>
            </form>
            <hr>
            <?php $customers = get_all_customers();?>

            <h3><?php esc_html_e('Available Customers', 'mobilesasa'); ?></h3>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="send_bulk_sms">
                <?php wp_nonce_field('send_bulk_sms_nonce'); ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Phone', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Opt-in', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Send SMS', 'mobilesasa'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td><?php echo esc_html($customer['name']); ?></td>
                            <td><?php echo esc_html($customer['phone']); ?></td>
                            <td>
                                <input type="checkbox" disabled <?php checked($customer['opt_in'], 'yes'); ?>>
                            </td>
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
                <textarea name="bulk_sms_message" rows="5"
                    style="width:100%;"><?php echo esc_textarea(get_option('mobilesasa_bulk_options')['bulk_message']); ?></textarea>
                <br><br>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Send SMS', 'mobilesasa'); ?>
                </button>
                <br><br>
            </form>
        </div>
        <div id="tab-2" class="tab-pane">
            <form action="<?php echo admin_url(); ?>options.php" method="post">
                <?php
                // Output nonce, action, and option_page fields for a settings page
                settings_fields('mobilesasa_bulk_group');

                // Output sections and fields for a settings page
                do_settings_sections('mobilesasa-settings');

                // Output Save Settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <div id="tab-3" class="tab-pane">
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