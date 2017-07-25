<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Affiliate_WP_Export Class
 *
 * @since 1.0
 */
class Affiliate_WP_Referral_Export extends Affiliate_WP_Export
{

    /**
     * Our export type. Used for export-type specific filters/actions
     * @var string
     * @since 1.0
     */
    public $export_type = 'referrals';

    /**
     * Date
     * @var array
     * @since 1.0
     */
    public $date;

    /**
     * Status
     * @var string
     * @since 1.0
     */
    public $status;

    /**
     * Affiliate ID
     * @var int
     * @since 1.0
     */
    public $affiliate = null;

    /**
     * Set the CSV columns
     *
     * @access public
     * @since 1.0
     * @return array $cols All the columns
     */
    public function csv_cols()
    {
        $cols = array(
            'campaign' => 'Партнер',
            'email' => __('Email', 'affiliate-wp'),
            'coupon' => 'Промокод',
            'description' => __('Description', 'affiliate-wp'),
            'reference' => __('Reference', 'affiliate-wp'),
            'amount' => __('Amount', 'affiliate-wp'),
            'date' => __('Date', 'affiliate-wp')
            /*'billing_partner' => 'Форма оплаты',
            'actual_address' => 'Фактический адрес'*/
        );
        return $cols;
    }

    /**
     * Get the data being exported
     *
     * @access public
     * @since 1.0
     * @return array $data Data for Export
     */
    public function get_data()
    {

        $args = array(
            'status' => 'unpaid',
            'date' => !empty($this->date) ? $this->date : '',
            'affiliate_id' => $this->affiliate,
            'number' => -1
        );

        $data = array();
        $affiliates = array();
        $referral_ids = array();
        $referrals = affiliate_wp()->referrals->get_referrals($args);


        if ($referrals) {

            foreach ($referrals as $referral) {

                $order = new WC_Order($referral->reference);
                $coupon = '';
                if (!empty($order)) {
                    $coupons = $order->get_used_coupons();
                    $order_url = admin_url('post.php?post=' . $order->id . '&action=edit');
                    if (!empty($coupons)) {
                        $coupon = $coupons[0];
                    } else {
                        $coupon = 'без промокода';
                    }
                }

                $data[] = array(
                    'campaign' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->billing_company,
                    'email' => affwp_get_affiliate_email($referral->affiliate_id),
                    'coupon' => $coupon,
                    'description' => strip_tags(str_replace(',', "\r\n", $referral->description)),
                    'reference' => $order_url,
                    'amount' => $referral->amount,
                    'date' => $referral->date
                    /*'billing_partner' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->billing_partner,
                    'actual_address' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->actual_address*/
                );

            }

        }

        $data = apply_filters('affwp_export_get_data', $data);
        $data = apply_filters('affwp_export_get_data_' . $this->export_type, $data);

        return $data;
    }

    /*******************удаление из массива дублей по key*******************/
    function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
                $i++;
            }

        }
        //unset($val);
        return $temp_array;
    }

    function get_stat_affil()
    {

        $args = array(
            'status' => 'unpaid',
            'date' => !empty($this->date) ? $this->date : '',
            'affiliate_id' => $this->affiliate,
            'number' => -1
        );

        $data_referrals = array();
        $referrals = affiliate_wp()->referrals->get_referrals($args);

        //print_r($referrals);

        if ($referrals) {

            foreach ($referrals as $referral) {

                $data_referrals[] = array(
                    'affiliate_id' => $referral->affiliate_id,
                    'campaign' => affwp_get_affiliate_name( $referral->affiliate_id ),
                    'email' => affwp_get_affiliate_email($referral->affiliate_id),
                    'reference' => $referral->reference,
                    'amount' => $referral->amount,
                    'payment_details' => get_userdata(affwp_get_affiliate($referral->affiliate_id)->user_id)->billing_partner
                );

            }
            //unset($referral);

        }

        asort($data_referrals);

        //return $data_referrals;

        $affiliates = $this->unique_multidim_array($data_referrals, 'affiliate_id');

        //return $affiliates;

        //$report = array();

        $report = array();

        /*******************************
         * Orange - 18402
         * Vodafone - 18438
         * Ortel - 18446
         * EuropaSim - 28328
         * Globalsim - 18455
         * Globalsim Internet - 18453
         * ******************************/

        foreach ($affiliates as $affiliate) {

            $report[] = array(
                'affiliate_id' => $affiliate['affiliate_id'],
                'campaign' => $affiliate['campaign'],
                'email' => $affiliate['email'],
                'simcards_qty' => array(
                    '18402' => 0,
                    '18438' => 0,
                    '18446' => 0,
                    '28328' => 0,
                    '18455' => 0,
                    '18453' => 0
                ),
                'amount' => 0,
                'payment_details' => ''
            );

        }

        $i = 0;
        foreach ($data_referrals as $data_referral) {

            if ($affiliates[$i]['affiliate_id'] != $data_referral['affiliate_id']) {
                $i++;
            }

            /* echo '$affiliates = ' . $affiliates[$i]['affiliate_id'] . '<br>';
             echo '$data_referral = ' . $data_referral['affiliate_id'] . '<br>';
             echo 'order: ' . $data_referral['reference'] . '<br><br>';*/

            $report[$i]['amount'] += $data_referral['amount'];
            $report[$i]['payment_details'] = $data_referral['payment_details'];

            $order = new WC_Order($data_referral['reference']);
            $items = $order->get_items();

            foreach ($items as $key => $item) {

                if (get_post_meta($item['product_id'], '_affwp_' . 'woocommerce' . '_referrals_disabled', true)) {
                    continue; // Referrals are disabled on this product
                }

                /*if ($data_referral['affiliate_id'] == 16)
                    echo 'товар: ' . $item['product_id'] . ' кол-во: ' . $item['qty'] . '<br>';*/

                $report[$i]['simcards_qty'][$item['product_id']] += $item['qty'];

            }

            unset($order);
        }

        return $report;

    }

}
