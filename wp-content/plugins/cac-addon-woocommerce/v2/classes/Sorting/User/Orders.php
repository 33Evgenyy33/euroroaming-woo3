<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_User_Orders extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$values = array();

		foreach ( $this->strategy->get_results() as $id ) {
			$values[ $id ] = count( $this->column->get_raw_value( $id ) );
		}

		return array(
			'ids' => $this->sort( $values )
		);
	}

}
