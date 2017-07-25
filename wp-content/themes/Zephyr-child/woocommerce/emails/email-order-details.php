<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$text_align = is_rtl() ? 'right' : 'left';

//do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php if ( ! $sent_to_admin ) : ?>
	<h2><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>
<?php else : ?>
    <h2><a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ); ?>)</h2>
<?php endif; ?>

<table class="td" cellspacing="0" cellpadding="6" style="border: 1px solid #1E73BE;width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="color: #FFFFFF;background-color: #1E73BE;border: 0px solid #e4e4e4;font-size:16px;width: 20%;text-align:center;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="td" scope="col" style="color: #FFFFFF;background-color: #1E73BE;border-left: 1px solid #E4E4E4;width: 1%;border-top: none!important;border-bottom: none!important;font-size:16px;text-align:center;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="td" scope="col" style="color: #FFFFFF;background-color: #4A4A4A;border-style: none!important;font-size:16px;width: 17%;text-align:center;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
    <?php echo wc_get_email_order_items( $order, array(
        'show_sku'      => $sent_to_admin,
        'show_image'    => false,
        'image_size'    => array( 32, 32 ),
        'plain_text'    => $plain_text,
        'sent_to_admin' => $sent_to_admin,
    ) ); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) {
							echo 'border-top: 3px solid #1E73BE!important; border-right: 1px solid #E4E4E4; border-bottom: 1px solid #E4E4E4;border-left: 1px solid #E4E4E4;border: 1px solid #797979;';
						} else {
							echo 'border: 1px solid #797979;';
						}
						?>"><?php echo $total['label']; ?></th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) {
							echo 'border-top: 3px solid #1E73BE!important; border-right: 1px solid #E4E4E4; border-bottom: 1px solid #E4E4E4; border-left: 1px solid #E4E4E4;border: 1px solid #797979;';
						} else {
							echo 'border: 1px solid #797979;';
						}
						?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
