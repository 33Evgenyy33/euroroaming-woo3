<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Dimensions extends ACP_Editing_Model {

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_virtual() ) {
			return null;
		}

		return (object) parent::get_edit_value( $id );
	}

	public function get_view_settings() {
		return array(
			'type' => 'dimensions',
		);
	}

	public function save( $id, $value ) {
		if ( is_array( $value ) && isset( $value['length'] ) && isset( $value['width'] ) && isset( $value['height'] ) ) {
			$product = wc_get_product( $id );

			if ( ! $product->is_virtual() ) {

				$product->set_length( $value['length'] );
				$product->set_width( $value['width'] );
				$product->set_height( $value['height'] );

				$product->save();
			}
		}
	}

}
