<?php
/*
Plugin Name: 		Admin Columns - WooCommerce add-on
Version: 			2.1.3
Description: 		Enhance your product and order overviews with new columns, and edit products directly from the overview page. WooCommerce integration Add-on for Admin Columns Pro.
Author: 			Codepress
Author URI: 		https://admincolumns.com
Text Domain: 		codepress-admin-columns
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ACA_WC_FILE', __FILE__ );

final class ACA_WC_Loader {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'init' ), 9 );
	}

	public function init() {
		$version = $this->is_woocommerce_version_gte( '3.0.0' ) ? 3 : 2;

		require_once plugin_dir_path( ACA_WC_FILE ) . sprintf( 'v%s/wc.php', $version );
	}

	/**
	 * Returns true if the installed version of WooCommerce is $version or greater
	 *
	 * @return boolean
	 */
	function is_woocommerce_version_gte( $version ) {
		if ( ! defined( 'WC_VERSION' ) || ! WC_VERSION ) {
			return false;
		}

		return version_compare( WC_VERSION, $version, '>=' );
	}

}

new ACA_WC_Loader;