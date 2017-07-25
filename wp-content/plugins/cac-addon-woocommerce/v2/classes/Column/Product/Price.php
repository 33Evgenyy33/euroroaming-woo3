<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_Price extends AC_Column_Meta
	implements ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'price' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Meta

	public function get_meta_key() {
		return '_price';
	}

	// Display

	public function get_raw_value( $post_id ) {
		return $this->get_prices( $post_id );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_Price( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Price( $this );
	}

	// Common

	private function get_prices( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( $product->is_type( array( 'variable', 'grouped' ) ) ) {
			return false;
		}

		$sale_from = $product->sale_price_dates_from;
		$sale_to = $product->sale_price_dates_to;

		return array(
			'regular_price'         => $product->get_regular_price(),
			'sale_price'            => $product->get_sale_price(),
			'sale_price_dates_from' => $sale_from ? date( 'Y-m-d', $sale_from ) : '',
			'sale_price_dates_to'   => $sale_to ? date( 'Y-m-d', $sale_to ) : '',
		);
	}

}
