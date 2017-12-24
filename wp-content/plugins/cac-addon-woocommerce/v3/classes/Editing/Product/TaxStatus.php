<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_TaxStatus $column
 */
class ACA_WC_Editing_Product_TaxStatus extends ACP_Editing_Model {

	public function __construct( ACA_WC_Column_Product_TaxStatus $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$settings = array(
			'type'    => 'select',
			'options' => $this->column->get_tax_status(),
		);

		return $settings;
	}

	public function save( $id, $value ) {
		$product = wc_get_product( $id );
		$product->set_tax_status( $value );
		$product->save();
	}

}
