<?php
/*
Plugin Name: Crafty Kits
Description: Plugin will make some customizations for crafty-kits.com
Version: 1.0.0
Author: Muhammad Atiq
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

define( 'CRAFTYKITS_PLUGIN_NAME', 'Crafty Kits' );
define( 'CRAFTYKITS_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'CRAFTYKITS_PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'CRAFTYKITS_PAYPAL_LOADER_PATH', CRAFTYKITS_PLUGIN_PATH.'includes/PayPal-PHP-SDK/' );
define( 'CRAFTYKITS_SITE_BASE_URL',get_bloginfo('url'));

require_once CRAFTYKITS_PLUGIN_PATH.'includes/craftykits_class.php';

register_activation_hook( __FILE__, array( 'CRAFTYKITS', 'craftykits_install' ) );
register_deactivation_hook( __FILE__, array( 'CRAFTYKITS', 'craftykits_uninstall' ) );