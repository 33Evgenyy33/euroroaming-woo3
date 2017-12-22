<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order customer message (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopOrder_CustomerMessage extends ACP_Export_Model {

	public function get_value( $id ) {
		return wc_get_order( $id )->get_customer_note();
	}

}
