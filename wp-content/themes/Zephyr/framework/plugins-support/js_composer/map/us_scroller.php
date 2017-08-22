<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_scroller
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_scroller',
		'name' => __( 'Page Scroller', 'us' ),
		'description' => '',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 115,
		'params' => array(
			array(
				'param_name' => 'disable_width',
				'heading' => __( 'Disable scrolling at width', 'us' ),
				'description' => __( 'When screen width is less than this value, scrolling by rows will be disabled.', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['disable_width'],
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'speed',
				'heading' => __( 'Scroll Speed (milliseconds)', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['speed'],
			),
			array(
				'param_name' => 'dots',
				'type' => 'checkbox',
				'value' => array( __( 'Show Navigation Dots', 'us' ) => TRUE ),
				( ( $config['atts']['dots'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['dots'],
			),
			array(
				'param_name' => 'dots_style',
				'heading' => __( 'Dots Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => '1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => '2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => '3',
					sprintf( __( 'Style %d', 'us' ), 4 ) => '4',
				),
				'std' => $config['atts']['dots_style'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'dots', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'dots_pos',
				'heading' => __( 'Dots Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['dots_pos'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'dots', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'dots_size',
				'heading' => __( 'Dots Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['dots_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'dots', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'dots_color',
				'heading' => __( 'Dots Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['dots_color'],
				'dependency' => array( 'element' => 'dots', 'not_empty' => TRUE ),
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

