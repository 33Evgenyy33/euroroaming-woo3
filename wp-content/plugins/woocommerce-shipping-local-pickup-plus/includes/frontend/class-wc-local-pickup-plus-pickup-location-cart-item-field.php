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
class WC_Local_Pickup_Plus_Pickup_Location_Cart_Item_Field extends WC_Local_Pickup_Plus_Pickup_Location_Field {


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

		$this->object_type   = 'cart-item';
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
	protected function get_pickup_data( $piece = '' ) {
		return wc_local_pickup_plus()->get_session_instance()->get_cart_item_pickup_data( $this->get_cart_item_id(), $piece );
	}


	/**
	 * Save pickup data to session.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pickup_data
	 */
	protected function set_pickup_data( array $pickup_data ) {
		wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $this->get_cart_item_id(), $pickup_data );
	}


	/**
	 * Reset pickup data for the cart item (defaults to shipping).
	 *
	 * @since 2.0.0
	 */
	protected function delete_pickup_data() {
		wc_local_pickup_plus()->get_session_instance()->delete_cart_item_pickup_data( $this->get_cart_item_id() );
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

		if ( $local_pickup_plus->is_available() && ( $product = $this->get_product() ) ) {

			if ( wc_local_pickup_plus_product_can_be_picked_up( $product ) ) {

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
				if (      $local_pickup_plus->is_per_item_selection_enabled()
				     && ! wc_local_pickup_plus_product_can_be_picked_up( $product, $chosen_pickup_location ) ) {

					$chosen_pickup_location = null;
				}

				ob_start();

				?>
				<div
					id="pickup-location-field-for-<?php echo esc_attr( $cart_item_id ); ?>"
					class="pickup-location-field pickup-location-cart-item-field"
					data-pickup-object-id="<?php echo esc_attr( $cart_item_id ); ?>">

					<?php // only display the item location select if enabled: ?>
					<?php if ( $local_pickup_plus->is_per_item_selection_enabled() ) : ?>
						<div style="display: <?php echo $must_be_picked_up || $should_be_picked_up ? 'block' : 'none'; ?>;">
							<?php echo $this->get_location_select_html( $cart_item_id, $chosen_pickup_location, $this->get_product() ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! $must_be_picked_up  ) : ?>

						<?php if ( ! $this->hiding_item_handling_toggle() ) : ?>

							<?php

							/**
							 * Filters the product handling links and their labels.
							 *
							 * @since 2.2.0
							 *
							 * @param array $item_handling_labels associative array of keys and HTML labels containing links
							 * @param string $enable_pickup_class CSS class expected to be in a link to set item for pickup
							 * @param string $disable_pickup_class CSS class expected to be in a link to set item for shipping
							 */
							$item_handling_labels = (array) apply_filters( 'wc_local_pickup_plus_item_handling_toggle_labels', array(
								/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
								'set_for_pickup'   => sprintf( esc_html__( 'This item is set for shipping. %1$sClick here to pickup this item%2$s.', 'woocommerce-shipping-local-pickup-plus' ), '<a class="enable-local-pickup"  href="#">', '</a>' ),
								/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
								'set_for_shipping' => sprintf( esc_html__( 'This item is set for pickup. %1$sClick here to ship this item%2$s.',     'woocommerce-shipping-local-pickup-plus' ), '<a class="disable-local-pickup" href="#">', '</a>' ),
							), 'enable-local-pickup', 'disable-local-pickup' );

							?>

							<?php if ( isset( $item_handling_labels['set_for_pickup'], $item_handling_labels['set_for_shipping'] ) ) : ?>

								<small style="display: <?php echo   $should_be_picked_up ? 'none' : 'block'; ?>;"><?php echo $item_handling_labels['set_for_pickup']; ?></small>
								<small style="display: <?php echo ! $should_be_picked_up ? 'none' : 'block'; ?>;"><?php echo $item_handling_labels['set_for_shipping']; ?></small>

							<?php endif; ?>

						<?php else : ?>

							<?php // if the customer control toggle to switch between ship and pickup is disabled, force handling into session
							wc_local_pickup_plus()->get_session_instance()->set_cart_item_pickup_data( $cart_item_id,  array(
								'handling'           => $should_be_picked_up                        ? 'pickup'                           : $local_pickup_plus->get_default_handling(),
								'lookup_area'        => isset( $pickup_data['lookup_area'] )        ? $pickup_data['lookup_area']        : '',
								'pickup_location_id' => isset( $pickup_data['pickup_location_id'] ) ? $pickup_data['pickup_location_id'] : 0,
							) ); ?>

						<?php endif; ?>

					<?php endif; ?>

				</div>
				<?php

				$field_html .= ob_get_clean();

			} elseif ( $product->needs_shipping() ) {

				// display a shipping handling notice only for non-virtual items
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
	 * Determines if the current product can be picked up, or must be shipped.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location pickup location to check
	 * @return bool
	 */
	protected function can_be_picked_up( $pickup_location ) {
		return $this->get_product() ? wc_local_pickup_plus_product_can_be_picked_up( $this->get_product(), $pickup_location ) : true;
	}


}
