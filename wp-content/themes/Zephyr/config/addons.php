<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Addons configuration
 *
 * @filter us_config_addons
 */

foreach ( $config as $key => $addon ) {
	if ( $addon['name'] == 'The Events Calendar' ) {
		unset( $config[$key] );
	}
}

return $config;
