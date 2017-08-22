<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header Options used by Header Builder plugin.
 * Options and elements' fields are described in USOF-style format.
 *
 * @var $config array Framework-based theme options config
 *
 * @return array Changed config
 */

unset( $config['options']['global']['shadow'] );
unset( $config['elements']['menu']['params']['hover_effect'] );

$config['elements']['btn']['params']['style']['options'] = array(
	'raised' => __( 'Raised', 'us' ),
	'flat' => __( 'Flat', 'us' ),
);
$config['elements']['btn']['params']['style']['std'] = 'raised';

return $config;
