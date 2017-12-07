<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_ReviewsEnabled extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-reviews_enabled' );
		$this->set_label( __( 'Reviews enabled', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $id ) {
		return ac_helper()->icon->yes_or_no( 'open' === $this->get_raw_value( $id ) );
	}

	public function get_raw_value( $id ) {
		return $this->get_comment_status( $id );
	}

	public function is_valid() {
		return post_type_supports( $this->get_post_type(), 'comments' );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_ReviewsEnabled( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_ReviewsEnabled( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	// Common

	private function get_comment_status( $id ) {
		return AC()->helper()->post->get_raw_field( 'comment_status', $id );
	}

}
