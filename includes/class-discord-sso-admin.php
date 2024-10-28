<?php
if (!defined('ABSPATH')) {
    exit;
}

class DiscordSSOAdmin {
    public static function add_plugin_admin_menu() {
        add_options_page(
            'Discord SSO Settings', 
            'Discord SSO', 
            'manage_options', 
            'discord-sso-settings', 
            array('DiscordSSOAdmin', 'render_settings_page')
        );
    }

    public static function render_settings_page() {
        // Sauvegarder les paramètres si le formulaire est soumis
        if (isset($_POST['discord_sso_save'])) {
            update_option('discord_client_id', sanitize_text_field($_POST['discord_client_id']));
            update_option('discord_client_secret', sanitize_text_field($_POST['discord_client_secret']));
            update_option('discord_guild_id', sanitize_text_field($_POST['discord_guild_id']));
            update_option('discord_redirect_uri', sanitize_text_field($_POST['discord_redirect_uri']));
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }

        // Récupérer les paramètres actuels
        $client_id = get_option('discord_client_id');
        $client_secret = get_option('discord_client_secret');
        $guild_id = get_option('discord_guild_id');
        $redirect_uri = get_option('discord_redirect_uri');

        // Afficher le formulaire d'administration
        ?>
        <div class="wrap">
            <h2>Discord SSO Settings</h2>
            <form method="post" action="">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Discord Client ID</th>
                        <td><input type="text" name="discord_client_id" value="<?php echo esc_attr($client_id); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Discord Client Secret</th>
                        <td><input type="text" name="discord_client_secret" value="<?php echo esc_attr($client_secret); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Discord Guild ID</th>
                        <td><input type="text" name="discord_guild_id" value="<?php echo esc_attr($guild_id); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Redirect URI</th>
                        <td><input type="text" name="discord_redirect_uri" value="<?php echo esc_attr($redirect_uri); ?>" /></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="discord_sso_save" class="button-primary" value="Save Settings" />
                </p>
            </form>
        </div>
        <?php
    }
}
