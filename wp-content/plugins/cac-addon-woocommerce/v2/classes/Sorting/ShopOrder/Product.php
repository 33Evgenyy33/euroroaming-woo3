<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_Product $column
 */
class ACA_WC_Sorting_ShopOrder_Product extends ACP_Sorting_Model {

	public function __construct( ACA_WC_Column_ShopOrder_Product $column ) {
		parent::__construct( $column );
	}

	public function get_sorting_vars() {
		$values = array();

		foreach ( $this->strategy->get_results() as $order_id ) {
			$value = false;

			if ( $product_ids = ac_addon_wc_helper()->get_product_ids_by_order( $order_id ) ) {
				$value = $this->column->get_setting( 'post' )->format( $product_ids[0], $product_ids[0] );
			}

			$values[ $order_id ] = $value;
		}

		return array(
			'ids' => $this->sort( $values ),
		);

	}

}
