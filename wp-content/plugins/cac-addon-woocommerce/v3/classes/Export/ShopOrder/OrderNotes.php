<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order title (default column) exportability model
 *
 * @since NEWVERSION
 */
class ACA_WC_Export_ShopOrder_OrderNotes extends ACP_Export_Model {

	public function get_value( $id ) {
		return get_post( $id )->comment_count;
	}

}
