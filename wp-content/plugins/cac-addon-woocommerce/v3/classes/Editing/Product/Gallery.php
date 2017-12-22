<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Gallery extends ACP_Editing_Model {

	public function get_view_settings() {
		$data = array(
			'type'         => 'media',
			'clear_button' => true,
			'attachment'   => array(
				'library' => array(
					'type' => 'image',
				),
			),
			'multiple'     => true,
			'store_values' => true,
		);

		return $data;
	}

	public function get_edit_value( $id ) {
		return wc_get_product( $id )->get_gallery_image_ids();
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );

		$product->set_gallery_image_ids( $value );
		$product->save();
	}

}
