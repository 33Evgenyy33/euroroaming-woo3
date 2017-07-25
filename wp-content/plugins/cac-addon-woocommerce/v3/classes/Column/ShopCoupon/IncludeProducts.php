<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_ShopCoupon_IncludeProducts extends AC_Column
	implements ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-include_products' );
		$this->set_label( __( 'Included products', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return 'product_ids';
	}

	// Display

	public function get_value( $post_id ) {
		$products = array();

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$products[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$value = implode( ', ', array_filter( $products ) );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_product_ids();
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_IncludeProducts( $this );
	}

}
