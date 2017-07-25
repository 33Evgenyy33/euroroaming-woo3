<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Descriptions.
 *
 * Display a description icon + tooltip on hover.
 *
 * @since 1.0.0
 *
 * @param string $condition Current condition to display the description for.
 */
function was_condition_description( $condition ) {

	$descriptions = array(
		'subtotal'                => __( 'Compared against the order subtotal', 'woocommerce-advanced-shipping' ),
		'subtotal_ex_tax'         => __( 'Compared against the order subtotal excluding taxes', 'woocommerce-advanced-shipping' ),
		'tax'                     => __( 'Compared against the tax total amount', 'woocommerce-advanced-shipping' ),
		'quantity'                => __( 'Compared against the quantity of items in the cart', 'woocommerce-advanced-shipping' ),
		'contains_product'        => __( 'Check if a product is or is not present in the cart', 'woocommerce-advanced-shipping' ),
		'coupon'                  => __( 'Matched against the applied coupon codes or coupon amounts (use \'%\' or \'$\' for the respective amounts', 'woocommerce-advanced-shipping' ),
		'weight'                  => __( 'Weight calculated on all the cart contents', 'woocommerce-advanced-shipping' ),
		'contains_shipping_class' => __( 'Check if a shipping class is or is not present in the cart', 'woocommerce-advanced-shipping' ),

		'zipcode'                 => __( 'Compare against customer zipcode. Comma separated list allowed. Use \'*\' for wildcard', 'woocommerce-advanced-shipping' ),
		'city'                    => __( 'Compare against customer city. Comma separated list allowed', 'woocommerce-advanced-shipping' ),
		'state'                   => __( 'Compare against the customer state. Note: only installed states will show up', 'woocommerce-advanced-shipping' ),
		'country'                 => __( 'Compare against the customer country', 'woocommerce-advanced-shipping' ),
		'role'                    => __( 'Compare against the user role', 'woocommerce-advanced-shipping' ),

		'length'                  => __( 'Compared to lengthiest product in cart', 'woocommerce-advanced-shipping' ),
		'width'                   => __( 'Compared to widest product in cart', 'woocommerce-advanced-shipping' ),
		'height'                  => __( 'Compared to highest product in cart', 'woocommerce-advanced-shipping' ),
		'stock_status'            => __( 'All products in cart must match stock status', 'woocommerce-advanced-shipping' ),
		'category'                => __( 'All products in cart must match the given category', 'woocommerce-advanced-shipping' ),
	);
	$descriptions = apply_filters( 'was_descriptions', $descriptions );

	// Display description
	if ( ! isset( $descriptions[ $condition ] ) ) :
		?><span class='was-description no-description'></span><?php
		return;
	endif;

	?><span class='was-description <?php echo $condition; ?>-description'>

		<span class='description'>
			<img class='help_tip' src='<?php echo WC()->plugin_url(); ?>/assets/images/help.png' height='24' width='24' data-tip="<?php echo esc_html( $descriptions[ $condition ] ); ?>" />
		</span>

	</span><?php

}
