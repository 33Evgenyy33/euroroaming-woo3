<?php
/**
 * ======================================
 * SLACK - DIRECT MESSAGE SYSTEM
 * ======================================
 *
 * This file receive the messages sent from Slack and process them for save it into the database and for show them to the user's chat or desk
 *
 * ONLY TEXT - RESPONSE EXAMPLE
 * $json = '{"token":"GUwjUFxF9V8tvtKKG4C5IKzJ","team_id":"T6QHRMS9G","api_app_id":"A6GR47Q9K","event":{"type":"message","user":"U6QHRMSHY","text":"merdina","ts":"1503387726.000202","channel":"C6RK0B3A5","event_ts":"1503387726.000202"},"type":"event_callback","authed_users":["U6QHRMSHY"],"event_id":"Ev6R8UHBQ9","event_time":1503387726}';
 *
 * TEXT AND ATTACHMENTS - RESPONSE EXAMPLE
 * $json = '{"token":"GUwjUFxF9V8tvtKKG4C5IKzJ","team_id":"T6QHRMS9G","api_app_id":"A6GR47Q9K","event":{"type":"message","subtype":"file_share","text":"<@U6QHRMSHY|fede> uploaded a file: <https:\/\/schio.slack.com\/files\/fede\/F6TUB2278\/documentation.pdf|Femmina>","file":{"id":"F6TUB2278","created":1503566243,"timestamp":1503566243,"name":"documentation.pdf","title":"My file","mimetype":"application\/pdf","filetype":"pdf","pretty_type":"PDF","user":"U6QHRMSHY","editable":false,"size":68668,"mode":"hosted","is_external":false,"external_type":"","is_public":true,"public_url_shared":false,"display_as_bot":false,"username":"","url_private":"https:\/\/files.slack.com\/files-pri\/T6QHRMS9G-F6TUB2278\/documentation.pdf","url_private_download":"https:\/\/files.slack.com\/files-pri\/T6QHRMS9G-F6TUB2278\/download\/documentation.pdf","thumb_pdf":"https:\/\/files.slack.com\/files-tmb\/T6QHRMS9G-F6TUB2278-0927808f39\/documentation_thumb_pdf.png","thumb_pdf_w":910,"thumb_pdf_h":1286,"permalink":"https:\/\/schio.slack.com\/files\/fede\/F6TUB2278\/documentation.pdf","permalink_public":"https:\/\/slack-files.com\/T6QHRMS9G-F6TUB2278-6186848098","channels":["C6T3ULVJQ"],"groups":[],"ims":[],"comments_count":0},"user":"U6QHRMSHY","upload":true,"display_as_bot":false,"username":"fede","bot_id":null,"ts":"1503566244.000388","channel":"C6T3ULVJQ","event_ts":"1503566244.000388"},"type":"event_callback","authed_users":["U6QHRMSHY"],"event_id":"Ev6SFVTUQH","event_time":1503566244}';
 */

$is_php = true;
global $sb_config;

if (file_exists("../../../../wp-load.php")) {
    require_once("../../../../wp-load.php");
    $is_php = false;
} else {
    require_once("../php/functions.php");
}
require_once("../include/functions.php");

header('Content-Type: application/json');
ob_start();
$json = file_get_contents('php://input');
$arr_slack = json_decode($json, true);
ob_end_clean();

$result = "error arr_slack";
$subtype;
if (isset($arr_slack["event"]["subtype"])) {
    $subtype = $arr_slack["event"]["subtype"];
}
if (isset($arr_slack["event"]["type"]) && $arr_slack["event"]["type"] == "message" && (!isset($subtype) || $subtype == "file_share") && $arr_slack["event"]["text"] != "") {
    $sb_slack_channels_arr = json_decode(get_option("sb-slack-channels"), true);
    $result = "error sb_slack_channels_arr";
    if (isset($sb_slack_channels_arr)) {
        $channel_id = $arr_slack["event"]["channel"];
        $user_id;
        foreach ($sb_slack_channels_arr as $key => $value) {
            if ($value["channel_id"] == $channel_id) {
                $user_id = $key;
                break;
            }
        }
        $result = "error user_id";
        if (isset($user_id)) {
            $sb_config;
            $msg = $arr_slack["event"]["text"];
            $file_string = "";
            $file_email = array();
            if ($subtype == "file_share") {
                $file_id = $arr_slack["event"]["file"]["id"];
                $sb_config = json_decode(str_replace('\\"','"', get_option("sb-settings")), true);
                $raw = sb_run_curl("https://slack.com/api/files.sharedPublicURL", array("token" => $sb_config["slack-token"], "file" => $file_id));
                $arr = json_decode($raw, true);
                $link = "";
                if (isset($arr["file"])) {
                    $link = $arr["file"]["permalink_public"];
                }
                $file_string = $link . "|" . $arr_slack["event"]["file"]["title"];
                array_push($file_email, $link);
                $msg = "";
            }

            $agent = sb_get_agent($arr_slack["event"]["user"], "forced");
            sb_add_message($user_id, $msg, "unix" . time(), $agent["id"], $agent['img'], $agent['username'], $file_string);
            $result = "success";

            //Notifications
            $sendAllowed = sb_check_email_allowed($user_id);
            if ($sendAllowed) {
                if (!isset($sb_config)) {
                    $sb_config = json_decode(str_replace('\\"','"', get_option("sb-settings")), true);
                }
                $user = sb_get_user($user_id);
                if (isset($sb_config["notify-user-email"]) && isset($user["email"])) {
                    if ($user["email"] != "") {
                        $emails = sb_get_emails($agent['username'], $msg, $file_email, false, "user");
                        wp_mail($user["email"], $emails[0], $emails[1]);
                    }
                }
            }
        }
    }
}

//$file = fopen("debug.txt","w");
//fwrite($file, $json)  or die("Unable to open file!");
?>
