<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_TransactionID extends AC_Column_Meta
	implements ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-transaction_id' );
		$this->set_label( __( 'Transaction ID', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_transaction_id';
	}

	// Display

	public function get_value( $post_id ) {
		$transaction_id = $this->get_raw_value( $post_id );

		if ( ! $transaction_id ) {
			return $this->get_empty_char();
		}

		return $transaction_id;
	}

	// Sorting

	public function sorting() {
		return new ACP_Sorting_Model_Meta( $this );
	}

}
