<?php
// Check if the user has submitted the settings
// WordPress adds the "settings-updated" $_GET parameter to the URL

$is_update = isset($_GET['settings-updated']);

// Determine the message based on the update status
$message = $is_update ? __('Credentials Updated', 'mobilesasa') : __('Settings Saved', 'mobilesasa');

if ($is_update) {
    // Add a settings updated message with the class of "updated"
    add_settings_error('github_actions_messages', 'ga_message', $message, 'updated');
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if ( ! get_option('hide-ga-welcome', false)) { ?>
    <div class="ga-welcome-panel welcome-panel">
        <a href="<?php echo admin_url('admin.php?page=mobilesasa-sms&mobilesasa-sms-welcome=0')?>"
            class="ga-welcome-panel-close welcome-panel-close" aria-label="Dismiss the welcome panel">Dismiss</a>
        <div class="ga-welcome-panel-content">
            <h3><?php _e( "Thanks for installing MobileSasa SMS" ); ?></h3>

            <p class="about-description">Here's how to get started:</p>
            <div class="ga-welcome-panel-column-container">
                <div class="ga-welcome-panel-column">
                    <h4>Actions</h4>
                    <ul>
                        <li class="welcome-icon welcome-edit-page">
                            <?php
                        $github_credentials_url = esc_url(add_query_arg('page', 'mobilesasa-sms', admin_url('admin.php')));
                        printf(
                            __('Add your <a target="_blank" href="https://sms.mobilesasa.com/docs">MOBILESASA</a> credentials', 'mobilesasa'),
                            $github_credentials_url
                        );
                        ?>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php settings_errors('github_actions_messages'); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Manage Settings</a></li>
        <li class=""><a href="#tab-2">About</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
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
        </div>

        <div id="tab-2" class="tab-pane">
            <h3>About</h3>
            <p><b>About the plugin</b></p>

            <p>
                The GitHub token is required for accessing private repositories. If your GitHub repository is public,
                you do not need to provide a token. However, for private repositories, a GitHub access token with the
                'repo'
                scope is necessary for authentication.
            </p>

            <p>
                To set up webhooks (for automatic updating of your theme/plugin) and clone private repositories, ensure
                your GitHub token has the required
                permissions.
                You can manage these permissions in your GitHub repository settings.
                <a href="https://docs.github.com/en/developers/apps/building-oauth-apps/scopes-for-oauth-apps#available-scopes"
                    target="_blank" rel="noopener noreferrer">Learn more about GitHub scopes and permissions</a>.
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