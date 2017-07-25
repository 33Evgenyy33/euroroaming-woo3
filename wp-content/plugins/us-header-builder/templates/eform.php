<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single element's editing form
 *
 * @var $type string ELement type
 * @var $params array List of config-based params
 * @var $values array List of param_name => value
 */

// Validating and sanitizing input
$values = ( isset( $values ) AND is_array( $values ) ) ? $values : array();

// Validating, sanitizing and grouping params
$groups = array();
foreach ( $params as $param_name => &$param ) {
	$param['type'] = isset( $param['type'] ) ? $param['type'] : 'textfield';
	if ( $param['type'] == 'image' ) {
		$param['type'] = 'images';
		$param['multiple'] = FALSE;
	}
	if ( $param['type'] == 'html' AND $param_name != 'content' ) {
		// For VC-compatibility we may have only one wysiwyg field and it should be called content
		$param['type'] = 'textarea';
	}
	$param['classes'] = isset( $param['classes'] ) ? $param['classes'] : '';
	$param['std'] = isset( $param['std'] ) ? $param['std'] : '';
	// Filling missing values with standard ones
	if ( ! isset( $values[ $param_name ] ) ) {
		$values[ $param_name ] = $param['std'];
	}
	$group = isset( $param['group'] ) ? $param['group'] : us_translate( 'General' );
	if ( ! isset( $groups[ $group ] ) ) {
		$groups[ $group ] = array();
	}
	$groups[ $group ][ $param_name ] = &$param;
}

$output = '<div class="usof-form for_' . $type . '">';
if ( count( $groups ) > 1 ) {
	$group_index = 0;
	$output .= '<div class="usof-tabs">';
	$output .= '<div class="usof-tabs-list">';
	foreach ( $groups as $group => &$group_params ) {
		$output .= '<div class="usof-tabs-item' . ( $group_index ? '' : ' active' ) . '">' . $group . '</div>';
		$group_index++;
	}
	$output .= '</div>';
	$output .= '<div class="usof-tabs-sections">';
}

$group_index = 0;
foreach ( $groups as &$group_params ) {
	if ( count( $groups ) > 1 ) {
		$output .= '<div class="usof-tabs-section" style="display: ' . ( $group_index ? 'none' : 'flex' ) . '">';
	}
	foreach ( $group_params as $param_name => &$field ) {
		$output .= us_get_template( 'vendor/usof/templates/field', array(
			'name' => $param_name,
			'id' => 'hb_elm_' . $type . '_' . $param_name,
			'field' => $field,
			'values' => $values,
		) );
	}
	if ( count( $groups ) > 1 ) {
		$output .= '</div><!-- .usof-tabs-section -->';
	}
	$group_index++;
}

if ( count( $groups ) > 1 ) {
	$output .= '</div><!-- .usof-tabs-sections -->';
	$output .= '</div><!-- .usof-tabs -->';
}
$output .= '</div>';

echo $output;
