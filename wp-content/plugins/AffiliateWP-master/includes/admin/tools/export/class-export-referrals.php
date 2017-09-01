<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

use AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Referral_Export extends Affiliate_WP_Export implements Exporter\CSV {

	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.0
	 */
	public $export_type = 'referrals';

	/**
	 * Date
	 * @var array
	 * @since 1.0
	 */
	public $date;

	/**
	 * Status
	 * @var string
	 * @since 1.0
	 */
	public $status;

	/**
	 * Affiliate ID
	 * @var int
	 * @since 1.0
	 */
	public $affiliate = null;

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.0
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		$cols = array(
			'affiliate_id'  => __( 'Affiliate ID', 'affiliate-wp' ),
			'email'         => __( 'Email', 'affiliate-wp' ),
			'name'          => __( 'Name', 'affiliate-wp' ),
			'payment_email' => __( 'Payment Email', 'affiliate-wp' ),
			'username'      => __( 'Username', 'affiliate-wp' ),
			'amount'        => __( 'Amount', 'affiliate-wp' ),
			'currency'      => __( 'Currency', 'affiliate-wp' ),
			'description'   => __( 'Description', 'affiliate-wp' ),
			'campaign'      => __( 'Campaign', 'affiliate-wp' ),
			'reference'     => __( 'Reference', 'affiliate-wp' ),
			'context'       => __( 'Context', 'affiliate-wp' ),
			'status'        => __( 'Status', 'affiliate-wp' ),
			'date'          => __( 'Date', 'affiliate-wp' )
		);
		return $cols;
	}

	/**
	 * Retrieves the data being exported.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return array $data Data for Export
	 */
	public function get_data() {

		$args = array(
			'status'       => 'unpaid',
			'date'         => ! empty( $this->date ) ? $this->date : '',
			'affiliate_id' => $this->affiliate,
			'number'       => -1
		);

		$data         = array();
		$affiliates   = array();
		$referral_ids = array();
		$referrals    = affiliate_wp()->referrals->get_referrals( $args );

		if( $referrals ) {

			foreach ( $referrals as $referral ) {

				$test1 = wc_get_order( $referral->reference );
				/*if ($test1 == '') {
					file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( 'да', true ), FILE_APPEND | LOCK_EX );
					file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );
				}*/

				if ( $test1 == '' ) {
					continue;
				}


				$order  = new WC_Order( $referral->reference );
				$coupon = '';
				if ( ! empty( $order ) ) {
					$coupons   = $order->get_used_coupons();
					$order_url = admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );
					if ( ! empty( $coupons ) ) {
						$coupon = $coupons[0];
					} else {
						$coupon = 'без промокода';
					}
				}

				$data[] = array(
					'campaign'    => get_userdata( affwp_get_affiliate( $referral->affiliate_id )->user_id )->billing_company,
					'email'       => affwp_get_affiliate_email( $referral->affiliate_id ),
					'coupon'      => $coupon,
					'description' => strip_tags( str_replace( ',', "\r\n", $referral->description ) ),
					'reference'   => $order_url,
					'amount'      => $referral->amount,
					'date'        => $referral->date
					/*'billing_partner' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->billing_partner,
					'actual_address' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->actual_address*/
				);
			}

		}

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	public function get_stat_affil() {
		return '';
	}

}
