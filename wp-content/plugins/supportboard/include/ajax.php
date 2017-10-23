<?php
/*
 * ======================================
 * SUPPORT BOARD - AJAX FUNCTIONS FILE
 * ======================================
 *
 * These functions are called via Javascript from main.js file
 */

function sb_ajax_save_option() {
    echo update_option($_POST['option_name'], $_POST['content']);
    die();
}
function sb_ajax_login() {
    session_start();
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")),true);
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            if ($users_arr[$i]["username"] == $_POST['user'] || $users_arr[$i]["email"] == $_POST['user']) {
                if ($users_arr[$i]["psw"] == $_POST['password']) {
                    $_SESSION['sb-login'] = encryptor("encrypt", "sb-logged-in-" . rand());
                    $_SESSION['sb-user-infos'] = $users_arr[$i];
                    die("success");
                }
            }
        }
    }
    die("error");
}
function sb_ajax_logout() {
    session_start();
    global $sb_config;
    session_unset();
    if (sb_get($sb_config,"users-engine") == "wp") wp_logout();
    die("success");
}
function sb_ajax_register() {
    $img = "";
    $email = "";
    $extra1 = "";
    $extra2 = "";
    $extra3 = "";
    $extra4 = "";
    if(isset($_POST['img'])) $img = $_POST['img'];
    if(isset($_POST['email'])) $email = $_POST['email'];
    if(isset($_POST['extra1'])) $extra1 = $_POST['extra1'];
    if(isset($_POST['extra2'])) $extra2 = $_POST['extra2'];
    if(isset($_POST['extra3'])) $extra3 = $_POST['extra3'];
    if(isset($_POST['extra4'])) $extra4 = $_POST['extra4'];
    $result = sb_register_user($_POST['id'], $img, $_POST['username'], $_POST['psw'], $email, $extra1, $extra2, $extra3, $extra4);
    die($result);
}
function sb_register_user($id="", $img="", $username="", $psw="", $email="", $extra1="", $extra2="", $extra3="", $extra4="") {
    global $sb_config;
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            if ($users_arr[$i]["username"] == $username) {
                return "error-user-double";
            }
        }
    } else {
        $users_arr = array();
    }
    if ($img == "") {
        $img = SB_PLUGIN_URL . "/media/user-2.jpg";
    }
    $user = array("id" => $id, "img" => $img, "username" => $username, "psw" => $psw, "email" => $email);

    if (isset($sb_config)) {
        if ($sb_config["user-extra-1"] != "") $user["extra1"] = $extra1;
        if ($sb_config["user-extra-2"] != "") $user["extra2"] = $extra2;
        if ($sb_config["user-extra-3"] != "") $user["extra3"] = $extra3;
        if ($sb_config["user-extra-4"] != "") $user["extra4"] = $extra4;
    }
    $user["last-email"] = "-1";
    array_push($users_arr, $user);
    update_option("sb-users-arr",json_encode($users_arr));
    return "success";
}
function sb_ajax_update_user() {
    if (isset($_POST['id'])) {
        $username = "";
        $img = "";
        $email = "";
        $psw = "";
        $extra1 = "";
        $extra2 = "";
        $extra3 = "";
        $extra4 = "";
        $last_email = "";
        if(isset($_POST['username'])) $email = $_POST['username'];
        if(isset($_POST['img'])) $email = $_POST['img'];
        if(isset($_POST['email'])) $email = $_POST['email'];
        if(isset($_POST['psw'])) $email = $_POST['psw'];
        if(isset($_POST['extra1'])) $extra1 = $_POST['extra1'];
        if(isset($_POST['extra2'])) $extra2 = $_POST['extra2'];
        if(isset($_POST['extra3'])) $extra3 = $_POST['extra3'];
        if(isset($_POST['extra4'])) $extra4 = $_POST['extra4'];
        if(isset($_POST['last_email'])) $last_email = $_POST['last_email'];
        $result = sb_update_user($_POST['id'], $img, $username, $psw, $email, $extra1, $extra2, $extra3, $extra4, $last_email);
        die($result);
    } else {
        die("error_no_user_id");
    }
}
function sb_ajax_add_message() {
    session_start();
    global $sb_config;
    global $users_arr;
    global $agents_arr;
    $environment = "wp";
    if ($_POST['environment'] == "php") $environment = "php";

    if (!isset($users_arr)) {
        $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
    }
    if (!isset($agents_arr)) {
        $agents_arr = json_decode(str_replace('\\"','"', get_option("sb-agents-arr")), true);
    }
    $user_type = "user";
    if (isset($_POST['user_type']) && $_POST['user_type'] == "agent") $user_type = "agent";

    $costumer_id = "";
    if (!isset($_POST['costumer_id']) && isset($_SESSION['sb-user-infos'])) {
        $costumer_id = $_SESSION['sb-user-infos']['id'];
    } else {
        if (isset($_POST['costumer_id'])) {
            $costumer_id = $_POST['costumer_id'];
        }
    }

    $arr_conversation = sb_add_message($costumer_id, $_POST['msg'], $_POST['time'], $_POST['user_id'], $_POST['user_img'], $_POST['user_name'], $_POST['files']);

    //Api.ai
    if ($user_type == "user") {
        if (sb_get($sb_config,"bot-active")) {
            $response = sb_api_ai_message($_POST['msg'], $costumer_id, $_POST['sb_lang']);
            if ($response != "" && $response != false) {
                $response = json_decode($response, true);
                if ($response != "" && $response != false) {
                    try {
                        $msg = $response['result']['fulfillment']['displayText'];
                        if ($msg == null) {
                            $msg = $response['result']['fulfillment']['messages'][0]['speech'];
                        }
                        if ($msg == null) {
                            $msg = $response['result']['fulfillment']['speech'];
                        }
                        if ($msg != null) {
                            $files_arr = array();
                            //{files name|link name|link ...}
                            if (strpos($msg,"{files") > -1) {
                                $start = strpos($msg,"{files");
                                $end = strpos($msg,"}",$start);
                                $files = substr($msg, $start + 7, ($end - $start  - 7));
                                $files_arr = explode(" ",$files);
                                $msg = substr($msg, 0, $start);
                            }

                            $bot_img = sb_get($sb_config,"bot-img");
                            $bot_name = sb_get($sb_config,"bot-name");
                            if ($bot_img == "") $bot_img = SB_PLUGIN_URL . "/media/user-1.jpg";
                            if ($bot_name == "") $bot_name = "Agent";

                            $item = array("msg" => sb_parse_message($msg), "files" => $files_arr, "time" => $_POST['time'], "user_id" => "100000", "user_img" => $bot_img, "user_name" => $bot_name);
                            sb_add_message($costumer_id, $item["msg"], $_POST['time'], "100000", $bot_img, $bot_name, $files_arr);
                            die(json_encode(array("success-bot",$item),JSON_UNESCAPED_UNICODE));
                        }
                    }  catch (Exception $exception) { }
                }
            }
        }
    }

    //Notifications
    if (sb_get($sb_config,"notify-user-email") && !isset($_SESSION['sb-activity-email'])) {
        if ($user_type == "agent") {
            if ($users_arr != false) {
                $sendAllowed = sb_check_email_allowed($costumer_id);
                if ($sendAllowed) {
                    for ($i = 0; $i < count($users_arr); $i++) {
                        if ($users_arr[$i]["id"] == $costumer_id) {
                            if (isset($users_arr[$i]["email"])) {
                                $emails = sb_get_emails("Support", $_POST['msg'], sb_get_files_arr($_POST['files']), false, "user", $environment);
                                if (function_exists(wp_mail)) {
                                    wp_mail($users_arr[$i]["email"], $emails[0], $emails[1]);
                                } else {
                                    sb_send_email($users_arr[$i]["email"], $emails[0], $emails[1]);
                                }
                                $_SESSION['sb-activity-email'] = "yes";
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    if (sb_get($sb_config,"notify-agent-email") && !isset($_SESSION['sb-activity-email'])) {
        if ($user_type == "user" && isset($_SESSION['sb-user-infos'])) {
            if (isset($_SESSION['sb-user-infos']['email'])) {
                $emails = sb_get_emails($_POST['user_name'], $_POST['msg'], sb_get_files_arr($_POST['files']), false, "agent", $environment);
                $agent_id = "";
                $sendAllowed = sb_check_email_allowed('agent'.$costumer_id);
                for ($i = count($arr_conversation) - 1; $i > 0; $i--) {
                    $id = $arr_conversation[$i]["user_id"];
                    if ($id != $costumer_id && $id != "10000") {
                        $agent_id = $arr_conversation[$i]["user_id"];
                        break;
                    }
                }
                $agents_emails = "";
                for ($i = 0; $i < count($agents_arr); $i++)  {
                    if ($agent_id != "" && $agents_arr[$i]["id"] == $agent_id) {
                        $agents_emails = $agents_arr[$i]["email"];
                        $agents_arr[$i]["last_email"] = time();
                    }
                    if ($agent_id == "") $agents_emails .= $agents_arr[$i]["email"] . ",";
                }
                if ($sendAllowed) {
                    if ($agent_id == "" && $agents_emails != "") {
                        $agents_emails = substr($agents_emails,0,strlen($agents_emails) - 1);
                    }
                    $_SESSION['sb-temp'] = array($agents_emails, $emails[2], $emails[3]);
                    $_SESSION['sb-activity-email'] = "yes";
                }
            }
        }
    }

    die(json_encode(array("success","")));
}
function sb_ajax_read_messages() {
    session_start();
    $user_id = "";
    if (isset($_POST["user_id"])) $user_id = $_POST["user_id"];
    $tt = $_SESSION['sb-user-infos'];
    if ($user_id == "" && isset($_SESSION['sb-user-infos'])) $user_id = $_SESSION['sb-user-infos']['id'];
    if ($user_id != "") {
        $arr_conversation = get_option("sb-conversation-" . $user_id);
        if ($arr_conversation != false) {
            die(stripslashes($arr_conversation));
        }
        die("");
    }
    die("error");
}
function sb_ajax_delete_conversation() {
    if (isset($_POST['costumer_id'])) {
        delete_option("sb-conversation-" . $_POST['costumer_id']);
        die("success");
    }
    die("error");
}
function sb_ajax_get_tickets() {
    global $sb_config;
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
    if ($users_arr == false) $users_arr = array();
    if (sb_get($sb_config,"users-engine") == "wp") {
        $users = get_users();
        foreach ($users as $user) {
            array_push($users_arr,array("id" => $user->ID, "img" => get_avatar_url($user->ID), "username" => $user->user_login));
        }
    }
    $tickets_arr = array();
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            $tickets_user = json_decode(str_replace('\\"','"', get_option("sb-conversation-" . $users_arr[$i]["id"])), true);
            if ($tickets_user != false) {
                array_push($tickets_arr, array("id" => $users_arr[$i]["id"], "username" => $users_arr[$i]["username"], "img" =>  $users_arr[$i]["img"], "tickets" => $tickets_user[count($tickets_user) - 1]));
            }
        }
    }
    $tickets_arr = stripslashes(json_encode($tickets_arr,JSON_UNESCAPED_UNICODE));
    die($tickets_arr);
}
function sb_ajax_delete_all_tickets() {
    global $sb_config;
    $users_arr = json_decode(str_replace('\\"','"', get_option("sb-users-arr")), true);
    if ($users_arr == false) $users_arr = array();
    if (sb_get($sb_config,"users-engine") == "wp") {
        $users = get_users();
        foreach ($users as $user) {
            array_push($users_arr,array("id" => $user->ID, "img" => get_avatar_url($user->ID), "username" => $user->user_login));
        }
    }
    if ($users_arr != false) {
        for ($i = 0; $i < count($users_arr); $i++){
            delete_option("sb-conversation-" . $users_arr[$i]["id"]);
        }
    }
    die("success");
}
function sb_send_test_email() {
    $emails = sb_get_emails("Test", "This is a lorem ipsum message for the test email.", array());
    wp_mail($_POST['email'], $emails[0], $emails[1]);
    die("success");
}
function sb_send_async_email() {
    session_start();
    if (isset($_SESSION['sb-temp'])) {
        try {
            $session_arr = $_SESSION['sb-temp'];
            $_SESSION['sb-temp'] = null;
            if (!function_exists(wp_mail) || (isset($_POST["environment"]) && $_POST["environment"] == "php")) {
                sb_send_email($session_arr[0], $session_arr[1], $session_arr[2]);
            } else {
                wp_mail($session_arr[0], $session_arr[1], $session_arr[2]);
            }
        } catch (Exception $exception) { die("error"); }
    }
    die("success");
}
function sb_ajax_slack_send_message() {
    //{"ok":true,"access_token":"xoxp-220847440245-220642241523-220782015812-318fbb518374e4d11eb7503fac2a24572","scope":"identify,incoming-webhook,chat:write:user,identity.basic","user_id":"U6GHGW73FD","team_name":"Test","team_id":"T6G55XCY77","incoming_webhook":{"channel":"#general","channel_id":"C6GM1656E","configuration_url":"https:\/\/schiocco.slack.com\/services\/B6GJI8B7","url":"https:\/\/hooks.slack.com\/services\/B6GJI8B7\/B6GJI8B7\/B6GJI8B7"}}
    global $sb_config;
    global $sb_slack_channels_arr;
    $username = "Test User";
    $user_id = "test-user-1";
    $msg = $_POST["msg"];
    $user_img = "https://board.support/wp-content/plugins/supportboard/media/icon-sb.png";
    $is_bot = false;
    if (isset($_POST["user_name"])) $username = $_POST["user_name"];
    if (isset($_POST["user_id"])) $user_id = $_POST["user_id"];
    if (isset($_POST["user_img"])) $user_img = $_POST["user_img"];
    if (isset($_POST["is_bot"]) && $_POST["is_bot"] == "true") {
        $is_bot = true;
    }
    $token = $sb_config["slack-token"];
    $channel_id = $sb_config["slack-channel"];
    $result = "slack-not-active";

    if (sb_get($sb_config,"slack-active") && isset($token)) {
        //Channel
        $newChannel = true;
        if (!isset($sb_slack_channels_arr)) {
            $sb_slack_channels_arr = json_decode(get_option("sb-slack-channels"), true);
            if (isset($sb_slack_channels_arr)) {
                if (isset($sb_slack_channels_arr[$user_id]["channel_id"])) {
                    $channel_id = $sb_slack_channels_arr[$user_id]["channel_id"];
                    $newChannel = false;
                }
            } else {
                $sb_slack_channels_arr = array();
            }
        }
        if ($newChannel && !$is_bot) {
            $raw = sb_run_curl("https://slack.com/api/channels.create", array("token" => $token, "name" => $username));
            $arr = json_decode($raw, true);
            if (isset($arr["ok"])) {
                if ($arr["ok"] == "true") {
                    $channel_id = $arr["channel"]["id"];
                    $sb_slack_channels_arr[$user_id] = array("channel_id" => $channel_id,"channel_name" => $arr["channel"]["name"]);
                    update_option("sb-slack-channels",json_encode($sb_slack_channels_arr));
                } else {
                    if ($arr["error"] == "name_taken") {
                        $channel_name = str_replace(" ","-", strtolower($username));
                        $raw = sb_run_curl("https://slack.com/api/channels.list", array("token" => $token, "exclude_archived" => true, "exclude_members" => true));
                        $arr = json_decode($raw, true);
                        if (isset($arr["channels"])) {
                            foreach ($arr["channels"] as $value) {
                                if ($value["name"] == $channel_name) {
                                    $channel_id = $value["id"];
                                    $sb_slack_channels_arr[$user_id] = array("channel_id" => $channel_id,"channel_name" => $value["name"]);
                                    update_option("sb-slack-channels",json_encode($sb_slack_channels_arr));
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        //Message
        $data = array(
            "token" => $token,
            "channel" => $channel_id,
            "text" => $msg,
            "username" => $username,
            "bot_id" => "support-board",
            "icon_url" => $user_img,
            "as_user" => false
        );

        //Attachments - www.link.com|Title 1?www.link.com|Title 2 ...
        if (isset($_POST["files"])) {
            $arr = explode("?",$_POST["files"]);
            $json = "[";
            for ($i = 0; $i < count($arr); $i++) {
                $sub = explode("|", $arr[$i]);
                $json .= '{"title": "' . $sub[1] . '","title_link": "' .  $sub[0] . '"},';
            }
            $json = substr($json,0, strlen($json) - 1);
            $json .= "]";
            $data["attachments"] = $json;
        }

        $result = sb_run_curl("https://slack.com/api/chat.postMessage", $data);
    }
    die($result);
}
function sb_ajax_slack_get_users() {
    global $sb_config;
    if (isset($sb_config["slack-token"])) {
        $raw = sb_run_curl("https://slack.com/api/users.list", array("token" => $sb_config["slack-token"]));
        die($raw);
    }
    die("slack-not-active");
}
function sb_ajax_init_user() {
    session_start();
    if (!isset($_SESSION['sb-user-infos'])) {
        if (isset($_POST["user_name"]) && isset($_POST["user_id"]) && isset($_POST["user_img"])){
            $email = "";
            if ($_POST["user_email"]) $email = $_POST["user_email"];
            $_SESSION['sb-user-infos'] = array("id" => $_POST["user_id"], "img" => $_POST["user_img"], "username" => $_POST["user_name"], "email" => $email);
        }
    } else {
        die(json_encode($_SESSION['sb-user-infos']));
    }
    die("success");
}
?>
