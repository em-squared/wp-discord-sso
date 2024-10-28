<?php
/*
Plugin Name: Discord SSO Plugin
Description: Permet la connexion via Discord OAuth2 avec synchronisation des rôles.
Version: 1.0
Author: Maxime Moraine
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Inclure les fichiers des classes
require_once plugin_dir_path(__FILE__) . 'includes/class-discord-sso-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-discord-oauth.php';

// Initialisation du plugin
class DiscordSSOPlugin {
    public function __construct() {
        // Initialiser les hooks
        add_action('admin_menu', array($this, 'create_admin_menu'));
        add_action('init', array($this, 'init_oauth'));
        add_action('login_form', array($this, 'add_discord_login_button'));
    }

    // Créer le menu d'administration
    public function create_admin_menu() {
        DiscordSSOAdmin::add_plugin_admin_menu();
    }

    // Initialiser OAuth2
    public function init_oauth() {
        if (isset($_GET['code'])) {
            $oauth = new DiscordOAuth();
            $oauth->handle_discord_callback();
        }
    }

    // Ajouter le bouton de connexion Discord sur la page de connexion
    public function add_discord_login_button() {
        $login_url = DiscordOAuth::get_discord_login_url();
        echo '<a href="' . $login_url . '" class="button button-primary">Login with Discord</a>';
    }
}

// Démarrer le plugin
$discordSSOPlugin = new DiscordSSOPlugin();
