<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_social_links
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

$social_links = us_config( 'social_links' );

vc_map(
	array(
		'base' => 'us_social_links',
		'name' => __( 'Social Links', 'us' ),
		'description' => '',
		'icon' => 'icon-wpb-balloon-facebook-left',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 170,
		'params' => array(
			array(
				'type' => 'param_group',
				'param_name' => 'items',
				'params' => array(
					array(
						'heading' => __( 'Icon', 'us' ),
						'param_name' => 'type',
						'type' => 'dropdown',
						'value' => array_merge( array_flip( $social_links ), array( __( 'Custom Icon', 'us' ) => 'custom' ) ),
						'std' => '',
						'edit_field_class' => 'vc_col-sm-6',
						'admin_label' => TRUE,
					),
					array(
						'heading' => us_translate( 'Link' ),
						'param_name' => 'url',
						'type' => 'textfield',
						'std' => '',
						'edit_field_class' => 'vc_col-sm-6',
					),
					array(
						'heading' => __( 'Custom Link Title', 'us' ),
						'param_name' => 'title',
						'type' => 'textfield',
						'std' => '',
						'dependency' => array( 'element' => 'type', 'value' => 'custom' ),
					),
					array(
						'heading' => __( 'Custom Link Icon', 'us' ),
						'param_name' => 'icon',
						'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
						'type' => 'textfield',
						'std' => '',
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array( 'element' => 'type', 'value' => 'custom' ),
					),
					array(
						'heading' => __( 'Custom Link Color', 'us' ),
						'param_name' => 'color',
						'type' => 'colorpicker',
						'std' => '#1abc9c',
						'edit_field_class' => 'vc_col-sm-6',
						'dependency' => array( 'element' => 'type', 'value' => 'custom' ),
					),
				),
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Icons Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'default',
					__( 'Inside the Solid square', 'us' ) => 'solid_square',
					__( 'Inside the Outlined square', 'us' ) => 'outlined_square',
					__( 'Inside the Solid circle', 'us' ) => 'solid_circle',
					__( 'Inside the Outlined circle', 'us' ) => 'outlined_circle',
				),
				'std' => $config['atts']['style'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'color',
				'heading' => __( 'Icons Color', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Default brands colors', 'us' ) => 'brand',
					__( 'Text (theme color)', 'us' ) => 'text',
					__( 'Link (theme color)', 'us' ) => 'link',
				),
				'std' => $config['atts']['color'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'size',
				'heading' => __( 'Icons Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['size'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'align',
				'heading' => __( 'Icons Alignment', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Center' ) => 'center',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['align'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
				'group' => us_translate( 'Appearance' ),
			),
		),
	)
);
