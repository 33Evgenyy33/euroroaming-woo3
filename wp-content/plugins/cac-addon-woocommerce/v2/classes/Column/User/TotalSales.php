<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_User_TotalSales extends AC_Column
	implements ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-user-total-sales' );
		$this->set_label( __( 'Total Sales', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $user_id ) {
		$values = array();

		foreach ( $this->get_totals( $user_id ) as $currency => $total ) {
			$values[] = get_woocommerce_currency_symbol( $currency ) . wc_trim_zeros( number_format( $total, 2 ) );
		}

		if ( ! $values ) {
			return $this->get_empty_char();
		}

		return implode( ' | ', $values );
	}

	public function get_raw_value( $user_id ) {
		return $this->get_totals( $user_id );
	}

	// Pro

	public function sorting() {
		return new ACA_WC_Sorting_User_TotalSales( $this );
	}

	// Common

	private function get_totals( $user_id ) {
		$totals = array();

		foreach ( ac_addon_wc_helper()->get_orders_by_user( $user_id ) as $order ) {
			if ( ! $order->get_total() ) {
				continue;
			}

			$currency = $order->get_order_currency();

			if ( ! isset( $totals[ $currency ] ) ) {
				$totals[ $currency ] = 0;
			}
			$totals[ $currency ] += $order->get_total();
		}

		return $totals;
	}

}
