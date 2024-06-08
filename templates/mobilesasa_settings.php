<?php
// Check if the user has submitted the settings
// WordPress adds the "settings-updated" $_GET parameter to the URL
$is_update = isset($_GET['settings-updated']);

// Determine the message based on the update status
$message = $is_update ? __('Credentials Updated', 'mobilesasa') : __('Settings Saved', 'mobilesasa');

if ($is_update) {
    // Add a settings updated message with the class of "updated"
    add_settings_error('mobilesasa_messages', 'ga_message', $message, 'updated');
}

// Check if the message was sent successfully
$message_sent = get_transient('mobilesasa_balance_response');
if ($message_sent) {
    delete_transient('mobilesasa_balance_response');
?>
<div class="notice notice-mobilesasa notice-success is-dismissible">
    <p><strong><?php esc_html_e('Balance retrieved successfully.', 'mobilesasa'); ?></strong></p>
</div>
<?php
}

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab-1'; // Initialize $current_tab variable

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if (!get_option('hide-ga-welcome', false)) { ?>
    <div class="ga-welcome-panel welcome-panel">

        <div class="ga-welcome-panel-content">
            <h3><?php _e("Thanks for installing MobileSasa SMS"); ?></h3>

            <p class="about-description">Here's how to get started:</p>
            <div class="ga-welcome-panel-column-container">
                <div class="ga-welcome-panel-column">
                    <h4>Actions</h4>
                    <ul>
                        <li class="welcome-icon welcome-edit-page">
                            <?php
                        $mobilesasa_credentials_url = esc_url(add_query_arg('page', 'mobilesasa-sms', admin_url('admin.php')));
                        printf(
                            __('Add your <a target="_blank" href="https://sms.mobilesasa.com/docs/">MOBILESASA</a> credentials', 'mobilesasa'),
                            $mobilesasa_credentials_url
                        );
                        ?>
                        </li>

                        <li>
                            <?php
                            $bulk_sms_url = esc_url(add_query_arg('page', 'mobilesasa-settings', admin_url('admin.php')));
                            printf('<a href="%s" class="welcome-icon welcome-add-page %s">%s</a>', $bulk_sms_url, ($current_tab === 'tab-1') ? 'active' : '', esc_html__('Bulk SMS Settings', 'mobilesasa'));
                            ?>
                        </li>
                        <li>
                            <?php
                            $transactional_sms_url = esc_url(add_query_arg(array(
                                'page' => 'mobilesasa-settings',
                                'tab' => 'tab-2'
                            ), admin_url('admin.php')));
                            printf('<a href="%s" class="welcome-icon welcome-add-page %s">%s</a>', $transactional_sms_url, ($current_tab === 'tab-2') ? 'active' : '', esc_html__('Transactional SMS Settings', 'mobilesasa'));
                            ?>
                        </li>
                        <li>
                            <?php
                            $otp_login_url = esc_url(add_query_arg(array(
                                'page' => 'mobilesasa-settings',
                                'tab' => 'tab-3'
                            ), admin_url('admin.php')));
                            printf('<a href="%s" class="welcome-icon welcome-add-page %s">%s</a>', $otp_login_url, ($current_tab === 'tab-3') ? 'active' : '', esc_html__('OTP Login Settings', 'mobilesasa'));
                            ?>
                        </li>


                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php settings_errors('mobilesasa_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="<?php echo ($current_tab === 'tab-1') ? 'active' : ''; ?>">
            <a href="#tab-1" id="tab-link-1">Manage Settings</a>
        </li>
        <li class="<?php echo ($current_tab === 'tab-2') ? 'active' : ''; ?>">
            <a href="#tab-2" id="tab-link-2">About</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane <?php echo ($current_tab === 'tab-1') ? 'active' : ''; ?>">
            <form action="<?php echo admin_url(); ?>options.php" method="post">
                <?php
                // Output nonce, action, and option_page fields for a settings page
                settings_fields('mobilesasa_admin_group');

                // Output sections and fields for a settings page
                do_settings_sections('mobilesasa-sms');

                // Output Save Settings button
                submit_button('Save Credentials');
                ?>
            </form>
            <hr>
            <h3><?php esc_html_e('Get Balance', 'mobilesasa'); ?></h3>
            <div class="balance-container">
                <h3 class="balance-label"><?php esc_html_e('Balance:', 'mobilesasa'); ?></h3>
                <?php
                // Get options
                $mobilesasa_defaults = get_option('mobilesasa_defaults', array());

                // Retrieve balance
                $balance = $mobilesasa_defaults['balance'] ?? false;

                if ($balance !== false) {
                    // Format the balance value with commas and maintain the decimals
                    $formatted_balance = number_format($balance, 2, '.', ',');
                    echo '<div class="balance-value">' . esc_html($formatted_balance) . '</div>';
                } else {
                    echo '<div class="balance-value">' . esc_html__('N/A', 'mobilesasa') . '</div>'; // Display default value if balance response is not available
                }
                ?>
            </div>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="get_balance">
                <?php wp_nonce_field('get_balance_nonce'); ?>

                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Get Balance', 'mobilesasa'); ?>
                </button>
                <br><br>
            </form>
        </div>

        <div id="tab-2" class="tab-pane <?php echo ($current_tab === 'tab-2') ? 'active' : ''; ?>">
            <h3>About</h3>
            <p><b>About the plugin</b></p>

            <p>
                The Mobile Sasa SMS Plugin is a powerful tool that helps you stay connected with your customers
                throughout their shopping journey. Built to seamlessly integrate with WooCommerce, this plugin leverages
                the Mobile Sasa SMS API to deliver timely and personalized SMS notifications to your customers and
                admins.
            </p>
            <p><b>Stay Informed, Stay Engaged</b></p>
            <p>
                With the Mobile Sasa SMS Plugin, you can ensure that your customers are always up-to-date with the
                status of their orders. From the moment an order is placed to the moment it's delivered (and even
                beyond), this plugin keeps your customers informed every step of the way. Receive notifications for
                various order events, including:
            </p>
            <ul>
                <li> - Order Draft</li>
                <li> - Order Pending</li>
                <li> - Order On Hold</li>
                <li> - Order Processing</li>
                <li> - Order Completed</li>
                <li> - Order Failed</li>
                <li> - Order Ready for Pickup</li>
                <li> - Order Failed Delivery</li>
                <li> - Order Returned</li>
                <li> - Order Refunded</li>
            </ul>
            <p>Not only does this plugin keep your customers in the loop, but it also keeps you, the store owner,
                informed. Receive admin notifications whenever an order is placed or its status changes, allowing you to
                stay on top of your business operations.
            </p>
            <p><b>Customizable and Efficient</b></p>
            <p>The Mobile Sasa SMS Plugin offers a high degree of customization, allowing you to tailor the SMS messages
                to suit your brand's voice and messaging. Easily customize the messages with dynamic shortcodes that
                automatically populate order details, such as customer name, order ID, total amount, and phone
                number.<br><br>
                Additionally, the plugin's seamless integration with the Mobile Sasa SMS API ensures efficient and
                reliable SMS delivery, ensuring that your messages reach your customers and admins without any hiccups.
            </p>
            <p><b>Stay Connected, Stay Successful</b></p>
            <p>
                This plugin also provides a simple way to send SMS messages to your customers and admins. Simply
                configure your SMS credentials and enter the message content and recipients, and the plugin will
                handle the rest.
            </p>
            <p>
                Whether you're a small business or a large enterprise, the Mobile Sasa SMS Plugin is a powerful tool
                that can help you stay connected with your customers and make informed decisions about your business.
            </p>
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