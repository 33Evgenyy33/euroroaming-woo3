<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_ShopOrder_CustomerMessage extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$ids = array();

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = $this->get_message( $id );
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}

	private function get_message( $id ) {
		$order = wc_get_order( $id );

		return $order->get_customer_note();
	}

}
