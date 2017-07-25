<?php
/**
 * Update WC_POS to 3.2.1
 *
 * @author      Actuality Extensions
 * @category    Admin
 * @package     WC_POS/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$wpdb->hide_errors();
$result = $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}wc_point_of_sale_sale_reports`");
$result = $wpdb->query("CREATE TABLE `{$wpdb->prefix}wc_point_of_sale_sale_reports` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `register_id` int(11) NOT NULL,
                          `register_name` varchar(255) NOT NULL,
                          `outlet_id` int(11) NOT NULL,
                          `opened` datetime NOT NULL,
                          `closed` datetime NOT NULL,
                          `cashier_id` int(11) NOT NULL,
                          `total_sales` float DEFAULT '0',
                          `report_data` text NOT NULL,
                          PRIMARY KEY (`id`)
                        ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
