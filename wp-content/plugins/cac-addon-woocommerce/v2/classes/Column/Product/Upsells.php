<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Upsells extends AC_Column
	implements ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-upsells' );
		$this->set_label( __( 'Upsells', 'codepress-admin-columns' ) );
	}

	// Display
	public function get_value( $post_id ) {
		$upsell_ids = $this->get_raw_value( $post_id );
		$upsells = array();

		foreach ( $upsell_ids as $id ) {
			if ( ! $id ) {
				continue;
			}

			$title = get_the_title( $id );
			$link = get_edit_post_link( $id );

			$upsells[] = ac_helper()->html->link( $link, $title );
		}

		return implode( ', ', $upsells );
	}

	// Editing
	public function get_raw_value( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product->get_upsells();
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Upsells( $this );
	}

}
