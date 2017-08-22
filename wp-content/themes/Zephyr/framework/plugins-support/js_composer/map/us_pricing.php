<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_pricing
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 * @param $congig    ['items_atts'] array Items' attributes and default values
 */
vc_map(
	array(
		'base' => 'us_pricing',
		'name' => __( 'Pricing Table', 'us' ),
		'description' => '',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 150,
		'params' => array(
			array(
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
						'admin_label' => TRUE,
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
							__( 'Border (theme color)', 'us' ) => 'light',
							__( 'Text (theme color)', 'us' ) => 'contrast',
							us_translate( 'Black' ) => 'black',
							us_translate( 'White' ) => 'white',
							__( 'Purple', 'us' ) => 'purple',
							__( 'Pink', 'us' ) => 'pink',
							__( 'Red', 'us' ) => 'red',
							__( 'Yellow', 'us' ) => 'yellow',
							__( 'Lime', 'us' ) => 'lime',
							__( 'Green', 'us' ) => 'green',
							__( 'Teal', 'us' ) => 'teal',
							__( 'Blue', 'us' ) => 'blue',
							__( 'Navy', 'us' ) => 'navy',
							__( 'Midnight', 'us' ) => 'midnight',
							__( 'Brown', 'us' ) => 'brown',
							__( 'Cream', 'us' ) => 'cream',
							__( 'Transparent', 'us' ) => 'transparent',
						),
						'std' => $config['items_atts']['btn_color'],
						'edit_field_class' => 'vc_col-sm-6',
					),
					array(
						'param_name' => 'btn_style',
						'heading' => __( 'Button Style', 'us' ),
						'type' => 'dropdown',
						'value' => array(
							__( 'Solid', 'us' ) => 'solid',
							__( 'Outlined', 'us' ) => 'outlined',
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
						'heading' => __( 'Button Icon', 'us' ),
						'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
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

