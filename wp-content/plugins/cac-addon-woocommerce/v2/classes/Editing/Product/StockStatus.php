<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_StockStatus $column
 */
class ACA_WC_Editing_Product_StockStatus extends ACP_Editing_Model {

	public function __construct( ACA_WC_Column_Product_StockStatus $column ) {
		parent::__construct( $column );
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product->is_type( 'simple' ) ) {
			return null;
		}

		return $this->column->get_raw_value( $id );
	}

	public function get_view_settings() {
		return array(
			'type'    => 'togglable',
			'options' => array( 'outofstock', 'instock' ),
		);
	}

	public function save( $id, $value ) {
		wc_update_product_stock_status( $id, wc_clean( $value ) );
	}

}
