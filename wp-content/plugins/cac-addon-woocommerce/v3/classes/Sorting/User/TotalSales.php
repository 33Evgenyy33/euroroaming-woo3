<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_User_TotalSales extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$values = array();

		foreach ( $this->strategy->get_results() as $user_id ) {
			$totals = $this->column->get_raw_value( $user_id );

			$values[ $user_id ] = $totals ? array_shift( array_values( $totals ) ) : false;
		}

		return array(
			'ids' => $this->sort( $values )
		);
	}

}
