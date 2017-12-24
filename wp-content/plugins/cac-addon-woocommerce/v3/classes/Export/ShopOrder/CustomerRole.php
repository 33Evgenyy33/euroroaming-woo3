<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order customer role (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopOrder_CustomerRole extends ACP_Export_Model {

	public function get_value( $id ) {
		$user = wc_get_order( $id )->get_user();

		if ( empty( $user->roles ) ) {
			return '';
		}

		return implode( ', ', $user->roles );
	}

}
