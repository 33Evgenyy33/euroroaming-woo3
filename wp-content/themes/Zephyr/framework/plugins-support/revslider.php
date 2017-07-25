<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Revolution Slider Support
 *
 * @link http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380?ref=UpSolution
 */

if ( ! class_exists( 'RevSliderFront' ) ) {
	return;
}

if ( function_exists( 'set_revslider_as_theme' ) ) {
	if ( ! defined( 'REV_SLIDER_AS_THEME' ) ) {
		define( 'REV_SLIDER_AS_THEME', TRUE );
	}
	set_revslider_as_theme();
}
// Actually the revslider's code above doesn't work as expected, so turning off the notifications manually
if ( get_option( 'revslider-valid-notice', 'true' ) != 'false' ) {
	update_option( 'revslider-valid-notice', 'false' );
}
if ( get_option( 'revslider-notices', array() ) != array() ) {
	update_option( 'revslider-notices', array() );
}
// Remove plugins page notices
if ( $pagenow == 'plugins.php' ) {
	remove_action( 'admin_notices', array( 'RevSliderAdmin', 'add_plugins_page_notices' ) );
}

// Move js for Admin Bar lower so it is not echoed before jquery core in footer
add_action( 'wp_enqueue_scripts', 'us_move_revslider_js_footer' );
function us_move_revslider_js_footer() {
	remove_action( 'wp_footer', array( 'RevSliderFront', 'putAdminBarMenus' ) );
	add_action( 'wp_footer', array( 'RevSliderFront', 'putAdminBarMenus' ), 99 );
}
