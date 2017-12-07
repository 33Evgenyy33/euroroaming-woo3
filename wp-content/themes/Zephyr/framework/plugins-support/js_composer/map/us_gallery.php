<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_gallery
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_gallery',
		'name' => __( 'Image Gallery', 'us' ),
		'description' => '',
		'icon' => 'icon-wpb-images-stack',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 360,
		'params' => array(
			array(
				'param_name' => 'ids',
				'heading' => us_translate( 'Images' ),
				'type' => 'attach_images',
				'std' => $config['atts']['ids'],
			),
			array(
				'param_name' => 'layout',
				'heading' => __( 'Display items as', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Grid', 'us' ) => 'default',
					__( 'Masonry', 'us' ) => 'masonry',
				),
				'std' => $config['atts']['layout'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'columns',
				'heading' => us_translate( 'Columns' ),
				'type' => 'dropdown',
				'value' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
				'std' => $config['atts']['columns'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'orderby',
				'type' => 'checkbox',
				'value' => array( __( 'Display items in random order', 'us' ) => 'rand' ),
				( ( $config['atts']['orderby'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['orderby'],
			),
			array(
				'param_name' => 'indents',
				'type' => 'checkbox',
				'value' => array( __( 'Add indents between items', 'us' ) => TRUE ),
				( ( $config['atts']['indents'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['indents'],
			),
			array(
				'param_name' => 'meta',
				'type' => 'checkbox',
				'value' => array( __( 'Show items titles and description', 'us' ) => TRUE ),
				( ( $config['atts']['meta'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['meta'],
			),
			array(
				'param_name' => 'meta_style',
				'heading' => __( 'Title and Description Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'simple',
					__( 'Modern', 'us' ) => 'modern',
				),
				'std' => $config['atts']['meta_style'],
				'dependency' => array( 'element' => 'meta', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'link',
				'type' => 'checkbox',
				'value' => array( __( 'Disable popup opening on click', 'us' ) => 'none' ),
				( ( $config['atts']['link'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['link'],
			),
			array(
				'param_name' => 'img_size',
				'heading' => __( 'Images Size', 'us' ),
				'description' => sprintf( __( 'To change the default image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . admin_url( 'options-media.php' ) . '">' . us_translate( 'Media Settings' ) . '</a>' ) . ' ' . sprintf( __( 'To add custom image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=us-theme-options#advanced' ) . '">' . __( 'Theme Options', 'us' ) . '</a>' ),
				'type' => 'dropdown',
				'value' => array_merge( array( us_translate( 'Default' ) => 'default' ), us_image_sizes_select_values() ),
				'std' => $config['atts']['img_size'],
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
vc_remove_element( 'vc_gallery' );
