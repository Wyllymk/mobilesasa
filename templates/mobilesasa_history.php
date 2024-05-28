<?php
// Check if the user has submitted the settings
// WordPress adds the "settings-updated" $_GET parameter to the URL

$is_update = isset($_GET['settings-updated']);

// Determine the message based on the update status
$message = $is_update ? __('Messages Updated', 'mobilesasa') : __('Settings Saved', 'mobilesasa');

if ($is_update) {
    // Add a settings updated message with the class of "updated"
    add_settings_error('mobilesasa_messages', 'ga_message', $message, 'updated');
}

?>



<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors('mobilesasa_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1" id="tab-link-1">History</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'mobilesasa'); ?></th>
                            <th><?php esc_html_e('Phone', 'mobilesasa'); ?></th>

                        </tr>
                    </thead>
                    <tbody style="max-height: 300px; overflow-y: auto;">
                        <tr>
                            <td><?php esc_html_e('Name', 'mobilesasa'); ?></td>
                            <td><?php esc_html_e('Name', 'mobilesasa'); ?></td>

                        </tr>
                    </tbody>
                </table>
                <br>

            </div>

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