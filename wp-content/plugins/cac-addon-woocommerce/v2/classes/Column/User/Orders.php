<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_User_Orders extends AC_Column
	implements ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-user-orders' );
		$this->set_label( __( 'Orders', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $user_id ) {
		$order_ids = $this->get_raw_value( $user_id );

		if ( ! $order_ids ) {
			return false;
		}

		$values = array();

		foreach ( $order_ids as $id ) {
			$order = new WC_Order( $id );
			$values[ $order->get_status() ][] = '<div class="order order-' . esc_attr( $order->get_status() ) . '" data-tip="' . $this->get_order_tooltip( $order ) . '">' . ac_helper()->html->link( get_edit_post_link( $id ), $id ) . '</div>';
		}

		if ( ! $values ) {
			return false;
		}

		$output = '';

		foreach ( $values as $status => $orders ) {
			$output .= implode( '', $orders ) . "</br>";
		}

		return $output;
	}

	public function get_raw_value( $user_id ) {
		return ac_addon_wc_helper()->get_order_ids_by_user( $user_id, 'any' );
	}

	// Pro

	public function sorting() {
		return new ACA_WC_Sorting_User_Orders( $this );
	}

	// Common

	/**
	 * @param $order WC_Order
	 *
	 * @return string
	 */
	private function get_order_tooltip( $order ) {
		$tooltip = array(
			wc_get_order_status_name( $order->get_status() ),
		);

		if ( $item_count = $order->get_item_count() ) {
			$tooltip[] = $item_count . ' ' . __( 'items', 'codepress-admin-columns' );
		}

		if ( $total = $order->get_total() ) {
			$tooltip[] = get_woocommerce_currency_symbol( $order->get_order_currency() ) . wc_trim_zeros( number_format( $total, 2 ) );
		}

		$tooltip[] = date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) );

		return implode( ' | ', $tooltip );
	}

}
