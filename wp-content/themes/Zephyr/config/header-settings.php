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

return $config;
