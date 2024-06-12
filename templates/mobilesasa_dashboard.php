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
$credentials_saved = get_transient('wc_credentials_saved');
$message_sent = get_transient('mobilesasa_balance_response');
$bulk_sender_id_empty = get_transient('wc_mobilesasa_bulk_sender_id_empty');
$transactional_sender_id_empty = get_transient('wc_mobilesasa_transactional_sender_id_empty');
$token_empty = get_transient('wc_mobilesasa_token_empty');
$bulk_sender_error = get_transient('wc_mobilesasa_bulk_sender_id_error');
$transactional_sender_error = get_transient('wc_mobilesasa_transactional_sender_id_error');
$bulk_sender_type = get_transient('wc_mobilesasa_bulk_sender_id_incorrect_type');
$transactional_sender_type = get_transient('wc_mobilesasa_transactional_sender_id_incorrect_type');


if ($message_sent) {
    delete_transient('mobilesasa_balance_response');
?>
<div class="notice notice-mobilesasa notice-success is-dismissible">
    <p><strong><?php esc_html_e('Balance retrieved successfully.', 'mobilesasa'); ?></strong></p>
</div>
<?php
} elseif($bulk_sender_id_empty){
    delete_transient('wc_mobilesasa_bulk_sender_id_empty');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter your Bulk MOBILESASA Sender ID.', 'mobilesasa'); ?></strong></p>
</div>
<?php
       
} elseif($bulk_sender_error){
    delete_transient('wc_mobilesasa_bulk_sender_id_error');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter a valid Bulk MOBILESASA Sender ID.', 'mobilesasa'); ?></strong></p>
</div>
<?php
       
} elseif($transactional_sender_id_empty){
    delete_transient('wc_mobilesasa_transactional_sender_id_empty');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter your Transactional MOBILESASA Sender ID.', 'mobilesasa'); ?></strong></p>
</div>
<?php
       
} elseif($transactional_sender_error){
    delete_transient('wc_mobilesasa_transactional_sender_id_error');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter a valid Transactional MOBILESASA Sender ID.', 'mobilesasa'); ?></strong>
    </p>
</div>
<?php
       
} elseif($token_empty){
    delete_transient('wc_mobilesasa_token_empty');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter your MOBILESASA Api Token.', 'mobilesasa'); ?></strong></p>
</div>
<?php
       
} elseif($bulk_sender_type){
    delete_transient('wc_mobilesasa_bulk_sender_id_incorrect_type');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter a Promotional MOBILESASA Sender ID.', 'mobilesasa'); ?></strong></p>
</div>
<?php
       
} elseif($transactional_sender_type){
    delete_transient('wc_mobilesasa_transactional_sender_id_incorrect_type');
    ?>
<div class="notice notice-mobilesasa notice-error is-dismissible">
    <p><strong><?php esc_html_e('Please enter a Transactional MOBILESASA Sender ID.', 'mobilesasa'); ?></strong>
    </p>
</div>
<?php
       
} elseif ($credentials_saved) {
    delete_transient('wc_credentials_saved');
?>
<div class="notice notice-mobilesasa notice-success is-dismissible">
    <p><strong><?php esc_html_e('Credentials saved successfully.', 'mobilesasa'); ?></strong></p>
</div>
<?php
}

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'tab-1'; // Initialize $current_tab variable

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if ( ! get_option('hide-ga-welcome', false)) { ?>
    <div class="ga-welcome-panel welcome-panel">

        <div class="ga-welcome-panel-content">
            <h3><?php _e( "Thanks for installing MobileSasa SMS" ); ?></h3>

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
        <li class="active">
            <a href="#tab-1" id="tab-link-1">Manage Settings</a>
        </li>
        <li class="">
            <a href="#tab-2" id="tab-link-2">About</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="save_credentials">
                <?php wp_nonce_field('save_credentials_nonce'); 
                
                // Get options
                $mobilesasa_defaults = get_option('mobilesasa_defaults', array());

                // Retrieve Sender ID
                $bulk_sender_id = $mobilesasa_defaults['bulk_sender_id'] ?? false;
                // Retrieve Sender ID
                $transactional_sender_id = $mobilesasa_defaults['transactional_sender_id'] ?? false;
                // Retrieve Api Token
                $api_token = $mobilesasa_defaults['mobilesasa_token'] ?? false;
                // Retrieve Bulk Sender Type
                $bulk_sender_type = $mobilesasa_defaults['mobilesasa_bulk_sender_id_type'] ?? false;
                // Retrieve Transactional Sender Type
                $transactional_sender_type = $mobilesasa_defaults['mobilesasa_transactional_sender_id_type'] ?? false;
                

                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr class="example-text">
                            <th scope="row"><label for="bulk_sender_id">Bulk SMS Sender ID</label></th>
                            <td><input type="text" class="regular-text" name="bulk_sender_id"
                                    value="<?php esc_html_e($bulk_sender_id); ?>"
                                    placeholder="Enter Bulk Mobile Sasa Sender ID">
                                <?php
                                    if ($bulk_sender_type !== false) {
                                        echo '<div class="right">' . esc_html($bulk_sender_type) . '</div>';
                                    } else {
                                        echo '<div class="right">' . esc_html__('N/A', 'mobilesasa') . '</div>'; // Display default value if balance response is not available
                                    }
                                ?>
                                <p class="description">e.g BITWISE</p>
                            </td>
                        </tr>
                        <tr class="example-text">
                            <th scope="row"><label for="transactional_sender_id">Transactional SMS Sender ID</label>
                            </th>
                            <td><input type="text" class="regular-text" name="transactional_sender_id"
                                    value="<?php esc_html_e($transactional_sender_id); ?>"
                                    placeholder="Enter Transactional Mobile Sasa Sender ID">
                                <?php
                                    if ($transactional_sender_type !== false) {
                                        echo '<div class="right">' . esc_html($transactional_sender_type) . '</div>';
                                    } else {
                                        echo '<div class="right">' . esc_html__('N/A', 'mobilesasa') . '</div>'; // Display default value if balance response is not available
                                    }
                                ?>
                                <p class="description">e.g MOBILESASA</p>
                            </td>
                        </tr>
                        <tr class="example-text">
                            <th scope="row"><label for="mobilesasa_token">API Token</label></th>
                            <td><input type="text" class="regular-text" name="mobilesasa_token"
                                    value="<?php esc_html_e($api_token); ?>" placeholder="Enter Mobile Sasa API Token">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Save Credentials', 'mobilesasa'); ?>
                </button>
                <br><br>
            </form>
            <hr>
            <h3><?php esc_html_e('Get Balance', 'mobilesasa'); ?></h3>
            <div class="balance-container">
                <h3 class="balance-label"><?php esc_html_e('Balance:', 'mobilesasa'); ?></h3>
                <?php
                // Get options
                $mobilesasa_defaults = get_option('mobilesasa_defaults', array());

                // Retrieve balance
                $balance = $mobilesasa_defaults['mobilesasa_balance'] ?? false;

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

        <div id="tab-2" class="tab-pane">
            <h3>About</h3>
            <p><b>About the plugin</b></p>

            <p>
                The Mobile Sasa SMS Plugin is your gateway to seamless communication with your customers, enriching
                their shopping experience at every step. Specifically designed to integrate flawlessly with WooCommerce,
                this plugin harnesses the Mobile Sasa SMS API to deliver personalized and timely SMS notifications to
                both your customers and administrators.
            </p>
            <p><b>Stay Informed, Stay Engaged</b></p>
            <p>
                With the Mobile Sasa SMS Plugin, you ensure that your customers remain well-informed about the status of
                their orders. Whether it's a newly drafted order, one that's pending, or one that's on hold, the plugin
                keeps your customers updated throughout the entire order lifecycle. Receive notifications for various
                order events, such as order draft, processing, completion, failure, readiness for pickup, failed
                delivery, return, or refund. <br><br>

                This robust functionality not only keeps your customers in the loop but also empowers you as the store
                owner. Receive instant admin notifications whenever an order is placed or its status changes, enabling
                you to manage your business operations efficiently.
            </p>
            <p><b>Customizable and Efficient</b></p>
            <p>Tailor your SMS messages to reflect your brand's identity with the Mobile Sasa SMS Plugin's extensive
                customization options. Utilize dynamic shortcodes to automatically include pertinent order details like
                customer names, order IDs, total amounts, and phone numbers. This level of customization ensures that
                your SMS messages resonate with your brand's voice and messaging.<br><br>

                The plugin's seamless integration with the Mobile Sasa SMS API guarantees efficient and reliable SMS
                delivery. Rest assured that your messages will reach your customers and administrators promptly and
                without any disruptions.
            </p>
            <p><b>Modules Overview</b></p>
            <p>
                The Mobile Sasa SMS Plugin comprises three main modules:
            </p>
            <ol>
                <li>
                    <b>Bulk SMS:</b> Send personalized bulk SMS messages to your customers effortlessly.
                </li>
                <li>
                    <b>Transactional SMS:</b> Keep your customers informed about critical transactional updates,
                    enhancing
                    their experience.
                </li>
                <li>
                    <b>OTP Login (Coming Soon):</b> Simplify user authentication and security with easy-to-use OTP login
                    functionality.
                </li>
            </ol>
            <p>Whether you're managing a small business or a large enterprise, the Mobile Sasa SMS Plugin is an
                invaluable asset that fosters seamless communication with your customers, empowering you to make
                informed decisions about your business.
            </p>
            <p><b>Stay Connected, Stay Successful</b></p>
            <p>
                In addition to order notifications, the Mobile Sasa SMS Plugin simplifies sending personalized SMS
                messages to your customers and administrators. Configure your SMS credentials, input message content,
                and specify recipients – the plugin handles the rest.<br><br>

                For optimal performance, ensure that you've set up your credentials properly before attempting any
                transactions. For bulk messaging, remember to input and save your message before sending it out.
            </p>
            <p><b>Contact Developer</b></p>
            <p>
                For any queries, problems, plugin development inquiries, or other matters, please visit
                <a target="_blank" href="https://wilsondevops.com">Wilson Devops</a>
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