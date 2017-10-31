<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_contacts
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_progbar',
		'name' => __( 'Progress Bar', 'us' ),
		'description' => '',
		'icon' => 'icon-wpb-graph',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 125,
		'params' => array(
			array(
				'param_name' => 'title',
				'heading' => us_translate( 'Title' ),
				'type' => 'textfield',
				'holder' => 'div',
				'std' => $config['atts']['title'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'count',
				'heading' => __( 'Progress Value', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['count'],
				'holder' => 'span',
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'style',
				'heading' => us_translate( 'Style' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => '1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => '2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => '3',
					sprintf( __( 'Style %d', 'us' ), 4 ) => '4',
					sprintf( __( 'Style %d', 'us' ), 5 ) => '5',
				),
				'std' => $config['atts']['style'],
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'color',
				'heading' => __( 'Progress Bar Color', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Primary (theme color)', 'us' ) => 'primary',
					__( 'Secondary (theme color)', 'us' ) => 'secondary',
					__( 'Heading (theme color)', 'us' ) => 'heading',
					__( 'Text (theme color)', 'us' ) => 'text',
					us_translate( 'Custom color' ) => 'custom',
				),
				'std' => $config['atts']['color'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'size',
				'heading' => __( 'Progress Bar Height', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['size'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'bar_color',
				'type' => 'colorpicker',
				'std' => $config['atts']['bar_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
			),
			array(
				'param_name' => 'hide_count',
				'type' => 'checkbox',
				'value' => array( __( 'Hide progress value counter', 'us' ) => TRUE ),
				( ( $config['atts']['hide_count'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['hide_count'],
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
			),
		),
	)
);
