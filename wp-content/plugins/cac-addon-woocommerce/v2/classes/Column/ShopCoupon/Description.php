<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Description extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_type( 'description' );
		$this->set_original( true );
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		$raw_value = ac_helper()->post->get_raw_field( 'post_excerpt', $post_id );

		return $raw_value ? $raw_value : '';
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Description( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

}
