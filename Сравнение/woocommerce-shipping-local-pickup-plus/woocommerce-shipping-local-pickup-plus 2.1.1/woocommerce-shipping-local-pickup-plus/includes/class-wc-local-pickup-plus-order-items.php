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
 * Handler of pickup location data stored in order items.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Order_Items {


	/** @var string meta holding the pickup location ID */
	private $pickup_location_id_meta = '';

	/** @var string meta holding the fallback pickup location name */
	private $pickup_location_name_meta = '';

	/** @var string meta holing the fallback pickup location address */
	private $pickup_location_address_meta = '';

	/** @var string meta holding the fallback pickup location phone */
	private $pickup_location_phone_meta = '';

	/** @var string meta holding the chosen pickup date */
	private $pickup_date_meta = '';

	/** @var string meta holding an array of IDs of corresponding items to pickup */
	private $pickup_items_meta = '';

	/** @var string meta holding temporarily a package ID matching an order line item to be linked to a shipping item */
	private $pickup_package_key_meta = '';


	/**
	 * Order items handler.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// set meta key names
		foreach ( $this->get_order_items_meta_keys() as $prop => $meta_key_name ) {
			$this->$prop = $meta_key_name;
		}

		// add pickup data to the order shipping items upon new order submission
		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			// step one: record the package key on each order line item that needs to be picked up
			add_action( 'woocommerce_checkout_create_order_line_item',     array( $this, 'link_order_line_item_to_package' ), 10, 2 );
			// step two: record the pickup location ID, the pickup date and the originating package key on the shipping order item marked for local pickup
			add_action( 'woocommerce_checkout_create_order_shipping_item', array( $this, 'set_order_shipping_item_pickup_data' ), 11, 2 );
			// step three: after WC objects IDs have been set, scan order line items that need to be picked up and cross link them with shipping items
			add_action( 'woocommerce_checkout_update_order_meta',          array( $this, 'link_order_line_items_to_order_shipping_items' ) );
		} else {
			// before WC 3.0 we have the item IDs immediately available in different hooks, so we only need two steps
			add_action( 'woocommerce_add_order_item_meta',                 array( $this, 'link_legacy_order_line_item_to_package' ), 10, 3 );
			add_action( 'woocommerce_add_shipping_order_item',             array( $this, 'set_order_legacy_shipping_item_pickup_data' ), 11, 3 );
		}

		// mark pickup shipping order item meta as hidden so it is no longer directly editable/viewable
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'order_items_hidden_pickup_meta_keys' ) );
	}


	/**
	 * Get the meta keys used in order items to store pickup data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_order_items_meta_keys() {
		return array(
			'pickup_location_id_meta'      => '_pickup_location_id',
			'pickup_location_name_meta'    => '_pickup_location_name',
			'pickup_location_address_meta' => '_pickup_location_address',
			'pickup_location_phone_meta'   => '_pickup_location_phone',
			'pickup_date_meta'             => '_pickup_date',
			'pickup_items_meta'            => '_pickup_items',
			'pickup_package_key_meta'      => '_pickup_package_key',
		);
	}


	/**
	 * Hide some order shipping item keys marked as private.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $item_meta_keys array of item meta keys to be hidden
	 * @return string[]
	 */
	public function order_items_hidden_pickup_meta_keys( $item_meta_keys ) {
		return array_merge( $item_meta_keys, array_values( $this->get_order_items_meta_keys() ) );
	}


	/**
	 * Get the chosen pickup location ID for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item an order item ID
	 * @return int|null
	 */
	public function get_order_item_pickup_location_id( $order_item ) {

		$order_item_id      = $order_item instanceof WC_Order_Item_Shipping ? $order_item->get_id() : $order_item;
		$pickup_location_id = is_numeric( $order_item_id ) ? wc_get_order_item_meta( (int) $order_item_id, $this->pickup_location_id_meta ) : null;

		return is_numeric( $pickup_location_id ) ? (int) $pickup_location_id : null;
	}


	/**
	 * Get the chosen pickup location for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the shipping item
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location
	 */
	public function get_order_item_pickup_location( $order_item ) {

		$pickup_location_id = $this->get_order_item_pickup_location_id( $order_item );
		$pickup_location    = is_numeric( $pickup_location_id ) ? wc_local_pickup_plus_get_pickup_location( (int) $pickup_location_id ) : null;

		return $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ? $pickup_location : null;
	}


	/**
	 * Get the chosen pickup location name for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the shipping item
	 * @return string
	 */
	public function get_order_item_pickup_location_name( $order_item ) {

		$pickup_location = $this->get_order_item_pickup_location( $order_item );

		if ( $pickup_location ) {
			$name = $pickup_location->get_name();
		} else {
			$name = wc_get_order_item_meta( $order_item instanceof WC_Order_Item_Shipping ? $order_item->get_id() : (int) $order_item, $this->pickup_location_name_meta );
		}

		return is_string( $name ) ? $name : '';
	}


	/**
	 * Get the chosen pickup location name for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the shipping item
	 * @param bool $one_line whether to output a single address or an HTML formatted address (default)
	 * @return string
	 */
	public function get_order_item_pickup_location_address( $order_item, $one_line = false ) {

		$pickup_location = $this->get_order_item_pickup_location( $order_item );

		if ( $pickup_location ) {

			$address = $pickup_location->get_address()->get_formatted_html( $one_line );

		} else {

			$address = wc_get_order_item_meta( $order_item instanceof WC_Order_Item_Shipping ? $order_item->get_id() : (int) $order_item, $this->pickup_location_address_meta );

			if ( ! empty( $address ) && is_array( $address ) ) {
				$address = new WC_Local_Pickup_Plus_Address( $address );
				$address = $address->get_formatted_html( $one_line );
			} else {
				$address = '';
			}

		}

		return is_string( $address ) ? $address : '';
	}


	/**
	 * Get the chosen pickup location phone for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the shipping item
	 * @param bool $html whether to output an HTML formatted string (default) or a plaintext one
	 * @return string
	 */
	public function get_order_item_pickup_location_phone( $order_item, $html = true ) {

		$pickup_location = $this->get_order_item_pickup_location( $order_item );

		if ( $pickup_location ) {

			$phone = $pickup_location->get_phone( $html );

		} else {

			$phone = wc_get_order_item_meta( $order_item instanceof WC_Order_Item_Shipping ? $order_item->get_id() : (int) $order_item, $this->pickup_location_phone_meta );

			if ( $html === true && ! empty( $phone ) ) {
				$phone = '<a href="tel:' . esc_attr( $phone ) . '">' . $phone . '</a>';
			}
		}

		return is_string( $phone ) ? $phone : '';
	}


	/**
	 * Set pickup location data for a pickup order item.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location or null to wipe the information
	 * @return bool whether the order item meta was successfully set
	 */
	public function set_order_item_pickup_location( $order_item, $pickup_location = null ) {

		$success = $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location || null === $pickup_location;

		if ( $order_item instanceof WC_Order_Item_Shipping ) {
			$order_item->add_meta_data( $this->pickup_location_id_meta, null === $pickup_location ? '' : $pickup_location->get_id(), true );
		} else {
			$success = $success && is_numeric( $order_item ) && wc_update_order_item_meta( (int) $order_item, $this->pickup_location_id_meta, null === $pickup_location ? '' : $pickup_location->get_id() );
		}

		$this->set_order_item_pickup_location_name( $order_item, $success ? $pickup_location->get_name() : '' );
		$this->set_order_item_pickup_location_address( $order_item, $success ? $pickup_location->get_address()->get_array() : array() );
		$this->set_order_item_pickup_location_phone( $order_item, $success && $pickup_location->has_phone() ? $pickup_location->get_phone() : '' );

		return $success;
	}


	/**
	 * Set the pickup location name as a fallback in case the pickup location object is deleted in future.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param string $name the pickup location name
	 * @return bool
	 */
	private function set_order_item_pickup_location_name( $order_item, $name = '' ) {

		$success = is_string( $name );

		if ( $order_item instanceof WC_Order_Item_Shipping ) {
			$order_item->add_meta_data( $this->pickup_location_name_meta, $success ? $name : '', true );
		} else {
			$success = $success && wc_update_order_item_meta( (int) $order_item, $this->pickup_location_name_meta, $success ? $name : '' );
		}

		return $success;
	}


	/**
	 * Set the pickup location address as a fallback in case the pickup location object is deleted in future.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param array $address a Local Pickup Plus formatted address array
	 * @return bool
	 */
	private function set_order_item_pickup_location_address( $order_item, array $address ) {

		$success = ! empty( $address );

		if ( $order_item instanceof WC_Order_Item_Shipping ) {
			$order_item->add_meta_data( $this->pickup_location_address_meta, is_array( $address ) ? $address : array(), true );
		} else {
			$success = $success && wc_update_order_item_meta( (int) $order_item, $this->pickup_location_address_meta, $address );
		}

		return $success;
	}


	/**
	 * Set the pickup location phone as a fallback in case the pickup location object is deleted in future.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param string $phone the pickup location phone as a string
	 * @return bool
	 */
	private function set_order_item_pickup_location_phone( $order_item, $phone = '' ) {

		$success = is_string( $phone );

		if ( $order_item instanceof WC_Order_Item_Shipping ) {
			$order_item->add_meta_data( $this->pickup_location_phone_meta, $success ? $phone : '', true );
		} else {
			$success = $success && wc_update_order_item_meta( (int) $order_item, $this->pickup_location_phone_meta, $success ? $phone : '' );
		}

		return $success;
	}


	/**
	 * Get the items to pickup for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item an order item ID
	 * @return int[] array of order line items IDs meant for pickup
	 */
	public function get_order_item_pickup_items( $order_item ) {

		$order_item_id = $order_item instanceof WC_Order_Item ? $order_item->get_id() : $order_item;
		$pickup_items  = is_numeric( $order_item_id ) ? wc_get_order_item_meta( (int) $order_item_id, $this->pickup_items_meta ) : array();

		return $pickup_items ? (array) $pickup_items : array();
	}


	/**
	 * Set items meant for pickup for a pickup order item.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param array $pickup_items array of pickup items
	 * @return bool whether the order item meta was successfully set
	 */
	public function set_order_item_pickup_items( $order_item, array $pickup_items ) {

		$pickup_items = ! empty( $pickup_items ) ? array_filter( array_map( 'absint', $pickup_items ) ) : array();

		if ( $order_item instanceof WC_Order_Item_Shipping ) {

			$order_item->add_meta_data( $this->pickup_items_meta, $pickup_items, true );

			$success = true;

		} else {

			$success = is_numeric( $order_item ) && wc_update_order_item_meta( (int) $order_item, $this->pickup_items_meta, $pickup_items );
		}

		return $success;
	}


	/**
	 * Get the scheduled pickup date for an order shipping item.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item an order item ID or object
	 * @param string $format the date format as a string (default 'Y-m-d') or timestamp ('timestamp')
	 * @return string|int
	 */
	public function get_order_item_pickup_date( $order_item, $format = 'Y-m-d' ) {

		$order_item_id    = $order_item instanceof WC_Order_Item_Shipping ? $order_item->get_id() : $order_item;
		$pickup_date      = is_numeric( $order_item_id ) ? wc_get_order_item_meta( (int) $order_item_id, $this->pickup_date_meta ) : '';
		$pickup_timestamp = is_string( $pickup_date ) ? (int) strtotime( $pickup_date ) : 0;

		if ( $pickup_timestamp > 0 ) {
			if ( 'timestamp' === $format ) {
				$pickup_date = $pickup_timestamp;
			} elseif ( 'Y-m-d' !== $format ) {
				$pickup_date = date( $format, $pickup_timestamp );
			}
		} else {
			$pickup_date = 'timestamp' === $format ? 0 : '';
		}

		return $pickup_date;
	}


	/**
	 * Set pickup date for a pickup order item.
	 *
	 * Important note: you should provide an order item ID if the order item already exists otherwise the order item object for newly created items.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Order_Item_Shipping $order_item the order item to save the meta for
	 * @param int|string $date a date as timestamp or in YYYY-MM-DD format
	 * @return bool whether the order item meta was successfully set
	 */
	public function set_order_item_pickup_date( $order_item, $date ) {

		if ( is_string( $date ) && '' !== $date ) {
			$pickup_timestamp = strtotime( $date );
		} elseif ( is_numeric( $date ) && $date > 0 ) {
			$pickup_timestamp = (int) $date;
		}

		$pickup_date = ! empty( $pickup_timestamp ) ? date( 'Y-m-d', $pickup_timestamp ) : '';

		if ( $order_item instanceof WC_Order_Item_Shipping ) {

			$order_item->add_meta_data( $this->pickup_date_meta, $pickup_date, true );

			$success = true;

		} else {

			$success = is_numeric( $order_item ) && wc_update_order_item_meta( (int) $order_item, $this->pickup_date_meta, $pickup_date );
		}

		return $success;
	}


	/**
	 * Associate an order line item to a package.
	 *
	 * This method sets an order item meta for a line item which holds the package key where the item came from.
	 * In this way we can associate shipping items to order line items and tell which line items are meant for pickup.
	 * @see WC_Local_Pickup_Plus_Order_Items::link_order_line_items_to_order_shipping_items()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order_Item $order_item the new order line item
	 * @param string $cart_item_key the cart item key where the order line item was generated from
	 */
	public function link_order_line_item_to_package( $order_item, $cart_item_key ) {

		if ( ! empty( $_POST['shipping_method_pickup_items'] ) && is_array( $_POST['shipping_method_pickup_items'] ) ) {

			$cart_item_keys = array();

			foreach ( $_POST['shipping_method_pickup_items'] as $package_key => $item_keys ) {

				// we always ensure this is an array
				$item_keys = explode( ',', $item_keys );

				foreach ( $item_keys as $item_key ) {
					// prefixing the package key with a string is a conservative workaround to prevent index oddities with index key 0 and data type handling in PHP (so we are sure these are strings now)
					$cart_item_keys[ trim( $item_key ) ] = "package_{$package_key}";
				}
			}

			if ( isset( $cart_item_keys[ $cart_item_key ] ) ) {
				// this sets the meta value as "package_{$package_key}"
				$order_item->update_meta_data( $this->pickup_package_key_meta, $cart_item_keys[ $cart_item_key ] );
			}
		}
	}


	/**
	 * Set pickup data for the order shipping items upon new order creation.
	 *
	 * We set the pickup location ID and the pickup date (if available).
	 * For the pickup items we need two more callbacks handling:
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_order_line_item_to_package()
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_order_line_items_to_order_shipping_items()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item_Shipping $shipping_item the shipping item ID
	 * @param string|int $package_key the package key from the package array
	 */
	public function set_order_shipping_item_pickup_data( $shipping_item, $package_key ) {

		if ( isset( $_POST, $_POST['shipping_method_pickup_location_id'][ $package_key ] ) ) {

			$pickup_location = wc_local_pickup_plus_get_pickup_location( $_POST['shipping_method_pickup_location_id'][ $package_key ] );

			if ( $pickup_location && $pickup_location->get_id() > 0 && $this->set_order_item_pickup_location( $shipping_item, $pickup_location ) ) {

				// prefixing the package key with a string is a conservative workaround to prevent index oddities with index key 0 and data type handling in PHP (so we are sure these are strings now)
				$shipping_item->update_meta_data( $this->pickup_package_key_meta, "package_{$package_key}" );

				$pickup_date = isset( $_POST['shipping_method_pickup_date'][ $package_key ] ) ? $_POST['shipping_method_pickup_date'][ $package_key ] : '';

				if ( 'disabled' !== wc_local_pickup_plus_appointments_mode() ) {
					$this->set_order_item_pickup_date( $shipping_item, $pickup_date );
				}
			}
		}
	}


	/**
	 * Link order line items to order shipping items.
	 *
	 * This runs once an order is created in the post type data store in WooCommerce 3.0 and we have order item IDs set.
	 * Since other hooks do not have yet an order item ID set, it's only later that we can connect order line items to shipping items, by cross referencing the original package key passed on line items.
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_order_line_item_to_package()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id WC_Order ID
	 */
	public function link_order_line_items_to_order_shipping_items( $order_id ) {

		$order = wc_get_order( $order_id );

		// run only if a new order with Local Pickup Plus among its shipping methods
		if ( $order instanceof WC_Order && $order->has_shipping_method( wc_local_pickup_plus_shipping_method_id() ) ) {

			$order_line_items_in_package       = array();
			$order_line_items_in_shipping_item = array();

			/* @type \WC_Order_Item[] $order_line_items */
			$order_line_items     = $order->get_items( 'line_item' );
			/* @type \WC_Order_Item_Shipping[] $order_shipping_items */
			$order_shipping_items = $order->get_items( 'shipping' );

			// loop order line items and gather the package the item came from
			foreach ( $order_line_items as $order_line_item ) {

				$order_line_item_package_key = wc_get_order_item_meta( $order_line_item->get_id(), $this->pickup_package_key_meta );

				if ( ! empty( $order_line_item_package_key ) ) {

					$order_line_items_in_package[ $order_line_item_package_key ][] = $order_line_item->get_id();

					// the transitory item meta can be safely deleted by this point
					wc_delete_order_item_meta( $order_line_item->get_id(), $this->pickup_package_key_meta );
				}
			}

			// cleanup: we use again this variable below for semantic reasons
			unset( $order_line_item_package_key );

			// loop the order shipping item and compare the package key
			foreach ( $order_line_items_in_package as $order_line_item_package_key => $order_line_item_ids ) {

				foreach ( $order_shipping_items as $order_shipping_item ) {

					$order_shipping_item_package_key = wc_get_order_item_meta( $order_shipping_item->get_id(), $this->pickup_package_key_meta );

					// check if the order line item package key and the order shipping item package key match
					if ( (string) $order_line_item_package_key === (string) $order_shipping_item_package_key && ! in_array( $order_shipping_item_package_key, array( '', false, null ), true ) ) {

						$order_line_items_in_shipping_item[] = array( $order_shipping_item->get_id() => $order_line_item_ids );

						// the transitory order item meta can be safely deleted by this point
						wc_delete_order_item_meta( $order_shipping_item->get_id(), $this->pickup_package_key_meta );
					}
				}
			}

			// finally, set a meta on the order shipping items to link the order line items meant for pickup
			if ( ! empty( $order_line_items_in_shipping_item ) ) {

				foreach ( $order_line_items_in_shipping_item as $order_shipping_items ) {

					foreach ( $order_shipping_items as $shipping_item_id => $order_line_items_ids ) {

						$this->set_order_item_pickup_items( $shipping_item_id, (array) $order_line_items_ids );
					}
				}
			}
		}
	}


	/**
	 * Link an order item to an order shipping item by package index.
	 *
	 * This callback method is used only WooCommerce versions v3.0.0 and older.
	 *
	 * This is useful to determine which items are meant for pickup upon new order:
	 * @see \WC_Local_Pickup_Plus_Order_Items::set_order_legacy_shipping_item_pickup_data()
	 *
	 * @internal
	 * @deprecated since 2.0.0
	 *
	 * TODO remove this method when support for WooCommerce 2.6 is dropped {FN 2017-04-26}
	 *
	 * @since 2.0.0
	 *
	 * @param int $item_id the newly assigned order item ID
	 * @param array $cart_item cart item data as an array
	 * @param string $cart_item_key cart item key
	 */
	public function link_legacy_order_line_item_to_package( $item_id, $cart_item, $cart_item_key ) {

		if ( isset( $_POST, $_POST['shipping_method_pickup_items'] ) && is_array( $_POST['shipping_method_pickup_items'] ) ) {

			$package_pickup_items = $_POST['shipping_method_pickup_items'];
			$cart_item_keys       = array();

			foreach ( $package_pickup_items as $package_key => $item_keys ) {

				// we always ensure this is an array
				$item_keys = explode( ',', $item_keys );

				foreach ( $item_keys as $item_key ) {
					// prefixing the package key with a string is a workaround for index oddities with index key 0
					$cart_item_keys[ (string) $item_key ] = 'package_' . $package_key;
				}
			}

			if ( isset( $cart_item_keys[ (string) $cart_item_key ] ) ) {

				// this sets the meta as "package_{$package_key}"
				wc_update_order_item_meta( $item_id, $this->pickup_package_key_meta, $cart_item_keys[ (string) $cart_item_key ] );
			}
		}
	}


	/**
	 * Set pickup data on the order shipping items designated for pickup.
	 *
	 * This callback is intended only for WooCommerce versions older than v3.0.0.
	 * @see \WC_Local_Pickup_Plus_Order_Items::link_legacy_order_line_item_to_package()
	 *
	 * @internal
	 * @deprecated since 2.0.0
	 *
	 * TODO remove this method when support for WooCommerce 2.6 is dropped {FN 2017-04-26}
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id the order ID
	 * @param int $shipping_item_id shipping Order item ID
	 * @param int $package_key the package index for this shipping order item
	 */
	public function set_order_legacy_shipping_item_pickup_data( $order_id, $shipping_item_id, $package_key ) {

		// we need at least a chosen pickup location to set the minimal data:
		if ( isset( $_POST, $_POST['shipping_method_pickup_location_id'][ $package_key ] ) ) {

			$order            = $order_id instanceof WC_Order ? $order_id : wc_get_order( $order_id );
			$shipping_methods = $order instanceof WC_Order ? $order->get_shipping_methods() : array();
			$pickup_location  = wc_local_pickup_plus_get_pickup_location( $_POST['shipping_method_pickup_location_id'][ $package_key ] );

			// confirm that the chosen shipping method is Local Pickup Plus
			if (    $pickup_location
			     && isset( $shipping_methods[ $shipping_item_id ]['method_id'] )
			     && wc_local_pickup_plus_shipping_method_id() === $shipping_methods[ $shipping_item_id ]['method_id'] ) {

				$pickup_date  = isset( $_POST['shipping_method_pickup_date'][ $package_key ] ) ? trim( $_POST['shipping_method_pickup_date'][ $package_key ] ) : null;
				$pickup_items = $this->get_package_legacy_order_items_for_pickup( $order, $package_key );

				// set the chosen location
				$this->set_order_item_pickup_location( $shipping_item_id, $pickup_location );
				// set the items and quantities for this package
				$this->set_order_item_pickup_items( $shipping_item_id, (array) $pickup_items );
				// in theory appointments mode was already enabled or we wouldn't have a date here...
				if ( ! empty( $pickup_date ) && 'disabled' !== wc_local_pickup_plus_appointments_mode() ) {
					// set the chosen pickup date
					$this->set_order_item_pickup_date( $shipping_item_id, $pickup_date );
				}
			}
		}
	}


	/**
	 * Parse package contents to extract order line items to associate to a shipping item.
	 *
	 * This is intended as a disposable private method (do not change to public) as the meta information returned is destroyed in the process.
	 *
	 * TODO remove this method when support for WooCommerce 2.6 is removed {FN 2017-04-26}
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order the order which the package is associated to
	 * @param int $package_key the index number of the package in submitted order
	 *
	 * @return int[] an array of order item IDs
	 */
	private function get_package_legacy_order_items_for_pickup( $order, $package_key ) {

		$order_item_ids = array();

		if ( $order instanceof WC_Order && ( $order_line_items = $order->get_items( 'line_item' ) ) ) {


			foreach ( array_keys( $order_line_items ) as $order_line_item_id ) {

				$linked_package_key = is_numeric( $order_line_item_id ) ? wc_get_order_item_meta( (int) $order_line_item_id, $this->pickup_package_key_meta ) : null;

				if ( ! empty( $linked_package_key ) && "package_{$package_key}" === $linked_package_key ) {

					$order_item_ids[] = (int) $order_line_item_id;

					// we also remove the order item meta as it is no longer needed, and it risks to be displayed or returned elsewhere
					wc_delete_order_item_meta( $order_line_item_id, $this->pickup_package_key_meta );
				}
			}
		}

		return $order_item_ids;
	}


}
