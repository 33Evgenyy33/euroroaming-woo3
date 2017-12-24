<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_UserMeta extends AC_Column_CustomField {

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
		$user_id = get_post_meta( $order_id, '_customer_user', true );

		return parent::get_raw_value( $user_id );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_UserMeta( $this ) );
		$this->add_setting( new AC_Settings_Column_BeforeAfter( $this ) );
	}

}
