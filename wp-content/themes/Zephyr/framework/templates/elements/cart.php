<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output cart element
 *
 * @var $icon           int
 * @var $dropdown_effect string Dropdown Effect
 * @var $icon_size      int
 * @var $design_options array
 * @var $id             string
 */

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

global $cache_enabled;

$classes = ' dropdown_' . $dropdown_effect;
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $design_options ) AND isset( $design_options['hide_for_not-sticky'] ) AND $design_options['hide_for_not-sticky'] ) {
	$classes .= ' hide-for-not-sticky';
}
foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
	if ( ! us_is_header_elm_shown( $id, $state ) ) {
		$classes .= ' hidden_for_' . $state;
	}
}
if ( $vstretch ) {
	$classes .= ' height_full';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
echo '<div class="w-cart' . $classes . ' empty">';
echo '<div class="w-cart-h">';
echo '<a class="w-cart-link" href="' . esc_attr( wc_get_cart_url() ) . '" aria-label="' . __( 'Cart', 'us' ) . '">';
echo '<span class="w-cart-icon">';

if ( ! empty( $icon ) ) {
	echo us_prepare_icon_tag( $icon );
}

echo '<span class="w-cart-quantity"></span></span></a>';
echo '<div class="w-cart-notification"><div><span class="product-name">' . us_translate( 'Product', 'woocommerce' ) . '</span> ' . __( 'was added to your cart', 'us' ) . '</div></div>';
echo '<div class="w-cart-dropdown">';
the_widget( 'WC_Widget_Cart' ); // This widget being always filled with products via AJAX
echo '</div>';
echo '</div>';
echo '</div>';
