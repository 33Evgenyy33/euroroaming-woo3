<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main ACF Addon plugin class
 *
 * @since 1.0
 */
final class ACA_WC {

	CONST CLASS_PREFIX = 'ACA_WC_';

	/**
	 * @var ACA_WC_Helper
	 */
	private $helper;

	/**
	 * Current plugin version
	 *
	 * @var null
	 */
	private $version;

	/**
	 * @since 3.8
	 */
	private static $_instance = null;

	/**
	 * @since 3.8
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'init' ) );
	}

	/**
	 * @since 2.0
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		if ( $this->has_missing_dependencies() ) {
			return;
		}

		AC()->autoloader()->register_prefix( self::CLASS_PREFIX, $this->get_dir() . 'classes/' );

		add_action( 'ac/list_screen_groups', array( $this, 'register_list_screen_groups' ) );
		add_action( 'ac/column_groups', array( $this, 'register_column_groups' ) );
		add_action( 'ac/list_screens', array( $this, 'register_list_screens' ) );
		add_action( 'acp/column_types', array( $this, 'register_columns' ) );

		// Scripts
		add_action( 'ac/table_scripts', array( $this, 'table_scripts' ) );
		add_action( 'ac/table_scripts/editing', array( $this, 'table_scripts_editing' ) );

		// Editing
		add_filter( 'ac/editing/role_group', array( $this, 'set_editing_role_group' ), 10, 2 );
	}

	/**
	 * @since 2.0
	 */
	public function get_dir() {
		return plugin_dir_path( __FILE__ );
	}

	/**
	 * @since NEWVERSION
	 */
	public function get_url() {
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * @return string
	 */
	public function get_basename() {
		return plugin_basename( ACA_WC_FILE );
	}

	public function register_list_screens() {
		AC()->register_list_screen( new ACA_WC_ListScreen_ShopOrder );
		AC()->register_list_screen( new ACA_WC_ListScreen_ShopCoupon );
		AC()->register_list_screen( new ACA_WC_ListScreen_Product );
	}

	/**
	 * @param AC_ListScreen $list_screen
	 */
	public function register_columns( $list_screen ) {
		if ( $list_screen instanceof AC_ListScreen_User ) {
			$list_screen->register_column_types_from_dir( $this->get_dir() . 'classes/Column/User', ACA_WC::CLASS_PREFIX );
		}
	}

	/**
	 * @param AC_Groups $groups
	 */
	public function register_list_screen_groups( $groups ) {
		$groups->register_group( 'woocommerce', 'WooCommerce', 7 );
	}

	/**
	 * @param AC_Groups $groups
	 */
	public function register_column_groups( $groups ) {
		$groups->register_group( 'woocommerce', __( 'WooCommerce' ), 15 );
	}

	/**
	 * @return bool True when there are missing dependencies
	 */
	private function has_missing_dependencies() {
		require_once $this->get_dir() . 'classes/Dependencies.php';

		$dependencies = new ACA_WC_Dependencies( $this->get_basename() );
		$dependencies->is_acp_active( '4.0.3' );

		if ( ! $this->is_woocommerce_active() ) {
			$dependencies->add_missing( $dependencies->get_search_link( 'WooCommerce', 'WooCommerce' ) );
		}

		return $dependencies->has_missing();
	}

	public function helper() {
		if ( null === $this->helper ) {
			$this->helper = new ACA_WC_Helper();
		}

		return $this->helper;
	}

	/**
	 * Set plugin version
	 */
	private function set_version() {
		$plugins = get_plugins();

		$this->version = $plugins[ $this->get_basename() ]['Version'];
	}

	/**
	 * @since 2.0
	 */
	public function get_version() {
		if ( null === $this->version ) {
			$this->set_version();
		}

		return $this->version;
	}

	private function is_wc_list_screen( $list_screen ) {
		return $list_screen instanceof ACA_WC_ListScreen_ShopOrder || $list_screen instanceof ACA_WC_ListScreen_ShopCoupon || $list_screen instanceof ACA_WC_ListScreen_Product || $list_screen instanceof AC_ListScreen_User;
	}

	/**
	 * @param AC_ListScreen $list_screen
	 */
	public function table_scripts_editing( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		$url = $this->get_url();

		wp_enqueue_script( 'aca-wc-xeditable-input-dimensions', $url . 'assets/js/xeditable/input/dimensions.js', array( 'jquery', 'acp-editing-table' ), $this->get_version() );
		wp_enqueue_script( 'aca-wc-xeditable-input-wc-price', $url . 'assets/js/xeditable/input/wc-price.js', array( 'jquery', 'acp-editing-table' ), $this->get_version() );
		wp_enqueue_script( 'aca-wc-xeditable-input-wc-stock', $url . 'assets/js/xeditable/input/wc-stock.js', array( 'jquery', 'acp-editing-table' ), $this->get_version() );
		wp_enqueue_script( 'aca-wc-xeditable-input-wc-usage', $url . 'assets/js/xeditable/input/wc-usage.js', array( 'jquery', 'acp-editing-table' ), $this->get_version() );

		// Translations
		wp_localize_script( 'acp-editing-table', 'acp_woocommerce_i18n', array(
			'woocommerce' => array(
				'stock_qty'              => __( 'Stock Qty', 'woocommerce' ),
				'manage_stock'           => __( 'Manage stock?', 'woocommerce' ),
				'stock_status'           => __( 'Stock status', 'woocommerce' ),
				'in_stock'               => __( 'In stock', 'woocommerce' ),
				'out_of_stock'           => __( 'Out of stock', 'woocommerce' ),
				'regular'                => __( 'Regular', 'codepress-admin-columns' ),
				'sale'                   => __( 'Sale', 'woocommerce' ),
				'sale_from'              => __( 'Sale from', 'codepress-admin-columns' ),
				'sale_to'                => __( 'Sale To', 'codepress-admin-columns' ),
				'schedule'               => __( 'Schedule', 'woocommerce' ),
				'usage_limit_per_coupon' => __( 'Usage limit per coupon', 'woocommerce' ),
				'usage_limit_per_user'   => __( 'Usage limit per user', 'woocommerce' ),
				'length'                 => __( 'Length', 'woocommerce' ),
				'width'                  => __( 'Width', 'woocommerce' ),
				'height'                 => __( 'Height', 'woocommerce' ),
			),
		) );
	}

	/**
	 * @since 1.3
	 *
	 * @param AC_ListScreen $list_screen
	 */
	public function table_scripts( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		$url = $this->get_url();

		wp_enqueue_style( 'aca-wc-column', $url . 'assets/css/column.css', array(), $this->get_version() );
	}

	/**
	 * Whether WooCommerce is active
	 *
	 * @since 1.0
	 *
	 * @return bool Returns true if WooCommerce is active, false otherwise
	 */
	public function is_woocommerce_active() {
		return class_exists( 'WooCommerce', false );
	}

	/**
	 * @param string $group
	 * @param string $role
	 *
	 * @return string
	 */
	public function set_editing_role_group( $group, $role ) {
		if ( in_array( $role, array( 'customer', 'shop_manager' ) ) ) {
			$group = __( 'WooCommerce' );
		}

		return $group;
	}

	function is_woocommerce_version_gte( $version = '1.0' ) {
		$wc_version = defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;

		return $wc_version && version_compare( $wc_version, $version, '>=' );
	}

}

function ac_addon_wc() {
	return ACA_WC::instance();
}

function ac_addon_wc_helper() {
	return ac_addon_wc()->helper();
}

ac_addon_wc();
