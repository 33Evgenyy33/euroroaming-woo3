<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_Product_ProductType extends ACP_Sorting_Model_Post_Taxonomy {

	public function get_sorting_vars() {
		$values = array();
		foreach ( $this->strategy->get_results() as $post_id ) {
			$values[ $post_id ] = $this->column->get_value( $post_id );
		}

		return array(
			'ids' => $this->sort( $values ),
		);
	}

}
