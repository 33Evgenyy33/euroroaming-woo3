<?php

/**
 * Process the add affiliate request
 *
 * @since 1.2
 * @return void|false
 */
function affwp_process_add_affiliate( $data ) {

	if ( empty( $data['user_id'] ) && empty( $data['user_name'] ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage affiliates', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	$affiliate_id = affwp_add_affiliate( $data );

	if ( $affiliate_id ) {

		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_added' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_added_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_add_affiliate', 'affwp_process_add_affiliate' );

/**
 * Add affiliate meta
 *
 * @since 2.0
 * @return void
 */
function affwp_process_add_affiliate_meta( $affiliate_id, $args ) {

	// add notes against affiliate
	$notes = ! empty( $args['notes'] ) ? wp_kses_post( $args['notes'] ) : '';

	if ( $notes ) {
		affwp_update_affiliate_meta( $affiliate_id, 'notes', $notes );
	}

}
add_action( 'affwp_insert_affiliate', 'affwp_process_add_affiliate_meta', 10, 2 );

/**
 * Process affiliate deletion requests
 *
 * @since 1.2
 * @param $data array
 * @return void
 */
function affwp_process_affiliate_deletion( $data ) {

	if ( ! is_admin() ) {
		return;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to delete affiliate accounts', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_delete_affiliates_nonce'], 'affwp_delete_affiliates_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( empty( $data['affwp_affiliate_ids'] ) || ! is_array( $data['affwp_affiliate_ids'] ) ) {
		wp_die( __( 'No affiliate IDs specified for deletion', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 400 ) );
	}

	$to_delete    = array_map( 'absint', $data['affwp_affiliate_ids'] );
	$delete_users = isset( $data['affwp_delete_users_too'] ) && current_user_can( 'delete_users' );

	foreach ( $to_delete as $affiliate_id ) {

		if ( $delete_users ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );

			$user_id = affwp_get_affiliate_user_id( $affiliate_id );

			if( (int) $user_id !== (int) get_current_user_id() ) {
				// Don't allow a user to delete themself
				wp_delete_user( $user_id );
			}

		}

		affwp_delete_affiliate( $affiliate_id, true );

	}

	wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_deleted' ) ) );
	exit;

}
add_action( 'affwp_delete_affiliates', 'affwp_process_affiliate_deletion' );

/**
 * Process the update affiliate request
 *
 * @since 1.2
 * @return void
 */
function affwp_process_update_affiliate( $data ) {

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage affiliates', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_update_affiliate( $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'action' => 'edit_affiliate', 'affwp_notice' => 'affiliate_updated', 'affiliate_id' => $data['affiliate_id'] ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_update_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_update_affiliate', 'affwp_process_update_affiliate' );

/**
 * Process the affiliate moderation request
 *
 * @since 1.7
 * @return void
 */
function affwp_process_affiliate_moderation( $data ) {

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( __( 'You do not have permission to manage affiliates', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_moderate_affiliates_nonce'], 'affwp_moderate_affiliates_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}


	$status = isset( $data['affwp_accept'] ) ? 'active' : 'rejected';
	$notice = isset( $data['affwp_accept'] ) ? 'affiliate_accepted' : 'affiliate_rejected';

	if( 'rejected' == $status ) {

		$reason = ! empty( $data['affwp_rejection_reason'] ) ? wp_kses_post( $data['affwp_rejection_reason'] ) : false;

		if( $reason ) {

			affwp_add_affiliate_meta( $data['affiliate_id'], '_rejection_reason', $reason, true );

		}

	}

	if ( affwp_set_affiliate_status( $data['affiliate_id'], $status ) ) {
		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => $notice, 'affiliate_id' => $data['affiliate_id'] ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_update_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_moderate_affiliate', 'affwp_process_affiliate_moderation' );
