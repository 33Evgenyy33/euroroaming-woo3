<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Crosssells extends AC_Column_Meta
	implements ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-crosssells' );
		$this->set_label( __( 'Cross Sells', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_crosssell_ids';
	}

	// Display

	public function get_value( $post_id ) {
		$crosssells = array();

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			if ( $title = get_the_title( $id ) ) {
				$crosssells[] = ac_helper()->html->link( get_edit_post_link( $id ), $title );
			}
		}

		return implode( ', ', $crosssells );
	}

	public function get_raw_value( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product->get_cross_sells();
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Crosssells( $this );
	}

}
