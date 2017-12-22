<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_BackordersAllowed $column
 */
class ACA_WC_Sorting_Product_BackordersAllowed extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$post_ids = array();
		foreach ( $this->strategy->get_results() as $post_id ) {
			$post_ids[ $post_id ] = $this->column->get_backorders( $post_id );
		}

		return array(
			'ids' => $this->sort( $post_ids ),
		);
	}

}
