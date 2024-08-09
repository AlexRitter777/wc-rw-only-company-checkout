<?php
/**
Plugin Name:  WooCommerce RW Only Company Checkout
Description: Checks products in the cart, and if at least one product can only be sold to a company, changes the checkout form to a company checkout.
Version: 1.0.0
Author: Alexej Bogačev
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main class for the plugin.
 */
class Wc_Rw_Only_Company_Checkout
{

    public function __construct()
    {
        $this->register_hooks();
    }


    /**
     * Registers all necessary hooks for the plugin.
     */
    private function register_hooks()
    {
        // Load styles for the frontend
        add_action('wp_enqueue_scripts', [$this, 'load_user_styles']);

        // Load the text domain for translations
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Initialize the plugin's main functionality
        add_action('plugins_loaded', [$this, 'initialize_plugin']);

    }

    /**
     * Initialize the plugin.
     */
    public function initialize_plugin()
    {
        $this->load_classes();

        // Initialize the plugin's core functionality
        Wc_Rw_Only_Company_Checkout_Init::get_instance();
    }

    /**
     * Enqueue styles for the frontend.
     */
    public function load_user_styles() {
        if (!is_admin()) {
            wp_enqueue_style('wc-rw-only-company-user-style', plugin_dir_url(__FILE__) . 'assets/css/main.css', array(), '1.0.0');
        }
    }


    /**
     * Load all necessary classes.
     */
    private function load_classes()
    {
        require_once WP_PLUGIN_DIR . '/wc-rw-only-company-checkout/includes/class-wc-rw-only-company-checkout-init.php';
    }


    /**
     * Load the plugin text domain for translations
     */
    public function load_textdomain() {
        // Load the text domain from the /languages directory
        load_plugin_textdomain('wc-rw-only-company-checkout', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }


}


/**
 * Get the instance of the main plugin class.
 *
 * @return Wc_Rw_Only_Company_Checkout
 */
function wc_rw_only_company_checkout()
{
    static $instance;

    if ( ! isset( $instance ) ) {
        $instance = new Wc_Rw_Only_Company_Checkout();
    }

    return $instance;
}

/**
 * Start execution of the plugin.
 */
wc_rw_only_company_checkout();