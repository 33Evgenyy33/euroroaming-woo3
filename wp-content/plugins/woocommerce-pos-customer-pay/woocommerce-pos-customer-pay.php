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

//register_activation_hook( __FILE__, array( 'MyPlugin', 'install' ) );
//
//class MyPlugin {
//	static function install() {
//		file_put_contents("hook-test.txt", print_r('test', true), FILE_APPEND | LOCK_EX);
//	}
//}


// Cron
// Регистрируем расписание при активации плагина
register_activation_hook(__FILE__, 'activation_geting_course_dollar');
function activation_geting_course_dollar() {
	wp_clear_scheduled_hook( 'geting_course_dollar' );
	wp_schedule_event( time(), 'one_minute', 'geting_course_dollar');
}

// Удаляем расписание при деактивации плагина
register_deactivation_hook( __FILE__, 'deactivation_geting_course_dollar');
function deactivation_geting_course_dollar() {
	wp_clear_scheduled_hook('geting_course_dollar');
}

// Проверка существования расписания во время работы плагина на всякий пожарный случай
if( ! wp_next_scheduled( 'geting_course_dollar' ) ) {
	wp_schedule_event( time(), 'one_minute', 'geting_course_dollar');
}

// Хук и функция, которая будет выполняться по Крону
add_action( 'geting_course_dollar', 'get_real_course_dollar' );
function get_real_course_dollar(){
	file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\cron-test.txt", print_r( time(), true )."\r\n", FILE_APPEND | LOCK_EX );
}


defined( 'ABSPATH' ) or exit;
// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}
/**
 * Add the gateway to WC Available Gateways
 *
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
add_filter( 'woocommerce_payment_gateways', 'pos_customer_pay_gateway' );
function pos_customer_pay_gateway( $methods ) {
	$methods[] = 'pos_customer_pay';

	return $methods;
}

/**
 * Adds plugin page links
 *
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pos_customer_pay_links' );
function pos_customer_pay_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pos_customer_pay' ) . '">Settings</a>',
	);

	return array_merge( $plugin_links, $links );
}

/**
 * @property string id
 * @property string method_title
 * @property string method_description
 * @property string icon
 * @property bool has_fields
 * @property array supports
 */
add_action( 'plugins_loaded', 'pos_customer_pay_init', 11 );

function pos_customer_pay_init() {
	class Pos_Customer_Pay extends WC_Payment_Gateway {


		public function __construct() {
			$this->id                 = 'pos_customer_pay';
			$this->method_title       = 'Оплачивает клиент (с чеком)';
			$this->method_description = 'Способ оплаты, при котором клиент получает на свой телефон смс со ссылкой для самостоятельной оплаты заказа';
			$this->icon               = plugins_url() . '/woocommerce-pos-customer-pay/7-min.png';
			$this->has_fields         = false;

			$this->init_form_fields();
			$this->init_settings();

			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
					$this,
					'process_admin_options'
				) );
			}

			foreach ( $this->settings as $setting_key => $value ) {
				$this->$setting_key = $value;
			}
		}

		// Build the administration fields for this specific Gateway
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => 'Enable / Disable',
					'label'   => 'Enable this payment gateway',
					'type'    => 'checkbox',
					'default' => 'no',
				),
				'title'       => array(
					'title'    => 'Title',
					'type'     => 'text',
					'desc_tip' => 'Payment title the customer will see during the checkout process.',
					'default'  => 'pos_customer_pay',
				),
				'description' => array(
					'title'    => 'Description',
					'type'     => 'textarea',
					'desc_tip' => 'Payment description the customer will see during the checkout process.',
					'css'      => 'max-width:350px;'
				),

				'company_id' => array(
					'title'    => 'Company Id',
					'type'     => 'text',
					'desc_tip' => 'Company id of pos_customer_pay',
				),

				'environment' => array(
					'title'       => 'Test Mode',
					'label'       => 'Enable Test Mode',
					'type'        => 'checkbox',
					'description' => 'Place the payment gateway in test mode.',
					'default'     => 'no',
				)
			);
		}


		public function process_payment( $order_id ) {
			global $woocommerce;
			$order = new WC_Order( $order_id );
			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status( 'pending', 'Ожидание оплаты от клиента' );

			file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\yandex-order_by111.txt", print_r( $order->get_checkout_payment_url(), true ) . "\r\n", FILE_APPEND | LOCK_EX );

			$checkout_url = $order->get_checkout_payment_url();

			// Getting all WC_emails objects
			$email_notifications = WC()->mailer()->get_emails();


			$email_notifications['WC_Email_Customer_Invoice']->trigger( $order_id );

//			echo  $this->send("gate.iqsms.ru", 80, "z1496927079417", "340467",
//				"79656314108", $checkout_url, "Euroroaming");


			// Reduce stock levels
			wc_reduce_stock_levels( $order->get_id() );

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thankyou redirect
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);
		}

		public function send( $host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false ) {
			$fp = fsockopen( $host, $port, $errno, $errstr );
			if ( ! $fp ) {
				return "errno: $errno \nerrstr: $errstr\n";
			}
			fwrite( $fp, "GET /send/" .
			             "?phone=" . rawurlencode( $phone ) .
			             "&text=" . rawurlencode( $text ) .
			             ( $sender ? "&sender=" . rawurlencode( $sender ) : "" ) .
			             ( $wapurl ? "&wapurl=" . rawurlencode( $wapurl ) : "" ) .
			             "  HTTP/1.0\n" );
			fwrite( $fp, "Host: " . $host . "\r\n" );
			if ( $login != "" ) {
				fwrite( $fp, "Authorization: Basic " .
				             base64_encode( $login . ":" . $password ) . "\n" );
			}
			fwrite( $fp, "\n" );
			$response = "";
			while ( ! feof( $fp ) ) {
				$response .= fread( $fp, 1 );
			}
			fclose( $fp );
			list( $other, $responseBody ) = explode( "\r\n\r\n", $response, 2 );

			return $responseBody;
		}


	}
}


?>