<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output button element
 *
 * @var $label            string
 * @var $link             string
 * @var $style            string
 * @var $icon             string
 * @var $iconpos          string
 * @var $size             string
 * @var $size_tablets     string
 * @var $size_mobiles     string
 * @var $color_bg         string
 * @var $color_hover_bg   string
 * @var $color_text       string
 * @var $color_hover_text string
 * @var $design_options   array
 * @var $id               string
 */

// .w-btn container additional classes and inner CSS-styles
$wrapper_classes = '';
$classes = '';
$inner_css = '';

$classes .= ' style_' . $style . ' color_custom';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$wrapper_classes .= ' hide-for-sticky';
}
if ( isset( $design_options ) AND isset( $design_options['hide_for_not-sticky'] ) AND $design_options['hide_for_not-sticky'] ) {
	$wrapper_classes .= ' hide-for-not-sticky';
}
foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
	if ( ! us_is_header_elm_shown( $id, $state ) ) {
		$wrapper_classes .= ' hidden_for_' . $state;
	}
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$wrapper_classes .= ' ush_' . str_replace( ':', '_', $id );
}

$icon_html = '';
if ( ! empty( $icon ) ) {
	$icon_html = us_prepare_icon_tag( $icon );
	$classes .= ' icon_at' . $iconpos;
} else {
	$classes .= ' icon_none';
}

$link_atts = usof_get_link_atts( $link );
if ( ! isset( $link_atts['href'] ) ) {
	$link_atts['href'] = '';
}
if ( ! empty( $link_atts['href'] ) AND strpos( $link_atts['href'], '[lang]' ) !== FALSE ) {
	$link_atts['href'] = str_replace( '[lang]', usof_get_lang(), $link_atts['href'] );
}

$output = '<div class="w-btn-wrapper' . $wrapper_classes . '">';
$output .= '<a class="w-btn' . $classes . '" href="' . esc_url( $link_atts['href'] ) . '"';
if ( ! empty( $link_atts['target'] ) ) {
	$output .= ' target="' . esc_attr( $link_atts['target'] ) . '"';
}
$output .= '>';
$output .= $icon_html;
$output .= '<span class="w-btn-label">' . $label . '</span>';
$output .= '</a></div>';

echo $output;
