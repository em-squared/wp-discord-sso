<?php
if (!defined('ABSPATH')) {
    exit;
}

class DiscordOAuth {
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $guild_id;

    public function __construct() {
        $this->client_id = get_option('discord_client_id');
        $this->client_secret = get_option('discord_client_secret');
        $this->guild_id = get_option('discord_guild_id');
        $this->redirect_uri = get_option('discord_redirect_uri'); // URL de redirection après login
    }

    // Générer l'URL de login Discord
    public static function get_discord_login_url() {
        $client_id = get_option('discord_client_id');
        $redirect_uri = get_option('discord_redirect_uri');
        return "https://discord.com/api/oauth2/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&response_type=code&scope=identify%20email%20guilds";
    }

    // Gérer le callback OAuth2 de Discord
    public function handle_discord_callback() {
        if (!isset($_GET['code'])) {
            return;
        }

        $code = $_GET['code'];
        $token_response = wp_remote_post('https://discord.com/api/oauth2/token', array(
            'body' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirect_uri,
            ),
        ));

        $body = json_decode(wp_remote_retrieve_body($token_response), true);
        if (isset($body['access_token'])) {
            $access_token = $body['access_token'];
            $this->process_discord_user($access_token);
        }
    }

    // Récupérer les données utilisateur et traiter la connexion/synchronisation
    private function process_discord_user($access_token) {
        $user_response = wp_remote_get('https://discord.com/api/users/@me', array(
            'headers' => array('Authorization' => "Bearer $access_token"),
        ));

        $user = json_decode(wp_remote_retrieve_body($user_response), true);
        if (isset($user['email'])) {
            $this->login_or_register_user($user, $access_token);
        }
    }

    // Login ou enregistrement d'un utilisateur
    private function login_or_register_user($discord_user, $access_token) {
        $email = $discord_user['email'];
        $username = $discord_user['username'];

        $user = get_user_by('email', $email);

        if (!$user) {
            // Créer un nouvel utilisateur si inexistant
            $random_password = wp_generate_password();
            $user_id = wp_create_user($username, $random_password, $email);
            $user = get_user_by('id', $user_id);
        }

        // Connecter l'utilisateur
        wp_set_auth_cookie($user->ID);
        // Notification
        if (function_exists('wp_add_notification')) {
            wp_add_notification( 'Vous êtes connecté(e) !', array( 'user_id' => $user->ID, 'fadeout' => 'never', 'delete_after_read' => true ) );
        }
        wp_redirect(home_url());
        exit;
    }
}
