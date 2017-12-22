<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_BackordersAllowed extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-backorders_allowed' );
		$this->set_label( __( 'Backorders Allowed', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_backorders';
	}

	// Display

	public function get_value( $post_id ) {
		$backorders_status = $this->get_raw_value( $post_id );

		$value = '';

		switch ( $backorders_status ) {
			case 'no' :
				$value = ac_helper()->icon->no( __( 'No' ) );
				break;
			case 'yes' :
				$value = ac_helper()->icon->yes( __( 'Yes' ) );
				break;
			case 'notify' :
				$icon_email = ac_helper()->icon->dashicon( array( 'icon' => 'email-alt' ) );

				$value = '<span data-tip="' . esc_attr( __( 'Yes, but notify customer', 'woocommerce' ) ) . '">' . ac_helper()->icon->yes() . $icon_email . '</span>';
				break;
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return $this->get_backorders( $post_id );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_BackordersAllowed( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_Product_BackordersAllowed( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_BackordersAllowed( $this );
	}

	// Common

	public function get_backorders( $id ) {
		$product = wc_get_product( $id );

		return $product->backorders;
	}

}
