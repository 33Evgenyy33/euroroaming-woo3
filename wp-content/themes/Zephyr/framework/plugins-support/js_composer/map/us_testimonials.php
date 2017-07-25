<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_testimonials
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 * @param $config    ['content'] string Shortcode's default content
 */

$us_testimonial_categories = array();
$us_testimonial_categories_raw = get_categories(
	array(
		'taxonomy' => 'us_testimonial_category',
		'hierarchical' => 0,
	)
);
if ( $us_testimonial_categories_raw ) {
	foreach ( $us_testimonial_categories_raw as $testimonial_category_raw ) {
		if ( is_object( $testimonial_category_raw ) ) {
			$us_testimonial_categories[$testimonial_category_raw->name] = $testimonial_category_raw->slug;
		}
	}
}
vc_map(
	array(
		'base' => 'us_testimonials',
		'name' => __( 'Testimonials', 'us' ),
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 270,
		'params' => array(
			array(
				'param_name' => 'type',
				'heading' => __( 'Display items as', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Grid', 'us' ) => 'grid',
					__( 'Masonry', 'us' ) => 'masonry',
					__( 'Carousel', 'us' ) => 'carousel',
				),
				'std' => $config['atts']['type'],
				'admin_label' => TRUE,
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 130,
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
				),
				'std' => $config['atts']['columns'],
				'admin_label' => TRUE,
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 120,
			),
			array(
				'param_name' => 'orderby',
				'heading' => us_translate( 'Order' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'By date (newer first)', 'us' ) => 'date',
					__( 'By date (older first)', 'us' ) => 'date_asc',
					us_translate( 'Random' ) => 'rand',
				),
				'std' => $config['atts']['orderby'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 70,
			),
			array(
				'param_name' => 'items',
				'heading' => __( 'Items Quantity', 'us' ),
				'description' => __( 'If left blank, will output all the items', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['items'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 60,
			),
			array(
				'param_name' => 'ids',
				'heading' => __( 'Items for display', 'us' ),
				'description' => __( 'Select specific items which will be shown', 'us' ),
				'type' => 'autocomplete',
				'settings' => array(
					'multiple' => TRUE,
					'sortable' => FALSE,
					'unique_values' => TRUE,
				),
				'save_always' => TRUE,
				'weight' => 50,
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Items Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => '1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => '2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => '3',
					sprintf( __( 'Style %d', 'us' ), 4 ) => '4',
					sprintf( __( 'Style %d', 'us' ), 5 ) => '5',
					sprintf( __( 'Style %d', 'us' ), 6 ) => '6',
				),
				'std' => $config['atts']['style'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'text_size',
				'heading' => __( 'Items Text Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['text_size'],
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
			array(
				'param_name' => 'carousel_arrows',
				'type' => 'checkbox',
				'value' => array( __( 'Show Navigation Arrows', 'us' ) => TRUE ),
				( ( $config['atts']['carousel_arrows'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['carousel_arrows'],
				'dependency' => array( 'element' => 'type', 'value' => 'carousel' ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
			array(
				'param_name' => 'carousel_dots',
				'type' => 'checkbox',
				'value' => array( __( 'Show Navigation Dots', 'us' ) => TRUE ),
				( ( $config['atts']['carousel_dots'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['carousel_dots'],
				'dependency' => array( 'element' => 'type', 'value' => 'carousel' ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
			array(
				'param_name' => 'carousel_center',
				'type' => 'checkbox',
				'value' => array( __( 'Enable first item centering', 'us' ) => TRUE ),
				( ( $config['atts']['carousel_center'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['carousel_center'],
				'dependency' => array( 'element' => 'type', 'value' => 'carousel' ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
			array(
				'param_name' => 'carousel_slideby',
				'type' => 'checkbox',
				'value' => array( __( 'Slide by several items instead of one', 'us' ) => TRUE ),
				( ( $config['atts']['carousel_slideby'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['carousel_slideby'],
				'dependency' => array( 'element' => 'type', 'value' => 'carousel' ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
			array(
				'param_name' => 'carousel_autoplay',
				'type' => 'checkbox',
				'value' => array( __( 'Enable Auto Rotation', 'us' ) => TRUE ),
				( ( $config['atts']['carousel_autoplay'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['carousel_autoplay'],
				'dependency' => array( 'element' => 'type', 'value' => 'carousel' ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
			array(
				'param_name' => 'carousel_interval',
				'heading' => __( 'Auto Rotation Interval (in seconds)', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['carousel_interval'],
				'dependency' => array( 'element' => 'carousel_autoplay', 'not_empty' => TRUE ),
				'group' => __( 'Carousel Settings', 'us' ),
			),
		),
	)
);

if ( ! empty( $us_testimonial_categories ) ) {
	vc_add_param(
		'us_testimonials', array(
			'param_name' => 'categories',
			'heading' => __( 'Display Items of selected categories', 'us' ),
			'type' => 'checkbox',
			'value' => $us_testimonial_categories,
			'std' => $config['atts']['categories'],
			'weight' => 20,
		)
	);
}
