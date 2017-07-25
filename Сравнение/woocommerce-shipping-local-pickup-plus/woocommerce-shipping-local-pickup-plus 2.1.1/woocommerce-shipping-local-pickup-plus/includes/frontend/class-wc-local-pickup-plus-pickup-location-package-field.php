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
class WC_Local_Pickup_Plus_Pickup_Location_Package_Field {


	/** @var int|string key index of current package this field is associated to */
	private $package_id;

	/** @var array package the current package this field is associated to */
	private $package;


	/**
	 * Field constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string $package_id the package key index
	 */
	public function __construct( $package_id ) {

		$this->package_id = $package_id;
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
	private function get_package() {

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
		return ! $this->pickup_location_has_changed() ? wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $this->get_package_id(), 'pickup_date' ) : '';
	}


	/**
	 * Detect whether the pickup location ID was updated by the user.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function pickup_location_has_changed() {

		$package_session_data = wc_local_pickup_plus()->get_session_instance()->get_package_pickup_data( $this->get_package_id() );
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

			$pickup_appointments = $shipping_method->pickup_appointments_mode();

			ob_start();

			?>
			<tr class="shipping pickup_location">
				<th><?php esc_html_e( 'Pickup Location', 'woocommerce-shipping-local-pickup-plus' ); ?></th>
				<td class="update_totals_on_change">
					<div
						id="pickup-location-field-for-package-<?php echo esc_attr( $this->get_package_id() ); ?>"
						class="pickup-location-field pickup-location-package-field"
						data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>">

						<?php if ( $chosen_location = $this->get_pickup_location() ) : ?>

							<?php // record the chosen pickup location ID ?>

							<input
								type="hidden"
								name="shipping_method_pickup_location_id[<?php echo esc_attr( $this->get_package_id() ); ?>]"
								value="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
								data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
							/>

							<?php // record cart items to pickup ?>

							<input
								type="hidden"
								name="shipping_method_pickup_items[<?php echo esc_attr( $this->get_package_id() ); ?>]"
								value="<?php echo implode( ',', $this->get_cart_items() ); ?>"
								data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
							/>

							<?php // display pickup location address & description ?>

							<div class="pickup-location-address">
								<?php $address     =  $chosen_location->get_address()->get_formatted_html( true ); ?>
								<?php echo ! empty( $address ) ? wp_kses_post( $address . '<br />' ) : ''; ?>
								<?php $description = $chosen_location->get_description(); ?>
								<?php echo ! empty( $description ) ? wp_kses_post( $description . '<br />' ) : ''; ?>
							</div>

							<?php // record the chosen pickup date & display time schedule for the chosen day ?>

							<?php if ( 'disabled' !== $pickup_appointments ) : ?>

								<?php $chosen_date = $this->get_pickup_date(); ?>

								<div class="pickup-location-appointment">

									<div class="pickup-location-calendar">

										<small class="pickup-location-field-label">
											<?php /* translators: Placeholder: %s - outputs an "(optional)" note if pickup appointments are optional */
											printf( __( 'Schedule a pickup appointment %s', 'woocommerce-shipping-local-pickup-plus' ), 'required' !== $pickup_appointments ? __( '(optional)', 'woocommerce-shipping-local-pickup-plus' ) : '' ); ?>
											<?php if ( 'required' === $pickup_appointments ) : ?>
												<abbr class="required" title="<?php esc_attr_e( 'Required', 'woocommerce-shipping-local-pickup-plus' ); ?>" style="border:none;">*</abbr>
											<?php endif; ?>
										</small>

										<input
											type="text"
											readonly="readonly"
											<?php echo 'required' === $pickup_appointments ? 'required="required"' : ''; ?>
											id="wc-local-pickup-plus-datepicker-<?php echo esc_attr( $this->get_package_id() ); ?>"
											class="pickup-location-appointment-date"
											name="shipping_method_pickup_date[<?php echo esc_attr( $this->get_package_id() ); ?>]"
											value="<?php echo esc_attr( $chosen_date ); ?>"
											style="display:inline-block; width:80%;"
											data-location-id="<?php echo esc_attr( $chosen_location->get_id() ); ?>"
											data-package-id="<?php echo esc_attr( $this->get_package_id() ); ?>"
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

							<?php endif; ?>

						<?php else : ?>

							<?php // the customer has previously selected items for pickup without specifying a location ?>

							<em><?php esc_html_e( 'Please choose a pickup location', 'woocommerce-shipping-local-pickup-plus' ); ?></em>

						<?php endif; ?>

						<?php $item_details = $this->get_cart_items_details(); ?>
						<?php if ( ! empty( $item_details ) && is_array( $item_details ) ) : ?>
							<p class="woocommerce-shipping-contents"><small><?php echo esc_html( implode( ', ', $item_details ) ); ?></small></p>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			<?php

			$field_html .= ob_get_clean();
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
	 * Output the field HTML.
	 *
	 * @since 2.0.0
	 */
	public function output_html() {

		echo $this->get_html();
	}


}
