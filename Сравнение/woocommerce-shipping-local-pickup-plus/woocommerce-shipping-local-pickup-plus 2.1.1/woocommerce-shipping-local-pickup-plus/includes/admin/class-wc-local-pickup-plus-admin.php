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
 * Admin class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Admin {


	/** @var \WC_Local_Pickup_Plus_Pickup_Locations_Admin pickup locations admin handler instance */
	private $pickup_locations;

	/** @var \WC_Local_Pickup_Plus_Orders_Admin admin handler for orders pickup locations data instance */
	private $orders;

	/** @var \WC_Local_Pickup_Plus_Products_Admin admin handler for products & product categories instance */
	private $products;

	/** @var \WC_Local_Pickup_Plus_Import pickup locations import class instance */
	private $import;

	/** @var \WC_Local_Pickup_Plus_Export pickup locations export class instance */
	private $export;

	/** @var \stdClass container of meta box classes instances */
	private $meta_boxes;


	/**
	 * Admin constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->includes();

		// init content in Local Pickup Plus admin screens
		add_action( 'current_screen', array( $this, 'init' ) );

		// init import/export page
		add_action( 'admin_menu', array( $this, 'add_import_export_admin_pages' ) );

		// makes sure that the WooCommerce Settings menu item is set to currently active when editing Pickup Locations
		add_filter( 'parent_file', array( $this, 'set_current_admin_menu_item' ) );

		// display admin messages
		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );

		// ensure WooCommerce core scripts and styles are loaded on plugin screens
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );
		// enqueue Local Pickup Plus own scripts & styles
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts_styles' ), 20 );

		// callback to output a search pickup locations field from a field settings array
		add_action( 'woocommerce_admin_field_search_pickup_locations', array( $this, 'output_settings_search_pickup_locations_field' ) );

		// process Pickup Locations import / export submission form
		add_action( 'admin_post_wc_local_pickup_plus_csv_import', array( $this, 'process_import_export_form' ) );
		add_action( 'admin_post_wc_local_pickup_plus_csv_export', array( $this, 'process_import_export_form' ) );
	}


	/**
	 * Include admin classes and objects.
	 *
	 * @since 2.0.0
	 */
	private function includes() {

		// Pickup Locations admin edit screens
		$this->pickup_locations = wc_local_pickup_plus()->load_class( '/includes/admin/class-wc-local-pickup-plus-pickup-locations-admin.php', 'WC_Local_Pickup_Plus_Pickup_Locations_Admin' );

		// Pickup Locations handler for WC orders
		$this->orders = wc_local_pickup_plus()->load_class( '/includes/admin/class-wc-local-pickup-plus-orders-admin.php', 'WC_Local_Pickup_Plus_Orders_Admin' );

		// Products and Product Categories handler class
		$this->products = wc_local_pickup_plus()->load_class( '/includes/admin/class-wc-local-pickup-plus-products-admin.php', 'WC_Local_Pickup_Plus_Products_Admin' );

		// Pickup Locations Import and Export handlers
		require_once( wc_local_pickup_plus()->get_plugin_path() . '/includes/admin/abstract-class-wc-local-pickup-plus-import-export.php' );

		$this->import = wc_local_pickup_plus()->load_class( '/includes/admin/class-wc-local-pickup-plus-import.php', 'WC_Local_Pickup_Plus_Import' );
		$this->export = wc_local_pickup_plus()->load_class( '/includes/admin/class-wc-local-pickup-plus-export.php', 'WC_Local_Pickup_Plus_Export' );
	}


	/**
	 * Init Local Pickup Plus admin screens.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->meta_boxes = $this->load_meta_boxes();
	}


	/**
	 * Get the pickup locations admin handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Pickup_Locations_Admin instance
	 */
	public function get_pickup_locations_instance() {
		return $this->pickup_locations;
	}


	/**
	 * Get the orders handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Orders_Admin instance
	 */
	public function get_orders_instance() {
		return $this->orders;
	}


	/**
	 * Get the products & product categories admin handler instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Products_Admin instance
	 */
	public function get_products_instance() {
		return $this->products;
	}


	/**
	 * Get the import class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Import instance
	 */
	public function get_import_instance() {
		return $this->import;
	}


	/**
	 * Get the export class instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Export instance
	 */
	public function get_export_instance() {
		return $this->export;
	}


	/**
	 * Get the meta boxes instances.
	 *
	 * @since 2.0.0
	 *
	 * @return \stdClass a container object for individual meta boxes class instances
	 */
	public function get_meta_boxes_instance() {
		return $this->meta_boxes;
	}


	/**
	 * Get Local Pickup Plus admin screens IDs.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of screen ID strings
	 */
	private function get_screen_ids() {
		return array(
			// Pickup Location post type
			'wc_pickup_location',
			// Pickup Locations Import & Export pages
			'admin_page_wc_local_pickup_plus_import',
			'admin_page_wc_local_pickup_plus_export',
		);
	}


	/**
	 * Check if we are on a Local Pickup Plus admin screen.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_admin_screen() {
		global $typenow;

		// Return true on the following screens:
		// - order, product or pickup location edit screens
		// - pickup locations import & export pages
		// - any of the Local Pickup Plus setting pages
		$is_admin_screen = in_array( $typenow, array( 'product', 'shop_order', 'wc_pickup_location' ) , true ) || false !== $this->is_import_export_page() || wc_local_pickup_plus()->is_plugin_settings();

		/**
		 * Filter whether the current admin screen is a Local Pick Plus admin screen.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $is_admin_screen whether we are on a Local Pickup Plus admin screen
		 */
		return apply_filters( 'wc_local_pickup_plus_is_admin_screen', $is_admin_screen );
	}


	/**
	 * Check if a screen is a pickup locations import or export page.
	 *
	 * @since 2.0.0
	 *
	 * @param null|\WP_Screen $screen optional, defaults to current screen global
	 * @return false|string false or ID string of the corresponding page
	 */
	public function is_import_export_page( $screen = null ) {

		$current_screen = null !== $screen ? $screen : get_current_screen();

		if ( $current_screen instanceof WP_Screen ) {
			switch ( $current_screen->id ) {
				case 'admin_page_wc_local_pickup_plus_import' :
					return 'import';
				case 'admin_page_wc_local_pickup_plus_export' :
					return 'export';
				default:
					return false;
			}
		}

		return false;
	}


	/**
	 * Display admin messages.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function show_admin_messages() {

		wc_local_pickup_plus()->get_message_handler()->show_messages();
	}


	/**
	 * Add a pickup locations import/export admin page.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_import_export_admin_pages() {

		$pages = array(
			'wc_local_pickup_plus_import' => __( 'Import', 'woocommerce-shipping-local-pickup-plus' ),
			'wc_local_pickup_plus_export' => __( 'Export', 'woocommerce-shipping-local-pickup-plus' ),
		);

		foreach ( $pages as $key => $page_name ) {
			add_submenu_page( '', $page_name, $page_name, $this->get_import_export_capability(), $key, array( $this, 'render_import_export_admin_page' ) );
		}
	}


	/**
	 * Get capability for managing the pickup locations CSV import / export functionality.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_import_export_capability() {

		/**
		 * Filter minimum capability to use Import / Export features.
		 *
		 * @since 2.0.0
		 * @param string $capability Defaults to Shop Managers with 'manage_woocommerce'.
		 */
		return apply_filters( 'wc_local_pickup_plus_can_import_export_capability', 'manage_woocommerce' );
	}


	/**
	 * Render the pickup locations import/export page.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function render_import_export_admin_page() {

		/**
		 * Output the Import / Export admin page.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wc_local_pickup_plus_render_import_export_page' );
	}


	/**
	 * Process a form submission for exporting or importing Pickup Locations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function process_import_export_form() {

		// get action and bail out if can't be found
		if ( isset( $_POST['action'], $_POST['_wp_http_referer'] ) && is_string( $_POST['action'] ) && SV_WC_Helper::str_starts_with( $_POST['action'], 'wc_local_pickup_plus_' ) ) {
			$action = str_replace( 'wc_local_pickup_plus_csv_', '', $_POST['action'] );
		} else {
			return;
		}

		// security checks
		if ( ! check_admin_referer( "wc_local_pickup_plus_csv_{$action}" ) || ! current_user_can( $this->get_import_export_capability() ) ) {
			wp_die( __( 'You are not allowed to perform this action.', 'woocommerce-shipping-local-pickup-plus' ) );
		}

		// run action
		switch ( $action ) {
			case 'export' :
				$this->get_export_instance()->process_export();
			break;
			case 'import' :
				$this->get_import_instance()->process_import();
			break;
		}

		// finally redirect back to import / export screen
		wp_safe_redirect( $_POST['_wp_http_referer'] );
		exit;
	}


	/**
	 * Load Meta Boxes
	 *
	 * @since 1.3.13-1
	 *
	 * @return stdClass
	 */
	private function load_meta_boxes() {
		global $pagenow, $current_screen;

		$meta_boxes = new stdClass();

		// bail out if not on a new post / edit post screen
		if ( ! $current_screen || ! in_array( $pagenow, array( 'post-new.php', 'post.php' ), true ) ) {
			return $meta_boxes;
		}

		// load meta boxes abstract class
		require_once( wc_local_pickup_plus()->get_plugin_path() . '/includes/admin/meta-boxes/abstract-class-wc-local-pickup-plus-meta-box.php' );

		$meta_boxes_classes = array();

		// load pickup location meta boxes on pickup location screens only
		if ( 'wc_pickup_location' === $current_screen->id ) {

			$meta_boxes_classes[] = 'WC_Local_Pickup_Plus_Meta_Box_Pickup_Location_Data';

			if ( wc_local_pickup_plus()->geocoding_enabled() ) {

				$meta_boxes_classes[] = 'WC_Local_Pickup_Plus_Meta_Box_Pickup_Location_Geodata';
			}
		}

		if ( ! empty( $meta_boxes_classes ) ) {

			// load and instantiate each meta box
			foreach ( $meta_boxes_classes as $class ) {

				$file_name = 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
				$file_path = wc_local_pickup_plus()->get_plugin_path() . '/includes/admin/meta-boxes/' . $file_name;

				if ( is_readable( $file_path ) ) {

					require_once( $file_path );

					if ( class_exists( $class ) ) {

						$instance_name              = strtolower( str_replace( 'WC_Local_Pickup_Plus_Meta_Box_', '', $class ) );
						$meta_boxes->$instance_name = new $class();
					}
				}
			}
		}

		return $meta_boxes;
	}


	/**
	 * Enqueue admin scripts & styles.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts_styles() {

		// only load scripts on appropriate screens
		if ( $this->is_admin_screen() ) {

			$this->enqueue_styles();
			$this->enqueue_scripts();
		}
	}


	/**
	 * Enqueue JS admin scripts.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_scripts() {
		global $typenow;

		$dependencies = array(
			'jquery',
			'jquery-ui-datepicker',
			'select2'
		);

		$scripts = array( 'wc-local-pickup-plus-admin' );

		if ( 'product' !== $typenow ) {
			$scripts[] = 'wc-local-pickup-plus-business-hours';
			$scripts[] = 'wc-local-pickup-plus-public-holidays';
		}

		if ( 'shop_order' === $typenow ) {
			$scripts[] = 'wc-local-pickup-plus-orders';
		}

		// enqueue the plugin own scripts
		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script, wc_local_pickup_plus()->get_plugin_url() . "/assets/js/admin/{$script}.min.js", $dependencies, WC_Local_Pickup_Plus::VERSION );
		}

		// localize the main script with variables and l10n strings
		wp_localize_script( 'wc-local-pickup-plus-admin', 'wc_local_pickup_plus_admin', array(

			// add any config/state properties here, for example:
			// 'is_user_logged_in' => is_user_logged_in()
			'ajax_url'                         => admin_url( 'admin-ajax.php' ),
			'wc_plugin_url'                    => WC()->plugin_url(),
			'select2_version'                  => SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? '4.0.3' : '3.5.3',
			'shipping_method_id'               => wc_local_pickup_plus_shipping_method_id(),
			'start_of_week'                    => get_option( 'start-of-week', 1 ),
			'search_pickup_locations_nonce'    => wp_create_nonce( 'search-pickup-locations' ),
			'search_products_nonce'            => wp_create_nonce( 'search-products' ),
			'search_terms_nonce'               => wp_create_nonce( 'search-terms' ),
			'get_time_range_picker_html_nonce' => wp_create_nonce( 'get-time-range-picker-html' ),
			'update_order_pickup_data_nonce'   => wp_create_nonce( 'update-order-pickup-data' ),

			'i18n' => array(

				// add i18n strings here, for example:
				// 'local_pickup_plus' => __( 'Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' )
				'search_type_minimum_characters' => __( 'Please enter 2 or more characters&hellip;', 'woocommerce-shipping-local-pickup-plus' ),
				'add_new_order_pickup_data'      => __( 'Please make sure you have added products, then create or update this order first, to be able to add pickup details.', 'woocommerce-shipping-local-pickup-plus.' ),

			),

		) );
	}


	/**
	 * Enqueue admin CSS stylesheets.
	 *
	 * @since 2.0.0
	 */
	private function enqueue_styles() {

		wp_enqueue_style( 'wc-local-pickup-plus-admin', wc_local_pickup_plus()->get_plugin_url() . '/assets/css/admin/wc-local-pickup-plus-admin.min.css', '', WC_Local_Pickup_Plus::VERSION );
	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its JS on.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $screen_ids WooCommerce screen IDs
	 * @return string[] Filtered IDs
	 */
	public function load_wc_scripts( array $screen_ids ) {
		return array_merge( $screen_ids, $this->get_screen_ids() );
	}


	/**
	 * Get HTML for a pickup locations search field for admin screens use.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field field settings
	 * @return string HTML
	 */
	public function get_search_pickup_locations_field( $field ) {

		$custom_attributes = array();

		if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
			foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( isset( $field['value'] ) ) {
			$value = $field['value'];
		} else {
			$value = ! empty( $field['default'] ) ? $field['default'] : '';
		}

		$pickup_location = $value;

		if ( is_numeric( $value ) ) {
			$pickup_location = $value > 0 ? wc_local_pickup_plus_get_pickup_location( $value ) : null;
		} elseif ( ! $value instanceof WC_Local_Pickup_Plus_Pickup_Location ) {
			$pickup_location = null;
		}

		ob_start(); ?>

		<?php if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) : ?>

			<select
				name="<?php echo esc_attr( isset( $field['input_name'] ) ? $field['input_name'] : $field['id'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="<?php echo esc_attr( $field['class'] ); ?>"
				style="<?php echo esc_attr( $field['css'] ); ?>"
				<?php echo implode( ' ', $custom_attributes ); ?>
				data-minimum-input-length="2">
				<?php if ( $pickup_location ) : ?>
					<option value="<?php echo esc_attr( $pickup_location->get_id() ); ?>" selected><?php echo esc_html( $pickup_location->get_name() ); ?></option>
				<?php endif; ?>
			</select>

		<?php else : ?>

			<input
				type="hidden"
				name="<?php echo esc_attr( isset( $field['input_name'] ) ? $field['input_name'] : $field['id'] ); ?>"
				id="<?php echo esc_attr( $field['id'] ); ?>"
				class="<?php echo esc_attr( $field['class'] ); ?>"
				style="<?php echo esc_attr( $field['css'] ); ?>"
				value="<?php echo $pickup_location ? esc_attr( $pickup_location->get_id() ) : ''; ?>"
				<?php echo implode( ' ', $custom_attributes ); ?>
				data-minimum-input-length="2"
			/>

		<?php endif; ?>

		<?php return ob_get_clean();
	}


	/**
	 * Output HTML for a pickup locations search field for admin screens use.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field Field settings
	 */
	public function output_search_pickup_locations_field( $field ) {

		echo $this->get_search_pickup_locations_field( $field );
	}


	/**
	 * Output a pickup search fields for settings usage.
	 *
	 * To output within WC, use 'search_pickup_locations' as field type.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 * @param $field
	 */
	public function output_settings_search_pickup_locations_field( $field ) {

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo ! empty( $field['desc_tip'] ) ? wc_help_tip( $field['desc_tip'] ) : ''; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ); ?>">
				<?php $this->output_search_pickup_locations_field( $field ); ?>
			</td>
		</tr>
		<?php
	}


	/**
	 * Set the WooCommerce Settings admin menu item as active while viewing a Pickup Location edit screen.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent_file
	 * @return string
	 */
	public function set_current_admin_menu_item( $parent_file ) {
		global $typenow, $menu, $submenu_file;

		if (    'wc_pickup_location' === $typenow
		     || ( isset( $_GET['post_type'] ) && 'wc_pickup_location' === $_GET['post_type'] )
		     || $this->is_import_export_page() ) {

			// overwrite the submenu global (this may appear unused in some IDEs)
			$submenu_file = 'admin.php?page=wc-settings&tab=shipping&section=local_pickup_plus';

			// Open the WooCommerce admin menu.
			if ( ! empty( $menu ) ) {

				foreach ( $menu as $key => $value ) {

					if ( isset( $value[2], $menu[ $key ][4] ) && 'woocommerce' === $value[2] ) {
						$menu[ $key ][4] .= ' wp-has-current-submenu wp-menu-open';
					}
				}

				// Highlight WooCommerce settings admin menu item.
				wc_enqueue_js( "
					jQuery( document ).ready( function( $ ) {
						var menuLink = $( '#adminmenuwrap' ).find( 'a[href=\"admin.php?page=wc-settings\"]' );
						if ( menuLink ) {
							menuItem = menuLink.parent().addClass( 'current' );
						}
					} );
				" );
			}
		}

		return $parent_file;
	}


	/**
	 * Output WooCommerce core setting tabs.
	 *
	 * @see \WC_Local_Pickup_Plus_Pickup_Locations_Admin::output_woocommerce_settings_tabs_html()
	 * @see \WC_Local_Pickup_Plus_Import_Export_Handler::output_woocommerce_settings_tabs_html()
	 *
	 * @since 2.0.0
	 */
	public function output_woocommerce_tabs_html() {

		WC_Admin_Settings::get_settings_pages();

		// get tabs for the settings page.
		$tabs = apply_filters( 'woocommerce_settings_tabs_array', array() );

		?>
		<div class="wrap woocommerce">
			<form method="<?php echo esc_attr( apply_filters( 'woocommerce_settings_form_method_tab_shipping', 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">

				<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php foreach ( $tabs as $name => $label ) : ?>
						<a href="<?php echo admin_url( "admin.php?page=wc-settings&tab={$name}" ); ?>" class="nav-tab <?php if ( 'shipping' === $name ) { echo 'nav-tab-active'; } ?>"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>

				<ul class="subsubsub">
					<?php

					$shipping   = new WC_Settings_Shipping();
					$sections   = $shipping->get_sections();
					$array_keys = array_keys( $sections );

					foreach ( $sections as $id => $label ) {
						echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . sanitize_title( $id ) ) . '" class="' . ( 'pickup_locations' === $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) === $id ? '' : '|' ) . ' </li>';
					}

					?>
				</ul>

				<br class="clear" />
			</form>
		</div>
		<?php
	}


}
