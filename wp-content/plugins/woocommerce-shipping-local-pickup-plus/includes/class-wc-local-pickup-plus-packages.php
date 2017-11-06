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
 * Handles shipping packages.
 *
 * @since 2.3.1
 */
class WC_Local_Pickup_Plus_Packages {


	/**
	 * Sets up the packages handler.
	 *
	 * @since 2.3.1
	 */
	public function __construct() {

		// filter shipping packages based on item handling data from session
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'handle_packages' ), 1 );

		// filter again the shipping packages to toggle Local Pickup Plus available from available rates
		add_filter( 'woocommerce_shipping_packages',      array( $this, 'filter_package_rates' ), 1 );

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

		$billing_address = array(
			'country'   => WC()->customer->get_billing_country(),
			'state'     => WC()->customer->get_billing_state(),
			'postcode'  => WC()->customer->get_billing_postcode(),
			'city'      => WC()->customer->get_billing_city(),
			'address'   => WC()->customer->get_billing_address(),
			'address_2' => WC()->customer->get_billing_address_2(),
		);

		$shipping_address = array(
			'country'   => WC()->customer->get_shipping_country(),
			'state'     => WC()->customer->get_shipping_state(),
			'postcode'  => WC()->customer->get_shipping_postcode(),
			'city'      => WC()->customer->get_shipping_city(),
			'address'   => WC()->customer->get_shipping_address(),
			'address_2' => WC()->customer->get_shipping_address_2(),
		);

		$set_shipping_address = array_diff_assoc( $billing_address, $shipping_address );

		return array(
			'contents'        => $items,
			'contents_cost'   => array_sum( wp_list_pluck( $items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->applied_coupons,
			'user'            => array(
				'ID' => get_current_user_id(),
			),
			'destination'     => ! empty( $set_shipping_address ) && '' !== $shipping_address['state'] && '' !== $shipping_address['postcode'] ? $shipping_address : $billing_address,
		);
	}


	/**
	 * Returns a pickup location ID when only a single pickup location can be used for a cart item.
	 *
	 * This covers cases where only a single pickup location exists for a product in cart item or when
	 * using per-order location mode and a location has been set for any other items in cart already.
	 *
	 * @since 2.2.0
	 *
	 * @param array $contents cart item contents
	 * @param array $pickup_data cart item pickup data
	 * @return int a pickup location ID or 0 if no pickup location is determined or there is more than one possible pickup location for the products
	 */
	private function get_package_pickup_location_id( array $contents, $pickup_data = array() ) {

		$location_ids = array();

		// determine if there's only a single pickup location possible for the cart item
		foreach ( $contents as $item ) {

			$package_product = isset( $item['data'] ) ? $item['data'] : null;

			if ( $package_product instanceof WC_Product ) {

				$available_locations = wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $package_product, array( 'fields' => 'ids' ) );
				$location_ids[]      = 1 === count( $available_locations ) ? (int) current( $available_locations ) : 0;
			}
		}

		$location_ids    = array_unique( $location_ids );
		$pickup_location = 1 === count( $location_ids ) ? wc_local_pickup_plus_get_pickup_location( current( $location_ids ) ) : null;

		// determine if the cart item should "inherit" a location from other cart items in per-order location mode
		if ( ! $pickup_location && ! empty( $pickup_data ) && wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) {

			foreach ( $pickup_data as $item_pickup_data ) {

				if ( ! empty( $item_pickup_data['pickup_location_id'] ) ) {
					$pickup_location = wc_local_pickup_plus_get_pickup_location( $item_pickup_data['pickup_location_id'] );
					break;
				}
			}
		}

		return $pickup_location ? $pickup_location->get_id() : 0;
	}


	/**
	 * Determines whether a cart item should be picked up while processing raw cart data.
	 *
	 * @see \WC_Local_Pickup_Plus_Checkout::handle_packages()
	 *
	 * @since 2.3.0
	 *
	 * @param array $cart_item the item data from cart
	 * @param string $cart_item_key the cart item key that could match a stored session array key
	 * @param array $pickup_data (optional) pickup data from session
	 * @param array $shipping_rates (optional) available shipping rates for the cart item
	 * @return bool
	 */
	private function cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data = array(), $shipping_rates = array() ) {

		$pickup = false;

		// customer session data indicates that this item should be picked up
		if ( array_key_exists( $cart_item_key, $pickup_data ) && isset( $pickup_data[ $cart_item_key ]['handling'] ) && 'pickup' === $pickup_data[ $cart_item_key ]['handling'] ) {

			$pickup = true;

			// sanity check for products marked for shipping only
			if ( isset( $cart_item['data'] ) && ! wc_local_pickup_plus_product_can_be_picked_up( $cart_item['data'] ) ) {
				$pickup = false;
			}

		// sanity check if the cart item must be picked up by product setting
		} elseif ( isset( $cart_item['data'] ) ) {

			$pickup = wc_local_pickup_plus_product_must_be_picked_up( $cart_item['data'] );

			// if there are no shipping methods available, the item should be picked up, unless
			// pickups for the item are disabled
			if ( ! $pickup && empty( $shipping_rates ) ) {
				$pickup = wc_local_pickup_plus_product_can_be_picked_up( $cart_item['data'] );
			}
		}

		return $pickup;
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

			$shipping_rates        = $this->get_rates_for_package( $packages[0] );
			$package_pickup_data   = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( 0 );
			$new_packages          = array();
			$cart_items            = WC()->cart->cart_contents;
			$index                 = 0;
			$pickup_items          = array();
			$ship_items            = array();

			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if ( $this->cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data, $shipping_rates ) ) {

					$cart_item_pickup_location_id = ! empty( $pickup_data[ $cart_item_key ]['pickup_location_id'] ) ? (int) $pickup_data[ $cart_item_key ]['pickup_location_id'] : 0;

					// special handling for cases where there is only a single pickup location possible
					if ( 0 === $cart_item_pickup_location_id ) {
						$contents                     = isset( $cart_item['contents'] ) ? $cart_item['contents'] : array();
						$cart_item_pickup_location_id = $this->get_package_pickup_location_id( $contents, $pickup_data );
					}

					// inherit pickup location from the package when per-order location mode is enabled
					if ( 0 === $cart_item_pickup_location_id && ! empty( $package_pickup_data ) && wc_local_pickup_plus_shipping_method()->is_per_order_selection_enabled() ) {
						$cart_item_pickup_location_id = ! empty( $package_pickup_data['pickup_location_id'] ) ? $package_pickup_data['pickup_location_id'] : 0;
					}

					$pickup_items[ $cart_item_key ]                       = $cart_item;
					$pickup_items[ $cart_item_key ]['pickup_location_id'] = $cart_item_pickup_location_id;
					$pickup_items[ $cart_item_key ]['pickup_date']        = ! empty( $pickup_data[ $cart_item_key ]['pickup_date'] ) ? $pickup_data[ $cart_item_key ]['pickup_date'] : '';

				} else {

					$ship_items[ $cart_item_key ] = $cart_item;
				}
			}

			/**
			 * Filters the cart items separated by handling before they are processed into packages.
			 *
			 * @since 2.2.0
			 *
			 * @param array $items associative array of cart items separated by handling (for pickup or shipping)
			 */
			$items = (array) apply_filters( 'wc_local_pickup_plus_cart_shipping_packages', array(
				'pickup_items' => $pickup_items,
				'ship_items'   => $ship_items,
			) );

			// create pickup packages and put pickup items with the same pickup location in the same package too
			if ( ! empty( $pickup_items ) ) {

				$same_pickup_locations = array();

				foreach ( $pickup_items as $item_key => $pickup_item ) {

					$pickup_location_id = isset( $pickup_item['pickup_location_id'] ) ? (int) $pickup_item['pickup_location_id'] : 0;

					// special handling for cases where there is only a single pickup location possible (this has to run again to account for filtered array)
					if ( 0 === $pickup_location_id ) {

						$contents           = is_array( $pickup_items ) ? $pickup_items : array();
						$pickup_location_id = $this->get_package_pickup_location_id( $contents, $pickup_data );
					}

					$same_pickup_locations[ (string) $pickup_location_id ][ $item_key ] = $pickup_item;
				}

				foreach ( $same_pickup_locations as $pickup_location_id => $pickup_items ) {

					if ( isset( $packages[ $index ]['pickup_location_id'] ) && (int) $packages[ $index ]['pickup_location_id'] !== (int) $pickup_location_id ) {
						// if the pickup location changed, the pickup date should be reset
						$pickup_date = '';
					} elseif ( ! empty( $pickup_items['pickup_date'] ) ) {
						// try using an available date from the current package
						$pickup_date = $pickup_items['pickup_date'] ? $pickup_items['pickup_date'] : '';
					} elseif ( ! empty( $packages[ $index ]['pickup_date'] ) ) {
						// try using an available date existing in the packages array
						$pickup_date = $packages[ $index ]['pickup_date'] ? $packages[ $index ]['pickup_date'] : '';
					} elseif ( $package_data = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $index ) ) {
						// try grabbing the date from an existing package with the same location ID
						$pickup_date = isset( $package_data['pickup_location_id'], $package_data['pickup_date'] ) && (int) $package_data['pickup_location_id'] === (int) $pickup_location_id ? $package_data['pickup_date'] : '';
					} else {
						// default empty
						$pickup_date = '';
					}

					wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $index, array(
						'pickup_location_id' => (int) $pickup_location_id,
						'pickup_date'        => $pickup_date,
					) );

					$new_packages[ $index ]                       = $this->create_package( $pickup_items );
					$new_packages[ $index ]['pickup_location_id'] = (int) $pickup_location_id;
					$new_packages[ $index ]['pickup_date']        = $pickup_date;
					$new_packages[ $index ]['ship_via']           = array( $local_pickup_plus->get_method_id() );

					$index++;
				}
			}

			// create a single package for items meant to be shipped otherwise
			if ( ! empty( $ship_items ) ) {

				// the index value here right one unit above the last pickup package, so the shipping package will be always the last package
				$new_packages[ $index ] = $this->create_package( $ship_items );

				// also wipe pickup data from session for this package
				wc_local_pickup_plus()->get_session_instance()->delete_package_pickup_data( $index );
			}

			$packages = $new_packages;
		}

		return $packages;
	}


	/**
	 * Determines whether a package not set for pickup by customer should be picked up.
	 *
	 * This is for internal use to rule out an edge case where the handling toggle cannot be printed because the package has no set rates.
	 *
	 * @see \WC_Local_Pickup_Plus_Checkout::filter_package_rates()
	 *
	 * @since 2.3.0
	 *
	 * @param array $package package data
	 * @return bool
	 */
	private function package_should_be_picked_up( $package ) {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();
		$pickup            = false;

		if ( isset( $package['rates'][ $local_pickup_plus->get_method_id() ] ) ) {
			unset( $package['rates'][ $local_pickup_plus->get_method_id() ] );
		}

		if (    $local_pickup_plus
		     && empty( $package['rates'] )
		     && $local_pickup_plus->is_per_order_selection_enabled() ) {

			// there are no other shipping methods so we should offer pickup (or split the package later if there are ship-only items)
			$pickup = ! $this->package_can_be_shipped( $package );
		}

		return $pickup;
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

		$local_pickup_plus      = wc_local_pickup_plus_shipping_method();
		$local_pickup_plus_id   = $local_pickup_plus->get_method_id();
		$append_ship_only_items = array();
		$pickup_packages        = array();

		if ( ! empty( $packages ) && $local_pickup_plus->is_available() ) {

			foreach ( $packages as $index => $package ) {

				if ( ! isset( $package['ship_via'] ) && isset( $package['rates'][ $local_pickup_plus_id ] ) ) {

					if (    isset( $package['contents'] )
					     && is_array( $package['contents'] )
					     && $this->package_should_be_picked_up( $package ) ) {

						// so we don't unset Local Pickup Rates, however, we need to check if there are any items that cannot be picked up
						foreach ( $package['contents'] as $item_key => $item ) {

							if ( isset( $item['data'] ) && ! wc_local_pickup_plus_product_can_be_picked_up( $item['data'] ) ) {

								// in this case we store the items forced to be shipped in a package that will be appended
								$append_ship_only_items[ $item_key ] = $item;

								unset( $package['contents'][ $item_key ] );
							}
						}

						// ensure the package is set for pickup
						$packages[ $index ]['ship_via'] = $package['ship_via'] = array( $local_pickup_plus_id );

					} else {

						unset( $packages[ $index ]['rates'][ $local_pickup_plus_id ] );
					}
				}

				if ( ! empty( $append_ship_only_items ) && empty( $package['contents'] ) ) {
					unset( $packages[ $index ] );
				}

				elseif ( isset( $package['ship_via'] ) && in_array( $local_pickup_plus_id, $package['ship_via'], true ) ) {
					$pickup_packages[ $index ] = $package;
				}
			}

		}

		// ensure that pickup packages are merged when location per order and automatic grouping are enabled
		if ( count( $pickup_packages ) > 1 ) {

			if ( $local_pickup_plus->is_per_order_selection_enabled() && $local_pickup_plus->is_item_handling_mode( 'automatic' ) ) {

				$grouped_pickup_package = null;

				// merge pickup packages
				foreach ( $pickup_packages as $index => $package ) {

					if ( ! $grouped_pickup_package ) {
						$grouped_pickup_package = $package;
					} else {
						$grouped_pickup_package['contents'] = array_merge( $grouped_pickup_package['contents'], $package['contents'] );
					}

					// remove from original packages
					unset( $packages[ $index ] );
				}

				// refresh rates
				$rates = $local_pickup_plus->get_rates_for_package( $grouped_pickup_package );
				$rates = array_merge( $this->get_rates_for_package( $grouped_pickup_package ), $rates );

				$grouped_pickup_package['rates'] = $rates;

				$packages[] = $grouped_pickup_package;

				$packages = array_values( $packages ); // ensure that there are no "holes" in the package array
			}
		}

		if ( ! empty( $append_ship_only_items ) ) {

			$package = $this->create_package( $append_ship_only_items );

			$package['rates'] = $this->get_rates_for_package( $package );

			$packages[ count( $packages ) ] = $package;
		}

		return $packages;
	}


	/**
	 * Returns shipping rates for a package.
	 *
	 * @since 2.3.0
	 *
	 * @param array $package shipping package
	 * @return array shipping rates
	 */
	public function get_rates_for_package( $package ) {

		$available_rates = array();

		if ( $shipping_zone = wc_get_shipping_zone( $package ) ) {

			/* @type \WC_Shipping_Method[] $shipping_methods */
			$shipping_methods = $shipping_zone->get_shipping_methods( true );

			if ( is_array( $shipping_methods ) ) {

				foreach ( $shipping_methods as $shipping_method ) {

					$rates = $shipping_method->get_rates_for_package( $package );

					if ( ! empty( $rates ) ) {
						$available_rates = array_merge( $available_rates, $rates );
					}
				}
			}
		}

		return $available_rates;
	}


	/**
	 * Checks whether the package can be shipped or not.
	 *
	 * If there are no shipping methods/rates available, the package cannot be shipped.
	 *
	 * @since 2.3.1
	 *
	 * @param array $package shipping package
	 * @return bool
	 */
	public function package_can_be_shipped( $package ) {

		unset( $package['ship_via'] );

		$shipping_rates = $this->get_rates_for_package( $package );

		return count( $shipping_rates ) > 0;
	}


	/**
	 * Returns the package for the guven cart item.
	 *
	 * Each cart item has a uinque key, which can only belong to a single packlage at a time.
	 * However, crat items do not know which package they are part of. Given the acrt item key,
	 * this method looks up the poackage the cart item is part of.
	 *
	 * @since 2.3.1
	 *
	 * @param string $cart_item_id cart item key
	 * @return array|null shipping package or null if none found
	 */
	public function get_cart_item_package( $cart_item_id ) {

		$the_package = null;
		$packages    = WC()->cart->get_shipping_packages();

		if ( ! empty( $packages ) ) {
			foreach ( $packages as $package_id => $package ) {

				foreach ( $package['contents'] as $cart_item_key => $item ) {
					if ( $cart_item_id === $cart_item_key ) {

						$the_package = $package;
						break 2;
					}
				}
			}
		}

		return $the_package;
	}

}
