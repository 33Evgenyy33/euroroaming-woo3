<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Stock extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type' => 'wc_stock',
		);
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product->is_type( 'simple' ) ) {
			return null;
		}

		$data = new stdClass();

		$data->stock_status = $product->get_stock_status();
		$data->woocommerce_option_manage_stock = false;
		$data->stock = false;

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$data->woocommerce_option_manage_stock = true;
			$data->manage_stock = $product->get_manage_stock() ? 'yes' : 'no';
			$data->stock = $product->get_stock_quantity();
		}

		return $data;
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		$product->set_stock_status( $value['stock_status'] );

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$product->set_manage_stock( false );

			if ( 'yes' === $value['manage_stock'] ) {
				$product->set_manage_stock( true );
				$product->set_stock_quantity( $value['stock'] );
			}
		}

		$product->save();
	}

}
