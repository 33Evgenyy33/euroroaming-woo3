<?php
/*
Plugin Name: WooCommerce clientpay
Plugin URI: http://wordpress.org/plugins/woocommerce-clientpay/
Description: Платежный шлюз clientpay.
Version: 1.0.0
Author: Deligence Technologies Pvt Ltd.
Text Domain: woo-clientpay
Author URI: https://euroroaming.ru/
*/
add_action('plugins_loaded', 'init_clientpay_gateway', 0);

function init_clientpay_gateway() {

	if ( ! class_exists( 'WC_Payment_Gateway' )) { return; }


	/**
	 * @property string id
	 * @property string method_title
	 * @property string method_description
	 * @property string icon
	 * @property bool has_fields
	 * @property array supports
	 */
	class clientpay extends WC_Payment_Gateway
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
			$this->id			= 'clientpay';
			$this->method_title = 'clientpay';
			$this->method_description ='Оплата с помощью терминала clientpay';
			$this->icon 		= plugins_url().'/woocommerce-clientpay/7-min.png';
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
					'default'	=> 'clientpay',
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
					'desc_tip'	=> 'Company id of clientpay',
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
			$order->update_status('pending', 'Ожидание оплаты через clientpay');

			file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\yandex-order_by111.txt", print_r( $order->get_checkout_payment_url(), true ), FILE_APPEND | LOCK_EX );


			// Reduce stock levels
			wc_reduce_stock_levels($order->get_id());

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thankyou redirect
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		}
	}

	add_filter( 'woocommerce_payment_gateways', 'clientpay_gateway' );
	function clientpay_gateway( $methods ) {
		$methods[] = 'clientpay';
		return $methods;
	}
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'clientpay_links' );
function clientpay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=clientpay' ) . '">Settings</a>',
	);
	return array_merge( $plugin_links, $links );
}

?>