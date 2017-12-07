<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_Product_Variation extends AC_Column
	implements AC_Column_AjaxValue, ACP_Column_SortingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-variation' );
		$this->set_label( __( 'Variations', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $id ) {
		$variations = $this->get_raw_value( $id );

		if ( ! $variations ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', count( $variations ) ), count( $variations ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	public function get_raw_value( $post_id ) {
		return $this->get_variations( $post_id );
	}

	public function get_ajax_value( $id ) {
		$value = false;

		if ( $variations = $this->get_variations( $id ) ) {
			$values = array();

			foreach ( $variations as $variation ) {

				$html = $this->get_variation_label( $variation );
				$html .= $this->get_variation_stock_status( $variation );
				$html .= $this->get_variation_price( $variation );

				$values[] = '<div class="variation">' . $html . '</div>';
			}

			$value = implode( $values );
		}

		return $value;
	}

	// Common

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_variation_label( WC_Product_Variation $variation ) {
		$label = $variation->get_id();

		if ( $attributes = $variation->get_variation_attributes() ) {
			$label = implode( ' | ', array_filter( $attributes ) );
		}

		return '<span class="label" ' . ac_helper()->html->get_tooltip_attr( $this->get_tooltip_variation( $variation ) ) . '">' . $label . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_variation_stock_status( WC_Product_Variation $variation ) {
		if ( ! $variation->is_in_stock() ) {
			return '<span class="stock outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</span>';
		}

		$stock = __( 'In stock', 'woocommerce' );

		if ( $qty = $variation->get_stock_quantity() ) {
			$stock .= ' <span class="qty">' . $qty . '</span>';
		}

		return '<span class="stock instock">' . $stock . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return bool|string
	 */
	protected function get_variation_price( WC_Product_Variation $variation ) {
		$price = $variation->get_price_html();

		if ( ! $price ) {
			return false;
		}

		return '<span class="price">' . $variation->get_price_html() . '</span>';
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return string
	 */
	protected function get_tooltip_variation( $variation ) {
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
		$tooltip[] = '#' . $variation->get_id();

		return implode( ' | ', $tooltip );
	}

	/**
	 * @param WC_Product_Variation $variation
	 *
	 * @return bool|string
	 */
	protected function get_dimensions( $variation ) {
		$dimensions = array(
			'length' => $variation->get_length(),
			'width'  => $variation->get_width(),
			'height' => $variation->get_height(),
		);

		if ( count( array_filter( $dimensions ) ) <= 0 ) {
			return false;
		}

		return implode( ' x ', $dimensions ) . ' ' . get_option( 'woocommerce_dimension_unit' );
	}

	/**
	 * @param $product_id
	 *
	 * @return array
	 */
	public function get_variation_ids( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product_Variable ) {
			return array();
		}

		return $product->get_children();
	}

	/**
	 * @param int $id
	 *
	 * @return WC_Product_Variation[]
	 */
	protected function get_variations( $product_id ) {
		$variations = array();

		foreach ( $this->get_variation_ids( $product_id ) as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( $variation->exists() ) {
				$variations[] = $variation;
			}
		}

		return $variations;
	}

	public function sorting() {
		return new ACA_WC_Sorting_Product_Variation( $this );
	}

	public function export() {
		return new ACA_WC_Export_Product_Variation( $this );
	}

}
