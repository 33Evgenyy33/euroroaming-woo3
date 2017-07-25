<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_iconbox
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 * @param $config    ['content'] string Shortcode's default content
 */
vc_map(
	array(
		'base' => 'us_iconbox',
		'name' => __( 'IconBox', 'us' ),
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 280,
		'params' => array(
			array(
				'param_name' => 'icon',
				'heading' => __( 'Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
				'type' => 'textfield',
				'std' => $config['atts']['icon'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 120,
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Icon Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'default',
					__( 'Inside the Solid circle', 'us' ) => 'circle',
					__( 'Inside the Outlined circle', 'us' ) => 'outlined',
				),
				'std' => $config['atts']['style'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 110,
			),
			array(
				'param_name' => 'color',
				'heading' => __( 'Icon Color', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Primary (theme color)', 'us' ) => 'primary',
					__( 'Secondary (theme color)', 'us' ) => 'secondary',
					__( 'Border (theme color)', 'us' ) => 'light',
					__( 'Text (theme color)', 'us' ) => 'contrast',
					__( 'Custom colors', 'us' ) => 'custom',
				),
				'std' => $config['atts']['color'],
				'weight' => 100,
			),
			array(
				'param_name' => 'icon_color',
				'heading' => __( 'Icon Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['icon_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
				'weight' => 90,
			),
			array(
				'param_name' => 'bg_color',
				'heading' => __( 'Icon Circle Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['bg_color'],
				'dependency' => array( 'element' => 'color', 'value' => 'custom' ),
				'weight' => 80,
			),
			array(
				'param_name' => 'iconpos',
				'heading' => __( 'Icon Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Top' ) => 'top',
					us_translate( 'Left' ) => 'left',
				),
				'std' => $config['atts']['iconpos'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 70,
			),
			array(
				'param_name' => 'size',
				'heading' => __( 'Icon Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['size'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 60,
			),
			array(
				'param_name' => 'title',
				'heading' => us_translate( 'Title' ),
				'type' => 'textfield',
				'holder' => 'div',
				'std' => $config['atts']['title'],
				'weight' => 50,
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
				'weight' => 45,
			),
			array(
				'param_name' => 'title_size',
				'heading' => __( 'Title Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['title_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'dependency' => array( 'element' => 'title', 'not_empty' => TRUE ),
				'weight' => 44,
			),
			array(
				'param_name' => 'content',
				'heading' => us_translate( 'Description' ),
				'type' => 'textarea',
				'std' => $config['content'],
				'holder' => 'div',
				'weight' => 40,
			),
			array(
				'param_name' => 'link',
				'heading' => __( 'Link (optional)', 'us' ),
				'type' => 'vc_link',
				'std' => $config['atts']['link'],
				'weight' => 30,
			),
			array(
				'param_name' => 'img',
				'heading' => us_translate( 'Image' ),
				'description' => __( 'Set an image, which overrides the font icon', 'us' ),
				'type' => 'attach_image',
				'std' => $config['atts']['img'],
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
