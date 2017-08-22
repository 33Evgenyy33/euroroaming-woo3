<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_cta
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_cta',
		'name' => __( 'ActionBox', 'us' ),
		'description' => '',
		'icon' => 'icon-wpb-call-to-action',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 220,
		'params' => array(
			array(
				'param_name' => 'title',
				'heading' => us_translate( 'Title' ),
				'type' => 'textfield',
				'std' => $config['atts']['title'],
				'holder' => 'div',
				'weight' => 265,
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
				'weight' => 260,
			),
			array(
				'param_name' => 'title_size',
				'heading' => __( 'Title Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['title_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
				'weight' => 255,
			),
			array(
				'param_name' => 'content',
				'heading' => us_translate( 'Description' ),
				'type' => 'textarea',
				'std' => '',
				'holder' => 'div',
				'weight' => 250,
			),
			array(
				'param_name' => 'color',
				'heading' => __( 'Color Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Primary bg & White text', 'us' ) => 'primary',
					__( 'Secondary bg & White text', 'us' ) => 'secondary',
					__( 'Alternate bg & Content text', 'us' ) => 'light',
					__( 'Custom colors', 'us' ) => 'custom',
				),
				'std' => $config['atts']['color'],
				'weight' => 240,
			),
			array(
				'param_name' => 'bg_color',
				'heading' => __( 'Background Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['bg_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
				'weight' => 230,
			),
			array(
				'param_name' => 'text_color',
				'heading' => __( 'Text Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['text_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
				'weight' => 220,
			),
			array(
				'param_name' => 'controls',
				'heading' => __( 'Button(s) Location', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Right' ) => 'right',
					us_translate( 'Bottom' ) => 'bottom',
				),
				'std' => $config['atts']['controls'],
				'weight' => 210,
			),
			array(
				'param_name' => 'btn_link',
				'heading' => __( 'Button Link', 'us' ),
				'type' => 'vc_link',
				'std' => $config['atts']['btn_link'],
				'weight' => 200,
			),
			array(
				'param_name' => 'btn_label',
				'heading' => __( 'Button Label', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn_label'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 190,
			),
			array(
				'param_name' => 'btn_style',
				'heading' => __( 'Button Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Solid', 'us' ) => 'solid',
					__( 'Outlined', 'us' ) => 'outlined',
				),
				'std' => $config['atts']['btn_style'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 180,
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
				'std' => $config['atts']['btn_color'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 170,
			),
			array(
				'param_name' => 'btn_size',
				'heading' => __( 'Button Size', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn_size'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 140,
			),
			array(
				'param_name' => 'btn_icon',
				'heading' => __( 'Button Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn_icon'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 130,
			),
			array(
				'param_name' => 'btn_iconpos',
				'heading' => __( 'Button Icon Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['btn_iconpos'],
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 120,
			),
			array(
				'param_name' => 'second_button',
				'type' => 'checkbox',
				'value' => array( __( 'Display second button', 'us' ) => TRUE ),
				( ( $config['atts']['second_button'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['second_button'],
				'weight' => 110,
			),
			array(
				'param_name' => 'btn2_link',
				'heading' => __( 'Button Link', 'us' ),
				'type' => 'vc_link',
				'std' => $config['atts']['btn2_link'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'weight' => 100,
			),
			array(
				'param_name' => 'btn2_label',
				'heading' => __( 'Button Label', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn2_label'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 90,
			),
			array(
				'param_name' => 'btn2_style',
				'heading' => __( 'Button Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Solid', 'us' ) => 'solid',
					__( 'Outlined', 'us' ) => 'outlined',
				),
				'std' => $config['atts']['btn2_style'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 80,
			),
			array(
				'param_name' => 'btn2_color',
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
				'std' => $config['atts']['btn2_color'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 70,
			),
			array(
				'param_name' => 'btn2_size',
				'heading' => __( 'Button Size', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn2_size'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 40,
			),
			array(
				'param_name' => 'btn2_icon',
				'heading' => __( 'Button Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
				'type' => 'textfield',
				'std' => $config['atts']['btn2_icon'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
				'weight' => 30,
			),
			array(
				'param_name' => 'btn2_iconpos',
				'heading' => __( 'Button Icon Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['btn2_iconpos'],
				'dependency' => array( 'element' => 'second_button', 'not_empty' => TRUE ),
				'edit_field_class' => 'vc_col-sm-4',
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
vc_remove_element( 'vc_cta' );

