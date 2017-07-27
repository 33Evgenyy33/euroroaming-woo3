<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Admin class.
 *
 * Handle all admin related functions.
 *
 * @author     	Jeroen Sormani
 * @version		1.0.0
 */
class WAS_Admin {


	/**
	 * Constructor.
	 *
	 * @since 1.0.5
	 */
	public function __construct() {

		// Initialize components
		add_action( 'admin_init', array( $this, 'init' ) );

	}


	/**
	 * Initialize class components.
	 *
	 * @since 1.1.8
	 */
	public function init() {

		global $pagenow;

		// Add to WC Screen IDs to load scripts.
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Keep WC menu open while in WAS edit screen
		add_action( 'admin_head', array( $this, 'menu_highlight' ) );

		if ( 'plugins.php' == $pagenow ) :
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( WooCommerce_Advanced_Shipping()->file ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		endif;

	}


	/**
	 * Screen IDs.
	 *
	 * Add 'was' to the screen IDs so the WooCommerce scripts are loaded.
	 *
	 * @since 1.0.5
	 *
	 * @param  array $screen_ids List of existing screen IDs.
	 * @return array             List of modified screen IDs.
	 */
	public function add_screen_ids( $screen_ids ) {

		$screen_ids[] = 'was';

		return $screen_ids;

	}


	/**
	 * Enqueue scripts.
	 *
	 * Enqueue style and java scripts.
	 *
	 * @since 1.0.5
	 */
	public function admin_enqueue_scripts() {

		// Only load scripts on relevant pages
		if (
			( isset( $_REQUEST['post'] ) && 'was' == get_post_type( $_REQUEST['post'] ) ) ||
			( isset( $_REQUEST['post_type'] ) && 'was' == $_REQUEST['post_type'] ) ||
			( isset( $_REQUEST['section'] ) && in_array( $_REQUEST['section'], array( 'was_advanced_shipping_method', 'advanced_shipping' ) ) )
		) :

			// Style script
			wp_enqueue_style( 'woocommerce-advanced-shipping', plugins_url( 'assets/admin/css/woocommerce-advanced-shipping.min.css', WooCommerce_Advanced_Shipping()->file ), array(), WooCommerce_Advanced_Shipping()->version );

			// Javascript
			wp_enqueue_script( 'woocommerce-advanced-shipping', plugins_url( 'assets/admin/js/woocommerce-advanced-shipping.min.js', WooCommerce_Advanced_Shipping()->file ), array( 'jquery', 'jquery-ui-sortable', 'jquery-blockui', 'jquery-tiptip' ), WooCommerce_Advanced_Shipping()->version, true );
			wp_enqueue_script( 'select2' );

			wp_localize_script( 'woocommerce-advanced-shipping', 'wpc', array(
				'nonce'         => wp_create_nonce( 'wpc-ajax-nonce' ),
				'action_prefix' => 'was_',
				'asset_url'     => plugins_url( 'assets/', WooCommerce_Advanced_Shipping()->file ),
			) );

		endif;

	}


	/**
	 * Keep menu open.
	 *
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 * @since 1.0.5
	 */
	public function menu_highlight() {

		global $parent_file, $submenu_file, $post_type;

		if ( 'was' == $post_type ) :
			$parent_file  = 'woocommerce';
			$submenu_file = 'wc-settings';
		endif;

	}


	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.1.8
	 *
	 * @param  array  $links List of existing links.
	 * @param  string $file  Name of the current plugin being looped.
	 * @return array         List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( $file == plugin_basename( WooCommerce_Advanced_Shipping()->file ) ) :
			$links = array_merge( array(
				'<a href="' . esc_url( admin_url( '/admin.php?page=wc-settings&tab=shipping&section=was_advanced_shipping_method' ) ) . '">' . __( 'Settings', 'woocommerce-advanced-shipping' ) . '</a>'
			), $links );
		endif;

		return $links;

	}


}
