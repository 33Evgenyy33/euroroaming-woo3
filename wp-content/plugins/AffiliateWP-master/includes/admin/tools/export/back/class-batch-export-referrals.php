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

	public $report = array();
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
			'campaign'    => 'Партнер',
			'email'       => __( 'Email', 'affiliate-wp' ),
			'coupon'      => 'Промокод',
			'description' => __( 'Description', 'affiliate-wp' ),
			'reference'   => __( 'Reference', 'affiliate-wp' ),
			'amount'      => __( 'Amount', 'affiliate-wp' ),
			'date'        => __( 'Date', 'affiliate-wp' )
			/*'billing_partner' => 'Форма оплаты',
			'actual_address' => 'Фактический адрес'*/
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
		$data_buff         = array();
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

				$data_buff[] = array(
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

		$test111 = $this->get_stat_affil();
		array_push($data, $data_buff);
		array_push($data, $test111);

		$data = apply_filters( 'affwp_export_get_data', $data );
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( $data, true ), FILE_APPEND | LOCK_EX );
//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );

		return $data;
	}

	/*******************удаление из массива дублей по key*******************/
	public function unique_multidim_array( $array, $key ) {
		$temp_array = array();
		$i          = 0;
		$key_array  = array();

		foreach ( $array as $val ) {
			if ( ! in_array( $val[ $key ], $key_array ) ) {
				$key_array[ $i ]  = $val[ $key ];
				$temp_array[ $i ] = $val;
				$i ++;
			}

		}

		//unset($val);
		return $temp_array;
	}

	public function get_stat_affil() {

		$args = array(
			'status'       => 'unpaid',
			'date'         => ! empty( $this->date ) ? $this->date : '',
			'affiliate_id' => $this->affiliate_id,
			'number'       => - 1
		);

		$data_referrals = array();
		$referrals      = affiliate_wp()->referrals->get_referrals( $args );

		//print_r($referrals);

		if ( $referrals ) {

			foreach ( $referrals as $referral ) {

				$data_referrals[] = array(
					'affiliate_id'    => $referral->affiliate_id,
					'campaign'        => affwp_get_affiliate_name( $referral->affiliate_id ),
					'email'           => affwp_get_affiliate_email( $referral->affiliate_id ),
					'reference'       => $referral->reference,
					'amount'          => $referral->amount,
					'payment_details' => get_userdata( affwp_get_affiliate( $referral->affiliate_id )->user_id )->billing_partner
				);

			}
			//unset($referral);

		}

		asort( $data_referrals );

		//return $data_referrals;

		$affiliates = $this->unique_multidim_array( $data_referrals, 'affiliate_id' );

		//return $affiliates;

		//$report = array();

		$report = array();

		/*******************************
		 * Orange - 58961
		 * Vodafone - 58981
		 * Ortel - 58995
		 * EuropaSim - 59104
		 * Globalsim - 59021
		 * Globalsim Internet - 59004
		 * Globalsim USA - 59135
		 * TravelChat - 59130
		 * Three - 59140
		 * ******************************/

		foreach ( $affiliates as $affiliate ) {

			$report[] = array(
				'affiliate_id'    => $affiliate['affiliate_id'],
				'campaign'        => $affiliate['campaign'],
				'email'           => $affiliate['email'],
				'simcards_qty'    => array(
					'58961' => 0,
					'58981' => 0,
					'58995' => 0,
					'59104' => 0,
					'59021' => 0,
					'59004' => 0,
					'59135' => 0,
					'59130' => 0,
					'59140' => 0,
				),
				'amount'          => 0,
				'payment_details' => ''
			);

		}

		$i = 0;
		foreach ( $data_referrals as $data_referral ) {

			$test1 = wc_get_order( $data_referral['reference'] );
			/*if ($test1 == '') {
				file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( 'да', true ), FILE_APPEND | LOCK_EX );
				file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/aff_wp.txt", print_r( "\n", true ), FILE_APPEND | LOCK_EX );
			}*/

			if ( $test1 == '' ) {
				continue;
			}

			if ( $affiliates[ $i ]['affiliate_id'] != $data_referral['affiliate_id'] ) {
				$i ++;
			}

			/* echo '$affiliates = ' . $affiliates[$i]['affiliate_id'] . '<br>';
			 echo '$data_referral = ' . $data_referral['affiliate_id'] . '<br>';
			 echo 'order: ' . $data_referral['reference'] . '<br><br>';*/

			$report[ $i ]['amount']          += $data_referral['amount'];
			$report[ $i ]['payment_details'] = $data_referral['payment_details'];

			$order = new WC_Order( $data_referral['reference'] );
			$items = $order->get_items();

			foreach ( $items as $key => $item ) {

				if ( get_post_meta( $item['product_id'], '_affwp_' . 'woocommerce' . '_referrals_disabled', true ) ) {
					continue; // Referrals are disabled on this product
				}

				//Пропустить симки в отчете
				//if  ($item['product_id'] == 41120 || $item['product_id'] == 48067) continue; //travelchat

				/*if ($data_referral['affiliate_id'] == 16)
					echo 'товар: ' . $item['product_id'] . ' кол-во: ' . $item['qty'] . '<br>';*/

				$report[ $i ]['simcards_qty'][ $item['product_id'] ] += $item['qty'];

			}

			unset( $order );
		}

		return $report;

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
