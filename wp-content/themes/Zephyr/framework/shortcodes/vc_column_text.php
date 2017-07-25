<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css_animation
 * @var $css
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column_text
 */

$classes = '';
$atts = us_shortcode_atts( $atts, 'vc_column_text' );

if ( function_exists( 'vc_shortcode_custom_css_class' ) AND ! empty( $atts['css'] ) ) {
	$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$el_id_string = '';
if ( $atts['el_id'] != '' ) {
	$el_id_string = ' id="' . esc_attr( $atts['el_id'] ) . '"';
}

$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );

$output = '
	<div class="wpb_text_column ' . esc_attr( $classes ) . '"' . $el_id_string . '>
		<div class="wpb_wrapper">
			' . do_shortcode( shortcode_unautop( $content ) ) . '
		</div>
	</div>
';

echo $output;
