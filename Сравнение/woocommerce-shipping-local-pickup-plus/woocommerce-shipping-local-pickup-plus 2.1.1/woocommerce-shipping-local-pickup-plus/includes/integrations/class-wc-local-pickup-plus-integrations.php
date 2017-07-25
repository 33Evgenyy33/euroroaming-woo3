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
 * Integrations class.
 *
 * Conditionally loads third party extensions and plugins compatibility code for:
 *
 * - WooCommerce Print Invoices & Packing Lists
 * - WooCommerce Customer Order CSV Export
 * - WooCommerce Customer Order XML Export Suite
 * - WooCommerce Per Product Shipping
 * - WooCommerce Subscriptions
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Integrations {


	/** @var \WC_Local_Pickup_Plus_Integration_PIP WooCommerce PIP integration instance */
	private $pip;

	/** @var bool whether WooCommerce PIP is active */
	private $is_pip_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Customer_Order_CSV_Export WooCommerce Customer Order CSV Export integration instance */
	private $csv_export;

	/** @var bool whether the WooCommerce Customer Order CSV Export extension is active */
	private $is_csv_export_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export WooCommerce Customer Order XML Export integration instance */
	private $xml_export;

	/** @var bool whether the WooCommerce Customer Order XML Export suite is active */
	private $is_xml_export_active;

	/** @var \WC_Local_Pickup_Plus_Integration_Subscriptions WooCommerce Subscriptions integration instance */
	private $subscriptions;

	/** @var null|bool whether WooCommerce Subscriptions is active */
	private $is_subscriptions_active;


	/**
	 * Load integrations.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// WooCommerce Customer Order CSV Export
		if ( $this->is_csv_export_active() ) {
			$this->csv_export = wc_local_pickup_plus()->load_class( '/includes/integrations/woocommerce-customer-order-csv-export/class-wc-local-pickup-plus-integration-customer-order-csv-export.php', 'WC_Local_Pickup_Plus_Integration_Customer_Order_CSV_Export' );
		}

		// WooCommerce Customer Order XML Export
		if ( $this->is_xml_export_active() ) {
			$this->xml_export = wc_local_pickup_plus()->load_class( '/includes/integrations/woocommerce-customer-order-xml-export-suite/class-wc-local-pickup-plus-integration-customer-order-xml-export.php', 'WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export' );
		}

		// WooCommerce Per Product Shipping
		add_filter( 'woocommerce_per_product_shipping_skip_free_method_local_pickup_plus', '__return_false' );

		// WooCommerce Print Invoices & Packing Lists
		if ( $this->is_pip_active() ) {
			$this->pip = wc_local_pickup_plus()->load_class( '/includes/integrations/woocommerce-pip/class-wc-local-pickup-plus-integration-pip.php', 'WC_Local_Pickup_Plus_Integration_PIP' );
		}

		// WooCommerce Subscriptions
		if ( $this->is_subscriptions_active() ) {
			$this->subscriptions = wc_local_pickup_plus()->load_class( '/includes/integrations/woocommerce-subscriptions/class-wc-local-pickup-plus-integration-subscriptions.php', 'WC_Local_Pickup_Plus_Integration_Subscriptions' );
		}
	}


	/**
	 * Get the PIP integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_PIP
	 */
	public function get_pip_instance() {
		return $this->pip;
	}


	/**
	 * Get the Subscriptions integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Subscriptions
	 */
	public function get_subscriptions_instance() {
		return $this->subscriptions;
	}


	/**
	 * Get the CSV Export integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Customer_Order_CSV_Export
	 */
	public function get_csv_export_instance() {
		return $this->csv_export;
	}


	/**
	 * Get the XML Export integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export
	 */
	public function get_xml_export_instance() {
		return $this->xml_export;
	}


	/**
	 * Check whether WooCommerce PIP is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_pip_active() {

		if ( is_bool( $this->is_pip_active ) ) {
			return $this->is_pip_active;
		}

		$this->is_pip_active = wc_local_pickup_plus()->is_plugin_active( 'woocommerce-pip.php' );

		return $this->is_pip_active;
	}


	/**
	 * Check whether WooCommerce Subscriptions is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_subscriptions_active() {

		if ( is_bool( $this->is_subscriptions_active ) ) {
			return $this->is_subscriptions_active;
		}

		$this->is_subscriptions_active = wc_local_pickup_plus()->is_plugin_active( 'woocommerce-subscriptions.php' );

		return $this->is_subscriptions_active;
	}


	/**
	 * Check whether WooCommerce Customer Order CSV Export is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_csv_export_active() {

		if ( is_bool( $this->is_csv_export_active ) ) {
			return $this->is_csv_export_active;
		}

		$this->is_csv_export_active = wc_local_pickup_plus()->is_plugin_active( 'woocommerce-customer-order-csv-export.php' );

		return $this->is_csv_export_active;
	}


	/**
	 * Check whether WooCommerce Customer Order XML Export Suite is installed and active.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_xml_export_active() {

		if ( is_bool( $this->is_xml_export_active ) ) {
			return $this->is_xml_export_active;
		}

		$this->is_xml_export_active = wc_local_pickup_plus()->is_plugin_active( 'woocommerce-customer-order-xml-export-suite.php' );

		return $this->is_xml_export_active;
	}


}
