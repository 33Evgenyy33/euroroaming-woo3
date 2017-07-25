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
 * Checkout form shipping handler.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Checkout {


	/** @var array memoization helper to prevent duplicate HTML output in checkout form */
	private static $pickup_package_form_output = array();


	/**
	 * Checkout hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// to output the checkout item pickup location selector we need a different hook than the one used in cart page
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'add_checkout_item_pickup_location_field' ), 999, 3 );

		// add pickup location information and a pickup appointment field to each package meant for pickup
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'output_pickup_package_form' ), 10, 2 );

		// workaround to avoid WooCommerce displaying pickup item details in wrong places in the checkout form
		add_filter( 'woocommerce_shipping_package_details_array', array( $this, 'maybe_hide_pickup_package_item_details' ), 10, 2 );

		// filter shipping packages based on item handling data from session
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'handle_packages' ), 1 );
		// filter again the shipping packages to toggle Local Pickup Plus available from available rates
		add_filter( 'woocommerce_shipping_packages',      array( $this, 'filter_package_rates' ), 1 );

		// if there are no packages for shipping, we can avoid asking to ship to an address altogether
		add_filter( 'woocommerce_cart_needs_shipping_address', array( $this, 'toggle_ship_to_different_address' ), 20, 1 );

		// if there are any chosen pickup locations that warrant a discount, apply the total discount as a negative fee
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'apply_pickup_discount' ) );

		// handle checkout validation upon submission
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout' ), 999 );
	}


	/**
	 * Render the pickup location selection box on the checkout items summary.
	 *
	 * @see \WC_Local_Pickup_Plus_Cart::add_cart_item_pickup_location_field()
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $product_qty_html HTML intended to output the item quantity to be ordered
	 * @param array $cart_item the cart item object as array
	 * @param string $cart_item_key the cart item identifier
	 * @return string HTML
	 */
	public function add_checkout_item_pickup_location_field( $product_qty_html, $cart_item, $cart_item_key ) {

		if ( is_checkout() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( $local_pickup_plus->is_available() ) {

				$product_field = new WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field( $cart_item_key );

				$product_qty_html .= $product_field->get_html();
			}
		}

		return $product_qty_html;
	}


	/**
	 * Output pickup location information and appointments box next to pickup packages in checkout form.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string|\WC_Shipping_Rate $shipping_rate the chosen shipping method for the package
	 * @param int|string $package_index the current package index
	 */
	public function output_pickup_package_form( $shipping_rate, $package_index ) {

		if ( is_checkout() ) {

			$local_pickup_plus_id = wc_local_pickup_plus_shipping_method_id();

			if ( ! array_key_exists( $package_index, self::$pickup_package_form_output ) ) {

				self::$pickup_package_form_output[ $package_index ] = true;

				if ( $shipping_rate === $local_pickup_plus_id || ( $shipping_rate instanceof WC_Shipping_Rate && $shipping_rate->method_id === $local_pickup_plus_id ) ) {

					$package_field = new WC_Local_Pickup_Plus_Pickup_Location_Package_Field( $package_index );

					$package_field->output_html();
				}
			}
		}
	}


	/**
	 * Workaround for a WC glitch which might display item details in wrong places while doing AJAX.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $item_details items in package meant for the current shipment
	 * @param array $package the current package array object
	 * @return array
	 */
	public function maybe_hide_pickup_package_item_details( $item_details, $package ) {

		if ( ! empty( $package['pickup_location_id'] ) || ( isset( $package['ship_via'] ) && wc_local_pickup_plus_shipping_method_id() ) ) {
			$item_details = array();
		}

		return $item_details;
	}


	/**
	 * Create a package for shipping or pickup.
	 *
	 * @since 2.0.0
	 *
	 * @param array $items items to put into the package
	 * @return array
	 */
	private function create_package( $items ) {
		return array(
			'contents'        => $items,
			'contents_cost'   => array_sum( wp_list_pluck( $items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->applied_coupons,
			'user'            => array(
				'ID' => get_current_user_id(),
			),
			'destination'     => array(
				'country'   => WC()->customer->get_shipping_country(),
				'state'     => WC()->customer->get_shipping_state(),
				'postcode'  => WC()->customer->get_shipping_postcode(),
				'city'      => WC()->customer->get_shipping_city(),
				'address'   => WC()->customer->get_shipping_address(),
				'address_2' => WC()->customer->get_shipping_address_2(),
			),
		);
	}


	/**
	 * Filter packages to separate packages for pickup from ordinary packages.
	 *
	 * @since 2.0.0
	 *
	 * @param array $packages the packages array
	 * @return array
	 */
	public function handle_packages( $packages ) {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();
		$pickup_data       = wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( null );

		if ( $pickup_data && $local_pickup_plus->is_available() ) {

			$new_packages = array();
			$cart_items   = WC()->cart->cart_contents;
			$index        = 0;
			$pickup_items = array();
			$ship_items   = array();

			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if (    array_key_exists( $cart_item_key, $pickup_data )
				     && isset( $pickup_data[ $cart_item_key ]['handling'] )
				     && 'pickup' === $pickup_data[ $cart_item_key ]['handling'] ) {

					$pickup_items[ $cart_item_key ] = $cart_item;
					$pickup_items[ $cart_item_key ]['pickup_location_id'] = ! empty( $pickup_data[ $cart_item_key ]['pickup_location_id'] ) ? (int) $pickup_data[ $cart_item_key ]['pickup_location_id'] : 0;

				} else {

					$ship_items[ $cart_item_key ] = $cart_item;
				}
			}

			// create pickup packages and put pickup items with the same pickup location in the same package too
			if ( ! empty( $pickup_items ) ) {

				$same_pickup_locations = array();

				foreach ( $pickup_items as $item_key => $pickup_item ) {
					$same_pickup_locations[ $pickup_item['pickup_location_id'] ][ $item_key ] = $pickup_item;
				}

				foreach ( $same_pickup_locations as $pickup_location_id => $pickup_items ) {

					// if the pickup location changed, the pickup date should be reset
					if ( isset( $packages[ $index ]['pickup_location_id'] ) && (int) $packages[ $index ]['pickup_location_id'] !== (int) $pickup_location_id ) {
						wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $index, array(
							'pickup_location_id' => (int) $pickup_location_id,
							'pickup_date'        => '',
						) );
					}

					$new_packages[ $index ]                       = $this->create_package( $pickup_items );
					$new_packages[ $index ]['pickup_location_id'] = (int) $pickup_location_id;
					$new_packages[ $index ]['ship_via']           = array( $local_pickup_plus->get_method_id() );

					$index++;
				}
			}

			// create a single package for items meant to be shipped otherwise
			if ( ! empty( $ship_items ) ) {

				$new_packages[ $index ] = $this->create_package( $ship_items );

				// also wipe pickup data from session for this package
				wc_local_pickup_plus()->get_session_instance()->delete_package_pickup_data( $index );
			}

			$packages = $new_packages;
		}

		return $packages;
	}


	/**
	 * Filter package rates to remove Local Pickup Plus option for packages meant for shipping.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $packages shipping packages array
	 * @return array
	 */
	public function filter_package_rates( $packages ) {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if ( ! empty( $packages ) && $local_pickup_plus->is_available() ) {

			$local_pickup_plus_id = $local_pickup_plus->get_method_id();

			foreach ( $packages as $index => $package ) {
				if ( ! isset( $package['ship_via'] ) && isset( $package['rates'][ $local_pickup_plus_id ] ) ) {
					unset( $packages[ $index ]['rates'][ $local_pickup_plus_id ] );
				}
			}
		}

		return $packages;
	}


	/**
	 * Toggle whether we need to prompt for a shipping address at checkout based on cart choices.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param bool $needs_shipping_address
	 * @return bool
	 */
	public function toggle_ship_to_different_address( $needs_shipping_address ) {

		if ( $needs_shipping_address ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( $local_pickup_plus && $local_pickup_plus->is_available() ) {

				$cart_items         = WC()->cart->cart_contents;
				$items_for_pickup   = 0;
				$items_for_shipping = 0;

				foreach ( $cart_items as $cart_key => $cart_item ) {

					if ( 'pickup' === wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $cart_key, 'handling' ) ) {
						$items_for_pickup++;
					} else {
						$items_for_shipping++;
					}
				}

				$needs_shipping_address = $items_for_shipping >= 1 || 0 === $items_for_pickup;
			}
		}

		return $needs_shipping_address;
	}


	/**
	 * Calculate any pickup location discounts when doing cart totals.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function apply_pickup_discount() {

		$cart              = WC()->cart;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (      $cart->cart_contents_total > 0
		     && ! $cart->is_empty()
		     &&   $local_pickup_plus->is_available() ) {

			$packages       = WC()->shipping()->get_packages();
			$total_discount = 0;

			foreach ( $packages as $package_key => $package ) {

				$chosen_location = ! empty( $package['pickup_location_id'] ) ? wc_local_pickup_plus_get_pickup_location( (int) $package['pickup_location_id' ] ) : null;

				if ( $chosen_location && isset( $package['contents_cost'] ) && $package['contents_cost'] > 0 ) {

					$package_costs    = $package['contents_cost'];
					$price_adjustment = $chosen_location->get_price_adjustment();

					if ( $price_adjustment->is_discount() ) {

						$discount_amount = $price_adjustment->get_amount( true );

						// if the discount is a percentage, then calculate over the package contents
						if ( $price_adjustment->is_percentage() ) {
							$discount_amount = $price_adjustment->get_relative_amount( $package_costs, true );
						}

						$total_discount += $discount_amount > 0 ? $discount_amount : 0;
					}
				}
			}

			if ( $total_discount > 0 ) {

				// the total discount shouldn't amount to more than the total cart costs, although WooCommerce wouldn't let a new order to have a negative total
				$total_discount = $total_discount >= $cart->cart_contents_total ? $cart->cart_contents_total : $total_discount;

				WC()->cart->add_fee(
					sprintf( __( '%s discount', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_plus->get_method_title() ),
					"-{$total_discount}",
					false
				);
			}
		}
	}


	/**
	 * Validate local pickup order upon checkout.
	 *
	 * The exceptions are converted into customer error notices by WooCommerce.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $posted_data checkout data (does not include package data, see $_POST)
	 * @throws Exception
	 */
	public function validate_checkout( $posted_data ) {

		$local_pickup_method = wc_local_pickup_plus_shipping_method();
		$shipping_methods    = $posted_data['shipping_method'];

		// Check if there are any packages meant for local pickup
		if ( $local_pickup_packages = isset( $posted_data['shipping_method'] ) ? array_keys( $shipping_methods, wc_local_pickup_plus_shipping_method_id() ) : null ) {

			$pickup_location_ids  = isset( $_POST['shipping_method_pickup_location_id'] ) ? $_POST['shipping_method_pickup_location_id'] : array();
			$pickup_dates         = isset( $_POST['shipping_method_pickup_date'] )        ? $_POST['shipping_method_pickup_date']        : array();

			foreach ( $local_pickup_packages as $package_id ) {

				$errors = array();

				// a pickup location has not been chosen:
				if ( empty( $pickup_location_ids[ $package_id ] ) ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$errors['pickup_location_id'] = sprintf( __( 'Please select a pickup location if you intend to use %s as shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
				}

				// a pickup date has not been set, but it's mandatory:
				if ( empty( $pickup_dates[ $package_id ] ) && 'required' === $local_pickup_method->pickup_appointments_mode() ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$errors['pickup_date'] = sprintf( __( 'Please choose a date to schedule a pickup when using %s shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
				}

				/**
				 * Filter validation of pickup errors at checkout.
				 *
				 * @since 2.0.0
				 *
				 * @param array $errors associative array of errors and predefined messages - leave empty to pass validation
				 * @param int|string $package_key the current package key for the package being evaluated for pickup data
				 * @param array $posted_data posted data incoming from form submission
				 */
				$errors = apply_filters( 'wc_local_pickup_plus_validate_pickup_checkout', $errors, $package_id, $posted_data );

				if ( empty( $errors ) ) {
					wc_local_pickup_plus()->get_session_instance()->clear_session_data();
				} elseif ( is_array( $errors ) ) {
					foreach ( $errors as $error => $message ) {
						throw new SV_WC_Plugin_Exception( $message );
					}
				}
			}

			// set the user preferred pickup location (we can choose only one)
			if ( ! empty( $pickup_location_ids ) && is_array( $pickup_location_ids ) ) {

				$pickup_location_id = current( $pickup_location_ids );

				if ( is_numeric( $pickup_location_id ) ) {
					wc_local_pickup_plus_set_user_default_pickup_location( $pickup_location_id );
				}
			}
		}
	}


}
