<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @package     WC-Shipping-Local-Pickup-Plus
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Cart handler.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Cart {


	/**
	 * Hook into cart.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// set the cart item keys as cart item properties
		add_action( 'template_redirect', array( $this, 'set_cart_item_keys' ) );

		// add a selector next to each product in cart to designate for pickup
		// note: this is normally a filter, we use an action to echo some content instead
		add_action( 'woocommerce_get_item_data', array( $this, 'add_cart_item_pickup_location_field' ), 999, 2 );
	}


	/**
	 * Add the cart item key to the cart item data.
	 *
	 * We will need a copy of the cart key to associate pickup choices with the corresponding cart item later.
	 *
	 * @see \WC_Local_Pickup_Plus_Cart::add_cart_item_pickup_location_field()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function set_cart_item_keys() {

		if ( ( is_cart() || is_checkout() ) && ! WC()->cart->is_empty() ) {

			$cart_contents = WC()->cart->cart_contents;

			foreach ( array_keys( WC()->cart->cart_contents ) as $cart_item_key ) {

				if ( ! isset( $cart_contents[ $cart_item_key ]['cart_item_key'] ) ) {
					$cart_contents[ $cart_item_key ]['cart_item_key'] = $cart_item_key;
				}

				wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_key, array() );
			}

			WC()->cart->cart_contents = $cart_contents;
		}
	}


	/**
	 * Render the pickup location selection box on the cart summary.
	 *
	 * This callback is performed as an action rather than a filter to echo some content.
	 *
	 * @see \WC_Local_Pickup_Plus_Cart::set_cart_item_keys()
	 * @see \WC_Local_Pickup_Plus_Checkout::add_checkout_item_pickup_location_field()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $item_data the product item data (e.g. used in variations)
	 * @param array $cart_item the product as a cart item array
	 * @return array unfiltered item data (see method description)
	 */
	public function add_cart_item_pickup_location_field( $item_data, $cart_item ) {

		if ( isset( $cart_item['cart_item_key'] ) && in_the_loop() && is_cart() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( $local_pickup_plus->is_available() ) {

				$product_field = new WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field( $cart_item['cart_item_key'] );

				echo $product_field->get_html();
			}
		}

		return $item_data;
	}


}
