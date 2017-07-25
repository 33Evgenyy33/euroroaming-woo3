<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Dimensions extends ACP_Editing_Model {

	public function get_edit_value( $id ) {

		// Ignore virtual products
		if ( 'yes' === get_post_meta( $id, '_virtual', true ) ) {
			return null;
		};

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
				update_post_meta( $id, '_length', ( $value === '' ) ? '' : wc_format_decimal( $value['length'] ) );
				update_post_meta( $id, '_width', ( $value === '' ) ? '' : wc_format_decimal( $value['width'] ) );
				update_post_meta( $id, '_height', ( $value === '' ) ? '' : wc_format_decimal( $value['height'] ) );
			}
		}
	}

}
