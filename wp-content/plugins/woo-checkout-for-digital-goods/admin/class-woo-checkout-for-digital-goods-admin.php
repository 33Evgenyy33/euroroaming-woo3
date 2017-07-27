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
class Woo_Checkout_For_Digital_Goods_Admin {

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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-checkout-for-digital-goods-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('wp-pointer');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-checkout-for-digital-goods-admin.js', array('jquery', 'jquery-ui-dialog'), $this->version, false);
        //enqueue script for notice pointer
        wp_enqueue_script('wp-pointer');
    }

    public function woo_checkout_for_digital_create_page() {
        add_menu_page("Woo Checkout Fields", "Woo Checkout Fields", "manage_options", "woo-checkout-fields", array($this, "woo_checkout_settings_page"), null, 99);
    }

    public function woo_checkout_settings_page($fields) {
        global $woocommerce;
        $current_user = wp_get_current_user();
        if (isset($_POST['save_chk_fields'])) {
            $woo_checkout_field_array = array();
            if (isset($_POST['woo_chk_checkout_field']) && !empty($_POST['woo_chk_checkout_field'])) {
                $woo_checkout_field_array = maybe_serialize($_POST['woo_chk_checkout_field']);
                update_option('wcdg_checkout_fields', $woo_checkout_field_array);
            } else if (!isset($_POST['woo_chk_checkout_field']) && empty($_POST['woo_chk_checkout_field'])) {
                update_option('wcdg_checkout_fields', array());
            }
        }
        $woo_checkout_field_array = get_option('wcdg_checkout_fields');
        $woo_checkout_field_array_serilize = array();
        $woo_checkout_field_array_serilize = maybe_unserialize($woo_checkout_field_array);

        $billing_company_selected = '';
        $billing_address_1_selected = '';
        $billing_address_2_selected = '';
        $billing_city_selected = '';
        $billing_postcode_selected = '';
        $billing_country_selected = '';
        $billing_state_selected = '';
        $billing_phone_selected = '';
        $order_comments_selected = '';

        if (isset($woo_checkout_field_array_serilize) && !empty($woo_checkout_field_array_serilize)) {

            if (in_array('billing_company', $woo_checkout_field_array_serilize)) {
                $billing_company_selected = 'checked';
            }

            if (in_array('billing_address_1', $woo_checkout_field_array_serilize)) {
                $billing_address_1_selected = 'checked';
            }

            if (in_array('billing_address_2', $woo_checkout_field_array_serilize)) {
                $billing_address_2_selected = 'checked';
            }

            if (in_array('billing_city', $woo_checkout_field_array_serilize)) {
                $billing_city_selected = 'checked';
            }

            if (in_array('billing_postcode', $woo_checkout_field_array_serilize)) {
                $billing_postcode_selected = 'checked';
            }

            if (in_array('billing_country', $woo_checkout_field_array_serilize)) {
                $billing_country_selected = 'checked';
            }

            if (in_array('billing_state', $woo_checkout_field_array_serilize)) {
                $billing_state_selected = 'checked';
            }

            if (in_array('billing_phone', $woo_checkout_field_array_serilize)) {
                $billing_phone_selected = 'checked';
            }

            if (in_array('order_comments', $woo_checkout_field_array_serilize)) {
                $order_comments_selected = 'checked';
            }
        }
        ?>
        <div class="div_checkout_fields">
            <h2>Checkout Fields Settings</h2>
            <h2>Select fields which you want to exclude on checkout page</h2>
            <form method="post" action="">

                <label><input type="checkbox"  class="woo_chk"  id="selecctall"/> Selecct All</label>
                <label><input type="checkbox" <?php echo $billing_company_selected; ?> class="woo_chk" value="billing_company" name="woo_chk_checkout_field[]"> Company Name <br/></label>
                <label><input type="checkbox" <?php echo $billing_address_1_selected; ?> class="woo_chk" value="billing_address_1" name="woo_chk_checkout_field[]"> Billing Address one <br/></label>
                <label><input type="checkbox" <?php echo $billing_address_2_selected; ?> class="woo_chk" value="billing_address_2" name="woo_chk_checkout_field[]"> Billing Address two <br/></label>
                <label><input type="checkbox" <?php echo $billing_city_selected; ?> class="woo_chk" value="billing_city" name="woo_chk_checkout_field[]"> Billing City <br/></label>
                <label><input type="checkbox" <?php echo $billing_postcode_selected; ?> class="woo_chk" value="billing_postcode" name="woo_chk_checkout_field[]"> Postal Code<br/></label>
                <label><input type="checkbox" <?php echo $billing_country_selected; ?> class="woo_chk" value="billing_country" name="woo_chk_checkout_field[]"> Billing Country <br/></label>
                <label><input type="checkbox" <?php echo $billing_state_selected; ?> class="woo_chk" value="billing_state" name="woo_chk_checkout_field[]"> Billing State <br/></label>
                <label><input type="checkbox" <?php echo $billing_phone_selected; ?> class="woo_chk" value="billing_phone" name="woo_chk_checkout_field[]"> Billing Phone <br/></label>
                <label><input type="checkbox" <?php echo $order_comments_selected; ?> class="woo_chk" value="order_comments" name="woo_chk_checkout_field[]"> Order Comment <br/></label>

                <input type="submit" name="save_chk_fields" value="Save Settings" class="button button-primary btnsave"/>
        </div>
        </form>
        <?php
        if (!get_option('wcf_plugin_notice_shown')) {
            ?>
            <div id="wcf_dialog">
                <p><?php _e('Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free!'); ?></p>
                <p><input type="text" id="txt_user_sub_wcf" class="regular-text" name="txt_user_sub_wcf" value="<?php echo $current_user->user_email; ?>"></p>
            </div>
            <?php
        }
    }

    public function wp_add_plugin_userfn() {
        $email_id = (isset($_POST["email_id"]) && !empty($_POST["email_id"])) ? $_POST["email_id"] : '';
        $log_url = $_SERVER['HTTP_HOST'];
        $cur_date = date('Y-m-d');
        $request_url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/wp-add-plugin-users.php';
        if (!empty($email_id)) {
            $request_response = wp_remote_post($request_url, array('method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array('user' => array('plugin_id' => '4', 'user_email' => $email_id, 'plugin_site' => $log_url, 'status' => 1, 'activation_date' => $cur_date)),
                'cookies' => array()));
            update_option('wcf_plugin_notice_shown', 'true');
        }
    }

    // Function for welocme screen page

    public function welcome_woocommerce_digital_goods_screen_do_activation_redirect() {

        if (!get_transient('_welcome_screen_digital_goods_activation_redirect_data')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('_welcome_screen_digital_goods_activation_redirect_data');

        // if activating from network, or bulk
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        // Redirect to extra cost welcome  page
        wp_safe_redirect(add_query_arg(array('page' => 'woo-checkout-fields-about&tab=about'), admin_url('index.php')));
    }

    public function welcome_pages_screen_woocommerce_digital_counter() {
        add_dashboard_page(
                'Woocommerce-Checkout-For-Digital-Goods Dashboard', 'Woocommerce Checkout For Digital Goods Dashboard', 'read', 'woo-checkout-fields-about', array(&$this, 'welcome_screen_content_woocommerce_digital_counter')
        );
    }

    public function welcome_screen_content_woocommerce_digital_counter() {
        ?>

        <div class="wrap about-wrap">
            <h1 style="font-size: 2.1em;"><?php printf(__('Welcome to Woocommerce Checkout For Digital Goods', 'page-visit-counter')); ?></h1>

            <div class="about-text woocommerce-about-text">
                <?php
                $message = '';
                printf(__('%s This plugin will remove billing address fields for downloadable and virtual products.', 'page-visit-counter'), $message, $this->version);
                ?>
                <img class="version_logo_img" src="<?php echo plugin_dir_url(__FILE__) . 'images/woo-checkout-for-digital-goods.png'; ?>">
            </div>

            <?php
            $setting_tabs_wc = apply_filters('woo_checkout_fields_setting_tab', array("about" => "Overview", "other_plugins" => "Checkout our other plugins"));
            $current_tab_wc = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
            $aboutpage = isset($_GET['page'])
            ?>
            <h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
                <?php
                foreach ($setting_tabs_wc as $name => $label)
                    echo '<a  href="' . home_url('wp-admin/index.php?page=woo-checkout-fields-about&tab=' . $name) . '" class="nav-tab ' . ( $current_tab_wc == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
                ?>
            </h2>

            <?php
            foreach ($setting_tabs_wc as $setting_tabkey_wc => $setting_tabvalue) {
                switch ($setting_tabkey_wc) {
                    case $current_tab_wc:
                        do_action('woocommerce_digital_goods_' . $current_tab_wc);
                        break;
                }
            }
            ?>
            <hr />
            <div class="return-to-dashboard">
                <a href="<?php echo home_url('/wp-admin/admin.php?page=woo-checkout-fields'); ?>"><?php _e('Go to Woocommerce Checkout For Digital Settings', 'woo-checkout-fields'); ?></a>
            </div>
        </div>






        <?php
    }

    /**
     * Extra flate rate overview welcome page content function
     *
     */
    public function woocommerce_digital_goods_about() {
        //do_action('my_own');
        $current_user = wp_get_current_user();
        ?>
        <div class="changelog">
            </br>
            <style type="text/css">
                p.digital_goods_overview {max-width: 100% !important;margin-left: auto;margin-right: auto;font-size: 15px;line-height: 1.5;}.Digital_Goods_Content_ul ul li {margin-left: 3%;list-style: initial;line-height: 23px;}
            </style>  
            <div class="changelog about-integrations">
                <div class="wc-feature feature-section col three-col">
                    <div>
                        <p class="digital_goods_overview"><?php _e('WooCommerce Checkout for Digital Goods allows you to skip the unnecessary fields so order can place faster. If you are selling downloadable digital products then you might not need customer`s billing and shipping address and customers expect to get the product as quickly as possible. This plugin helps to remove unnecessary fields from checkout page and make process smooth and easy for customer. No settings required just activate and that`s it.', 'woo-checkout-fields'); ?></p>

                        <p class="digital_goods_overview"><strong>Plugin Functionality: </strong></p>  
                        <div class="Digital_Goods_Content_ul">
                            <ul>
                                <li>Removes Billing and Shipping address fields for downloadable digital goods.</li>
                            </ul>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <?php
        if (!get_option('wcf_plugin_notice_shown')) {
            ?>
            <div id="wcf_dialog">
                <p><?php _e('Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free!'); ?></p>
                <p><input type="text" id="txt_user_sub_wcf" class="regular-text" name="txt_user_sub_wcf" value="<?php echo $current_user->user_email; ?>"></p>
            </div>
            <?php
        }
    }

    public function woocommerce_digital_goods_other_plugins() {
        global $wpdb;
        $url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/checkout_other_plugin.php';
        $response = wp_remote_post($url, array('method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array('plugin' => 'advance-flat-rate-shipping-method-for-woocommerce'),
            'cookies' => array()));

        $response_new = array();
        $response_new = json_decode($response['body']);
        $get_other_plugin = maybe_unserialize($response_new);

        $paid_arr = array();
        ?>

        <div class="plug-containter">
            <div class="paid_plugin">
                <h3>Paid Plugins</h3>
                <?php
                foreach ($get_other_plugin as $key => $val) {
                    if ($val['plugindesc'] == 'paid') {
                        ?>


                        <div class="contain-section">
                            <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
                            <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl']; ?>"><?php echo $key; ?></a></div>
                        </div>	


                        <?php
                    } else {

                        $paid_arry[$key]['plugindesc'] = $val['plugindesc'];
                        $paid_arry[$key]['pluginimage'] = $val['pluginimage'];
                        $paid_arry[$key]['pluginurl'] = $val['pluginurl'];
                        $paid_arry[$key]['pluginname'] = $val['pluginname'];
                        ?>


                        <?php
                    }
                }
                ?>
            </div>
            <?php if (isset($paid_arry) && !empty($paid_arry)) { ?>
                <div class="free_plugin">
                    <h3>Free Plugins</h3>
                    <?php foreach ($paid_arry as $key => $val) { ?>  	
                        <div class="contain-section">
                            <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
                            <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl']; ?>"><?php echo $key; ?></a></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

        </div>

        <?php
    }

    /**
     * Remove the Extra flate rate menu in dashboard
     *
     */
    public function welcome_screen_digital_goods_remove_menus() {
        remove_submenu_page('index.php', 'woo-checkout-fields-about');
    }

    /**
     * Function For display admin side notice 
     *
     * 
     */
    public function custom_woo_digital_goods_pointers_footer() {
        $admin_pointers = custom_woo_digital_goods_admin_pointers();
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            (function($) {
        <?php
        foreach ($admin_pointers as $pointer => $array) {
            if ($array['active']) {
                ?>
                        $('<?php echo $array['anchor_id']; ?>').pointer({
                            content: '<?php echo $array['content']; ?>',
                            position: {
                                edge: '<?php echo $array['edge']; ?>',
                                align: '<?php echo $array['align']; ?>'
                            },
                            close: function() {
                                $.post(ajaxurl, {
                                    pointer: '<?php echo $pointer; ?>',
                                    action: 'dismiss-wp-pointer'
                                });
                            }
                        }).pointer('open');
                <?php
            }
        }
        ?>
            })(jQuery);
            /* ]]> */
        </script>
        <?php
    }

}

function custom_woo_digital_goods_admin_pointers() {

    $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
    $version = '1_0'; // replace all periods in 1.0 with an underscore
    $prefix = 'custom_woo_digital_goods_admin_pointers' . $version . '_';

    $new_pointer_content = '<h3>' . __('Woo-Checkout-For-Digital-Goods') . '</h3>';
    $new_pointer_content .= '<p>' . __('This plugin will remove billing address fields for downloadable and virtual products.') . '</p>';

    return array(
        $prefix . 'woo_digital_goods_notice_view' => array(
            'content' => $new_pointer_content,
            'anchor_id' => '#toplevel_page_woo-checkout-fields',
            'edge' => 'left',
            'align' => 'left',
            'active' => (!in_array($prefix . 'woo_digital_goods_notice_view', $dismissed) )
        )
    );
}
