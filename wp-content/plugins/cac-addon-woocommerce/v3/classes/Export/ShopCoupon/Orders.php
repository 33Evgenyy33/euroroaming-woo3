<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.2.1
 */
class ACA_WC_Export_ShopCoupon_Orders extends ACP_Export_Model {

	public function get_value( $id ) {
		return implode( ', ', $this->column->get_raw_value( $id ) );
	}

}
