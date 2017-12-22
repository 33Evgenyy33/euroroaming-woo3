<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Settings_Product extends AC_Settings_Column_Post
	implements AC_Settings_FormatValueInterface {

	protected function get_post_type() {
		return 'product';
	}

	protected function get_display_options() {
		$options = parent::get_display_options();

		$options['sku'] = __( 'SKU', 'woocommerce' );

		return $options;
	}

	public function format( $value, $original_value ) {
		$value = parent::format( $value, $original_value );

		switch ( $this->get_post_property_display() ) {
			case 'sku' :
				$value = esc_html( get_post_meta( $original_value, '_sku', true ) );

				break;
		}

		return $value;
	}

}
