<?php

/**

 * Plugin Name:       Woo Checkout for Digital Goods
 * Plugin URI:        http://www.multidots.com
 * Description:       This plugin will remove billing address fields for downloadable and virtual products.
 * Version:           1.9
 * Author:            Multidots
 * Author URI:        http://www.multidots.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-checkout-for-digital-goods
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-checkout-for-digital-goods-activator.php
 */
function activate_woo_checkout_for_digital_goods() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods-activator.php';
	Woo_Checkout_For_Digital_Goods_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-checkout-for-digital-goods-deactivator.php
 */
function deactivate_woo_checkout_for_digital_goods() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods-deactivator.php';
	Woo_Checkout_For_Digital_Goods_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_checkout_for_digital_goods' );
register_deactivation_hook( __FILE__, 'deactivate_woo_checkout_for_digital_goods' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-checkout-for-digital-goods.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_checkout_for_digital_goods() {

	$plugin = new Woo_Checkout_For_Digital_Goods();
	$plugin->run();

}
run_woo_checkout_for_digital_goods();
