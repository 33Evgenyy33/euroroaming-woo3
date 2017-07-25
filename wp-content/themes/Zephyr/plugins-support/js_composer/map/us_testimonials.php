<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Overloading framework's VC shortcode mapping of: us_testimonials
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

global $us_template_directory;
require $us_template_directory . '/framework/plugins-support/js_composer/map/us_testimonials.php';

vc_update_shortcode_param(
	'us_testimonials', array(
	'param_name' => 'style',
	'value' => array(
		__( 'Card Style', 'us' ) => '1',
		__( 'Flat Style', 'us' ) => '2',
	),
)
);
