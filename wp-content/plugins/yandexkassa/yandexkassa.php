<?php
/**
 * Plugin Name: Яндекс.Касса для WooCommerce
 * Plugin URI: https://wordpress.org/plugins/yandexkassa/
 * Description: Платежный модуль для работы с сервисом Яндекс.Касса через плагин WooCommerce
 * Version: 2.3.6.1
 * Author: Yandex.Money
 * Author URI: http://kassa.yandex.ru
 * License URI: https://money.yandex.ru/doc.xml?id=527132
 */
if (!defined('ABSPATH')) exit;
include_once 'yandexkassa_gateway.class.php';
define("YAMONEY_VERSION", '2.3.6.1');

function yamoney_all_gateway_icon($gateways)
{
    $list_icons = array('kassa' => 'kassa', 'yandex_money' => 'pc', 'bank' => 'ac', 'terminal' => 'gp', 'mobile' => 'mc', 'yandex_webmoney' => 'wm', 'alfabank' => 'ab', 'sberbank' => 'sb', 'masterpass' => 'ma', 'psbank' => 'pb', 'qiwi' => 'qw', 'qppi' => 'qp', 'mpos' => 'ac');
    $url = plugins_url('images/', __FILE__);
    foreach ($list_icons as $name => $png_name) if (isset($gateways[$name])) $gateways[$name]->icon = $url . $png_name . '.png';
    return $gateways;
}

add_filter('woocommerce_available_payment_gateways', 'yamoney_all_gateway_icon');

if (!class_exists('WC_yamoney_Gateway')) return;

class WC_yamoney_smartpay extends WC_yamoney_smartpay_Gateway
{
    public function __construct()
    {
        $this->id = 'kassa';
        $this->method_title = 'Яндекс.Касса (банковские карты, электронные деньги и другое)';
        $this->long_name = 'Оплата через сервис Яндекс.Касса';
        $this->payment_type = '';
        parent::__construct();
    }
}

class WC_yamoney_PC extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'yandex_money';
        $this->method_title = 'Кошелек Яндекс.Деньги';
        $this->long_name = 'Оплата из кошелька в Яндекс.Деньгах';
        $this->payment_type = 'PC';
        parent::__construct();
    }
}

class WC_yamoney_AC extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'bank';
        $this->method_title = 'Банковская карта';
        $this->long_name = 'Оплата с произвольной банковской карты';
        $this->payment_type = 'AC';
        parent::__construct();
    }
}

class WC_yamoney_GP extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'terminal';
        $this->method_title = 'Наличными через кассы и терминалы';
        $this->long_name = 'Оплата наличными через кассы и терминалы';
        $this->payment_type = 'GP';
        parent::__construct();
    }
}

class WC_yamoney_MC extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'mobile';
        $this->method_title = 'Счет мобильного телефона';
        $this->long_name = 'Платеж со счета мобильного телефона';
        $this->payment_type = 'MC';
        parent::__construct();
    }
}

class WC_yamoney_WM extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'yandex_webmoney';
        $this->method_title = 'Кошелек WebMoney';
        $this->long_name = 'Оплата из кошелька в системе WebMoney';
        $this->payment_type = 'WM';
        parent::__construct();
    }
}

class WC_yamoney_AB extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'alfabank';
        $this->method_title = 'Альфа-Клик';
        $this->long_name = 'Оплата через Альфа-Клик';
        $this->payment_type = 'AB';
        parent::__construct();
    }
}

class WC_yamoney_SB extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'sberbank';
        $this->method_title = 'Сбербанк: оплата по SMS или Сбербанк Онлайн';
        $this->long_name = 'Оплата через Сбербанк: оплата по SMS или Сбербанк Онлайн';
        $this->payment_type = 'SB';
        parent::__construct();
    }
}

class WC_yamoney_PB extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'psbank';
        $this->method_title = 'Интернет-банк Промсвязьбанка';
        $this->long_name = 'Оплата через интернет-банк Промсвязьбанка';
        $this->payment_type = 'PB';
        parent::__construct();
    }
}

class WC_yamoney_QW extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'qiwi';
        $this->method_title = 'QIWI Wallet';
        $this->long_name = 'Оплата через QIWI Wallet';
        $this->payment_type = 'QW';
        parent::__construct();
    }
}

class WC_yamoney_QP extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'qppi';
        $this->method_title = 'Доверительный платеж (Куппи.ру)';
        $this->long_name = 'Оплата через доверительный платеж (Куппи.ру)';
        $this->payment_type = 'QP';
        parent::__construct();
    }
}

class WC_yamoney_MA extends WC_yamoney_Gateway
{
    public function __construct()
    {
        $this->id = 'masterpass';
        $this->method_title = 'MasterPass';
        $this->long_name = 'Оплата через MasterPass';
        $this->payment_type = 'MA';
        parent::__construct();
    }
}

if (!class_exists('WC_yamoney_mpos_Gateway')) return;

class WC_yamoney_MP extends WC_yamoney_mpos_Gateway
{
    public function __construct()
    {
        $this->id = 'mpos';
        $this->method_title = 'Оплата картой при доставке';
        $this->long_name = 'Оплата картой при доставке';
        $this->payment_type = 'MP';
        parent::__construct();
    }
}

function yamoney_add_all_gateway($methods)
{
    if (get_option('ym_paymode') == '1') {
        $methods[] = 'WC_yamoney_smartpay';
    } else {
        $methods[] = 'WC_yamoney_PC';
        $methods[] = 'WC_yamoney_AC';
        $methods[] = 'WC_yamoney_GP';
        $methods[] = 'WC_yamoney_MC';
        $methods[] = 'WC_yamoney_WM';
        $methods[] = 'WC_yamoney_AB';
        $methods[] = 'WC_yamoney_SB';
        $methods[] = 'WC_yamoney_MA';
        $methods[] = 'WC_yamoney_PB';
        $methods[] = 'WC_yamoney_QW';
        $methods[] = 'WC_yamoney_QP';
        $methods[] = 'WC_yamoney_MP';
    }
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'yamoney_add_all_gateway');

function register_yamoney_setting()
{
    register_setting('woocommerce-yamoney', 'ym_Scid');
    register_setting('woocommerce-yamoney', 'ym_ShopID');
    register_setting('woocommerce-yamoney', 'ym_shopPassword');
    register_setting('woocommerce-yamoney', 'ym_Demo');

    register_setting('woocommerce-yamoney', 'ym_paymode');
    register_setting('woocommerce-yamoney', 'ym_page_mpos');
    register_setting('woocommerce-yamoney', 'ym_success');
    register_setting('woocommerce-yamoney', 'ym_fail');
    register_setting('woocommerce-yamoney', 'ym_tax_rates_enum');
    register_setting('woocommerce-yamoney', 'ym_enable_receipt');
    register_setting('woocommerce-yamoney', 'ym_default_tax_rate');
    register_setting('woocommerce-yamoney', 'ym_tax_rate');

    update_option('ym_tax_rates_enum', array(
        1 => "Не облагается",
        2 => "0%",
        3 => "10%",
        4 => "18%",
        5 => "Расчетная ставка",
        6 => "Расчетная ставка 18/118"
    ));
}

add_action('admin_menu', 'register_yandexMoney_submenu_page');
add_action('update_option_ym_ShopID', 'after_update_setting');
function register_yandexMoney_submenu_page()
{
    add_submenu_page('woocommerce', 'Настройки Яндекс.Кассы', 'Настройки Яндекс.Кассы', 'manage_options', 'yandex_money_menu', 'yandexMoney_submenu_page_callback');
    add_action('admin_init', 'register_yamoney_setting');
}


function yandexMoney_submenu_page_callback()
{ ?>
    <div class="wrap">
        <h2>Настройки модуля Яндекс.Касса для WooCommerce</h2>
        <p>Работая с модулем, вы автоматически соглашаетесь с <a href='https://money.yandex.ru/doc.xml?id=527132'
                                                                 target='_blank'>условиями его использования</a>.</p>
        <p>Версия модуля <?php echo YAMONEY_VERSION; ?></p>
        <p>Для работы с модулем необходимо подключить магазин к <a target="_blank" href=\"https://kassa.yandex.ru/">Яндекс.Кассе</a>
        </p>
        <form method="post" action="options.php">
            <?php
            wp_nonce_field('update-options');
            settings_fields('woocommerce-yamoney');
            do_settings_sections('woocommerce-yamoney');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <input type="radio" name="ym_Demo"
                               value="1" <?php echo get_option('ym_Demo') == '1' ? 'checked="checked"' : ''; ?>/>Тестовый
                        режим
                        <input type="radio" name="ym_Demo"
                               value="0" <?php echo get_option('ym_Demo') != '1' ? 'checked="checked"' : ''; ?>/>Рабочий
                        режим
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">checkUrl/avisoUrl</th>
                    <td><code><?php echo site_url('/?yandex_money=check', 'https'); ?></code><br>
                        <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Скопируйте эту ссылку в поля Check URL и Aviso URL в <a
                                    target="_blank" href="https://kassa.yandex.ru/my">настройках личного кабинета Яндекс.Кассы</a><span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">successUrl/failUrl</th>
                    <td><code>Страницы с динамическими адресами</code><br>
                        <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Включите «Использовать страницы успеха и ошибки с динамическими адресами» в <a
                                    target="_blank" href="https://kassa.yandex.ru/my">настройках личного кабинета Яндекс.Кассы</a><span>
                    </td>
                </tr>
            </table>
            <h3>Параметры из личного кабинета Яндекс.Кассы</h3>
            <p>Shop ID, scid, shopPassword можно посмотреть в <a href='https://money.yandex.ru/my' target='_blank'>личном
                    кабинете</a> после подключения Яндекс.Кассы.</p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Shop ID</th>
                    <td><input type="text" name="ym_ShopID" value="<?php echo get_option('ym_ShopID'); ?>"/><br/>
                        <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">
                            Идентификатор магазина<span>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">scid</th>
                    <td><input type="text" name="ym_Scid" value="<?php echo get_option('ym_Scid'); ?>"/>
                        <br/><span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Номер витрины магазина<span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">ShopPassword</th>
                    <td><input type="text" name="ym_shopPassword" value="<?php echo get_option('ym_shopPassword'); ?>"/>
                        <br/><span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Секретное слово<span>
                    </td>
                </tr>
            </table>
            <h3>Настройка сценария оплаты</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Сценарий оплаты</th>
                    <td>
                        <input type="radio" name="ym_paymode"
                               value="1" <?php if (get_option('ym_paymode') == '1') echo ' checked="checked" '; ?> />Выбор
                        оплаты на стороне сервиса Яндекс.Касса<br>
                        <input type="radio" name="ym_paymode"
                               value="0" <?php if (get_option('ym_paymode') != '1') echo ' checked="checked" '; ?> />Выбор
                        оплаты на стороне магазина<br>
                        <a href='https://tech.yandex.ru/money/doc/payment-solution/payment-form/payment-form-docpage/'
                           target='_blank'>Подробнее о сценариях оплаты</a>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Страница успеха платежа</th>
                    <td><select id="ym_success" name="ym_success">
                            <option value="wc_success" <?php echo((get_option('ym_success') == 'wc_success') ? ' selected' : ''); ?>>
                                Страница "Заказ принят" от WooCommerce
                            </option>
                            <option value="wc_checkout" <?php echo((get_option('ym_success') == 'wc_checkout') ? ' selected' : ''); ?>>
                                Страница оформления заказа от WooCommerce
                            </option>
                            <?php
                            if ($pages = get_pages()) {
                                foreach ($pages as $page) {
                                    $selected = ($page->ID == get_option('ym_success')) ? ' selected' : '';
                                    echo '<option value="' . $page->ID . '"' . $selected . '>' . $page->post_title . '</option>';
                                }
                            }
                            ?></select>
                        <br/><span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Эту страницу увидит покупатель, когда оплатит заказ<span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Страница отказа</th>
                    <td><select id="ym_fail" name="ym_fail">
                            <option value="wc_checkout" <?php echo((get_option('ym_fail') == 'wc_checkout') ? ' selected' : ''); ?>>
                                Страница оформления заказа от WooCommerce
                            </option>
                            <option value="wc_payment" <?php echo((get_option('ym_fail') == 'wc_payment') ? ' selected' : ''); ?>>
                                Страница оплаты заказа от WooCommerce
                            </option>
                            <?php
                            if ($pages = get_pages()) {
                                foreach ($pages as $page) {
                                    $selected = ($page->ID == get_option('ym_fail')) ? ' selected' : '';
                                    echo '<option value="' . $page->ID . '"' . $selected . '>' . $page->post_title . '</option>';
                                }
                            }
                            ?></select>
                        <br/><span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Эту страницу увидит покупатель, если что-то пойдет не так: например, если ему не хватит денег на карте<span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Страница успеха для способа «Оплата картой при доставке»</th>
                    <td><select id="ym_page_mpos" name="ym_page_mpos">
                            <?php
                            if ($pages = get_pages()) {
                                foreach ($pages as $page) {
                                    $selected = ($page->ID == get_option('ym_page_mpos')) ? ' selected' : '';
                                    echo '<option value="' . $page->ID . '"' . $selected . '>' . $page->post_title . '</option>';
                                }
                            }
                            ?></select>
                        <br/>
                        <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Это страница с информацией о доставке. Укажите на ней, когда привезут товар и как его можно будет оплатить<span>
                    </td>
                </tr>
            </table>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Отправлять в Яндекс.Кассу данные для чеков (54-ФЗ)</th>
                    <td>
                        <input type="checkbox" id="ym_enable_receipt"
                               name="ym_enable_receipt" <?php echo get_option('ym_enable_receipt') == 'on' ? "checked" : ""; ?> >
                    </td>
                </tr>
                <tr valign="top">
                    <th></th>
                    <td>
                        <?php if (get_option('ym_enable_receipt')): ?>

                            <?php
                            $wcTaxes = yamoney_getAllTaxes();
                            $wcCalcTaxes = get_option('woocommerce_calc_taxes');
                            $ymTaxRatesEnum = get_option('ym_tax_rates_enum');
                            ?>

                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">Ставка по умолчанию</th>
                                    <td>
                                        <select id="ym_default_tax_rate" name="ym_default_tax_rate">
                                            <?php foreach ($ymTaxRatesEnum as $taxId => $taxName) : ?>
                                                <option value="<?php echo $taxId ?>" <?php echo $taxId == get_option('ym_default_tax_rate') ? 'selected=\'selected\'' : ''; ?>><?php echo $taxName ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <br/>
                                        <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Ставка по умолчанию будет в чеке, если в карточке товара не указана другая ставка. <span>
                                    </td>
                                </tr>
                            </table>

                            <?php if ($wcCalcTaxes == 'yes' && $wcTaxes) : ?>
                                <table class="form-table">
                                    <tr valign="top">
                                        <th scope="row">Ставка в вашем магазине</th>
                                        <td>
                                            <span style="line-height: 1;font-weight: normal;font-style: italic;font-size: 12px;">Слева - ставка НДС в вашем магазине, справа - в Яндекс.Кассе. Пожалуйста, сопоставьте их.<span>
                                        </td>
                                    </tr>
                                </table>
                                <table class="form-table">
                                    <?php
                                    $ymTaxes = get_option('ym_tax_rate');
                                    ?>
                                    <?php foreach ($wcTaxes as $wcTax) : ?>
                                        <tr valign="top">
                                            <th scope="row" style="width: 50px;"><?php echo round($wcTax->tax_rate) ?>%
                                            </th>
                                            <td style="vertical-align: top; text-align: left;padding: 20px 10px 20px 0;width: 210px;line-height: 1.3;">
                                                передавать в Яндекс.Кассу как
                                            </td>
                                            <td>
                                                <div>
                                                    <?php $selected = isset($ymTaxes[$wcTax->tax_rate_id]) ? $ymTaxes[$wcTax->tax_rate_id] : null; ?>
                                                    <select id="ym_tax_rate[<?php echo $wcTax->tax_rate_id ?>]"
                                                            name="ym_tax_rate[<?php echo $wcTax->tax_rate_id ?>]">
                                                        <?php foreach ($ymTaxRatesEnum as $taxId => $taxName) : ?>
                                                            <option value="<?php echo $taxId ?>" <?php echo $selected == $taxId ? 'selected' : ''; ?> >
                                                                <?php echo $taxName ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <br/>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="action" value="update"/>
            <input type="hidden" name="page_options"
                   value="ym_Scid,ym_ShopID,ym_shopPassword,ym_Demo,ym_success,ym_fail,ym_page_mpos,ym_paymode, ym_enable_receipt, ym_default_tax_rate, ym_tax_rate"/>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
            </p>

        </form>
    </div>
    <?php
}

add_action('parse_request', 'yamoney_checkPayment');

function after_update_setting($one)
{
    new yamoney_statistics();
}

function yamoney_checkPayment()
{
    global $wpdb;
    if (isset($_REQUEST['yandex_money']) && $_REQUEST['yandex_money'] == 'check') {
        $hash = md5($_POST['action'] . ';' . $_POST['orderSumAmount'] . ';' . $_POST['orderSumCurrencyPaycash'] . ';' .
            $_POST['orderSumBankPaycash'] . ';' . $_POST['shopId'] . ';' . $_POST['invoiceId'] . ';' .
            $_POST['customerNumber'] . ';' . get_option('ym_shopPassword'));
        header('Content-Type: application/xml');
        $code = 1;
        $techMessage = 'bad md5';
        if (isset($_POST['md5']) && strtolower($hash) == strtolower($_POST['md5'])) {
            $order = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'posts WHERE ID = ' . (int)$_POST['customerNumber']);
            $order_summ = (isset($order->ID)) ? get_post_meta($order->ID, '_order_total', true) : 0;
            if ($order) {
                if ($order_summ != $_POST['orderSumAmount']) { // !=
                    $code = 100;
                    $techMessage = 'wrong orderSumAmount';
                } else {
                    $code = 0;
                    $techMessage = 'completed';
                    $order_w = new WC_Order($order->ID);
                    if ($_POST['action'] == 'paymentAviso') {
                        $order_w->payment_complete();
                        $order_w->add_order_note("Номер транзакции " . $_POST['invoiceId'] . ", Сумма оплаты " . $_POST['orderSumAmount']);
                        //$order_w->update_status($techMessage, __( 'Awaiting BACS payment', 'woocommerce' ));
                    }
                }
            } elseif ($_POST['paymentType'] == 'MP') {
                $code = 0;
                $techMessage = 'Mpos ok';
            } else {
                $code = 200;
                $techMessage = 'wrong customerNumber';
            }
        }
        $action = ($_POST['action'] == 'checkOrder') ? "checkOrder" : "paymentAviso";
        $invoice = (intval($_POST['invoiceId']) > 0) ? $_POST['invoiceId'] : 0;
        $answer = '<?xml version="1.0" encoding="UTF-8"?>
			<' . $action . 'Response performedDatetime="' . date('c') . '" code="' . $code . '" invoiceId="' . $invoice . '" shopId="' . get_option('ym_ShopID') . '" techMessage="' . $techMessage . '"/>';
        @ob_clean();
        die($answer);
    }
}

class yamoney_statistics
{
    public function __construct()
    {
        $this->send();
    }

    private function send()
    {
        global $wp_version;
        $epl = (bool)(get_option('ym_paymode') == '1');
        $array = array(
            'url' => get_option('siteurl'),
            'cms' => 'wordpress-woo',
            'version' => $wp_version,
            'ver_mod' => YAMONEY_VERSION,
            'yacms' => false,
            'email' => get_option('admin_email'),
            'shopid' => get_option('ym_ShopID'),
            'settings' => array(
                'kassa' => true,
                'kassa_epl' => $epl
            )
        );
        $array_crypt = base64_encode(serialize($array));

        $url = 'https://statcms.yamoney.ru/v2/';
        $curlOpt = array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_POST => true,
        );

        $curlOpt[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded');
        $curlOpt[CURLOPT_POSTFIELDS] = http_build_query(array('data' => $array_crypt, 'lbl' => 0));

        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOpt);
        $rbody = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    }
}

function yamoney_getAllTaxes()
{
    global $wpdb;

    $query = "
			SELECT *
			FROM {$wpdb->prefix}woocommerce_tax_rates
			WHERE 1 = 1
		";

    $order_by = ' ORDER BY tax_rate_order';

    $result = $wpdb->get_results($query . $order_by);

    return $result;
}
