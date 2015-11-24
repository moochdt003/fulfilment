<?php

/*
  Plugin Name: fulfilment tool MOA
  Plugin URI: http://www.moadev.jpdev.co.za/wp-content/plugin/fulfilment/
  Description: Plugin for fulfilment process on ECommerce site
  Author: D. Muchatibaya
  Version: 1.0
  Author URI: http://www.moadev.jpdev.co.za
 */

defined('ABSPATH') or die('Plugin file cannot be accessed directly.');


if (!class_exists('fulfilment')) {

    class Fulfilment {

        public function __construct() {
            /* */
        }

    }

    new Fulfilment;
}


//All files includes
include_once('FulfilmentObj.php');
include_once('createFulfilmentDb.php');
include_once('fulfilment_hs_code.php');
include_once('product_custom_fields.php');
include_once('create_tracking_page.php');
//include_once('order_info.php');

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'my_plugin_install');
/* Runs on plugin deactivation */
register_deactivation_hook(__FILE__, 'my_plugin_remove');

//Functions responsible for installing and initalising plugin in setup
function my_plugin_install() {
    $the_page_title = 'Mall of America - Tracking';
    $the_page_name = 'Mall-of-America - Tracking';

    // the menu entry...
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title($the_page_title);

    if (!$the_page) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] ='<h1>Track your order here:</h1>'
                                . '<form method="POST">'
                                . '<p><input type="text" id="orderNo" name="orderNo" placeholder="Enter order number" /></p>'
                                . '<h3>Or</h3>'
                                . '<p><input type="text" id="trackNo" name="trackNo" placeholder="Enter tracking number" /></p>'
                                . '<p><input type="submit" id="submit" name="submit" value="Submit" /></p>'
                                . '</form>';
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'
        // Insert the post into the database
        $the_page_id = wp_insert_post($_p);
    } else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        wp_update_post($the_page);
    }

    delete_option('my_plugin_page_id');
    add_option('my_plugin_page_id', $the_page_id);

    fulfilment_setup_db();
    //moa_tracking_page();
}

function my_plugin_remove() {
    //  the id of our page...
    $the_page_id = get_option('my_plugin_page_id');
    if ($the_page_id) {

        wp_delete_post($the_page_id); // this will trash, not delete
    }

    delete_option("my_plugin_page_title");
    delete_option("my_plugin_page_name");
    delete_option("my_plugin_page_id");
}

// This filter adds button to wooCommerce orders page
add_filter('woocommerce_admin_order_actions', 'create_new_button_order_actions_fulfilment', 10, 2);

// This function creates the fulfilment button for the wooCommerce orders page
function create_new_button_order_actions_fulfilment($actions, $order) {
    $actions['fulfil'] = array(
        'url' => admin_url("?page=fulfilment-page-all&order=" . $order->id),
        'name' => 'fulfil'
    );
    return $actions;
}

//This filter adds a fulfilment administration page to the sites backend
add_action('admin_menu', 'wpdocs_register_my_custom_submenu_page');

// This function creates the fulfilment admin page
function wpdocs_register_my_custom_submenu_page() {
    add_submenu_page(
            null, 'Fulfillment Admin', 'Fulfillment Admin', 'manage_options', 'fulfilment-page-all', 'wpdocs_my_custom_submenu_page_callback');
}

// This function creates the fulfilment admin page
function wpdocs_my_custom_submenu_page_callback() {
    include_once("fulfillment_interface.php");
}


          if(isset($_POST['submit_to_elogix'])){
            
            
            
            
        }