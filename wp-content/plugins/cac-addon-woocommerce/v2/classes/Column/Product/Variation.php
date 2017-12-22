<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_Product_Variation extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-variation' );
		$this->set_label( __( 'Variations', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $post_id ) {
		$variations = $this->get_raw_value( $post_id );

		if ( ! $variations ) {
			return false;
		}

		$values = array();

		foreach ( $variations as $_variation ) {

			$variation = new WC_Product_Variation( $_variation['variation_id'] );

			if ( ! $_variation ) {
				continue;
			}

			$label = $variation->get_variation_id();
			if ( $attributes = $variation->get_variation_attributes() ) {
				$label = implode( ' | ', array_filter( $attributes ) );
			}

			$stock = __( 'In stock', 'woocommerce' );
			$stock_class = 'instock';
			if ( ! $variation->is_in_stock() ) {
				$stock = __( 'Out of stock', 'woocommerce' );
				$stock_class = 'outofstock';
			} else if ( $qty = $variation->get_stock_quantity() ) {
				$stock .= ' <span class="qty">' . $variation->get_stock_quantity() . '</span>';
			}

			$tooltip = array();
			if ( $sku = $variation->get_sku() ) {
				$tooltip[] = __( 'SKU', 'woocommerce' ) . ' ' . $sku;
			}
			if ( $weight = $variation->get_weight() ) {
				$tooltip[] = floatval( $weight ) . get_option( 'woocommerce_weight_unit' );
			}
			if ( $dimensions = $this->get_dimensions( $variation ) ) {
				$tooltip[] = $dimensions;
			}
			if ( $shipping_class = $variation->get_shipping_class() ) {
				$tooltip[] = $shipping_class;
			}
			$tooltip[] = '#' . $variation->get_variation_id();

			$items = array(
				'<span class="label" data-tip="' . implode( ' | ', $tooltip ) . '">' . $label . '</span>',
				'<span class="stock ' . $stock_class . '">' . $stock . '</span>',
			);

			if ( $price = $variation->get_price_html() ) {
				$items[] = '<span class="price">' . $variation->get_price_html() . '</span>';
			}

			$values[] = '<div class="variation">' . implode( $items ) . '</div>';
		}

		if ( ! $values ) {
			return false;
		}

		return implode( '', $values );
	}

	public function get_raw_value( $post_id ) {
		return $this->get_variations( $post_id );
	}

	// Common

	public function get_dimensions( $variation ) {
		$dimensions = array(
			'length' => $variation->length,
			'width'  => $variation->width,
			'height' => $variation->height,
		);

		return count( array_filter( $dimensions ) ) > 0 ? implode( ' x ', $dimensions ) . ' ' . get_option( 'woocommerce_dimension_unit' ) : false;
	}

	public function get_variations( $id ) {
		$product = wc_get_product( $id );

		return $product instanceof WC_Product_Variable ? $product->get_available_variations() : false;
	}

}
