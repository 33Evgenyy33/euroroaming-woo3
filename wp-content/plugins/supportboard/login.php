<?php

/**
* ======================================
* LOGIN AND REGISTRATION FORMS
* ======================================
* 
* Support Board login and registration forms. Showed always for desk and for chat only 
* if the option Chat visibility is setted to Logged users.
*/

global $sb_config;
$user = __("email","sb");
if (sb_get($sb_config,"username-type") == "username") $user = __("username","sb");
?>
<div id="sb-main" class="sb-account-cnt <?php if (sb_get($sb_config,"rtl")) echo "sb-rtl"; ?>" style="display:none">
    <div class="sb-account">
        <!-- LOGIN -->
        <div class="sb-login active">
            <div class="sb-success-msg sb-success-registration">
                <?php _e("Registration completed.","sb") ?>
            </div>
            <div class="sb-input sb-input-user">
                <div>
                    <?php echo ucfirst($user); ?>
                </div>
                <input type="text" value="" />
            </div>
            <div class="sb-input sb-input-psw">
                <div>
                    <?php _e("Password","sb") ?>
                </div>
                <input type="password" value="" />
            </div>
            <div class="sb-error-msg">
                <?php _e("User or password not valid.","sb") ?>
            </div>
            <div class="sb-submit sb-btn sb-submit-login">
                <?php _e("Login","sb") ?>
            </div>
            <div class="sb-register-link">
                <?php _e("New here?","sb") ?>
                <div>
                    <?php _e("Create an account","sb") ?>
                </div>
            </div>
        </div>
        <!-- REGISTER -->
        <div class="sb-register">
            <?php if (sb_get($sb_config,"user-img")) { ?>
            <div class="sb-input sb-input-reg-img">
                <div>
                    <?php _e("Profile image","sb") ?>
                </div>
                <img src="<?php echo SB_PLUGIN_URL . "/media/user-2.jpg" ?>" alt="" />
            </div>
            <div class="sb-progress">
                <div class="sb-progress-bar" style="width: 0%;"></div>
            </div>
            <form class="sb-upload-form" action="#" method="post" enctype="multipart/form-data">
                <input type="file" name="profile-img" class="sb-upload-profile-img" accept=".jpg,.png" />
            </form>
            <?php } ?>
            <div class="sb-input sb-input-reg-user">
                <div>
                    <?php echo __("Type your ","sb") . $user ?>
                </div>
                <input type="<?php echo (($user == "email") ? "email" : "text") ?>" value="" />
            </div>
            <div class="sb-input sb-input-reg-psw">
                <div>
                    <?php _e("Choose your password","sb") ?>
                </div>
                <input type="password" value="" />
            </div>
            <div class="sb-input sb-input-reg-psw-2">
                <div>
                    <?php _e("Insert again your password","sb") ?>
                </div>
                <input type="password" value="" />
            </div>
            <?php
            $html = "";
            if ($user != "email" && sb_get($sb_config,"user-email")) {
                $html = '<div class="sb-input sb-email"><div>' . __("Email","sb") . '</div><input type="email" required value="" /></div>';
            }
            for ($i = 1; $i < 5; $i++){
                if (sb_get($sb_config,"user-extra-" . $i) != "") {
                    $html .= ' <div class="sb-input sb-extra-' . $i . '"><div>' . __(sb_get($sb_config,"user-extra-" . $i),"sb") . '</div><input type="text" value="" /></div>';
                }
            }
            echo $html;
            ?>
            <div class="sb-error-msg sb-error-msg-reg" data-messages="<?php _e("Passwords not match.|Minimum password length is 4 characters.|User already registered.|Email is not valid.","sb") ?>">
                <?php _e("Passwords not match.","sb") ?>
            </div>
            <div class="sb-submit sb-btn sb-submit-register">
                <?php _e("Register now","sb") ?>
            </div>
            <div class="sb-login-link">
                <?php _e("Already got an account?","sb") ?>
                <div>
                    <?php _e("Sign in here","sb") ?>
                </div>
            </div>
        </div>
    </div>
</div>
