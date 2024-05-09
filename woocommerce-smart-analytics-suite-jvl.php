<?php
/**
 * Plugin Name: WooCommerce Smart Analytics Suite JVL
 * Plugin URI: https://github.com/javiervilchezl/woocommerce-smart-analytics-suite-jvl
 * Description: An advanced analysis plugin for WooCommerce, to know the values ​​of important KPIs, track them and compare them with other dates.
 * Version: 1.1
 * Requires at least: 5.8
 * Requires PHP: 5.6
 * Author: Javier Vílchez Luque
 * Author URI: https://github.com/javiervilchezl
 * Licence: License MIT
 *
 * Copyright 2023-2024 WooCommerce Smart Analytics Suite JVL - Javier Vílchez Luque (javiervilchezl)
 */

defined( 'ABSPATH' ) or die( '¡Sin acceso directo, por favor!' );

require_once plugin_dir_path(__FILE__) . 'classes/AnalyticsDataManager.php';
require_once plugin_dir_path(__FILE__) . 'classes/WooCommerceAnalytics.php';


function wcasjvl_admin_menu() {
    add_menu_page(
        'WooCommerce Smart Analytics Suite JVL',
        'Smart Analytics',
        'manage_options',
        'wcasjvl_main',
        'wcasjvl_main_page',
        'dashicons-chart-area'
    );
    // Submenú de seguimiento
    add_submenu_page(
        'wcasjvl_main',                          
        'Tracking Analytics',                     
        'Tracking',                               
        'manage_options',                         
        'wcasjvl_tracking',                       
        'wcasjvl_tracking_page'                   
    );

    // Submenú de análisis
    add_submenu_page(
        'wcasjvl_main',                           
        'Detailed Analysis',                      
        'Analysis',                               
        'manage_options',                         
        'wcasjvl_analysis',                       
        'wcasjvl_analysis_page'                   
    );
}
add_action( 'admin_menu', 'wcasjvl_admin_menu' );

function wcasjvl_main_page() {
    include_once('admin/pages/main-page.php');
}
function wcasjvl_tracking_page() {
    include_once('admin/pages/tracking-page.php');
}

function wcasjvl_analysis_page() {
    include_once('admin/pages/analysis-page.php');
}

function wcasjvl_enqueue_scripts() {
    if (isset($_GET['page']) && (
        $_GET['page'] == 'wcasjvl_main' ||
        $_GET['page'] == 'wcasjvl_tracking' ||
        $_GET['page'] == 'wcasjvl_analysis'
    )) {
        wp_enqueue_script('chartjs', plugins_url('/js/chart.js', __FILE__), array(), '2.9.4', false);
        wp_enqueue_style('stylecsswsasj', plugins_url('/css/wsasj-style.css', __FILE__), array(), false);
        wp_enqueue_script('charttrendlinejs', plugins_url('/js/chartjs-plugin-trendline.js', __FILE__), array(), '2.9.4', false);
    }
    if (isset($_GET['page']) && ($_GET['page'] == 'wcasjvl_tracking')) {
        wp_enqueue_script('validatejs', plugins_url('/js/validate.js', __FILE__), array(), '1.1', false);
    }
    
}

add_action('admin_enqueue_scripts', 'wcasjvl_enqueue_scripts');

function wcasjvl_create_cart_starts_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcasjvl_cart_starts';  

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        start_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    wcasjvl_insert_existing_orders_as_carts(); 
}

// Hook this function to your plugin activation or theme setup
register_activation_hook(__FILE__, 'wcasjvl_create_cart_starts_table');


function wcasjvl_generate_negative_user_id($order_id) {

    return -1 * (1000000 + $order_id); 
}

function wcasjvl_insert_existing_orders_as_carts() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcasjvl_cart_starts';


    $orders = wc_get_orders(array('limit' => -1, 'return' => 'ids'));

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        if (!$order) continue; 

        $start_time = $order->get_date_created()->date('Y-m-d H:i:s');
        $user_id = $order->get_user_id() ? $order->get_user_id() : wcasjvl_generate_negative_user_id($order_id);

   
        $existing_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %s AND start_time = %s",
            $user_id,
            $start_time
        ));

        if ($existing_count == 0) {
           
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'start_time' => $start_time
                ),
                array('%s', '%s') 
            );
        }
    }
}



function wcasjvl_increment_cart_counter() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcasjvl_cart_starts';


    $user_id = get_current_user_id() ?: wcasjvl_generate_unique_id();


    if (!WC()->session->get('wcasjvl_cart_started_' . $user_id)) {
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'start_time' => current_time('mysql', 1)
            ),
            array('%d', '%s')
        );

        if ($inserted) {
           
            WC()->session->set('wcasjvl_cart_started_' . $user_id, true);
        } else {

            error_log('Failed to insert new cart start entry.');
        }
    }
}
add_action('woocommerce_add_to_cart', 'wcasjvl_increment_cart_counter');


function wcasjvl_generate_unique_id() {
    
    if (!session_id()) {
        session_start();
    }


    if (empty($_SESSION['guest_user_id'])) {
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        $uniq_id = uniqid($remote_ip, true); 
        $hash = md5($uniq_id); 
        $hashed_id = hexdec(substr($hash, 0, 15)); 
        $negative_id = -abs((int)$hashed_id); 
        $_SESSION['guest_user_id'] = $negative_id;
    }

    return $_SESSION['guest_user_id'];
}


function wcasjvl_plugin_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wcasjvl_cart_starts';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

register_uninstall_hook(__FILE__, 'wcasjvl_plugin_uninstall');


?>