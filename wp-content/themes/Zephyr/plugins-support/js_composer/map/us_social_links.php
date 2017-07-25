<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Overloading framework's VC shortcode mapping of: us_social_links
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

global $us_template_directory;
require $us_template_directory . '/framework/plugins-support/js_composer/map/us_social_links.php';

vc_remove_param( 'us_social_links', 'style' );
vc_update_shortcode_param(
	'us_social_links', array(
	'param_name' => 'color',
	'value' => array(
		__( 'Colored', 'us' ) => 'brand',
		__( 'Desaturated', 'us' ) => 'desaturated',
		__( 'Colored Inverted', 'us' ) => 'brand_inv',
		__( 'Desaturated Inverted', 'us' ) => 'desaturated_inv',
	),
	'edit_field_class' => 'vc_col-sm-12',
)
);
