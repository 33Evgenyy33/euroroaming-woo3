<?php
/*
Plugin Name: WooCommerce POS Customer Pay Paytravel
Plugin URI: http://wordpress.org/plugins/woocommerce-pos-customer-pay-paytravel/
Description: Оплаты заказа клиентом самостоятельно через Pay.Travel.
Version: 1.0.0
Author: Evgeny Egorov.
Text Domain: woo-pos-customer-pay-paytravel
Author URI: https://euroroaming.ru/
*/

require_once __DIR__ . '\vendor\autoload.php';


// Cron
// Регистрируем расписание при активации плагина
register_activation_hook( __FILE__, 'activation_geting_course_dollar' );
function activation_geting_course_dollar() {
	wp_clear_scheduled_hook( 'geting_course_dollar' );
	wp_schedule_event( time(), 'one_minute', 'geting_course_dollar' );
}

// Удаляем расписание при деактивации плагина
register_deactivation_hook( __FILE__, 'deactivation_geting_course_dollar' );
function deactivation_geting_course_dollar() {
	wp_clear_scheduled_hook( 'geting_course_dollar' );
}

// Проверка существования расписания во время работы плагина на всякий пожарный случай
if ( ! wp_next_scheduled( 'geting_course_dollar' ) ) {
	wp_schedule_event( time(), 'one_minute', 'geting_course_dollar' );
}

// Хук и функция, которая будет выполняться по Крону
add_action( 'geting_course_dollar', 'get_real_course_dollar' );
function get_real_course_dollar() {
	$mailbox = new PhpImap\Mailbox( '{mail.sgsim.ru:993/imap/ssl/novalidate-cert}INBOX', 'paytravel@euroroaming.ru', 'p44T94^3!', null );

	$date = date( "j F Y", strtotime( "-5 day" ) );
	// Read all messaged into an array:
	$mailsIds = $mailbox->searchMailbox( 'UNSEEN SINCE ' . '"' . $date . '"' );
	if ( ! $mailsIds ) {
		return;
	}

	foreach ($mailsIds as $mailid){
		// Get the first message and save its attachment(s) to disk:
		$mail = $mailbox->getMail( $mailid );

		$mail_subject = $mail->subject;

		if (strpos($mail_subject, 'registry') !== false) continue;

		$mail_text = $mail->textPlain;

		$mail_array = explode( ';', $mail_text );
		$order_id   = $mail_array[10];

		$order = wc_get_order($order_id);

		if ($order->get_status() == 'pending'){
			$order->update_status('processing', 'оплата через PayTravel поддтверждена');
		}
	}

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
 *
 * @param array $gateways all available WC gateways
 *
 * @return array $gateways all WC gateways + offline gateway
 */
add_filter( 'woocommerce_payment_gateways', 'pos_customer_pay_paytravel_gateway' );
function pos_customer_pay_paytravel_gateway( $methods ) {
	$methods[] = 'pos_customer_pay_paytravel';

	return $methods;
}

/**
 * Adds plugin page links
 *
 * @since 1.0.0
 *
 * @param array $links all plugin links
 *
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pos_customer_pay_paytravel_links' );
function pos_customer_pay_paytravel_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=pos_customer_pay_paytravel' ) . '">Settings</a>',
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
add_action( 'plugins_loaded', 'pos_customer_pay_paytravel_init', 11 );

function pos_customer_pay_paytravel_init() {
	class Pos_Customer_Pay_Paytravel extends WC_Payment_Gateway {


		public function __construct() {
			$this->id                 = 'pos_customer_pay_paytravel';
			$this->method_title       = 'Оплачивает клиент через PayTravel';
			$this->method_description = 'Способ оплаты, при котором клиент получает на свой телефон смс с номером заказа для самостоятельной оплаты через терминал Pay.Travel';
			$this->icon               = plugins_url() . '/woocommerce-pos-customer-pay-paytravel/7-min.png';
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
					'default'  => 'pos_customer_pay_paytravel',
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
					'desc_tip' => 'Company id of pos_customer_pay_paytravel',
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

			$key_customer_name    = '_billing_first_name';
			$order_customer_name  = str_replace( ' ', '', get_post_meta( $order_id, $key_customer_name, true ) );
			$order_customer_phone = str_replace( ' ', '', get_post_meta( $order_id, 'client_phone', true ) );
			$order_message        = 'Для оплаты заказа #' . $order_id . '. воспользуйтесь терминалом PayTravel';

			echo $this->send( "gate.iqsms.ru", 80, "z1496927079417", "340467",
				$order_customer_phone, $order_message, "Euroroaming" );

			$order_client_email = get_post_meta( $order_id, 'client_email', true );
			$headers = 'MIME-Version: 1.0' . "\r\n";
			//Отправляем сообщение на почту
			wp_mail($order_client_email, $order_message, $order_message, $headers);


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