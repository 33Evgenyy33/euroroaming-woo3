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

		// set the default handling data based on product-level settings
		add_action( 'woocommerce_get_item_data', array( $this, 'set_cart_item_pickup_handling' ), 10, 2 );

		// add a selector next to each product in cart to designate for pickup
		// note: this is normally a filter, we use an action to echo some content instead
		add_action( 'woocommerce_get_item_data', array( $this, 'add_cart_item_pickup_location_field' ), 999, 2 );

		// perhaps disable the shipping calculator if the first and sole item in the cart totals is for pickup
		add_filter( 'option_woocommerce_enable_shipping_calc', array( $this, 'disable_shipping_calculator' ) );

		// do not require a shipping address if local pickup plus is the sole shipping method
		add_filter( 'woocommerce_cart_needs_shipping_address', array( $this, 'needs_shipping_address' ), 40 );
	}


	/**
	 * Perhaps disables the cart page shipping calculator by toggling a WordPress option value.
	 *
	 * If in the cart totals there is only one package and is meant for pickup, we don't need the shipping calculator.
	 *
	 * @internal
	 *
	 * @since 2.2.0
	 *
	 * @param string $default_setting the option default setting
	 * @return string 'yes' or 'no'
	 */
	public function disable_shipping_calculator( $default_setting ) {

		if ( 'no' !== $default_setting && is_cart() ) {

			$packages = WC()->cart->get_shipping_packages();
			$package  = count( $packages ) > 0 ? current( $packages ) : array();

			if ( isset( $package['ship_via'][0] ) && $package['ship_via'][0] === wc_local_pickup_plus_shipping_method_id() ) {

				$default_setting = 'no';
			}
		}

		return $default_setting;
	}


	/**
	 * Check if cart needs shipping address from customer.
	 *
	 * @internal
	 *
	 * @since 2.1.1
	 *
	 * @param bool $needs_shipping_address whether collection of a shipping address is needed at cart/checkout
	 * @return bool
	 */
	public function needs_shipping_address( $needs_shipping_address ) {

		if ( $needs_shipping_address && $packages = WC()->shipping()->get_packages() ) {

			$packages_to_ship   = 0;
			$packages_to_pickup = 0;

			foreach ( $packages as $package_id => $package ) {

				$session_data = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $package_id );

				if ( isset( $session_data['handling'], $session_data['pickup_location_id' ] ) && 'pickup' === $session_data['handling'] && $session_data['pickup_location_id' ] > 0 ) {
					$packages_to_pickup++;
				} else {
					$packages_to_ship++;
				}
			}

			$needs_shipping_address = $packages_to_ship > 0 || $packages_to_ship >= $packages_to_pickup;
		}

		return $needs_shipping_address;
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
	 * Sets the pickup handling for cart items to respect their product-level
	 * settings.
	 *
	 * @since 2.1.0
	 *
	 * @param array $item_data the product item data (e.g. used in variations)
	 * @param array $cart_item the product as a cart item array
	 * @return array unfiltered item data (see method description)
	 */
	public function set_cart_item_pickup_handling( $item_data, $cart_item ) {

		if ( isset( $cart_item['cart_item_key'] ) ) {

			$product_id = ! empty( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
			$product    = wc_get_product( $product_id );
			$handling   = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_item['cart_item_key'], 'handling' );

			if ( $product ) {

				if ( wc_local_pickup_plus_product_must_be_picked_up( $product ) ) {
					$handling = 'pickup';
				} elseif ( ! wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {
					$handling = 'ship';
				}

				wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item['cart_item_key'], array( 'handling' => $handling ) );
			}
		}

		return $item_data;
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

				$product_field->output_html();
			}
		}

		return $item_data;
	}


}
