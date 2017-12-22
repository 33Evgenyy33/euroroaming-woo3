<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.2.1
 */
class ACA_WC_Export_ShopOrder_ProductCustomField extends ACP_Export_Model {

	public function get_value( $id ) {
		$collection = $this->column->get_raw_value( $id );

		if ( ! $collection instanceof AC_Collection ) {
			return false;
		}

		return $collection->implode( ', ' );
	}

}
