<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Meta boxes
 *
 * @filter us_config_meta-boxes
 */

foreach ( $config as $key => $item ) {
	if ( $item['id'] == 'us_portfolio_settings' ) {
		unset( $config[$key]['fields']['us_tile_additional_image'] );
	}
	if ( $item['id'] == 'us_page_settings' ) {
		unset( $config[$key]['fields']['us_header_shadow'] );
	}
}

return $config;
