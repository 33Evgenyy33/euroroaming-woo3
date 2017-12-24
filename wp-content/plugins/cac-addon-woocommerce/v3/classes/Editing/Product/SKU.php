<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_SKU extends ACP_Editing_Model {

	public function get_edit_value( $id ) {
		return wc_get_product( $id )->get_sku();
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		try {
			$product->set_sku( $value );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage() );
		}

		return $product->save();
	}

}
