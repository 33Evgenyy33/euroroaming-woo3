<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's widgets
 *
 * @filter us_config_widgets
 */

$social_links = us_config( 'social_links' );

$social_links_config = array();

foreach ( $social_links as $name => $title ) {
	$social_links_config[$name] = array(
		'type' => 'textfield',
		'heading' => $title,
		'std' => '',
	);
}

return array(
	'us_contacts' => array(
		'class' => 'US_Widget_Contacts',
		'name' => US_THEMENAME . ' &ndash; ' . __( 'Contacts', 'us' ),
		'description' => __( 'Contact Information', 'us' ),
		'params' => array(
			'title' => array(
				'type' => 'textfield',
				'heading' => us_translate( 'Title' ),
				'std' => __( 'Contacts', 'us' ),
			),
			'address' => array(
				'type' => 'textarea',
				'heading' => __( 'Address', 'us' ),
				'std' => '',
			),
			'phone' => array(
				'type' => 'textarea',
				'heading' => __( 'Phone', 'us' ),
				'std' => '',
			),
			'fax' => array(
				'type' => 'textfield',
				'heading' => __( 'Fax', 'us' ),
				'std' => '',
			),
			'email' => array(
				'type' => 'textfield',
				'heading' => us_translate( 'Email' ),
				'std' => '',
			),
		),
	),
	'us_login' => array(
		'class' => 'US_Widget_Login',
		'name' => US_THEMENAME . ' &ndash; ' . __( 'Login', 'us' ),
		'description' => __( 'Login Form', 'us' ),
		'params' => array(
			'title' => array(
				'type' => 'textfield',
				'heading' => us_translate( 'Title' ),
				'std' => '',
			),
			'register' => array(
				'type' => 'textfield',
				'heading' => __( 'Register URL', 'us' ),
				'std' => '',
			),
			'lostpass' => array(
				'type' => 'textfield',
				'heading' => __( 'Lost Password URL', 'us' ),
				'std' => '',
			),
			'login_redirect' => array(
				'type' => 'textfield',
				'heading' => __( 'Login Redirect URL', 'us' ),
				'std' => '',
			),
			'logout_redirect' => array(
				'type' => 'textfield',
				'heading' => __( 'Logout Redirect URL', 'us' ),
				'std' => '',
			),
		),
	),
	'us_socials' => array(
		'class' => 'US_Widget_Socials',
		'name' => US_THEMENAME . ' &ndash; ' . __( 'Social Links', 'us' ),
		'description' => __( 'Social Links', 'us' ),
		'params' => array_merge(
			array(
				'title' => array(
					'type' => 'textfield',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'size' => array(
					'type' => 'textfield',
					'heading' => us_translate( 'Size' ),
					'std' => '20px',
				),
				'style' => array(
					'type' => 'dropdown',
					'heading' => __( 'Icons Style', 'us' ),
					'value' => array(
						__( 'Simple', 'us' ) => 'default',
						__( 'Inside the Solid square', 'us' ) => 'solid_square',
						__( 'Inside the Outlined square', 'us' ) => 'outlined_square',
						__( 'Inside the Solid circle', 'us' ) => 'solid_circle',
						__( 'Inside the Outlined circle', 'us' ) => 'outlined_circle',
					),
					'std' => 'default',
				),
				'color' => array(
					'type' => 'dropdown',
					'heading' => __( 'Icons Color', 'us' ),
					'value' => array(
						__( 'Default brands colors', 'us' ) => 'brand',
						__( 'Text (theme color)', 'us' ) => 'text',
						__( 'Link (theme color)', 'us' ) => 'link',
					),
					'std' => 'brand',
				),
				'email' => array(
					'type' => 'textfield',
					'heading' => 'Email',
					'std' => '',
				),
			), $social_links_config, array(
				'custom_link' => array(
					'type' => 'textfield',
					'heading' => __( 'Custom Link', 'us' ),
					'std' => '',
				),
				'custom_title' => array(
					'type' => 'textfield',
					'heading' => __( 'Custom Link Title', 'us' ),
					'std' => '',
				),
				'custom_icon' => array(
					'type' => 'textfield',
					'heading' => __( 'Custom Link Icon', 'us' ),
					'std' => '',
				),
				'custom_color' => array(
					'type' => 'textfield',
					'heading' => __( 'Custom Link Color', 'us' ),
					'std' => '#1abc9c',
				),
			)
		),
	),
	'us_portfolio' => array(
		'class' => 'US_Widget_Portfolio',
		'name' => US_THEMENAME . ' &ndash; ' . __( 'Portfolio', 'us' ),
		'description' => __( 'Portfolio', 'us' ),
		'params' => array(
			'title' => array(
				'type' => 'textfield',
				'heading' => us_translate( 'Title' ),
				'std' => '',
			),
			'columns' => array(
				'type' => 'dropdown',
				'heading' => us_translate( 'Columns' ),
				'value' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'std' => '3',
			),
			'items' => array(
				'type' => 'textfield',
				'heading' => __( 'Items Quantity', 'us' ),
				'std' => '',
			),
			'orderby' => array(
				'type' => 'dropdown',
				'heading' => us_translate( 'Order' ),
				'value' => array(
					__( 'By date (newer first)', 'us' ) => 'date',
					__( 'By date (older first)', 'us' ) => 'date_asc',
					__( 'Alphabetically', 'us' ) => 'alpha',
					us_translate( 'Random' ) => 'rand',
				),
				'std' => 'date',
			),
		),
	),
);
