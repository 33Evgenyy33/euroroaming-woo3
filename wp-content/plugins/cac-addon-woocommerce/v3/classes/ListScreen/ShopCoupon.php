<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_ListScreen_ShopCoupon extends AC_ListScreen_Post {

	public function __construct() {
		parent::__construct( 'shop_coupon' );

		$this->set_group( 'woocommerce' );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_dir( ac_addon_wc()->get_dir() . 'classes/Column/ShopCoupon', ACA_WC::CLASS_PREFIX );
	}

}
