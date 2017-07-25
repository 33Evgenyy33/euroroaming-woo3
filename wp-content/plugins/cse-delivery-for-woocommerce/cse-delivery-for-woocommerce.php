<?php

/**
 * Plugin Name: Express Courier Service
 * Plugin URI: https://euroroaming.ru
 * Description: Custom Shipping Method for WooCommerce based on CSE (Express Courier Service)
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

/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function cse_shipping_method()
    {
        if (!class_exists('cse_Shipping_Method')) {
            class cse_Shipping_Method extends WC_Shipping_Method
            {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 */
                public function __construct()
                {
                    $this->id = 'cse_shipping_method';
                    $this->method_title = 'Курьерская служба КСЭ';
                    $this->method_description = 'Метод доставки на основе API CSE';

                    $this->init();

                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Курьером до двери', 'cse');
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));

                }

                public $recipient_data;
                public $calc_response;

                /**
                 * @return mixed
                 */
                function getRecipientData()
                {
                    return $this->recipient_data;
                }

                /**
                 * @param mixed $recipient_data
                 */
                public function setRecipientData($recipient_data)
                {
                    $this->recipient_data = $recipient_data;
                }

                /**
                 * @return mixed
                 */
                public function getCalcResponse()
                {
                    return $this->calc_response;
                }

                /**
                 * @param mixed $calc_response
                 */
                public function setCalcResponse($calc_response)
                {
                    $this->calc_response = $calc_response;
                }

                function soapclient_GetReferenceData($fias = '')
                {
                    $ship_type_request = array(
                        'login' => 'ЕВРОРОУМИНГ-ИМ',
                        'password' => 'gnJkA6GwsLJ6',
                        'parameters' => array(
                            'Key' => 'Parameters',
                            'List' => array(
                                0 => array(
                                    'Key' => 'Reference',
                                    'Value' => 'Geography',
                                    'ValueType' => 'string'
                                ),
                                1 => array(
                                    'Key' => 'Search',
                                    'Value' => $fias,
                                    'ValueType' => 'string'
                                )
                            )
                        )
                    );

                    try {
                        $client = new SoapClient('http://web.cse.ru/1c/ws/Web1C.1cws?wsdl', array(
                            'trace' => true,
                            'features' => SOAP_WAIT_ONE_WAY_CALLS,
                            'cache_wsdl' => WSDL_CACHE_NONE
                        ));
                    } catch (Exception $e) {
                        //die($e->getMessage());
                        return;
                    }

                    try {
                        $this->setRecipientData($client->GetReferenceData($ship_type_request)); //данные получателя (GUID, КЛАДР и т.д.)
                    } catch (Exception $e) {
                        //die($e->getMessage());
                        return;
                    }
                }

                function soapclient_calc($recipient)
                {
                    $calc_request = array(
                        'login' => 'ЕВРОРОУМИНГ-ИМ',
                        'password' => 'gnJkA6GwsLJ6',
                        'data' => array(
                            'Key' => 'Destinations',
                            'List' => array(
                                'Key' => 'Destination',
                                'Fields' => array(
                                    0 => array(
                                        'Key' => 'SenderGeography',
                                        'Value' => 'cf862f56-442d-11dc-9497-0015170f8c09',
                                        'ValueType' => 'string'
                                    ),
                                    1 => array(
                                        'Key' => 'RecipientGeography', //получатель
                                        'Value' => $recipient,
                                        'ValueType' => 'string'
                                    ),
                                    2 => array(
                                        'Key' => 'TypeOfCargo',
                                        'Value' => '81dd8a13-8235-494f-84fd-9c04c51d50ec',
                                        'ValueType' => 'string'
                                    ),
                                    3 => array(
                                        'Key' => 'Weight',
                                        'Value' => 0.1,
                                        'ValueType' => 'float'
                                    ),
                                    4 => array(
                                        'Key' => 'Qty',
                                        'Value' => 1,
                                        'ValueType' => 'int'
                                    )

                                )
                            )
                        )
                    );

                    try {
                        $client = new SoapClient('http://web.cse.ru/1c/ws/Web1C.1cws?wsdl', array(
                            'trace' => true,
                            'features' => SOAP_WAIT_ONE_WAY_CALLS,
                            'cache_wsdl' => WSDL_CACHE_NONE
                        ));
                    } catch (Exception $e) {
                        //die($e->getMessage());
                        return;
                    }

                    try {
                        $this->setCalcResponse($client->Calc($calc_request));
                    } catch (Exception $e) {
                        //die($e->getMessage());
                        return;
                    }
                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 *
                 * @param mixed $package
                 *
                 * @return void
                 */
                public function calculate_shipping($package = array())
                {
                    $country = $package['destination']['country'];
                    if ($country != 'RU') return;

                    $fias_num = $package['destination']['address_2'];
                    if ($fias_num == '') return;

                    $city = $package['destination']['city'];
                    if ($city != '') {

                        $fias = 'fias-' . $fias_num;
                        $recipient = ''; //получатель
                        $cost = 0;

                        $this->soapclient_GetReferenceData($fias);

                        if ($this->getRecipientData() == null) return;

                        if (is_array($this->getRecipientData()->return->List)) {

                            foreach ($this->getRecipientData()->return->List['Fields'] as $field_el) {
                                if ($field_el->Key == 'ID') {
                                    $recipient = $field_el->Value;
                                    break;
                                }
                                break;
                            }

                        } else {

                            foreach ($this->getRecipientData()->return->List->Fields as $field) {
                                if ($field->Key == 'ID') {
                                    $recipient = $field->Value;
                                    break;
                                }
                            }
                        }

                        $this->soapclient_calc($recipient);

                        //echo '<pre>' . print_r($this->getCalcResponse(), true) . '</pre>';

                        foreach ($this->getCalcResponse()->return->List->List as $field) {
                            if ($field->Fields[4]->Value == 'Срочная') {
                                $cost = intval($field->Fields[0]->Value);
                                break;
                            }
                        }

                        $rate = array(
                            'id' => $this->id,
                            'label' => $this->title,
                            'cost' => $cost
                        );

                        $this->add_rate($rate);

                    } else {
                        return;
                    }
                    error_log("Расчитано: " . $cost, 0);
                }
            }
        }
    }


    add_action('woocommerce_shipping_init', 'cse_shipping_method');

    function add_cse_shipping_method($methods)
    {
        $methods[] = 'cse_Shipping_Method';

        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_cse_shipping_method');
}