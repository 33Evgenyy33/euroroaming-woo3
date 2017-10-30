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
 * Admin handler of local pickup data in WooCommerce Orders.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Orders_Admin {


	/**
	 * Add actions/filters for View Orders/Edit Order screen.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// add a 'Pickup Locations' column to the orders edit screen
		add_filter( 'manage_edit-shop_order_columns',        array( $this, 'add_pickup_locations_column_header' ), 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_pickup_locations_column_content' ) );
		add_action( 'admin_head',                            array( $this, 'pickup_locations_column_styles' ) );

		// add a Pickup Location field for each shipping item to edit the Pickup Location ID
		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'output_order_shipping_item_pickup_data_field' ), 1, 2 );

		// filter orders by pickup locations.
		add_action( 'restrict_manage_posts', array( $this, 'add_pickup_locations_filter' ), 20, 1 );
		add_filter( 'request',               array( $this, 'filter_orders_by_locations' ), 20, 1 );
	}


	/**
	 * Adds 'Pickup Locations' column header to 'Orders' page.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $columns
	 * @return array
	 */
	public function add_pickup_locations_column_header( $columns ) {

		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'shipping_address' === $column_name ) {

				$new_columns['pickup_locations'] = __( 'Pickup Locations', 'woocommerce-shipping-local-pickup-plus' );
			}
		}

		return $new_columns;
	}


	/**
	 * Adds 'Pickup Locations' column content to 'Orders' page.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $column name of column being displayed
	 */
	public function add_pickup_locations_column_content( $column ) {
		global $post;

		if ( 'pickup_locations' === $column ) {

			$order                 = wc_get_order( $post->ID );
			$orders_handler        = wc_local_pickup_plus()->get_orders_instance();
			$pickup_locations      = $orders_handler ? $orders_handler->get_order_pickup_locations( $order ) : array();
			$pickup_location_names = array();
			$lost_locations        = 0;

			if ( ! empty( $pickup_locations ) ) {

				foreach ( $pickup_locations as $pickup_location ) {

					if ( $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ) {
						$pickup_location_names[ $pickup_location->get_id() ] = '<a href="' . esc_url( get_edit_post_link( $pickup_location->get_id() ) ) . '">' . $pickup_location->get_name() . '</a>';
 					} else {
						$lost_locations++;
					}
				}

				// Suppose a Order was linked to a location, but the location has been since deleted.
				// We still have static meta in the order shipping items, but no location object.
				// We can generically mention here that the order has "pickup locations" in it.
				if ( ! empty( $lost_locations ) ) {

					if ( ! empty( $pickup_location_names ) ) {
						$locations_not_found = sprintf( _n( '%d more location', '%d more locations', $lost_locations ), $lost_locations );
					} else {
						$locations_not_found = sprintf( _n( '%d location', '%d locations', $lost_locations ), $lost_locations );
					}

					$pickup_location_names[] = $locations_not_found . ' ' . '<a href="' . esc_url( get_edit_post_link( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) ) ) . '">' . __( '(see order)', 'woocommerce-shipping-local-pickup-plus' ) . '</a>';
				}

				$output = implode( '<br />', $pickup_location_names );

			} else {

				// this order should have no items for pickup
				$output = '&ndash;';
			}

			echo $output;
		}
	}


	/**
	 * Adds CSS to style the 'Pickup Locations' column.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function pickup_locations_column_styles() {

		$screen = get_current_screen();

		if ( 'edit-shop_order' === $screen->id ) :

			?>
			<style type="text/css">
				.widefat .column-pickup_locations {
					width: 11%;
				}
			</style>
			<?php

		endif;
	}


	/**
	 * Show an input to filter orders by pickup location.
	 *
	 * @see \WC_Local_Pickup_Plus_Orders_Admin::filter_orders_by_locations()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $screen the screen ID (equivalent to $typenow global)
	 */
	public function add_pickup_locations_filter( $screen ) {

		if ( 'shop_order' === $screen ) {

			$pickup_location_id   = ! empty( $_GET['_pickup_location'] ) ? absint( $_GET['_pickup_location'] ) : '';
			$pickup_location      = $pickup_location_id > 0 ? wc_local_pickup_plus_get_pickup_location( $pickup_location_id ) : null;
			$pickup_location_name = $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ? esc_html__( $pickup_location->get_name() ) : '';
			$filter_input_args    = array(
				'id'                => 'wc-local-pickup-plus-pickup-location-search',
				'input_name'        => '_pickup_location',
				'class'             => 'wc-local-pickup-plus-pickup-location-search',
				'css'               => 'display:block;float:left;width:100%;max-width:216px;margin-right: 6px;',
				'value'             => $pickup_location,
				'custom_attributes' => array(
					'data-allow_clear' => true,
					'data-placeholder' => __( 'Search for a location&hellip;', 'woocommerce-shipping-local-pickup-plus' ),
				),
			);

			if ( SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() ) {
				$filter_input_args['custom_attributes']['data-selected'] = htmlspecialchars( $pickup_location_name );
			}

			wc_local_pickup_plus()->get_admin_instance()->output_search_pickup_locations_field( $filter_input_args );
		}
	}


	/**
	 * Filter orders query by locations.
	 *
	 * @see \WC_Local_Pickup_Plus_Orders_Admin::add_pickup_locations_filter()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_vars query variables
	 * @return array
	 */
	public function filter_orders_by_locations( $query_vars ) {
		global $typenow;

		if (    'shop_order' === $typenow
		     && isset( $_GET['_pickup_location'] )
		     && $_GET['_pickup_location'] > 0
		     && ( $orders_handler = wc_local_pickup_plus()->get_orders_instance() ) ) {

			$order_ids = $orders_handler->get_pickup_location_order_ids( $_GET['_pickup_location'] );

			// if no orders are found, show no orders then
			$query_vars['post__in'] = ! empty( $order_ids ) ? $order_ids : array( 0 );
		}

		return $query_vars;
	}


	/**
	 * Get a special composite field for handling order shipping item pickup data.
	 *
	 * @since 2.0.0
	 *
	 * @param int $item_id order shipping item ID
	 * @param array $item order shipping item array
	 */
	public function output_order_shipping_item_pickup_data_field( $item_id, $item ) {
		global $post;

		$order           = wc_get_order( $post );
		$shipping_method = isset( $item['method_id'] ) ? $item['method_id'] : null;

		if (      $order instanceof WC_Order
		     && ! $order instanceof WC_Subscription
		     &&   ( wc_local_pickup_plus_shipping_method_id() === $shipping_method ) ) :

			$local_pickup_plus   = wc_local_pickup_plus();
			$items_to_choose     = $order->get_items();
			$items_to_pickup     = ! empty( $item['pickup_items'] ) ? array_map( 'absint', maybe_unserialize( $item['pickup_items'] ) ) : array();
			$pickup_location     = isset( $item['pickup_location_id'] ) ? wc_local_pickup_plus_get_pickup_location( $item['pickup_location_id'] ) : null;
			$pickup_date         = isset( $item['pickup_date'] ) ? strtotime( $item['pickup_date'] ) : null;

			$pickup_locations_field = $local_pickup_plus->get_admin_instance()->get_search_pickup_locations_field( array(
				'id'                => 'wc-local-pickup-plus-pickup-location-search-for-item-' . $item_id,
				'input_name'        => '_pickup_location[' . $item_id .']',
				'class'             => 'wc-local-pickup-plus-pickup-location-search',
				'css'               => 'display:block;float:left;width:100%;max-width:376px;margin-right: 6px;',
				'value'             => $pickup_location,
				'custom_attributes' => array(
					'data-item-id'     => $item_id,
					'data-allow_clear' => true,
					'data-placeholder' => __( 'Search for a location&hellip;', 'woocommerce-shipping-local-pickup-plus' ),
					'data-selected'    => $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ? htmlspecialchars( $pickup_location->get_name() ) : '',
				),
			) );

			?>
			<div
				id="wc-local-pickup-plus-order-shipping-item-pickup-data-<?php echo $item_id; ?>"
				class="wc-local-pickup-plus wc-local-pickup-plus-order-shipping-item-pickup-data view">
				<table
					class="display_meta">

					<tbody>

						<tr>
							<th><label for="<?php echo 'wc-local-pickup-plus-pickup-location-search-for-item-' . $item_id; ?>"><?php esc_html_e( 'Pickup Location:', 'woocommerce-shipping-local-pickup-plus' ); ?></label></th>
							<td class="pickup-location">
								<div class="value">
									<?php if ( $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ) : ?>
										<?php echo esc_html( $pickup_location->get_name() ); ?><br />
										<?php echo esc_html( $pickup_location->get_address()->get_formatted_html( true ) ); ?><br />
										<?php echo $pickup_location->has_phone() ? esc_html( $pickup_location->get_phone() ) : ''; ?>
									<?php else : ?>
										&mdash;
									<?php endif; ?>
								</div>
								<div class="field" style="display:none;">
									<?php echo $pickup_locations_field; ?>
								</div>
							</td>
						</tr>

						<?php if ( 'disabled' !== $local_pickup_plus->get_shipping_method_instance()->pickup_appointments_mode() ) : ?>

							<tr>
								<th><label for="<?php echo 'wc-local-pickup-plus-pickup-date-for-item-' . $item_id; ?>"><?php esc_html_e( 'Pickup Date:', 'woocommerce-shipping-local-pickup-plus' ); ?></label></th>
								<td class="pickup-date">
									<div class="value">
										<?php echo $pickup_date ? date_i18n( wc_date_format(), $pickup_date ) : '&mdash;'; ?>
									</div>
									<div class="field" style="display:none;">
										<input
											name="_pickup_date[<?php echo $item_id; ?>]"
											id="<?php echo 'wc-local-pickup-plus-pickup-date-for-item-' . $item_id; ?>"
											class="pickup-date"
											type="text"
											value="<?php echo $pickup_date ? date( 'Y-m-d', $pickup_date ) : ''; ?>"
											readonly="readonly"
										/>
									</div>
								</td>
							</tr>

						<?php endif; ?>

						<tr>
							<th><label for="<?php echo 'wc-local-pickup-plus-pickup-items-for-item-' . $item_id; ?>"><?php esc_html_e( 'Items to Pickup:', 'woocommerce-shipping-local-pickup-plus' ); ?></label></th>
							<td class="pickup-items">
								<div class="value">
									<?php $items = array(); ?>
									<?php foreach ( $items_to_choose as $id => $item_data ) : ?>
										<?php if ( isset( $item_data['name'], $item_data['qty'] ) && in_array( $id, $items_to_pickup, false ) ) : ?>
											<?php $items[] = is_rtl() ? '&times; ' . $item_data['qty'] . ' ' . $item_data['name'] : $item_data['name'] . ' &times; ' . $item_data['qty']; ?>
										<?php endif; ?>
									<?php endforeach; ?>
									<?php echo ! empty( $items ) ? implode( ', ', $items ) : '&mdash;'; ?>
								</div>
								<div class="field" style="display:none;">
									<select
										name="_pickup_items[<?php echo $item_id; ?>]"
										id="<?php echo 'wc-local-pickup-plus-pickup-items-for-item-' . $item_id; ?>"
										class="wc-enhanced-select"
										style="width: 100%;"
										multiple="multiple">
										<option value=""></option>
										<?php foreach ( $items_to_choose as $id => $item_data ) : ?>
											<?php $name = isset( $item_data['name'] ) ? $item_data['name'] : null; ?>
											<?php $qty  = isset( $item_data['qty'] )  ? $item_data['qty']  : null; ?>
											<?php if ( $name && $qty ) : ?>
												<?php $label = is_rtl() ? '&times; ' . $qty . ' ' . $name : $name . ' &times; ' . $qty; ?>
												<option value="<?php echo $id; ?>" <?php selected( true, in_array( $id, $items_to_pickup, false ) ); ?>><?php esc_html_e( $label ); ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
								</div>
							</td>
						</tr>

					</tbody>

					<?php if ( $order->is_editable() ) : ?>

						<tfoot>
							<tr>
								<td><button class="button edit-pickup-data"><?php esc_html_e( 'Edit', 'woocommerce-shipping-local-pickup-plus' ); ?></button></td>
								<td><button class="button-primary update-pickup-data" style="display:none;"><?php esc_html_e( 'Update Pickup Information', 'woocommerce-shipping-local-pickup-plus' ); ?></button></td>
							</tr>
						</tfoot>

					<?php endif; ?>

				</table>
			</div>
			<?php

			$select2selector = SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? 'select' : 'input';

			wc_enqueue_js( '

				jQuery( document ).ready( function( $ ) {

					var $field     = $( "#wc-local-pickup-plus-order-shipping-item-pickup-data-' . $item_id . '" ),
						$row       = $field.parent( "td" ).parent( "tr" ),
						$pencil    = $row.find( ".edit-order-item" ),
						$locationV = $field.find( "td.pickup-location .value" ),
						$locationF = $field.find( "td.pickup-location .field" ),
						$dateV     = $field.find( "td.pickup-date .value" ),
						$dateF     = $field.find( "td.pickup-date .field" ),
						$itemsV    = $field.find( "td.pickup-items .value" ),
						$itemsF    = $field.find( "td.pickup-items .field" ),
						$editBtn   = $field.find( "button.edit-pickup-data" ),
						$updateBtn = $field.find( "button.update-pickup-data" );

					$editBtn.on( "click", function( e ) {
						e.preventDefault();

						$locationF.show();
						$locationV.hide();
						$dateF.show();
						$dateV.hide();
						$itemsF.show();
						$itemsV.hide();
						$( this ).hide();
						$updateBtn.show();
					} );

					$updateBtn.on( "click", function( e ) {
						e.preventDefault();

						var data = {
							"action" :          "wc_local_pickup_plus_update_order_shipping_item_pickup_data",
							"item_id" :         ' . (int) $item_id . ',
							"pickup_location" : $locationF.find( "' . $select2selector . '#wc-local-pickup-plus-pickup-location-search-for-item-' . $item_id . '" ).val(),
							"pickup_date" :     $dateF.find( "input" ).val(),
							"pickup_items" :    $itemsF.find( "select" ).val(),
							"security" :        wc_local_pickup_plus_admin.update_order_pickup_data_nonce
						};

						$.post( wc_local_pickup_plus_admin.ajax_url, data, function( response ) {
							if ( response && response.success ) {
								location.reload();
							} else {
								console.log( response ); 
							}
						} );
					} );

					$pencil.hide();

				} );
			' );

		endif;
	}


}
