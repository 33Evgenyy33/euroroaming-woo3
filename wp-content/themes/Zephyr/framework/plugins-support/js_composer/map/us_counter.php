<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_counter
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_counter',
		'name' => __( 'Counter', 'us' ),
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 190,
		'params' => array(
			array(
				'param_name' => 'initial',
				'heading' => __( 'The initial number value', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['initial'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'target',
				'heading' => __( 'The final number value', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['target'],
				'holder' => 'span',
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'prefix',
				'heading' => __( 'Prefix (optional)', 'us' ),
				'description' => __( 'Text before number', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['prefix'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'suffix',
				'heading' => __( 'Suffix (optional)', 'us' ),
				'description' => __( 'Text after number', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['suffix'],
				'holder' => 'span',
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'color',
				'heading' => __( 'Number Color', 'us' ),
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
				'heading' => __( 'Number Size', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Small', 'us' ) => 'small',
					__( 'Medium', 'us' ) => 'medium',
					__( 'Large', 'us' ) => 'large',
				),
				'std' => $config['atts']['size'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'custom_color',
				'type' => 'colorpicker',
				'std' => $config['atts']['custom_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
			),
			array(
				'param_name' => 'title',
				'heading' => us_translate( 'Title' ),
				'type' => 'textfield',
				'std' => $config['atts']['title'],
				'holder' => 'span',
			),
			array(
				'param_name' => 'title_tag',
				'heading' => __( 'Title Tag Name', 'us' ),
				'description' => __( 'Used for SEO purposes', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
					'p' => 'p',
					'div' => 'div',
				),
				'std' => $config['atts']['title_tag'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'title_size',
				'heading' => __( 'Title Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['title_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'align',
				'heading' => us_translate( 'Alignment' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Center' ) => 'center',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['align'],
				'admin_label' => TRUE,
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

