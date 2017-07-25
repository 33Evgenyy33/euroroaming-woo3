<?php
$referral = affwp_get_referral( absint( $_GET['referral_id'] ) );

$payout = affwp_get_payout( $referral->payout_id );

$disabled    = disabled( (bool) $payout, true, false );
$payout_link = add_query_arg( array(
	'page'      => 'affiliate-wp-payouts',
	'action'    => 'view_payout',
	'payout_id' => $payout ? $payout->ID : 0
), admin_url( 'admin.php' ) );

?>
<div class="wrap">

	<h2><?php _e( 'Edit Referral', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_edit_referral">

		<?php do_action( 'affwp_edit_referral_top', $referral ); ?>

		<table class="form-table">


			<tr class="form-row form-required">

				<th scope="row">
					<label for="affiliate_id"><?php _e( 'Affiliate ID', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="text" name="affiliate_id" id="affiliate_id" value="<?php echo esc_attr( $referral->affiliate_id ); ?>" disabled="disabled"/>
					<p class="description"><?php _e( 'The affiliate&#8217;s ID this referral belongs to. This value cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<?php if ( $payout ) : ?>
				<tr class="form-row form-required">

					<th scope="row">
						<label for="payout_id"><?php _e( 'Payout ID', 'affiliate-wp' ); ?></label>
					</th>

					<td>
						<input class="small-text" type="text" name="payout_id" id="affiliate_id" value="<?php echo esc_attr( $payout->ID ); ?>" disabled="disabled"/>
						<?php
						/* translators: 1: View payout link, 2: payout amount */
						printf( __( '%1$s | Total: %2$s', 'affiliate-wp'),
							sprintf( '<a href="%1$s">%2$s</a>',
								esc_url( $payout_link ),
								esc_html_x( 'View', 'payout', 'affiliate-wp' )
							),
							affwp_currency_filter( affwp_format_amount( $payout->amount ) )
						)
						?>
					</td>

				</tr>
			<?php endif; ?>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="amount"><?php _e( 'Amount', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="amount" id="amount" value="<?php echo esc_attr( $referral->amount ); ?>" <?php echo $disabled; ?>/>
					<?php if ( $payout ) : ?>
						<p class="description"><?php esc_html_e( 'The referral amount cannot be changed once it has been included in a payout.', 'affiliate-wp' ); ?></p>
					<?php else : ?>
						<p class="description"><?php _e( 'The amount of the referral, such as 15.', 'affiliate-wp' ); ?></p>
					<?php endif; ?>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Date', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="date" id="date" value="<?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $referral->date ) ) ); ?>" disabled="disabled" />
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="description"><?php _e( 'Description', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<textarea name="description" id="description" rows="5" cols="60"><?php echo esc_html( $referral->description ); ?></textarea>
					<p class="description"><?php _e( 'Enter a description for this referral.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="reference"><?php _e( 'Reference', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="reference" id="reference" value="<?php echo esc_attr( $referral->reference ); ?>" />
					<p class="description"><?php _e( 'Enter a reference for this referral (optional). Usually this would be the transaction ID of the associated purchase.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">
				<?php $readonly = __checked_selected_helper( true, ! empty( $referral->context ), false, 'readonly' ); ?>
				<th scope="row">
					<label for="context"><?php _e( 'Context', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="context" id="context" value="<?php echo esc_attr( $referral->context ); ?>" <?php echo $readonly; ?> />
					<p class="description"><?php _e( 'Context for this referral (optional). Usually this is used to help identify the payment system that was used for the transaction.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="status"><?php _e( 'Status', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<select name="status" id="status" <?php echo $disabled; ?>>
						<option value="unpaid"<?php selected( 'unpaid', $referral->status ); ?>><?php _e( 'Unpaid', 'affiliate-wp' ); ?></option>
						<option value="paid"<?php selected( 'paid', $referral->status ); ?>><?php _e( 'Paid', 'affiliate-wp' ); ?></option>
						<option value="pending"<?php selected( 'pending', $referral->status ); ?>><?php _e( 'Pending', 'affiliate-wp' ); ?></option>
						<option value="rejected"<?php selected( 'rejected', $referral->status ); ?>><?php _e( 'Rejected', 'affiliate-wp' ); ?></option>
					</select>
					<?php if ( $payout ) : ?>
						<p class="description"><?php esc_html_e( 'The referral status cannot be changed once it has been included in a payout.', 'affiliate-wp' ); ?></p>
					<?php else : ?>
						<p class="description"><?php _e( 'Select the status of the referral.', 'affiliate-wp' ); ?></p>
					<?php endif; ?>
				</td>

			</tr>

		</table>

		<?php do_action( 'affwp_edit_referral_bottom', $referral ); ?>

		<?php echo wp_nonce_field( 'affwp_edit_referral_nonce', 'affwp_edit_referral_nonce' ); ?>
		<input type="hidden" name="referral_id" value="<?php echo absint( $referral->referral_id ); ?>" />
		<input type="hidden" name="affwp_action" value="process_update_referral" />

		<?php submit_button( __( 'Update Referral', 'affiliate-wp' ) ); ?>

	</form>

</div>
