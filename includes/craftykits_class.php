<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Plugin main class that will control the whole skeleton
 *
 * PHP version 5
 *
 * @category   Main
 * @package    Crafty Kits
 * @author     Muhammad Atiq
 * @version    1.0.0
 * @since      File available since Release 1.0.0
*/

class CRAFTYKITS
{
     
    //Plugin starting point. Will call appropriate actions
    public function __construct() {

        add_action( 'plugins_loaded', array( $this, 'craftykits_init' ) );
    }

    //Plugin initialization
    public function craftykits_init() {

        do_action('craftykits_before_init');

        wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'craftykits_css', CRAFTYKITS_PLUGIN_URL.'css/craftykits.css' );
        
        if(is_admin()){
            require_once CRAFTYKITS_PLUGIN_PATH.'craftykits_admin.php';            
        }else{
            wp_enqueue_script( 'jquery_validation_lang', CRAFTYKITS_PLUGIN_URL.'js/jquery.validationEngine-en.js', array( 'jquery' ) );
            wp_enqueue_script( 'dmuploader', CRAFTYKITS_PLUGIN_URL.'js/dmuploader.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'jquery_validation_js', CRAFTYKITS_PLUGIN_URL.'js/jquery.validationEngine.js', array( 'jquery_validation_lang' ) );
            wp_enqueue_script( 'craftykits_js', CRAFTYKITS_PLUGIN_URL.'js/craftykits.js', array( 'jquery_validation_js', 'dmuploader' ) );
            
            wp_enqueue_style( 'jquery_validation_css', CRAFTYKITS_PLUGIN_URL.'css/validationEngine.jquery.css', array( 'craftykits_css' ) );
        }
        
        require_once CRAFTYKITS_PLUGIN_PATH.'craftykits_front.php';
        
        do_action('craftykits_after_init');
    }
 
    //Function will get called on plugin activation
    static function craftykits_install() {

        do_action('craftykits_before_install');

        require_once CRAFTYKITS_PLUGIN_PATH.'includes/craftykits_install.php';

        do_action('craftykits_after_install');
    }

    // Function will get called on plugin de activation
    static function craftykits_uninstall() {

        do_action('craftykits_before_uninstall');

        require_once CRAFTYKITS_PLUGIN_PATH.'includes/craftykits_uninstall.php';

        do_action('craftykits_after_uninstall');
    }
}

$wptp = new CRAFTYKITS();