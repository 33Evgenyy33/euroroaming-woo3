<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 *
 * @package    Woo_Checkout_For_Digital_Goods
 * @subpackage Woo_Checkout_For_Digital_Goods/public
 */
class Woo_Checkout_For_Digital_Goods_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-checkout-for-digital-goods-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-checkout-for-digital-goods-public.js', array('jquery'), $this->version, false);
    }

    /**
     * Function for remove checkout fields.
     */
    public function custom_override_checkout_fields($fields) {

        global $woocommerce,$product;
        $woo_checkout_field_array = get_option('wcdg_checkout_fields');

        $items = WC()->cart->get_cart();

	    /*********Удаление поля Желаемая дата активации, если в корзине нет Orange***********/
	    $is_orange = false;
	    foreach ($items as $cart_item_key => $values) {
		    $product_id = apply_filters('woocommerce_cart_item_product_id', $values['product_id'], $values, $cart_item_key);
		    if ($product_id == 58961){
			    $is_orange = true;
			    break;
		    }
	    }
	    if (!$is_orange){
		    unset($fields['billing']['date_activ']);
	    }
	    /*********************************-*************************************************/

	    foreach ($items as $cart_item_key => $values) {
		    $product_id = apply_filters('woocommerce_cart_item_product_id', $values['product_id'], $values, $cart_item_key);
		    if ($product_id == 59102) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['passport']);
			    unset($fields['billing']['activation_conditions']);
			    unset($fields['billing']['internet_pass_num']);
			    unset($fields['billing']['orage_num']);
			    unset($fields['billing']['date_activ_orange_visa']);
			    unset($fields['billing']['pointofsale_email']);
			    unset($fields['billing']['number_simcard']);
			    unset($fields['billing']['client_phone']);
			    unset($fields['billing']['client_email']);
			    unset($fields['billing']['date_activ']);

			    echo '<style>.col-2{display: none}.woocommerce-message{display: none}.woocommerce-info{display: none}div#wc_checkout_add_ons{display: none;}</style>';

			    return $fields;
		    }
	    }

         //return the regular billing fields if we need shipping fields
        if ($woocommerce->cart->needs_shipping()) {
	        unset($fields['billing']['orange_replenishment']);
	        unset($fields['billing']['pin_code_recovery']);
	        unset($fields['billing']['vodafone_replenishment']);
	        unset($fields['billing']['ortel_replenishment']);
	        unset($fields['billing']['pointofsale_email']);
	        unset($fields['billing']['number_simcard']);
	        unset($fields['billing']['orage_num']);
	        unset($fields['billing']['internet_pass_num']);
	        unset($fields['billing']['recovery_vodafone']);
	        unset($fields['billing']['date_activ_orange_visa']);
	        unset($fields['billing']['client_email']);

	        unset($fields['billing']['client_phone']);
            return $fields;
        }

        $temp_product = array();

        $temp_product_flag = 1;
        // basic checks

        foreach ($items as $cart_item_key => $values) {
            $_product = $values['data'];
            $product_id = apply_filters('woocommerce_cart_item_product_id', $values['product_id'], $values, $cart_item_key);

            if ($_product->is_virtual() || $_product->is_downloadable()) {
                $temp_product_flag = 0;
            }else{
                $temp_product[] = $product_id;
            }
        }

	    if (WC()->cart->get_cart_contents_count() == 0) {

		    unset($fields['billing']['billing_company']);
		    unset($fields['billing']['billing_address_1']);
		    unset($fields['billing']['billing_address_2']);
		    unset($fields['billing']['billing_city']);
		    unset($fields['billing']['billing_postcode']);
		    unset($fields['billing']['billing_country']);
		    unset($fields['billing']['billing_state']);
		    unset($fields['order']['order_comments']);
		    unset($fields['billing']['billing_address_2']);
		    unset($fields['billing']['billing_postcode']);
		    unset($fields['billing']['billing_company']);
		    unset($fields['billing']['billing_city']);
		    unset($fields['billing']['passport']);
		    unset($fields['billing']['activation_conditions']);
		    unset($fields['billing']['orage_num']);
		    unset($fields['billing']['internet_pass_num']);
		    unset($fields['billing']['recovery_vodafone']);
		    unset($fields['billing']['date_activ_orange_visa']);
		    unset($fields['billing']['orange_replenishment']);
		    unset($fields['billing']['pin_code_recovery']);
		    unset($fields['billing']['vodafone_replenishment']);
		    unset($fields['billing']['ortel_replenishment']);

		    return $fields;

	    }

        if (!empty($temp_product)) {
	        unset($fields['billing']['orange_replenishment']);
	        unset($fields['billing']['pin_code_recovery']);
	        unset($fields['billing']['vodafone_replenishment']);
	        unset($fields['billing']['ortel_replenishment']);
	        unset($fields['billing']['pointofsale_email']);
	        unset($fields['billing']['number_simcard']);
	        unset($fields['billing']['orage_num']);
	        unset($fields['billing']['internet_pass_num']);
	        unset($fields['billing']['recovery_vodafone']);
	        unset($fields['billing']['date_activ_orange_visa']);
	        unset($fields['billing']['client_email']);

	        unset($fields['billing']['client_phone']);

	        return $fields;
        }

	    foreach ($items as $cart_item_key => $values) {
		    $_product = $values['data'];
		    $product_id = apply_filters('woocommerce_cart_item_product_id', $values['product_id'], $values, $cart_item_key);

		    if ($product_id == 23575) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_address_1']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_city']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_country']);
			    unset($fields['billing']['billing_state']);
			    unset($fields['order']['order_comments']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_city']);
			    unset($fields['billing']['passport']);
			    unset($fields['billing']['activation_conditions']);
			    unset($fields['billing']['internet_pass_num']);
			    unset($fields['billing']['recovery_vodafone']);
			    unset($fields['billing']['pointofsale_email']);
			    unset($fields['billing']['number_simcard']);
			    unset($fields['billing']['client_email']);

			    unset($fields['billing']['client_phone']);
			    unset($fields['billing']['date_activ']);

			    echo '<style>.col-2{display: none}.woocommerce-message{display: none}.woocommerce-info{display: none}</style>';

			    return $fields;

		    }

		    if ($product_id == 22325) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_address_1']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_city']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_country']);
			    unset($fields['billing']['billing_state']);
			    unset($fields['order']['order_comments']);
			    unset($fields['billing']['billing_address_2']);
			    unset($fields['billing']['billing_postcode']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['billing_city']);
			    unset($fields['billing']['passport']);
			    unset($fields['billing']['activation_conditions']);
			    unset($fields['billing']['recovery_vodafone']);
			    unset($fields['billing']['orage_num']);
			    unset($fields['billing']['date_activ_orange_visa']);
			    unset($fields['billing']['pointofsale_email']);
			    unset($fields['billing']['number_simcard']);
			    unset($fields['billing']['client_email']);

			    unset($fields['billing']['client_phone']);
			    unset($fields['billing']['date_activ']);

			    echo '<style>.col-2{display: none}.woocommerce-message{display: none}.woocommerce-info{display: none}div#wc_checkout_add_ons{display: none;}</style>';

			    return $fields;

		    }

		    if ($product_id == 59102) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['billing_company']);
			    unset($fields['billing']['passport']);
			    unset($fields['billing']['activation_conditions']);
			    unset($fields['billing']['internet_pass_num']);
			    unset($fields['billing']['orage_num']);
			    unset($fields['billing']['date_activ_orange_visa']);
			    unset($fields['billing']['pointofsale_email']);
			    unset($fields['billing']['number_simcard']);
			    unset($fields['billing']['client_email']);

			    unset($fields['billing']['client_phone']);
			    unset($fields['billing']['date_activ']);

			    echo '<style>.col-2{display: none}.woocommerce-message{display: none}.woocommerce-info{display: none}div#wc_checkout_add_ons{display: none;}</style>';

			    return $fields;

		    }

		    if ($product_id == 59058) {

			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);

		    }

		    if ($product_id == 59139) {

			    unset($fields['billing']['vodafone_replenishment']);
			    unset($fields['billing']['ortel_replenishment']);
			    unset($fields['billing']['orange_replenishment']);

		    }

		    if ($product_id == 59056) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['ortel_replenishment']);

		    }

		    if ($product_id == 59059) {

			    unset($fields['billing']['orange_replenishment']);
			    unset($fields['billing']['pin_code_recovery']);
			    unset($fields['billing']['vodafone_replenishment']);

		    }

		    unset($fields['billing']['billing_company']);
		    unset($fields['billing']['billing_address_1']);
		    unset($fields['billing']['billing_address_2']);
		    unset($fields['billing']['billing_city']);
		    unset($fields['billing']['billing_postcode']);
		    unset($fields['billing']['billing_country']);
		    unset($fields['billing']['billing_state']);
		    unset($fields['order']['order_comments']);
		    unset($fields['billing']['billing_address_2']);
		    unset($fields['billing']['billing_postcode']);
		    unset($fields['billing']['billing_company']);
		    unset($fields['billing']['billing_city']);
		    unset($fields['billing']['passport']);
		    unset($fields['billing']['activation_conditions']);
		    unset($fields['billing']['pointofsale_email']);
		    unset($fields['billing']['number_simcard']);
		    unset($fields['billing']['orage_num']);
		    unset($fields['billing']['internet_pass_num']);
		    unset($fields['billing']['recovery_vodafone']);
		    unset($fields['billing']['date_activ_orange_visa']);
		    unset($fields['billing']['client_email']);

		    unset($fields['billing']['client_phone']);
		    unset($fields['billing']['date_activ']);
		    echo '<style>#wc_checkout_add_ons{display: none;}.input-file-plupload{display: none;}.col-2{display: none}.woocommerce-message{display: none}.woocommerce-info{display: none}</style>';

		    return $fields;
	    }


        return $fields;
    }

    /**
     * BN code added
     */
    function paypal_bn_code_filter_woo_checkout_field($paypal_args) {
        $paypal_args['bn'] = 'Multidots_SP';
        return $paypal_args;
    }

}
