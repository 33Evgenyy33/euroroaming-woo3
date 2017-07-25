<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements a Totals meta box for the Overview screen.
 *
 * The meta box displays an overview of recent affiliate
 * earnings activity, and related totals during
 * various date ranges.
 *
 * @since 1.9
 * @see   \AffWP\Admin\Meta_Box
 */
class Totals extends Meta_Box implements Meta_Box\Base {

	/**
	 * Initialize.
	 *
	 * Define the meta box name, meta box id,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 * $this->action        = 'affwp_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		$this->action        = 'affwp_overview_meta_boxes';
		$this->meta_box_name = __( 'Totals', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-totals';
		$this->context       = 'primary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content The metabox content.
	 * @since  1.9
	 */
	public function content() { ?>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Paid Earnings', 'Paid Earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Paid Earnings This Month', 'Paid Earnings This Month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Paid Earnings Today', 'Paid Earnings Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>

		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Unpaid Referrals', 'Unpaid Referrals column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid Referrals This Month', 'Unpaid Referrals This Month column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid Referrals Today', 'Unpaid Referrals Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
		<table class="affwp_table">

			<thead>

				<tr>

					<th><?php _ex( 'Unpaid Earnings', 'Unpaid Earnings column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid Earnings This Month', 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Unpaid Earnings Today', 'Unpaid Earnings Today column table header', 'affiliate-wp' ); ?></th>

				</tr>

			</thead>

			<tbody>

				<tr>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
					<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
				</tr>

			</tbody>

		</table>
<?php }
}
