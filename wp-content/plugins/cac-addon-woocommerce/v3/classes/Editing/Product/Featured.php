<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Featured extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type'    => 'togglable',
			'options' => array( 'no', 'yes' ),
		);
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		return $product->get_featured();
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_featured( $value );
		$product->save();
	}

}
