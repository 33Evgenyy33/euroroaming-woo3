<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_User_TotalSales extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$values = array();

		foreach ( $this->strategy->get_results() as $user_id ) {
			$value = false;

			if ( $total_per_currency = $this->column->get_raw_value( $user_id ) ) {

				// use only the first currency to sort
				if ( $amount = reset( $total_per_currency ) ) {
					$value = $amount;
				}
			}

			$values[ $user_id ] = $value;
		}

		return array(
			'ids' => $this->sort( $values ),
		);
	}

}
