<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's thumbnails image sizes
 *
 * @filter us_config_image-sizes
 */

return array(

	// 600x600 - gallery large, blog layouts
	'tnail-1x1' => array(
		'width' => 600,
		'height' => 600,
		'crop' => TRUE,
	),
	// 350x350 - small image blog layout, gallery medium, person
	'tnail-1x1-small' => array(
		'width' => 350,
		'height' => 350,
		'crop' => TRUE,
	),

);
