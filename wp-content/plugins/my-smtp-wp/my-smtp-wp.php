<?php
/*
Plugin Name: My SMTP WP
Plugin URI: https://github.com/valeriosouza/my-smtp-wp
Description: WP SMTP can help us to send emails via SMTP instead of the PHP mail() function.
Version: 1.3.3
Author: Valerio Souza
Author URI: http://valeriosouza.com.br
Text Domain: my-smtp-wp
Domain Path: /languages
*/

// Actions and Filters

add_filter('plugins_loaded','load_my_smtp_wp_lang');

add_action('phpmailer_init','my_smtp_wp');

register_activation_hook( __FILE__ , 'my_smtp_wp_activate' );

add_filter('plugin_action_links','my_smtp_wp_settings_link',10,2);

add_action('admin_menu', 'my_smtp_wp_admin');

$wsOptions = get_option("my_smtp_wp_options");

add_filter( 'plugin_row_meta', 'my_smtp_plugin_row_meta', 10, 4 );

if($wsOptions["deactivate"]=="yes"){
	register_deactivation_hook( __FILE__ , create_function('','delete_option("my_smtp_wp_options");') );
}
if ($wsOptions["returnpath"]=="yes") {
// Function return path fix
class email_return_path {
  	function __construct() {
		add_action( 'phpmailer_init', array( $this, 'fix' ) );    
  	}

	function fix( $phpmailer ) {
	  	$phpmailer->Sender = $phpmailer->From;
	}
}

new email_return_path();
}
// Functions

function load_my_smtp_wp_lang(){
	load_plugin_textdomain( 'my-smtp-wp', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function my_smtp_wp($phpmailer){
	global $wsOptions;
	if( !is_email($wsOptions["from"]) || empty($wsOptions["host"]) ){
		return;
	}
	$phpmailer->Mailer = "smtp";
	$phpmailer->From = $wsOptions["from"];
	$phpmailer->FromName = $wsOptions["fromname"];
	$phpmailer->Sender = $phpmailer->From; //Return-Path
	//$phpmailer->AddReplyTo($phpmailer->From,$phpmailer->FromName); //Reply-To
	$phpmailer->AddReplyTo = $wsOptions["replyto"]; //Reply-To
	$phpmailer->Host = $wsOptions["host"];
	$phpmailer->SMTPSecure = $wsOptions["smtpsecure"];
	$phpmailer->Port = $wsOptions["port"];
	$phpmailer->Returnpath = $wsOptions["returnpath"];
	$phpmailer->SMTPAuth = ($wsOptions["smtpauth"]=="yes") ? TRUE : FALSE;
	if($phpmailer->SMTPAuth){
		$phpmailer->Username = $wsOptions["username"];
		$phpmailer->Password = $wsOptions["password"];
	}
}

function my_smtp_wp_activate(){
	$wsOptions = array();
	$wsOptions["from"] = "";
	$wsOptions["fromname"] = "";
	$wsOptions["replyto"] = "";
	$wsOptions["host"] = "";
	$wsOptions["smtpsecure"] = "";
	$wsOptions["port"] = "";
	$wsOptions["smtpauth"] = "yes";
	$wsOptions["username"] = "";
	$wsOptions["password"] = "";
	$wsOptions["returnpath"] = "";
	$wsOptions["deactivate"] = "";
	add_option("my_smtp_wp_options",$wsOptions);
}

function my_smtp_wp_settings_link($action_links,$plugin_file){
	if($plugin_file==plugin_basename(__FILE__)){
		$ws_settings_link = '<a href="options-general.php?page=' . dirname(plugin_basename(__FILE__)) . '/class-my-smtp-wp.php">' . __("Settings") . '</a>';
		array_unshift($action_links,$ws_settings_link);
	}
	return $action_links;
}

function my_smtp_plugin_row_meta( $links, $file ) {
		if( plugin_basename( __FILE__ ) === $file ) {
			$links[] = sprintf(
				'<a target="_blank" href="%s">%s</a>',
				esc_url('http://wordlab.com.br/donate/'),
				__( 'Donate', 'my-smtp-wp' )
			);
		}
		return $links;
	}

if(is_admin()){require_once('class-my-smtp-wp.php');}

?>