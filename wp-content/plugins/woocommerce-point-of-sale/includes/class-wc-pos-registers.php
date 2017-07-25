<?php
/**
 * WoocommercePointOfSale Registers Class
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/Registers
 * @category    Class
 * @since     0.1
 */


if (!defined('ABSPATH')) exit; // Exit if accessed directly


class WC_Pos_Registers
{

    public static $register_detail_fields;
    public static $register_end_of_sale_fields;

    /**
     * @var WC_Pos_Registers The single instance of the class
     * @since 1.9
     */
    protected static $_instance = null;

    /**
     * Main WC_Pos_Registers Instance
     *
     * Ensures only one instance of WC_Pos_Registers is loaded or can be loaded.
     *
     * @since 1.9
     * @static
     * @return WC_Pos_Registers Main instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.9
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'woocommerce'), '1.9');
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.9
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'woocommerce'), '1.9');
    }

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Init address fields we display + save
     */
    public function init_form_fields()
    {
        $_shipping_methods = WC()->shipping->load_shipping_methods();
        $shipping_methods = array('' => __('No Shipping', 'wc_point_of_sale'));
        foreach ($_shipping_methods as $key => $method) {
            $shipping_methods[$method->id] = $method->get_title();
        }
        self::$register_detail_fields = array(
            'name' => array(
                'label' => __('Name', 'woocommerce'),
                'description' => __('The name of the register that will appear when opening it.', 'wc_point_of_sale'),
                'custom_attributes' => array(
                    'required' => true
                )
            ),
            'ta_id' => array(
                'label' => 'Номер ТА',
                'description' => 'Введите название ТА',
                'custom_attributes' => array(
                    'required' => true
                )
            ),
            'grid_template' => array(
                'label' => __('Product Grid', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => WC_POS()->grid()->get_data_names(),
                'description' => __('Select the product grid that this register will use.', 'wc_point_of_sale'),
            ),
            'receipt_template' => array(
                'label' => __('Receipt Template', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => WC_POS()->receipt()->get_data_names(),
                'description' => __('Select the receipt template that this register will use.', 'wc_point_of_sale'),
            ),
            'prefix' => array(
                'label' => __('Prefix', 'wc_point_of_sale'),
                'description' => __('Enter the prefix of the orders from this register.', 'wc_point_of_sale'),
                'custom_attributes' => array()
            ),
            'suffix' => array(
                'label' => __('Suffix', 'wc_point_of_sale'),
                'description' => __('Enter the suffix of the orders from this register.', 'wc_point_of_sale'),
                'custom_attributes' => array()
            ),
            'outlet' => array(
                'label' => __('Outlet', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => WC_POS()->outlet()->get_data_names(),
                'description' => __('Select the outlet that this register is assigned to.', 'wc_point_of_sale'),
            ),
            'tax_number' => array(
                'label' => __('Tax Number', 'wc_point_of_sale'),
                'description' => __('Enter the tax number which is applied to this particular register. This will be printed on receipts if tax number is enabled on receipt template.', 'wc_point_of_sale'),
                'custom_attributes' => array()
            ),
            'default_customer' => array(
                'label' => __('Default Customer', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => array(__('Guest', 'wc_point_of_sale')),
                'description' => __('Select what you want the default customer to be when register is opened.', 'wc_point_of_sale'),
            ),
            'default_shipping_method' => array(
                'label' => __('Default Shipping Method', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => $shipping_methods,
                'description' => __('Select what you want the default shipping method to be when shipping methods are loaded.', 'wc_point_of_sale'),
            ),
            'float_cash_management' => array(
                'label' => __('Cash Management', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => array(0 => 'No', 1 => 'Yes'),
                'description' => __('Select whether you want to manage the float of cash in the register.', 'wc_point_of_sale'),
            ),
        );
        self::$register_end_of_sale_fields = array(
            'change_user' => array(
                'label' => __('Change User', 'wc_point_of_sale'),
                'type' => 'select',
                'value' => 0,
                'options' => array(1 => 'Yes', 0 => 'No'),
                'description' => __('Select whether user to be changed at end of sale.', 'wc_point_of_sale'),
            ),
            'email_receipt' => array(
                'label' => __('Email Receipt', 'wc_point_of_sale'),
                'type' => 'select',
                'value' => 0,
                'options' => array(1 => 'Yes, for all customers', 2 => 'Yes, for non-guest customers only', 0 => 'No'),
                'description' => __('Select whether to email receipt at end of sale.', 'wc_point_of_sale'),
            ),
            'print_receipt' => array(
                'label' => __('Print Receipt', 'wc_point_of_sale'),
                'type' => 'select',
                'value' => 0,
                'options' => array(1 => 'Yes', 0 => 'No'),
                'description' => __('Select whether to print receipt at end of sale.', 'wc_point_of_sale'),
            ),
            'gift_receipt' => array(
                'label' => __('Gift Receipt', 'wc_point_of_sale'),
                'type' => 'select',
                'value' => 0,
                'options' => array(1 => 'Yes', 0 => 'No'),
                'description' => __('Select whether to print gift receipt at end of sale.', 'wc_point_of_sale'),
            ),
            'note_request' => array(
                'label' => __('Note Request', 'wc_point_of_sale'),
                'type' => 'select',
                'options' => array(0 => 'None', 1 => 'On Save', 2 => 'On All Sales'),
                'description' => __('Select whether to add a note at end of sale.', 'wc_point_of_sale'),
            ),
        );
    }

    public function get_register_detail_fields()
    {
        if (!isset(self::$register_detail_fields) || empty(self::$register_detail_fields)) {
            self::init_form_fields();
        }
        return self::$register_detail_fields;
    }

    public function display()
    {
        self::init_form_fields();
        ?>
        <div class="wrap nosubsub" id="wc-pos-registers">
            <h2>
                <?php echo get_admin_page_title(); ?>
                <?php if (isset($_GET['s']) && !empty($_GET['s'])) { ?>
                    <span class="subtitle">Search results for “<?php echo $_GET['s']; ?>”</span>
                <?php } ?>
            </h2>

            <?php if (isset($_GET['message']) && !empty($_GET['message'])) {
                $message = self::get_message($_GET['message']);
                if (!empty($message)) {
                    ?>
                    <div class="<?php echo $message['class']; ?> below-h2" id="message">
                        <p><?php echo $message['text']; ?></p></div>
                <?php }
            } ?>
            <div id="ajax-response"></div>
            <?php if (current_user_can('manage_wc_point_of_sale')) { ?>
                <form method="get" action="" class="search-form">

                    <p class="search-box">
                        <label for="register-search-input"
                               class="screen-reader-text"><?php _e('Search Registers', 'wc_point_of_sale'); ?></label>
                        <input type="hidden" value="wc_pos_registers" name="page">
                        <input type="search" value="" name="s" id="register-search-input">
                        <input type="submit" value="<?php _e('Search Registers', 'wc_point_of_sale'); ?>" class="button"
                               id="search-submit" name="">
                    </p>

                </form>
            <?php } ?>
            <br class="clear">
            <?php if (current_user_can('manage_wc_point_of_sale')) { ?>
                <div id="col-container">
                    <div id="col-right" class="register_table">
                        <?php self::display_register_table(); ?>
                    </div> <!-- /col-right -->
                    <div id="col-left">
                        <?php self::display_register_form();
                        ?>
                    </div> <!-- /col-left -->
                </div>
            <?php } else {
                self::display_register_table();
            } ?>
        </div>
        <?php
        if (isset($_GET['report']) && !empty($_GET['report'])) {
            global $wpdb;
            $rg_id = $_GET['report'];
            $data = WC_POS()->register()->get_data($rg_id);
            $data = $data[0];
            $outlets_name = WC_POS()->outlet()->get_data_names();
            $outlet = $outlets_name[$data['outlet']];
            ?>
            <div id="sale_report_overlay" class="overlay_order_popup" style="display: block;">
                <div id="sale_report_popup">
                    <div class="media-frame-title">
                        <h1><?php _e('Report', 'wc_point_of_sale'); ?></h1>
                    </div>
                    <span class="close_popup"></span>
                    <?php
                    include_once(WC_POS()->plugin_views_path() . '/html-admin-registers-sale_report_overlay.php');
                    ?>
                    <div class="media-frame-footer">
                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=' . WC_POS_TOKEN . '-print&print=report&report=' . $rg_id), 'print_pos_report'); ?>"
                           class="button alignright" target="_blank">Print</a><br class="clear">
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function display_edit_form($id = 0)
    {
        $data = array();
        self::init_form_fields();
        if ($id) {
            $data = $this->get_data($id);
            $data = $data[0];
            foreach ($data['detail'] as $i => $val) {
                $data[$i] = $val;
            }
            foreach ($data['settings'] as $i => $val) {
                $data[$i] = $val;
            }
        }

        ?>
        <div class="wrap" id="wc-pos-registers-edit">
            <h2><?php _e('Edit Register', 'wc_point_of_sale'); ?></h2>
            <div id="ajax-response"></div>
            <form class="validate" action="" method="post">
                <input type="hidden" value="edit-wc-pos-registers" name="action">
                <input type="hidden" value="<?php echo $data['ID']; ?>" name="id" id="id_register">
                <?php wp_nonce_field('nonce-edit-wc-pos-registers', '_wpnonce_edit-wc-pos-registers'); ?>
                <table class="form-table">
                    <tbody>
                    <?php
                    foreach (self::$register_detail_fields as $key => $field) {
                        if (!isset($field['type']))
                            $field['type'] = 'text';
                        $value = "";
                        if (isset($field['value'])) {
                            $value = $field['value'];
                        } else {
                            $value = isset($data[$key]) ? $data[$key] : '';
                        }

                        if ($key == 'default_customer' && $value != 0) {
                            $customer = get_userdata($value);
                            $field['options'][$customer->ID] = $customer->first_name . ' ' . $customer->last_name . ' &ndash; ' . sanitize_email($customer->user_email);
                        }

                        switch ($field['type']) {
                            case "select" :
                                wc_pos_select(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                            case "radio" :
                                wc_pos_radio(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                            default :
                                wc_pos_text_input(array('id' => '_register_' . $key, 'label' => $field['label'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                        }
                    }
                    ?>
                    <tr class="form-field">
                        <th colspan="2">
                            <h3><?php _e('End Of Sale', 'wc_point_of_sale'); ?></h3>
                        </th>
                    </tr>
                    <?php
                    foreach (self::$register_end_of_sale_fields as $key => $field) {
                        if (!isset($field['type']))
                            $field['type'] = 'text';
                        $value = "";
                        if ($data[$key] || $data[$key] == 0) {
                            $value = $data[$key];
                        }
                        switch ($field['type']) {
                            case "select" :
                                wc_pos_select(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                            case "radio" :
                                wc_pos_radio(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                            default :
                                wc_pos_text_input(array('id' => '_register_' . $key, 'label' => $field['label'], 'value' => $value, 'description' => $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
                                break;
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <p class="submit"><input type="submit" value="<?php _e('Update', 'wc_point_of_sale'); ?>"
                                         class="button button-primary" id="submit" name="submit"></p>
            </form>
        </div>
        <?php
    }

    public function display_register_table()
    {
        ?>
        <div class="col-wrap">
            <form id="wc_pos_registers_table" action="" method="post">
                <?php
                if (current_user_can('manage_wc_point_of_sale')) {
                    $registers_table = WC_POS()->registers_table();
                    $registers_table->prepare_items();
                    $registers_table->display();
                } else {
                    $data = WC_POS()->register()->get_data();
                    $custom_data = array();
                    $i = 0;
                    foreach ($data as $datum) {
                        if (pos_check_user_can_open_register($datum['ID'])) { // Проверка. Привязан ли id кабинета(outlet) к user id
                            array_push($custom_data, $datum);
                            //self::$custom_data = array($datum);
                            //break;
                        }
                    }

                    if (pos_check_register_is_open($custom_data[0]['ID']) && WC_POS()->wc_api_is_active) {
                        $btn_text = __('Открыт', 'wc_point_of_sale');
                        $status = '<span class="register-status-open tips" data-tip=' . $btn_text . '></span>';
                    } else {
                        $btn_text = __('Закрыт', 'wc_point_of_sale');
                        $status = '<span class="register-status-closed tips" data-tip=' . $btn_text . '></span>';
                    }

                    $outlets_name = WC_POS()->outlet()->get_data_names();

                    //print_r($outlets_name);

                    if (pos_check_user_can_open_register($custom_data[0]['ID']) && !pos_check_register_lock($custom_data[0]['ID']) && WC_POS()->wc_api_is_active) {

                        $btn_text = __('Открыть', 'wc_point_of_sale');
                        if (pos_check_register_is_open($custom_data[0]['ID'])) {
                            $btn_text = __('Войти', 'wc_point_of_sale');
                        }
                        $outlet = sanitize_title($outlets_name[$custom_data[0]['outlet']]);
                        $register = $custom_data[0]['slug'];

                        if (class_exists('SitePress')) {
                            $settings = get_option('icl_sitepress_settings');
                            if ($settings['urls']['directory_for_default_language'] == 1) {
                                $register_url = get_home_url() . '/' . ICL_LANGUAGE_CODE . "/point-of-sale/$outlet/$register";
                            } else {
                                $register_url = get_home_url() . "/point-of-sale/$outlet/$register";
                            }
                        } else {
                            $register_url = get_home_url() . "/point-of-sale/$outlet/$register";
                        }

                        if (is_ssl() || get_option('woocommerce_pos_force_ssl_checkout') == 'yes') {
                            $register_url = str_replace('http:', 'https:', $register_url);
                        }
                        $button = '<a class="button tips ' . $btn_text . '-register" href="' . $register_url . '" data-tip="' . $btn_text . ' Кабинет" >' . $btn_text . '</a>';

                    } else {
                        if (!WC_POS()->wc_api_is_active) {
                            $btn_text = __('Open', 'wc_point_of_sale');
                            $button = '<a class="button tips open-register" data-tip="' . __('The WooCommerce API is disabled on this site.', 'wc_point_of_sale') . '" disabled>' . $btn_text . '</button>';
                        } else {
                            $userid = pos_check_register_lock($custom_data[0]['ID']);
                            $user = get_userdata($userid);
                            $btn_text = __('Open', 'wc_point_of_sale');
                            if ($user) {
                                $name = trim($user->first_name . ' ' . $user->last_name);
                                if ($name == '')
                                    $name = $user->user_nicename;
                                $button = '<a class="button tips open-register" data-tip="' . $name . ' уже работает в кабинете." disabled>Открыт</button>';
                            } else {
                                $button = '<a class="button tips open-register" data-tip="У Вас нет доступа к этому кабинету" disabled>Открыть</button>';
                            }
                        }
                    }


                    ?>
                    <div class="md-card md-card-hover">
                        <div class="md-card-head">
                            <h3 class="md-card-head-text uk-text-center">
                                <span class="uk-text-truncate"><?= $custom_data[0]['name'] ?></span>
                            </h3>
                            <?= $status ?>
                        </div>
                        <div class="md-card-content access column-access" style="text-align: center;">
                            <?= $button ?>
                        </div>
                    </div>

                    <?php

                }
                ?>
            </form>
        </div>
        <?php
    }


    public function display_register_form()
    {
        ?>
        <div class="col-wrap">
            <p><?php echo _e('Registers for your store can be managed here. Registers can be added using the form below. To edit an register, simply hover over the register and click on Edit.', 'wc_point_of_sale'); ?></p>
            <div class="form-wrap">
                <h3><?php _e('Register Details', 'wc_point_of_sale'); ?></h3>
                <form id="add_wc_pos_registers" class="validate" action="" method="post">
                    <input type="hidden" value="add-wc-pos-registers" name="action">
                    <?php wp_nonce_field('nonce-add-wc-pos-registers', '_wpnonce_add-wc-pos-registers'); ?>
                    <?php
                    foreach (self::$register_detail_fields as $key => $field) {
                        if (!isset($field['type']))
                            $field['type'] = 'text';
                        $value = "";
                        if (isset($field['value'])) {
                            $value = $field['value'];
                        }
                        switch ($field['type']) {
                            case "select" :
                                wc_pos_select(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description']));
                                break;
                            case "radio" :
                                wc_pos_radio(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description']));
                                break;
                            default :
                                wc_pos_text_input(array('id' => '_register_' . $key, 'label' => $field['label'], 'value' => $value, 'description' => $field['description'], 'custom_attributes' => $field['custom_attributes']));
                                break;
                        }
                    }
                    ?>
                    <h3><?php _e('End Of Sale', 'wc_point_of_sale'); ?></h3>
                    <?php
                    foreach (self::$register_end_of_sale_fields as $key => $field) {
                        if (!isset($field['type']))
                            $field['type'] = 'text';
                        $value = "";
                        if (isset($field['value'])) {
                            $value = $field['value'];
                        }
                        switch ($field['type']) {
                            case "select" :
                                wc_pos_select(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description']));
                                break;
                            case "radio" :
                                wc_pos_radio(array('id' => '_register_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' => $field['description']));
                                break;
                            default :
                                wc_pos_text_input(array('id' => '_register_' . $key, 'label' => $field['label'], 'value' => $value, 'description' => $field['description']));
                                break;
                        }
                    }
                    ?>
                    <p class="submit"><input type="submit" value="<?php _e('Add New Register', 'wc_point_of_sale'); ?>"
                                             class="button button-primary" id="submit" name="submit"></p>
                </form>
            </div>
        </div>
        <?php
    }

    public function save_register($redirect = true)
    {
        global $wpdb;
        $wpdb->show_errors();
        $id = 0;
        if (isset($_POST['id']) && $_POST['id'] != '') $id = $_POST['id'];
        self::init_form_fields();

        $detail = array();
        $settings = array();

        foreach (self::$register_detail_fields as $key => $value) {
            if ($key == 'name') continue;
            if ($key == 'outlet') continue;
            $detail[$key] = isset($_POST['_register_' . $key]) ? $_POST['_register_' . $key] : '';
        }
        foreach (self::$register_end_of_sale_fields as $key => $value) {
            $settings[$key] = isset($_POST['_register_' . $key]) ? $_POST['_register_' . $key] : '';
        }
        $data['name'] = isset($_POST['_register_name']) ? $_POST['_register_name'] : '';

        $data['slug'] = wc_sanitize_taxonomy_name(stripslashes($data['name']));
        $data['outlet'] = isset($_POST['_register_outlet']) ? $_POST['_register_outlet'] : '';
        $data['default_customer'] = isset($_POST['_register_default_customer']) ? $_POST['_register_default_customer'] : '';
        $data['detail'] = json_encode($detail);
        $data['settings'] = json_encode($settings);

        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        if ($id) {
            $rows_affected = $wpdb->update($table_name, $data, array('ID' => $id));
            if ($redirect) {
                return wp_redirect(add_query_arg(array("page" => WC_POS()->id_registers, "message" => 2), 'admin.php'));
            }
        } else {
            $rows_affected = $wpdb->insert($table_name, $data);
            $new_register_id = $wpdb->insert_id;
            $new_order = array(
                'post_title' => 'POS Register #' . $new_register_id,
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'pos_temp_register_or'
            );
            // Insert the post into the database
            $order_id = wp_insert_post($new_order);
            $rows_affected = $wpdb->update($table_name, array('order_id' => $order_id), array('ID' => $new_register_id));
            if ($redirect) {
                return wp_redirect(add_query_arg(array("page" => WC_POS()->id_registers, "message" => 1), 'admin.php'));
            }
        }
    }

    public function crate_order_id($register_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";

        $new_order = array(
            'post_title' => 'POS Register #' . $register_id,
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'post_type' => 'pos_temp_register_or'
        );
        // Insert the post into the database
        $order_id = wp_insert_post($new_order);
        $rows_affected = $wpdb->update($table_name, array('order_id' => $order_id), array('ID' => $register_id));
        return $order_id;
    }

    public function save_register_as_order()
    {
        if (!empty($_POST) && check_admin_referer('nonce-save-wc-pos-registers-as-order', '_wpnonce_save-wc-pos-registers-as-order')) {

            global $wpdb;
            $id = 0;

            $id_register = $_POST['id_register'];
            if (isset($_POST['user_id'])) {
                $user_id = $_POST['user_id'];
            } else {
                $user_id = 0;
            }

            if (isset($_POST['id']) && $_POST['id'] != '') $id = $_POST['id'];
            else return wp_redirect(add_query_arg(array("page" => WC_POS()->id_registers, "action" => "view", "id" => $id_register, "message" => 1), 'admin.php'));

            new WC_Pos_Checkout($id, $user_id);
        }
    }


    public function delete_register($ids = 0)
    {
        global $wpdb;
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $ids = $_POST['id'];
        } elseif (isset($_GET['id']) && !empty($_GET['id'])) {
            $ids = $_GET['id'];
        }
        $filter = '';
        if ($ids) {
            if (is_array($ids)) {
                $ids = implode(',', array_map('intval', $ids));
                $filter .= "WHERE ID IN ($ids)";
            } else {
                $filter .= "WHERE ID = $ids";
            }
            $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
            $query = "DELETE FROM $table_name $filter";
            if ($wpdb->query($query)) {
                return wp_redirect(add_query_arg(array("page" => WC_POS()->id_registers, "message" => 3), 'admin.php'));
            }
        }
        return wp_redirect(add_query_arg(array("page" => WC_POS()->id_registers), 'admin.php'));
    }


    function get_message($id = 0)
    {
        $message = array();
        switch ($id) {
            case 1:
                $message['class'] = 'updated';
                $message['text'] = __('New register added.', 'wc_point_of_sale');
                break;
            case 2:
                $message['class'] = 'updated';
                $message['text'] = __('Register updated.', 'wc_point_of_sale');
                break;
            case 3:
                $message['class'] = 'updated';
                $message['text'] = __('Register deleted.', 'wc_point_of_sale');
                break;
        }
        return $message;
    }


    public function get_data_by_slug($slug = '')
    {
        global $wpdb;
        $filter = '';
        if (!empty($slug)) {
            $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
            $db_data = $wpdb->get_results("SELECT * FROM $table_name WHERE slug = '$slug'");
            $data = array();

            foreach ($db_data as $value) {
                $value->detail = (array)json_decode($value->detail);
                $value->settings = (array)json_decode($value->settings);
                $data[] = get_object_vars($value);
            }
        }
        return $data;
    }

    public function get_data($ids = '')
    {
        global $wpdb;
        $filter = '';
        if (!empty($ids)) {
            if (is_array($ids)) {
                $ids = implode(',', array_map('intval', $ids));
                $filter .= "WHERE ID IN  == ($ids)";
            } else {
                $filter .= "WHERE ID = $ids";
            }
        }
        if (isset($_GET['s']) && !empty($_GET['s']) && isset($_GET['page']) && $_GET['page'] == WC_POS()->id_registers) {
            $s = $_GET['s'];
            $filter = "WHERE lower( concat(name, detail) ) LIKE lower('%$s%')";
        }
        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        $db_data = $wpdb->get_results("SELECT * FROM $table_name $filter");
        $data = array();

        foreach ($db_data as $value) {
            $value->detail = (array)json_decode($value->detail);
            $value->settings = (array)json_decode($value->settings);
            $data[] = get_object_vars($value);
        }
        return $data;
    }

    public function get_data_names()
    {
        global $wpdb;
        $names = array();
        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        $db_data = $wpdb->get_results("SELECT ID, name FROM $table_name");
        if (!empty($db_data)) {
            foreach ($db_data as $value) {
                $names[$value->ID] = $value->name;
            }
        }
        return $names;
    }

    public function get_register_name_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wc_poin_of_sale_registers";
        $db_data = $wpdb->get_var("SELECT name FROM $table_name WHERE ID = {$id}");
        return $db_data;
    }

}

?>