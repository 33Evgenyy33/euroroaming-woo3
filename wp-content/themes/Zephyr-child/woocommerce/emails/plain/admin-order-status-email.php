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
 * Default admin order status email template
 *
 * @since 1.0.0
 * @version 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

if ( $email_body_text ) {
	echo "\n\n";
	echo $email_body_text . "\n\n";
}

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

/* translators: Placeholders: %s - order number */
echo sprintf( __( 'Order number: %s', 'woocommerce-order-status-manager' ), $order->get_order_number() ) . "\n";
/* translators: Placeholders: %s - order link */
echo sprintf( __( 'Order link: %s', 'woocommerce-order-status-manager' ), esc_url( admin_url( 'post.php?post=' . $order->id . '&action=edit' ) ) ) . "\n";
/* translators: Placeholders: %s - order date */
echo sprintf( __( 'Order date: %s', 'woocommerce-order-status-manager' ), date_i18n( __( 'jS F Y', 'woocommerce-order-status-manager' ), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

echo "\n";

if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_2_5() ) {
	echo $order->email_order_items_table( array(
		'show_sku'   => true,
		'plain_text' => true,
	) );
} else {
	echo $order->email_order_items_table( false, true, '', '', '', true );
}

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );

echo __( 'Customer details', 'woocommerce-order-status-manager' ) . "\n";

if ( $order->billing_email ) {
	echo __( 'Email:', 'woocommerce-order-status-manager' ); echo $order->billing_email . "\n";
}

if ( $order->billing_phone ) {
	echo __( 'Tel:', 'woocommerce-order-status-manager' ); ?> <?php echo $order->billing_phone . "\n";
}

wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
