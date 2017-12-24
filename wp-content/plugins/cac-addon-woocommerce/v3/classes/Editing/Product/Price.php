<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Price extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type' => 'wc_price',
		);
	}

	/**
	 * @return array
	 */
	private function excluded_types() {
		return array( 'variable', 'grouped' );
	}

	/**
	 * @param int $id
	 *
	 * @return null|object
	 */
	public function get_edit_value( $id ) {
		$product = $this->get_editable_product( $id );

		if ( ! $product ) {
			return null;
		}

		return (object) array(
			'regular_price'         => $product->get_regular_price(),
			'sale_price'            => $product->get_sale_price(),
			'sale_price_dates_from' => $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->date( 'Y-m-d' ) : '',
			'sale_price_dates_to'   => $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->date( 'Y-m-d' ) : '',
		);
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Product|false
	 */
	private function get_editable_product( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product ) {
			return false;
		}

		if ( $product->is_type( $this->excluded_types() ) ) {
			return false;
		}

		return $product;
	}

	/**
	 * @param int   $id
	 * @param array $value
	 */
	public function save( $id, $value ) {
		if ( ! is_array( $value ) || empty( $value ) ) {
			return;
		}

		$product = $this->get_editable_product( $id );

		if ( ! $product ) {
			return;
		}

		$product->set_regular_price( $value['regular_price'] );
		$product->set_sale_price( $value['sale_price'] );

		$product->set_date_on_sale_from( $value['sale_price_dates_from'] );
		$product->set_date_on_sale_to( $value['sale_price_dates_to'] );

		$product->save();
	}

}
