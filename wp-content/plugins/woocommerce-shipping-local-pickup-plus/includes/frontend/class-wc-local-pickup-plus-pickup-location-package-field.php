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
 * Field component to attach pickup data and schedule an appointment for items to be picked up at checkout.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Pickup_Location_Package_Field extends WC_Local_Pickup_Plus_Pickup_Location_Field {


	/** @var int|string key index of current package this field is associated to */
	private $package_id;

	/** @var array package the current package this field is associated to */
	private $package;

	/** @var array associative array to cache the latest pickup date (values) associated to a pickup location id (keys) */
	private $location_pickup_date = array();


	/**
	 * Field constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $package_id the package key index
	 */
	public function __construct( $package_id ) {

		$this->object_type = 'package';
		$this->package_id  = $package_id;
	}


	/**
	 * Get the ID of the package for this field.
	 *
	 * @since 2.0.0
	 *
	 * @return int|string
	 */
	private function get_package_id() {
		return $this->package_id;
	}


	/**
	 * Get the current package for this field.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array
	 */
	public function get_package() {

		$package_id = $this->get_package_id();

		if ( null !== $package_id ) {

			if ( empty( $this->package ) ) {

				$packages = WC()->shipping()->get_packages();

				if ( ! empty( $packages[ $package_id ] ) ) {
					$this->package = $packages[ $package_id ];
				}
			}
		}

		return empty( $this->package ) ? array() : $this->package;
	}


	/**
	 * Get a package key,
	 *
	 * @since 2.0.0
	 *
	 * @param string $key the key to retrieve a value for
	 * @param null|mixed $default the default value (optional)
	 * @return null|string|int|array
	 */
	private function get_package_key( $key = null, $default = null ) {

		$value   = $default;
		$package = $this->get_package();

		if ( '' !== $key && is_string( $key ) && ! empty( $package ) ) {
			$value = isset( $this->package[ $key ] ) ? $this->package[ $key ] : $value;
		}

		return $value;
	}


	/**
	 * Returns the current product object, if there is only a single one in package.
	 *
	 * @since 2.2.0
	 *
	 * @return \WC_Product|null
	 */
	private function get_single_product() {

		$product = null;
		$package = $this->get_package();

		if ( ! empty( $package['contents'] ) && 1 === count( $package['contents'] ) ) {
			$content = current( $package['contents'] );
			$product = isset( $content['data'] ) && $content['data'] instanceof WC_Product ? $content['data'] : null;
		}

		return $product;
	}


	/**
	 * Get the ID of the pickup location associated with the package.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	private function get_pickup_location_id() {
		return $this->get_package_key( 'pickup_location_id',0 );
	}


	/**
	 * Get the pickup location associated with the package.
	 *
	 * @since 2.0.0
	 *
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	private function get_pickup_location() {

		$pickup_location_id = $this->get_pickup_location_id();

		// special handling when there is only a single pickup location
		if ( 0 === $pickup_location_id ) {

			$package = $this->get_package();

			if (    ! empty( $package )
			     &&   isset( $package['contents'] )
			     &&   is_array( $package['contents'] ) ) {

				$location_ids = array();

				foreach ( $package['contents'] as $item ) {

					$package_product = isset( $item['data'] ) ? $item['data'] : null;

					if ( $package_product instanceof WC_Product ) {

						$available_locations = wc_local_pickup_plus()->get_products_instance()->get_product_pickup_locations( $package_product, array( 'fields' => 'ids' ) );
						$location_ids[]      = ! empty( $available_locations ) && 1 === count( $available_locations ) ? current( $available_locations ) : 0;
					}
				}

				$location_ids       = array_unique( $location_ids );
				$pickup_location_id = 1 === count( $location_ids ) ? current( $location_ids ) : 0;
			}
		}

		return $pickup_location_id > 0 ? wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) : null;
	}


	/**
	 * Get any set pickup appointment for the package pickup.
	 *
	 * @since 2.0.0
	 *
	 * @return string a date as a string
	 */
	private function get_pickup_date() {

		$pickup_date        = '';
		$pickup_location_id = $this->get_pickup_location_id();

		if ( 0 === $pickup_location_id || ! $this->pickup_location_has_changed() ) {
			$pickup_date = $this->get_pickup_data( 'pickup_date' );
		}

		if ( empty( $pickup_date ) ) {
			$pickup_date = array_key_exists( $pickup_location_id, $this->location_pickup_date ) ? $this->location_pickup_date[ $pickup_location_id ] : '';
		} else {
			$this->location_pickup_date[ $pickup_location_id ] = $pickup_date;
		}

		return $pickup_date;
	}


	/**
	 * Detect whether the pickup location ID was updated by the user.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function pickup_location_has_changed() {

		$package_session_data = $this->get_pickup_data();
		$pickup_location_id   = $this->get_pickup_location_id();

		return ! empty( $package_session_data['pickup_location_id'] ) && $pickup_location_id !== (int) $package_session_data['pickup_location_id'];
	}


	/**
	 * Get the package cart items.
	 *
	 * This is useful later when submitting the checkout form to associate a order line items to a package and thus an order shipping item.
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_order_line_item_to_package()
	 *
	 * @since 2.0.0
	 *
	 * @return int[]|string[]
	 */
	private function get_cart_items() {

		$items   = array();
		$package = $this->get_package();

		if ( ! empty( $package['contents'] ) && is_array( $package['contents'] ) ) {
			foreach ( array_keys( $package['contents'] ) as $cart_item_key  ) {
				$items[] = $cart_item_key;
			}
		}

		return $items;
	}


	/**
	 * Get cart item details for the current package.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array of product names and quantities
	 */
	private function get_cart_items_details() {

		$items   = array();
		$package = $this->get_package();

		if ( ! empty( $package['contents'] ) && is_array( $package['contents'] ) ) {

			foreach ( $package['contents'] as $cart_item_key => $cart_item ) {

				if ( isset( $cart_item['data'], $cart_item['quantity'] ) ) {

					$item_product = $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;
					$item_qty     = max( 0, abs( $cart_item['quantity'] ) );

					if ( $item_product && $item_qty > 0 ) {

						$product_name = SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? $item_product->get_name() : $item_product->get_title();

						/* translators: Placeholders: %1$s product name, %2$s product quantity - e.g. "Product name x2" */
						$items[ $cart_item_key ] = sprintf( __( '%1$s &times; %2$s', 'woocommerce-shipping-local-pickup-plus' ), $product_name, $item_qty );
					}
				}
			}
		}

		/**
		 * Filter the pickup package details.
		 *
		 * @see \WC_Local_Pickup_Plus_Checkout::maybe_hide_pickup_package_item_details()
		 * @see \wc_cart_totals_shipping_html() for a similar filter in WooCommerce
		 *
		 * @since 2.0.0
		 *
		 * @param array $items an array of item keys and name/quantity details as strings
		 * @param array $package the package for pickup the details are meant for
		 */
		return apply_filters( 'wc_local_pickup_plus_shipping_package_details_array', $items, $package );
	}


	/**
	 * Gets the pickup location select HTML.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_pickup_location_html() {

		$shipping_method = wc_local_pickup_plus_shipping_method();
		$chosen_location = $this->get_pickup_location();

		ob_start(); ?>

		<?php if ( $shipping_method->is_per_order_selection_enabled() ) : ?>

			<?php echo $this->get_location_select_html( $this->get_package_id(), $chosen_location, $this->get_single_product() ); ?>

		<?php elseif ( $chosen_location ) : ?>

			<?php // record the chosen pickup location ID ?>

			<input
				type="hidden"
				name="shipping_method_pickup_location_id[<?php echo esc_attr( $this->get_package_id() ); ?>]"
				value="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
				data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
			/>

		<?php endif; ?>

		<?php if ( $chosen_location ) : ?>

			<?php // display pickup location name, address & description ?>

			<div class="pickup-location-address">

				<?php if ( is_cart() && $shipping_method->is_per_item_selection_enabled() ) : ?>
					<?php /* translators: Placeholder: %s - the name of the pickup location */
					echo sprintf( __( 'Pickup Location: %s', 'woocommerce-shipping-local-pickup-plus' ), esc_html( $chosen_location->get_name() ) ) . '<br />'; ?>
				<?php endif; ?>

				<?php $address     = $chosen_location->get_address()->get_formatted_html( true ); ?>
				<?php echo ! empty( $address ) ? wp_kses_post( $address . '<br />' ) : ''; ?>
				<?php $description = $chosen_location->get_description(); ?>
				<?php echo ! empty( $description ) ? wp_kses_post( $description . '<br />' ) : ''; ?>
			</div>

		<?php elseif ( is_checkout() ) : ?>

			<?php // the customer has previously selected items for pickup without specifying a location ?>

			<em><?php esc_html_e( 'Please choose a pickup location', 'woocommerce-shipping-local-pickup-plus' ); ?></em>

		<?php endif; ?>

		<?php // record cart items to pickup ?>

		<input
			type="hidden"
			name="wc_local_pickup_plus_pickup_items[<?php echo esc_attr( $this->get_package_id() ); ?>]"
			value="<?php echo implode( ',', $this->get_cart_items() ); ?>"
			data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
		/>

		<?php

		return ob_get_clean();
	}


	/**
	 * Gets the pickup appointment form HTML.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_pickup_appointments_html() {

		$mode            = wc_local_pickup_plus_shipping_method()->pickup_appointments_mode();
		$chosen_location = $this->get_pickup_location();
		$chosen_date     = $this->get_pickup_date();
		$html            = '';

		if ( $chosen_location ) {

			ob_start();

			?>
			<div class="pickup-location-appointment update_totals_on_change">

				<div class="pickup-location-calendar">

					<small class="pickup-location-field-label">
						<?php /* translators: Placeholder: %s - outputs an "(optional)" note if pickup appointments are optional */
						printf( __( 'Schedule a pickup appointment %s', 'woocommerce-shipping-local-pickup-plus' ), 'required' !== $mode ? __( '(optional)', 'woocommerce-shipping-local-pickup-plus' ) : '' ); ?>
						<?php if ( 'required' === $mode ) : ?>
							<abbr class="required" title="<?php esc_attr_e( 'Required', 'woocommerce-shipping-local-pickup-plus' ); ?>" style="border:none;">*</abbr>
						<?php endif; ?>
					</small>

					<input
						type="text"
						readonly="readonly"
						<?php echo 'required' === $mode ? 'required="required"' : ''; ?>
						id="wc-local-pickup-plus-datepicker-<?php echo esc_attr( $this->get_package_id() ); ?>"
						class="pickup-location-appointment-date"
						name="shipping_method_pickup_date[<?php echo esc_attr( $this->get_package_id() ); ?>]"
						value="<?php echo esc_attr( $chosen_date ); ?>"
						style="display:inline-block; width:80%;"
						data-location-id="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
						data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
						data-pickup-date="<?php echo esc_attr( $chosen_date ); ?>"
					/><span class="pickup-location-calendar-icon"></span>

					<div class="pickup-location-schedule" <?php if ( empty( $chosen_date ) ) { echo ' style="display:none;" '; } ?>>

						<?php $chosen_day    = ! empty( $chosen_date ) && is_string( $chosen_date ) ? date( 'w', strtotime( $chosen_date ) ) : null; ?>
						<?php $opening_hours = ! empty( $chosen_day )  && $chosen_location          ? $chosen_location->get_business_hours()->get_schedule( $chosen_day ) : null; ?>

						<?php if ( ! empty( $opening_hours ) ) : ?>

							<small class="pickup-location-field-label"><?php
								/* translators: Placeholder: %s - day of the week name */
								printf( __( 'Opening hours for pickup on %s:', 'woocommerce-shipping-local-pickup-plus' ),
									'<strong>' . date_i18n( 'l', strtotime( $chosen_date ) ) . '</strong>'
								); ?></small>
							<ul>
								<?php foreach ( $opening_hours as $time_string ) : ?>
									<li><small><?php echo esc_html( $time_string ); ?></small></li>
								<?php endforeach; ?>
							</ul>

						<?php endif; ?>

					</div>

				</div>

			</div>
			<?php

			$html = ob_get_clean();
		}

		return $html;
	}


	/**
	 * Returns the default handling toggle.
	 *
	 * This is available on both cart totals and checkout review if "per order" pickup mode and automatic grouping are enabled in settings.
	 *
	 * @since 2.2.0
	 *
	 * @return string HTML
	 */
	public function get_package_handling_toggle_html() {

		$toggle            = '';
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (    $local_pickup_plus
		     && $local_pickup_plus->is_per_order_selection_enabled()
		     && $local_pickup_plus->is_item_handling_mode( 'automatic' ) ) {

			$package          = $this->get_package();
			$package_contents = isset( $package['contents'] ) && is_array( $package['contents'] ) ? $package['contents'] : array();
			$default_handling = $local_pickup_plus->get_default_handling();
			$total_items      = 0;
			$can_be_picked_up = 0;
			$can_be_shipped   = 0;

			foreach ( $package_contents as $item ) {

				$product  = isset( $item['data'] )     ? $item['data']           : null;
				$quantity = isset( $item['quantity'] ) ? (int) $item['quantity'] : 1;

				if ( $product instanceof WC_Product ) {

					if ( wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {

						$can_be_picked_up += $quantity;

						if ( ! wc_local_pickup_plus_product_must_be_picked_up( $product ) ) {
							$can_be_shipped += $quantity;
						}

					} elseif ( $product->needs_shipping() && ! wc_local_pickup_plus_product_must_be_picked_up( $product ) ) {

						$can_be_shipped += $quantity;
					}

					$total_items += $quantity;
				}
			}

			// only show if toggling is a logical possibility
			if (    ( $can_be_picked_up > 0 && 'ship'   === $default_handling && 0 !== $can_be_shipped )
			     || ( $can_be_shipped   > 0 && 'pickup' === $default_handling && 0 !== $can_be_picked_up ) ) {

				$show_toggle = true;

				// do not show the toggle if toggling from pickup to shipping but there are no available shipping methods
				if (    isset( $package['rates'] )
				     && 1 === count( $package['rates'] )
				     && $local_pickup_plus->get_method_id() === key( $package['rates'] ) ) {

					$available_rates = $rates = array();

					$check_package_rates = $package;

					unset( $check_package_rates['ship_via'] );

					if ( $shipping_zone = wc_get_shipping_zone( $package ) ) {

						$shipping_methods = $shipping_zone->get_shipping_methods( true );

						if ( is_array( $shipping_methods ) ) {

							foreach ( $shipping_methods as $shipping_method ) {

								$rates = $shipping_method->get_rates_for_package( $check_package_rates );

								if ( ! empty( $rates ) ) {
									$available_rates = array_merge( $available_rates, $rates );
								}
							}
						}

						if ( 0 === count( $available_rates ) ) {

							$show_toggle      = false;
							$default_handling = 'pickup';
						}
					}
				}

				if ( $show_toggle ) {

					$ship_visibility   = 'pickup' === $default_handling ? 'style="display: none;"' : '';
					$ship_info         = _n( 'This item will be shipped.', 'These items will be shipped.', $total_items, 'woocommerce-shipping-local-pickup-plus' );
					$ship_label        = _n( 'Click if you want to pickup this item', 'Click if you want to pickup these items', $total_items, 'woocommerce-shipping-local-pickup-plus' );
					$ship_toggle       = '<a href="#" class="toggle-default-handling pickup">' . $ship_label . '.</a>';

					$pickup_visibility = 'ship' === $default_handling ? 'style="display: none;"' : '';
					$pickup_info       = _n( 'This item is for pickup.', 'These items are for pick up.', $total_items, 'woocommerce-shipping-local-pickup-plus' );
					$pickup_label      = _n( 'Click if you want to ship this item', 'Click if you want these items to be shipped', $total_items, 'woocommerce-shipping-local-pickup-plus' );
					$pickup_toggle     = '<a href="#" class="toggle-default-handling ship">' . $pickup_label . '.</a>';

					ob_start();

					?>
					<p id="wc-local-pickup-plus-toggle-default-handling">
					<span <?php echo $pickup_visibility; ?>><?php
						printf( '%1$s <br /> %2$s', $pickup_info, $pickup_toggle ); ?></span>
						<span <?php echo $ship_visibility; ?>><?php
							printf( '%1$s <br /> %2$s', $ship_info, $ship_toggle ); ?></span>
						<?php echo is_cart() ? '<br />' : ''; ?>
					</p>
					<?php

					$toggle = ob_get_clean();
				}
			}
		}

		return $toggle;
	}


	/**
	 * Get the field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html      = '';
		$shipping_method = wc_local_pickup_plus_shipping_method();

		if ( $shipping_method && $shipping_method->is_available() ) {

			ob_start();

			?>
			<div
				id="pickup-location-field-for-<?php echo esc_attr( $this->get_package_id() ); ?>"
				class="pickup-location-field pickup-location-<?php echo sanitize_html_class( $this->get_object_type() ); ?>-field"
				data-pickup-object-id="<?php echo esc_attr( $this->get_package_id() ); ?>">

				<?php // display the selected location, or location select field ?>
				<?php echo $this->get_pickup_location_html(); ?>

				<?php // display the pickup appointment fields at checkout if enabled ?>
				<?php if ( is_checkout() && 'disabled' !== $shipping_method->pickup_appointments_mode() && $this->get_pickup_location() ) : ?>
					<?php echo $this->get_pickup_appointments_html(); ?>
				<?php endif; ?>

				<?php // display the item details list ?>
				<?php $item_details = $this->get_cart_items_details(); ?>
				<?php if ( ! empty( $item_details ) && is_array( $item_details ) ) : ?>
					<p class="woocommerce-shipping-contents"><small><?php echo esc_html( implode( ', ', $item_details ) ); ?></small></p>
				<?php endif; ?>

				<?php // display a package handling toggle conditionally to checkout display settings  ?>
				<?php if ( $shipping_method->is_per_order_selection_enabled() && $shipping_method->is_item_handling_mode('automatic' ) ) : ?>
					<?php echo $this->get_package_handling_toggle_html(); ?>
				<?php endif; ?>
			</div>
			<?php

			$field_html = ob_get_clean();
		}

		/**
		 * Filter the package pickup location field HTML.
		 *
		 * @since 2.0.0
		 *
		 * @param string $field_html input field HTML
		 * @param int|string $package_id the current package identifier
		 * @param array $package the current package array
		 */
		return apply_filters( 'wc_local_pickup_plus_get_pickup_location_package_field_html', $field_html, $this->get_package_id(), $this->get_package() );
	}


	/**
	 * Gets the pickup location data.
	 *
	 * @since 2.1.0
	 *
	 * @param string $piece specific data to get. Defaults to getting all available data.
	 * @return array|string
	 */
	protected function get_pickup_data( $piece = '' ) {
		return wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $this->get_package_id(), $piece );
	}


	/**
	 * Sets the pickup location data.
	 *
	 * @since 2.1.0
	 * @param array $pickup_data pickup data
	 */
	protected function set_pickup_data( array $pickup_data ) {
		wc_local_pickup_plus()->get_session_instance()->set_package_pickup_data( $this->get_package_id(), $pickup_data );
	}


	/**
	 * Deletes the pickup location data.
	 *
	 * @since 2.1.0
	 */
	protected function delete_pickup_data() {
		wc_local_pickup_plus()->get_session_instance()->delete_package_pickup_data( $this->get_package_id() );
	}


}
