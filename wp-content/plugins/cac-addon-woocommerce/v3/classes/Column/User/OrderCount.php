<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_User_OrderCount extends AC_Column
	implements ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-user-order_count' );
		$this->set_label( __( 'Number of orders', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $user_id ) {
		return $this->get_raw_value( $user_id );
	}

	public function get_raw_value( $user_id ) {
		return count( ac_addon_wc_helper()->get_order_ids_by_user( $user_id, 'any' ) );
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

}
