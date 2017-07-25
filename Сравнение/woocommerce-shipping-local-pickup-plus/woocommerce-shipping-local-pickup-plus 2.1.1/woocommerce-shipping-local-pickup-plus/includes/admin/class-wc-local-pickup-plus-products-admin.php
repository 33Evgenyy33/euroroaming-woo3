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
 * WooCommerce Products and Product Categories admin handler for local pickup.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Products_Admin {


	/**
	 * Products and Product Categories admin handler constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// add Pickup Locations fields for Products and Product Categories
		add_action( 'woocommerce_product_options_shipping', array( $this, 'add_product_pickup_locations_options' ), -1 ); // The low priority here is to rule out an issue with Subscriptions.
		add_action( 'product_cat_add_form_fields',          array( $this, 'add_product_category_pickup_locations_options' ) );
		add_action( 'product_cat_edit_form_fields',         array( $this, 'edit_product_category_pickup_locations_options' ) );

		// save or update Pickup Location fields for Products and Product Categories
		add_action( 'create_term', array( $this, 'save_product_cat_local_pickup_availability' ), 10, 3 );
		add_action( 'edit_term',   array( $this, 'save_product_cat_local_pickup_availability' ), 10, 3 );

		foreach( array_keys( wc_get_product_types() ) as $product_type ) {
			add_action( "woocommerce_process_product_meta_{$product_type}", array( $this, 'save_product_local_pickup_availability' ) );
		}

		// add a product availability status information to stock availability
		add_filter( 'woocommerce_admin_stock_html', array( $this, 'add_product_local_pickup_availability_status' ), 20, 2 );
	}


	/**
	 * Adds a pickup availability status information next to stock availability column in the Products edit screen admin page.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $stock_html stock HTML information
	 * @param \WC_Product $product the product
	 * @return string
	 */
	public function add_product_local_pickup_availability_status( $stock_html, $product ) {

		$product              = $product->is_type( 'variation' ) ? SV_WC_Product_Compatibility::get_parent( $product ) : $product;
		$product_availability = wc_local_pickup_plus_get_product_availability( $product->get_id() );
		$availability_types   = wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_availability_types( true );

		if ( 'allowed' !== $product_availability && array_key_exists( $product_availability, $availability_types ) ) {
			$stock_html .= '<br><small style="opacity:.5;"><em>' . strtolower( $availability_types[ $product_availability ] ) . '</em></small>';
		}

		return $stock_html;
	}


	/**
	 * Get the local pickup availability input field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field_name the field name used in field attributes
	 * @param string $field_value the field value
	 * @param string $description the field description text, default empty
	 * @return string input field HTML
	 */
	private function get_local_pickup_availability_input_html( $field_name, $field_value = 'allowed', $description = '' ) {

		ob_start();

		?>
		<select
			id="<?php echo esc_attr( $field_name ); ?>"
			name="<?php echo esc_attr( $field_name ); ?>"
			class="select short wc_local_pickup_plus_local_pickup_availability">
			<?php foreach ( wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_availability_types( true ) as $type => $label ) : ?>
				<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $field_value, $type, true ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $description ) ) : global $post; ?>

			<?php if ( null === $post ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php else : ?>
				<?php echo wc_help_tip( $description ); ?>
			<?php endif; ?>

		<?php endif;

		return ob_get_clean();
	}


	/**
	 * Adds Pickup Locations options to a product in the Shipping tab.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_product_pickup_locations_options() {
		global $post;

		$product = wc_get_product( $post );

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		?>
		<div class="options_group wc-local-pickup-plus-product-pickup-locations">
			<p class="form-field wc-local-pickup-plus-local-pickup-product-availability_field ">
				<?php

				$name  = '_wc_local_pickup_plus_local_pickup_product_availability';
				$value = wc_local_pickup_plus_get_product_availability( $product );
				$desc  = __( 'Choose whether local pickup is available for this product, or if local pickup is the only type of shipment possible.', 'woocommerce-shipping-local-pickup-plus' );

				?>
				<label for="<?php echo esc_attr( $name ); ?>"><?php esc_html_e( 'Local Pickup', 'woocommerce-shipping-local-pickup-plus' ); ?></label>
				<?php echo $this->get_local_pickup_availability_input_html( $name, $value, $desc ); ?>
			</p>
		</div>
		<?php
	}


	/**
	 * Adds Pickup Locations options to a product categories.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_product_category_pickup_locations_options() {

		echo $this->get_local_pickup_availability_input_html(
			'_wc_local_pickup_plus_local_pickup_product_cat_availability',
			'allowed',
			__( 'Choose whether local pickup is possible for this category of products, or if local pickup is the only type of shipment possible. Individual products may override this setting.', 'woocommerce-shipping-local-pickup-plus' )
		);
	}


	/**
	 * Adds Pickup Locations options to a product categories.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function edit_product_category_pickup_locations_options() {
		global $tag;

		if ( ! $tag ) {
			return;
		}

		$name  = '_wc_local_pickup_plus_local_pickup_product_cat_availability';
		$value = wc_local_pickup_plus_get_product_cat_availability( $tag );
		$desc  = __( 'Choose whether local pickup is possible for this category of products, or if local pickup is the only type of shipment possible. Individual products may override this setting.', 'woocommerce-shipping-local-pickup-plus' );

		?>
		<tr class="form-field term-name-wrap">
			<th scope="row"><label for="<?php echo esc_attr( $name ); ?>"><?php esc_html_e( 'Local Pickup', 'woocommerce-shipping-local-pickup-plus' ); ?></label></th>
			<td><?php echo $this->get_local_pickup_availability_input_html( $name, $value, $desc ); ?></td>
		</tr>
		<?php
	}


	/**
	 * Save or update a product local pickup availability.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the product post ID
	 */
	public function save_product_local_pickup_availability( $post_id ) {

		$meta_key     = '_wc_local_pickup_plus_local_pickup_product_availability';
		$availability = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : null;

		if (    $availability
		     && ( $product = wc_get_product( $post_id ) )
		     && in_array( $availability, wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_availability_types(), true ) ) {

			SV_WC_Product_Compatibility::update_meta_data( $product, $meta_key, $availability );
		}
	}


	/**
	 * Save or update a product category local pickup availability.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $term_id the product category term ID
	 * @param int $taxonomy_id the term taxonomy ID
	 * @param string $taxonomy_slug the term taxonomy slug
	 */
	public function save_product_cat_local_pickup_availability( $term_id, $taxonomy_id, $taxonomy_slug ) {

		$meta_key     = '_wc_local_pickup_plus_local_pickup_product_cat_availability';
		$availability = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : null;

		if ( $availability && in_array( $availability, wc_local_pickup_plus()->get_products_instance()->get_local_pickup_product_availability_types(), true ) ) {

			update_term_meta( $term_id, $meta_key, $availability );
		}
	}


}
