<?php
/**
 * Plugin Name: WooCommerce Point of Sale
 * Plugin URI: http://codecanyon.net/item/woocommerce-point-of-sale-pos/7869665&ref=actualityextensions/
 * Description: WooCommerce Point of Sale is an extension which allows you to place orders through a Point of Sale interface swiftly using the WooCommerce products and orders database. This extension is most suitable for retailers who have both an online and offline store.
 * Version: 3.2.6.17
 * Author: Actuality Extensions
 * Author URI: http://actualityextensions.com/
 * Tested up to: 4.7.2
 *
 * Text Domain: wc_point_of_sale
 * Domain Path: /lang/
 *
 * Copyright: (c) 2017 Actuality Extensions (info@actualityextensions.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Point-Of-Sale
 * @author      Actuality Extensions
 * @category    Plugin
 * @copyright   Copyright (c) 2017, Actuality Extensions
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (function_exists('is_multisite') && is_multisite()) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (!is_plugin_active('woocommerce/woocommerce.php'))
        return;
} else {
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
        return; // Check if WooCommerce is active
}

// Load plugin class files
require_once('includes/class-wc-pos.php');

require 'updater/updater.php';
global $aebaseapi;
$aebaseapi->add_product(__FILE__);
/**
 * Returns the main instance of WC_POS to prevent the need to use globals.
 *
 * @since    3.0.5
 * @return object WC_POS
 */
add_filter('woocommerce_stock_amount', 'floatval', 1);
function WC_POS()
{
    $instance = WC_POS::instance(__FILE__, '3.2.6.9');
    return $instance;
}

// Global for backwards compatibility.
global $wc_point_of_sale, $wc_pos_db_version;

$wc_pos_db_version = WC_POS()->db_version;
$wc_point_of_sale = WC_POS();
$GLOBALS['wc_pos'] = WC_POS();


// Добавление metabox загранпаспорта в заказ
add_action('add_meta_boxes', 'mv_add_meta_boxes');
if (!function_exists('mv_add_meta_boxes')) {
    function mv_add_meta_boxes()
    {
        global $woocommerce, $order, $post;

        add_meta_box('mv_other_fields', 'Загранпаспорта', 'mv_add_other_fields_for_packaging', 'shop_order', 'side', 'core');
    }
}

// добавление в metabox загранпаспорта заказа ссылок на файлы
if (!function_exists('mv_save_wc_order_other_fields')) {
    function mv_add_other_fields_for_packaging()
    {
        global $woocommerce, $order, $post;

        $meta_field_data = get_post_meta($post->ID, 'uploaded_files', true); //? get_post_meta( $post->ID, '_my_choice', true ) : '';

        if (empty($meta_field_data)) return;

        //echo get_post_meta($post->ID, 'uploaded_files', true);

        echo '<input type="hidden" name="mv_other_meta_field_nonce" value="' . wp_create_nonce() . '">';

        $html = '';
        $urld = explode(",", $meta_field_data);
        $i = 1;
        foreach ($urld as $datum) {
            $html .= '<p><a target="_blank" href="' . $datum . '">Скан ' . $i . '</a></p>';
            $i++;
        }
        // print_r($meta_field_data[0]);
        echo $html;

    }
}

//Save the data of the Meta field
add_action('save_post', 'mv_save_wc_order_other_fields', 10, 1);
if (!function_exists('mv_save_wc_order_other_fields')) {

    function mv_save_wc_order_other_fields($post_id)
    {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if (!isset($_POST['mv_other_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['mv_other_meta_field_nonce'];

        //Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {

            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        update_post_meta($post_id, 'my_field_name', $_POST['my_field_name']);
    }
}


//
// Save the order meta with hidden field value
//
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta($order_id)
{
    $key = 'number_simcard';
    $order = get_post_meta($order_id, $key, false);
    add_post_meta($order_id, '_my_choice', $order);

}


add_action('wp_ajax_nopriv_submit_dropzonejs', 'dropzonejs_upload', 10, 1); //allow on front-end
add_action('wp_ajax_submit_dropzonejs', 'dropzonejs_upload', 10, 1);
function dropzonejs_upload()
{

    if (!empty($_FILES) && wp_verify_nonce($_REQUEST['my_nonce_field'], 'protect_content')) {

        $cyr = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у',
            'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У',
            'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
        $lat = array('a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
            'f', 'h', 'ts', 'ch', 'sh', 'sht', 'a', 'i', 'y', 'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Zh',
            'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U',
            'F', 'H', 'Ts', 'Ch', 'Sh', 'Sht', 'A', 'Y', 'Yu', 'Ya');

        /*$uploaded_bits = wp_upload_bits(
            str_replace($cyr, $lat, $_FILES['file']['name'][0]),
            null, //deprecated
            file_get_contents($_FILES['file']['tmp_name'][0])
        );
        print_r($uploaded_bits['url']);*/

        $tmp_name = $_FILES["file"]["tmp_name"][0];

        $unic_id_for_file = uniqid('pass_');
        $transform_name = str_replace(",", "",str_replace(" ", "-", str_replace($cyr, $lat, $unic_id_for_file.'-'.$_FILES["file"]["name"][0])));
        $name = basename($transform_name);

        $upload_dir = wp_get_upload_dir()['basedir'];
        $passport_dir = "$upload_dir/passports-from-tourist";

        wp_mkdir_p($passport_dir);

        move_uploaded_file($tmp_name, "$passport_dir/$name");

        $upload_url = wp_get_upload_dir()['baseurl'];
        $passport_url = "$upload_url/passports-from-tourist/$name";

        print_r($passport_url);
    }

    wp_die();
}

add_action('wp_ajax_nopriv_remove_dropzonejs_file', 'dropzonejs_remove', 10, 1); //allow on front-end
add_action('wp_ajax_remove_dropzonejs_file', 'dropzonejs_remove', 10, 1);
function dropzonejs_remove(){
    $whatever = $_POST['whatever'];
    //$whatever += 10;

    $upload_dir = wp_get_upload_dir()['basedir'];
    $passport_url = "$upload_dir/passports-from-tourist/$whatever";
    unlink($passport_url);
    print_r($whatever);

    wp_die();
}

// ADDING COLUMN TITLES (Here 2 columns)
//add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 11);
//function custom_shop_order_column($columns)
//{
//    //add columns
//    $columns['my-column1'] = __('Column Title', 'theme_slug');
//    return $columns;
//}
//
//// adding the data for each orders by column (example)
//add_action('manage_shop_order_posts_custom_column', 'cbsp_credit_details', 10, 2);
//function cbsp_credit_details($column)
//{
//    global $post, $woocommerce, $the_order;
//    $order_id = $the_order->id;
//
//
//    if ($column == 'my-column1') {
//        $key = 'uploaded_files';
//        $myVarOne = get_post_meta( $order_id, $key, false );
//        if (!isset($myVarOne) || empty($myVarOne)) return;
//        $urld = explode(",", $myVarOne[0]);
//        $html = '';
//        $i = 1;
//        foreach ($urld as $datum) {
//            $html .= '<p><a target="_blank" href="' . $datum . '">Скан ' . $i . '</a></p>';
//            $i++;
//        }
//        echo $html;
//    }
//
//
//}