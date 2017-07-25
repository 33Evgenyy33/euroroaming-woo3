<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: design_options
 *
 * Design options.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @var $value array Current value
 */

if ( ! is_array( $value ) ) {
	$value = array();
}

$output = '<div class="usof-design">';
$states = array(
	'default' => __( 'Default Margin', 'us' ),
	'tablets' => __( 'Margin on Tablets', 'us' ),
	'mobiles' => __( 'Margin on Mobiles', 'us' ),
);
foreach ( $states as $state => $state_title ) {
	$output .= '<div class="usof-design-control for_' . $state . '">';
	$output .= '<h5>' . $state_title . '</h5>';
	$output .= '<div class="usof-design-margins">';
	foreach ( array( 'top', 'right', 'bottom', 'left' ) as $part ) {
		$subname = 'margin_' . $part . '_' . $state;
		$subvalue = isset( $value[ $subname ] ) ? $value[ $subname ] : '';
		if ( preg_match( '~^\d+$~', $subvalue ) ) {
			$subvalue = $subvalue . 'px';
		}
		$output .= '<input class="' . $part . '" type="text" name="' . esc_attr( $subname ) . '" value="' . esc_attr( $subvalue ) . '" placeholder="-">';
	}
	$output .= '</div></div>';
}

// Hide this element when the header is sticky
$subvalue = isset( $value['hide_for_sticky'] ) ? $value['hide_for_sticky'] : '0';
$output .= '<div class="usof-switcher">';
$output .= '<input name="hide_for_sticky" value="0" type="hidden">';
$output .= '<input id="' . $id . '_hide_for_sticky" name="hide_for_sticky" value="1" type="checkbox"';
if ( $subvalue ) {
	$output .= ' checked';
}
$output .= '>';
$output .= '<label for="' . $id . '_hide_for_sticky">';
$output .= '<span class="usof-switcher-box"><i></i></span>';
$output .= '<span class="usof-switcher-text">' . __( 'Hide this element when the header is sticky', 'us' ) . '</span>';
$output .= '</label>';
$output .= '</div>';

// Hide this element when the header is NOT sticky
$subvalue = isset( $value['hide_for_not-sticky'] ) ? $value['hide_for_not-sticky'] : '0';
$output .= '<div class="usof-switcher">';
$output .= '<input name="hide_for_not-sticky" value="0" type="hidden">';
$output .= '<input id="' . $id . '_hide_for_not-sticky" name="hide_for_not-sticky" value="1" type="checkbox"';
if ( $subvalue ) {
	$output .= ' checked';
}
$output .= '>';
$output .= '<label for="' . $id . '_hide_for_not-sticky">';
$output .= '<span class="usof-switcher-box"><i></i></span></span>';
$output .= '<span class="usof-switcher-text">' . __( 'Hide this element when the header is NOT sticky', 'us' ) . '</span>';
$output .= '</label>';
$output .= '</div>';

$output .= '</div>';

echo $output;
