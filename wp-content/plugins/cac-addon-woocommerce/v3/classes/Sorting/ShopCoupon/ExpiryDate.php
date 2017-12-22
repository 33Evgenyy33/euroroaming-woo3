<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_ShopCoupon_ExpiryDate extends ACP_Sorting_Model_Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

}
