<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_ShopOrder_ProductDetails extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-product_details' );
		$this->set_label( __( 'Product - Details', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $order_id ) {
		$result = array();

		$order = wc_get_order( $order_id );

		$order_items = $order->get_items();

		if ( 0 == sizeof( $order_items ) ) {
			return '';
		}

		foreach ( $order_items as $item ) {

			/* @var WC_Order_Item_Product $item */
			/* @var WC_Product $product */

			if ( ! $item->get_quantity() ) {
				continue;
			}

			$output = false;

			$quantity = '<span class="qty">' . absint( $item->get_quantity() ) . 'x</span>';

			if ( $product = $item->get_product() ) {
				$output .= '<strong>' . $quantity . ac_helper()->html->link( get_edit_post_link( $product->get_id() ), $product->get_name() ) . '</strong>';

				if ( wc_product_sku_enabled() && $product->get_sku() ) {
					$output .= '<div class="meta">' . __( 'SKU', 'woocommerce' ) . ': ' . $product->get_sku() . '</div>';
				}

			} else {
				$output .= '<strong>' . $quantity . esc_html( $item->get_name() ) . '</strong>';
			}

			if ( $item instanceof WC_Order_Item_Product ) {
				$meta = $item->get_formatted_meta_data( true, true );
				$meta_values = array();

				foreach ( $meta as $info ) {
					$meta_values[] = $info->display_key . ': ' . strip_tags( $info->display_value );
				}

				$output .= '<div class="meta">' . implode( ', ', $meta_values ) . '</div>';
			}

			$result[] = '<div class="ac-wc-product">' . $output . '</div>';
		}

		return implode( $result );
	}

	public function get_raw_value( $order_id ) {
		return ac_addon_wc_helper()->get_product_ids_by_order( $order_id );
	}

	public function export() {
		return new ACP_Export_Model_Disabled( $this );
	}

}
