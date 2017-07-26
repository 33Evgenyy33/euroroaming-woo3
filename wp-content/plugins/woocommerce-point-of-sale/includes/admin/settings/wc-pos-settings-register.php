<?php
/**
 * WooCommerce POS General Settings
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/settings
 * @category	Class
 * @since     0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_POS_Admin_Settings_Register' ) ) :

/**
 * WC_POS_Admin_Settings_Layout
 */
class WC_POS_Admin_Settings_Register extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'register_pos';
		$this->label = __( 'Register', 'woocommerce' );

		add_filter( 'wc_pos_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'wc_pos_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'wc_pos_settings_save_' . $this->id, array( $this, 'save' ) );

	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		global $woocommerce;

		return apply_filters( 'woocommerce_point_of_sale_general_settings_fields', array(
			
			array( 'title' => __( 'Register Options', 'woocommerce' ), 'type' => 'title', 'desc' => __('The following options affect the settings that are applied when loading all registers.', 'woocommerce'), 'id' => 'general_options' ),

            array(
                'name' => __('Auto Update Stock', 'wc_point_of_sale'),
                'id' => 'wc_pos_autoupdate_stock',
                'type' => 'checkbox',
                'desc' => __('Enable update stock automatically ', 'wc_point_of_sale'),
                'desc_tip' => __('Updates the stock inventories for products automatically whilst running the register. Enabling this may hinder server performance. ', 'wc_point_of_sale'),
                'default' => 'no',
                'autoload' => true
            ),
            array(
                'name' => __('Update Interval', 'wc_point_of_sale'),
                'id' => 'wc_pos_autoupdate_interval',
                'type' => 'number',
                'desc_tip' => __('Enter the interval for auto-update in seconds.', 'wc_point_of_sale'),
                'desc' => __('seconds', 'wc_point_of_sale'),
                'default' => 240,
                'autoload' => true,
                'css' => 'width: 50px;'
            ),
            array(
                'name' => __('Stock Quantity', 'wc_point_of_sale'),
                'id' => 'wc_pos_show_stock',
                'type' => 'checkbox',
                'desc' => __('Enable stock quantity identifier', 'wc_point_of_sale'),
                'desc_tip' => __('Shows the remaining stock when adding products to the basket.', 'wc_point_of_sale'),
                'default' => 'yes',
                'autoload' => true
            ),
            array(
                'name' => __('Out of Stock', 'wc_point_of_sale'),
                'id' => 'wc_pos_show_out_of_stock_products',
                'type' => 'checkbox',
                'desc' => __('Enable out of stock products', 'wc_point_of_sale'),
                'desc_tip' => __('Shows out of stock products in the product grid.', 'wc_point_of_sale'),
                'default' => 'yes',
                'autoload' => true
            ),
            array(
                'title' => __('Bill Screen', 'wc_point_of_sale'),
                'desc' => __('Display bill screen', 'wc_point_of_sale'),
                'desc_tip' => __('Allows you to display the order on a separate display i.e. pole display.', 'wc_point_of_sale'),
                'id' => 'wc_pos_bill_screen',
                'default' => 'no',
                'type' => 'checkbox',
                'checkboxgroup' => 'start',
            ),
            array(
                'title' => __('Product Visiblity', 'wc_point_of_sale'),
                'desc' => __('Enable product visibility control', 'wc_point_of_sale'),
                'desc_tip' => __('Allows you to show and hide products from either the POS, web or both shops.', 'wc_point_of_sale'),
                'id' => 'wc_pos_visibility',
                'default' => 'no',
                'type' => 'checkbox',
                'checkboxgroup' => 'start',
            ),

			array( 'type' => 'sectionend', 'id' => 'checkout_pos_options'),
			
					
		)); // End general settings

	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		WC_POS_Admin_Settings::save_fields( $settings );
	}

}

endif;

return new WC_POS_Admin_Settings_Register();