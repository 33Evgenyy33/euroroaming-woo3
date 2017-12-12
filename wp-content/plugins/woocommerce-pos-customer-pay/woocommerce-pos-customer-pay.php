<?php
/*
Plugin Name: WooCommerce POS Customer Pay
Plugin URI: http://wordpress.org/plugins/woocommerce-pos-customer-pay/
Description: Оплаты заказа клиентом самостоятельно.
Version: 1.0.0
Author: Evgeny Egorov.
Text Domain: woo-pos-customer-pay
Author URI: https://euroroaming.ru/
*/
add_action('plugins_loaded', 'init_pos_customer_pay_gateway', 0);

function init_pos_customer_pay_gateway() {

	if ( ! class_exists( 'WC_Payment_Gateway' )) { return; }


	/**
	 * @property string id
	 * @property string method_title
	 * @property string method_description
	 * @property string icon
	 * @property bool has_fields
	 * @property array supports
	 */
	class Pos_Customer_Pay extends WC_Payment_Gateway
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
			$this->id			= 'pos_customer_pay';
			$this->method_title = 'Оплачивает клиент (с чеком)';
			$this->method_description ='Способ оплаты, при котором клиент получает на свой телефон смс со ссылкой для самостоятельной оплаты заказа';
			$this->icon 		= plugins_url().'/woocommerce-pos-customer-pay/7-min.png';
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
					'default'	=> 'pos_customer_pay',
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
					'desc_tip'	=> 'Company id of pos_customer_pay',
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
			$order->update_status('pending', 'Ожидание оплаты от клиента');

			file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\yandex-order_by111.txt", print_r( $order->get_checkout_payment_url(), true )."\r\n",  FILE_APPEND | LOCK_EX );

			$checkout_url = $order->get_checkout_payment_url();

			// Getting all WC_emails objects
			$email_notifications = WC()->mailer()->get_emails();


			$email_notifications['WC_Email_Customer_Invoice']->trigger( $order_id );

//			echo  $this->send("gate.iqsms.ru", 80, "z1496927079417", "340467",
//				"79656314108", $checkout_url, "Euroroaming");



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

		public function send($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false )
		{
			$fp = fsockopen($host, $port, $errno, $errstr);
			if (!$fp) {
				return "errno: $errno \nerrstr: $errstr\n";
			}
			fwrite($fp, "GET /send/" .
			            "?phone=" . rawurlencode($phone) .
			            "&text=" . rawurlencode($text) .
			            ($sender ? "&sender=" . rawurlencode($sender) : "") .
			            ($wapurl ? "&wapurl=" . rawurlencode($wapurl) : "") .
			            "  HTTP/1.0\n");
			fwrite($fp, "Host: " . $host . "\r\n");
			if ($login != "") {
				fwrite($fp, "Authorization: Basic " .
				            base64_encode($login. ":" . $password) . "\n");
			}
			fwrite($fp, "\n");
			$response = "";
			while(!feof($fp)) {
				$response .= fread($fp, 1);
			}
			fclose($fp);
			list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
			return $responseBody;
		}



	}

	add_filter( 'woocommerce_payment_gateways', 'pos_customer_pay_gateway' );
	function pos_customer_pay_gateway( $methods ) {
		$methods[] = 'pos_customer_pay';
		return $methods;
	}
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pos_customer_pay_links' );
function pos_customer_pay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pos_customer_pay' ) . '">Settings</a>',
	);
	return array_merge( $plugin_links, $links );
}

?>