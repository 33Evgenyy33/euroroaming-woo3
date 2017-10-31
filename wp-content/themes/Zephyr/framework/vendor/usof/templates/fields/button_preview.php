<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Button Preview
 *
 * Shows how buttons will look.
 *
 */

$prefixes = array( 'heading', 'body', 'menu' );
$font_families = array();
$default_font_weights = array_fill_keys( $prefixes, 400 );
foreach ( $prefixes as $prefix ) {
	$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
	if ( $font[0] == 'none' ) {
		// Use the default font
		$font_families[$prefix] = '';
	} elseif ( strpos( $font[0], ',' ) === FALSE ) {
		// Use some specific font from Google Fonts
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		// The first active font-weight will be used for "normal" weight
		$default_font_weights[$prefix] = intval( $font[1] );
		$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
		$font_families[$prefix] = 'font-family: "' . $font[0] . '", ' . $fallback_font_family . ";\n";
	} else {
		// Web-safe font combination
		$font_families[$prefix] = 'font-family: ' . $font[0] . ";\n";
	}
}

$output = '<div class="usof-button-preview hov_' . us_get_option( 'button_hover' ) . '">';
$output .= '<div class="usof-button-example style_solid">';
$output .= '<div class="usof-button-example-before"></div>';
$output .= '<span>' . __( 'Button Example', 'us' ) . '</span>';
$output .= '</div>';
$output .= '<div class="usof-button-example style_outlined">';
$output .= '<div class="usof-button-example-before"></div>';
$output .= '<span>' . __( 'Button Example', 'us' ) . '</span>';
$output .= '</div>';
$output .= '</div>';

echo $output;
