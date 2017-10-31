<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Assets configuration (JS and CSS components)
 *
 * @filter us_config_assets
 */

return array(

	'style' => array(
		'title' => us_translate( 'Style' ),
		'css' => '/css/style.css',
		'css_size' => 231,
	),
	'woocommerce' =>  array(
		'title' => 'WooCommerce',
		'css' => '/css/plugins/woocommerce.css',
		'css_size' => 34,
		'separated' => TRUE,
		'apply_if' => class_exists( 'woocommerce' ),
	),

);
