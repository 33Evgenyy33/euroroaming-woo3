<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce product stock (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_Product_Stock extends ACP_Export_Model {

	public function get_value( $id ) {
		$product = wc_get_product( $id );

		$stock = $product->get_stock_status();

		if ( $product->managing_stock() ) {
			$stock .= ', ' . $product->get_stock_quantity();
		}

		return $stock;
	}

}
