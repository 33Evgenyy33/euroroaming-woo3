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
 * needs please refer to http://docs.woothemes.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @package     WC-Order-Status-Manager/Templates
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Default customer order status email template
 *
 * @since 1.0.0
 * @version 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

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
																		<div style="font-size:16px;line-height:19px;color:#555555;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;text-align:left;">
																			<?php
																			$some_field = get_post_meta( $order->id, 'tel', true );
																			if($some_field != '') {
																				$billing_first = get_post_meta( $order->id, 'billing_first_name', true );
																				echo '<p>Уважаемый(-ая) ' . $order->billing_first_name . ',' . '</p>' . '<p>' . 'Ваша сим-карта Vodafone активирована, присвоен номер ' . $some_field . '</p>';
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

<?php //do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

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
																		<?php
																		$sent_to_admin ='';
																		$plain_text ='';
																		$email ='';
																		do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email ); ?>
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
																		<?php
																		$sent_to_admin ='';
																		$plain_text ='';
																		$email ='';
																		do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>
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
