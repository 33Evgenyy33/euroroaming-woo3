<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_FreeShipping extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-free_shipping' );
		$this->set_label( __( 'Free shipping', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return 'free_shipping';
	}

	// Display

	public function get_value( $post_id ) {
		$free_shipping = $this->get_raw_value( $post_id );

		if ( 'yes' == $free_shipping ) {
			return ac_helper()->icon->yes( __( 'The free shipping method must be enabled with the &quot;must use coupon&quot; setting.', 'codepress-admin-columns' ) );
		}

		return ac_helper()->icon->no( $free_shipping );;
	}

	public function get_raw_value( $id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $id );

		return $coupon->enable_free_shipping() ? 'yes' : 'no';
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_ShopCoupon_FreeShipping( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_FreeShipping( $this );
	}

}
