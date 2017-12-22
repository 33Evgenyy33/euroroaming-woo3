<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Visibility $column
 */
class ACA_WC_Editing_Product_Visibility extends ACP_Editing_Model {

	public function __construct( ACA_WC_Column_Product_Visibility $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return $data = array(
			'type'    => 'select',
			'options' => wc_get_product_visibility_options(),
		);
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_catalog_visibility( $value );
		$product->save();
	}

}
