<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Variation $column
 */
class ACA_WC_Sorting_Product_Variation extends ACP_Sorting_Model {

	public function __construct( ACA_WC_Column_Product_Variation $column ) {
		parent::__construct( $column );
	}

	public function get_sorting_vars() {
		$ids = array();
		foreach ( $this->strategy->get_results() as $product_id ) {
			$ids[ $product_id ] = count( $this->column->get_variation_ids( $product_id ) );
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}

}
