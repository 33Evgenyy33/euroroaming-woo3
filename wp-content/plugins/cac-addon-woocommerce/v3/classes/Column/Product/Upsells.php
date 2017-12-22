<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Upsells extends AC_Column
	implements ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-upsells' );
		$this->set_label( __( 'Upsells', 'codepress-admin-columns' ) );
	}

	// Display

	public function get_value( $post_id ) {
		$upsells = array();

		foreach ( $this->get_raw_value( $post_id ) as $id ) {
			$upsells[] = ac_helper()->html->link( get_edit_post_link( $id ), get_the_title( $id ) );
		}

		$upsells = array_filter( $upsells );

		if ( ! $upsells ) {
			return $this->get_empty_char();
		}

		return implode( ', ', $upsells );
	}

	// Editing

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_upsell_ids();
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Upsells( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
