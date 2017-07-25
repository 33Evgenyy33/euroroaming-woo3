<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopCoupon_Amount $column
 */
class ACA_WC_Sorting_ShopCoupon_Amount extends ACP_Sorting_Model {

	public function __construct( ACA_WC_Column_ShopCoupon_Amount $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function get_sorting_vars() {
		$post_ids = array();

		foreach ( $this->strategy->get_results() as $post_id ) {
			$post_ids[ $post_id ] = $this->column->get_coupon_amount( $post_id );
		}

		return array(
			'ids' => $this->sort( $post_ids ),
		);
	}

}
