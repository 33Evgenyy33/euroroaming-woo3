<?php
/**
 * Retrieves a referral object.
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return AffWP\Referral|false Referral object, otherwise false.
 */
function affwp_get_referral( $referral = null ) {

	if ( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif ( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
	} else {
		return false;
	}

	$referral = affiliate_wp()->referrals->get_object( $referral_id );

	if ( ! empty( $referral->products ) ) {
		// products is a multidimensional array. Double unserialize is not a typo
		$referral->products = maybe_unserialize( maybe_unserialize( $referral->products ) );
	}

	return $referral;
}

/**
 * Retrieves a referral's status.
 *
 * @since 1.6
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return string|false Referral status, otherwise false.
 */
function affwp_get_referral_status( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	return $referral->status;
}

/**
 * Retrieves the status label for a referral.
 *
 * @since 1.6
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return string|false $label The localized version of the referral status, otherwise false. If the status
 *                             isn't registered and the referral is valid, the default 'pending' status will
 *                             be returned
 */
function affwp_get_referral_status_label( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	$statuses = array(
		'paid'     => __( 'Paid', 'affiliate-wp' ),
		'unpaid'   => __( 'Unpaid', 'affiliate-wp' ),
		'rejected' => __( 'Rejected', 'affiliate-wp' ),
		'pending'  => __( 'Pending', 'affiliate-wp' ),
	);

	$label = array_key_exists( $referral->status, $statuses ) ? $statuses[ $referral->status ] : 'pending';

	/**
	 * Filters the referral status label.
	 *
	 * @since 1.6
	 *
	 * @param string         $label    A localized version of the referral status label.
	 * @param AffWP\Referral $referral Referral object.
	 */
	return apply_filters( 'affwp_referral_status_label', $label, $referral );

}

/**
 * Sets a referral's status.
 *
 * @since
 *
 * @param int|AffWP\Referral $referral   Referral ID or object.
 * @param string             $new_status Optional. New referral status to set. Default empty.
 * @return bool True if the referral status was successfully changed from the old status to the
 *              new one, otherwise false.
 */
function affwp_set_referral_status( $referral, $new_status = '' ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	$old_status = $referral->status;

	if( $old_status == $new_status ) {
		return false;
	}

	if( empty( $new_status ) ) {
		return false;
	}

	if( affiliate_wp()->referrals->update( $referral->ID, array( 'status' => $new_status ), '', 'referral' ) ) {

		if( 'paid' == $new_status ) {

			affwp_increase_affiliate_earnings( $referral->affiliate_id, $referral->amount );
			affwp_increase_affiliate_referral_count( $referral->affiliate_id );

		} elseif ( 'unpaid' == $new_status && ( 'pending' == $old_status || 'rejected' == $old_status ) ) {

			// Update the visit ID that spawned this referral
			affiliate_wp()->visits->update( $referral->visit_id, array( 'referral_id' => $referral->ID ), '', 'visit' );

			do_action( 'affwp_referral_accepted', $referral->affiliate_id, $referral );

		} elseif( 'paid' != $new_status && 'paid' == $old_status ) {

			affwp_decrease_affiliate_earnings( $referral->affiliate_id, $referral->amount );
			affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

		}

		/**
		 * Fires immediately after a referral's status has been successfully updated.
		 *
		 * Will not fire if the new status matches the old one, or if `$new_status` is empty.
		 *
		 * @since
		 *
		 * @param int    $referral_id Referral ID.
		 * @param string $new_status  New referral status.
		 * @param string $old_status  Old referral status.
		 */
		do_action( 'affwp_set_referral_status', $referral->ID, $new_status, $old_status );

		return true;
	}

	return false;

}

/**
 * Adds a new referral to the database.
 *
 * Referral status cannot be updated here, use affwp_set_referral_status().
 *
 * @since 1.0
 *
 * @param array $data {
 *     Optional. Arguments for adding a new referral. Default empty array.
 *
 *     @type int    $affiliate_id Affiliate ID.
 *     @type float  $amount       Referral amount. Default empty.
 *     @type string $description  Description. Default empty.
 *     @type string $reference    Referral reference (usually product information). Default empty.
 *     @type string $context      Referral context (usually the integration it was generated from).
 *                                Default empty.
 *     @type string $status       Status to update the referral too. Default 'pending'.
 * }
 * @return int|bool 0|false if no referral was added, referral ID if it was successfully added.
 */
function affwp_add_referral( $data = array() ) {
	
	if ( empty( $data['user_id'] ) && empty( $data['affiliate_id'] ) ) {
		return 0;
	}

	if ( empty( $data['affiliate_id'] ) ) {

		$user_id      = absint( $data['user_id'] );
		$affiliate_id = affiliate_wp()->affiliates->get_column_by( 'affiliate_id', 'user_id', $user_id );

		if ( ! empty( $affiliate_id ) ) {

			$data['affiliate_id'] = $affiliate_id;

		} else {

			return 0;

		}

	}

	$args = array(
		'affiliate_id' => absint( $data['affiliate_id'] ),
		'amount'       => ! empty( $data['amount'] )      ? sanitize_text_field( $data['amount'] )      : '',
		'description'  => ! empty( $data['description'] ) ? sanitize_text_field( $data['description'] ) : '',
		'reference'    => ! empty( $data['reference'] )   ? sanitize_text_field( $data['reference'] )   : '',
		'context'      => ! empty( $data['context'] )     ? sanitize_text_field( $data['context'] )     : '',
		'status'       => 'pending',
	);

	if ( ! empty( $data['date'] ) ) {
		$args['date'] = date_i18n( 'Y-m-d H:i:s', strtotime( $data['date'] ) );
	}

	$referral_id = affiliate_wp()->referrals->add( $args );

	if ( $referral_id ) {

		$status = ! empty( $data['status'] ) ? sanitize_text_field( $data['status'] ) : 'pending';

		affwp_set_referral_status( $referral_id, $status );

		return $referral_id;
	}

	return 0;

}

/**
 * Deletes a referral.
 *
 * If the referral has a status of 'paid', the affiliate's earnings and referral count will decrease.
 *
 * @since
 *
 * @param int|AffWP\Referral $referral Referral ID or object.
 * @return bool True if the referral was successfully deleted, otherwise false.
 */
function affwp_delete_referral( $referral ) {

	if ( ! $referral = affwp_get_referral( $referral ) ) {
		return false;
	}

	if( $referral && 'paid' == $referral->status ) {

		// This referral has already been paid, so decrease the affiliate's earnings
		affwp_decrease_affiliate_earnings( $referral->affiliate_id, $referral->amount );

		// Decrease the referral count
		affwp_decrease_affiliate_referral_count( $referral->affiliate_id );

	}

	if( affiliate_wp()->referrals->delete( $referral->ID, 'referral' ) ) {

		/**
		 * Fires immediately after a referral has been deleted.
		 *
		 * @since
		 *
		 * @param int $referral_id Referral ID.
		 */
		do_action( 'affwp_delete_referral', $referral->ID );

		return true;

	}

	return false;
}

/**
 * Calculate the referral amount
 *
 * @param  string  $amount
 * @param  int     $affiliate_id
 * @param  int     $reference
 * @param  string  $rate
 * @param  int     $product_id
 * @return float
 */
function affwp_calc_referral_amount( $amount = '', $affiliate_id = 0, $reference = 0, $rate = '', $product_id = 0 ) {

	$rate     = affwp_get_affiliate_rate( $affiliate_id, false, $rate, $reference );
	$type     = affwp_get_affiliate_rate_type( $affiliate_id );
	$decimals = affwp_get_decimal_count();

	$referral_amount = ( 'percentage' === $type ) ? round( $amount * $rate, $decimals ) : $rate;

	if ( $referral_amount < 0 ) {
		$referral_amount = 0;
	}

	return (string) apply_filters( 'affwp_calc_referral_amount', $referral_amount, $affiliate_id, $amount, $reference, $product_id );

}

/**
 * Retrieves the number of referrals for the given affiliate.
 *
 * @since
 *
 * @param int|AffWP\Affiliate $affiliate Optional. Affiliate ID or object. Default is the current affiliate.
 * @param string|array        $status    Optional. Referral status or array of statuses. Default empty array.
 * @param array|string        $date      Optional. Array of date data with 'start' and 'end' key/value pairs,
 *                                       or a timestamp. Default empty array.
 * @return int Zero if the affiliate is invalid, or the number of referrals for the given arguments.
 */
function affwp_count_referrals( $affiliate_id = 0, $status = array(), $date = array() ) {

	if ( ! $affiliate = affwp_get_affiliate( $affiliate_id ) ) {
		return 0;
	}

	$args = array(
		'affiliate_id' => $affiliate->ID,
		'status'       => $status
	);

	if( ! empty( $date ) ) {
		$args['date'] = $date;
	}

	return affiliate_wp()->referrals->count( $args );
}
