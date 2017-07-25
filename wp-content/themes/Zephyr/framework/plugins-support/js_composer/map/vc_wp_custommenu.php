<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_wp_custommenu
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

vc_update_shortcode_param(
	'vc_wp_custommenu', array(
		'param_name' => 'title',
		'weight' => 50,
	)
);

vc_add_params(
	'vc_wp_custommenu', array(
	array(
		'param_name' => 'layout',
		'heading' => __( 'Layout', 'us' ),
		'type' => 'dropdown',
		'admin_label' => TRUE,
		'value' => array(
			__( 'Vertical', 'us' ) => 'ver',
			__( 'Horizontal', 'us' ) => 'hor',
		),
		'std' => $config['atts']['layout'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 40,
	),
	array(
		'param_name' => 'align',
		'heading' => us_translate( 'Alignment' ),
		'type' => 'dropdown',
		'admin_label' => TRUE,
		'value' => array(
			us_translate( 'Left' ) => 'left',
			us_translate( 'Center' ) => 'center',
			us_translate( 'Right' ) => 'right',
		),
		'std' => $config['atts']['align'],
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 30,
	),
	array(
		'param_name' => 'font_size',
		'heading' => __( 'Font Size', 'us' ),
		'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
		'type' => 'textfield',
		'std' => $config['atts']['font_size'],
		'weight' => 20,
	),
)
);