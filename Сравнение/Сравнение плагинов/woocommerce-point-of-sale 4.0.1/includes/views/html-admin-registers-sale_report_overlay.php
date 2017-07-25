<script type="text/javascript">
    var register_id = <?php echo $_GET['report']; ?>
</script>
<div id="sale_report_popup_inner">
    <table>
        <tr>
            <td class="first-col"><?php _e('Register:', 'wc_point_of_sale'); ?></td>
            <td><strong><?php echo $data['name']; ?></strong></td>
        </tr>
        <tr>
            <td class="first-col"><?php _e('Outlet:', 'wc_point_of_sale'); ?></td>
            <td><strong><?php echo $outlet; ?></strong></td>
        </tr>
        <tr>
            <td class="first-col"><?php _e('Opened:', 'wc_point_of_sale'); ?></td>
            <td><strong><?php
                    echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($data['opened'])) . "\n";
                    _e(' at ', 'wc_point_of_sale');
                    echo date_i18n(__('g:i:s A', 'woocommerce'), strtotime($data['opened'])) . "\n";
                    ?></strong></td>
        </tr>
        <tr>
            <td class="first-col"><?php _e('Closed:', 'wc_point_of_sale'); ?></td>
            <td><strong><?php
                    echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($data['closed'])) . "\n";
                    _e(' at ', 'wc_point_of_sale');
                    echo date_i18n(__('g:i:s A', 'woocommerce'), strtotime($data['closed'])) . "\n";
                    ?></strong></td>
        </tr>
    </table>
    <h3><?php _e('Sales', 'wc_point_of_sale'); ?></h3>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
            <th class="manage-column column-order_customer" scope="col">
                <?php _e('Order', 'wc_point_of_sale'); ?>
            </th>
            <th class="manage-column column-order-date" scope="col">
                <?php _e('Date', 'wc_point_of_sale'); ?>
            </th>
            <th class="manage-column column-order-time" scope="col">
                <?php _e('Time', 'wc_point_of_sale'); ?>
            </th>
            <th class="manage-column column-order_total" style="width: 25%;" scope="col">
                <?php _e('Total', 'wc_point_of_sale'); ?>
            </th>
        </tr>
        </thead>
        <?php
        global $wpdb;
        $canceled_orders = array();
        $saved_orders = array();
        $report_opened = $data['opened'];
        $report_closed = $data['closed'];

        $save_order_status = get_option('wc_pos_save_order_status', 'pending');
        $save_order_status = 'wc-' === substr($save_order_status, 0, 3) ? substr($save_order_status, 3) : $save_order_status;

        $sql = "SELECT ID, post_status FROM {$wpdb->posts}
                INNER JOIN {$wpdb->postmeta} reg_id
    ON ( reg_id.post_id = {$wpdb->posts}.ID AND reg_id.meta_key = 'wc_pos_id_register' AND reg_id.meta_value = $rg_id )

    WHERE {$wpdb->posts}.post_type='shop_order' AND ({$wpdb->posts}.post_date BETWEEN '$report_opened' AND '$report_closed') 
            ";
        $results = $wpdb->get_results($sql);
        $payment_methods = array();
        ?>
        <tbody>
        <?php if ($results) {
            foreach ($results as $value) {
                if ($value->post_status == 'wc-cancelled') {
                    $canceled_orders[] = $value->ID;
                    continue;
                }
                if ($value->post_status == 'wc-' . $save_order_status) {
                    $saved_orders[] = $value->ID;
                    continue;
                }
                $the_order = new WC_Order($value->ID);
                ?>
                <tr>
                    <td>
                        <?php

                        echo '<div class="tips" >';

                        if ($the_order->user_id) {
                            $user_info = get_userdata($the_order->user_id);
                        }

                        if (!empty($user_info)) {

                            $username = '<a href="user-edit.php?user_id=' . absint($user_info->ID) . '">';

                            if ($user_info->first_name || $user_info->last_name) {
                                $username .= esc_html(ucfirst($user_info->first_name) . ' ' . ucfirst($user_info->last_name));
                            } else {
                                $username .= esc_html(ucfirst($user_info->display_name));
                            }

                            $username .= '</a>';

                        } else {
                            if ($the_order->billing_first_name || $the_order->billing_last_name) {
                                $username = trim($the_order->billing_first_name . ' ' . $the_order->billing_last_name);
                            } else {
                                $username = __('Guest', 'woocommerce');
                            }
                        }

                        printf(__('%s by %s', 'woocommerce'), '<a href="' . admin_url('post.php?post=' . absint($value->ID) . '&action=edit') . '"><strong>' . esc_attr($the_order->get_order_number()) . '</strong></a>', $username);

                        if ($the_order->billing_email) {
                            echo '<small class="meta email"><a href="' . esc_url('mailto:' . $the_order->billing_email) . '">' . esc_html($the_order->billing_email) . '</a></small>';
                        }

                        echo '</div>';
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('g:i:s A', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td><?php
                        echo esc_html(strip_tags($the_order->get_formatted_order_total()));

                        if ($the_order->payment_method_title) {
                            if (!isset($payment_methods[$the_order->payment_method_title]))
                                $payment_methods[$the_order->payment_method_title] = $the_order->get_total();
                            else
                                $payment_methods[$the_order->payment_method_title] += $the_order->get_total();
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="4"> No sales </td></tr>';
        } ?>
        </tbody>
    </table>

    <?php if (!empty($canceled_orders)) { ?>
        <h3><?php _e('Cancelled', 'wc_point_of_sale'); ?></h3>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th class="manage-column column-order_customer" scope="col">
                    <?php _e('Order', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order-date" scope="col">
                    <?php _e('Date', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order-time" scope="col">
                    <?php _e('Time', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order_total" style="width: 25%;" scope="col">
                    <?php _e('Total', 'wc_point_of_sale'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($canceled_orders as $ID) {
                $the_order = new WC_Order($ID);
                ?>
                <tr>
                    <td>
                        <?php

                        echo '<div class="tips" >';

                        if ($the_order->user_id) {
                            $user_info = get_userdata($the_order->user_id);
                        }

                        if (!empty($user_info)) {

                            $username = '<a href="user-edit.php?user_id=' . absint($user_info->ID) . '">';

                            if ($user_info->first_name || $user_info->last_name) {
                                $username .= esc_html(ucfirst($user_info->first_name) . ' ' . ucfirst($user_info->last_name));
                            } else {
                                $username .= esc_html(ucfirst($user_info->display_name));
                            }

                            $username .= '</a>';

                        } else {
                            if ($the_order->billing_first_name || $the_order->billing_last_name) {
                                $username = trim($the_order->billing_first_name . ' ' . $the_order->billing_last_name);
                            } else {
                                $username = __('Guest', 'woocommerce');
                            }
                        }

                        printf(__('%s by %s', 'woocommerce'), '<a href="' . admin_url('post.php?post=' . absint($value->ID) . '&action=edit') . '"><strong>' . esc_attr($the_order->get_order_number()) . '</strong></a>', $username);

                        if ($the_order->billing_email) {
                            echo '<small class="meta email"><a href="' . esc_url('mailto:' . $the_order->billing_email) . '">' . esc_html($the_order->billing_email) . '</a></small>';
                        }

                        echo '</div>';
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('g:i:s A', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td><?php
                        echo esc_html(strip_tags($the_order->get_formatted_order_total()));

                        if ($the_order->payment_method_title) {
                            if (!isset($payment_methods[$the_order->payment_method_title]))
                                $payment_methods[$the_order->payment_method_title] = $the_order->get_total();
                            else
                                $payment_methods[$the_order->payment_method_title] += $the_order->get_total();
                        }
                        ?>
                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    <?php } ?>
    <?php if (!empty($saved_orders)) { ?>
        <h3><?php _e('Saved', 'wc_point_of_sale'); ?></h3>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th class="manage-column column-order_customer" scope="col">
                    <?php _e('Order', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order-date" scope="col">
                    <?php _e('Date', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order-time" scope="col">
                    <?php _e('Time', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-order_total" style="width: 25%;" scope="col">
                    <?php _e('Total', 'wc_point_of_sale'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($saved_orders as $ID) {
                $the_order = new WC_Order($ID);
                ?>
                <tr>
                    <td>
                        <?php

                        echo '<div class="tips" >';

                        if ($the_order->user_id) {
                            $user_info = get_userdata($the_order->user_id);
                        }

                        if (!empty($user_info)) {

                            $username = '<a href="user-edit.php?user_id=' . absint($user_info->ID) . '">';

                            if ($user_info->first_name || $user_info->last_name) {
                                $username .= esc_html(ucfirst($user_info->first_name) . ' ' . ucfirst($user_info->last_name));
                            } else {
                                $username .= esc_html(ucfirst($user_info->display_name));
                            }

                            $username .= '</a>';

                        } else {
                            if ($the_order->billing_first_name || $the_order->billing_last_name) {
                                $username = trim($the_order->billing_first_name . ' ' . $the_order->billing_last_name);
                            } else {
                                $username = __('Guest', 'woocommerce');
                            }
                        }

                        printf(__('%s by %s', 'woocommerce'), '<a href="' . admin_url('post.php?post=' . absint($value->ID) . '&action=edit') . '"><strong>' . esc_attr($the_order->get_order_number()) . '</strong></a>', $username);

                        if ($the_order->billing_email) {
                            echo '<small class="meta email"><a href="' . esc_url('mailto:' . $the_order->billing_email) . '">' . esc_html($the_order->billing_email) . '</a></small>';
                        }

                        echo '</div>';
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('jS F Y', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td>
                        <?php
                        echo date_i18n(__('g:i:s A', 'woocommerce'), strtotime($the_order->order_date)) . "\n";
                        ?>
                    </td>
                    <td><?php
                        echo esc_html(strip_tags($the_order->get_formatted_order_total()));

                        if ($the_order->payment_method_title) {
                            if (!isset($payment_methods[$the_order->payment_method_title]))
                                $payment_methods[$the_order->payment_method_title] = $the_order->get_total();
                            else
                                $payment_methods[$the_order->payment_method_title] += $the_order->get_total();
                        }
                        ?>
                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    <?php } ?>

    <?php if (isset($data['detail']['float_cash_management']) && $data['detail']['float_cash_management']) {
        $cash = 0;
        $cash_in = 0;
        $cash_out = 0;
        if (isset($data['detail']['opening_cash_amount']->amount) && $data['detail']['opening_cash_amount']->amount) {
            $cash_in = $cash_in + $data['detail']['opening_cash_amount']->amount;
        }
    } ?>
    <?php if (!empty($payment_methods)): ?>
        <h3><?php _e('Payments', 'wc_point_of_sale'); ?></h3>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th class="manage-column column-payment_type" scope="col">
                    <?php _e('Type', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column column-amount" scope="col">
                    <?php _e('Amount', 'wc_point_of_sale'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($payment_methods as $name => $amount) { ?>
                <?php if ($name == 'Cash on Delivery') {
                    $cash = $cash + $amount;
                } ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo wc_price($amount); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if (isset($data['detail']['float_cash_management']) && $data['detail']['float_cash_management']) { ?>
        <h3><?php _e('Cash Summary', 'wc_point_of_sale'); ?></h3>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <th class="manage-column" scope="col">
                    <?php _e('Type', 'wc_point_of_sale'); ?>
                </th>
                <th class="manage-column" scope="col">
                    <?php _e('Amount', 'wc_point_of_sale'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($this->register->detail->opening_cash_amount) && $data['detail']['opening_cash_amount']->status && $data['detail']['opening_cash_amount']->amount) { ?>
                <tr>
                    <td><?php _e('Opening cash amount', 'wc_point_of_sale'); ?><?php echo ' (' . $data['detail']['opening_cash_amount']->note . ')' ?></td>
                    <td><?php echo wc_price($data['detail']['opening_cash_amount']->amount); ?></td>
                </tr>
            <?php } ?>
            <?php if (isset($data['detail']['cash_management_actions']) && $data['detail']['cash_management_actions']) {
                foreach ($data['detail']['cash_management_actions'] as $cash_action) {
                    switch ($cash_action->type) {
                        case 'add-cash':
                            $cash_in = $cash_in + $cash_action->amount;
                            break;
                        case 'remove-cash':
                            $cash_out = $cash_out + $cash_action->amount;
                            break;
                    }
                } ?>
                <tr>
                    <td><?php _e('Cash in', 'wc_point_of_sale'); ?></td>
                    <td><?php echo wc_price($cash_in); ?></td>
                </tr>
                <tr>
                    <td><?php _e('Cash out', 'wc_point_of_sale'); ?></td>
                    <td>-<?php echo wc_price($cash_out); ?></td>
                </tr>
            <?php } ?>

            <?php foreach ($payment_methods as $name => $amount) { ?>
                <tr>
                    <td><?php _e('Cash', 'wc_point_of_sale'); ?></td>
                    <td><?php echo wc_price($cash); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="cash-result">
            <h3><?php _e('Cash Totals', 'wc_point_of_sale'); ?></h3>
            <table class="wp-list-table widefat fixed posts">
                <tbody>
                <tr>
                    <td><?php _e('Drawer Cash:', 'wc_point_of_sale'); ?></td>
                    <td id="drawer-cash"
                        data-value="<?php echo $total_cash = $cash + $cash_in - $cash_out ?>"><?php echo wc_price($total_cash) ?></td>
                </tr>
                <tr>
                    <td><?php _e('Actual Cash:', 'wc_point_of_sale'); ?></td>
                    <td class="actual-cash"><a class="button"><?php echo ($data['detail']['actual_cash']) ? wc_price($data['detail']['actual_cash']) : __('Enter Remaining Cash', 'wc_point_of_sale'); ?></a></td>
                </tr>
	            <?php if (!isset($_GET['print'])) { ?>
				<tr id="cash-popup" class="cash-popup"><td colspan="2"><p class="description"><?php _e('Enter the remaining cash found in the cash drawer. Use the denominations setup under Point of Sale > Settings > Denominations to enter these values:', 'wc_point_of_sale'); ?></p></td></tr>
                <?php $nominals = get_option('wc_pos_cash_nominal') ?>
                <?php foreach ($nominals as $nominal) { ?>
                    <tr class="nominal-row cash-popup">
                        <td><?php echo wc_price($nominal) ?></td>
                        <td><input type="number" class="nominal" id="nominal-<?php echo $nominal ?>"
                               data-value="<?php echo $nominal ?>"></td>
                    </tr>
                <?php } ?>
                <tr class="cash-popup">
	                <td colspan="2">
                <a href="#" class="button alignright"><?php _e('Enter Cash Values', 'wc_point_of_sale') ?></a></td>
                </tr>
        <?php } ?>
                <tr>
                    <td><?php _e('Difference:', 'wc_point_of_sale'); ?></td>
                    <td class="cash-difference"><?php echo ($data['detail']['actual_cash']) ? wc_price($data['detail']['actual_cash'] - $total_cash) : '' ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        
    <?php } ?>
</div>