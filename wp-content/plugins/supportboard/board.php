<?php
/**
 * ======================================
 * MAIN CONTENT - CHAD AND DESK
 * ======================================
 *
 * Support Board chat and desk contents.
 */

$user_arr = array();
$user_name = "";
$user_arr_json = "";
$is_wp = ((sb_get($sb_config,"users-engine")  == "wp") ? true : false);

$css_global = "";
if (sb_get($sb_config,"rtl")) $css_global .= " sb-rtl";
if (sb_get($sb_config,"push-notifications") != "users" || sb_get($sb_config,"push-notifications") != "all") $css_global .= " sb-push";
if (sb_get($sb_config,"flash-notifications")) $css_global .= " sb-flash";

$css_board = "";
if (!sb_get($sb_config,"user-img")) $css_board = "sb-no-profile";
if (sb_get($sb_config,"hide-message-time") && $atts["type"] != "chat") $css_board .= " sb-no-time";

$css_chat = "";
if ($atts["type"] == "chat") {
    if (sb_get($sb_config,"hide-chat-time")) $css_chat = "sb-no-time";
    if (sb_get($sb_config,"chat-avatars")) $css_chat .= " sb-avatars";
}

$style_board = "";
if (sb_get($sb_config,"width") != "") $style = "width:" . sb_get($sb_config,"width") . "px;";

if (isset($_SESSION['sb-user-infos'])) {
    $user_arr = $_SESSION['sb-user-infos'];
    $user_name = $user_arr["username"];
    if ($is_wp) $user_name = sb_get_user_name($user_arr);
    $user_arr_json = json_encode(array("id" => $user_arr["id"],"img" => $user_arr["img"],"name" => $user_name,"username" => $user_arr["username"]));
    if (!strpos($user_name,"@") > 0) $user_name = ucwords($user_name);
}

$scrollbox = "";
if (sb_get($sb_config,"scrollbox-active")) {
    $scrollbox = 'data-scroll="true" data-height="';
    $height = sb_get($sb_config,"scrollbox-height");
    $offset = sb_get($sb_config,"scrollbox-offset");
    $options = sb_get($sb_config,"scrollbox-options");
    if ($height == "") $height = 350;
    $scrollbox .=  $height . '"';
    if ($offset != "") $scrollbox .= ' data-offset="' . $offset . '"';
    if ($options != "") $scrollbox .= ' data-options="' . $options . '"';
}

$attr = "";
if ($atts["type"] == "chat") {
    if (sb_get($sb_config,"chat-sound")) {
        $attr = 'data-sound="true"';
        echo '<audio id="sb-audio" preload><source src="' . SB_PLUGIN_URL . '/media/sound.mp3" type="audio/mpeg"></audio>';
    }
    if (sb_get($sb_config,"welcome-active")) {
        $attr .= ' data-welcome="' . sb_get($sb_config,"welcome-msg") . '"';
        if (sb_get($sb_config,"welcome-img") != "") {
            $attr .= ' data-welcome-img="' . sb_get($sb_config,"welcome-img") . '"';
        } else {
            $attr .= ' data-welcome-img="' . SB_PLUGIN_URL . '/media/user-2.jpg"';
        }
        if (sb_get($sb_config,"welcome-always") != "") $css_global .= " welcome-always";
    }
    if (sb_get($sb_config,"follow-active")) {
        $attr .= ' data-follow="true"';
    }
}

if (sb_get($sb_config,"slack-token") != "") {
    $attr .= ' data-slack="true"';
}
if (sb_get($sb_config,"flash-notifications-text") != "") {
    $attr .= ' data-flash="' . $sb_config["flash-notifications-text"] . '"';
}
if (sb_get($sb_config,"agent-subtitle") != "") {
    $attr .= ' data-agent="' . $sb_config["agent-subtitle"] . '"';
}

$chat_icon = sb_get($sb_config,"chat-icon"); SB_PLUGIN_URL . "/media/chat.svg";
if ($chat_icon == "" || $chat_icon == (SB_PLUGIN_URL . "/media/chat-2.svg")) {
    $chat_icon = SB_PLUGIN_URL . "/media/chat.svg";
}
?>

<div id="sb-main" class="sb-main <?php echo $css_global ?>" <?php echo $attr ?> style="display:none">
    <?php if ($atts["type"] == "board") { ?>
    <div class="sb-cnt-global sb-cnt <?php echo $css_board ?>" style="<?php echo $style_board ?>">
        <div class="sb-header">
            <div class="sb-title">
                <img class="sb-header-img" src="<?php echo $user_arr["img"] ?>" />
                <?php echo $user_name ?>
            </div>
            <div class="sb-btn sb-logout">
                <?php _e("Logout","sb") ?>
            </div>
        </div>
        <div class="sb-list" <?php echo $scrollbox ?>>
            <img class="sb-list-loader" src="<?php echo SB_PLUGIN_URL . "/media/loader.svg" ?>" alt="" />
            <div class="sb-list-msg">
                <?php _e("Create a new ticket by write your support request from the form below.","sb") ?>
            </div>
        </div>
        <div class="sb-clear"></div>
        <?php sb_get_editor() ?>
    </div>
    <?php } ?>
    <?php if ($atts["type"] == "chat") { ?>
    <div class="sb-cnt-global sb-chat-cnt <?php echo $css_chat ?>">
        <div class="sb-chat">
            <div class="sb-chat-header">
                <div class="sb-header-title">
                    <?php echo ((sb_get($sb_config,"chat-header") != "") ? sb_get($sb_config,"chat-title") : "Support Board Chat") ?>
                </div>
                <div class="sb-header-text">
                    <?php echo ((sb_get($sb_config,"chat-header") != "") ? sb_get($sb_config,"chat-header") : "") ?>
                </div>
                <?php
              if (sb_get($sb_config,"chat-avatars")) {
                  if (isset($agents_arr)) {
                      $c = count($agents_arr);
                      $html = "";
                      if ($c > 3) $c = 3;
                      for ($i = 0; $i < $c; $i++)  {
                          $html .= '<div><img src="' . $agents_arr[$i]["img"] . '" /><span>' . $agents_arr[$i]["username"] . '</span></div>';
                      }
                      if ($html != "") {
                          echo '<div class="sb-header-avatars">' . $html . '</div>';
                      }
                  }
              }
                ?>
            </div>
            <div class="sb-chat-list">
                <img class="sb-chat-list-loader" src="<?php echo SB_PLUGIN_URL . "/media/loader.svg" ?>" alt="" />
            </div>
            <div class="sb-clear"></div>
            <div class="sb-chat-editor">
                <?php sb_get_editor() ?>
            </div>
        </div>
        <div class="sb-chat-btn">
            <img class="sb-chat-icon" alt="" src="<?php echo $chat_icon ?>" />
            <img class="sb-close-icon" alt="" src="<?php echo SB_PLUGIN_URL . "/media/close.svg" ?>" />
        </div>
    </div>
    <?php
          }
          if (sb_get($sb_config,"follow-active")) { ?>
    <div id="sb-card-contacts-cnt">
        <div class="sb-card sb-card-exclude sb-card-contacts">
            <div class="sb-card-cnt">
                <div class="sb-message">
                    <?php echo ((sb_get($sb_config,"follow-msg") != "") ? sb_get($sb_config,"follow-msg") : "Get notified when we reply or contact us from Facebook Messenger or Whatsapp.") ?>
                </div>
                <div class="sb-contacts">
                    <?php _e("Email","sb") ?>
                    <div class="sb-email-cnt">
                        <input type="email" placeholder="name@email.com" value="" />
                        <div class="sb-btn-email"></div>
                        <div class="sb-error-msg">
                            <?php _e("Email is not valid","sb") ?>
                        </div>
                        <div class="sb-success-msg">
                            <?php _e("You'll be notified here and by email.","sb") ?>
                        </div>
                    </div>
                    <?php echo ((sb_get($sb_config,"follow-fb") != "") ? '<a target="_blank" class="sb-fb" href="' . sb_get($sb_config,"follow-fb") . '"></a>':'') ?>
                    <?php echo ((sb_get($sb_config,"follow-wa") != "") ? '<a target="_blank" class="sb-wa" href="' . sb_get($sb_config,"follow-wa") . '"></a>':'') ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
