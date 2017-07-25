<?php

class Affiliate_WP_PayPal extends Affiliate_WP_Base {

	/**
	 * Get thigns started
	 *
	 * @access  public
	 * @since   1.9
	 */
	public function init() {

		$this->context = 'paypal';

		add_action( 'wp_footer', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'wp_ajax_nopriv_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'init', array( $this, 'process_ipn' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Add JS to site footer for detecting PayPal form submissions
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function scripts() {
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

			$('form').on('submit', function(e) {

				var action = $(this).prop( 'action' );

				if( 'undefined' == action || -1 == action.indexOf( 'paypal.com/cgi-bin/webscr' ) ) {
					return;
				}

				e.preventDefault();

				var $form = $(this);
				var ipn_url = "<?php echo home_url( 'index.php?affwp-listener=paypal' ); ?>";

				$.ajax({
					type: "POST",
					data: {
						action: 'affwp_maybe_insert_paypal_referral'
					},
					url: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
					success: function (response) {

						$form.append( '<input type="hidden" name="custom" value="' + response.data.ref + '"/>' );
						$form.append( '<input type="hidden" name="notify_url" value="' + ipn_url + '"/>' );

						$form.get(0).submit();

					}

				}).fail(function (response) {

					if ( window.console && window.console.log ) {
						console.log( response );
					}

				});

			});
		});
		</script>
<?php
	}

	/**
	 * Create a referral during PayPal form submission if customer was referred
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function maybe_insert_referral() {

		$response = array();

		if( $this->was_referred() ) {

			$reference   = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . time();
			$referral_id = $this->insert_pending_referral( 0.01, $reference, __( 'Pending PayPal referral', 'affiliate-wp' ) );

			if( $referral_id && $this->debug ) {

				$this->log( 'Pending referral created successfully during maybe_insert_referral()' );

			} elseif ( $this->debug ) {

				$this->log( 'Pending referral failed to be created during maybe_insert_referral()' );

			}

			$response['ref'] = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . $referral_id;

		}

		wp_send_json_success( $response );

	}

	/**
	 * Process PayPal IPN requests in order to mark referrals as Unpaid
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function process_ipn() {

		if( empty( $_GET['affwp-listener'] ) || 'paypal' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

		$ipn_data = $_POST;

		if( ! is_array( $ipn_data ) ) {
			wp_parse_str( $ipn_data, $ipn_data );
		}

		$verified = $this->verify_ipn( $ipn_data );

		if( ! $verified ) {
			die( 'IPN verification failed' );
		}

		$to_process = array(
			'web_accept',
			'cart',
			'subscr_payment',
			'express_checkout',
			'recurring_payment',
		);

		if( ! empty( $ipn_data['txn_type'] ) && ! in_array( $ipn_data['txn_type'], $to_process ) ) {
			return;
		}

		if( empty( $ipn_data['mc_gross'] ) ) {

			if( $this->debug ) {
				$this->log( 'IPN not processed because mc_gross was empty' );
			}

			return;
		}

		if( empty( $ipn_data['custom'] ) ) {

			if( $this->debug ) {
				$this->log( 'IPN not processed because custom was empty' );
			}

			return;
		}

		$total        = sanitize_text_field( $ipn_data['mc_gross'] );
		$custom       = explode( '|', $ipn_data['custom'] );
		$visit_id     = $custom[0];
		$affiliate_id = $custom[1];
		$referral_id  = $custom[2];
		$visit        = affwp_get_visit( $visit_id );
		$referral     = affwp_get_referral( $referral_id );

		if( empty( $affiliate_id ) ) {

			if( $this->debug ) {
				$this->log( 'IPN not processed because affiliate ID was empty' );
			}

			return;
		}

		if( ! $visit || ! $referral ) {

			if( $this->debug ) {

				if( ! $visit ) {

					$this->log( 'Visit not successfully retrieved during process_ipn()' );

				}

				if( ! $referral ) {

					$this->log( 'Referral not successfully retrieved during process_ipn()' );

				}

			}

			die( 'Missing visit or referral data' );
		}

		if( $this->debug ) {

			$this->log( 'Referral ID (' . $referral->ID . ') successfully retrieved during process_ipn()' );

		}

		if( 'completed' === strtolower( $ipn_data['payment_status'] ) ) {

			if( 'pending' !== $referral->status ) {

				if( $this->debug ) {

					$this->log( 'Referral has status other than Pending during process_ipn()' );

				}

				die( 'Referral not pending' );
			}

			$visit->set( 'referral_id', $referral->ID, true );

			$reference   = sanitize_text_field( $ipn_data['txn_id'] );
			$description = ! empty( $ipn_data['item_name'] ) ? sanitize_text_field( $ipn_data['item_name'] ) : sanitize_text_field( $ipn_data['payer_email'] );
			$amount      = $this->calculate_referral_amount( $total, $reference, 0, $referral->affiliate_id );

			$referral->set( 'description', $description );
			$referral->set( 'amount', $amount );
			$referral->set( 'reference', $reference );

			$this->log( 'Referral updated in preparation for save(): ' . print_r( $referral->to_array(), true ) );

			if( $referral->save() ) {

				$this->log( 'Referral saved: ' . print_r( $referral->to_array(), true ) );

				$completed = $this->complete_referral( $referral );

				if( $completed ) {

					if( $this->debug ) {

						$this->log( 'Referral completed successfully during process_ipn()' );

					}

					die( 'Referral completed successfully' );

				} else if ( $this->debug ) {

					$this->log( 'Referral failed to be completed during process_ipn()' );

				}

				die( 'Referral not completed successfully' );

			} else {

				if ( $this->debug ) {

					$this->log( 'Referral not updated successfully during process_ipn()' );

				}

				die( 'Referral not updated successfully' );

			}

		} elseif ( 'refunded' === strtolower( $ipn_data['payment_status'] ) || 'reversed' === strtolower( $ipn_data['payment_status'] ) ) {

			if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {

				if ( $this->debug ) {

					$this->log( 'Referral not rejected because revoke on refund is not enabled' );

				}

				return;
			}

			$this->reject_referral( $referral->reference );

		} else {

			if ( $this->debug ) {

				$this->log( 'Payment status in IPN data not Complete, Refunded, or Reversed' );

			}

		}

	}

	/**
	 * Verify IPN from PayPal
	 *
	 * @access  public
	 * @since   1.9
	 * @return  bool True|false
	*/
	private function verify_ipn( $post_data ) {

		$verified = false;
		$endpoint = array_key_exists( 'test_ipn', $post_data ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
		$args     = wp_unslash( array_merge( array( 'cmd' => '_notify-validate' ), $post_data ) );

		if( $this->debug ) {

			$this->log( 'Data passed to verify_ipn(): ' . print_r( $post_data, true ) );
			$this->log( 'Data to be sent to IPN verification: ' . print_r( $args, true ) );

		}

		$request  = wp_remote_post( $endpoint, array( 'timeout' => 45, 'sslverify' => false, 'httpversion' => '1.1', 'body' => $args ) );
		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if( ! is_wp_error( $request ) && 200 === (int) $code && 'OK' == $message ) {

			if( 'VERIFIED' == strtoupper( $body ) ) {

				$verified = true;

				if( $this->debug ) {

					$this->log( 'IPN successfully verified' );

				}

			} else {

				if( $this->debug ) {

					$this->log( 'IPN response came back as INVALID' );

				}

			}

		} else {

			if( $this->debug ) {

				$this->log( 'IPN verification request failed' );
				$this->log( 'Request: ' . print_r( $request, true ) );

			}

		}

		return $verified;
	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'paypal' != $referral->context ) {

			return $reference;

		}

		$url = 'https://www.paypal.com/webscr?cmd=_history-details-from-hub&id=' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_PayPal;