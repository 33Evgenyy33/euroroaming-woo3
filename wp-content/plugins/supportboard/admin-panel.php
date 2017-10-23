<?php
/**
 * ======================================
 * WORDPRESS ADMIN PAGE
 * ======================================
 *
 * Support Board page content of WordPress administration. 
 * This page contain all the administration contents: tickets, users, agens, settings, email
 */

global $sb_settings_string;
global $sb_config;
$emails_arr = sb_get_emails("","",array(),true);

function sb_set_bot_multilanguage() {
    if (function_exists('icl_object_id')) {
        $langs = icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str');
        $langs = array_reverse($langs);
        $i = 0;
        foreach ($langs as $item) {
            $lan_code = $item['language_code'];
            $prefix = "";
            if ($i > 0) $prefix = "-" .$lan_code;
            echo '<div class="item-input"><p><img src="' . $item['country_flag_url'] . '">' . $lan_code . '</p><input data-setting="bot-token' . $prefix . '" type="text" /> </div>';
            $i++;
        }
    
    } else {
        echo '<input data-setting="bot-token" type="text" />';
    }
}
if (sb_get($sb_config,"auth-not-admin") && !current_user_can('administrator')) {
    echo '<style> .tab-plugin ul li:not(:first-child),.tab-plugin .panel-plugin:not(.active) { display: none !important; }</style>';
}
?>
<div class="wrap">
    <h1>Support Board</h1>
    <div id="sb-admin" class="settings-cnt">
        <input type="hidden" name="save_array_json" id="save_array_json" value='<?php echo str_replace('\\"','"',$sb_settings_string) ?>' />
        <a class="sb-doc-link" href="https://board.support/docs/"><?php _e("Documentation","sb") ?></a>
        <div class="tab-plugin">
            <ul class="nav-plugin">
                <li class="active">
                    <a id="tab-tickets" href="#"><?php _e("Tickets","sb") ?></a>
                </li>
                <li>
                    <a id="tab-users" href="#"><?php _e("Users","sb") ?></a>
                </li>
                <li>
                    <a id="tab-agents" href="#"><?php _e("Agents","sb") ?></a>
                </li>
                <li>
                    <a id="tab-settings" href="#"><?php _e("Settings","sb") ?></a>
                </li>
                <li>
                    <a id="tab-emails" href="#"><?php _e("Emails","sb") ?></a>
                </li>
            </ul>
            <div class="panel-plugin active">
                <div class="sb-list sb-all-tickets">
                    <div class="sb-user-all-parent">
                        <div class="sb-btn sb-all-users-guests active"><?php _e("All","sb") ?></div>
                        <div class="sb-btn sb-all-users"><?php _e("Users","sb") ?></div>
                        <div class="sb-btn sb-all-guests"><?php _e("Guests","sb") ?></div>
                        <div class="sb-clear"></div>
                    </div>
                    <div class="sb-all-tickets-list">
                        <div class="sb-list-msg"><?php _e("You not have any ticket, when your users will require support the tickets will appear here.","sb") ?></div>
                    </div>
                </div>
                <div class="sb-list sb-user-tickets">
                    <img class="sb-loader" src="<?php echo SB_PLUGIN_URL . '/media/loader.svg' ?>" alt="" />
                    <div class="sb-user-tickets-parent">
                        <div class="sb-btn sb-user-tickets-back"><?php _e("Back to tickets list","sb") ?></div>
                        <div class="sb-btn-text sb-user-tickets-delete"><?php _e("Delete conversation","sb") ?></div>
                        <div class="sb-user-tickets-cnt"></div>
                        <?php sb_get_editor() ?>
                    </div>
                </div>
            </div>
            <div class="panel-plugin">
                <?php 
                if (sb_get($sb_config,"users-engine") == "wp") echo '<div class="sb-msg sb-msg-warning sb-show">' . __("You are using the WordPress users system so you must manage your users from the Users page. From the list below only the guests users of the chat are still active.","sb") . '</div>';
                ?>
                <table class="table-users sb-table wp-list-table widefat striped users fixed">
                    <thead>
                        <tr>
                            <td><?php _e("ID","sb") ?></td>
                            <td><?php _e("Username or Email","sb") ?></td> 
                            <td><?php _e("Email","sb") ?></td>
                            <td><?php _e("Password","sb") ?></td>
                            <?php
                            if (isset($sb_config)) {
                                if ($sb_config["user-extra-1"] != "") echo "<td>" . $sb_config["user-extra-1"] . "</td>";
                                if ($sb_config["user-extra-2"] != "") echo "<td>" . $sb_config["user-extra-2"] . "</td>";
                                if ($sb_config["user-extra-3"] != "") echo "<td>" . $sb_config["user-extra-3"] . "</td>";
                                if ($sb_config["user-extra-4"] != "") echo "<td>" . $sb_config["user-extra-4"] . "</td>";
                            }
                            ?>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <hr class="space s" />
                <a id="sb-btn-save" class="button button-primary"><?php _e("Save","sb") ?></a>
                <a id="sb-btn-add-new-user" class="button action"><?php _e("Add new user","sb") ?></a>
                <span class="sb-msg sb-msg-success"><?php _e("Saved","sb") ?></span>
                <span class="sb-msg sb-msg-error"><?php _e("Every user must have username and password.","sb") ?></span>
            </div>
            <div class="panel-plugin">
                <table class="table-agents sb-table wp-list-table widefat striped users fixed">
                    <thead>
                        <tr>
                            <td><?php _e("ID","sb") ?></td>
                            <td><?php _e("Username","sb") ?></td>
                            <td><?php _e("Email","sb") ?></td>
                            <td><?php _e("WordPress user","sb") ?></td>
                            <?php if (sb_get($sb_config,"slack-token") !== "") echo '<td class="slack-td">' . __("Slack user","sb") . '</td>' ?>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <hr class="space s" />
                <a id="sb-btn-save-agent" class="button button-primary"><?php _e("Save","sb") ?></a>
                <a id="sb-btn-add-new-agent" class="button action"><?php _e("Add new agent","sb") ?></a>
                <span class="sb-msg sb-msg-success-agent"><?php _e("Saved","sb") ?></span>
                <span class="sb-msg sb-msg-error-agent"><?php _e("Every user must have username and password.","sb") ?></span>
            </div>
            <div class="panel-plugin">
                <div class="panel-inner">
                    <h2><?php _e("General","sb") ?></h2>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Main color","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Set the main color.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="color-main" class="sb-color-picker" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Secondary color","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Set the secondary color.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="color-secondary" class="sb-color-picker" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Disable Google font","sb") ?>
                            </h4>
                            <p>
                                <?php _e("For performance reasons you can disable the Google font loaded by the plugin and save about 150kB.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="font-disable" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("RTL","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Active the Right-To-Left (RTL) layout.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="rtl" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Agent text","sb") ?>
                            </h4>
                            <p>
                                <?php _e("The text that will appear under the agents name.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="agent-subtitle" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Authorizations","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Show only the tickets to not admin users.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="auth-not-admin" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Sub domains","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Active this options if you have a multilingual website based on sub domains.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="subdomains" type="checkbox" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><?php _e("Registration","sb") ?></h2>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Users engine","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Select WordPress for use the original users engine of WordPress. The users will need to login into WordPress to access the support page.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <select data-setting="users-engine">
                                <option value="sb" selected>Support Board</option>
                                <option value="wp">WordPress</option>
                            </select>
                        </div>
                    </div>
                    <hr class="sb-wp-only" />
                    <div class="item-row sb-wp-only">
                        <div class="item-title">
                            <h4>
                                <?php _e("Login url","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Insert the WordPress login url.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="wp-login-url" type="text" />
                        </div>
                    </div>
                    <hr class="sb-only" />
                    <div class="item-row sb-only">
                        <div class="item-title">
                            <h4>
                                <?php _e("Fields","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Add extra fields to the registration form by set their names.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Extra field 1","sb") ?></p>
                                <input data-setting="user-extra-1" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Extra field 2","sb") ?></p>
                                <input data-setting="user-extra-2" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Extra field 3","sb") ?></p>
                                <input data-setting="user-extra-3" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Extra field 4","sb") ?></p>
                                <input data-setting="user-extra-4" type="text" />
                            </div>
                        </div>
                    </div>
                    <hr class="sb-only" />
                    <div class="item-row sb-only">
                        <div class="item-title">
                            <h4>
                                <?php _e("Email field","sb") ?>
                            </h4>
                            <p>
                                <?php _e("User must insert the email. This field is not showed for email username type.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="user-email" type="checkbox" />
                        </div>
                    </div>
                    <hr class="sb-only" />
                    <div class="item-row sb-only">
                        <div class="item-title">
                            <h4>
                                <?php _e("Username type","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Choose what the users must insert as username","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <select data-setting="username-type">
                                <option value="email"><?php _e("Email","sb") ?></option>
                                <option value="username"><?php _e("Username","sb") ?></option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Profile image","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Show the profile image to the conversation and allow the user to set their profile image on registration form.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="user-img" type="checkbox" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><?php _e("Support desk","sb") ?></h2>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Scroll box","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Active the scroll box, height can be the number of pixel or the string 'fullscreen', offset adjust the fullscreen height, options list can be found at <a target='_blank' href='http://rocha.la/jQuery-slimScroll'>rocha.la/jQuery-slimScroll</a>.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Active","sb") ?></p>
                                <input data-setting="scrollbox-active" type="checkbox" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Height","sb") ?></p>
                                <input data-setting="scrollbox-height" placeholder="123 or fullscreen" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Offset","sb") ?></p>
                                <input data-setting="scrollbox-offset" placeholder="123" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Options","sb") ?></p>
                                <input data-setting="scrollbox-options" placeholder="option:value,option:value..." type="text" />
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Hide time stamp","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Hide date and time of the messages.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="hide-message-time" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Width","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Set the width of the support panel.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="width" type="text" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><?php _e("Notifications","sb") ?></h2>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Agents emails","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Send a email to the agent everytime a user send a new message. If the ticket is from a new user the email is sent to all the agents otherwise is sent only to the last agent of the conversation. A maximum of 1 email every 1 hour is sent to the agent.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="notify-agent-email" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Users emails","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Send a email to the user when the agent reply. A maximum of 1 email every 1 hour is sent to the user.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="notify-user-email" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Push notifications","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Show push notifications to users and agents when a new message is received.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <select data-setting="push-notifications">
                                <option value="" selected><?php _e("Disabled","sb") ?></option>
                                <option value="all"><?php _e("Users and agents","sb") ?></option>
                                <option value="users"><?php _e("Users only","sb") ?></option>
                                <option value="agents"><?php _e("Agents only","sb") ?></option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Flash notifications","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Make browser tab flash a notification to users only when a new message is received.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Active","sb") ?></p>
                                <input data-setting="flash-notifications" type="checkbox" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Text","sb") ?></p>
                                <input data-setting="flash-notifications-text" type="text" />
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Test email","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Send a test email to the specified email address.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input id="test-email" placeholder="Email address" type="text" />
                            <a id="sb-btn-test-email" class="button action">Send email</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><?php _e("Chat","sb") ?></h2>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Visibility","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Choose when you want show the chat.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <select data-setting="chat-visibility">
                                <option value="all" selected><?php _e("Everyone","sb") ?></option>
                                <option value="logged"><?php _e("Logged in users only","sb") ?></option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Hide time stamp","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Hide date and time of the messages.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="hide-chat-time" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Sounds","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Play a sound notification when the user receive a new message.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="chat-sound" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Show profile image","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Show the profile image of the agents.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="chat-avatars" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Welcome message","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Write an automatic message to new users. The message is sent only to the new guest users.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Active","sb") ?></p>
                                <input data-setting="welcome-active" type="checkbox" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Show always","sb") ?></p>
                                <input data-setting="welcome-always" type="checkbox" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Message","sb") ?></p>
                                <input data-setting="welcome-msg" type="text" />
                            </div>
                            <div class="item-input item-input-img">
                                <p><?php _e("Profile image","sb") ?></p>
                                <img data-setting="welcome-img" class="sb-upload-img" src="<?php echo SB_PLUGIN_URL . '/media/user-1.jpg' ?>" alt="">
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Follow up message","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Write a message to guests users if no agents reply within 15 seconds. The message ask the user email and contain Facebook Messenger and Whatsapp Web links. You can send manually this message by type the command #follow.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Active","sb") ?></p>
                                <input data-setting="follow-active" type="checkbox" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Message","sb") ?></p>
                                <input data-setting="follow-msg" placeholder="Get notified when we reply or contact us from Facebook messenger or Whatsapp." type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Messenger link","sb") ?></p>
                                <input data-setting="follow-fb" placeholder="https://www.facebook.com/messages/t/company" type="text" />
                            </div>
                            <div class="item-input">
                                <p><?php _e("Whatsapp link","sb") ?></p>
                                <input data-setting="follow-wa" placeholder="https://web.whatsapp.com/send?&phone=1234656789" type="text" />
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Header title","sb") ?>
                            </h4>
                            <p>
                                <?php _e("The chat title, this text will be replaced by the agent name when the first reply is sent.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="chat-title" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Header text","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Write any text you want, this text will be replaced by the agent name when the first reply is sent.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="chat-header" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Show agents avatars","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Show the profile image of the agents on chat header. This content will be replaced by the agent name when the first reply is sent.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="chat-header-avatars" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Chat button icon","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Change the chat button image.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input item-input-img">
                                <img data-setting="chat-icon" class="sb-upload-img no-radius" src="<?php echo SB_PLUGIN_URL . '/media/chat-2.svg' ?>" alt="">
                                <br />
                                <div class="sb-upload-img-remove button" data-src="<?php echo SB_PLUGIN_URL . '/media/chat-2.svg' ?>"><?php _e("Restore original") ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><img class="brand-logo" src="<?php echo SB_PLUGIN_URL . "/media/slack.svg" ?>" /></h2>
                    <p><?php _e("","sb") ?></p>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Active","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Active the Slack integration.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="slack-active" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Start","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Click the button to start setting the Slack synchronization. Localhost not receive messages, only live domains. Login with another account or as guest to perform your tests.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <a href="https://board.support/slack/?customer_url=<?php echo SB_PLUGIN_URL ?>" target="_blank" class="button action"><?php _e("Start Slack synchronization","sb") ?></a>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Buy credits","sb") ?>
                            </h4>
                            <p>
                                <?php _e("You have 200 messages for free. Fees are applied only for sending messages from Slack to the chat. Buy new credits by click the button.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <a href="https://board.support/slack/prices/" target="_blank" class="button action"><?php _e("Buy Slack credits","sb") ?></a>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Access Token","sb") ?>
                            </h4>
                            <p>
                                <?php _e("You will get this code by complete the Slack synchronization above.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="slack-token" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Channel ID","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Your Slack channel ID. You will get this code by complete the Slack synchronization above.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="slack-channel" type="text" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Test Slack","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Send a test message to your Slack channel, this test check only send function, not read function.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <a id="sb-slack-test" class="button action">Send test message to Slack</a>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Reset","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Reset Slack settings. Use this option if something not work as expected. You will need to synchronize Slack again.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <a id="sb-slack-reset" class="button action">Reset Slack</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <h2><img class="brand-logo" src="<?php echo SB_PLUGIN_URL . "/media/api.ai.svg" ?>" /></h2>
                    <p><?php _e("Add a chat bot that will automatically answer to the support requests of your users, the chat bot system is powered by <a target='_blank' href='https://dialogflow.com/'>dialogflow.com</a>. For more information visit the <a target='_blank' href='https://board.support/docs'>documentation</a>.","sb") ?></p>
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Active","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Active the chat bot.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <input data-setting="bot-active" type="checkbox" />
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Developer access token","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Insert the developer access token of your Dialogflow bot. To use multilingual bots you must install the <a target='_blank' href='https://wpml.org/?aid=154204&affiliate_key=LgJiCRvycckb'>WPML</a> plugin","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting item-setting-lan">
                            <?php sb_set_bot_multilanguage() ?>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Main bot language","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Set the language of the main Dialogflow bot.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <select data-setting="bot-lan">
                                <option value="">--</option>
                                <option value="pt-BR">Brazilian Portuguese</option>
                                <option value="zh-HK">Chinese (Cantonese)</option>
                                <option value="zh-CN">Chinese (Simplified)</option>
                                <option value="zh-TW">Chinese (Traditional)</option>
                                <option value="en">English</option>
                                <option value="nl">Dutch</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="it">Italian</option>
                                <option value="ja">Japanese</option>
                                <option value="ko">Korean</option>
                                <option value="pt">Portuguese</option>
                                <option value="ru">Russian</option>
                                <option value="es">Spanish</option>
                                <option value="uk">Ukranian</option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <div class="item-row">
                        <div class="item-title">
                            <h4>
                                <?php _e("Bot agent details","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Insert the profile informations of the bot agent.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <p><?php _e("Name","sb") ?></p>
                                <input data-setting="bot-name" type="text" />
                            </div>
                            <div class="item-input item-input-img">
                                <p><?php _e("Profile image","sb") ?></p>
                                <img data-setting="bot-img" class="sb-agent-img" src="<?php echo SB_PLUGIN_URL . '/media/user-1.jpg' ?>" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                
                <hr class="space s" />
                <a id="btn-save-settings" class="button button-primary"><?php _e("Save settings","sb") ?></a>
                <span class="sb-msg sb-msg-success"><?php _e("Saved","sb") ?></span>
                <span class="sb-msg sb-msg-error"><?php _e("Error","sb") ?></span>
            </div>
            <div class="panel-plugin">
                <div class="panel-inner">
                    <div class="item-row item-row-vertical">
                        <div class="item-title">
                            <h4>
                                <?php _e("Users email","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Email template for the email sent to the user when an egent reply.","sb") ?>
                                <?php _e("You can use only text and the following patterns: {user_username}, {message}, {files}, {reply_link}, {site_name}, {site_url}.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <input id="email-user-subject" placeholder="Email subject" value="<?php echo $emails_arr[0] ?>" type="text" />
                            </div>
                            <textarea id="email-user"><?php echo str_replace("<br>",PHP_EOL,$emails_arr[1]) ?></textarea>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="panel-inner">
                    <div class="item-row item-row-vertical">
                        <div class="item-title">
                            <h4>
                                <?php _e("Agents email","sb") ?>
                            </h4>
                            <p>
                                <?php _e("Email template for the email sent to the agent everytime a user send a new message.","sb") ?>
                                <?php _e("You can use only text and the following patterns: {user_username}, {message}, {files}, {reply_link}, {site_name}, {site_url}.","sb") ?>
                            </p>
                        </div>
                        <div class="item-setting">
                            <div class="item-input">
                                <input id="email-agent-subject" placeholder="Email subject" type="text" value="<?php echo $emails_arr[2] ?>" />
                            </div>
                            <textarea id="email-agent"><?php echo str_replace("<br>",PHP_EOL,$emails_arr[3]) ?></textarea>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <a id="btn-save-emails" class="button button-primary"><?php _e("Save emails","sb") ?></a>
                <span class="sb-msg sb-msg-success sb-msg-success-email"><?php _e("Saved","sb") ?></span>
                <span class="sb-msg sb-msg-error sb-msg-error-email"><?php _e("Error","sb") ?></span>
            </div>
        </div>
    </div>
</div>
