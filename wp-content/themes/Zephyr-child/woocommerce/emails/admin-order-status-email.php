<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager/Templates
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Default admin order status email template.
 *
 * @type string $email_heading The email heading.
 * @type string $email_body_text The email body.
 * @type \WC_Order $order The order object.
 * @type bool $sent_to_admin Whether email is sent to admin.
 * @type bool $plain_text Whether email is plain text.
 * @type bool $show_download_links Whether to show download links.
 * @type bool $show_purchase_note Whether to show purchase note.
 *
 * @since 1.0.0
 * @version 1.7.0
 */
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php if ( $email_body_text ) : ?>
    <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: transparent"
           cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
        <tbody>
        <tr style="vertical-align: top">
            <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
                width="100%">
                <!--[if gte mso 9]>
                <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td>
                <![endif]-->
                <!--[if (IE)]>
                <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                <table class="container"
                       style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                       cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                    <tbody>
                    <tr style="vertical-align: top">
                        <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
                            width="100%">
                            <table class="block-grid"
                                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #000000;background-color: #fff"
                                   cellpadding="0" cellspacing="0" width="100%" bgcolor="#fff">
                                <tbody>
                                <tr style="vertical-align: top">
                                    <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                        <!--[if (gte mso 9)|(IE)]>
                                        <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                               cellspacing="0" border="0">
                                            <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                        <td valign="top" width="600"><![endif]-->
                                        <div class="col num12"
                                             style="display: inline-block;vertical-align: top;width: 100%">
                                            <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                   cellpadding="0" cellspacing="0" align="center" width="100%"
                                                   border="0">
                                                <tbody>
                                                <tr style="vertical-align: top">
                                                    <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 1px solid #bbbbbb;border-right: 1px solid #bbbbbb;border-bottom: 1px solid #bbbbbb;border-left: 1px solid #bbbbbb">
                                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                               cellpadding="0" cellspacing="0" width="100%">
                                                            <tbody>
                                                            <tr style="vertical-align: top">
                                                                <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
                                                                    <div style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                                                        <div style="font-size:16px;line-height:17px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
																			<?php
																			$some_field = get_post_meta( $order->get_id(), 'tel', true );
																			if($some_field != '') {
																				echo '<p>'.'Присвоенный номер: '.$some_field.'</p>';
																			}
																			echo $email_body_text;
																			?>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                        <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!--[if mso]>
                </td></tr></table>
                <![endif]-->
                <!--[if (IE)]>
                </td></tr></table>
                <![endif]-->
            </td>
        </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php //do_action( 'woocommerce_email_before_order_table', $order, true, false ); ?>

<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: transparent"
       cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
    <tbody>
    <tr style="vertical-align: top">
        <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
            width="100%">
            <!--[if gte mso 9]>
            <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td>
            <![endif]-->
            <!--[if (IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->
            <table class="container"
                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                   cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
                        width="100%">
                        <table class="block-grid"
                               style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #000000;background-color: #fff"
                               cellpadding="0" cellspacing="0" width="100%" bgcolor="#fff">
                            <tbody>
                            <tr style="vertical-align: top">
                                <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                           cellspacing="0" border="0">
                                        <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                    <td valign="top" width="600"><![endif]-->
                                    <div class="col num12"
                                         style="display: inline-block;vertical-align: top;width: 100%">
                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                               cellpadding="0" cellspacing="0" align="center" width="100%"
                                               border="0">
                                            <tbody>
                                            <tr style="vertical-align: top">
                                                <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 1px solid #bbbbbb;border-bottom: 1px solid #bbbbbb;border-left: 1px solid #bbbbbb">
                                                    <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                           cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
                                                                <div style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                                                    <div style="font-size:16px;line-height:17px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
                                                                        <h2>
                                                                            <a href="<?php echo admin_url( 'post.php?post=' . SV_WC_Order_Compatibility::get_prop( $order, 'id' ) . '&action=edit' ); ?>">
																				<?php /* translators: Placeholders: %s - order number */
																				printf( __( 'Order: %s', 'woocommerce-order-status-manager' ), $order->get_order_number() ); ?>
																				<?php $order_timestamp = SV_WC_Order_Compatibility::get_date_created( $order )->getTimestamp(); ?>
                                                                            </a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', $order_timestamp ), date_i18n( wc_date_format(), $order_timestamp ) ); ?>)
                                                                        </h2>

                                                                        <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
                                                                            <thead>
                                                                            <tr>
                                                                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'woocommerce-order-status-manager' ); ?></th>
                                                                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'woocommerce-order-status-manager' ); ?></th>
                                                                                <th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'woocommerce-order-status-manager' ); ?></th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
																			<?php

																			$email_order_items = array(
																				'show_sku' => true,
																			);

																			echo SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ? wc_get_email_order_items( $order, $email_order_items ) : $order->email_order_items_table( $email_order_items );

																			?>
                                                                            </tbody>
                                                                            <tfoot>
																			<?php
																			if ( $totals = $order->get_order_item_totals() ) {
																				$i = 0;
																				foreach ( $totals as $total ) {
																					$i++;
																					?><tr>
                                                                                    <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
                                                                                    <td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
                                                                                    </tr><?php
																				}
																			}
																			?>
                                                                            </tfoot>
                                                                        </table>

																		<?php do_action( 'woocommerce_email_after_order_table', $order, true, false ); ?>

																		<?php do_action( 'woocommerce_email_order_meta', $order, true, false ); ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                    <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--[if mso]>
            </td></tr></table>
            <![endif]-->
            <!--[if (IE)]>
            </td></tr></table>
            <![endif]-->
        </td>
    </tr>
    </tbody>
</table>

<table style="border-spacing: 0;border-collapse: collapse;vertical-align: top;background-color: transparent"
       cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
    <tbody>
    <tr style="vertical-align: top">
        <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
            width="100%">
            <!--[if gte mso 9]>
            <table id="outlookholder" border="0" cellspacing="0" cellpadding="0" align="center">
                <tr>
                    <td>
            <![endif]-->
            <!--[if (IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->
            <table class="container"
                   style="border-spacing: 0;border-collapse: collapse;vertical-align: top;max-width: 600px;margin: 0 auto;text-align: inherit"
                   cellpadding="0" cellspacing="0" align="center" width="100%" border="0">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top"
                        width="100%">
                        <table class="block-grid"
                               style="border-spacing: 0;border-collapse: collapse;vertical-align: top;width: 100%;max-width: 600px;color: #000000;background-color: #fff"
                               cellpadding="0" cellspacing="0" width="100%" bgcolor="#fff">
                            <tbody>
                            <tr style="vertical-align: top">
                                <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;text-align: center;font-size: 0">
                                    <!--[if (gte mso 9)|(IE)]>
                                    <table width="100%" align="center" bgcolor="transparent" cellpadding="0"
                                           cellspacing="0" border="0">
                                        <tr><![endif]--><!--[if (gte mso 9)|(IE)]>
                                    <td valign="top" width="600"><![endif]-->
                                    <div class="col num12"
                                         style="display: inline-block;vertical-align: top;width: 100%">
                                        <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                               cellpadding="0" cellspacing="0" align="center" width="100%"
                                               border="0">
                                            <tbody>
                                            <tr style="vertical-align: top">
                                                <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;background-color: transparent;padding-top: 5px;padding-right: 0px;padding-bottom: 5px;padding-left: 0px;border-top: 0px solid transparent;border-right: 1px solid #bbbbbb;border-bottom: 0px dashed #BBBBBB;border-left: 1px solid #bbbbbb">
                                                    <table style="border-spacing: 0;border-collapse: collapse;vertical-align: top"
                                                           cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                        <tr style="vertical-align: top">
                                                            <td style="word-break: normal;border-collapse: collapse !important;vertical-align: top;padding-top: 10px;padding-right: 10px;padding-bottom: 10px;padding-left: 10px">
                                                                <div style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                                                    <div style="font-size:16px;line-height:17px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
                                                                        <h2><?php esc_html_e( 'Customer details', 'woocommerce-order-status-manager' ); ?></h2>
																		<?php if ( $billing_email = SV_WC_Order_Compatibility::get_prop( $order, 'billing_email' ) ) : ?>
                                                                            <p><strong><?php esc_html_e( 'Email:', 'woocommerce-order-status-manager' ); ?></strong> <?php echo $billing_email; ?></p>
																		<?php endif; ?>
																		<?php if ( $billing_phone = SV_WC_Order_Compatibility::get_prop( $order, 'billing_phone' ) ) : ?>
                                                                            <p><strong><?php esc_html_e( 'Tel:', 'woocommerce-order-status-manager' ); ?></strong> <?php echo $billing_phone; ?></p>
																		<?php endif; ?>

																		<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--[if (gte mso 9)|(IE)]></td><![endif]-->
                                    <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--[if mso]>
            </td></tr></table>
            <![endif]-->
            <!--[if (IE)]>
            </td></tr></table>
            <![endif]-->
        </td>
    </tr>
    </tbody>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>
