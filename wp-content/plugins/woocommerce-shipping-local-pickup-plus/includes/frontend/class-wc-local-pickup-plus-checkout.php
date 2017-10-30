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

	/** @var bool flag if packages have been counted yet */
	private static $packages_count_output = false;


	/**
	 * Checkout hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// to output the checkout item pickup location selector we need a different hook than the one used in cart page
		add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'add_checkout_item_pickup_location_field' ), 999, 3 );

		// add pickup location information and a pickup appointment field to each package meant for pickup
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'output_pickup_package_form' ), 999, 2 );

		// workaround to avoid WooCommerce displaying pickup item details in wrong places in the checkout form
		add_filter( 'woocommerce_shipping_package_details_array', array( $this, 'maybe_hide_pickup_package_item_details' ), 10, 2 );

		// filter shipping packages based on item handling data from session
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'handle_packages' ), 1 );
		// filter again the shipping packages to toggle Local Pickup Plus available from available rates
		add_filter( 'woocommerce_shipping_packages',      array( $this, 'filter_package_rates' ), 1 );

		// output hidden counters for packages by handling type for JS use
		add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'packages_count' ), 40 );

		// ensure cash on delivery is available as it deactivates itself if there are multiple packages
		add_filter( 'woocommerce_available_payment_gateways', array( $this, 'enable_cash_on_delivery' ), 9 );

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

		$local_pickup_plus    = wc_local_pickup_plus_shipping_method();
		$local_pickup_plus_id = $local_pickup_plus ?  $local_pickup_plus->get_method_id() : null;
		$is_local_pickup      = $shipping_rate === $local_pickup_plus_id || ( $shipping_rate instanceof WC_Shipping_Rate && $shipping_rate->method_id === $local_pickup_plus_id );

		if ( ! array_key_exists( $package_index, self::$pickup_package_form_output ) ) {

			if ( $is_local_pickup ) {

				self::$pickup_package_form_output[ $package_index ] = true;

				$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );

				if ( isset( $chosen_methods[ $package_index ] ) && $chosen_methods[ $package_index ] === $local_pickup_plus_id ) {

					$package_field = new WC_Local_Pickup_Plus_Pickup_Location_Package_Field( $package_index );

					if ( is_checkout() ) {

						?>
						<tr class="shipping pickup_location">
							<th><?php esc_html_e( 'Pickup Location', 'woocommerce-shipping-local-pickup-plus' ); ?></th>
							<td><?php $package_field->output_html(); ?></td>
						</tr>
						<?php

					} elseif ( is_cart() ) {

						$package_field->output_html();
					}
				}

			} elseif ( $local_pickup_plus && $local_pickup_plus->is_per_order_selection_enabled() && $local_pickup_plus->is_item_handling_mode( 'automatic' ) ) {

				$package_field    = new WC_Local_Pickup_Plus_Pickup_Location_Package_Field( $package_index );
				$package          = $package_field->get_package();
				$shipping_rate    = $shipping_rate->id;
				$shipping_rates   = isset( $package['rates'] ) ? array_keys( $package['rates'] ) : array( $shipping_rate );

				if ( end( $shipping_rates ) === $shipping_rate ) {

					self::$pickup_package_form_output[ $package_index ] = true;

					echo $package_field->get_package_handling_toggle_html();
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
	 * Returns a pickup location ID when only a single pickup location exists for a product in cart item.
	 *
	 * @since 2.2.0
	 *
	 * @param array $contents cart item contents
	 * @return int a pickup location ID or 0 if no pickup location is determined or there is more than one possible pickup location for the products
	 */
	private function get_package_pickup_location_id( array $contents ) {

		$location_ids = array();

		foreach ( $contents as $item ) {

			$package_product = isset( $item['data'] ) ? $item['data'] : null;

			if ( $package_product instanceof WC_Product ) {

				$available_locations = wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $package_product, array( 'fields' => 'ids' ) );
				$location_ids[]      = 1 === count( $available_locations ) ? (int) current( $available_locations ) : 0;
			}
		}

		$location_ids    = array_unique( $location_ids );
		$pickup_location = 1 === count( $location_ids ) ? wc_local_pickup_plus_get_pickup_location( current( $location_ids ) ) : null;

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
	 * @param array $pickup_data optional pickup data from session
	 * @return bool
	 */
	private function cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data ) {

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

			$new_packages = array();
			$cart_items   = WC()->cart->cart_contents;
			$index        = 0;
			$pickup_items = array();
			$ship_items   = array();

			foreach ( $cart_items as $cart_item_key => $cart_item ) {

				if ( $this->cart_item_should_be_picked_up( $cart_item, $cart_item_key, $pickup_data ) ) {

					$cart_item_pickup_location_id = ! empty( $pickup_data[ $cart_item_key ]['pickup_location_id'] ) ? (int) $pickup_data[ $cart_item_key ]['pickup_location_id'] : 0;

					// special handling for cases where there is only a single pickup location possible
					if ( 0 === $cart_item_pickup_location_id ) {
						$contents                     = isset( $cart_item['contents'] ) ? $cart_item['contents'] : array();
						$cart_item_pickup_location_id = $this->get_package_pickup_location_id( $contents );
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

			$pickup_items = $items['pickup_items'];
			$ship_items   = $items['ship_items'];

			// create pickup packages and put pickup items with the same pickup location in the same package too
			if ( ! empty( $pickup_items ) ) {

				$same_pickup_locations = array();

				foreach ( $pickup_items as $item_key => $pickup_item ) {

					$pickup_location_id = isset( $pickup_item['pickup_location_id'] ) ? (int) $pickup_item['pickup_location_id'] : 0;

					// special handling for cases where there is only a single pickup location possible (this has to run again to account for filtered array)
					if ( 0 === $pickup_location_id ) {

						$contents           = is_array( $pickup_items ) ? $pickup_items : array();
						$pickup_location_id = $this->get_package_pickup_location_id( $contents );

						// there might be a setup combination where we can look for the first pickup location available
						if ( ! $pickup_location_id > 0 && count( $pickup_items ) > 1 ) {
	                        $pickup_location_id = $this->get_first_pickup_location_id();
						}
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
	 * Returns the first pickup location ID among all the pickup locations available.
	 *
	 * This is only used internally when a per order / automatic checkout mode may result in a initial state with two packages where the latter does not have a pickup location ID set.
	 *
	 * @since 2.3.0
	 *
	 * @return int
	 */
	private function get_first_pickup_location_id() {

		$local_pickup_plus  = wc_local_pickup_plus_shipping_method();
		$pickup_location_id = 0;

		if (      $local_pickup_plus
		     &&   $local_pickup_plus->is_per_order_selection_enabled()
		     &&   $local_pickup_plus->is_item_handling_mode( 'automatic' )
		     && ! $local_pickup_plus->is_enhanced_search_enabled() ) {

			$all_pickup_locations = wc_local_pickup_plus()->get_pickup_locations_instance()->get_sorted_pickup_locations();
			$pickup_location      = ! empty( $all_pickup_locations ) ? current( $all_pickup_locations ) : null;
			$pickup_location_id   = $pickup_location ? $pickup_location->get_id() : $pickup_location_id;
		}

		return $pickup_location_id;
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
		     && $local_pickup_plus->is_per_order_selection_enabled()
		     && $local_pickup_plus->is_item_handling_mode( 'automatic' )
		     && ( $shipping_zone = wc_get_shipping_zone( $package ) ) ) {

			/* @type \WC_Shipping_Method[] $shipping_methods */
			$shipping_methods = $shipping_zone->get_shipping_methods( true );

			if ( is_array( $shipping_methods ) ) {

				$available_rates = $this->get_rates_for_package( $package );

				// there are no other shipping methods so we should offer pickup (or split the package later if there are ship-only items)
				$pickup = 0 === count( array_unique( $available_rates ) );
			}
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

				if (    ! isset( $package['ship_via'] )
				     &&   isset( $package['rates'][ $local_pickup_plus_id ] ) ) {

					if (    isset( $package['contents'] )
					     && is_array( $package['contents'] )
					     && $this->package_should_be_picked_up( $package ) ) {

						// so we don't unset Local Pickup Rates, however, we need to check if there are any items that cannot be picked up
						foreach ( $package['contents'] as $item_key => $item ) {

							if (      isset( $item['data'] )
							     && ! wc_local_pickup_plus_product_can_be_picked_up( $item['data'] ) ) {

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
	private function get_rates_for_package( $package ) {

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
	 * Add a flag to mark the total number of packages meant for shipping, and the total number of packages meant for pickup.
	 *
	 * This can be useful to JS scripts that need to quickly grab the count, for example to toggle the visibility of the shipping address fields.
	 *
	 * @internal
	 *
	 * @since 2.1.1
	 */
	public function packages_count() {

		if (    true !== self::$packages_count_output
		     && is_checkout()
		     && $packages = WC()->shipping()->get_packages() ) {

			$shipping_method_id = wc_local_pickup_plus_shipping_method_id();
			$packages_to_ship   = 0;
			$packages_to_pickup = 0;

			foreach ( $packages as $package ) {
				if ( isset( $package['ship_via'] ) && in_array( $shipping_method_id, $package['ship_via'], true ) ) {
					$packages_to_pickup++;
				} else {
					$packages_to_ship++;
				}
			}

			?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input
						type="hidden"
						id="wc-local-pickup-plus-packages-to-ship"
						value="<?php echo $packages_to_ship; ?>"
					/>
					<input
						type="hidden"
						id="wc-local-pickup-plus-packages-to-pickup"
						value="<?php echo $packages_to_pickup; ?>"
					/>
				</td>
			</tr>
			<?php

			self::$packages_count_output = true;
		}
	}


	/**
	 * Ensure that cash on delivery stays enabled when there are multiple pickup packages.
	 *
	 * @internal
	 *
	 * @since 2.1.1
	 *
	 * @param array $available_gateways associative array
	 * @return array
	 */
	public function enable_cash_on_delivery( $available_gateways ) {

		// ensure we don't enable this for "add payment method" or other places we shouldn't
		// this will return true for checkout and the order pay page
		if ( is_checkout() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( ! array_key_exists( 'cod', $available_gateways ) && $local_pickup_plus && $local_pickup_plus->is_available() ) {

				/* @type \WC_Payment_Gateway $gateway */
				foreach ( WC()->payment_gateways()->payment_gateways as $gateway ) {

					if ( 'WC_Gateway_COD' === get_class( $gateway ) && isset( $gateway->settings, $gateway->settings['enabled'] ) && 'yes' === $gateway->settings['enabled'] ) {

						if ( empty( $gateway->settings['enable_for_methods'] ) || in_array( wc_local_pickup_plus_shipping_method_id(), $gateway->settings['enable_for_methods'], true ) ) {

							foreach ( WC()->shipping()->get_packages() as $package ) {

								if ( ! empty( $package['rates'] ) && array_key_exists( $local_pickup_plus->get_method_id(), $package['rates'] ) ) {

									$available_gateways['cod'] = $gateway;
									break;
								}
							}
						}
					}
				}
			}
		}

		return $available_gateways;
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

					if ( $price_adjustment && $price_adjustment->is_discount() ) {

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
		$shipping_methods    = isset( $posted_data['shipping_method'] ) ? (array) $posted_data['shipping_method'] : array();
		$exception_message   = '';

		// check if there are any packages meant for local pickup
		if ( $local_pickup_packages = ! empty( $shipping_methods ) ? array_keys( $shipping_methods, $local_pickup_method->get_method_id() ) : null ) {

			$pickup_location_ids  = isset( $_POST['shipping_method_pickup_location_id'] ) ? $_POST['shipping_method_pickup_location_id'] : array();
			$pickup_dates         = isset( $_POST['shipping_method_pickup_date'] )        ? $_POST['shipping_method_pickup_date']        : array();

			foreach ( $local_pickup_packages as $package_id ) {

				$error_messages = array();

				// a pickup location has not been chosen:
				if ( empty( $pickup_location_ids[ $package_id ] ) ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$error_messages['pickup_location_id'] = sprintf( __( 'Please select a pickup location if you intend to use %s as shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
				}

				// a pickup date has not been set, but it's mandatory:
				if ( empty( $pickup_dates[ $package_id ] ) && 'required' === $local_pickup_method->pickup_appointments_mode() ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$error_messages['pickup_date'] = sprintf( __( 'Please choose a date to schedule a pickup when using %s shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
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
				$error_messages = apply_filters( 'wc_local_pickup_plus_validate_pickup_checkout', $error_messages, $package_id, $posted_data );

				if ( ! empty( $error_messages ) && is_array( $error_messages ) ) {
					$exception_message = implode( '<br />', $error_messages );
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

		if ( '' !== $exception_message ) {
			throw new Exception( $exception_message );
		} elseif ( $session = wc_local_pickup_plus()->get_session_instance() ) {
			$session->delete_default_handling();
		}
	}


}
