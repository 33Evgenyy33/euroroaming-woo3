<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order shipping address (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopOrder_ProductThumbnails extends ACP_Export_Model {

	public function get_value( $id ) {
		$thumbnails_ids = $this->column->get_raw_value( $id );
		$values = array();

		foreach ( $thumbnails_ids as $id ) {
			$values[] = wp_get_attachment_image_url( $id, 'full' );
		}

		return implode( ', ', $values );
	}

}
