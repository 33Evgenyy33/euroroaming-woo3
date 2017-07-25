<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_ShopCoupon_IncludeProducts extends AC_Column_Meta
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
			if ( $title = get_the_title( $id ) ) {
				$products[] = ac_helper()->html->link( get_edit_post_link( $id ), $title );
			}
		}

		return implode( ', ', $products );
	}

	public function get_raw_value( $id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $id );

		return $coupon->product_ids;
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_IncludeProducts( $this );
	}

}
