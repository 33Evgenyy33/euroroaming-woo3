<?php
function my_smtp_wp_admin(){
	add_options_page('My SMTP WP Options', 'My SMTP WP','manage_options', __FILE__, 'my_smtp_wp_page');
	add_action('load-my_smtp_wp_admin', 'my_smtp_wp_help_tab');
}

function my_smtp_wp_page(){
	$ws_nonce = wp_create_nonce('my_ws_nonce');
	global $wsOptions;
	if(isset($_POST['my_smtp_mail_update']) && isset($_POST['my_smtp_mail_nonce_update'])){
		if(!wp_verify_nonce(trim($_POST['my_smtp_mail_nonce_update']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$wsOptions = array();
		$wsOptions["from"] = trim($_POST['my_smtp_mail_from']);
		$wsOptions["fromname"] = trim($_POST['my_smtp_mail_fromname']);
		$wsOptions["replyto"] = trim($_POST['my_smtp_reply_to']);
		$wsOptions["host"] = trim($_POST['my_smtp_mail_host']);
		$wsOptions["smtpsecure"] = trim($_POST['my_smtp_mail_smtpsecure']);
		$wsOptions["port"] = trim($_POST['my_smtp_mail_port']);
		$wsOptions["smtpauth"] = trim($_POST['my_smtp_mail_smtpauth']);
		$wsOptions["username"] = trim($_POST['my_smtp_mail_username']);
		$wsOptions["password"] = trim($_POST['my_smtp_mail_password']);
		$wsOptions["returnpath"] = trim($_POST['my_smtp_mail_returnpath']);
		$wsOptions["deactivate"] = (isset($_POST['my_smtp_mail_deactivate'])) ? trim($_POST['my_smtp_mail_deactivate']) : "";
		update_option("my_smtp_wp_options",$wsOptions);
		if(!is_email($wsOptions["from"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"From Email\" must be a valid email address!","my-smtp-wp") . '</strong></p></div>';
		}
		elseif(empty($wsOptions["host"])){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The field \"SMTP Host\" can not be left blank!","my-smtp-wp") . '</strong></p></div>';
		}
		else{
			echo '<div id="message" class="updated fade"><p><strong>' . __("Options saved.","my-smtp-wp") . '</strong></p></div>';
		}
	}

	if ( isset( $_POST['my_smtp_mail_test'] ) && isset($_POST['my_smtp_mail_nonce_test']) ) {	
		if(!wp_verify_nonce(trim($_POST['my_smtp_mail_nonce_test']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
			if( isset( $_POST['my_smtp_mail_to'] ) ){
				if( is_email( $_POST['my_smtp_mail_to'] ) ){
					$to =$_POST['my_smtp_mail_to'];
				}
				else{
					$error .= " " . __( "Please enter a valid email address in the 'FROM' field.", 'my-smtp-wp' );
				}
			}
			
			if( ! empty( $to ) )
				$result = my_smtp_wp_test_mail( $to );
		}
	/*
	Old Test
	if(isset($_POST['my_smtp_mail_test']) && isset($_POST['my_smtp_mail_nonce_test'])){
		if(!wp_verify_nonce(trim($_POST['my_smtp_mail_nonce_test']),'my_ws_nonce')){
			wp_die('Security check not passed!');
		}
		$to = trim($_POST['my_smtp_mail_to']);
		$subject = trim($_POST['my_smtp_mail_subject']);
		$message = trim($_POST['my_smtp_mail_message']);
		$failed = 0;
		if(!empty($to) && !empty($subject) && !empty($message)){
			try{
				$result = wp_mail($to,$subject,$message);
				//$result->Send();
			}catch(phpmailerException $e){
				$failed = 1;
				return new WP_Error( $e->getCode(), $e->getMessage() );
			}
		}else{
			$failed = 2;
		}
		if(!$failed){
			if($result==TRUE){
				echo '<div id="message" class="updated fade"><p><strong>' . __("Message sent successfully!","my-smtp-wp") . '</strong></p></div>';
			}
			else{
				$failed = 1;
			}
		}
		if($failed == 1){
			echo '<div id="message" class="updated fade"><p><strong>' . __("Some errors occurred! Check the settings!","my-smtp-wp") . '</strong></p></div>';
		}
		elseif($failed == 2){
			echo '<div id="message" class="updated fade"><p><strong>' . __("The fields \"To\" can not be left blank when testing!","my-smtp-wp") . '</strong></p></div>';
		}
	}*/
	if(is_admin()){require_once('my-smtp-wp-admin.php');}
}

function my_smtp_wp_test_mail( $to ) {
                /*if(!swpsmtp_credentials_configured()){
                    return;
                }*/
		$errors = '';
		global $wsOptions;
		//$swpsmtp_options = get_option( 'swpsmtp_options' );

		require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
		$mail = new PHPMailer();
                
        $charset = get_bloginfo( 'charset' );
		$mail->CharSet = $charset;
                
		$from_name  = $wsOptions["fromname"];
		$from_email = $wsOptions["from"];

		$subject = __('[My SMTP WP] Your plugin is working','my-smtp-wp');
		$message = __('If you are reading this email, it is because your plugin is successfully configured.','my-smtp-wp');
		
		$mail->IsSMTP();
		
		/* If using smtp auth, set the username & password */
		if( 'yes' == $wsOptions['smtpauth'] ){
			$mail->SMTPAuth = true;
			$mail->Username = $wsOptions['username'];
			$mail->Password = $wsOptions['password'];
		}
		
		/* Set the SMTPSecure value, if set to none, leave this blank */
		if ( $wsOptions['smtpsecure'] !== '' ) {
			$mail->SMTPSecure = $wsOptions['smtpsecure'];
		}
                
                /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
                $mail->SMTPAutoTLS = false;
		
		/* Set the other options */
		$mail->Host = $wsOptions['host'];
		$mail->Port = $wsOptions['port']; 
		$mail->SetFrom( $from_email, $from_name );
		$mail->isHTML( true );
		$mail->Subject = $subject;
		$mail->MsgHTML( $message );
		$mail->AddAddress( $to );
		$mail->SMTPDebug = 0;

		/* Send mail and return result */
		if ( ! $mail->Send() )
			$errors = $mail->ErrorInfo;
		
		$mail->ClearAddresses();
		$mail->ClearAllRecipients();
			
		if ( ! empty( $errors ) ) {
			echo '<div id="message" class="updated fade"><p><strong>' . __("Some errors occurred! Check the settings!","my-smtp-wp") . '</strong><br><br>';
			echo  $errors . '</p></div>';

		}
		else{
			echo '<div id="message" class="updated fade"><p><strong>' . __("Message sent successfully!","my-smtp-wp") . '</strong></p></div>';
		}
	}

function my_smtp_wp_help_tab () {
    $screen = get_current_screen();

    // Add my_help_tab if current screen is My Admin Page
    $screen->add_help_tab( array(
        'id'	=> 'my_help_tab',
        'title'	=> __('My Help Tab'),
        'content'	=> '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
    ) );
}
?>
