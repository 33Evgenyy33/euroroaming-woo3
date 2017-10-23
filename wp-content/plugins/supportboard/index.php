<?php
/**
 * Plugin Name: Support Board
 * Plugin URI: https://board.support/
 * Description: Simple and fast WordPress support system.
 * Version: 1.2.1
 * Author: Schiocco
 * Author URI: http://schiocco.io/
 */


/**
 * --------------------------------------
 * MAIN VARIABLES
 * --------------------------------------
*/
session_start();
include("include/functions.php");
define("SB_PLUGIN_URL", plugins_url() . "/supportboard");
$sb_settings_string = get_option("sb-settings");
$users_arr_string = str_replace('\\"','"', get_option("sb-users-arr"));
$agents_arr_string = str_replace('\\"','"', get_option("sb-agents-arr"));
$sb_config = json_decode(str_replace('\\"','"', $sb_settings_string), true);
$users_arr = json_decode($users_arr_string, true);
$agents_arr = json_decode($agents_arr_string, true);


/**
 * --------------------------------------
 * LOAD SCRIPT AND CSS
 * --------------------------------------
 */
function sb_enqueue() {
    global $sb_config;
    wp_enqueue_style("sb-main-css", SB_PLUGIN_URL . "/include/main.css", array(), "1.2", "all");
    wp_add_inline_style('sb-main-css', sb_set_css());
    wp_enqueue_script("sb-main-js", SB_PLUGIN_URL . "/include/main.js", array("jquery"), "1.2");
    $url = admin_url('admin-ajax.php');
    if (sb_get($sb_config,"subdomains")) {
        $lan = "";
        if (defined("ICL_LANGUAGE_CODE")) $lan = ICL_LANGUAGE_CODE;
        elseif (function_exists("qtrans_getLanguage")) $lan = qtrans_getLanguage();
        elseif (isset($_SERVER['HTTP_X_GT_LANG'])) $lan = $_SERVER['HTTP_X_GT_LANG'];
        if ($lan != "") {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = str_replace("https://","",admin_url('admin-ajax.php'));
            $url = $protocol . $lan . "." . str_replace("http://", "", $url);
        }
    }
    wp_add_inline_script("sb-main-js", "var sb_ajax_url = '" . $url . "'; var sb_wp_url = '" . get_site_url() . "';\nvar sb_plugin_url_wp = '" . SB_PLUGIN_URL . "';");
    if (!sb_get($sb_config,"font-disable")) {
        wp_enqueue_style("sb-google-font", sb_get_fonts_url("Raleway:500,600"), array(), "1.0", "all");
    }
}
add_action('wp_enqueue_scripts', 'sb_enqueue');

function sb_enqueue_admin() {
    global $users_arr_string;
    global $agents_arr_string;

    wp_enqueue_style("sb-admin-css", SB_PLUGIN_URL . "/include/admin.css", array(), "1.2", "all");
    wp_enqueue_script("sb-admin-js", SB_PLUGIN_URL . "/include/admin.js", array("jquery"), "1.2");
    wp_enqueue_media();

    $wp_users_arr = "var sb_wp_users_arr = [";
    if ($users_arr_string != "" && $users_arr_string != false) $users_arr_string = "var sb_users_arr = '" . $users_arr_string . "';"; else $users_arr_string = "var sb_users_arr = '[]';";
    if ($agents_arr_string != "" && $agents_arr_string != false && strlen($agents_arr_string) > 5) {
        $agents_arr_string = " var sb_agents_arr = '" . $agents_arr_string . "';\n";
    } else {
        $user = wp_get_current_user();
        $arr_string = '[{"id":"10012504","img":"' . SB_PLUGIN_URL . '/media/user-1.jpg","username":"Support","email":"' . $user->user_email . '","wp_user_id":"' . $user->ID . '"}]';
        update_option("sb-agents-arr", $arr_string);
        $agents_arr_string = "var sb_agents_arr = '" . $arr_string . "';\n";
    }
    $users = get_users();
    foreach ($users as $user) {
        $wp_users_arr .= '["' . $user->ID . '","' . $user->user_login . '"],';
    }
    $wp_users_arr = substr($wp_users_arr, 0, strlen($wp_users_arr) - 1) . "];\n";
    wp_add_inline_script("sb-admin-js", "var sb_current_wp_user = '" . get_current_user_id() . "';\nvar sb_ajax_url = '" . admin_url('admin-ajax.php') . "';\nvar sb_plugin_url = '" . SB_PLUGIN_URL . "';\n" . $users_arr_string . $agents_arr_string . $wp_users_arr);
}
add_action('admin_enqueue_scripts', 'sb_enqueue_admin');

/**
 * --------------------------------------
 * ADMIN SIDE
 * --------------------------------------
 */
function sb_set_admin_menu() {
	add_menu_page('Support', 'Support', 'manage_options', 'support-board', 'sb_admin_page','dashicons-groups');
}
function sb_admin_page() {
    include("admin-panel.php");
}
add_action("admin_menu", "sb_set_admin_menu");

/**
 * --------------------------------------
 * SHORTCODE
 * --------------------------------------
 */
function sb_init_support_board($atts)  {
    global $sb_config;
    global $agents_arr;

    $html = "";
    $css = "";
    $arr = get_option("sb_settings");
    if ($arr != "" && $arr != false) $arr = explode("/",$arr);
    else $arr = "";
    extract(shortcode_atts(array(
            'id' => rand(),
            'user' => '',
            'type' => ''
    ), $atts));
    ob_start();
    if (!isset($atts["id"])) $atts["id"] = rand();
    if (!isset($atts["user"])) $atts["user"] = "";
    if (!isset($atts["type"])) $atts["type"] = "board";

    if (isset($sb_config) && $sb_config["users-engine"] == "wp") {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $_SESSION['sb-user-infos'] = array("id" => $user->ID, "img" => get_avatar_url($user->ID), "username" => $user->user_login, "email" => $user->user_email);
            include("board.php");
        } else {
            if ($atts["type"] != "chat" || sb_get($sb_config,"chat-visibility") != "all") {
                $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $login_url = sb_get($sb_config,"wp-login-url");
                if ($login_url == "") $login_url = get_site_url() . "/wp-login.php";
                echo '<script>document.location = "' . $login_url . '?redirect_to=' . $current_url . '"</script>';
            } else {
                include("board.php");
            }
        }
    } else {
        $isLogged = false;
        if (isset($_SESSION['sb-login']) && !empty($_SESSION['sb-login'])) {
            $session = encryptor("decrypt", $_SESSION['sb-login']);
            if (strpos($session,"sb-logged-in") > -1) {
                $isLogged = true;
            }
        }
        if ($isLogged || ($atts["type"] == "chat" && (sb_get($sb_config,"chat-visibility") == "all" || sb_get($sb_config,"chat-visibility") == ""))) {
            include("board.php");
        } else {
            include("login.php");
        }
    }

    $output = ob_get_clean();
    return $output;
}
add_shortcode('sb', 'sb_init_support_board');

/**
 * --------------------------------------
 * AJAX
 * --------------------------------------
 */
include("include/ajax.php");
add_action('wp_ajax_sb_ajax_save_option', 'sb_ajax_save_option');
add_action('wp_ajax_nopriv_sb_ajax_save_option', 'sb_ajax_save_option');
add_action('wp_ajax_sb_ajax_login', 'sb_ajax_login');
add_action('wp_ajax_nopriv_sb_ajax_login', 'sb_ajax_login');
add_action('wp_ajax_sb_ajax_logout', 'sb_ajax_logout');
add_action('wp_ajax_nopriv_sb_ajax_logout', 'sb_ajax_logout');
add_action('wp_ajax_sb_ajax_add_message', 'sb_ajax_add_message');
add_action('wp_ajax_nopriv_sb_ajax_add_message', 'sb_ajax_add_message');
add_action('wp_ajax_sb_ajax_read_messages', 'sb_ajax_read_messages');
add_action('wp_ajax_nopriv_sb_ajax_read_messages', 'sb_ajax_read_messages');
add_action('wp_ajax_sb_ajax_register', 'sb_ajax_register');
add_action('wp_ajax_nopriv_sb_ajax_register', 'sb_ajax_register');
add_action('wp_ajax_sb_ajax_update_user', 'sb_ajax_update_user');
add_action('wp_ajax_nopriv_sb_ajax_update_user', 'sb_ajax_update_user');
add_action('wp_ajax_sb_ajax_get_tickets', 'sb_ajax_get_tickets');
add_action('wp_ajax_nopriv_sb_ajax_get_tickets', 'sb_ajax_get_tickets');
add_action('wp_ajax_sb_ajax_delete_conversation', 'sb_ajax_delete_conversation');
add_action('wp_ajax_sb_send_test_email', 'sb_send_test_email');
add_action('wp_ajax_sb_send_async_email', 'sb_send_async_email');
add_action('wp_ajax_nopriv_sb_send_async_email', 'sb_send_async_email');
add_action('wp_ajax_sb_ajax_delete_all_tickets', 'sb_ajax_delete_all_tickets');
add_action('wp_ajax_sb_ajax_slack_send_message', 'sb_ajax_slack_send_message');
add_action('wp_ajax_nopriv_sb_ajax_slack_send_message', 'sb_ajax_slack_send_message');
add_action('wp_ajax_sb_ajax_slack_get_users', 'sb_ajax_slack_get_users');
add_action('wp_ajax_nopriv_sb_ajax_init_user', 'sb_ajax_init_user');

/**
 * --------------------------------------
 * LANGUAGES
 * --------------------------------------
 */
function sb_load_textdomain() {
	load_plugin_textdomain('sb', false, dirname(plugin_basename(__FILE__)) . '/lang' );
}
add_action('init', 'sb_load_textdomain');
