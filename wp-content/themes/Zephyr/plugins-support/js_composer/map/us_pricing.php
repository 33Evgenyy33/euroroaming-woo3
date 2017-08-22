<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Overloading framework's VC shortcode mapping of: us_pricing
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

global $us_template_directory;
require $us_template_directory . '/framework/plugins-support/js_composer/map/us_pricing.php';

vc_add_param(
	'us_pricing', array(
	'param_name' => 'style',
	'heading' => us_translate( 'Style' ),
	'type' => 'dropdown',
	'value' => array(
		__( 'Card Style', 'us' ) => '1',
		__( 'Flat Style', 'us' ) => '2',
	),
	'std' => $config['atts']['style'],
	'weight' => 30,
)
);
vc_remove_param( 'us_pricing', 'items' );
vc_add_param(
	'us_pricing', array(
	'param_name' => 'items',
	'type' => 'param_group',
	'heading' => __( 'Pricing Items', 'us' ),
	'value' => $config['atts']['items'],
	'params' => array(
		array(
			'param_name' => 'title',
			'heading' => us_translate( 'Title' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['title'],
			'admin_label' => TRUE,
		),
		array(
			'param_name' => 'type',
			'type' => 'checkbox',
			'value' => array( __( 'Mark this item as featured', 'us' ) => 'featured' ),
			( ( $config['items_atts']['type'] !== FALSE ) ? 'std' : '_std' ) => $config['items_atts']['type'],
		),
		array(
			'param_name' => 'price',
			'heading' => __( 'Price', 'us' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['type'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'substring',
			'heading' => __( 'Price Substring', 'us' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['substring'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'features',
			'heading' => __( 'Features List', 'us' ),
			'type' => 'textarea',
			'std' => $config['items_atts']['features'],
		),
		array(
			'param_name' => 'btn_text',
			'heading' => __( 'Button Label', 'us' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['btn_text'],
			'class' => 'wpb_button',
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_color',
			'heading' => __( 'Button Color', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Primary (theme color)', 'us' ) => 'primary',
				__( 'Secondary (theme color)', 'us' ) => 'secondary',
				__( 'Light (theme color)', 'us' ) => 'light',
				__( 'Contrast (theme color)', 'us' ) => 'contrast',
				us_translate( 'Black' ) => 'black',
				us_translate( 'White' ) => 'white',
				__( 'Custom colors', 'us' ) => 'custom',
			),
			'std' => $config['items_atts']['btn_color'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_bg_color',
			'heading' => __( 'Button Background Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['items_atts']['btn_bg_color'],
			'class' => '',
			'dependency' => array( 'element' => 'btn_color', 'value' => 'custom' ),
		),
		array(
			'param_name' => 'btn_text_color',
			'heading' => __( 'Button Text Color', 'us' ),
			'type' => 'colorpicker',
			'std' => $config['items_atts']['btn_text_color'],
			'class' => '',
			'dependency' => array( 'element' => 'btn_color', 'value' => 'custom' ),
		),
		array(
			'param_name' => 'btn_style',
			'heading' => __( 'Button Style', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				__( 'Raised', 'us' ) => 'raised',
				__( 'Flat', 'us' ) => 'flat',
			),
			'std' => $config['items_atts']['btn_style'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_size',
			'heading' => __( 'Button Size', 'us' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['btn_size'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_icon',
			'heading' => __( 'Button Icon (optional)', 'us' ),
			'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
			'type' => 'textfield',
			'std' => $config['items_atts']['btn_icon'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_iconpos',
			'heading' => __( 'Button Icon Position', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'Left' ) => 'left',
				us_translate( 'Right' ) => 'right',
			),
			'std' => $config['items_atts']['btn_iconpos'],
			'edit_field_class' => 'vc_col-sm-6',
		),
		array(
			'param_name' => 'btn_link',
			'heading' => __( 'Button Link', 'us' ),
			'type' => 'vc_link',
			'std' => $config['items_atts']['btn_link'],
		),
	),
	'weight' => 20,
)
);
