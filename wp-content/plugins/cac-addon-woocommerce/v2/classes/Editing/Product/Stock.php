<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Stock extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type' => 'wc_stock'
		);
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product->is_type( 'simple' ) ) {
			return null;
		}

		$value = array();
		$value['stock_status'] = $product->is_in_stock() ? 'instock' : 'outofstock';

		if ( get_option( 'woocommerce_manage_stock' ) === 'yes' ) {
			$value['woocommerce_option_manage_stock'] = true;
			$value['manage_stock'] = $product->manage_stock;

			$stock = get_post_meta( $id, '_stock', true );
			$value['stock'] = ( $stock !== false ) ? $stock : '';
		}
		else {
			$value['woocommerce_option_manage_stock'] = false;
		}

		return (object) $value;
	}

	public function save( $id, $value ) {
		if ( get_option( 'woocommerce_manage_stock' ) == 'yes' ) {
			if ( $value['manage_stock'] == 'yes' ) {
				update_post_meta( $id, '_manage_stock', 'yes' );

				wc_update_product_stock_status( $id, wc_clean( $value['stock_status'] ) );
				wc_update_product_stock( $id, intval( $value['stock'] ) );

			}
			else {
				// Don't manage stock
				update_post_meta( $id, '_manage_stock', 'no' );
				update_post_meta( $id, '_stock', '' );

				wc_update_product_stock_status( $id, wc_clean( $value['stock_status'] ) );
			}
		}
		else {
			wc_update_product_stock_status( $id, wc_clean( $value['stock_status'] ) );
		}
	}

}
