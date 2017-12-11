
<div class="wrap">
<style type="text/css">
	th{
		padding-left: 10px!important;
	}
</style>	
<?php screen_icon(); ?>
<h2>
My SMTP WP
</h2>
<div id="message" class="notice notice-info fade"><p><strong><?php _e('Need help setting up?','my-smtp-wp'); ?> <a href="http://wordlab.com.br/help-in-my-smtp-wp-configuration/" target="_blank"><?php _e('Check how!','my-smtp-wp'); ?></a></strong></p></div>
<div id="message" class="notice notice-error is-dismissible"><p><strong><?php _e('Using Gmail and getting error?','my-smtp-wp'); ?> <a href="https://www.google.com/settings/u/0/security/lesssecureapps" target="_blank"><?php _e('Authorize low security with applications','my-smtp-wp'); ?></a> <?php _e('or','my-smtp-wp'); ?> <a href="https://security.google.com/settings/u/0/security/apppasswords?pli=1" target="_blank"><?php _e('Create a application password for the plugin.','my-smtp-wp'); ?></a> </strong></p></div>
<form action="" method="post" enctype="multipart/form-data" name="my_smtp_wp_form">

<table class="form-table postbox">
	<div class="inside">
		<tr valign="top">
			<th scope="row">
				<?php _e('From Email','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_mail_from" value="<?php echo $wsOptions["from"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('From Name','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_mail_fromname" value="<?php echo $wsOptions["fromname"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('Reply To','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_reply_to" value="<?php echo $wsOptions["replyto"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<h3><?php _e('SMTP Options','my-smtp-wp'); ?></h3>
			</th>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('SMTP Host','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_mail_host" value="<?php echo $wsOptions["host"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('SMTP Encryption','my-smtp-wp'); ?>
			</th>
			<td>
				<p><label>
					<input name="my_smtp_mail_smtpsecure" type="radio" value=""<?php if ($wsOptions["smtpsecure"] == '') { ?> checked="checked"<?php } ?> />
					<?php _e('No encryption','my-smtp-wp'); ?>
				</label></p>
				
				<p><label>
					<input name="my_smtp_mail_smtpsecure" type="radio" value="ssl"<?php if ($wsOptions["smtpsecure"] == 'ssl') { ?> checked="checked"<?php } ?> />
					<?php _e('Use SSL encryption','my-smtp-wp'); ?>
				</label></p>
				<p><label>
					<input name="my_smtp_mail_smtpsecure" type="radio" value="tls"<?php if ($wsOptions["smtpsecure"] == 'tls') { ?> checked="checked"<?php } ?> />
					<?php _e('Use TLS encryption','my-smtp-wp'); ?>
				</label></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('SMTP Port','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_mail_port" value="<?php echo $wsOptions["port"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('SMTP Authentication','my-smtp-wp'); ?>
			</th>
			<td>
				<p><label>
					<input name="my_smtp_mail_smtpauth" type="radio" value="no"<?php if ($wsOptions["smtpauth"] == 'no') { ?> checked="checked"<?php } ?> />
					<?php _e('No: Do not use SMTP authentication','my-smtp-wp'); ?>
				</label></p>
				<p><label>
					<input name="my_smtp_mail_smtpauth" type="radio" value="yes"<?php if ($wsOptions["smtpauth"] == 'yes') { ?> checked="checked"<?php } ?> />
					<?php _e('Yes: Use SMTP authentication','my-smtp-wp'); ?>
				</label></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('Username ( Email Address )','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="text" name="my_smtp_mail_username" value="<?php echo $wsOptions["username"]; ?>" size="43" style="width:272px;height:24px;" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('Password ( Email Address )','my-smtp-wp'); ?>
			</th>
			<td>
				<label>
					<input type="password" name="my_smtp_mail_password" value="<?php echo $wsOptions["password"]; ?>" size="43" style="width:272px;height:24px;" />
					<input type="hidden" name="my_smtp_mail_deactivate" value="yes" />
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e('Active Return-Path Header Fix?','my-smtp-wp'); ?>
			</th>
			<td>
				<p><label>
					<input name="my_smtp_mail_returnpath" type="radio" value="no"<?php if ($wsOptions["returnpath"] == 'no') { ?> checked="checked"<?php } ?> />
					<?php _e('No: Do not use Return-Path','my-smtp-wp'); ?>
				</label></p>
				<p><label>
					<input name="my_smtp_mail_returnpath" type="radio" value="yes"<?php if ($wsOptions["returnpath"] == 'yes') { ?> checked="checked"<?php } ?> />
					<?php _e('Yes: Use Return-Path','my-smtp-wp'); ?>
				</label></p>
			</td>
		</tr>
	</div>
</table>

<p class="submit">
<input type="hidden" name="my_smtp_mail_update" value="update" />
<input type="hidden" name="my_smtp_mail_nonce_update" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes'); ?>" />
</p>

</form>

<form action="" method="post" enctype="multipart/form-data" name="my_smtp_mail_testform">
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<h3><?php _e('Send a Test Email','my-smtp-wp'); ?></h3>
		</th>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php _e('To:','my-smtp-wp'); ?>
		</th>
		<td>
			<label>
				<input type="text" name="my_smtp_mail_to" value="" size="43" style="width:272px;height:24px;" />
			</label>
			<span class="description"><?php _e('Type an email address here and then click Send Test to generate a test email.','my-smtp-wp'); ?></span>
		</td>
	</tr>
</table>
<p class="submit">
<input type="hidden" name="my_smtp_mail_test" value="test" />
<input type="hidden" name="my_smtp_mail_nonce_test" value="<?php echo $ws_nonce; ?>" />
<input type="submit" class="button-primary" value="<?php _e('Send Test','my-smtp-wp'); ?>" />
</p>
</form>

</div>