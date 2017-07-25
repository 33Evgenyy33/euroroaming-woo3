<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header Builder Meta Boxes changes.
 *
 * @var $config array Framework- and theme-defined metaboxes config
 *
 * @return array Changed config
 */

foreach ( $config as &$cfg ) {
	if ( $cfg['id'] === 'us_page_settings' ) {
		$cfg['fields'] = us_array_merge_insert(
			$cfg['fields'], array(
				'us_header_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'description' => sprintf( __( 'You can edit selected header or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_header" target="_blank">' . _x( 'Headers', 'site top area', 'us' ) . '</a>' ),
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'' => __( 'Default (from Theme Options)', 'us' ),
						), ushb_get_existing_headers()
					),
					'show_if' => array( 'us_header', '=', 'custom' ),
				),
				'us_header_sticky_override' => array(
					'title' => __( 'Sticky Header', 'us' ),
					'type' => 'switch',
					'text' => __( 'Override this setting', 'us' ),
					'std' => 0,
					'show_if' => array( 'us_header', '=', 'custom' ),
				),
				'us_header_sticky' => array(
					'type' => 'checkboxes',
					'options' => array(
						'default' => __( 'On Desktops', 'us' ),
						'tablets' => __( 'On Tablets', 'us' ),
						'mobiles' => __( 'On Mobiles', 'us' ),
					),					
					'std' => array(),
					'classes' => 'for_above',
					'show_if' => array(
						array( 'us_header', '=', 'custom' ),
						'and',
						array( 'us_header_sticky_override', '=', '1' ),
					),
				),			
				'us_header_transparent_override' => array(
					'title' => __( 'Transparent Header', 'us' ),
					'type' => 'switch',
					'text' => __( 'Override this setting', 'us' ),
					'std' => 0,
					'show_if' => array( 'us_header', '=', 'custom' ),
				),
				'us_header_transparent' => array(
					'type' => 'checkboxes',
					'options' => array(
						'default' => __( 'On Desktops', 'us' ),
						'tablets' => __( 'On Tablets', 'us' ),
						'mobiles' => __( 'On Mobiles', 'us' ),
					),					
					'std' => array(),
					'classes' => 'for_above',
					'show_if' => array(
						array( 'us_header', '=', 'custom' ),
						'and',
						array( 'us_header_transparent_override', '=', '1' ),
					),
				),
				'us_header_shadow' => array(
					'title' => __( 'Header Shadow', 'us' ),
					'type' => 'switch',
					'text' => __( 'Remove header shadow', 'us' ),
					'std' => 0,
					'show_if' => array( 'us_header', '=', 'custom' ),
				),
			), 'after', 'us_header'
		);
		break;
	}
}

return $config;