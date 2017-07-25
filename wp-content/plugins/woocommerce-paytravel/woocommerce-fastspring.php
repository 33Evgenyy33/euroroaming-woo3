<?php
/*
Plugin Name: WooCommerce PayTravel
Plugin URI: http://wordpress.org/plugins/woocommerce-paytravel/
Description: Платежный шлюз Pay.Travel.
Version: 1.0.0
Author: Deligence Technologies Pvt Ltd.
Text Domain: woo-paytravel
Author URI: https://euroroaming.ru/
*/
add_action('plugins_loaded', 'init_paytravel_gateway', 0);

function init_paytravel_gateway() {
	
	if ( ! class_exists( 'WC_Payment_Gateway' )) { return; }
	
	
	class PayTravel extends WC_Payment_Gateway
	{
		public static $_instance = NULL;	
					
        public $log;
	    
		
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
        
		
		public function __construct() 
		{
		    $this->id			= 'paytravel';
		    $this->method_title = 'PayTravel';
			$this->method_description ='Оплата с помощью терминала Pay.Travel';
		    $this->icon 		= plugins_url().'/woocommerce-paytravel/7-min.png';
		    $this->has_fields 	= false;	
		    $this->supports = array( 
		    	'products', 
		    );
			$this->init_form_fields();
			$this->init_settings();
			
			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	       }

		   foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
			}
		}
		
		// Build the administration fields for this specific Gateway
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title'		=> 'Enable / Disable',
					'label'		=> 'Enable this payment gateway',
					'type'		=> 'checkbox',
					'default'	=> 'no',
				),
				'title' => array(
					'title'		=> 'Title',
					'type'		=> 'text',
					'desc_tip'	=> 'Payment title the customer will see during the checkout process.',
					'default'	=> 'PayTravel',
				),
				'description' => array(
					'title'		=> 'Description',
					'type'		=> 'textarea',
					'desc_tip'	=> 'Payment description the customer will see during the checkout process.',
					'css'		=> 'max-width:350px;'
				),
				
				'company_id' => array(
					'title'		=> 'Company Id',
					'type'		=> 'text',
					'desc_tip'	=> 'Company id of PayTravel',
				),
				
				'environment' => array(
					'title'		=> 'Test Mode',
					'label'		=> 'Enable Test Mode',
					'type'		=> 'checkbox',
					'description' =>'Place the payment gateway in test mode.',
					'default'	=> 'no',
				)
			);		
		}


		public function process_payment( $order_id ) {
			global $woocommerce;
			$order = new WC_Order( $order_id );

			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('on-hold', 'Ожидание оплаты через PayTravel');

			// Reduce stock levels
			$order->reduce_order_stock();

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thankyou redirect
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);
		 
		}
	}
		
		add_filter( 'woocommerce_payment_gateways', 'paytravel_gateway' );
		function paytravel_gateway( $methods ) {
			$methods[] = 'PayTravel';
			return $methods;
		}
}
	
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'paytravel_links' );
		function paytravel_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=paytravel' ) . '">Settings</a>',
			);
		    return array_merge( $plugin_links, $links );	
         }
		 
?>