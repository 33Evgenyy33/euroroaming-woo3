<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_User_TotalSales $column
 * @since 2.2.1
 */
class ACA_WC_Export_User_TotalSales extends ACP_Export_Model {

	public function get_value( $id ) {
		$totals = $this->column->get_totals( $id );

		if ( ! $totals ) {
			return false;
		}

		$values = array();

		foreach( $totals as $currency => $amount ) {
			$values[] = get_woocommerce_currency_symbol( $currency ) . $amount;
		}

		return implode( ',', $values );
	}

}
