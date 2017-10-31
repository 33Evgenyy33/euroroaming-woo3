<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Addons configuration
 *
 * @filter us_config_addons
 */

global $us_template_directory;

return array(
	array(
		'name' => 'WPBakery Page Builder',
		'free' => FALSE,
		'description' => __( 'Most popular drag & drop WordPress page builder. Save tons of time working on your website content.', 'us' ),
		'slug' => 'js_composer',
		'source' => '',
		'changelog_url' => 'https://wpbakery.atlassian.net/wiki/display/VC/Release+Notes',
		'url' => 'https://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=UpSolution',
	),
	array(
		'name' => 'Header Builder',
		'free' => FALSE,
		'description' => __( 'Allows you to create website headers with custom layout and any elements.', 'us' ),
		'slug' => 'us-header-builder',
		'source' => '',
		'changelog_url' => 'https://help.us-themes.com/' . strtolower( US_THEMENAME ) . '/changelog/',
		'url' => 'https://help.us-themes.com/impreza/hb/',
	),
	array(
		'name' => 'Slider Revolution',
		'free' => FALSE,
		'description' => __( 'Allows you to create attractive and interactive sliders and presentations.', 'us' ),
		'slug' => 'revslider',
		'source' => '',
		'changelog_url' => 'http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380?ref=UpSolution',
		'url' => 'https://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380?ref=UpSolution',
	),
	array(
		'name' => 'Ultimate Addons for&nbsp;Visual&nbsp;Composer',
		'free' => FALSE,
		'description' => __( 'Adds dozens of options and content elements to WPBakery Page Builder.', 'us' ),
		'slug' => 'Ultimate_VC_Addons',
		'source' => '',
		'changelog_url' => 'https://changelog.brainstormforce.com/ultimate/',
		'url' => 'https://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199?ref=UpSolution',
	),
	array(
		'name' => 'WooCommerce',
		'free' => TRUE,
		'description' => __( 'Most popular eCommerce plugin that allows you to sell anything.', 'us' ),
		'slug' => 'woocommerce',
		'source' => 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip',
		'changelog_url' => '',
		'url' => 'https://wordpress.org/plugins/woocommerce/',
	),
	array(
		'name' => 'CodeLights Elements',
		'free' => TRUE,
		'description' => __( 'Adds "Modal Popup", "Stats Counter", "FlipBox", "Interactive Text" and some other content elements and widgets.', 'us' ),
		'slug' => 'codelights-shortcodes-and-widgets',
		'source' => 'https://downloads.wordpress.org/plugin/codelights-shortcodes-and-widgets.latest-stable.zip',
		'changelog_url' => '',
		'url' => 'https://wordpress.org/plugins/codelights-shortcodes-and-widgets/',
	),
	array(
		'name' => 'Contact Form 7',
		'free' => TRUE,
		'description' => __( 'Allows you to create customizable contact forms and edit the mail contents.', 'us' ),
		'slug' => 'contact-form-7',
		'source' => 'https://downloads.wordpress.org/plugin/contact-form-7.latest-stable.zip',
		'changelog_url' => '',
		'url' => 'https://wordpress.org/plugins/contact-form-7/',
	),
	array(
		'name' => 'The Events Calendar',
		'free' => TRUE,
		'description' => __( 'Allows you to create an events calendar and manage it with ease.', 'us' ),
		'slug' => 'the-events-calendar',
		'source' => 'https://downloads.wordpress.org/plugin/the-events-calendar.latest-stable.zip',
		'changelog_url' => '',
		'url' => 'https://wordpress.org/plugins/the-events-calendar/',
	),
);
