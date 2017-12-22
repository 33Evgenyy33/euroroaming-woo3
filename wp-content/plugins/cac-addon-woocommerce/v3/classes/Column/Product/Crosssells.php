<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Crosssells extends AC_Column
	implements ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-crosssells' );
		$this->set_label( __( 'Cross Sells', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $post_id ) {
		$crosssells = array();

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$crosssells[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$value = implode( ', ', array_filter( $crosssells ) );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_cross_sell_ids();
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Crosssells( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
