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
 * Field component to select a pickup location for a cart item.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field {


	/** @var string $cart_item_key the ID of the cart item for this field */
	private $cart_item_key;


	/**
	 * Field constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $cart_item_key the current cart item key
	 */
	public function __construct( $cart_item_key ) {

		$this->cart_item_key = $cart_item_key;
	}


	/**
	 * Get the field ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string|int
	 */
	public function get_cart_item_id() {
		return $this->cart_item_key;
	}


	/**
	 * Get the cart item for this field.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_cart_item() {

		$cart_item    = array();
		$cart_item_id = $this->get_cart_item_id();

		if ( ! empty( $cart_item_id ) && ! WC()->cart->is_empty() ) {

			$cart_contents = WC()->cart->cart_contents;

			if ( isset( $cart_contents[ $cart_item_id ] ) ) {
				$cart_item = $cart_contents[ $cart_item_id ];
			}
		}

		return $cart_item;
	}


	/**
	 * Get the ID of the product for the cart item related to this field.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	private function get_product_id() {

		$cart_item = $this->get_cart_item();

		return isset( $cart_item['product_id'] ) ? abs( $cart_item['product_id'] ) : 0;
	}


	/**
	 * Get the product object for the cart item related to this field.
	 *
	 * @since 2.0.0
	 *
	 * @return null|\WC_Product
	 */
	private function get_product() {

		$product_id = $this->get_product_id();
		$product    = $product_id > 0 ? wc_get_product( $product_id ) : null;

		return $product instanceof WC_Product ? $product : null;
	}


	/**
	 * Get the cart item pickup data, if set.
	 *
	 * @since 2.0.0
	 *
	 * @param string $piece optionally get a specific pickup data key instead of the whole array (default)
	 * @return string|int|\WC_Local_Pickup_Plus_Pickup_Location|array
	 */
	private function get_pickup_data( $piece = '' ) {
		return wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $this->get_cart_item_id(), $piece );
	}


	/**
	 * Save pickup data to session.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pickup_data
	 */
	private function set_pickup_data( array $pickup_data ) {
		wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $this->get_cart_item_id(), $pickup_data );
	}


	/**
	 * Reset pickup data for the cart item (defaults to shipping).
	 *
	 * @since 2.0.0
	 */
	private function delete_pickup_data() {
		wc_local_pickup_plus()->get_session_instance()->delete_cart_item_pickup_data( $this->get_cart_item_id() );
	}


	/**
	 * Get the current user default lookup area.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_user_default_lookup_area() {

		$user   = wp_get_current_user();
		$lookup = array(
			'country' => '',
			'state'   => '',
		);

		if ( $user instanceof WP_User && ( $default_pickup = wc_local_pickup_plus_get_user_default_pickup_location( $user ) ) ) {
			$lookup['country'] = $default_pickup->get_address( 'country' );
			$lookup['state']   = $default_pickup->get_address( 'state' );
		}

		return $lookup;
	}


	/**
	 * Get default lookup country:state area.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_lookup_area() {

		$plugin      = wc_local_pickup_plus();
		$geocoding   = $plugin->geocoding_enabled();
		$codes       = $plugin->get_pickup_locations_instance()->get_available_pickup_location_country_state_codes();
		$chosen      = $this->get_pickup_data( 'lookup_area' );
		$country     = '';
		$state       = '';

		// get selected value
		if ( ! empty( $chosen ) ) {
			if ( is_string( $chosen ) ) {
				$chosen = explode( ':', $chosen );
			}
			if ( is_array( $chosen ) ) {
				$country = isset( $chosen[0] ) ? $chosen[0] : '';
				$state   = isset( $chosen[1] ) ? $chosen[1] : '';
			}
		}

		// get or fallback to default value
		if ( empty( $chosen ) || empty( $country ) ) {

			if ( wc_local_pickup_plus_get_user_default_pickup_location() ) {

				$preferred_lookup = $this->get_user_default_lookup_area();

				if ( ! empty( $preferred_lookup['country'] ) ) {

					$country = $preferred_lookup['country'];
					$state   = ! empty( $preferred_lookup['state'] ) ? $preferred_lookup['state'] : '';

				} else {

					if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

						$location = wc_get_customer_default_location();
						$country  = isset( $location['country'] ) ? $location['country'] : '';
						$state    = isset( $location['state'] )   ? $location['state'] : '';

					} else {

						$country = WC()->customer->get_default_country();
						$state   = WC()->customer->get_default_state();
					}
				}

			} elseif ( $geocoding ) {

				$country = 'anywhere';
				$state   = '';
			}
		}

		// sanity check:
		if ( '' === $country || ( ! in_array( "{$country}:{$state}", $codes, true ) && ! in_array( $country, $codes, true ) ) ) {
			$country = ! $geocoding ? WC()->countries->get_base_country() : 'anywhere';
			$state   = ! $geocoding ? WC()->countries->get_base_state()   : '';
		}

		return array(
			'country' => $country,
			'state'   => $state
		);
	}


	/**
	 * Get the default lookup area label.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_lookup_area_label() {

		$lookup = $this->get_lookup_area();

		if ( ! empty( $lookup['state'] ) ) {
			$states    = WC()->countries->get_states( $lookup['country'] );
			$label     = $states[ $lookup['state'] ];
		} elseif ( 'anywhere' === $lookup['country'] ) {
			$label     = __( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' );
		} else {
			$countries = WC()->countries->get_countries();
			$label     = $countries[ $lookup['country'] ];
		}

		return $label;
	}


	/**
	 * Get dropdown options with countries and states with available pickup locations.
	 *
	 * @see \WC_Countries::country_dropdown_options()
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	private function get_country_dropdown_options() {

		$chosen         = $this->get_lookup_area();
		$chosen_country = isset( $chosen['country'] ) ? $chosen['country'] : '';
		$chosen_state   = isset( $chosen['state'] )   ? $chosen['state']   : '';
		$countries      = wc_local_pickup_plus()->get_pickup_locations_instance()->get_available_pickup_location_countries();

		ob_start();

		if ( ! empty( $countries ) ) :

			?>
			<option value="anywhere"><?php esc_html_e( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
			<?php

			foreach ( $countries as $country_code => $country_label ) :

				if ( $states = wc_local_pickup_plus()->get_pickup_locations_instance()->get_available_pickup_location_states( $country_code, $this->get_product_id() ) ) :

					?>
					<optgroup label="<?php echo esc_attr( $country_label ); ?>">
						<?php foreach ( $states as $state_code => $state_label ) : ?>
							<option
								value="<?php echo esc_attr( "{$country_code}:{$state_code}" ); ?>"
								<?php if ( $chosen_country === $country_code && $chosen_state === $state_code ) { echo 'selected="selected"'; } ?>
							><?php echo esc_html( sprintf( '%1$s &mdash; %2$s', $country_label, $state_label ) ); ?></option>
						<?php endforeach; ?>
					</optgroup>
					<?php

				else :

					?>
					<option
						value="<?php echo esc_attr( $country_code ); ?>"
						<?php if ( $chosen_country === $country_code ) { echo 'selected="selected"'; } ?>
					><?php echo esc_html( $country_label ); ?></option>
					<?php

				endif;

			endforeach;

		endif;

		return ob_get_clean();
	}


	/**
	 * Get a dropdown with available country/state options for available pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	private function get_country_state_dropdown() {

		ob_start();

		?>
		<select
			id="pickup-location-lookup-area-for-cart-item-<?php echo esc_attr( $this->get_cart_item_id() ); ?>"
			class="wc-enhanced-select country_to_state country_select pickup-location-lookup-area"
			style="width:100%; max-width:512px;"
			placeholder="<?php echo esc_html_x( 'Choose an area&hellip;', 'Geographic area to search', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			data-placeholder="<?php echo esc_html_x( 'Choose an area&hellip;', 'Geographic area to search', 'woocommerce-shipping-local-pickup-plus' ); ?>"
			autocomplete="country">
			<?php if ( $this->use_enhanced_search() ) : ?>
				<?php echo $this->get_country_dropdown_options(); ?>
			<?php else : ?>
				<option value="anywhere" selected="selected"><?php esc_html_e( 'Anywhere', 'woocommerce-shipping-local-pickup-plus' ); ?></option>
			<?php endif; ?>
		</select>
		<?php

		return ob_get_clean();
	}


	/**
	 * Whether to enable enhanced search.
	 *
	 * If there are more than 80 published locations, use enhanced search (perhaps with geocoding) in lookup fields.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function use_enhanced_search() {

		$enabled = wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count() > 80;

		/**
		 * Whether to use an enhanced AJAX search in front end or a simpler dropdown.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $use_enhanced_search by default this is true if there are at least 80 public locations
		 */
		return (bool) apply_filters( 'wc_local_pickup_plus_enhanced_pickup_location_search_enabled', $enabled );;
	}


	/**
	 * Returns all locations available.
	 *
	 * This should be only used when simple dropdown is active and locations are less than a hundred or will cause performance issues.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[]
	 */
	private function get_all_pickup_locations() {

		$query_args = array();

		switch ( wc_local_pickup_plus_shipping_method()->pickup_locations_sort_order() ) {

			case 'location_alphabetical' :

				$query_args['order']   = 'ASC';
				$query_args['orderby'] = 'title';
				$pickup_locations      = wc_local_pickup_plus_get_pickup_locations( $query_args );

			break;

			case 'location_date_added' :

				$query_args['order']   = 'ASC';
				$query_args['orderby'] = 'date';
				$pickup_locations      = wc_local_pickup_plus_get_pickup_locations( $query_args );

			break;

			case 'distance_customer' :

				if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

					$country           = '';
					$state             = '';
					$customer_location = wc_get_customer_default_location();
					$shop_location     = wc_get_base_location();

					if ( isset( $customer_location['country'], $customer_location['state'] ) ) {
						$country = $customer_location['country'];
						$state   = $customer_location['state'];
					} elseif( isset( $shop_location['country'], $shop_location['state'] ) ) {
						$country = $shop_location['country'];
						$state   = $shop_location['state'];
					}

				} else {

					$country = WC()->customer->get_default_country();
					$country = empty( $country ) ? WC()->countries->get_base_country() : $country;
					$state   = WC()->customer->get_default_state();
					$state   = empty( $state )   ? WC()->countries->get_base_state()   : $state;
				}

				$coordinates = wc_local_pickup_plus()->get_geocoding_api_instance()->get_coordinates( array( 'country' => $country, 'state' => $state ) );
				$coordinates = empty( $coordinates ) ? array( 'lat' => 0.00000, 'lon' => 0.000000 ) : $coordinates;

				$pickup_locations = wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_by_distance( $coordinates, array( 'post_status' => 'publish', 'orderby' => 'distance_customer' ), '40000km' );

			break;

			default :
				$pickup_locations = wc_local_pickup_plus_get_pickup_locations();
			break;
		}

		return $pickup_locations;
	}


	/**
	 * Get the field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML
	 */
	public function get_html() {

		$field_html        = '';
		$cart_item_id      = '';
		$product           = null;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if ( $local_pickup_plus->is_available() ) {

			$product = $this->get_product();

			if ( $product && wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {

				$enhanced_search     = $this->use_enhanced_search();
				$cart_item_id        = $this->get_cart_item_id();
				$pickup_data         = $this->get_pickup_data();
				$should_be_picked_up = isset( $pickup_data['handling'] ) && 'pickup' === $pickup_data['handling'];
				$must_be_picked_up   = wc_local_pickup_plus_product_must_be_picked_up( $product );

				if ( ! empty( $pickup_data['pickup_location_id'] ) ) {
					$chosen_pickup_location = wc_local_pickup_plus_get_pickup_location( (int) $pickup_data['pickup_location_id'] );
				} else {
					$chosen_pickup_location = wc_local_pickup_plus_get_user_default_pickup_location();
				}

				// sanity check
				if ( ! wc_local_pickup_plus_product_can_be_picked_up( $product, $chosen_pickup_location ) ) {
					$chosen_pickup_location = null;
				}

				ob_start();

				?>
				<div
					id="pickup-location-field-for-cart-item-<?php echo esc_attr( $cart_item_id ); ?>"
					class="pickup-location-field pickup-location-cart-item-field"
					data-cart-item-id="<?php echo esc_attr( $cart_item_id ); ?>">

					<?php if ( ! $must_be_picked_up ) : ?>
						<small style="display: <?php echo   $should_be_picked_up ? 'none' : 'block'; ?>;">&rarr; <a class="enable-local-pickup"  href="#"><?php esc_html_e( 'I want to pickup this item',     'woocommerce-shipping-local-pickup-plus' ); ?></a></small>
						<small style="display: <?php echo ! $should_be_picked_up ? 'none' : 'block'; ?>;">&larr; <a class="disable-local-pickup" href="#"><?php esc_html_e( 'I want this item to be shipped', 'woocommerce-shipping-local-pickup-plus' ); ?></a></small>
					<?php else : ?>
						<?php $this->set_pickup_data( array( 'handling' => 'pickup' ) ); ?>
					<?php endif; ?>

					<div style="display: <?php echo $must_be_picked_up || $should_be_picked_up ? 'block' : 'none'; ?>;">

						<div
							id="pickup-location-lookup-area-field-for-cart-item-<?php echo esc_attr( $cart_item_id ); ?>"
							class="pickup-location-lookup-area-field"
							data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
							data-cart-item-id="<?php echo esc_attr( $cart_item_id ); ?>"
							<?php if ( ! $enhanced_search ) { echo 'style="display: none;"'; } ?>>
							<small
								class="pickup-location-current-lookup-area"
								<?php if ( ! $enhanced_search ) { echo 'style="display: none;"'; } ?>><?php
								$change = '<a class="pickup-location-change-lookup-area" href="#">' . strtolower( esc_html__( 'Change', 'woocommerce-shipping-local-pickup-plus' ) ) . '</a>';
								/* translators: Placeholder: %s - country or state name (or "Anywhere") */
								printf( __( 'Enter a postcode or city to search for pickup locations from: %s', 'woocommerce-shipping-local-pickup-plus' ) . ' (' . $change . ')', '<em class="pickup-location-current-lookup-area-label">' . $this->get_lookup_area_label() . '</em>' ); ?>
							</small>
							<div style="display: none;">
								<?php echo $this->get_country_state_dropdown(); ?>
							</div>
						</div>

						<?php if ( ! $enhanced_search ) : ?>

							<?php $pickup_locations = $this->get_all_pickup_locations(); ?>
							<select
								name="_pickup_location_id[<?php echo esc_attr( $cart_item_id ); ?>]"
								class="pickup-location-lookup"
								style="width:100%; max-width:512px;"
								placeholder="<?php esc_attr_e( 'Search locations&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>"
								data-cart-item-id="<?php echo esc_attr( $cart_item_id ); ?>"
								data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php foreach ( $pickup_locations as $pickup_location ) : ?>
									<?php if ( wc_local_pickup_plus_product_can_be_picked_up( $product, $pickup_location ) ) : ?>
										<option value="<?php echo esc_attr( $pickup_location->get_id() ); ?>" <?php selected( $pickup_location->get_id(), $chosen_pickup_location ? $chosen_pickup_location->get_id() : null, true ); ?>><?php echo esc_html( $pickup_location->get_name() ); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>

						<?php else : ?>

							<?php if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) : ?>

								<select
									name="_pickup_location_id[<?php echo esc_attr( $cart_item_id ); ?>]"
									class="pickup-location-lookup"
									style="width:100%; max-width:512px;"
									placeholder="<?php esc_attr_e( 'Search locations&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>"
									data-cart-item-id="<?php echo esc_attr( $cart_item_id ); ?>"
									data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
									<?php if ( $chosen_pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ) : ?>
										<option value="<?php echo $chosen_pickup_location->get_id(); ?>" selected><?php echo esc_html( $chosen_pickup_location->get_name() ); ?></option>
									<?php endif; ?>
								</select>

							<?php else : ?>

								<input
									type="hidden"
									name="_pickup_location_id[<?php echo esc_attr( $cart_item_id ); ?>]"
									class="pickup-location-lookup"
									style="width:100%; max-width:512px;"
									value="<?php echo $chosen_pickup_location ? $chosen_pickup_location->get_id() : ''; ?>"
									placeholder="<?php esc_attr_e( 'Search locations&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>"
									data-cart-item-id="<?php echo esc_attr( $cart_item_id ); ?>"
									data-product-id="<?php echo esc_attr( $this->get_product_id() ); ?>"
								/>

							<?php endif; ?>

						<?php endif; ?>

					</div>

				</div>
				<?php

				$field_html .= ob_get_clean();

			} elseif ( $product ) {

				$this->delete_pickup_data();

				$field_html .= '<br /><em><small>' . __( 'This item can only be shipped', 'woocommerce-shipping-local-pickup-plus' ) . '</small></em>';
			}
		}

		/**
		 * Filter the cart item pickup location field HTML.
		 *
		 * @since 2.0.0
		 *
		 * @param string $field_html HTML
		 * @param string $cart_item_id the current cart item ID
		 * @param \WC_Product|null $product the cart item product
		 */
		return apply_filters( 'wc_local_pickup_plus_get_pickup_location_cart_item_field_html', $field_html, $cart_item_id, $product );
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
