<?php
/**
 * Plugin Name: WooCommerce Local Pickup Plus
 * Plugin URI: http://woocommerce.com/products/local-pickup-plus/
 * Description: A shipping plugin for WooCommerce that allows the store operator to define local pickup locations, which the customer can then choose from when making a purchase.
 * Author: SkyVerge
 * Author URI: http://woocommerce.com
 * Version: 2.3.3
 * Text Domain: woocommerce-shipping-local-pickup-plus
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2017 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Shipping-Local-Pickup-Plus
 * @author      SkyVerge
 * @category    Shipping
 * @copyright   Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 18696:4d6fbe9e8968a669d11cec40b85a0caa
 * WC requires at least: 2.6.14
 * WC tested up to: 3.2.0
 */

defined( 'ABSPATH' ) or exit;

// required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '4d6fbe9e8968a669d11cec40b85a0caa', '18696' );

// bail out if WooCommerce is not active
if ( ! is_woocommerce_active() ) {
	return;
}

// required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.7.3', __( 'WooCommerce Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' ), __FILE__, 'init_woocommerce_shipping_local_pickup_plus', array(
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_shipping_local_pickup_plus() {

/**
 * WooCommerce Local Pickup Plus Shipping Method.
 *
 * @since 1.0.0
 */
class WC_Local_Pickup_Plus extends SV_WC_Plugin {


	const VERSION = '2.3.3';

	/** shipping method ID */
	const SHIPPING_METHOD_ID = 'local_pickup_plus';

	/** shipping method class name */
	const SHIPPING_METHOD_CLASS_NAME = 'WC_Shipping_Local_Pickup_Plus';

	/** @var \WC_Local_Pickup_Plus single instance of this plugin */
	protected static $instance;

	/** @var bool whether the shipping method has been loaded while doing AJAX */
	private static $ajax_loaded = false;

	/** @var \WC_Local_Pickup_Plus_Pickup_Locations pickup locations handler instance */
	private $pickup_locations;

	/** @var string|\WC_Shipping_Local_Pickup_Plus Local pickup plus shipping class name or object */
	private $shipping_method;

	/** @var \WC_Local_Pickup_Plus_Geocoding_API geocoding API handler instance */
	private $geocoding;

	/** @var \WC_Local_Pickup_Plus_Geolocation geolocation handler instance */
	private $geolocation;

	/** @var \WC_Local_Pickup_Plus_Products products handler instance */
	private $products;

	/** @var \WC_Local_Pickup_Plus_Orders orders handler instance */
	private $orders;

	/** @var \WC_Local_Pickup_Plus_Packages packages handler instance */
	private $packages;

	/** @var \WC_Local_Pickup_Plus_Admin admin instance */
	private $admin;

	/** @var \WC_Local_Pickup_Plus_Frontend frontend instance */
	private $frontend;

	/** @var \WC_Local_Pickup_Plus_Ajax AJAX instance */
	private $ajax;

	/** @var \WC_Local_Pickup_Plus_Session session handler instance */
	private $session;

	/** @var \WC_Local_Pickup_Plus_Integrations integrations instance */
	private $integrations;

	/** @var bool whether geocoding features are enabled */
	private $geocoding_enabled;

	/** @var bool whether logging is enabled */
	private $logging_enabled;

	/** @var bool whether custom tables have been set */
	private $tables_exist = false;


	/**
	 * Setup main plugin class.
	 *
	 * @see \SV_WC_Plugin::__construct()
	 *
	 * @since 1.4
	 */
	public function __construct() {

		parent::__construct(
			self::SHIPPING_METHOD_ID,
			self::VERSION,
			array(
				'text_domain'        => 'woocommerce-shipping-local-pickup-plus',
				'display_php_notice' => true,
			)
		);

		$this->shipping_method = self::SHIPPING_METHOD_CLASS_NAME;

		add_action( 'init',                           array( $this, 'init_plugin' ) );
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'load_plugin' ) );
	}


	/**
	 * Load plugin classes.
	 *
	 * @since 2.0.0
	 */
	private function includes() {

		$plugin_path = $this->get_plugin_path();

		// load helper functions
		require_once( $plugin_path . '/includes/functions/wc-local-pickup-plus-functions.php' );

		// static class for custom post types handling
		require_once( $plugin_path . '/includes/class-wc-local-pickup-plus-post-types.php' );

		// include the Shipping method class
		require_once( $plugin_path . '/includes/class-wc-shipping-local-pickup-plus.php' );

		// geocoding API handler
		$this->geocoding        = $this->load_class( '/includes/api/class-wc-local-pickup-plus-geocoding-api.php', 'WC_Local_Pickup_Plus_Geocoding_API' );
		// geolocation handler
		$this->geolocation      = $this->load_class( '/includes/class-wc-local-pickup-plus-geolocation.php', 'WC_Local_Pickup_Plus_Geolocation' );
		// init session handler
		$this->session          = $this->load_class( '/includes/class-wc-local-pickup-plus-session.php', 'WC_Local_Pickup_Plus_Session' );
		// products handler
		$this->products         = $this->load_class( '/includes/class-wc-local-pickup-plus-products.php', 'WC_Local_Pickup_Plus_Products' );
		// orders handler
		$this->orders           = $this->load_class( '/includes/class-wc-local-pickup-plus-orders.php', 'WC_Local_Pickup_Plus_Orders' );
		// packages handler
		$this->packages         = $this->load_class( '/includes/class-wc-local-pickup-plus-packages.php', 'WC_Local_Pickup_Plus_Packages' );
		// init pickup locations
		$this->pickup_locations = $this->load_class( '/includes/class-wc-local-pickup-plus-pickup-locations.php', 'WC_Local_Pickup_Plus_Pickup_Locations' );

		// init UI handlers
		if ( is_admin() ) {
			// admin side
			$this->admin    = $this->load_class( '/includes/admin/class-wc-local-pickup-plus-admin.php', 'WC_Local_Pickup_Plus_Admin' );
		} else {
			// frontend side
			$this->frontend = $this->load_class( '/includes/frontend/class-wc-local-pickup-plus-frontend.php', 'WC_Local_Pickup_Plus_Frontend' );
		}

		// load ajax methods
		if ( is_ajax() ) {
			$this->ajax = $this->load_class( '/includes/class-wc-local-pickup-plus-ajax.php', 'WC_Local_Pickup_Plus_Ajax' );
		}

		// init integrations classes
		$this->integrations = $this->load_class( '/includes/integrations/class-wc-local-pickup-plus-integrations.php', 'WC_Local_Pickup_Plus_Integrations' );
	}


	/**
	 * Loads plugin classes and main hooks.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function load_plugin() {

		$this->includes();
		$this->init_shipping_method();
	}


	/**
	 * Init custom post types.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function init_plugin() {

		WC_Local_Pickup_Plus_Post_Types::init();

		// loads the local pickup plus class from the 'woocommerce_update_shipping_method' AJAX action early, which otherwise would not be loaded in time to update
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( ( isset( $_REQUEST['wc-ajax'] ) && 'update_order_review' === $_REQUEST['wc-ajax'] ) || ( isset( $_REQUEST['action' ] ) && 'woocommerce_update_shipping_method' === $_REQUEST['action'] ) ) ) {

			if ( false === self::$ajax_loaded ) {

				$this->load_shipping_method();

				self::$ajax_loaded = true;
			}
		}
	}


	/**
	 * Init main hooks.
	 *
	 * @since 2.0.0
	 */
	private function init_shipping_method() {

		// add class to WooCommerce Shipping Methods
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_shipping_method' ) );

		// make sure one instance of the Shipping class is set
		add_action( 'wc_shipping_local_pickup_plus_init', array( $this, 'set_shipping_method' ) );
	}


	/**
	 * Add the Shipping Method to WooCommerce.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[]|\WC_Shipping_Method[] $methods array of hipping method class names or objects
	 * @return string[]|\WC_Shipping_Method[]
	 */
	public function add_shipping_method( $methods ) {

		if ( ! array_key_exists( self::SHIPPING_METHOD_ID, $methods ) ) {

			// Since the shipping method is always constructed, we'll pass it in to the register filter so it doesn't have to be re-instantiated;
			// so, the following will be either the class name, or the class object if we've already instantiated it.
			$methods[ self::SHIPPING_METHOD_ID ] = $this->shipping_method;
		}

		return $methods;
	}


	/**
	 * Set the Local Pickup Plus shipping method.
	 *
	 * In this way, if shipping methods are loaded more than once during a request,
	 * we can avoid instantiating the class a second time and duplicating action hooks.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Shipping_Local_Pickup_Plus $local_pickup_plus Local Pickup Plus shipping class
	 */
	public function set_shipping_method( WC_Shipping_Local_Pickup_Plus $local_pickup_plus ) {

		if ( ! $this->shipping_method instanceof WC_Shipping_Local_Pickup_Plus ) {
			$this->shipping_method = $local_pickup_plus;
		}
	}


	/**
	 * Ensures the shipping method class is loaded.
	 *
	 * @since 2.0.0
	 */
	public function load_shipping_method() {
		$this->get_shipping_method_instance();
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the Local Pickup Plus shipping method main instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Shipping_Local_Pickup_Plus Local pickup plus shipping method
	 */
	public function get_shipping_method_instance() {

		if ( ! $this->shipping_method instanceof WC_Shipping_Local_Pickup_Plus ) {
			$this->shipping_method = new WC_Shipping_Local_Pickup_Plus();
		}

		return $this->shipping_method;
	}


	/**
	 * Get the pickup locations handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Locations
	 */
	public function get_pickup_locations_instance() {
		return $this->pickup_locations;
	}


	/**
	 * Get the geocoding API instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Geocoding_API
	 */
	public function get_geocoding_api_instance() {
		return $this->geocoding;
	}


	/**
	 * Get the geolocation instance.
	 *
	 * @since 2.1.1
	 *
	 * @return \WC_Local_Pickup_Plus_Geolocation
	 */
	public function get_geolocation_instance() {
		return $this->geolocation;
	}


	/**
	 * Get the session handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Session
	 */
	public function get_session_instance() {
		return $this->session;
	}


	/**
	 * Get the products handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Products
	 */
	public function get_products_instance() {
		return $this->products;
	}


	/**
	 * Get the orders handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Orders
	 */
	public function get_orders_instance() {
		return $this->orders;
	}


	/**
	 * Get the packages handler instance.
	 *
	 * @since 2.3.1
	 *
	 * @return \WC_Local_Pickup_Plus_Packages
	 */
	public function get_packages_instance() {
		return $this->packages;
	}


	/**
	 * Get the admin instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Get the frontend instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Frontend
	 */
	public function get_frontend_instance() {
		return $this->frontend;
	}


	/**
	 * Get the ajax instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Ajax
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Get the integrations instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integrations
	 */
	public function get_integrations_instance() {
		return $this->integrations;
	}


	/**
	 * Main Local Pickup Plus instance.
	 *
	 * Ensures only one instance loaded at one time.
	 *
	 * @see \wc_local_pickup_plus()
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Local_Pickup_Plus
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @see \SV_WC_Payment_Gateway::get_plugin_name()
	 *
	 * @since 1.5
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @see \SV_WC_Payment_Gateway::get_file()
	 *
	 * @since 1.5
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Check whether the plugin is using geocoding services for matching locations.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function geocoding_enabled() {

		if ( ! is_bool( $this->geocoding_enabled ) && ( $shipping_method = $this->get_shipping_method_instance() ) ) {

			$has_api_key = $shipping_method && $shipping_method->get_google_maps_api_key();

			/**
			 * Switch whether using geocoding features.
			 *
			 * @since 2.0.0
			 *
			 * @param bool $use_geocoding whether to use geocoding features (true) or not (false)
			 */
			$this->geocoding_enabled = (bool) apply_filters( 'wc_local_pickup_plus_geocoding_enabled', ! empty( $has_api_key ) );
		}

		return (bool) $this->geocoding_enabled;
	}


	/**
	 * Check if logging is enabled in settings.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function logging_enabled() {

		if ( ! is_bool( $this->logging_enabled ) && ( $shipping_method = $this->get_shipping_method_instance() ) ) {
			$this->logging_enabled = 'yes' === $shipping_method->get_option( 'enable_logging', 'no' );
		}

		return (bool) $this->logging_enabled;
	}


	/**
	 * Don't log API requests/responses when doing geocoding API calls when logging is disabled.
	 *
	 * Overrides framework parent method:
	 * @see \SV_WC_Plugin::add_api_request_logging()
	 * @see \SV_WC_API_Base::broadcast_request()
	 * @see \WC_Local_Pickup_Plus_Geocoding_API::get_coordinates()
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_api_request_logging() {

		if ( has_action( 'wc_' . $this->get_id() . '_api_request_performed' ) ) {
			remove_action( 'wc_' . $this->get_id() . '_api_request_performed', array( $this, 'log_api_request' ), 10, 2 );
		}
	}


	/**
	 * Check that pickup locations custom tables exist otherwise create them.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $create whether to create tables if they do not exist
	 * @return bool whether tables did exist or not
	 */
	public function check_tables( $create = true ) {
		global $wpdb;

		if ( ! $this->tables_exist ) {

			require_once( wc_local_pickup_plus()->get_plugin_path() . '/includes/class-wc-local-pickup-plus-lifecycle.php' );

			foreach ( WC_Local_Pickup_Plus_Lifecycle::get_table_names() as $table_name ) {

				if ( $table_name !== $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) {

					if ( true === $create ) {
						WC_Local_Pickup_Plus_Lifecycle::create_tables();
						$this->tables_exist = true;
					}

					break;
				}
			}
		}

		return $this->tables_exist;
	}


	/** Admin methods ******************************************************/


	/**
	 * Gets the plugin documentation url, which for Local Pickup Plus is non-standard.
	 *
	 * @since 1.5.0
	 *
	 * @see \SV_WC_Plugin::get_documentation_url()
	 *
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'https://docs.woocommerce.com/document/local-pickup-plus/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @see \SV_WC_Plugin::get_support_url()
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the shipping method configuration URL.
	 *
	 * @see \SV_WC_Plugin::get_settings_url()
	 *
	 * @since 1.5
	 *
	 * @param string $plugin_id the plugin identifier
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {
		return admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . strtolower( self::SHIPPING_METHOD_ID ) );
	}


	/**
	 * Returns true if on the shipping method settings page.
	 *
	 * @see \SV_WC_Plugin::is_plugin_settings()
	 *
	 * @since 1.5
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {
		return
			isset( $_GET['page'] )    && 'wc-settings' === $_GET['page'] &&
			isset( $_GET['tab'] )     && 'shipping' === $_GET['tab'] &&
			isset( $_GET['section'] ) && strtolower( self::SHIPPING_METHOD_ID ) === $_GET['section'];
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Display a dismissible welcome notice upon activation.
	 *
	 * @see \SV_WC_Plugin::add_admin_notices()
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notices() {

		parent::add_admin_notices();

		$screen = get_current_screen();

		// only render on plugins or settings screen
		if ( 'plugins' === $screen->id || $this->is_plugin_settings() ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				/* translators: the %s placeholders are meant for pairs of opening <a> and closing </a> link tags */
				sprintf( __( 'Thanks for installing Local Pickup Plus! To get started, please take a minute to %1$sread the documentation%2$s :)', 'woocommerce-shipping-local-pickup-plus' ),
					'<a href="' . esc_url( $this->get_documentation_url() ) . '" target="_blank">', '</a>'
				),
				'get-started-notice',
				array(
					'always_show_on_settings' => false,
					'notice_class'            => 'updated',
				)
			);
		}
	}


	/**
	 * Perform any initial install steps.
	 *
	 * @see \SV_WC_Plugin::install()
	 *
	 * @since 1.5
	 */
	protected function install() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-local-pickup-plus-lifecycle.php' );

		WC_Local_Pickup_Plus_Lifecycle::install();
	}


	/**
	 * Perform upgrades from older versions.
	 *
	 * @see \SV_WC_Plugin::install()
	 *
	 * @since 2.0.0
	 *
	 * @param string $installed_version Current installed version
	 */
	protected function upgrade( $installed_version ) {

		if ( ! $this->pickup_locations ) {
			$this->pickup_locations = $this->load_class( '/includes/class-wc-local-pickup-plus-pickup-locations.php', 'WC_Local_Pickup_Plus_Pickup_Locations' );
		}

		require_once( $this->get_plugin_path() . '/includes/class-wc-local-pickup-plus-lifecycle.php' );

		WC_Local_Pickup_Plus_Lifecycle::upgrade( $installed_version );
	}


} // \WC_Local_Pickup_Plus


/**
 * Returns the One True Instance of Local Pickup Plus.
 *
 * @since 1.10.0
 *
 * @return \WC_Local_Pickup_Plus
 */
function wc_local_pickup_plus() {
	return WC_Local_Pickup_Plus::instance();
}

// fire it up!
wc_local_pickup_plus();

} // init_woocommerce_shipping_local_pickup_plus()
