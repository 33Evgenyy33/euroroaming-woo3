<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_ExpiryDate extends ACP_Editing_Model_Meta {

	public function get_view_settings() {
		return array(
			'type' => 'date',
		);
	}

	public function get_edit_value( $id ) {
		$raw = get_post_meta( $id, $this->column->get_meta_key(), true );

		return $raw ? date( 'Ymd', strtotime( $raw ) ) : '';
	}

	public function save( $id, $value ) {
		$date = $value ? date( 'Y-m-d', strtotime( $value ) ) : '';

		parent::save( $id, $date );
	}

}
