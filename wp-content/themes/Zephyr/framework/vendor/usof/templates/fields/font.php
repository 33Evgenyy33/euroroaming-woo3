<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Font
 *
 * Select font
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['preview'] array
 * @param $field ['preview']['text'] string Preview text
 * @param $field ['preview']['size'] string Font size in css format
 *
 * @var   $value array List of checked keys
 */

global $usof_options;

$output = '';

$font_value = explode( '|', $value, 2 );
if ( ! isset( $font_value[1] ) OR empty( $font_value[1] ) ) {
	$font_value[1] = '400,700';
}
$font_value[1] = explode( ',', $font_value[1] );

if ( ! isset( $web_safe_fonts ) ) {
	$web_safe_fonts = array(
		'Georgia, serif',
		'"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'"Times New Roman", Times, serif',
		'Arial, Helvetica, sans-serif',
		'Impact, Charcoal, sans-serif',
		'"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma, Geneva, sans-serif',
		'"Trebuchet MS", Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif',
		'"Courier New", Courier, monospace',
		'"Lucida Console", Monaco, monospace',
	);
}

if ( ! isset( $google_fonts ) ) {
	$google_fonts = us_config( 'google-fonts', array() );
	$output .= '<div class="usof-fonts-json"' . us_pass_data_to_js( $google_fonts ) . '></div>';
}

$output .= '<div class="usof-font">';

// Font preview
$field['preview'] = isset( $field['preview'] ) ? $field['preview'] : array();
$field['preview']['text'] = isset( $field['preview']['text'] ) ? $field['preview']['text'] : '0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz';

$output .= '<div class="usof-font-preview" style="';
if ( isset( $field['preview']['size_field'] ) AND ! empty( $usof_options[$field['preview']['size_field']] ) ) {
	$output .= 'font-size: ' . $usof_options[$field['preview']['size_field']] . 'px;';
}
if ( isset( $field['preview']['lineheight_field'] ) AND ! empty( $usof_options[$field['preview']['lineheight_field']] ) ) {
	$output .= 'line-height: ' . $usof_options[$field['preview']['lineheight_field']] . 'px;';
}
if ( isset( $field['preview']['letterspacing_field'] ) AND ! empty( $usof_options[$field['preview']['letterspacing_field']] ) ) {
	$output .= 'letter-spacing: ' . $usof_options[$field['preview']['letterspacing_field']] . 'em;';
}
if ( isset( $field['preview']['transform_field'] ) AND ! empty( $usof_options[$field['preview']['transform_field']] ) ) {
	if ( in_array( 'uppercase', $usof_options[$field['preview']['transform_field']] ) ) {
		$output .= 'text-transform: uppercase;';
	}
	if ( in_array( 'italic', $usof_options[$field['preview']['transform_field']] ) ) {
		$output .= 'font-style: italic;';
	}
}
if ( isset( $field['preview']['weight_field'] ) AND ! empty( $usof_options[$field['preview']['weight_field']] ) ) {
	$output .= 'font-weight: ' . $usof_options[$field['preview']['weight_field']] . ';';
} elseif ( count( $font_value[1] ) > 0 ) {
	$output .= 'font-weight: ' . intval( $font_value[1][0] ) . ';';
}
$output .= '"><div>' . $field['preview']['text'] . '</div></div>';

$output .= '<input type="hidden" name="' . $name . '" value="' . esc_attr( $value ) . '" />';
$output .= '<select>';
$output .= '<option value="none" ' . selected( $font_value[0], 'none', FALSE ) . '>' . __( 'Font not specified', 'us' ) . '</option>';
$output .= '<optgroup label="' . __( 'Web safe font combinations (do not need to be loaded)', 'us' ) . '">';
foreach ( $web_safe_fonts as $font_name ) {
	$output .= "<option value='" . esc_attr( $font_name ) . "' " . selected( $font_value[0], $font_name, FALSE ) . '>' . $font_name . '</option>';
}
$output .= '</optgroup>';
$output .= '<optgroup label="' . __( 'Custom fonts (loaded from Google Fonts)', 'us' ) . '">';
foreach ( $google_fonts as $font_name => &$tmp_font_value ) {
	$output .= '<option value="' . esc_attr( $font_name ) . '"' . selected( $font_value[0], $font_name, FALSE ) . '>' . $font_name . '</option>';
}
$output .= '</optgroup>';
$output .= '</select>';

// Font weights
if ( ! isset( $font_weights ) ) {
	$font_weights = array(
		'100' => '100 ' . __( 'thin', 'us' ),
		'100italic' => '100 ' . __( 'thin', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'200' => '200 ' . __( 'extra-light', 'us' ),
		'200italic' => '200 ' . __( 'extra-light', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'300' => '300 ' . __( 'light', 'us' ),
		'300italic' => '300 ' . __( 'light', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'400' => '400 ' . __( 'normal', 'us' ),
		'400italic' => '400 ' . __( 'normal', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'500' => '500 ' . __( 'medium', 'us' ),
		'500italic' => '500 ' . __( 'medium', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'600' => '600 ' . __( 'semi-bold', 'us' ),
		'600italic' => '600 ' . __( 'semi-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'700' => '700 ' . __( 'bold', 'us' ),
		'700italic' => '700 ' . __( 'bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'800' => '800 ' . __( 'extra-bold', 'us' ),
		'800italic' => '800 ' . __( 'extra-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
		'900' => '900 ' . __( 'ultra-bold', 'us' ),
		'900italic' => '900 ' . __( 'ultra-bold', 'us' ) . ' <i>' . __( 'italic', 'us' ) . '</i>',
	);
}
$show_weights = (array) us_config( 'google-fonts.' . $font_value[0] . '.variants', array() );

$output .= '<ul class="usof-checkbox-list">';
foreach ( $font_weights as $font_weight => $font_title ) {
	$output .= '<li class="usof-checkbox' . ( in_array( $font_weight, $show_weights ) ? '' : ' hidden' ) . '" data-value="' . $font_weight . '">';
	$output .= '<input type="checkbox" id="' . $id . '_weight_' . $font_weight . '" ';
	$output .= 'value="' . $font_weight . '"';
	$output .= ( in_array( $font_weight, $font_value[1] ) ? ' checked' : '' );
	$output .= '><label for="' . $id . '_weight_' . $font_weight . '" ';
	$output .= 'class="' . ( in_array( $font_weight, $show_weights ) ? '' : 'hidden' ) . '">';
	$output .= '<span class="usof-checkbox-icon"></span><span class="usof-checkbox-text">';
	$output .= $font_title . '</span></label></li>';
}
$output .= '</ul>';

$font_style_fields_data = array();
if ( isset( $field['preview']['size_field'] )) {
	$font_style_fields_data['sizeField'] = $field['preview']['size_field'];
}
if ( isset( $field['preview']['lineheight_field'] ) ) {
	$font_style_fields_data['lineheightField'] = $field['preview']['lineheight_field'];
}
if ( isset( $field['preview']['weight_field'] ) ) {
	$font_style_fields_data['weightField'] = $field['preview']['weight_field'];
}
if ( isset( $field['preview']['letterspacing_field'] ) ) {
	$font_style_fields_data['letterspacingField'] = $field['preview']['letterspacing_field'];
}
if ( isset( $field['preview']['transform_field'] ) ) {
	$font_style_fields_data['transformField'] = $field['preview']['transform_field'];
}

$output .= '<div class="usof-font-style-fields-json"' . us_pass_data_to_js( $font_style_fields_data ) . '></div>';

$output .= '</div>';

echo $output;
