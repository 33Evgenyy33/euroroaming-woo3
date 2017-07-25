<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Sorting_ShopCoupon_Type extends ACP_Sorting_Model {

	public function get_sorting_vars() {
		$post_ids = array();
		foreach ( $this->strategy->get_results() as $post_id ) {
			$post_ids[ $post_id ] = $this->get_coupon_type_label( $post_id );
		}

		return array(
			'ids' => $this->sort( $post_ids ),
		);
	}

	private function get_coupon_type_label( $id ) {
		$coupon_types = wc_get_coupon_types();
		$coupon_type = $this->column->get_raw_value( $id );

		if ( isset( $coupon_types[ $coupon_type ] ) ) {
			return false;
		}

		return $coupon_types[ $coupon_type ];
	}

}
