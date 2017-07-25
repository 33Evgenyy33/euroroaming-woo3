<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output elements list to choose from
 */

global $cl_uri;
$elements = us_config( 'header-settings.elements', array() );

$output = '<div class="us-hb-window for_adding"><div class="us-hb-window-h"><div class="us-hb-window-header">';
$output .= '<div class="us-hb-window-title">' . __( 'Add element', 'us' ) . '</div>';
$output .= '<div class="us-hb-window-closer" title="' . us_translate( 'Close' ) . '"></div></div>';
$output .= '<div class="us-hb-window-body"><ul class="us-hb-window-list">';
foreach ( $elements as $name => $elm ) {
	if ( isset( $elm['place_if'] ) AND $elm['place_if'] === FALSE ) {
		continue;
	}
	$output .= '<li class="us-hb-window-item type_' . $name . '" data-name="' . $name . '"><div class="us-hb-window-item-h">';
	$output .= '<div class="us-hb-window-item-icon"';
	if ( isset( $elm['icon'] ) AND ! empty( $elm['icon'] ) ) {
		$output .= ' style="background-image: url(' . $elm['icon'] . ')';
	}
	$output .= '></div>';
	$output .= '<div class="us-hb-window-item-title">' . ( isset( $elm['title'] ) ? $elm['title'] : $name ) . '</div>';
	if ( isset( $elm['description'] ) AND ! empty( $elm['description'] ) ) {
		$output .= '<div class="us-hb-window-item-description">' . $elm['description'] . '</div>';
	}
	$output .= '</div></li>';
}
$output .= '</ul></div></div></div>';

echo $output;

