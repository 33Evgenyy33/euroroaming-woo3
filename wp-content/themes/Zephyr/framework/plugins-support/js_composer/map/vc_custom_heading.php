<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_custom_heading
 *
 * @var $shortcode string Current shortcode name
 * @var $config    array Shortcode's config
 */
vc_remove_param( 'vc_custom_heading', 'css_animation' );

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_custom_heading', array( 'weight' => 70 ) );
