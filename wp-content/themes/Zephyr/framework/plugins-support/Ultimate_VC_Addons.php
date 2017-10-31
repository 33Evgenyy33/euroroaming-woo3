<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ultimate Addons for Visual Composer Support
 *
 * @link http://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199?ref=UpSolution
 */

if ( ! class_exists( 'Ultimate_VC_Addons' ) ) {
	return;
}

defined( 'ULTIMATE_USE_BUILTIN' ) OR define( 'ULTIMATE_USE_BUILTIN', TRUE );
defined( 'ULTIMATE_NO_EDIT_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_EDIT_PAGE_NOTICE', TRUE );
defined( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE' ) OR define( 'ULTIMATE_NO_PLUGIN_PAGE_NOTICE', TRUE );
// Removing potentially dangerous functions
if ( ! function_exists( 'bsf_grant_developer_access' ) ) {
	function bsf_grant_developer_access() {
	}
}
if ( ! function_exists( 'bsf_allow_developer_access' ) ) {
	function bsf_allow_developer_access() {
	}
}
if ( ! function_exists( 'bsf_process_developer_login' ) ) {
	function bsf_process_developer_login() {
	}
}
if ( ! function_exists( 'bsf_notices' ) ) {
	function bsf_notices() {
	}
}
add_action( 'init', 'us_sanitize_ultimate_addons', 20 );
function us_sanitize_ultimate_addons() {
	remove_action( 'admin_init', 'bsf_update_all_product_version', 1000 );
	remove_action( 'admin_notices', 'bsf_notices', 1000 );
	remove_action( 'network_admin_notices', 'bsf_notices', 1000 );
	remove_action( 'admin_footer', 'bsf_update_counter', 999 );
	remove_action( 'wp_ajax_bsf_update_client_license', 'bsf_server_update_client_license' );
	remove_action( 'wp_ajax_nopriv_bsf_update_client_license', 'bsf_server_update_client_license' );
}

// Disabling after-activation redirect to Ultimate Addons Dashboard
if ( get_option( 'ultimate_vc_addons_redirect' ) == TRUE ) {
	update_option( 'ultimate_vc_addons_redirect', FALSE );
}

add_action( 'admin_init', 'us_ultimate_addons_for_vc_integration' );
function us_ultimate_addons_for_vc_integration() {
	if ( get_option( 'ultimate_updater' ) != 'disabled' ) {
		update_option( 'ultimate_updater', 'disabled' );
	}
}

add_action( 'core_upgrade_preamble', 'us_ultimate_addons_core_upgrade_preamble' );
function us_ultimate_addons_core_upgrade_preamble() {
	remove_action( 'core_upgrade_preamble', 'list_bsf_products_updates', 999 );
}

add_filter( 'pre_set_site_transient_update_plugins', 'us_ultimate_addons_update_plugins_transient', 99 );
function us_ultimate_addons_update_plugins_transient( $_transient_data ) {
	if ( isset( $_transient_data->response[ 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ] ) AND empty( $_transient_data->response[ 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ]->package ) ) {
		unset( $_transient_data->response[ 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ] );
	}
	return $_transient_data;
}

add_filter( 'ultimate_front_scripts_post_content', 'us_ultimate_front_scripts_post_content' );
function us_ultimate_front_scripts_post_content( $content ) {
	$hide_footer = FALSE;

	// Default footer option
	$footer_id = us_get_option( 'footer_id', NULL );
	if ( is_singular( array( 'us_portfolio' ) ) ) {
		if ( us_get_option( 'footer_portfolio_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_portfolio_id', NULL );
		}
	} elseif ( is_singular( array( 'post', 'attachment' ) ) ) {
		if ( us_get_option( 'footer_post_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_post_id', NULL );
		}
	} elseif ( function_exists( 'is_woocommerce' ) AND is_woocommerce() ) {
		if ( is_singular() ) {
			if ( us_get_option( 'footer_product_defaults', 1 ) == 0 ) {
				$footer_id = us_get_option( 'footer_product_id', NULL );
			}
		} else {
			if ( us_get_option( 'footer_shop_defaults', 1 ) == 0 ) {
				$footer_id = us_get_option( 'footer_shop_id', NULL );
			}
			if ( ! is_search() AND ! is_tax() ) {
				if ( usof_meta( 'us_footer', array(), wc_get_page_id( 'shop' ) ) == 'hide' ) {
					$hide_footer = TRUE;
				}
				if ( usof_meta( 'us_footer', array(), wc_get_page_id( 'shop' ) ) == 'custom' ) {
					$footer_id = usof_meta( 'us_footer_id', array(), wc_get_page_id( 'shop' ) );
				}
			}
		}
	} elseif ( is_archive() OR is_search() ) {
		if ( us_get_option( 'footer_archive_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_archive_id', NULL );
		}
	}


	if ( is_singular() OR ( is_404() AND $page_404 = get_page_by_path( 'error-404' ) ) ) {
		if ( is_singular() ) {
			$postID = get_the_ID();
		} elseif ( is_404() ) {
			$postID = $page_404->ID;
		}
		if ( usof_meta( 'us_footer', array(), $postID ) == 'hide' ) {
			$hide_footer = TRUE;
		}
		if ( usof_meta( 'us_footer', array(), $postID ) == 'custom' ) {
			$footer_id = usof_meta( 'us_footer_id', array(), $postID );
		}
	}

	if ( ! $hide_footer ) {
		$footer = FALSE;
		if ( ! empty( $footer_id ) ) {
			$footer = get_page_by_path( $footer_id, OBJECT, 'us_footer' );
		}

		if ( $footer ) {
			$translated_footer_id = apply_filters( 'wpml_object_id', $footer->ID, 'us_footer', TRUE );
			if ( $translated_footer_id != $footer->ID ) {
				$footer = get_post( $translated_footer_id );
			}

			$content .= $footer->post_content;
		}


	}

	return $content;
}
