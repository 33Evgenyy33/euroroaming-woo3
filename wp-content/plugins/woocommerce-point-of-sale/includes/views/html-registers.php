<?php
/**
 * HTML for a view registers page in admin.
 *
 * @author   Actuality Extensions
 * @package  WoocommercePointOfSale/views
 * @since    0.1
 */

$admin_url = get_admin_url( get_current_blog_id(), '/' );
if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
	$ref = $_SERVER['HTTP_REFERER'];
	if ( ! empty( $_SERVER['HTTPS'] ) && ! empty( $ref ) && strpos( $ref, 'https://' ) === false ) {
		$admin_url = str_replace( 'https://', 'http://', $admin_url );
	}
}

?>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
<div class="wrap" id="wc-pos-registers-edit">
    <h2>
        <div id="bar_register_info" style="float: left;padding-left: 20px;">
			<?php echo $data['name']; ?>
			<?php
			if ( get_option( 'wc_pos_disable_connection_status', 'no' ) != 'yes' ) {
				?>
                <div class="offline-ui-up" id="offline_indication">
                    <div class="offline-ui-content"></div>
                    <a class="offline-ui-retry" href=""></a>
                </div>
				<?php
			}
			?>
            <a class="button tips" href="#" id="retrieve_sales"
               data-tip="<?php _e( 'Load Order', 'wc_point_of_sale' ); ?>"><?php _e( 'Load', 'wc_point_of_sale' ); ?></a>
            <button class="button" id="start_tour" style="background: #4CAF50;color: #fff;">Ознакомиться с кабинетом
            </button>
			<?php if ( current_user_can( 'edit_private_shop_orders' ) ) { ?>
                <a class="button tips" href="<?php echo $admin_url; ?>edit.php?post_type=shop_order"
                   id="orders_page"
                   data-tip="<?php _e( 'Orders', 'wc_point_of_sale' ); ?>"><?php _e( 'Orders', 'wc_point_of_sale' ); ?></a>
			<?php } ?>
			<?php if ( current_user_can( 'manage_wc_point_of_sale' ) ) { ?>
                <a class="button tips" href="<?php echo $admin_url; ?>admin.php?page=wc_pos_settings"
                   id="settings_page"
                   data-tip="<?php _e( 'Settings', 'wc_point_of_sale' ); ?>"><?php _e( 'Settings', 'wc_point_of_sale' ); ?></a>
			<?php } ?>
			<?php if ( $data['float_cash_management'] ) { ?>
                <a class="button tips"
                   href="<?php echo $admin_url; ?>admin.php?page=wc_pos_cash_management&register=<?php echo $data['ID'] ?>"
                   id="cash_management"
                   data-tip="<?php _e( 'Cash Management', 'wc_point_of_sale' ); ?>"><?php _e( 'Cash Management', 'wc_point_of_sale' ); ?></a>
			<?php } ?>

        </div>
        <div style="float: right;padding-right: 20px;">
            <a class="button button-primary tips" style="float:right;"
               href="<?php echo $admin_url; ?>admin.php?page=wc_pos_registers&amp;close=<?php echo $data['ID']; ?>"
               id="close_register" data-tip="<?php _e( 'Close Register', 'wc_point_of_sale' ); ?>">
            </a>
			<?php if ( get_option( 'wc_pos_lock_screen' ) == 'yes' ) { ?>
                <a class="button tips" style="float:right;" href="#" id="lock_register"
                   data-tip="<?php _e( 'Lock Register', 'wc_point_of_sale' ); ?>">
                </a>
			<?php } ?>
			<?php $current_user = wp_get_current_user(); ?>
			<?php if ( get_option( 'wc_pos_bill_screen', 'no' ) == 'yes' ) { ?>
                <a class="button tips" href="<?php echo site_url() . '/bill-screen/' . $data['ID']; ?>"
                   id="bill_full_screen"
                   data-tip="<?php _e( 'Bill Full screen', 'wc_point_of_sale' ); ?>" target="_blank"></a>
			<?php } ?>
            <a class="button tips" href="#" id="full_screen"
               data-tip="<?php _e( 'Во весь экран', 'wc_point_of_sale' ); ?>"></a>
        </div>
    </h2>
	<?php
	$revert_columns = get_option( 'woocommerce_pos_register_revert_columns' );
	?>
    <div id="edit_wc_pos_registers">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-<?php echo ( $revert_columns == 'no' || ! $revert_columns ) ? '2' : '1' ?>"
                     class="postbox-container">

                    <input type="hidden" id="customer_details_id" value="{{id}}">
                    <div id="pos_billing_details" class="pos-customer-details-tab">
                        <h3>Данные клиента</h3>
                        <div class="woocommerce-billing-fields">
			                <?php


			                $checkout = WC()->checkout();

			                /**********************************************/
			                $billing_fields  = $checkout->get_checkout_fields( 'billing' );
			                $billing_fields1 = $checkout->get_checkout_fields();

			                //echo '<pre>' . print_r( $billing_fields1, true ) . '</pre>';
			                //echo '<pre>' . print_r( $data, true ) . '</pre>';
			                /**********************************************/

			                // Отображение полей добавления клиента

			                $field_client_first_name          = $billing_fields['billing_first_name'];
			                $field_client_first_name['label'] = 'Имя клиента';

			                $field_client_last_name          = $billing_fields['billing_last_name'];
			                $field_client_last_name['label'] = 'Фамилия клиента';

			                $field_client_client_email = $billing_fields['client_email'];

			                $field_client_client_phone = $billing_fields['client_phone'];

			                $field_client_ta_email            = $billing_fields['billing_email'];
			                $field_client_ta_email['label']   = 'Почта ТА';
			                $field_client_ta_email['default'] = '33Evgenyy33@gmail.com';

			                $field_client_ta_phone          = $billing_fields['billing_phone'];
			                $field_client_ta_phone['label'] = 'Телефон ТА';

			                //НУЖНО!!!
			                //							$data1 = WC_POS()->register()->get_data();
			                //							$ta_id = 0;
			                //							foreach ( $data1 as $datum ) {
			                //								if ( pos_check_user_can_open_register( $datum['ID'] ) ) { // Проверка. Привязан ли id кабинета(outlet) к user id
			                //									$regis = wc_pos_get_register( $datum['ID'] );
			                //									$ta_id = json_decode( $regis->detail )->ta_id;
			                //									break;
			                //								}
			                //							}

			                $ta_id = $data['detail']['ta_id'];


			                /* Запрос к селлеру */
			                $url = "http://seller.sgsim.ru/euroroaming_order_submit?operation=get_simcards_new&ta=$ta_id";
			                $ch  = curl_init();
			                curl_setopt( $ch, CURLOPT_HEADER, 0 );
			                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
			                curl_setopt( $ch, CURLOPT_URL, $url );
			                $data_curl = curl_exec( $ch );
			                curl_close( $ch );
			                //echo "Ответ на Ваш запрос: ".$data_curl;

			                $links = json_decode( $data_curl, true );
			                unset( $links['globalsim'] );
			                unset( $links['europasim'] );
			                $simcard_options = array_reduce( $links, 'array_merge', array() );
			                //print_r($simcard_options);
			                /*********************/

			                $field_number_simcard            = $billing_fields['number_simcard'];
			                $field_number_simcard['options'] = $simcard_options;

			                $field_date_activ             = $billing_fields['activation_date'];
			                $field_date_activ['label']    = 'Дата активации (обязательно)';
			                $field_date_activ['required'] = true;

			                woocommerce_form_field( 'billing_first_name', $field_client_first_name );
			                woocommerce_form_field( 'billing_last_name', $field_client_last_name );
			                woocommerce_form_field( 'billing_client_email', $field_client_client_email );
			                woocommerce_form_field( 'billing_client_phone', $field_client_client_phone );
			                woocommerce_form_field( 'billing_email', $field_client_ta_email );
			                woocommerce_form_field( 'billing_phone', $field_client_ta_phone );
			                woocommerce_form_field( 'billing_activation_date', $field_date_activ );
			                woocommerce_form_field( 'billing_number_simcard', $field_number_simcard );

			                $current_user = wp_get_current_user();
			                $filling_fields_script = '<script>jQuery(document).ready(function($) {$("#billing_email").val("' . $current_user->user_email . '");});</script>';
			                echo $filling_fields_script;

			                ?>
                        </div>

	                    <?php

	                    $url         = admin_url( 'admin-ajax.php' );
	                    $nonce_files = wp_nonce_field( 'protect_content', 'my_nonce_field' );

	                    ?>
                        <div id="register_customer_dates">
                            <div id="dropzone-wordpress">
                                <form action='<?php echo $url ?>' enctype="multipart/form-data"
                                      class="dropzone needsclick dz-clickable"
                                      id="dropzone-wordpress-form">
				                    <?php echo $nonce_files ?>
                                    <div class="dz-message needsclick">
                                        Перетащите сюда скан <strong>загранпаспорта</strong><br> или нажмите чтобы загрузить.
                                    </div>
                                    <input type='hidden' name='action' value='submit_dropzonejs'>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
                <div id="postbox-container-<?php echo ( $revert_columns == 'no' || ! $revert_columns ) ? '1' : '2' ?>"
                     class="postbox-container">
                    <div id="wc-pos-register-grids" class="postbox ">
                        <div class="tbc">
							<?php
							$pos_layout = get_option( 'woocommerce_pos_second_column_layout', 'product_grids' );

							if ( $pos_layout == 'product_grids' ) :
								$grid_id = $data['grid_template'];
								if ( $grid_id == 'all' ) {
									?>
                                    <h3 class="hndle">
                                        <span id="wc-pos-register-grids-title">Сим-карты</span>
                                        <i class="close_product_grids"></i>
                                    </h3>
									<?php
								} else if ( $grid_id == 'categories' ) {
									?>
                                    <h3 class="hndle">
                                        <span id="wc-pos-register-grids-title" class="cat_title"
                                              data-parent="0">Сим-карты</span>
                                        <i class="close_product_grids"></i>
                                    </h3>
									<?php
								} else {
									$grids_single_record = wc_point_of_sale_tile_record( $grid_id );
									$grids_all_record    = wc_point_of_sale_get_all_grids( $grid_id );
									?>
                                    <h3 class="hndle">
                                        <span id="wc-pos-register-grids-title">Сим-карты</span>
                                        <i class="close_product_grids"></i>
                                    </h3>
									<?php
								} ?>
                                <div class="inside" id="grid_layout_cycle"></div>
                                <div class="previous-next-toggles">
                                    <span class="previous-grid-layout tips"
                                          data-tip="<?php _e( 'Previous', 'wc_point_of_sale' ); ?>"></span>
                                    <div id="nav_layout_cycle_wrap">
                                        <div id="nav_layout_cycle"></div>
                                    </div>
                                    <span class="next-grid-layout tips"
                                          data-tip="<?php _e( 'Next', 'wc_point_of_sale' ); ?>"></span>
                                </div>
							<?php
							else: ?>
                                <div class="inside" id="grid_layout_cycle">
									<?php if ( $pos_layout == 'company_image' ) {
										$woocommerce_pos_company_logo = get_option( 'woocommerce_pos_company_logo', '' );
										$src                          = '';
										if ( ! empty( $woocommerce_pos_company_logo ) ) {
											$src = wp_get_attachment_image_src( $woocommerce_pos_company_logo, 'full' );
											$src = $src[0];
										}
										?>
                                        <div class="grid_logo">
                                            <img src="<?php echo $src; ?>" alt="">
                                        </div>
									<?php } elseif ( $pos_layout == 'text' ) { ?>
                                        <div class="grid_text">
											<?php echo get_option( 'woocommerce_pos_register_layout_text', '' ); ?>
                                        </div>
									<?php } elseif ( $pos_layout == 'company_image_text' ) {
										$woocommerce_pos_company_logo = get_option( 'woocommerce_pos_company_logo', '' );
										$src                          = '';
										if ( ! empty( $woocommerce_pos_company_logo ) ) {
											$src = wp_get_attachment_image_src( $woocommerce_pos_company_logo, 'full' );
											$src = $src[0];
										}
										?>
                                        <div class="grid_logo" style="height: 33%; ">
                                            <img src="<?php echo $src; ?>" alt="">
                                        </div>
                                        <div class="grid_text" style="height: 67%; ">
											<?php echo get_option( 'woocommerce_pos_register_layout_text', '' ); ?>
                                        </div>
									<?php } ?>
                                </div>
							<?php
							endif; ?>
                        </div>
                    </div>
                    <div id="wc-pos-register-data" class="postbox ">
                        <div class="tbc">
                            <div class="tb">
                                <div class="hndle tbr" style="display: none;">
                                    <div class="add_items">
                                        <input id="add_product_id"
                                               class="ajax_chosen_select_products_and_variations"
                                               data-placeholder="<?php _e( 'Search Products', 'wc_point_of_sale' ); ?>"/>

                                        <a class="tips" id="add_product_to_register"
                                           data-modal="modal-add_custom_product"
                                           data-tip="<?php _e( 'Add Custom Product', 'wc_point_of_sale' ); ?>"><span></span></a>
										<?php if ( get_option( 'woocommerce_calc_shipping' ) == 'yes' ) { ?>
                                            <a class="tips" id="add_shipping_to_register"
                                               data-modal="modal-add_custom_shipping"
                                               data-tip="<?php _e( 'Add Shipping', 'wc_point_of_sale' ); ?>">
                                                <span></span>
                                            </a>
										<?php } ?>
                                    </div>
                                    <span class="clearfix"></span>
                                </div>
                                <div class="inside tbr">
                                    <div class="tb" id="bill_screen">
                                        <div class="woocommerce_order_items_wrapper tbr">
                                            <div class="tbc" id="woocommerce_order_items-container">
                                                <table class="woocommerce_order_items" cellspacing="0"
                                                       cellpadding="0">
                                                    <thead>
                                                    <tr>
                                                        <th class="quantity"><?php _e( 'Qty', 'wc_point_of_sale' ); ?></th>
                                                        <th colspan="3"
                                                            class="item"><?php _e( 'Сим-карта', 'wc_point_of_sale' ); ?></th>
                                                        <th class="line_cost"><?php _e( 'Цена для клиента', 'wc_point_of_sale' ); ?></th>
														<?php do_action( 'wc_pos_tmpl_cart_product_item_thead' ); ?>
                                                        <th class="line_cost_total"><?php _e( 'Total', 'wc_point_of_sale' ); ?></th>
                                                        <th class="line_remove">&nbsp;</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="order_items_list">
													<?php
													$order = new WC_Order( $data['order_id'] );
													?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="wc_pos_register_subtotals tbr">
                                            <table class="woocommerce_order_items" cellspacing="0" cellpadding="0">
                                                <tr id="tr_order_subtotal_label">
                                                    <th class="subtotal_label"><?php _e( 'Subtotal', 'wc_point_of_sale' ); ?></th>
                                                    <td class="subtotal_amount">
                                                        <span id="subtotal_amount"><?php echo wc_price( 0 ); ?></span>
                                                    </td>
                                                </tr>
												<?php /********************************/ ?>
												<?php
												if ( isset( $detail_data['default_shipping_method'] ) && $detail_data['default_shipping_method'] != '' ){
												?>
                                            <tr class="shipping_methods_register" style="display: table-row;">
											<?php
											}else{
											?>
                                                <tr class="shipping_methods_register">
													<?php } ?>
                                                    <th>
														<?php
														if ( isset( $detail_data['default_shipping_method'] ) && $detail_data['default_shipping_method'] != '' ) {
															_e( 'Shipping and Handling', 'woocommerce' );
														}
														?>
                                                    </th>
                                                    <td>
														<?php
														if ( isset( $detail_data['default_shipping_method'] ) && $detail_data['default_shipping_method'] != '' ) {
															$chosen_method    = $detail_data['default_shipping_method'];
															$shipping_methods = WC()->shipping->load_shipping_methods();
															?>
                                                            <select name="shipping_method[0]" data-index="0"
                                                                    id="shipping_method_0" class="shipping_method">
                                                                <option value="no_shipping" <?php selected( 'no_shipping', $chosen_method ); ?>
                                                                        data-cost="0"><?php _e( 'No Shipping', 'wc_point_of_sale' ); ?></option>
																<?php
																foreach ( $shipping_methods as $key => $method ) {
																	?>
                                                                    <option value="<?php echo esc_attr( $method->id ); ?>" <?php selected( $method->id, $chosen_method ); ?>
                                                                            data-cost="<?php echo isset( $method->cost ) ? $method->cost : 0; ?>"><?php echo $method->get_title(); ?><?php echo isset( $method->cost ) ? wc_price( $method->cost ) : ''; ?></option>
																	<?php
																}
																?>
                                                            </select>
															<?php
														}
														?>
                                                    </td>
                                                </tr>
												<?php
												/********************************/
												if ( wc_pos_tax_enabled() ) {
													?>
                                                    <tr class="tax_row">
                                                        <td colspan="2" class="tax_col">
                                                            <table></table>
                                                        </td>
                                                        <!-- <th class="tax_label"><?php _e( 'Tax', 'wc_point_of_sale' ); ?></th>
                                                    <td class="tax_amount"><strong id="tax_amount"></strong></td> -->
                                                    </tr>
													<?php
												}

												if ( $d = $order->get_total_discount() ) { ?>
                                                    <tr id="tr_order_discount">
                                                        <th class="total_label"><?php _e( 'Order Discount', 'wc_point_of_sale' ); ?>
                                                            <span id="span_clear_order_discount"></span>
                                                        </th>
                                                        <td class="total_amount">
                                                            <input type="hidden" value="<?php echo $d; ?>"
                                                                   id="order_discount" name="order_discount">
                                                            <span id="formatted_order_discount"><?php echo wc_price( $d, array( 'currency' => $order->get_order_currency() ) ); ?></span>

                                                        </td>
                                                    </tr>
												<?php } ?>
                                                <tr id="tr_order_total_label">
                                                    <th class="total_label"><?php _e( 'Total', 'wc_point_of_sale' ); ?></th>
                                                    <td class="total_amount"><span
                                                                id="total_amount"><?php echo wc_price( 0 ); ?></span>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="wc-pos-register-buttons" class="postbox ">
                        <div class="tbc">
                            <div class="tb">
                                <div class="tbr">
                                    <div class="button tips wc_pos_show_tiles tbc" type="button"
                                         data-tip="<?php _e( 'Show Tiles', 'wc_point_of_sale' ); ?>">
                                    </div>
                                    <div class="button tips wc_pos_register_void tbc" type="button"
                                         data-tip="<?php _e( 'Void Order', 'wc_point_of_sale' ); ?>">
                                        аннулировать
                                    </div>
                                    <div class="button tips wc_pos_register_save" type="submit"
                                         data-tip="<?php _e( 'Save Order', 'wc_point_of_sale' ); ?>">
                                        отложить
                                    </div>
                                    <div class="button tips wc_pos_register_notes tbc" type="button"
                                         data-tip="<?php _e( 'Add A Note', 'wc_point_of_sale' ); ?>">
                                        заметка
                                    </div>
                                    <div class="button tips wc_pos_register_pay tbc" type="button"
                                         data-tip="<?php _e( 'Accept Payment', 'wc_point_of_sale' ); ?>">
                                        оплата
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

</div>
<?php
#var_dump($data);
?>
<script>
    var change_user = <?php echo json_encode( isChangeUserAfterSale( $data['ID'] ) ); ?>;
    var note_request = <?php echo json_encode( isNoteRequest( $data['ID'] ) ); ?>;
    var print_receipt = <?php echo json_encode( isPrintReceipt( $data['ID'] ) ); ?>;
    var email_receipt = <?php echo absint( $data['settings']['email_receipt'] ); ?>;

</script>