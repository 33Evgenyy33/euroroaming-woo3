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
 * Frontend methods.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Frontend {


	/** @var \WC_Local_Pickup_Plus_Cart cart handler instance */
	private $cart;

	/** @var \WC_Local_Pickup_Plus_Checkout checkout handler instance */
	private $checkout;


	/**
	 * Frontend constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if ( $local_pickup_plus && $local_pickup_plus->is_available() ) {

			$plugin_path = wc_local_pickup_plus()->get_plugin_path();

			// load field objects
			require_once( $plugin_path . '/includes/frontend/class-wc-local-pickup-plus-pickup-location-cart-item-field.php' );
			require_once( $plugin_path . '/includes/frontend/class-wc-local-pickup-plus-pickup-location-package-field.php' );

			// load handlers
			$this->cart     = wc_local_pickup_plus()->load_class( '/includes/frontend/class-wc-local-pickup-plus-cart.php', 'WC_Local_Pickup_Plus_Cart' );
			$this->checkout = wc_local_pickup_plus()->load_class( '/includes/frontend/class-wc-local-pickup-plus-checkout.php', 'WC_Local_Pickup_Plus_Checkout' );

			// add frontend script and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		}
	}


	/**
	 * Get the cart instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Cart
	 */
	public function get_cart_instance() {
		return $this->cart;
	}


	/**
	 * Get the checkout instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Checkout
	 */
	public function get_checkout_instance() {
		return $this->checkout;
	}


	/**
	 * Load frontend script and styles.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts_styles() {

		if ( is_cart() || is_checkout() ) {

			$this->load_styles();
			$this->load_scripts();
		}
	}

	/**
	 * Load frontend styles.
	 *
	 * @since 2.0.0
	 */
	private function load_styles() {

		$dependencies = array(
			'select2',
		);

		// by default WooCommerce doesn't load Select2 library in cart page
		if ( ! wp_style_is( 'select2', 'enqueued' ) ) {

			$style_path    = str_replace( array( 'http:', 'https:' ), '', plugins_url('assets/css/select2.css', WC_PLUGIN_FILE ) );
			$style_version = SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() ? '3.5.3' : '4.0.3';

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				wp_register_style( 'select2', $style_path, array(), $style_version );
			}

			wp_enqueue_style( 'select2', $style_path, array(), $style_version );
		}

		wp_enqueue_style( 'wc-local-pickup-plus-frontend', wc_local_pickup_plus()->get_plugin_url() . '/assets/css/frontend/wc-local-pickup-plus-frontend.min.css', $dependencies, WC_Local_Pickup_Plus::VERSION );

		/**
		 * Upon enqueueing Local Pickup Plus frontend styles.
		 *
		 * @since 2.0.0
		 *
		 * @param array $styles handlers
		 * @param array $dependencies dependencies handles
		 */
		do_action( 'wc_local_pickup_plus_load_frontend_styles', array( 'wc-local-pickup-plus-frontend' ), $dependencies );
	}


	/**
	 * Load and localize frontend scripts.
	 *
	 * @since 2.0.0
	 */
	private function load_scripts() {

		$dependencies = array(
			'jquery',
			'jquery-ui-datepicker',
			'select2',
		);

		// by default WooCommerce doesn't load Select2 library in cart page
		if ( ! wp_script_is( 'select2', 'enqueued' ) ) {

			$script_extension = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			$script_path      = str_replace( array( 'http:', 'https:' ), '', plugins_url('assets/js/select2/select2.full' . $script_extension, WC_PLUGIN_FILE ) );
			$script_version   = SV_WC_Plugin_Compatibility::is_wc_version_lt_3_0() ? '3.5.3' : '4.0.3';

			if ( ! wp_style_is( 'select2', 'registered' ) ) {
				wp_register_script( 'select2', $script_path, array( 'jquery' ), $script_version, false );
			}

			wp_enqueue_script( 'select2', $script_path, array( 'jquery' ), $script_version, false );
		}

		// load scripts
		wp_enqueue_script( 'wc-local-pickup-plus-frontend', wc_local_pickup_plus()->get_plugin_url() . '/assets/js/frontend/wc-local-pickup-plus-frontend.min.js', $dependencies, WC_Local_Pickup_Plus::VERSION );

		// localize scripts
		wp_localize_script( 'wc-local-pickup-plus-frontend', 'wc_local_pickup_plus_frontend', array(

			// Add any config/state properties here, for example:
			// 'is_user_logged_in' => is_user_logged_in()
			'ajax_url'                                     => admin_url( 'admin-ajax.php' ),
			'is_cart'                                      => is_cart(),
			'is_checkout'                                  => is_checkout(),
			'select2_version'                              => SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? '4.0.3' : '3.5.3',
			'shipping_method_id'                           => wc_local_pickup_plus_shipping_method_id(),
			'use_enhanced_search'                          => wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count() > 80,
			'start_of_week'                                => get_option( 'start_of_week', 1 ),
			'month_names'                                  => $this->get_month_names(),
			'day_initials'                                 => $this->get_day_initials(),
			'pickup_appointments'                          => wc_local_pickup_plus_appointments_mode(),
			'pickup_locations_lookup_nonce'                => wp_create_nonce( 'pickup-locations-lookup' ),
			'set_cart_item_handling_nonce'                 => wp_create_nonce( 'set-cart-item-handling' ),
			'set_package_handling_nonce'                   => wp_create_nonce( 'set-package-handling' ),
			'get_pickup_location_area_nonce'               => wp_create_nonce( 'get-pickup-location-area' ),
			'get_pickup_location_name_nonce'               => wp_create_nonce( 'get-pickup-location-name' ),
			'get_pickup_location_appointment_data_nonce'   => wp_create_nonce( 'get-pickup-location-appointment-data' ),
			'get_pickup_location_opening_hours_list_nonce' => wp_create_nonce( 'get-pickup-location-opening-hours-list' ),

			'i18n' => array(

				// Add i18n strings here, for example:
				// 'local_pickup_plus' => __( 'Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' )
				'search_type_minimum_characters' => __( 'Enter a postcode or address&hellip;', 'woocommerce-shipping-local-pickup-plus' ),

			),

		) );

		/**
		 * Upon enqueueing Local Pickup Plus frontend scripts.
		 *
		 * @since 2.0.0
		 *
		 * @param array $scripts handlers
		 * @param array $dependencies dependencies handles
		 */
		do_action( 'wc_local_pickup_plus_load_frontend_scripts', array( 'wc-local-pickup-plus-frontend' ), $dependencies );
	}


	/**
	 * Get localized month names.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of names ordered by month (1-12)
	 */
	private function get_month_names() {

		$month_names = array();

		// important reminder: in JavaScript month numbers range from 0 to 11 vs 1 to 12 in PHP
		for ( $i = 11; $i > -1; $i-- ) {

			$month_number = $i + 1;

			$month_names[ (string) $i ] = date_i18n( 'F', strtotime( "1980-{$month_number}-01" ) );
		}

		return $month_names;
	}


	/**
	 * Get localized day initial letters.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of day initials
	 */
	private function get_day_initials() {

		$day_initials = array();

		for ( $i = 0; $i < 7; $i++ ) {
			$day_initial    = date_i18n( 'D', strtotime( "Sunday + $i days" ) );
			$day_initials[] = $day_initial[0];
		}

		return $day_initials;
	}


}
