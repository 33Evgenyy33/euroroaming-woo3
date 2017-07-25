<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Contact form configuration
 *
 * @var $config array Framework-based theme options config
 *
 * @filter us_config_cform
 */

// Using titles instead of placeholders
return us_array_merge(
	$config, array(
		'fields' => array(
			'name' => array(
				'placeholder' => '',
				'title' => us_translate( 'Name' ),
			),
			'email' => array(
				'placeholder' => '',
				'title' => us_translate( 'Email' ),
			),
			'phone' => array(
				'placeholder' => '',
				'title' => __( 'Phone Number', 'us' ),
			),
			'message' => array(
				'placeholder' => '',
				'title' => __( 'Message', 'us' ),
			),
		),
	)
);
