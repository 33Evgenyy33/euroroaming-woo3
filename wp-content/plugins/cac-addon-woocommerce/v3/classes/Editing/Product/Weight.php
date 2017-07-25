<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Weight extends ACP_Editing_Model {

	public function get_edit_value( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( $product->is_virtual() ) {
			return null;
		}

		return $product->get_weight();
	}

	public function get_view_settings() {
		return array(
			'type' => 'float',
			'js'   => array(
				'inputclass' => 'small-text',
			),
		);
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_weight( $value );
		$product->save();
	}

}
