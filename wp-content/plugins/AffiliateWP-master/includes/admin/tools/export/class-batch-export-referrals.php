<?php

namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;
use WC_Order;

/**
 * Implements a batch processor for exporting referrals based on status to a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Referrals extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export-referrals';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'referrals';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'export_referral_data';

	/**
	 * ID of affiliate to export referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $affiliate_id = 0;

	/**
	 * Start and/or end dates to retrieve referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $date = array();

	/**
	 * Status to export referrals for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $status = '';

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {

		if ( null !== $data ) {

			$data = affiliate_wp()->utils->process_request_data( $data, 'user_name' );

			if ( ! empty( $data['user_id'] ) ) {
				if ( $affiliate_id = affwp_get_affiliate_id( absint( $data['user_id'] ) ) ) {
					$this->affiliate_id = $affiliate_id;
				}
			}

			if ( ! empty( $data['start_date'] ) ) {
				$this->date['start'] = sanitize_text_field( $data['start_date'] );
			}

			if ( ! empty( $data['end_date'] ) ) {
				$this->date['end'] = sanitize_text_field( $data['end_date'] );
			}

			if ( ! empty( $data['status'] ) ) {
				$this->status = sanitize_text_field( $data['status'] );

				if ( 0 === $this->status ) {
					$this->status = '';
				}
			}
		}

	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {
		$total_to_export = $this->get_total_count();

		if ( false === $total_to_export ) {
			$args = array(
				'number'       => - 1,
				'fields'       => 'ids',
				'status'       => $this->status,
				'date'         => $this->date,
				'affiliate_id' => $this->affiliate_id,
			);

			$total_to_export = affiliate_wp()->referrals->get_referrals( $args, true );

			$this->set_total_count( $total_to_export );
		}
	}

	/**
	 * Retrieves the columns for the CSV export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array The list of CSV columns.
	 */
	public function csv_cols() {
		return array(
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
			'date'          => __( 'Date', 'affiliate-wp' ),
		);
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {
		if ( is_null( $this->status ) ) {
			return new \WP_Error( 'no_status_found', __( 'No valid referral status was selected for export.', 'affiliate-wp' ) );
		}

		return parent::process_step();
	}

	/**
	 * Retrieves the referral export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Data for a single step of the export.
	 */
	public function get_data() {

		$args = array(
			'status'       => 'unpaid',
			'date'         => $this->date,
			'affiliate_id' => $this->affiliate_id,
			'number'       => $this->per_step,
			'offset'       => $this->get_offset(),
		);

		$data         = array();
		$data_buff    = array();
		$affiliates   = array();
		$referral_ids = array();
		$referrals    = affiliate_wp()->referrals->get_referrals( $args );

		if ( $referrals ) {

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

		/*$test111 = $this->get_stat_affil();
		array_push( $data, $data_buff );
		array_push( $data, $test111 );*/
		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( $data, true ), FILE_APPEND | LOCK_EX );
		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );
		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "==========================================", true ), FILE_APPEND | LOCK_EX );
		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );


		$data = apply_filters( 'affwp_export_get_data', $data );
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );




		return $data;
	}

	/**
	 * Retrieves a message for the given code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 *
	 * @return string Message.
	 */
	public function get_message( $code ) {

		switch ( $code ) {

			case 'done':
				$final_count = $this->get_current_count();

				$message = sprintf(
					_n(
						'%s referral was successfully exported.',
						'%s referrals were successfully exported.',
						$final_count,
						'affiliate-wp'
					), number_format_i18n( $final_count )
				);
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

}
