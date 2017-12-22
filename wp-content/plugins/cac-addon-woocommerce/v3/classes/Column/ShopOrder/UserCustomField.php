<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_UserCustomField extends AC_Column_CustomField
	implements ACP_Export_Column {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-order-usermeta' );
		$this->set_label( __( 'User - Custom Field', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_type() {
		return 'user';
	}

	// Display

	public function get_raw_value( $order_id ) {
		$order = wc_get_order( $order_id );

		return parent::get_raw_value( $order->get_user_id() );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_UserMeta( $this ) );
		$this->add_setting( new AC_Settings_Column_BeforeAfter( $this ) );
	}

	public function export() {
		return new ACP_Export_Model_Disabled( $this );
	}

}
