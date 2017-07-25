<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's demo-import settings
 *
 * @filter us_config_demo-import
 */
return array(
	'main' => array(
		'title' => 'Main Demo',
		'preview_url' => 'http://zephyr.us-themes.com/',
		'front_page' => 'Home',
		'content' => array(
			'pages',
			'posts',
			'portfolio_items',
			'testimonials',
			'products',
			'widgets',
		),
		'sliders' => array(
			'slider-main.zip',
			'slider-second.zip',
		),
		'sidebars' => array(
			'us_widget_area_shop_sidebar' => 'Shop Sidebar',
			'us_widget_area_login_widget' => 'Login',
		),
	),
);
