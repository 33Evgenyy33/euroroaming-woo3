<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Checkout_For_Digital_Goods {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Checkout_For_Digital_Goods_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'woo-checkout-for-digital-goods';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Checkout_For_Digital_Goods_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Checkout_For_Digital_Goods_i18n. Defines internationalization functionality.
	 * - Woo_Checkout_For_Digital_Goods_Admin. Defines all hooks for the admin area.
	 * - Woo_Checkout_For_Digital_Goods_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-checkout-for-digital-goods-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-checkout-for-digital-goods-i18n.php';
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-checkout-for-digital-goods-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-checkout-for-digital-goods-admin.php';

		$this->loader = new Woo_Checkout_For_Digital_Goods_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_Checkout_For_Digital_Goods_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_Checkout_For_Digital_Goods_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Checkout_For_Digital_Goods_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_checkout_fields', $plugin_public, 'custom_override_checkout_fields',10 );
		$this->loader->add_filter( 'woocommerce_paypal_args', $plugin_public, 'paypal_bn_code_filter_woo_checkout_field',99,1 );

	}
	
	private function define_admin_hooks() {
		
		$plugin_admin = new Woo_Checkout_For_Digital_Goods_Admin( $this->get_plugin_name(), $this->get_version() );
	    $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'woo_checkout_for_digital_create_page' );
		$this->loader->add_action( 'wp_ajax_add_plugin_user_wcf', $plugin_admin, 'wp_add_plugin_userfn' );
		$this->loader->add_action( 'wp_ajax_hide_subscribe_wcf', $plugin_admin, 'hide_subscribe_wcffn' );
		
		$this->loader->add_action('admin_init', $plugin_admin, 'welcome_woocommerce_digital_goods_screen_do_activation_redirect');
        $this->loader->add_action('admin_menu', $plugin_admin, 'welcome_pages_screen_woocommerce_digital_counter');
        $this->loader->add_action('woocommerce_digital_goods_other_plugins', $plugin_admin, 'woocommerce_digital_goods_other_plugins');
        $this->loader->add_action('woocommerce_digital_goods_about', $plugin_admin, 'woocommerce_digital_goods_about');
        $this->loader->add_action('admin_print_footer_scripts', $plugin_admin, 'custom_woo_digital_goods_pointers_footer');
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'welcome_screen_digital_goods_remove_menus', 999 );
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Checkout_For_Digital_Goods_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
