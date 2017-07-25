<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_portfolio
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
$us_portfolio_categories = array();
$us_portfolio_categories_raw = get_categories(
	array(
		'taxonomy' => 'us_portfolio_category',
		'hierarchical' => 0,
	)
);
if ( $us_portfolio_categories_raw ) {
	foreach ( $us_portfolio_categories_raw as $portfolio_category_raw ) {
		if ( is_object( $portfolio_category_raw ) ) {
			$us_portfolio_categories[$portfolio_category_raw->name] = $portfolio_category_raw->slug;
		}
	}
}
vc_map(
	array(
		'base' => 'us_portfolio',
		'name' => __( 'Portfolio', 'us' ),
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 250,
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
				'weight' => 150,
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
				'weight' => 140,
			),
			array(
				'param_name' => 'orderby',
				'heading' => us_translate( 'Order' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'By date (newer first)', 'us' ) => 'date',
					__( 'By date (older first)', 'us' ) => 'date_asc',
					__( 'Alphabetically', 'us' ) => 'alpha',
					us_translate( 'Random' ) => 'rand',
				),
				'std' => $config['atts']['orderby'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 90,
			),
			array(
				'param_name' => 'items',
				'heading' => __( 'Items Quantity', 'us' ),
				'description' => __( 'If left blank, will output all the items', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['items'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 80,
			),
			array(
				'param_name' => 'pagination',
				'heading' => us_translate( 'Pagination' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => 'none',
					__( 'Regular pagination', 'us' ) => 'regular',
					__( 'Load More Button', 'us' ) => 'ajax',
					__( 'Infinite Scroll', 'us' ) => 'infinite',
				),
				'std' => $config['atts']['pagination'],
				'dependency' => array( 'element' => 'type', 'value' => array( 'grid', 'masonry' ) ),
				'weight' => 70,
			),
			array(
				'param_name' => 'ratio',
				'heading' => __( 'Items Aspect Ratio', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( '4:3 (landscape)', 'us' ) => '4x3',
					__( '3:2 (landscape)', 'us' ) => '3x2',
					__( '1:1 (square)', 'us' ) => '1x1',
					__( '2:3 (portrait)', 'us' ) => '2x3',
					__( '3:4 (portrait)', 'us' ) => '3x4',
					'16:9' => '16x9',
				),
				'std' => $config['atts']['ratio'],
				'dependency' => array( 'element' => 'type', 'value' => array( 'grid', 'carousel' ) ),
				'weight' => 60,
			),
			array(
				'param_name' => 'items_action',
				'heading' => __( 'Items action on click', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Navigate to item\'s page', 'us' ) => 'default',
					__( 'Open item\'s page in a popup', 'us' ) => 'lightbox_page',
					__( 'Open item\'s featured image in a popup', 'us' ) => 'lightbox_image',
				),
				'std' => $config['atts']['items_action'],
				'weight' => 20,
			),
			array(
				'param_name' => 'popup_width',
				'heading' => __( 'Popup Width', 'us' ),
				'description' => __( 'If left blank, popup will be stretched to the screen width', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['popup_width'],
				'dependency' => array( 'element' => 'items_action', 'value' => 'lightbox_page' ),
				'weight' => 10,
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Items Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => 'style_1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => 'style_2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => 'style_3',
					sprintf( __( 'Style %d', 'us' ), 4 ) => 'style_4',
					sprintf( __( 'Style %d', 'us' ), 5 ) => 'style_5',
					sprintf( __( 'Style %d', 'us' ), 6 ) => 'style_6',
					sprintf( __( 'Style %d', 'us' ), 7 ) => 'style_7',
					sprintf( __( 'Style %d', 'us' ), 8 ) => 'style_8',
					sprintf( __( 'Style %d', 'us' ), 9 ) => 'style_9',
					sprintf( __( 'Style %d', 'us' ), 10 ) => 'style_10',
					sprintf( __( 'Style %d', 'us' ), 11 ) => 'style_11',
					sprintf( __( 'Style %d', 'us' ), 12 ) => 'style_12',
					sprintf( __( 'Style %d', 'us' ), 13 ) => 'style_13',
					sprintf( __( 'Style %d', 'us' ), 14 ) => 'style_14',
					sprintf( __( 'Style %d', 'us' ), 15 ) => 'style_15',
					sprintf( __( 'Style %d', 'us' ), 16 ) => 'style_16',
					sprintf( __( 'Style %d', 'us' ), 17 ) => 'style_17',
					sprintf( __( 'Style %d', 'us' ), 18 ) => 'style_18',
				),
				'std' => $config['atts']['style'],
				'admin_label' => TRUE,
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'align',
				'heading' => __( 'Items Text Alignment', 'us' ),
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
				'param_name' => 'with_indents',
				'type' => 'checkbox',
				'value' => array( __( 'Add indents between items', 'us' ) => TRUE ),
				( ( $config['atts']['with_indents'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['with_indents'],
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'title_size',
				'heading' => __( 'Items Title Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['title_size'],
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'meta',
				'heading' => __( 'Items Meta', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					us_translate( 'Date' ) => 'date',
					us_translate( 'Categories' ) => 'categories',
					us_translate( 'Description' ) => 'desc',
				),
				'std' => $config['atts']['meta'],
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'meta_size',
				'heading' => __( 'Items Meta Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['meta_size'],
				'dependency' => array( 'element' => 'meta', 'not_empty' => TRUE ),
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'bg_color',
				'heading' => __( 'Items Background Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['bg_color'],
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'text_color',
				'heading' => __( 'Items Text Color', 'us' ),
				'type' => 'colorpicker',
				'std' => $config['atts']['text_color'],
				'group' => us_translate( 'Appearance' ),
			),
			array(
				'param_name' => 'img_size',
				'heading' => __( 'Images Size', 'us' ),
				'type' => 'dropdown',
				'value' => us_image_sizes_select_values(),
				'std' => $config['atts']['img_size'],
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
			array(
				'param_name' => 'filter',
				'type' => 'checkbox',
				'value' => array( __( 'Enable filtering by category', 'us' ) => 'category' ),
				( ( $config['atts']['filter'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['filter'],
				'group' => us_translate( 'Filter' ),
				'dependency' => array( 'element' => 'type', 'value' => array( 'grid', 'masonry' ) ),
			),
			array(
				'param_name' => 'filter_style',
				'heading' => __( 'Filter Bar Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					sprintf( __( 'Style %d', 'us' ), 1 ) => 'style_1',
					sprintf( __( 'Style %d', 'us' ), 2 ) => 'style_2',
					sprintf( __( 'Style %d', 'us' ), 3 ) => 'style_3',
				),
				'std' => $config['atts']['filter_style'],
				'group' => us_translate( 'Filter' ),
				'dependency' => array( 'element' => 'filter', 'not_empty' => TRUE ),
			),
		),

	)
);
if ( ! empty( $us_portfolio_categories ) ) {
	vc_add_param(
		'us_portfolio', array(
			'param_name' => 'categories',
			'heading' => __( 'Display Items of selected categories', 'us' ),
			'type' => 'checkbox',
			'value' => $us_portfolio_categories,
			'std' => $config['atts']['categories'],
			'weight' => 30,
		)
	);
}
vc_add_param(
	'us_portfolio', array(
		'param_name' => 'el_class',
		'heading' => us_translate( 'Extra class name', 'js_composer' ),
		'type' => 'textfield',
		'std' => $config['atts']['el_class'],
		'group' => us_translate( 'Appearance' ),
	)
);
