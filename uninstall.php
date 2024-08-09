<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/**
 * Deletes all custom meta fields added by the plugin.
 */
wc_rw_only_company_checkout_drop_plugin_added_meta();

/**
 * Function to delete all meta fields with the key '_only_company'.
 */
function wc_rw_only_company_checkout_drop_plugin_added_meta(){

    // Delete all product meta fields "_only_company"
    delete_post_meta_by_key('_only_company');


}