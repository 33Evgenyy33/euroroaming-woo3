<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_StockStatus extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-stock-status' );
		$this->set_label( __( 'Stock status', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_stock_status';
	}

	// Display

	public function get_value( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return false;
		}

		$data_tip = '';

		if ( ! $product->is_type( $this->get_supported_types() ) ) {
			$data_tip = ' <em>' . sprintf( __( 'Stock status editing is only supported for %s products.', 'codepress-admin-columns' ), ac_helper()->string->enumeration_list( $this->get_supported_types(), 'and' ) ) . '</em>';
		}

		switch ( $this->get_raw_value( $post_id ) ) {

			case 'instock' :
				$value = ac_helper()->icon->yes( __( 'In stock', 'codepress-admin-columns' )  . '.' . $data_tip );

				break;
			case 'outofstock' :
				$value = ac_helper()->icon->no( __( 'Out of stock', 'codepress-admin-columns' ) . '.' . $data_tip );

				break;

			default :
				$value = $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product->stock_status;
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_StockStatus( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_StockStatus( $this );
	}

	// Common

	public function get_supported_types() {
		return array( 'simple' );
	}

}
