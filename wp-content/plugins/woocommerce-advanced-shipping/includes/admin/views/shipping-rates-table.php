<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$methods           = get_posts( array( 'posts_per_page' => '-1', 'post_type' => 'was', 'post_status' => array( 'draft', 'publish' ), 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$wc_status_options = wp_parse_args( get_option( 'woocommerce_status_options', array() ), array( 'shipping_debug_mode' => 0 ) );

?><tr valign="top">
	<th scope="row" class="titledesc"><?php
		_e( 'Shipping rates', 'woocommerce-advanced-shipping' ); ?>:<br />
	</th>
	<td class="forminp" id="<?php echo esc_attr( $this->id ); ?>_shipping_methods">

		<table class='wp-list-table wpc-conditions-post-table widefat'>

			<thead>
				<tr>
					<th scope="col" style='width: 17px;' class="column-sort"></th>
					<th scope="col" style='padding-left: 0;' class="column-primary">
						<div style='padding-left: 10px;'><?php _e( 'Title', 'woocommerce-advanced-shipping' ); ?></div>
					</th>
					<th scope="col" style='padding-left: 10px;' class="column-title"><?php _e( 'Shipping title', 'woocommerce-advanced-shipping' ); ?></th>
					<th scope="col" style='padding-left: 10px; width: 100px;' class="column-cost"><?php _e( 'Shipping cost', 'woocommerce-advanced-shipping' ); ?></th>
					<th scope="col" style='width: 70px;' class="column-conditions"><?php _e( '# Groups', 'woocommerce-advanced-shipping' ); ?></th>
					<th scope="col" style='width: 70px;' class="column-priority"><?php _e( 'Priority', 'woocommerce-advanced-shipping' ); ?>&nbsp;<span class="tips" data-tip="<?php echo esc_attr( __( 'Available methods will be chosen by default in this order. If multiple methods have the same priority, they will be sorted by cost. <br/>Will mix with other default shipping rate priorities.', 'woocommerce-advanced-shipping' ) ); ?>">[?]</span></th>
				</tr>
			</thead>
			<tbody><?php

				$i = 0;
				foreach ( $methods as $method ) :

					$method_details = get_post_meta( $method->ID, '_was_shipping_method', true );
					$conditions     = get_post_meta( $method->ID, '_was_shipping_method_conditions', true );
					$priority       = get_post_meta( $method->ID, '_priority', true );

					$alt = ( $i++ ) % 2 == 0 ? 'alternate' : '';
					?><tr class='<?php echo $alt; ?>'>

						<td class='sort'>
							<input type='hidden' name='sort[]' value='<?php echo absint( $method->ID ); ?>' />
						</td>
						<td class="column-primary">
							<strong>
								<a href='<?php echo get_edit_post_link( $method->ID ); ?>' class='row-title' title='<?php _e( 'Edit Method', 'woocommerce-advanced-shipping' ); ?>'><?php
									if ( $wc_status_options['shipping_debug_mode'] ) {
										echo '<small>#' . absint( $method->ID ) . '</small> - ';
									}
									echo _draft_or_post_title( $method->ID );
								?></a><?php
								echo _post_states( $method );
							?></strong>
							<div class='row-actions'>
								<span class='edit'>
									<a href='<?php echo get_edit_post_link( $method->ID ); ?>' title='<?php _e( 'Edit Method', 'woocommerce-advanced-shipping' ); ?>'>
										<?php _e( 'Edit', 'woocommerce-advanced-shipping' ); ?>
									</a>
									|
								</span>
								<span class='trash'>
									<a href='<?php echo get_delete_post_link( $method->ID ); ?>' title='<?php _e( 'Delete Method', 'woocommerce-advanced-shipping' ); ?>'>
										<?php _e( 'Delete', 'woocommerce-advanced-shipping' ); ?>
									</a>
								</span>
							</div>
							<button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details' ); ?></span></button>
						</td>
						<td class="column-title" data-colname="<?php _e( 'Shipping', 'woocommerce-advanced-shipping' ); ?>"><?php
							if ( empty( $method_details['shipping_title'] ) ) :
								_e( 'Shipping', 'woocommerce-advanced-shipping' );
							else :
								echo wp_kses_post( $method_details['shipping_title'] );
							endif;
						?></td>
						<td class="column-cost" data-colname="<?php _e( 'Shipping cost', 'woocommerce-advanced-shipping' ); ?>"><?php
							echo isset( $method_details['shipping_cost'] ) ? wp_kses_post( wc_price( $method_details['shipping_cost'] ) ) : '';
						?></td>
						<td  class="column-conditions" data-colname="<?php _e( 'Condition groups', 'woocommerce-advanced-shipping' ); ?>"><?php
							echo absint( count( $conditions ) );
						?></td>
						<td width="1%" class="priority column-priority" data-colname="<?php _e( 'Priority', 'woocommerce-advanced-shipping' ); ?>">
							<input type="number" step="1" min="0" name="method_priority[<?php echo esc_attr( $method->ID ); ?>]" value="<?php echo absint( max( $priority, 1 ) ); ?>" />
						</td>

					</tr><?php

				endforeach;

				if ( empty( $method ) ) :
					?><tr>
						<td colspan='6'><?php _e( 'There are no Advanced Shipping conditions. Yet...', 'woocommerce-advanced-shipping' ); ?></td>
					</tr><?php
				endif;

			?></tbody>
			<tfoot>
				<tr>
					<th colspan='6' style='padding-left: 10px;'>
						<a href='<?php echo admin_url( 'post-new.php?post_type=was' ); ?>' class='add button'>
							<?php _e( 'Add Shipping Rate', 'woocommerce-advanced-shipping' ); ?>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>
