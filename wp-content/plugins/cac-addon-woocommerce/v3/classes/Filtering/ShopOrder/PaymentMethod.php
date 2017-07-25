<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_ShopOrder_PaymentMethod extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		$options = array();

		/* @var WC_Payment_Gateway[] $gateways */
		$gateways = WC()->payment_gateways()->payment_gateways();

		foreach ( $gateways as $gateway ) {
			if ( 'yes' == $gateway->enabled ) {
				$options[ $gateway->get_title() ] = $gateway->get_title();
			}
		}

		return array(
			'empty_option' => true,
			'options'      => $options,
		);
	}

}
