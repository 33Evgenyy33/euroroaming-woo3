<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Settings_ProductAttributes extends AC_Settings_Column
	implements AC_Settings_FormatValueInterface {

	/**
	 * @var int
	 */
	private $product_taxonomy_display;

	protected function set_name() {
		$this->name = 'product_taxonomy';
	}

	protected function define_options() {
		return array( 'product_taxonomy_display' );
	}

	private function get_attributes_taxonomies() {
		$product_taxonomies = array();

		if ( $taxonomies = get_taxonomies( array( 'object_type' => array( 'product' ) ), 'objects' ) ) {
			foreach ( $taxonomies as $name => $taxonomy ) {

				// Only use Product attributes. Their name always begins with pa_.
				if ( 'pa_' !== substr( $name, 0, strlen( 'pa_' ) ) ) {
					continue;
				}

				$product_taxonomies[ $name ] = $taxonomy->labels->name;
			}
		}

		return $product_taxonomies;
	}

	private function get_attribute_taxonomy_label( $name ) {
		$options = $this->get_attributes_taxonomies();

		return isset( $options[ $name ] ) ? $options[ $name ] : false;
	}

	private function get_attribute_options() {
		global $wpdb;

		$results = $wpdb->get_col( "Select {$wpdb->postmeta}.meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_product_attributes'" );

		if ( ! $results ) {
			return false;
		}

		$custom = array();
		$taxonomies = array();

		foreach ( $results as $atts ) {
			$atts = unserialize( $atts );

			foreach ( $atts as $key => $attr ) {
				if ( $attr['is_taxonomy'] ) {
					$taxonomies[ $key ] = $this->get_attribute_taxonomy_label( $key );
				} else {
					$custom[ $key ] = $attr['name'];
				}
			}
		}

		if ( ! $custom && ! $taxonomies ) {
			return false;
		}

		if ( ! $taxonomies ) {
			return $custom;
		}

		if ( ! $custom ) {
			return $taxonomies;
		}

		return array(
			array(
				'title'   => 'Taxonomies',
				'options' => $taxonomies,
			),
			array(
				'title'   => 'Custom',
				'options' => $custom,
			),
		);
	}

	public function create_view() {
		$attributes = $this->get_attribute_options();

		if ( ! $attributes ) {
			return false;
		}

		$attributes = array( '' => __( 'Show all attributes', 'codepress-admin-columns' ) ) + $attributes;

		$select = $this->create_element( 'select', 'product_taxonomy_display' )
		               ->set_options( $attributes );

		$view = new AC_View( array(
			'label'   => __( 'Show Single', 'codepress-admin-columns' ),
			'tooltip' => __( 'Display a single attribute.', 'codepress-admin-columns' ) . ' ' . __( 'Only works for taxonomy attributes.', 'codepress-admin-columns' ),
			'setting' => $select,
		) );

		return $view;
	}

	/**
	 * @return int
	 */
	public function get_product_taxonomy_display() {
		return $this->product_taxonomy_display;
	}

	/**
	 * @param int $product_taxonomy_display
	 *
	 * @return $this
	 */
	public function set_product_taxonomy_display( $product_taxonomy_display ) {
		$this->product_taxonomy_display = $product_taxonomy_display;

		return $this;
	}

	public function format( $value, $original_value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$divs = array();

		foreach ( $value as $name => $attribute ) {
			// Show single attribute?
			if ( $this->get_product_taxonomy_display() && $name !== $this->get_product_taxonomy_display() ) {
				continue;
			}

			// Default
			$label = $attribute['name'];
			$values = str_replace( ' |', ', ', $attribute['value'] );

			// Taxonomy
			if ( $attribute['is_taxonomy'] ) {
				$label = $this->get_attribute_taxonomy_label( $name );
				$product = wc_get_product( $original_value );
				$values = $product->get_attribute( $name );
			}

			// Tooltip
			$tooltip = array();

			if ( $attribute['is_visible'] ) {
				$tooltip[] = __( 'Visible on the product page', 'woocommerce' );
			}

			if ( $attribute['is_variation'] ) {
				$tooltip[] = __( 'Used for variations', 'woocommerce' );
			}

			if ( $attribute['is_taxonomy'] ) {
				$tooltip[] = __( 'Is a taxonomy', 'codepress-admin-columns' );
			}

			// Add tooltip
			if ( $tooltip ) {
				$label = '<strong class="label" data-tip="' . esc_attr( implode( ' | ', $tooltip ) ) . '">' . esc_html( $label ) . ':</strong>';
			}

			$divs[] = '
				<div class="attribute">
					' . $label . '
					<span class="values">' . $values . '</span>
				</div>
				';
		}

		return implode( $divs );
	}

}
