<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce product variation (default column) exportability model
 *
 * @since NEWVERSION
 */
class ACA_WC_Export_Product_Variation extends ACP_Export_Model {

	public function get_value( $id ) {
		return count( $this->column->get_raw_value( $id ) );
	}

}
