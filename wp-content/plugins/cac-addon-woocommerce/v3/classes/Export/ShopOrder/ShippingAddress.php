<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order shipping address (default column) exportability model
 *
 * @since NEWVERSION
 */
class ACA_WC_Export_ShopOrder_ShippingAddress extends ACP_Export_Model {

	public function get_value( $id ) {
		$address = wc_get_order( $id )->get_formatted_shipping_address();

		return preg_replace( '#<br\s*/?>#i', ', ', $address );
	}

}
