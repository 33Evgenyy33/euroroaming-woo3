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
