<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_BackordersAllowed extends ACP_Editing_Model {

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		// Only items that have manage stock enabled can have back orders
		if ( ! $product->managing_stock() ) {
			return null;
		}

		return $product->get_backorders();
	}

	public function get_view_settings() {
		return array(
			'type'    => 'select',
			'options' => $this->get_backorder_options(),
		);
	}

	public function save( $id, $value ) {
		if ( ! in_array( $value, array_keys( $this->get_backorder_options() ) ) ) {
			return;
		}

		$product = wc_get_product( $id );
		$product->set_backorders( $value );
		$product->save();
	}

	private function get_backorder_options() {
		return array(
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' ),
		);
	}

}
