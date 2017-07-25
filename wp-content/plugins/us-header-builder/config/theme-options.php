<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header Theme Options changes.
 *
 * @var $config array Framework- and theme-defined theme options config
 *
 * @return array Changed config
 */

$config = us_array_merge_insert(
	$config, array(
		'hb' => array(
			'title' => __( 'Header Options', 'us' ),
			'icon' => $us_template_directory_uri . '/framework/admin/img/usof/header',
			'new' => TRUE,
			'fields' => array(
			
				// Header Defaults
				'h_header_1' => array(
					'title' => __( 'Defaults', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'header_id' => array(
					'type' => 'select',
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'options' => ushb_get_existing_headers(),
					'classes' => 'width_full desc_4',
				),

				// Header for Portfolio Pages
				'h_header_2' => array(
					'title' => __( 'Portfolio Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),
				'header_portfolio_defaults' => array(
					'type' => 'switch',
					'text' => __( 'Use Defaults', 'us' ),
					'std' => 1,
					'classes' => 'width_full',
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),
				'header_portfolio_id' => array(
					'type' => 'select',
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'options' => ushb_get_existing_headers(),
					'classes' => 'width_full desc_4',
					'place_if' => ( $usof_enable_portfolio == 1 ),
					'show_if' => array( 'header_portfolio_defaults', '=', '0' ),
				),

				// Header for Posts
				'h_header_3' => array(
					'title' => us_translate_x( 'Posts', 'post type general name' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'header_post_defaults' => array(
					'type' => 'switch',
					'text' => __( 'Use Defaults', 'us' ),
					'std' => 1,
					'classes' => 'width_full',
				),
				'header_post_id' => array(
					'type' => 'select',
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'options' => ushb_get_existing_headers(),
					'classes' => 'width_full desc_4',
					'show_if' => array( 'header_post_defaults', '=', '0' ),
				),

				// Header for Archive, Search Results Pages
				'h_header_4' => array(
					'title' => __( 'Archive, Search Results Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'header_archive_defaults' => array(
					'type' => 'switch',
					'text' => __( 'Use Defaults', 'us' ),
					'std' => 1,
					'classes' => 'width_full',
				),
				'header_archive_id' => array(
					'type' => 'select',
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'options' => ushb_get_existing_headers(),
					'classes' => 'width_full desc_4',
					'show_if' => array( 'header_archive_defaults', '=', '0' ),
				),

				// Header for Shop and Product Pages
				'h_header_5' => array(
					'title' => __( 'Shop and Product Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'header_shop_defaults' => array(
					'type' => 'switch',
					'text' => __( 'Use Defaults', 'us' ),
					'std' => 1,
					'classes' => 'width_full',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'header_shop_id' => array(
					'type' => 'select',
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'options' => ushb_get_existing_headers(),
					'classes' => 'width_full desc_4',
					'place_if' => class_exists( 'woocommerce' ),
					'show_if' => array( 'header_shop_defaults', '=', '0' ),
				),
				
			),
		),
	), 'after', 'colors'
);

// Hiding the sections that are overloaded by Header Builder
$config['header']['place_if'] = FALSE;

return $config;
