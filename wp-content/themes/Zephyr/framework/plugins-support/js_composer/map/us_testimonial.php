<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_testimonial
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 * @param $config    ['content'] string Shortcode's default content
 */
vc_map(
	array(
		'base' => 'us_testimonial',
		'name' => __( 'Testimonial', 'us' ),
		'deprecated' => 3.9,
		'weight' => 270,
		'params' => array(
			array(
				'param_name' => 'style',
				'heading' => us_translate( 'Style' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => '1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => '2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => '3',
					sprintf( __( 'Style %d', 'us' ), 4 ) => '4',
				),
				'std' => $config['atts']['style'],
				'weight' => 70,
			),
			array(
				'param_name' => 'content',
				'heading' => __( 'Quote Text', 'us' ),
				'type' => 'textarea',
				'std' => $config['content'],
				'admin_label' => TRUE,
				'weight' => 60,
			),
			array(
				'param_name' => 'author',
				'heading' => __( 'Author Name', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['author'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 50,
			),
			array(
				'param_name' => 'company',
				'heading' => __( 'Author Role', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['company'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 40,
			),
			array(
				'param_name' => 'img',
				'heading' => __( 'Author Photo', 'us' ),
				'type' => 'attach_image',
				'std' => $config['atts']['img'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 30,
			),
			array(
				'param_name' => 'link',
				'heading' => us_translate( 'Link' ),
				'description' => __( 'Applies to the Name and to the Photo', 'us' ),
				'type' => 'vc_link',
				'std' => $config['atts']['link'],
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
