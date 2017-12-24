<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Dimensions $column
 */
class ACA_WC_Sorting_Product_Dimensions extends ACP_Sorting_Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function get_sorting_vars() {
		$values = array();
		foreach ( $this->strategy->get_results() as $post_id ) {
			$dimensions = $this->column->dimensions_used( $this->column->get_dimensions( $post_id ) );
			$values[ $post_id ] = $dimensions ? array_product( $dimensions ) : false;
		}

		return array(
			'ids' => $this->sort( $values ),
		);
	}

}
