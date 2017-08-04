<?php

/**
 * Plugin Name: AmoCrm for Woocommerce
 * Plugin URI: https://euroroaming.ru
 * Description: AmoCrm for Woocommerce
 * Version: 1.0.0
 * Author: Evgeny Egorov
 * Author URI: https://euroroaming.ru
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: euroroaming
 */

if (!defined('WPINC')) {
	die;
}
require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . '/amocrm.phar';

/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

	function is_category_sim_karty($order_id)
	{
		//Получение заказа
		$order = new WC_Order($order_id);

		//Проверка заказа на категорию товара
		$items = $order->get_items();
		$product_id = 0;
		foreach ($items as $item) {
			$product_id = $item['product_id'];
			break;
		}
		$product_cats = get_the_terms($product_id, 'product_cat');
		$i_sim = 0;
		foreach ($product_cats as $product_cat) {
			if ($product_cat->slug == 'sim-karty')
				$i_sim++;
		}
		//если категория не 'сим-карты' выходим
		if ($i_sim === 0)
			return false;
		else return true;
	}

	function amocrm_contact()
	{

	}

	function amocrm_create_order($order_id)
	{

		//если категория не 'сим-карты' выходим
		if (!is_category_sim_karty($order_id)) return;


//        $myfile = fopen("processing-".$order_id.".txt", "w") or die("Unable to open file!");
//        file_put_contents("processing-".$order_id.".txt", $product_cat); //get_post_meta($order_id,'_billing_phone', true)
//        file_put_contents("processing-".$order_id.".txt", print_r($order_by,  true));
//        error_log( "Payment has been received for order". print_r($items) );

		try {
			$amo = new \AmoCRM\Client('new5909c3a25d8be', 'it@sgsim.ru', 'adc424c3655952ffe453b387c5a9cdfc');

			$order = new WC_Order($order_id);//Получение объекта заказа
			$items = $order->get_items();//Получение товаров заказа
			$ordet_total = $order->get_total();//Стоимость заказ

			//получаем способ доставки
			$shipping_method = @array_shift($order->get_shipping_methods());
			$shipping_method_id = $shipping_method['method_id'];

			//проверяем оформлен заказ на сайте или в ТА (Если в ТА, то ответ POS)
			$order_by = get_post_meta($order_id, '_created_via', true);

			//$myfile = fopen("processing-".$order_id.".txt", "w") or die("Unable to open file!");
			//file_put_contents("processing-".$order_id.".txt", $order_by); //get_post_meta($order_id,'_billing_phone', true)

			$lead = $amo->lead;//Создаем сделку
			//$lead->debug(true); // Режим отладки
			$lead['name'] = 'Заказ #' . $order_id;
			$lead['tags'] = 'Заказ';


			switch ($order->get_status()) {
				case 'processing':
					$lead['status_id'] = 15447709;
					break;
				case 'on-hold':
					$lead['status_id'] = 15447712;
					break;
				case 'point-of-sale':
					$lead['status_id'] = 15447688;
					break;
			}


			$lead['price'] = $ordet_total;
			//$lead['responsible_user_id'] = 1468330; //Ответственный менеджер
			//$lead['visitor_uid'] = '12345678-52d2-44c2-9e16-ba0052d9f6d6';

			if ($order_by == 'POS') {
				$lead->addCustomField(187241, 404987); //Статус доставки ТА
			}
			if ($shipping_method_id == 'local_pickup_plus') {
				$lead->addCustomField(187241, 404983); //Статус доставки Выдача
			}
			if ($shipping_method_id == 'flat_rate:1') {
				$lead->addCustomField(187241, 404985); //Статус доставки Почта РФ
			}
			if ($shipping_method_id != 'local_pickup_plus' && $shipping_method_id != 'flat_rate:1' && $order_by != 'POS') {
				$lead->addCustomField(187241, 404981); //Статус доставки Курьерка
			}


			$operators_array = array(
				'orange' => 0,
				'vodafone' => 0,
				'ortel' => 0,
				'gs_classic' => 0,
				'gs_internet' => 0,
				'gs_usa' => 0,
				'europasim' => 0,
				'travelchat' => 0,
				'three' => 0,
			);


			foreach ($items as $item) {
				switch ($item['product_id']) {
					case 18402: //orange
						$operators_array['orange'] += $item['qty'];
						break;
					case 18438: //vodafone
						$operators_array['vodafone'] += $item['qty'];
						break;
					case 18446: //ortel
						$operators_array['ortel'] += $item['qty'];
						break;
					case 18455: //GLOBALSIM «Classic»
						$operators_array['gs_classic'] += $item['qty'];
						break;
					case 18453: //GLOBALSIM «Internet»
						$operators_array['gs_internet'] += $item['qty'];
						break;
					case 48067: //GLOBALSIM с тарифом «США»
						$operators_array['gs_usa'] += $item['qty'];
						break;
					case 28328: //EuropaSim
						$operators_array['europasim'] += $item['qty'];
						break;
					case 41120: //TravelChat
						$operators_array['travelchat'] += $item['qty'];
						break;
					case 55050: //Three
						$operators_array['three'] += $item['qty'];
						break;
				}
			}

			foreach ($operators_array as $key => $value) {

				if ($value == 0) continue;

				switch ($key) {
					case 'orange': //orange
						$lead->addCustomField(187211, $value);
						break;
					case 'vodafone': //vodafone
						$lead->addCustomField(187225, $value);
						break;
					case 'ortel': //ortel
						$lead->addCustomField(187227, $value);
						break;
					case 'gs_classic': //GLOBALSIM «Classic»
						$lead->addCustomField(187229, $value);
						break;
					case 'gs_internet': //GLOBALSIM «Internet»
						$lead->addCustomField(187231, $value);
						break;
					case 'gs_usa': //GLOBALSIM с тарифом «США»
						$lead->addCustomField(187233, $value);
						break;
					case 'europasim': //EuropaSim
						$lead->addCustomField(187237, $value);
						break;
					case 'travelchat': //TravelChat
						$lead->addCustomField(187239, $value);
						break;
					case 'three': //Three
						$lead->addCustomField(187235, $value);
						break;
				}
			}


			$id_lead = $lead->apiAdd();

			$client_name = get_post_meta($order_id, '_billing_first_name', true);
			$client_last_name = get_post_meta($order_id, '_billing_last_name', true);
			$client_email = get_post_meta($order_id, '_billing_email', true);
			$client_phone = get_post_meta($order_id, '_billing_phone', true);

			if ($order_by == 'POS') {
				$client_email = get_post_meta($order_id, 'client_email', true);
				$client_phone = get_post_meta($order_id, 'client_phone', true);
			}

			$contact = $amo->contact;

			$contacts_list = $amo->contact->apiList([
				'query' => $client_phone,
			]);

			//Если контакта нет в Амо - создаем без ответственного и привязываем заказ
			if (!$contacts_list) {
				$contact['name'] = $client_name . ' ' . $client_last_name;
				$contact->addCustomField(57524, [
					[$client_email, 'PRIV'],
				]);
				$contact->addCustomField(57522, [
					[$client_phone, 'MOB'],
				]);
				$contact['linked_leads_id'] = $id_lead;
				$contact['tags'] = 'Заказ';
				$id_contact = $contact->apiAdd();
			} else { //Контакт есть. Берем самый первый контакт и привязывает к сделке. У сделки обновляем ответственного.

				$sorted_contacts = array();
				$contacts_responsible_users_ids = array();
				$linked_leads_ids = array();
				foreach ($contacts_list as $contact){
					$sorted_contacts[$contact['id']] = $contact['date_create'];
					$contacts_responsible_users_ids[$contact['id']] = $contact['responsible_user_id'];
					$linked_leads_ids[$contact['id']] = $contact['linked_leads_id'];
				}

				$contact_id = array_keys($sorted_contacts, min($sorted_contacts))[0];
				$contact_responsible_user_id = $contacts_responsible_users_ids[$contact_id];
				$linked_leads_id = $linked_leads_ids[$contact_id];

				$contact = $amo->contact;

				if (count($linked_leads_id) == 0) {
					$contact['linked_leads_id'] = $id_lead;
				} else {
					array_push($linked_leads_id, $id_lead);
					$contact['linked_leads_id'] = $linked_leads_id;
				}

				//$order_by = get_post_meta($order_id, '_created_via', true);
				//file_put_contents($_SERVER['DOCUMENT_ROOT']."/logs/amocrm-order_by-$order_by-" . $order_id . ".txt", print_r((int)$contact_id, true)."\r\n", FILE_APPEND | LOCK_EX);
				//file_put_contents($_SERVER['DOCUMENT_ROOT']."/logs/amocrm-order_by-$order_by-" . $order_id . ".txt", print_r($contact_responsible_user_id, true)."\r\n", FILE_APPEND | LOCK_EX);
				//file_put_contents($_SERVER['DOCUMENT_ROOT']."/logs/amocrm-order_by-$order_by-" . $order_id . ".txt", print_r($contact['linked_leads_id'], true)."\r\n", FILE_APPEND | LOCK_EX);

				$contact->apiUpdate((int)$contact_id, 'now');

				$lead = $amo->lead;
				//$lead->debug(true); // Режим отладки
				$lead['responsible_user_id'] = (int)$contact_responsible_user_id;
				sleep(1);
				$lead->apiUpdate((int)$id_lead, 'now');
			}

		} catch (\AmoCRM\Exception $e) {
			error_log('Error (%d): %s', $e->getCode(), $e->getMessage());

		}
	}

	function woocommerce_order_statuses($order_id)
	{
		try {
			$amo = new \AmoCRM\Client('new5909c3a25d8be', 'it@sgsim.ru', 'adc424c3655952ffe453b387c5a9cdfc');

			//Проверяем существует ли заказ в Амо
			$lead_is_create = $amo->lead->apiList([
				'query' => 'Заказ #' . $order_id,
			]);

			//Заказа нет в Амо
			if (!$lead_is_create) {
				amocrm_create_order($order_id);
				return;
			}

			//$myfile = fopen("processing-" . $order_id . ".txt", "w") or die("Unable to open file!");
			//file_put_contents("processing-" . $order_id . ".txt", print_r($lead_is_create, true)); //get_post_meta($order_id,'_billing_phone', true)


			//Заказ есть в Амо
			$order = new WC_Order($order_id);//Получение объекта заказа
			$lead = $amo->lead;//Создаем сделку

			switch ($order->get_status()) {
				case 'processing':
					$lead['status_id'] = 15447709;
					break;
				case 'order-issued':
					$lead['status_id'] = 15447703;
					break;
				case 'on-hold':
					$lead['status_id'] = 15447712;
					break;
				case 'waiting-delivery':
					$lead['status_id'] = 14805337;
					break;
				case 'point-of-sale':
					$lead['status_id'] = 15447688;
					break;
				case 'waiting-for-passp':
					$lead['status_id'] = 15447700;
					break;
				case 'activating-by-dat':
					$lead['status_id'] = 14805340;
					break;
				case 'pending-activatio':
					$lead['status_id'] = 15447691;
					break;
				case 'orange-completed':
					$lead['status_id'] = 15447697;
					break;
				case 'ortel-completed':
					$lead['status_id'] = 14805331;
					break;
				case 'vodafone-complete':
					$lead['status_id'] = 15447694;
					break;
				case 'instructions-comp':
					$lead['status_id'] = 15447706;
					break;
				case 'balance-refilled':
					$lead['status_id'] = 14805334;
					break;
				case 'completed':
					$lead['status_id'] = 15447715;
					break;
				case 'cancelled':
					$lead['status_id'] = 15447718;
					break;
				case 'refunded':
					$lead['status_id'] = 15495493;
					break;
			}

			$lead->apiUpdate((int)$lead_is_create[0]['id'], 'now');


		} catch (\AmoCRM\Exception $e) {
			error_log('Error (%d): %s', $e->getCode(), $e->getMessage());
		}

	}

	add_action('woocommerce_order_status_processing', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_order-issued', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_on-hold', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_waiting-delivery', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_point-of-sale', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_waiting-for-passp', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_activating-by-dat', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_pending-activatio', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_orange-completed', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_ortel-completed', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_vodafone-complete', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_instructions-comp', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_balance-refilled', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_completed', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_cancelled', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_order_status_refunded', 'woocommerce_order_statuses', 10, 1);
	add_action('woocommerce_payment_complete', 'woocommerce_order_statuses', 10, 1);

	add_action('gform_after_submission_5', 'post_to_third_party', 10, 2);
	function post_to_third_party($entry, $form)
	{

		$first_name = rgar($entry, '3');
		$phone = rgar($entry, '1');
		$phone_bu = '7'.preg_replace('/\D/', '', $phone);
		$page_url = rgar($entry, 'source_url');
		$client_ip = rgar($entry, 'ip');

		/*$myfile = fopen("processing-011.txt", "w") or die("Unable to open file!");
		file_put_contents("processing-011.txt", '123'); //get_post_meta($order_id,'_billing_phone', true)*/
		$date = date(DATE_RFC822);
		$timestamp = strtotime($date);

		try {

			$amo = new \AmoCRM\Client('new5909c3a25d8be', 'it@sgsim.ru', 'adc424c3655952ffe453b387c5a9cdfc');
			$unsorted = $amo->unsorted;
			$unsorted['source'] = 'www.my-awesome-site.com';
			$unsorted['source_uid'] = null;
			$unsorted['source_data'] = [
				'data' => [
					'name_1' => [
						'type' => 'text',
						'id' => 'name',
						'element_type' => '1',
						'name' => $first_name,
						'value' => $phone_bu,
					]
				],
				'form_id' => 1,
				'form_type' => 1,
				'origin' => [
					'ip' => strval($client_ip),
				],
				'date' => $timestamp,
				'from' => 'Заказ обратного звонка с сайта Евророуминг',
				'form_name' => 'Заказ обратного звонка с сайта Евророуминг',
			];
			// Добавление сделки которая будет создана после одобрения заявки.
			$lead = $amo->lead;
			$lead['name'] = 'Заказ обратного звонка от '.$phone_bu;
			$lead['tags'] = 'Заявка с сайта Евророуминг';
			$unsorted->addDataLead($lead);

			$contact = $amo->contact;
			$contact['name'] = $first_name;
			$contact->addCustomField(57522, [
				[$phone_bu, 'MOB'],
			]);
			$contact['tags'] = 'Заявка с сайта Евророуминг';

			$note = $amo->note;
			$note['text'] = 'Страница формы: '.strval($page_url);
			$note['note_type'] = 4;
			$note['element_type'] = 1;
			$contact['notes'] = $note;
			$unsorted->addDataContact($contact);

			// Добавление неразобранных заявок с типом FORMS
			$unsortedId = $unsorted->apiAddForms();

			//$myfile = fopen("processing-01.txt", "w") or die("Unable to open file!");
			//file_put_contents("processing-01.txt", $unsortedId); //get_post_meta($order_id,'_billing_phone', true)

			//print_r('test111');

		} catch (\AmoCRM\Exception $e) {
			//print_r('error1111');
			//$myfile = fopen("processing-01.txt", "w") or die("Unable to open file!");
			//file_put_contents("processing-01.txt", 'Error (%d): %s', $e->getCode(), $e->getMessage()); //get_post_meta($order_id,'_billing_phone', true)
			error_log('Error (%d): %s', $e->getCode(), $e->getMessage());
		}



	}
}