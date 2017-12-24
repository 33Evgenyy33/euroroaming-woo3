<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_ShopOrder_ProductDetails extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-product_details' );
		$this->set_label( __( 'Product - Details', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Based on the default WooCommerce column order_items
	public function get_value( $order_id ) {
		$result = array();

		$order = wc_get_order( $order_id );

		if ( sizeof( $order->get_items() ) == 0 ) {
			return '';
		}

		foreach ( $order->get_items() as $item ) {
			$output = false;

			$product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			$product_name = apply_filters( 'woocommerce_order_item_name', $item['name'], $item, false );

			$quantity = '<span class="qty">' . absint( $item['qty'] ) . 'x</span>';

			if ( $product ) {
				$output .= '<strong>' . $quantity . ac_helper()->html->link( get_edit_post_link( $product->id ), $product_name ) . '</strong>';
				$output .= ( wc_product_sku_enabled() && $product->get_sku() ) ? '<div class="meta">' . __( 'SKU', 'woocommerce' ) . ': ' . $product->get_sku() . '</div>' : '';
			} else {
				$output .= '<strong>' . $quantity . esc_html( $product_name ) . '</strong>';
			}

			$item_meta = new WC_Order_Item_Meta( $item, $product );

			if ( $item_meta && ( $_item_meta_html = $item_meta->display( true, true ) ) ) {
				$output .= '<div class="meta">' . $_item_meta_html . '</div>';
			}

			$result[] = '<div class="ac-wc-product">' . $output . '</div>';
		}

		return implode( $result );
	}

	public function get_raw_value( $order_id ) {
		return ac_addon_wc_helper()->get_product_ids_by_order( $order_id );
	}

}
