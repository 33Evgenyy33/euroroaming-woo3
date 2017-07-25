<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header Builder support
 *
 * Show alert message when plugin version is not compatible with the current theme version
 */

if ( defined( 'US_HB_VERSION' ) AND version_compare( US_HB_VERSION, 2, '<' ) ) {
	add_filter( 'us_config_theme-options', 'us_remove_old_ushb_options' );
	function us_remove_old_ushb_options( $config ) {
		if ( is_admin() ) {
			unset( $config['header'] );
		}
		return $config;
	}

	add_action( 'admin_notices', 'us_update_ushb_notice', 1 );

	function us_update_ushb_notice(){

		$output = '<div class="error us-migration">';
		$output .= '<h2>Update Header Builder</h2>';
		$output .= '<p>For correct work of your site header options you need to update "UpSolution Header Builder" to version 2.* on the <a href="' . admin_url( 'plugins.php' ) . '">Plugins page</a>.<br>If you don\'t see the update notification there, just delete Header Builder plugin and install it again via Addons page.</p>';
		$output .= '</div>';

		echo $output;

	}
}