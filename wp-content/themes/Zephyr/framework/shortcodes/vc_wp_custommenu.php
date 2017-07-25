<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_wp_custommenu
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $atts           array Shortcode attributes
 *
 */

$title = $nav_menu = $el_class = '';
$output = '';
$atts = us_shortcode_atts( $atts, $shortcode_base );

if ( ! empty( $atts['layout'] ) )  {
	$el_class .= ' layout_' . $atts['layout'];
}
$el_class .= ' align_' . $atts['align'];

if ( $atts['el_class'] != '' ) {
	$el_class .= ' ' . $atts['el_class'];
}

$output = '<div class="vc_wp_custommenu ' . esc_attr( $el_class ) . '"';
if ( ! empty( $atts['el_id'] ) )  {
	$output .= ' id="' . $atts['el_id'] . '"';
}
if ( ! empty( $atts['font_size'] ) )  {
	$output .= ' style="font-size:' . $atts['font_size'] . ';"';
}
$output .= '>';
$type = 'WP_Nav_Menu_Widget';
$args = array();
global $wp_widget_factory;
// to avoid unwanted warnings let's check before using widget
if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
	ob_start();
	the_widget( $type, $atts, $args );
	$output .= ob_get_clean();
	$output .= '</div>';

	echo $output;
} else {
	echo 'Widget ' . esc_attr( $type ) . 'Not found in : vc_wp_custommenu';
}
