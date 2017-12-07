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
		'name' => us_translate( 'Contact Info' ),
		'base' => 'us_contacts',
		'deprecated' => 3.9,
		'weight' => 140,
		'params' => array(
			array(
				'param_name' => 'address',
				'heading' => __( 'Address', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['address'],
				'weight' => 50,
			),
			array(
				'param_name' => 'phone',
				'heading' => __( 'Phone', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['phone'],
				'weight' => 40,
			),
			array(
				'param_name' => 'fax',
				'heading' => __( 'Fax', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['fax'],
				'weight' => 30,
			),
			array(
				'param_name' => 'email',
				'heading' => us_translate( 'Email' ),
				'type' => 'textfield',
				'std' => $config['atts']['email'],
				'weight' => 20,
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
				'weight' => 10,
			),
		),
	)
);
