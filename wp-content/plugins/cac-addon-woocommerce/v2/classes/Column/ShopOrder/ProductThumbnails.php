<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopOrder_ProductThumbnails extends AC_Column {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-product_thumbnails' );
		$this->set_label( __( 'Product - Thumbnails', 'woocommerce' ) );
	}

	public function get_value( $post_id ) {
		$image_ids = $this->get_raw_value( $post_id );

		if ( empty( $image_ids ) ) {
			return $this->get_empty_char();
		}

		$images = array();

		foreach ( $image_ids as $product_id => $image_id ) {
			if ( $image = $this->get_formatted_value( $image_id ) ) {
				$images[] = $image;
			}
		}

		return implode( $images );
	}

	/**
	 * @return array [ int $product_id => int $image_id ]
	 */
	public function get_raw_value( $post_id ) {
		$order = new WC_Order( $post_id );

		$images = array();
		if ( $items = $order->get_items() ) {
			foreach ( $items as $item ) {
				if ( $image = get_post_thumbnail_id( $item['product_id'] ) ) {
					$images[ $item['product_id'] ] = $image;
				}
			}
		}

		return $images;
	}

	public function register_settings() {
		$this->add_setting( new AC_Settings_Column_Image( $this ) );
	}

}
