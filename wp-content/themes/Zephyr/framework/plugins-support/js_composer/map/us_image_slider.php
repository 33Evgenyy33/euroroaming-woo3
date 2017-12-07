<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_image_slider
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_image_slider',
		'name' => __( 'Image Slider', 'us' ),
		'description' => '',
		'icon' => 'icon-wpb-images-carousel',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 350,
		'params' => array(
			array(
				'param_name' => 'ids',
				'heading' => us_translate( 'Images' ),
				'type' => 'attach_images',
				'std' => $config['atts']['ids'],
			),
			array(
				'param_name' => 'arrows',
				'heading' => __( 'Navigation Arrows', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Show always', 'us' ) => 'always',
					__( 'Show on hover', 'us' ) => 'hover',
					us_translate( 'Hide' ) => 'hide',
				),
				'std' => $config['atts']['arrows'],
				'edit_field_class' => 'vc_col-sm-4',
			),
			array(
				'param_name' => 'nav',
				'heading' => __( 'Additional Navigation', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => 'none',
					__( 'Dots', 'us' ) => 'dots',
					__( 'Thumbs', 'us' ) => 'thumbs',
				),
				'std' => $config['atts']['nav'],
				'edit_field_class' => 'vc_col-sm-4',
			),
			array(
				'param_name' => 'transition',
				'heading' => __( 'Transition Effect', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Slide', 'us' ) => 'slide',
					__( 'Fade', 'us' ) => 'crossfade',
				),
				'std' => $config['atts']['transition'],
				'edit_field_class' => 'vc_col-sm-4',
			),
			array(
				'param_name' => 'meta',
				'type' => 'checkbox',
				'value' => array( __( 'Show items titles and description', 'us' ) => TRUE ),
				( ( $config['atts']['meta'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['meta'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'orderby',
				'type' => 'checkbox',
				'value' => array( __( 'Display items in random order', 'us' ) => 'rand' ),
				( ( $config['atts']['orderby'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['orderby'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'autoplay',
				'type' => 'checkbox',
				'value' => array( __( 'Enable Auto Rotation', 'us' ) => TRUE ),
				( ( $config['atts']['autoplay'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['autoplay'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'fullscreen',
				'type' => 'checkbox',
				'value' => array( __( 'Allow Full Screen view', 'us' ) => TRUE ),
				( ( $config['atts']['fullscreen'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['fullscreen'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'autoplay_period',
				'heading' => __( 'Auto Rotation Interval (in seconds)', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['autoplay_period'],
				'dependency' => array( 'element' => 'autoplay', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'img_size',
				'heading' => __( 'Images Size', 'us' ),
				'description' => sprintf( __( 'To change the default image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . admin_url( 'options-media.php' ) . '">' . us_translate( 'Media Settings' ) . '</a>' ) . ' ' . sprintf( __( 'To add custom image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=us-theme-options#advanced' ) . '">' . __( 'Theme Options', 'us' ) . '</a>' ),
				'type' => 'dropdown',
				'value' => us_image_sizes_select_values(),
				'std' => $config['atts']['img_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'img_fit',
				'heading' => __( 'Images Fit', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Initial', 'us' ) => 'scaledown',
					__( 'Fit to Area', 'us' ) => 'contain',
					__( 'Fill Area', 'us' ) => 'cover',
				),
				'std' => $config['atts']['img_fit'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Images Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => 'none',
					__( 'Phone 6 Black Realistic', 'us' ) => 'phone6-1',
					__( 'Phone 6 White Realistic', 'us' ) => 'phone6-2',
					__( 'Phone 6 Black Flat', 'us' ) => 'phone6-3',
					__( 'Phone 6 White Flat', 'us' ) => 'phone6-4',
				),
				'std' => $config['atts']['style'],
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
			),
		),
	)
);
vc_remove_element( 'vc_simple_slider' );
