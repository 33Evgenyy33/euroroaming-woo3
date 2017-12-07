<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce product SKU (default column) exportability model
 *
 * @since NEWVERSION
 */
class ACA_WC_Export_Product_SKU extends ACP_Export_Model {

	public function get_value( $id ) {
		return wc_get_product( $id )->get_sku();
	}

}
