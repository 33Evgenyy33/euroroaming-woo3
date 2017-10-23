<?php
/**
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
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
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php if ( $order->has_status( 'pending' ) ) : ?>
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
                                                                            <p><?php printf( 'Для оплаты заказа, пожалуйста, воспользуйтесь следующей ссылкой:'); ?></p>
                                                                            <p style="width: 100%;text-align: center;"><?php printf( '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '" style="color: #ffffff;font-weight:normal;text-decoration: none;background: #00bc6a;padding: 9px;font-size: 16px;text-transform: uppercase;text-align: center;">' . __( 'оплатить', 'woocommerce' ) . '</a>'); ?></p>
                                                                            <p><?php printf( '<p>Если у Вас возникли какие-либо трудности с оплатой заказа, пожалуйста, обратитесь к нам за помощью</p>'); ?></p>
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
																		<?php do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email ); ?>
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
																		<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email ); ?>
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


<?php do_action( 'woocommerce_email_footer', $email ); ?>


