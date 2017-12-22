<?php

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

//=======================================================================================================
// Отключаем принудительную проверку новых версий WP, плагинов и темы в админке,
// чтобы она не тормозила, когда долго не заходил и зашел...
// Все проверки будут происходить незаметно через крон или при заходе на страницу: "Консоль > Обновления".
//=======================================================================================================
if ( is_admin() ) {
	// отключим проверку обновлений при любом заходе в админку...
	remove_action( 'admin_init', '_maybe_update_core' );
	remove_action( 'admin_init', '_maybe_update_plugins' );
	remove_action( 'admin_init', '_maybe_update_themes' );

	// отключим проверку обновлений при заходе на специальную страницу в админке...
	remove_action( 'load-plugins.php', 'wp_update_plugins' );
	remove_action( 'load-themes.php', 'wp_update_themes' );

	// оставим принудительную проверку при заходе на страницу обновлений...
	//remove_action( 'load-update-core.php', 'wp_update_plugins' );
	//remove_action( 'load-update-core.php', 'wp_update_themes' );

	// внутренняя страница админки "Update/Install Plugin" или "Update/Install Theme" - оставим не мешает...
	//remove_action( 'load-update.php', 'wp_update_plugins' );
	//remove_action( 'load-update.php', 'wp_update_themes' );

	// событие крона не трогаем, через него будет проверяться наличие обновлений - тут все отлично!
	//remove_action( 'wp_version_check', 'wp_version_check' );
	//remove_action( 'wp_update_plugins', 'wp_update_plugins' );
	//remove_action( 'wp_update_themes', 'wp_update_themes' );

	/**
	 * отключим проверку необходимости обновить браузер в консоли - мы всегда юзаем топовые браузеры!
	 * эта проверка происходит раз в неделю...
	 * @see https://wp-kama.ru/function/wp_check_browser_version
	 */
	add_filter( 'pre_site_transient_browser_' . md5( $_SERVER['HTTP_USER_AGENT'] ), '__return_true' );
}

//=======================================================================================================
// Загрузка скрипта плавного скролла
//=======================================================================================================
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
function my_scripts_method() {
	wp_enqueue_script( 'smoothscroll', get_stylesheet_directory_uri() . '/js/smoothscroll.js', array( 'jquery' ), '1.0', true );
}

//=======================================================================================================
// Показывать медиафайлы только админам
//=======================================================================================================
/************************************************************/
add_filter( 'pre_get_posts', 'hide_posts_media_by_other' );
function hide_posts_media_by_other( $query ) {
	global $pagenow;
	if ( ( 'edit.php' != $pagenow && 'upload.php' != $pagenow ) || ! $query->is_admin ) {
		return $query;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		global $user_ID;
		$query->set( 'author', $user_ID );
	}

	return $query;
}

add_filter( 'posts_where', 'hide_attachments_wpquery_where' );
function hide_attachments_wpquery_where( $where ) {
	global $current_user;
	if ( ! current_user_can( 'manage_options' ) ) {
		if ( is_user_logged_in() ) {
			if ( isset( $_POST['action'] ) ) {
				// library query
				if ( $_POST['action'] == 'query-attachments' ) {
					$where .= ' AND post_author=' . $current_user->data->ID;
				}
			}
		}
	}

	return $where;
}

//=======================================================================================================
// Загрузка скрипта VKontakte в шапку сайта
//=======================================================================================================
add_action( 'wp_head', 'my_custom_js' );
function my_custom_js() {
	echo '<script type="text/javascript">(window.Image ? (new Image()) : document.createElement(\'img\')).src = location.protocol + \'//vk.com/rtrg?r=HdsKraxE8HWoxhJ9OdOoqg5IsYvCGO0MUyAtZPZSWnZjFqyBwsZwGSimf9a01GccFD*fVs8cOL/y33Qs1uNfJPOURuay/bk2uZXD*BcncsKrfhtGgL5i4hvdMr8*07HDKD*1BUTHOS*rTVsYrS8oATYONJXDHctGbr8JeWB0ffw-&pixel_id=1000021590\';</script><!-- Facebook Pixel Code --><script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,\'script\',\'https://connect.facebook.net/en_US/fbevents.js\');fbq(\'init\', \'392030000921269\');fbq(\'track\', "PageView");</script><noscript><img height="1" width="1" style="display:none"src="https://www.facebook.com/tr?id=392030000921269&ev=PageView&noscript=1"/></noscript><!-- End Facebook Pixel Code -->';
}

//=======================================================================================================
// Загрузка скрипта Scroll To ID
//=======================================================================================================
add_action( 'wp_enqueue_scripts', 'my_scrolltoid_scripts' );
function my_scrolltoid_scripts() {
	if ( is_single() ) {
		wp_enqueue_script( 'scrolltoid', get_stylesheet_directory_uri() . '/js/scrolltoid.js' );
	}
}

//=======================================================================================================
// Загрузка скрипта для страницы CHECKOUT
//=======================================================================================================
add_action( 'wp_enqueue_scripts', 'my_checkout_scripts' );
function my_checkout_scripts() {
	if ( is_page( 'checkout' ) ) {
		wp_enqueue_script( 'mycheckout', get_stylesheet_directory_uri() . '/js/mycheckout.js', '', '', true );
	}
}

//=======================================================================================================
// Стоимость пластика на миниатюре товара на странице shop
//=======================================================================================================
//add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_price', 10 );
//function woocommerce_template_loop_product_price() {
//	global $post;
//	$price  = 0;
//	$cource = 74;
//
//	switch ( $post->ID ) {
//		case 18402:
//			$price = 20 * $cource;
//			break;
//		case 18455:
//			$price = 750;
//			break;
//		case 28328:
//			$price = 750;
//			break;
//		case 18446:
//			$price = 15 * $cource;
//			break;
//		case 41120:
//			$price = 1000;
//			break;
//		case 18453:
//			$price = 1140;
//			break;
//		case 18438:
//			$price = 35 * $cource;
//			break;
//		case 48067:
//			$price = 750;
//			break;
//		case 55050:
//			$price = 0;
//			break;
//		case 18443:
//			return;
//	}
//	//echo '<h3 id="shop-plastic-price">'.$post->ID.'</h3>';
//	echo '<style>#shop-plastic-price{padding:0;color:#6d7404;font-weight:400;font-size:20px}</style><h3 id="shop-plastic-price">' . $price . '₽</h3>';
//}

//=======================================================================================================
// Разрешение загрузки разных тапов файлов
//=======================================================================================================
//function bodhi_svgs_disable_real_mime_check( $data, $file, $filename, $mimes ) {
//    $wp_filetype = wp_check_filetype( $filename, $mimes );
//
//    $ext = $wp_filetype['ext'];
//    $type = $wp_filetype['type'];
//    $proper_filename = $data['proper_filename'];
//
//    return compact( 'ext', 'type', 'proper_filename' );
//}
//add_filter( 'wp_check_filetype_and_ext', 'bodhi_svgs_disable_real_mime_check', 10, 4 );

add_filter( 'upload_mimes', 'cc_mime_types' );
function cc_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}

//=======================================================================================================
// Подключение файла с шоткодами
//=======================================================================================================
get_template_part( 'shortcodes' );

//=======================================================================================================
// Возможность изменить стоимость заказа в админке после оплаты
//=======================================================================================================
add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
	if ( $order->get_status() == 'processing' ) {
		$is_editable = true;
	}

	return $is_editable;
}

//=======================================================================================================
// Убираем определенные пункты меню из админки, если не администратор
//=======================================================================================================
add_action( 'admin_menu', 'my_remove_menu_pages', 999 );
function my_remove_menu_pages() {
	global $submenu;

	if ( ! current_user_can( 'add_users' ) ) {
		remove_menu_page( 'users.php' );
		remove_menu_page( 'vc-welcome' );
		unset( $submenu['wc_point_of_sale'][2] );
	}

}

//=======================================================================================================
// Шоткод таблица стран
//=======================================================================================================
add_shortcode( 'country_table', 'country_table_func' );
function country_table_func() {
	$output = '<script>jQuery(document).ready(function(n){n(document).ready(function(){"use strict";n(".menu > ul > li:has( > ul)").addClass("menu-dropdown-icon"),n(".menu > ul > li > ul:not(:has(ul))").addClass("normal-sub"),n(".menu > ul > li").hover(function(u){n(window).width()>943&&(n(this).children("ul").stop(!0,!1).fadeToggle(150),u.preventDefault())}),n(".menu > ul > li").click(function(){n(window).width()<=943&&n(this).children("ul").fadeToggle(150)})})});</script><div class="menu-container"> <div class="menu"> <ul> <li><a class="btn-pricing-tabl"><i class="material-icons">public</i> <span class="country_table_rep_text">Сравнение тарифов по странам</span></a> <ul> <li><span>А-Д</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-avstrii/" target="_blank">Австрия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-albanii/" target="_blank">Албания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-andorre/" target="_blank">Андорра</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-belgii/" target="_blank">Бельгия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-bolgarii/" target="_blank">Болгария</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-bosnii-i-gertsegovine/" target="_blank">Босния и Герцеговина</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-velikobritanii/" target="_blank">Великобритания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-vengrii/" target="_blank">Венгрия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gvadelupe/" target="_blank">Гваделупе</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-germanii/" target="_blank">Германия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gernsi/" target="_blank">Гернси</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-gibraltare/" target="_blank">Гибралтар</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gonkonge/" target="_blank">Гонконг</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-grenlandii/" target="_blank">Гренландия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gretsii/" target="_blank">Греция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-danii/" target="_blank">Дания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-dzhersi/" target="_blank">Джерси</a></li> </ul> </li> <li><span>И-М</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-izraile/" target="_blank">Израиль</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-indii/" target="_blank">Индия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-irlandii/" target="_blank">Ирландия</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-islandii/" target="_blank">Исландия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-ispanii/" target="_blank">Испания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-italii/" target="_blank">Италия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-kanarskih-ostrovah/" target="_blank">Канарские острова</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-kipre/" target="_blank">Кипр</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-kitae/" target="_blank">Китай</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-latvii/" target="_blank">Латвия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-litve/" target="_blank">Литва</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-lihtenshtejne/" target="_blank">Лихтенштейн</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-lyuksemburge/" target="_blank">Люксембург</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-makedonii/" target="_blank">Македония</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-malajzii/" target="_blank">Малайзия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-malte/" target="_blank">Мальта</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-marokko/" target="_blank">Марокко</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-martinike/" target="_blank">Мартиника</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-monako/" target="_blank">Монако</a></li> </ul> </li> <li><span>Н-Т</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-niderlandah/" target="_blank">Нидерланды</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-norvegii/" target="_blank">Норвегия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-ostrove-men/" target="_blank">Остров Мэн</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-oae" target="_blank">ОАЭ</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-polshe/" target="_blank">Польша</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-portugalii/" target="_blank">Португалия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-pribaltike/" target="_blank" rel="nofollow">Прибалтика</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-reyunone/" target="_blank">Реюньон</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-rumynii/" target="_blank">Румыния</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-san-marino/" target="_blank">Сан-Марино</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-sen-bartelemi/" target="_blank">Сен-Бартелеми</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-sen-martene/" target="_blank">Сен-Мартен</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-serbii/" target="_blank">Сербия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-singapure/" target="_blank">Сингапур</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-skandinaviya/" target="_blank">Скандинавия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-slovakii/" target="_blank">Словакия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-slovenii/" target="_blank">Словения</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-ssha/" target="_blank">США</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-tailande/" target="_blank">Таиланд</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-turtsii/" target="_blank">Турция</a></li></ul> </li> <li><span>Ф-Э</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-farerskih-ostrovah/" target="_blank">Фарерские острова</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-finlyandii/" target="_blank">Финляндия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-vo-frantsii/" target="_blank">Франция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-vo-frantsuzskoj-gviane/" target="_blank">Французская Гвиана</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-horvatii/" target="_blank">Хорватия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-chehii/" target="_blank">Чехия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-chili/" target="_blank">Чили</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-shvejtsarii/" target="_blank">Швейцария</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-shvetsii/" target="_blank">Швеция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-estonii/" target="_blank">Эстония</a></li> </ul> </li> </ul> </li> </ul> </div> </div>';

	return $output;
}

//=======================================================================================================
// Шоткод таблица стран для главной
//=======================================================================================================
add_shortcode( 'country_table_main', 'country_table_main_func' );
function country_table_main_func() {
	$output = '<ul class=g-cols><li class="vc_col-md-3 vc_col-sm-6"><span>А-Д</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-avstrii/ rel=nofollow target=_blank>Австрия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-albanii/ rel=nofollow target=_blank>Албания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-andorre/ rel=nofollow target=_blank>Андорра</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-belgii/ rel=nofollow target=_blank>Бельгия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-bolgarii/ rel=nofollow target=_blank>Болгария</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-bosnii-i-gertsegovine/ rel=nofollow target=_blank>Босния и Герцеговина</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-velikobritanii/ rel=nofollow target=_blank>Великобритания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-vengrii/ rel=nofollow target=_blank>Венгрия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gvadelupe/ rel=nofollow target=_blank>Гваделупе</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-germanii/ rel=nofollow target=_blank>Германия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gernsi/ rel=nofollow target=_blank>Гернси</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-gibraltare/ rel=nofollow target=_blank>Гибралтар</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gonkonge/ rel=nofollow target=_blank>Гонконг</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-grenlandii/ rel=nofollow target=_blank>Гренландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gretsii/ rel=nofollow target=_blank>Греция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-danii/ rel=nofollow target=_blank>Дания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-dzhersi/ rel=nofollow target=_blank>Джерси</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>И-М</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-izraile/ rel=nofollow target=_blank>Израиль</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-indii/ rel=nofollow target=_blank>Индия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-irlandii/ rel=nofollow target=_blank>Ирландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-islandii/ rel=nofollow target=_blank>Исландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-ispanii/ rel=nofollow target=_blank>Испания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-italii/ rel=nofollow target=_blank>Италия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-kanarskih-ostrovah/ rel=nofollow target=_blank>Канарские острова</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-kipre/ rel=nofollow target=_blank>Кипр</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-kitae/ rel=nofollow target=_blank>Китай</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-latvii/ rel=nofollow target=_blank>Латвия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-litve/ rel=nofollow target=_blank>Литва</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-lihtenshtejne/ rel=nofollow target=_blank>Лихтенштейн</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-lyuksemburge/ rel=nofollow target=_blank>Люксембург</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-makedonii/ rel=nofollow target=_blank>Македония</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-malajzii/ rel=nofollow target=_blank>Малайзия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-malte/ rel=nofollow target=_blank>Мальта</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-marokko/ rel=nofollow target=_blank>Марокко</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-martinike/ rel=nofollow target=_blank>Мартиника</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-monako/ rel=nofollow target=_blank>Монако</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>Н-Т</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-niderlandah/ rel=nofollow target=_blank>Нидерланды</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-norvegii/ rel=nofollow target=_blank>Норвегия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-ostrove-men/ rel=nofollow target=_blank>Остров Мэн</a><li><a href="https://euroroaming.ru/mobilnyj-internet-v-oae" rel=nofollow target=_blank>ОАЭ</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-polshe/ rel=nofollow target=_blank>Польша</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-portugalii/ rel=nofollow target=_blank>Португалия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-pribaltike/ rel=nofollow target=_blank>Прибалтика</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-reyunone/ rel=nofollow target=_blank>Реюньон</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-rumynii/ rel=nofollow target=_blank>Румыния</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-san-marino/ rel=nofollow target=_blank>Сан-Марино</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-sen-bartelemi/ rel=nofollow target=_blank>Сен-Бартелеми</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-sen-martene/ rel=nofollow target=_blank>Сен-Мартен</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-serbii/ rel=nofollow target=_blank>Сербия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-singapure/ rel=nofollow target=_blank>Сингапур</a><li><a href="https://euroroaming.ru/mobilnyj-internet-v-skandinaviya/" target="_blank">Скандинавия</a></li><li><a href=https://euroroaming.ru/mobilnyj-internet-v-slovakii/ rel=nofollow target=_blank>Словакия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-slovenii/ rel=nofollow target=_blank>Словения</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-ssha/ rel=nofollow target=_blank>США</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-tailande/ rel=nofollow target=_blank>Таиланд</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-turtsii/ rel=nofollow target=_blank>Турция</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>Ф-Э</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-na-farerskih-ostrovah/ rel=nofollow target=_blank>Фарерские острова</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-finlyandii/ rel=nofollow target=_blank>Финляндия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-vo-frantsii/ rel=nofollow target=_blank>Франция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-vo-frantsuzskoj-gviane/ rel=nofollow target=_blank>Французская Гвиана</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-horvatii/ rel=nofollow target=_blank>Хорватия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-chehii/ rel=nofollow target=_blank>Чехия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-chili/ rel=nofollow target=_blank>Чили</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-shvejtsarii/ rel=nofollow target=_blank>Швейцария</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-shvetsii/ rel=nofollow target=_blank>Швеция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-estonii/ rel=nofollow target=_blank>Эстония</a></ul></ul>';

	return $output;
}

//=======================================================================================================
// Настройка почты
//=======================================================================================================
add_action( 'phpmailer_init', 'tweak_mailer_ssl', 999 );
function tweak_mailer_ssl( $phpmailer ) {
	$phpmailer->SMTPOptions = array(
		'ssl' => array(
			'verify_peer'       => false,
			'verify_peer_name'  => false,
			'allow_self_signed' => true
		)
	);
}

//=======================================================================================================
// Добавление областе для woocommerce
//=======================================================================================================
add_filter( 'woocommerce_states', 'custom_woocommerce_states' );
function custom_woocommerce_states( $states ) {

	$states['RU'] = array(
		'Респ Адыгея'                              => 'Республика Адыгея',
		'Респ Алтай'                               => 'Республика Алтай',
		'Респ Башкортостан'                        => 'Республика Башкортостан',
		'Респ Бурятия'                             => 'Республика Бурятия',
		'Респ Дагестан'                            => 'Республика Дагестан',
		'Респ Ингушетия'                           => 'Республика Ингушетия',
		'Кабардино-Балкарская Респ'                => 'Кабардино-Балкарская республика',
		'Респ Калмыкия'                            => 'Республика Калмыкия',
		'Карачаево-Черкесская Респ'                => 'Карачаево-Черкесская республика',
		'Респ Карелия'                             => 'Республика Карелия',
		'Респ Коми'                                => 'Республика Коми',
		'Респ Крым'                                => 'Крым',
		'Респ Марий Эл'                            => 'Республика Марий Эл',
		'Респ Мордовия'                            => 'Республика Мордовия',
		'Респ Саха /Якутия/'                       => 'Республика Саха (Якутия)',
		'Респ Северная Осетия - Алания'            => 'Респ Северная Осетия-Алания',
		'Респ Татарстан'                           => 'Республика Татарстан',
		'Респ Тува'                                => 'Республика Тыва',
		'Удмуртская Респ'                          => 'Удмуртская республика',
		'Респ Хакасия'                             => 'Республика Хакасия',
		'Чеченская Респ'                           => 'Чеченская республика',
		'Чувашская Республика - Чувашия'           => 'Чувашская республика',
		'Алтайский край'                           => 'Алтайский край',
		'Забайкальский край'                       => 'Забайкальский край',
		'Камчатский край'                          => 'Камчатский край',
		'Краснодарский край'                       => 'Краснодарский край',
		'Красноярский край'                        => 'Красноярский край',
		'Пермский край'                            => 'Пермский край',
		'Приморский край'                          => 'Приморский край',
		'Ставропольский край'                      => 'Ставропольский край',
		'Хабаровский край'                         => 'Хабаровский край',
		'Амурская обл'                             => 'Амурская область',
		'Архангельская обл'                        => 'Архангельская область',
		'Астраханская обл'                         => 'Астраханская область',
		'Белгородская обл'                         => 'Белгородская область',
		'Брянская обл'                             => 'Брянская область',
		'Владимирская обл'                         => 'Владимирская область',
		'Волгоградская обл'                        => 'Волгоградская область',
		'Вологодская обл'                          => 'Вологодская область',
		'Воронежская обл'                          => 'Воронежская область',
		'Ивановская обл'                           => 'Ивановская область',
		'Иркутская обл'                            => 'Иркутская область',
		'Калининградская обл'                      => 'Калининградская область',
		'Калужская обл'                            => 'Калужская область',
		'Кемеровская обл'                          => 'Кемеровская область',
		'Кировская обл'                            => 'Кировская область',
		'Костромская обл'                          => 'Костромская область',
		'Курганская обл'                           => 'Курганская область',
		'Курская обл'                              => 'Курская область',
		'Ленинградская обл'                        => 'Ленинградская область',
		'Липецкая обл'                             => 'Липецкая область',
		'Магаданская обл'                          => 'Магаданская область',
		'Московская обл'                           => 'Московская область',
		'Мурманская обл'                           => 'Мурманская область',
		'Нижегородская обл'                        => 'Нижегородская область',
		'Новгородская обл'                         => 'Новгородская область',
		'Новосибирская обл'                        => 'Новосибирская область',
		'Омская обл'                               => 'Омская область',
		'Оренбургская обл'                         => 'Оренбургская область',
		'Орловская обл'                            => 'Орловская область',
		'Пензенская обл'                           => 'Пензенская область',
		'Псковская обл'                            => 'Псковская область',
		'Ростовская обл'                           => 'Ростовская область',
		'Рязанская обл'                            => 'Рязанская область',
		'Самарская обл'                            => 'Самарская область',
		'Саратовская обл'                          => 'Саратовская область',
		'Сахалинская обл'                          => 'Сахалинская область',
		'Свердловская обл'                         => 'Свердловская область',
		'Смоленская обл'                           => 'Смоленская область',
		'Тамбовская обл'                           => 'Тамбовская область',
		'Тверская обл'                             => 'Тверская область',
		'Томская обл'                              => 'Томская область',
		'Тульская обл'                             => 'Тульская область',
		'Тюменская обл'                            => 'Тюменская область',
		'Ульяновская обл'                          => 'Ульяновская область',
		'Челябинская обл'                          => 'Челябинская область',
		'Ярославская обл'                          => 'Ярославская область',
		'г Москва'                                 => 'Москва',
		'г Санкт-Петербург'                        => 'Санкт-Петербург',
		'г Севастополь'                            => 'Севастополь',
		'Еврейская Аобл'                           => 'Еврейская автономная область',
		'Ненецкий АО'                              => 'Ненецкий автономный округ',
		'Ханты-Мансийский Автономный округ - Югра' => 'Ханты-Мансийский автономный округ - Югра',
		'Чукотский АО'                             => 'Чукотский автономный округ',
		'Ямало-Ненецкий АО'                        => 'Ямало-Ненецкий автономный округ'
	);

	return $states;
}

//=======================================================================================================
// Woocommerce промокод
//=======================================================================================================
add_action( 'woocommerce_applied_coupon', 'apply_product_on_coupon' );
function apply_product_on_coupon() {
	global $woocommerce;

	$coupons = $woocommerce->cart->get_applied_coupons();

	$coupon_id = $coupons[0];

	$coupon_code_vodafone           = $coupon_id . '-vodafone';
	$coupon_code_orange             = $coupon_id . '-orange';
	$coupon_code_ortel              = $coupon_id . '-ortel';
	$coupon_code_globalsim          = $coupon_id . '-globalsim';
	$coupon_code_globalsim_usa      = $coupon_id . '-globalsim-usa';
	$coupon_code_globalsim_internet = $coupon_id . '-globalsim-internet';
	$coupon_code_europasim          = $coupon_id . '-europasim';
	$coupon_code_travelchat         = $coupon_id . '-travelchat';
	$coupon_code_three              = $coupon_id . '-three';

	$the_coupon_vodafone           = new WC_Coupon( $coupon_code_vodafone );
	$the_coupon_orange             = new WC_Coupon( $coupon_code_orange );
	$the_coupon_ortel              = new WC_Coupon( $coupon_code_ortel );
	$the_coupon_globalsim          = new WC_Coupon( $coupon_code_globalsim );
	$the_coupon_globalsim_usa      = new WC_Coupon( $coupon_code_globalsim_usa );
	$the_coupon_globalsim_internet = new WC_Coupon( $coupon_code_globalsim_internet );
	$the_coupon_europasim          = new WC_Coupon( $coupon_code_europasim );
	$the_coupon_travelchat         = new WC_Coupon( $coupon_code_travelchat );
	$the_coupon_three              = new WC_Coupon( $coupon_code_three );


	if ( in_array( $coupon_id, $woocommerce->cart->applied_coupons ) ) {
		if ( $the_coupon_vodafone->is_valid() && ! in_array( $coupon_code_vodafone, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_vodafone->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_vodafone );
		}
		if ( $the_coupon_travelchat->is_valid() && ! in_array( $the_coupon_travelchat, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_travelchat->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $the_coupon_travelchat );
		}
		if ( $the_coupon_three->is_valid() && ! in_array( $the_coupon_three, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_three->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $the_coupon_three );
		}

		if ( $the_coupon_orange->is_valid() && ! in_array( $coupon_code_orange, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_orange->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_orange );
		}
		if ( $the_coupon_ortel->is_valid() && ! in_array( $coupon_code_ortel, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_ortel->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_ortel );
		}
		if ( $the_coupon_globalsim->is_valid() && ! in_array( $coupon_code_globalsim, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_globalsim->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_globalsim );
		}
		if ( $the_coupon_globalsim_usa->is_valid() && ! in_array( $coupon_code_globalsim_usa, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_globalsim_usa->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_globalsim_usa );
		}
		if ( $the_coupon_globalsim_internet->is_valid() && ! in_array( $coupon_code_globalsim_internet, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_globalsim_internet->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_globalsim_internet );
		}
		if ( $the_coupon_europasim->is_valid() && ! in_array( $coupon_code_europasim, $woocommerce->cart->applied_coupons ) ) {
			$the_coupon_europasim->add_coupon_message( '' );

			$woocommerce->cart->add_discount( $coupon_code_europasim );
		}
	}


//	$coupons = $woocommerce->cart->get_coupons();
//	if (in_array($coupon_id, $woocommerce->cart->applied_coupons) && count($coupons) == 1) {
//		WC()->cart->remove_coupon($coupon_id);
//		echo '<style>.woocommerce-message{display: none;}</style>';
//		wc_add_notice(sprintf(__("Жаль, но этот промокод не может быть использован для товаров, которые находятся у вас в корзине.", "your-theme-language")), 'error');
//	} else {
//		echo '<style>.woocommerce div.woocommerce-message + div.woocommerce-message{display: none;}</style>';
//	}

	$coupons = $woocommerce->cart->get_coupons();

	if ( count( $coupons ) >= 1 ) {
		echo '<style>.woocommerce-info{display:none;}</style>';
	}
}

//=======================================================================================================
// Проверка на совместимость продуктов в корзине.
// Баланс не может быть вместе с сим-картой в одном заказе
//=======================================================================================================
add_filter( 'woocommerce_add_to_cart_validation', 'filter_woocommerce_add_to_cart_validation', 10, 3 );
function filter_woocommerce_add_to_cart_validation( $true, $product_id, $quantity ) {
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();

	$add_product_cat = get_the_terms( $product_id, 'product_cat' );

	foreach ( $items as $item ) {
		$product_in_cart_cat = get_the_terms( $item['product_id'], 'product_cat' );
		if ( $add_product_cat[0]->slug !== $product_in_cart_cat[0]->slug ) {
			wc_add_notice( __( 'Добавляемый продукт не совместим с тем, что Вы уже добавили в корзину. Данные продукты оформляются по отдельности', 'woocommerce' ), 'error' );

			return false;
		}
	}

	//file_put_contents("processing-3.txt", print_r($add_product_cat[0]->slug, true));
	return $true;
}


//=======================================================================================================
// Сортировка товаров в магазине по категории
//=======================================================================================================
add_action( 'pre_get_posts', 'shop_filter_cat' );
function shop_filter_cat( $query ) {
	if ( ! is_admin() && is_post_type_archive( 'product' ) && $query->is_main_query() ) {
		$query->set( 'tax_query', array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => 'sim-karty'
				)
			)
		);
	}
}

add_filter( 'woocommerce_available_payment_gateways', 'filter_gateways' );
function filter_gateways( $gateways ) {
	$payment_NAME = 'cheque';

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	//var_dump($chosen_methods);
	$chosen_shipping = $chosen_methods[0];

	if ( $chosen_shipping == '18616' || $chosen_shipping == '27442' || $chosen_shipping == 'local_pickup_plus' || $chosen_shipping == 'flat_rate:1' || $chosen_shipping == 'flat_rate:2' ) {
		unset( $gateways[ $payment_NAME ] );
	}

	if ( $chosen_shipping == null ) {
		unset( $gateways[ $payment_NAME ] );
	}

	return $gateways;
}

//=======================================================================================================
// Доступные способы доставки в зависимости от товара в корзине
//=======================================================================================================
add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );
function my_hide_shipping_when_free_is_available( $rates ) {
	global $woocommerce;

	$free = array();

	$items = $woocommerce->cart->get_cart();

	foreach ( $items as $item ) {

//		$_product = $values['data']->post;
//		print_r($values['variation_id']);
//		$myfile = fopen("items2.txt", "w") or die("Unable to open file!");
//		file_put_contents("items2.txt", print_r($item, true), FILE_APPEND | LOCK_EX);
//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/sim_cards.txt", print_r($item['variation'], true), FILE_APPEND | LOCK_EX);
//		file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/sim_cards.txt", print_r("\n", true), FILE_APPEND | LOCK_EX);

		if ( $item['product_id'] == 25841 ) {
			foreach ( $rates as $rate_id => $rate ) {
				if ( 'advanced_shipping' == $rate->method_id ) {
					$free[ $rate_id ] = $rate;
				}
			}
			break;
		}

		/************Three*************/
//		if ($_product->ID == 55050) {
//			//print_r($_product);
//			foreach ($rates as $rate_id => $rate) {
//				//echo $rate->method_id.'<br>';
//				if ('advanced_shipping' == $rate->method_id || 'flat_rate' == $rate->method_id) {
//					$free[$rate_id] = $rate;
//				}
//			}
//			break;
//		}

		/************Vodafone только выдача (все форматы)*************/
		if ( $item['product_id'] == 18438 ) {
			//print_r($_product);
			foreach ( $rates as $rate_id => $rate ) {
				if ( 'local_pickup_plus' == $rate->method_id ) {
					$free[ $rate_id ] = $rate;
				}
			}
			break;
		}

		/************EuropaSim только выдача (все форматы)*************/
		if ( $item['product_id'] == 28328 ) {
			//print_r($_product);
			foreach ( $rates as $rate_id => $rate ) {
				if ( 'local_pickup_plus' == $rate->method_id ) {
					$free[ $rate_id ] = $rate;
				}
			}
			break;
		}

		/************GS Internet только выдача (все форматы)*************/
//		if ($item['product_id'] == 18453 && $item['variation']['attribute_format-sim-karty'] === 'Nano-SIM') {
//			//print_r($_product);
//			foreach ($rates as $rate_id => $rate) {
//				if ('local_pickup_plus' == $rate->method_id) {
//					$free[$rate_id] = $rate;
//				}
//			}
//			break;
//		}

		/************Orange только выдача (все форматы)*************/
//		if ($item['product_id'] == 18402) {
//			//print_r($_product);
//			foreach ($rates as $rate_id => $rate) {
//				if ('local_pickup_plus' == $rate->method_id) {
//					$free[$rate_id] = $rate;
//				}
//			}
//			break;
//		}

		/************Orange только выдача (nano)*************/
//		if ($_product->ID == 18402) {
//			//print_r($_product);
//			if ($values['variation_id'] == 24062 || $values['variation_id'] == 24059 || $values['variation_id'] == 24056
//				|| $values['variation_id'] == 31083 || $values['variation_id'] == 30954 || $values['variation_id'] == 30955) {
//				foreach ($rates as $rate_id => $rate) {
//					if ('local_pickup_plus' == $rate->method_id) {
//						$free[$rate_id] = $rate;
//					}
//				}
//				break;
//			}
//		}
	}

	return ! empty( $free ) ? $free : $rates;
}

//=======================================================================================================
// Добавление кастомной валюты для AffiliateWP
//=======================================================================================================
add_filter( 'affwp_currencies', 'affwp_custom_add_currency' );
function affwp_custom_add_currency( $currencies ) {
	$currencies['ye'] = 'YE';

	return $currencies;
}

//=======================================================================================================
// Панель администратора по ролям
//=======================================================================================================
/*add_filter( 'show_admin_bar', 'my_function_admin_bar' );
function my_function_admin_bar() {
	if ( members_current_user_has_role( 'cashier' ) || members_current_user_has_role( 'editor' ) || members_current_user_has_role( 'administrator' ) || members_current_user_has_role( 'shop_manager' ) ) {
		return true;
	} else {
		return false;
	}
}*/

//=======================================================================================================
// Редирект на главную после выхода
//=======================================================================================================
add_action( 'wp_logout', create_function( '', 'wp_redirect(home_url());exit();' ) );

//=======================================================================================================
// Настройка.....
//=======================================================================================================
add_action( 'woocommerce_before_checkout_form', 'action_woocommerce_before_checkout_form', 10, 2 );
function action_woocommerce_before_checkout_form() {
	global $woocommerce, $wp_query;

	$product_in_cart = false;

	foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
		$_product_id = $values['product_id'];
		$terms       = get_the_terms( $_product_id, 'product_cat' );
		//echo '<pre>',print_r($values,1),'</pre>';
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$_categoryid = $term->term_id;
				if ( $_categoryid == 139 ) {
					//category is in cart!
					$product_in_cart = true;
				}
			}
		}
	}

	if ( $product_in_cart == true ) {
		wp_enqueue_script( 'jquery-inputmask', get_stylesheet_directory_uri() . '/js/inputmask/jquery.inputmask.bundle.min.js' );
		echo '<script>jQuery(document).ready(function(i){i("#billing_phone").inputmask({mask:"79999999999"}), i("#date_activ_field label").html("<p style=\'margin-bottom: 0;line-height: 17px\'><span style=\'font-weight: 500;\'>Желаемая дата активации. <abbr class=\'required\' title=\'обязательно\'>*</abbr></span><br><span style=\'font-size: 14px;font-weight: 400;color: #000\'>Активация сим-карты производится в будние дни. В праздничные и выходные дни сим-карты не активируются.</span></p>")});</script>';
		echo '<p style="box-shadow: 0 1px 1px 0 rgba(0,0,0,0.05), 0 1px 3px 0 rgba(0,0,0,0.25);border-width: 1px 1px;background: #ffef74;padding: 20px;border-left: .618em solid rgba(0,0,0,.15);">Для выбора способа доставки или пункта самовывоза заполните все обязательные поля, отмеченные звездочкой <span style="color: #F60000;font-size: 18px;">*</span></p>';
	} else {
		wp_enqueue_script( 'jquery-inputmask', get_stylesheet_directory_uri() . '/js/inputmask/jquery.inputmask.bundle.min.js' );
		echo '<script>jQuery(document).ready(function(i){i("#billing_phone").inputmask({mask:"79999999999"}),i("#orange_replenishment").inputmask({mask:"699999999"}),i("#pin_code_recovery").inputmask({mask:"699999999"}),i("#vodafone_replenishment").inputmask({mask:"3499999999"})});</script>';
	}
}

//=======================================================================================================
// Надпись перед выбором способа оплаты
//=======================================================================================================
add_action( 'woocommerce_review_order_before_payment', 'action_woocommerce_checkout_before_order_review', 10, 0 );
function action_woocommerce_checkout_before_order_review() {
	echo '<h3 style="background: #f8f8f8;padding: 7px 0 7px 0;text-align: center;">Выберите способ оплаты</h3>';
}

//=======================================================================================================
// Убираем поле url
//=======================================================================================================
add_filter( 'comment_form_default_fields', 'crunchify_disable_comment_url' );
function crunchify_disable_comment_url( $fields ) {
	unset( $fields['url'] );

	return $fields;
}


//=======================================================================================================
// Добавление полей в профиль пользователя
//=======================================================================================================
add_action( 'show_user_profile', 'affwp_custom_extra_profile_fields' );
add_action( 'edit_user_profile', 'affwp_custom_extra_profile_fields' );
add_action( "user_new_form", 'affwp_custom_extra_profile_fields' );
function affwp_custom_extra_profile_fields( $user ) {
	if ( is_object( $user ) ) {
		$actual_address   = esc_attr( get_the_author_meta( 'actual_address', $user->ID ) );
		$billing_partner  = esc_attr( get_the_author_meta( 'billing_partner', $user->ID ) );
		$promocod_partner = esc_attr( get_the_author_meta( 'promocod_partner', $user->ID ) );
	} else {
		$actual_address   = null;
		$billing_partner  = null;
		$promocod_partner = null;
	}
	?>

    <h3>Адрес партнера</h3>
    <table class="form-table">
        <tr>
            <th><label for="actual_address">Фактический адрес</label></th>
            <td>
                <input type="text" name="actual_address" id="actual_address"
                       value="<?php echo $actual_address; ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
    </table>
    <h3>Платежная информация партнера</h3>
    <table class="form-table">
        <tr>
            <th><label for="billing_partner">Форма оплаты</label></th>
            <td>
                <input type="text" name="billing_partner" id="billing_partner"
                       value="<?php echo $billing_partner; ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
    </table>
    <h3>Промокод партнера</h3>
    <table class="form-table">
        <tr>
            <th><label for="promocod_partner">Промокод</label></th>
            <td>
                <input type="text" name="promocod_partner" id="promocod_partner"
                       value="<?php echo $promocod_partner; ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
    </table>

<?php }

//=======================================================================================================
// Сохранение значений кастомных полей в профиле пользователя
//=======================================================================================================
add_action( 'user_register', 'affwp_custom_save_extra_profile_fields' );
add_action( 'profile_update', 'affwp_custom_save_extra_profile_fields' );
add_action( 'personal_options_update', 'affwp_custom_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'affwp_custom_save_extra_profile_fields' );
function affwp_custom_save_extra_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_user_meta( $user_id, 'billing_partner', $_POST['billing_partner'] );
	update_user_meta( $user_id, 'actual_address', $_POST['actual_address'] );
	update_user_meta( $user_id, 'promocod_partner', $_POST['promocod_partner'] );

}

add_action( 'woocommerce_checkout_process', 'wdm_validate_custom_field', 10, 1 );
function wdm_validate_custom_field( $args ) {
	global $wpdb;
	//echo 'test11';
	if ( isset( $_POST['orange_replenishment'] ) ) {
		$o_id    = $_POST['orange_replenishment'];
		$track_o = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_orange_numbers WHERE numbers = %d", $o_id ) );

		if ( empty( $track_o->numbers ) ) {
			wc_add_notice( 'Номер <strong>' . $o_id . '</strong> Orange не принадлежит компании Евророуминг или введен некорректно', 'error' );
		}
	}


	if ( isset( $_POST['pin_code_recovery'] ) ) {
		$o_id    = $_POST['pin_code_recovery'];
		$track_o = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_orange_numbers WHERE numbers = %d", $o_id ) );
		if ( empty( $track_o->numbers ) ) {
			wc_add_notice( 'Номер <strong>' . $o_id . '</strong> Orange не принадлежит компании Евророуминг или введен некорректно', 'error' );
		}
	}


	if ( isset( $_POST['vodafone_replenishment'] ) ) {
		$v_id    = $_POST['vodafone_replenishment'];
		$track_v = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_vodafone_numbers WHERE numbers = %s", $v_id ) );
		//file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "/logs/vodafone_numbers.txt", print_r( $v_id, true )."\n", FILE_APPEND | LOCK_EX );
		if ( empty( $track_v->numbers ) ) {
			wc_add_notice( 'Номер <strong>' . $v_id . '</strong> Vodafone не принадлежит компании Евророуминг или введен некорректно', 'error' );
		}
	}
}

//=======================================================================================================
// Название полей формы заказа
//=======================================================================================================
add_filter( 'woocommerce_default_address_fields', 'bbloomer_override_postcode_validation' );
function bbloomer_override_postcode_validation( $address_fields ) {
	$address_fields['postcode']['required'] = false;
	$address_fields['postcode']['label']    = 'Почтовый индекс (для почты РФ)';
	$address_fields['city']['label']        = 'Город (населенный пункт)';

	return $address_fields;
}

//=======================================================================================================
// Сортировка пользователей по дате регистрации
//=======================================================================================================
add_action( 'pre_user_query', 'tgm_order_users_by_date_registered' );
function tgm_order_users_by_date_registered( $query ) {
	global $pagenow;
	if ( ! is_admin() || 'users.php' !== $pagenow ) {
		return;
	}
	$query->query_orderby = 'ORDER BY user_registered DESC';
}

//=======================================================================================================
// Store Locator кастомный шаблон
//=======================================================================================================
add_filter( 'wpsl_templates', 'custom_templates' );
function custom_templates( $templates ) {
	/**
	 * The 'id' is for internal use and must be unique ( since 2.0 ).
	 * The 'name' is used in the template dropdown on the settings page.
	 * The 'path' points to the location of the custom template,
	 * in this case the folder of your active theme.
	 */
	$templates[] = array(
		'id'   => 'custom',
		'name' => 'Custom template',
		'path' => get_stylesheet_directory() . '/' . 'wpsl-templates/custom.php',
	);

	return $templates;
}

//=======================================================================================================
// Store Locator убираем отступы в заголовке, если нет миниатюры
//=======================================================================================================
add_action( 'us_before_template:templates/l-header', 'action_woocommerce_checkout_billing' );
function action_woocommerce_checkout_billing() {
	if ( ! has_post_thumbnail() && is_singular( 'wpsl_stores' ) ) {
		echo "<style>.sidebar_none .l-section.preview_modern .w-blog{padding-top:0;}.w-blog-post-meta{display: none;}.w-blog-post-preview,.l-section.preview_modern .w-blog:after{background: #ffffff!important;}.l-section.preview_modern .w-blog-post-title{color:#3b4664;text-align:center}article.l-section>.l-section-h.i-cf{margin-top:2.7rem;padding-bottom:.7rem;padding-top:0}</style>";
	}
}

//=======================================================================================================
// Загрузка стилей если роль cashier
//=======================================================================================================
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );
function wpdocs_enqueue_custom_admin_style() {
	$role = 'cashier';
	$user = wp_get_current_user();

	if ( in_array( $role, (array) $user->roles ) ) {

		wp_register_style( 'cashier_admin_style', get_stylesheet_directory_uri() . '/css/cashier-admin-style.css', false, '1.0.0' );
		wp_enqueue_style( 'cashier_admin_style' );

	}

}

//=======================================================================================================
// Редирект на страницу кабинета, если роль cashier
//=======================================================================================================
add_action( 'admin_init', 'redirect_so_15396771' );
function redirect_so_15396771() {
	$role = 'cashier';
	$user = wp_get_current_user();

	if ( $_SERVER['REQUEST_URI'] == '/wp-admin/' && in_array( $role, (array) $user->roles ) ) {
		wp_redirect( '/wp-admin/admin.php?page=wc_pos_registers', 301 );
		exit;
	}

	if ( $_SERVER['REQUEST_URI'] == '/wp-admin/index.php' && in_array( $role, (array) $user->roles ) ) {
		wp_redirect( '/wp-admin/admin.php?page=wc_pos_registers', 301 );
		exit;
	}
}

//=======================================================================================================
// Отправка оповещения на почту, если появился новыйй комментарий
//=======================================================================================================
add_filter( 'comment_post', 'comment_notification' );
function comment_notification( $comment_ID, $comment_approved ) {

	// Send email only when it's not approved
	if ( $comment_approved == 0 ) {
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		$comment = get_comment( $comment_ID );

		$subject = "Проверьте комментарий к статье: " . get_the_title( $comment->comment_post_ID );
		$message = 'Ссылка на статью: ' . get_permalink( $comment->comment_post_ID );
		$message .= '<br>';
		$message .= 'Текст комментария:';
		$message .= '<br>';
		$message .= get_comment_text( $comment_ID, array() );

		wp_mail( 'o.koshkina@euroroaming.ru', $subject, $message, $headers );
		wp_mail( 'm.prohorova@euroroaming.ru', $subject, $message, $headers );
		wp_mail( 'e.simakova@euroroaming.ru', $subject, $message, $headers );
	}
}


//=======================================================================================================
// Загрузка кода callback после футора
//=======================================================================================================
//add_action( 'us_after_footer', 'callback_us_after_footer' );
//function callback_us_after_footer() {
//	//echo get_the_ID();
//	wp_enqueue_style( 'my-fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.25/jquery.fancybox.min.css', array(), '3.1.27' );
//	wp_enqueue_script( 'my-fancybox-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.1.25/jquery.fancybox.min.js', array( 'jquery' ), '3.1.27', true );
//	echo '<div style="display: none;" id="callback_request">';
//	gravity_form( 5, true, false, false, '', true, 12 );
//	echo '</div>';
//	echo '<div class="log-in">';
//	echo '  <a data-fancybox="" data-src="#callback_request" href="javascript:;" data-width="700" class="callback-modal" id="popup__toggle">';
//	echo '  <div class="circlephone" style="transform-origin: center;"></div>';
//	echo '  <div class="circle-fill" style="transform-origin: center;"></div>';
//	echo '  <div class="img-circle" style="transform-origin: center;">';
//	echo '    <i style="" class="fa fa-phone img-circleblock"></i>';
//	echo '  </div>';
//	echo '  </a>';
//	echo '</div>';
//}

//=======================================================================================================
// Загрузка кода мессенджеров после футора
//=======================================================================================================
add_action( 'us_after_footer', 'messaandgers_us_after_footer' );
function messaandgers_us_after_footer() {
	wp_enqueue_script( 'fab', get_stylesheet_directory_uri() . '/js/fab.js', array( 'jquery' ), '1.0', true );
	echo '<div class="fab js-fab"> <button class="fab__button fab__button--primary js-toggle"><span class="fab__button--primary__icon"></span> </button> <button id="fab-viber-btn" class="fab__button fab__button--secondary" style="display:none" data-label="Viber"> <svg id="fab-icon-svg" fill="#7d3daf" width="30" height="33" viewBox="-4 0 55 50" xmlns="http://www.w3.org/2000/svg"> <path class="cls-1" d="M48.88 12.43v-.06c-1.21-4.88-6.64-10.11-11.64-11.2h-.06a64.66 64.66 0 0 0-24.34 0h-.06c-5 1.09-10.43 6.32-11.64 11.2v.06a47.66 47.66 0 0 0 0 20.53v.06c1.16 4.67 6.19 9.66 11 11v5.44a2.19 2.19 0 0 0 3.77 1.52l5.52-5.73c1.2.07 2.39.1 3.59.1a64.9 64.9 0 0 0 12.17-1.15h.06c5-1.09 10.43-6.32 11.64-11.2v-.06a47.66 47.66 0 0 0 0-20.53zm-4.37 19.54c-.8 3.18-4.93 7.14-8.22 7.87a60.26 60.26 0 0 1-12.95 1 .3.3 0 0 0-.23.09l-4 4.13-4.28 4.4a.5.5 0 0 1-.86-.35v-9a.31.31 0 0 0-.25-.3c-3.28-.73-7.41-4.69-8.21-7.87a43.23 43.23 0 0 1 0-18.55c.81-3.18 4.93-7.14 8.21-7.87a60.21 60.21 0 0 1 22.61 0c3.28.73 7.41 4.69 8.22 7.87a43.18 43.18 0 0 1 0 18.55zm0 0"></path> <path class="cls-1" d="M32.14 36.13c-.5-.15-1-.26-1.43-.44a32.51 32.51 0 0 1-12.25-8.19 31.82 31.82 0 0 1-4.69-7.17c-.6-1.22-1.11-2.49-1.63-3.76s.22-2.34 1-3.21a7.55 7.55 0 0 1 2.52-1.9 1.62 1.62 0 0 1 2 .48 26.11 26.11 0 0 1 3.13 4.39 2.09 2.09 0 0 1-.58 2.83c-.24.16-.45.35-.67.53a2.23 2.23 0 0 0-.51.54 1.47 1.47 0 0 0-.1 1.29 11.68 11.68 0 0 0 6.56 7.27 3.35 3.35 0 0 0 1.68.41c1-.12 1.36-1.25 2.08-1.84a2 2 0 0 1 2.36-.1c.76.48 1.49 1 2.22 1.52a25.32 25.32 0 0 1 2.09 1.59 1.66 1.66 0 0 1 .5 2.06 7 7 0 0 1-3 3.29 7.21 7.21 0 0 1-1.29.41c-.5-.15.44-.14 0 0zM25.02 9.87A12.29 12.29 0 0 1 37.1 20.04c.18 1 .25 2.06.33 3.1a.73.73 0 0 1-.68.85c-.48 0-.7-.4-.73-.83-.06-.86-.11-1.73-.22-2.58a10.81 10.81 0 0 0-8.71-9c-.68-.12-1.37-.15-2.05-.22-.43 0-1-.07-1.09-.61a.74.74 0 0 1 .73-.83h.35c6.05.17-.12 0 0 0zm0 0"></path> <path class="cls-1" d="M34.21 21.79a2.81 2.81 0 0 1-.06.42.69.69 0 0 1-1.29.07 1.83 1.83 0 0 1-.07-.58 7.33 7.33 0 0 0-.93-3.68 6.84 6.84 0 0 0-2.87-2.69 8.26 8.26 0 0 0-2.29-.7c-.34-.06-.69-.09-1-.14a.66.66 0 0 1-.62-.74.65.65 0 0 1 .72-.64 9.5 9.5 0 0 1 4 1 8 8 0 0 1 4.36 6.23c0 .13 0 .25.06.38 0 .31 0 .63.06 1 0 .08 0-.42 0 0zm0 0"></path> <path class="cls-1" d="M30.45 21.64a.75.75 0 0 1-.83-.74 7.56 7.56 0 0 0-.14-1 2.72 2.72 0 0 0-1-1.58 2.63 2.63 0 0 0-.82-.39c-.37-.11-.76-.08-1.13-.17a.69.69 0 0 1-.56-.8.73.73 0 0 1 .77-.58A4.16 4.16 0 0 1 31 20.52a1.91 1.91 0 0 1 0 .59.6.6 0 0 1-.54.5c-.51 0 .23 0 0 0zm0 0"></path> </svg> </button> <button id="fab-whatsapp-btn" class="fab__button fab__button--secondary" style="display:none" data-label="WhatsApp"> <svg id="fab-icon-svg" fill="#00b45c" width="30" height="33" viewBox="-4 0 55 50" xmlns="http://www.w3.org/2000/svg"> <path class="cls-1" d="M42.63,7.46A24.51,24.51,0,0,0,4.07,37L.59,49.72l13-3.41a24.48,24.48,0,0,0,11.71,3h0A24.51,24.51,0,0,0,42.63,7.46ZM25.3,45.16h0a20.33,20.33,0,0,1-10.37-2.84l-.74-.44-7.71,2,2.06-7.51L8,35.62A20.36,20.36,0,1,1,25.3,45.16ZM36.47,29.91c-.61-.31-3.62-1.79-4.18-2s-1-.31-1.38.31-1.58,2-1.94,2.4-.71.46-1.33.15a16.72,16.72,0,0,1-4.92-3,18.45,18.45,0,0,1-3.41-4.24c-.36-.61,0-.94.27-1.25s.61-.72.92-1.07a4.18,4.18,0,0,0,.61-1,1.13,1.13,0,0,0-.05-1.07c-.15-.31-1.38-3.32-1.89-4.55s-1-1-1.38-1.05-.77,0-1.17,0a2.25,2.25,0,0,0-1.63.77,6.87,6.87,0,0,0-2.14,5.11c0,3,2.19,5.92,2.5,6.33s4.32,6.59,10.46,9.24A35.16,35.16,0,0,0,29.3,36.2a8.39,8.39,0,0,0,3.86.24c1.18-.18,3.62-1.48,4.13-2.91a5.12,5.12,0,0,0,.36-2.91C37.49,30.37,37.08,30.21,36.47,29.91Z" transform="translate(-0.59 -0.28)"></path> </svg> </button> <button id="fab-skype-btn" class="fab__button fab__button--secondary" style="display:none" data-label="Skype"> <svg id="fab-icon-svg" fill="#0bbff2" width="27" height="30" viewBox="-7 0 60 60" xmlns="http://www.w3.org/2000/svg"> <path class="cls-1" d="M48.25,45.56a19.22,19.22,0,0,1-8.61,6.55,34.51,34.51,0,0,1-13.43,2.37q-9.31,0-15.37-3.24a19.89,19.89,0,0,1-7-6.22,13.37,13.37,0,0,1-2.68-7.57A4.84,4.84,0,0,1,2.8,33.81a5.7,5.7,0,0,1,4.1-1.52,5.06,5.06,0,0,1,3.41,1.19A9.14,9.14,0,0,1,12.69,37a22.48,22.48,0,0,0,2.6,4.62,10.34,10.34,0,0,0,3.92,3.06,15.74,15.74,0,0,0,6.66,1.2,16,16,0,0,0,9.21-2.43,7.17,7.17,0,0,0,3.54-6.06,6.12,6.12,0,0,0-1.91-4.68A12.54,12.54,0,0,0,31.77,30q-3-1-8.09-2a63,63,0,0,1-11.35-3.41,18.22,18.22,0,0,1-7.25-5.33,13.06,13.06,0,0,1-2.68-8.39A13.6,13.6,0,0,1,5.22,2.32a17.74,17.74,0,0,1,8.2-5.71A36.11,36.11,0,0,1,26-5.38a34,34,0,0,1,10,1.32,21.79,21.79,0,0,1,7,3.52,14.93,14.93,0,0,1,4.08,4.6,9.92,9.92,0,0,1,1.29,4.69,5.32,5.32,0,0,1-1.6,3.77,5.34,5.34,0,0,1-4,1.68,4.89,4.89,0,0,1-3.33-1A13.5,13.5,0,0,1,37,9.9a15.1,15.1,0,0,0-4.12-5.11C31.3,3.57,28.73,3,25.18,3a14.21,14.21,0,0,0-8,2c-2,1.32-3,2.93-3,4.8a4.64,4.64,0,0,0,1,3,8.35,8.35,0,0,0,2.84,2.17,20.54,20.54,0,0,0,3.64,1.43c1.23.34,3.26.84,6.1,1.5q5.31,1.14,9.63,2.53a30.76,30.76,0,0,1,7.34,3.35,14.25,14.25,0,0,1,4.72,5,14.93,14.93,0,0,1,1.7,7.4,16,16,0,0,1-2.94,9.43Zm0,0" transform="translate(-1.19 5.38)"></path> </svg> </button> <button id="fab-telegram-btn" class="fab__button fab__button--secondary" style="display:none" data-label="Telegram"> <svg id="fab-icon-svg" fill="#00c9eb" width="33" height="36" viewBox="0 -7 59 59" xmlns="http://www.w3.org/2000/svg"> <path class="cls-1" d="M79.69,4,34,21.82A1.67,1.67,0,0,0,34.09,25L45.71,28.4,50,42.16a2,2,0,0,0,3.33.8l6-6.13,11.79,8.65A2.46,2.46,0,0,0,75,44L82.84,6.67A2.35,2.35,0,0,0,79.69,4ZM74,12.28,52.71,31.06a1.14,1.14,0,0,0-.38.73l-.82,7.27a.22.22,0,0,1-.43,0L47.72,28.26A1.14,1.14,0,0,1,48.21,27L73.3,11.39a.55.55,0,0,1,.66.89Zm0,0" transform="translate(-32.9 -3.83)"></path> </svg> </button> <button id="fab-vk-btn" class="fab__button fab__button--secondary" style="display:none" data-label="VK"> <svg id="fab-icon-svg" fill="#507299" width="33" height="36" viewBox="-3 -16 59 59" xmlns="http://www.w3.org/2000/svg"> <path class="cls-1" d="M34.28,39.21h3a2.51,2.51,0,0,0,1.36-.6A2.19,2.19,0,0,0,39,37.3s-.06-4,1.8-4.6,4.19,3.87,6.69,5.59a4.73,4.73,0,0,0,3.32,1l6.68-.09s3.49-.22,1.84-3c-.14-.23-1-2-5-5.74-4.19-3.88-3.63-3.26,1.42-10,3.07-4.09,4.3-6.59,3.92-7.66s-2.62-.75-2.62-.75l-7.52,0a1.71,1.71,0,0,0-1,.17,2.09,2.09,0,0,0-.66.81A43.35,43.35,0,0,1,45.18,19c-3.35,5.69-4.69,6-5.23,5.63-1.27-.83-1-3.31-1-5.07,0-5.51.84-7.81-1.63-8.4a12.73,12.73,0,0,0-3.51-.35c-2.68,0-5,0-6.24.64-.86.42-1.51,1.35-1.11,1.41A3.38,3.38,0,0,1,28.73,14a7.26,7.26,0,0,1,.74,3.4s.44,6.49-1,7.29c-1,.55-2.4-.58-5.39-5.73a48,48,0,0,1-2.68-5.56,2.23,2.23,0,0,0-.62-.84,3.12,3.12,0,0,0-1.16-.47l-7.14,0s-1.07,0-1.47.5,0,1.27,0,1.27S15.54,27,21.88,33.56c5.81,6,12.4,5.65,12.4,5.65Zm0,0" transform="translate(-9.82 -10.81)"></path> </path> </svg> <a href="https://vk.com/euroroaming" style="width: 47px;height: 47px;position: absolute;top: 0px;left: 0px;" rel="nofollow" target="_blank"></a> </button></div><div class=sandra id=dialogEffects> <div class=dialog id=fab-dialog> <div class=dialog__overlay></div><div class=dialog__content> <p class="fab-dialog-title"></p><div class=dialog__content_h2> <h2 id=dialog__h2>+7(965)636-22-33</h2> </div><div class="align_left w-btn-wrapper" style=margin-right:0><a class="color_custom icon_atleft style_raised w-btn social-close" href="" style=background-color:#7d3daf;color:#fff><i class=material-icons>chat</i><span class=w-btn-label>перейти</span><span class=ripple-container></span></a> </div><div class="align_left w-btn-wrapper" style=margin-right:0><a class="color_custom icon_atleft style_raised w-btn" id=copy-button data-clipboard-action=copy data-clipboard-target=#dialog__h2 style=background-color:#767676;color:#fff><i class=material-icons>content_copy</i><span class=w-btn-label>копировать</span><span class=ripple-container></span></a> </div></div></div></div>';
}

//=======================================================================================================
// Загрузка кода после футора для GS NEW
//=======================================================================================================
add_action( 'us_after_footer', 'ga_new_us_after_footer' );
function ga_new_us_after_footer() {
	$page_ID = get_queried_object_id();
	if ( $page_ID == 70063 ) {
		wp_enqueue_style( 'my-tippy-css', '/wp-content/themes/Zephyr-child/css/tippy.css', array(), '1.0' );
		wp_enqueue_style( 'my-dataTables-css', 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/jquery.dataTables.min.css', array(), '1.0' );

		wp_register_script( 'my-tippy-js', '/wp-content/themes/Zephyr-child/js/tippy.min.js', array(), '1.0', true );
		wp_register_script( 'my-dataTables-js', 'https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'my-gs-new-js', '/wp-content/themes/Zephyr-child/js/gs.new.js', array(
			'jquery',
			'my-tippy-js',
			'my-dataTables-js'
		), '1.0', true );

		wp_enqueue_script( 'my-tippy-js' );
		wp_enqueue_script( 'my-dataTables-js' );
		wp_enqueue_script( 'my-gs-new-js' );
	}
}

//=======================================================================================================
// Отображение хлебных крошек на страницах
//=======================================================================================================
add_action( 'us_before_page', 'my_us_before_page' );
function my_us_before_page() {
	$pagename = get_query_var( 'pagename' );
	if ( $pagename == "questions" ) {
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
		}
	}
}

add_action( 'us_before_archive', 'my_us_before_archive' );
function my_us_before_archive() {
//	echo get_the_ID();
//	echo '<div class="rev-slider-for-category-blog" style="margin-bottom:30px;">';
//	putRevSlider( "slajder-dlya-bloga" );
//	echo '</div>';
}

//=======================================================================================================
// Сортировка пунктов выдачи
//=======================================================================================================
add_filter( 'wpsl_store_data', 'custom_result_sort' );
function custom_result_sort( $store_meta ) {

	$custom_sort = array();

	foreach ( $store_meta as $key => $row ) {
		$custom_sort[ $key ] = $row['store'];
	}

	array_multisort( $custom_sort, SORT_ASC, SORT_REGULAR, $store_meta );

	return $store_meta;
}

//=======================================================================================================
// Временное отключение подключения плагина по условию (на определенной странице)
//=======================================================================================================
//add_filter( 'option_active_plugins', 'lg_disable_plugin' );
//function lg_disable_plugin($plugins){
//
//    $plugins_not_needed = array();
//
//    if( $_SERVER['REQUEST_URI'] ==  '/checkout/') {
//        $key = array_search( 'wp-store-locator/wp-store-locator.php' , $plugins );
//        if ( false !== $key ) {
//            unset( $plugins[$key] );
//        }
//    }
//
//    return $plugins;
//}

//=======================================================================================================
// Пример редиректа на определенную страницу
//=======================================================================================================
//add_action('init', 'my_insert_post_hook');
//function my_insert_post_hook($my_post)
//{
//    if ($_SERVER['REQUEST_URI'] == '/otzyvy/page/2/') {
//        wp_redirect('https://euroroaming.ru/category/otzyvy/', 301);
//        exit;
//    }
//}

add_action( 'us_before_template:shortcodes/us_sharing', 'my_us_before_sharing_in_post' );
function my_us_before_sharing_in_post() {
	if ( is_singular( 'post' ) ) {
// Код, который будет работать только на отдельных страницах с типом записи post
		echo '<div style="padding: 20px 0px 3px 0;margin-bottom: 12px;background: #4c75a3;background-image: url(https://euroroaming.ru/wp-content/uploads/2016/08/bg-hero6-7.svg);background-position: bottom left;background-repeat: repeat;">';
		echo '<h3 style="padding-left: 20px;color: #fff;text-align: center;font-weight: 400;">Не забудьте взять сим-карту для интернета в путешествие</h3>';

		wp_register_style( 'owl-base-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/assets/owl.carousel.min.css' );
		wp_register_script( 'owl-script', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/owl.carousel.min.js' );
		wp_print_styles( 'owl-base-style' );
		wp_print_scripts( 'owl-script' );


		echo '<div class="owl-carousel owl-theme">';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/07/Orange-blog.png" alt=""></div>';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/07/Ortel-Mobile-blog.png" alt=""></div>';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/02/three-4xx.png" alt=""></div>';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/02/GlobalSim-blog-1.png" alt=""></div>';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/02/europasim-4xx.png" alt=""></div>';
		echo '<div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/07/TravelChat-blog.png" alt=""></div>';
		echo ' <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/07/Vodafone-blog.png" alt=""></div>';
		echo '</div>';

		echo '<script>jQuery(document).ready(function(a){a(".owl-carousel").owlCarousel({items:8,lazyLoad:!0,loop:!0,margin:10,stagePadding:6,autoplay:!0,smartSpeed:1200,slideSpeed:1200,autoplayHoverPause:!0,responsiveClass:!0,responsive:{0:{items:1,nav:!0},600:{items:2,nav:!0},1e3:{items:2,nav:!0,loop:!0}}})});</script>';
		echo '<style>.owl-carousel{background:rgba(255,255,255,.8);padding:12px 0 12px 0}</style>';

		echo do_shortcode( '[vc_row height="auto" css=".vc_custom_1477837569113{padding-bottom: 20px !important;}"][vc_column el_class="buy-btn"][us_btn text="подробнее" link="url:https%3A%2F%2Feuroroaming.ru||target:%20_blank|"][us_btn text="купить" link="url:https%3A%2F%2Feuroroaming.ru%2Fshop||target:%20_blank|" color="secondary"][/vc_column][/vc_row]' );
		echo '<style>#gform_5 h3.gform_title{font-weight:500;padding-top: 10px;}#gform_5 label.gfield_label{font-weight:400}#gform_5 .gform_footer.top_label{background:#fec947}#gform_5 .ginput_container input{background:rgba(242,242,242,.52)!important}.buy-btn{text-align: center;}.slick-slider{background: rgba(255, 255, 255, 0.8);padding: 15px 0 12px 0;}.ult-carousel-wrapper{ margin-bottom: 0!important;}</style>';
		gravity_form( 5, true, false, false, '', true );
		echo '</div>';
		echo '<h3 style="padding-left: 20px;color: #124572;text-align: center;font-weight: 400;"><i class="fa fa-share-alt" style="color: #1e73be;"></i> Забирайте статью себе, чтобы не потерять :) </h3>';
	}
	if ( is_singular( 'wpsl_stores' ) ) {
		// Код, который будет работать только на отдельных страницах с типом записи wpsl_stores
		echo '<style>#gform_5 h3.gform_title{font-weight:500;padding-top: 10px;}#gform_5 label.gfield_label{font-weight:400}#gform_5 .gform_footer.top_label{background:#fec947}#gform_5 .ginput_container input{background:rgba(242,242,242,.52)!important}@media only screen and (max-width: 641px){input#gform_submit_button_5{max-width:176px;line-height:3}ul#gform_fields_5{padding:16px}.gform_validation_error ul#gform_fields_5 {padding-right: 12px!important;} }.gform_validation_error ul#gform_fields_5 {padding-right: 33px;}</style>';
		gravity_form( 5, true, false, false, '', true );
	}
	echo '<style>#gform_wrapper_5{padding: 10px;}#gform_5{text-align: center;}</style>';
}

//=======================================================================================================
// Отправка данных на seller при продаже сим-карты ТА
//=======================================================================================================
add_action( 'woocommerce_order_status_processing', 'woocommerce_order_statuses_pos' ); // Отправка данных на seller если заказ оплачен
//add_action( 'woocommerce_order_status_on-hold', 'woocommerce_order_statuses_pos' ); // Отправка данных на seller если статус заказа "Новый заказ" (Pay.Travel)
function woocommerce_order_statuses_pos( $order_id ) {
	$order_by = get_post_meta( $order_id, '_created_via', true );

	//если категория не 'сим-карты' выходим
	if ( ! is_category_sim_karty( $order_id ) || $order_by != 'POS' ) {
		return;
	}

	$key_sim_numbers       = 'number_simcard';
	$order_sim_numbers_buf = get_post_meta( $order_id, $key_sim_numbers, true );
	$order_sim_numbers     = str_replace( ' ', '', $order_sim_numbers_buf );
	$sim_numbers           = explode( ',', $order_sim_numbers );

	$key_ta_id   = 'wc_pos_ta_id';
	$order_ta_id = get_post_meta( $order_id, $key_ta_id, true );

	$key_customer_name   = '_billing_first_name';
	$order_customer_name = str_replace( ' ', '', get_post_meta( $order_id, $key_customer_name, true ) );

	$key_customer_surname   = '_billing_last_name';
	$order_customer_surname = str_replace( ' ', '', get_post_meta( $order_id, $key_customer_surname, true ) );

	$key_customer_email   = 'client_email';
	$order_customer_email = str_replace( ' ', '', get_post_meta( $order_id, $key_customer_email, true ) );

	$key_customer_phone   = 'client_phone';
	$order_customer_phone = str_replace( ' ', '', get_post_meta( $order_id, $key_customer_phone, true ) );

	$payment_method = get_post_meta( $order_id, '_payment_method', true );

	$orange_deposit = str_replace( ' ', '', get_post_meta( $order_id, 'wc_pos_orange_discount', true ) );

	$three_deposit = str_replace( ' ', '', get_post_meta( $order_id, 'wc_pos_three_discount', true ) );


	//$order_meta1 = get_post_meta($order_id);
	foreach ( $sim_numbers as $number ) {
		$buf_number = $number;
		$int_number = intval( $buf_number );
		$space      = ' ';
		$ta_deposit = 0;
		$url        = '';

		if ( $payment_method == "pos_customer_pay" || $payment_method == "pos_customer_pay_paytravel" ) {
			if ( strlen( $number ) == 9 || strlen( $number ) == 13 ) {
				$ta_deposit = intval( $orange_deposit );
			} else if ( strlen( $number ) == 11 ) {
				$ta_deposit = intval( $three_deposit );
			}
			$url = "http://seller.sgsim.ru/euroroaming_order_submit?operation=submit_operation&ta=$order_ta_id&orderid=$order_id&customer=$order_customer_name%20$order_customer_surname&email=$order_customer_email&phone=$order_customer_phone&onum=$number&ta_deposit=$ta_deposit";
		} else {
			$url = "http://seller.sgsim.ru/euroroaming_order_submit?operation=submit_operation&ta=$order_ta_id&orderid=$order_id&customer=$order_customer_name%20$order_customer_surname&email=$order_customer_email&phone=$order_customer_phone&onum=$number&ta_deposit=0";
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
		curl_setopt( $ch, CURLOPT_URL, $url );
		$data1 = curl_exec( $ch );
		curl_close( $ch );
	}


	// Подтверждение оплаты заказа для ТА
	if ( $payment_method == "pos_customer_pay" ){
		$order_ta_phone = get_post_meta( $order_id, 'wc_pos_ta_phone', true );
		$order_message = 'Заказ #'.$order_id.' был оплачен';
		//Отправляем смс
	    do_action('send_sms_hook', array("gate.iqsms.ru", 80, "z1496927079417", "340467", $order_ta_phone, $order_message, "Euroroaming"));

		$order_ta_email = get_post_meta( $order_id, 'wc_pos_ta_email', true );
		$headers = 'From: Евророуминг <info@euroroaming.ru>' . "\r\n";
		//Отправляем сообщение на почту
		wp_mail($order_ta_email, 'Заказ #'.$order_id.' был оплачен', 'Заказ #'.$order_id.' был оплачен', $headers);
    }

    $nene = new WPSEO_Redirect_Manager();
	$tests = new WPSEO_Redirect('61306-pp', 'checkout/order-pay/61306?pay_for_order=true&key=wc_order_5a3c4e4b99cf4');
	$nene->create_redirect($tests);


	//$order = wc_get_order( $order_id );
	//$order_data = $order->get_data(); // The Order data
	//file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\pos_test.txt", print_r( get_post_meta( $order_id ), true ), FILE_APPEND | LOCK_EX );
	//file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\pos_test.txt", "\r\n" . '/***********************************************************************/' . "\r\n", FILE_APPEND | LOCK_EX );
	//file_put_contents( $_SERVER['DOCUMENT_ROOT'] . "\logs\pos_test.txt", print_r( $order_id . ': Данные отправлены' . "\r\n", true ), FILE_APPEND | LOCK_EX );
}

function send_sms_message( $data) {
	$wapurl = false;
	$fp = fsockopen( $data[0], $data[1], $errno, $errstr );
	if ( ! $fp ) {
		return "errno: $errno \nerrstr: $errstr\n";
	}
	fwrite( $fp, "GET /send/" .
	             "?phone=" . rawurlencode( $data[4] ) .
	             "&text=" . rawurlencode( $data[5] ) .
	             ( $data[6] ? "&sender=" . rawurlencode( $data[6] ) : "" ) .
	             ( $wapurl ? "&wapurl=" . rawurlencode( $wapurl ) : "" ) .
	             "  HTTP/1.0\n" );
	fwrite( $fp, "Host: " . $data[0] . "\r\n" );
	if ( $data[2] != "" ) {
		fwrite( $fp, "Authorization: Basic " .
		             base64_encode( $data[2] . ":" . $data[3] ) . "\n" );
	}
	fwrite( $fp, "\n" );
	$response = "";
	while ( ! feof( $fp ) ) {
		$response .= fread( $fp, 1 );
	}
	fclose( $fp );
	list( $other, $responseBody ) = explode( "\r\n\r\n", $response, 2 );
}
add_action( 'send_sms_hook', 'send_sms_message' );

function is_category_sim_karty_pos( $order_id ) {
	//Получение заказа
	$order = new WC_Order( $order_id );

	//Проверка заказа на категорию товара
	$items      = $order->get_items();
	$product_id = 0;
	foreach ( $items as $item ) {
		$product_id = $item['product_id'];
		break;
	}
	$product_cats = get_the_terms( $product_id, 'product_cat' );
	$i_sim        = 0;
	foreach ( $product_cats as $product_cat ) {
		if ( $product_cat->slug == 'sim-karty' ) {
			$i_sim ++;
		}
	}
	//если категория не 'сим-карты' выходим
	if ( $i_sim === 0 ) {
		return false;
	} else {
		return true;
	}
}

/*function storefront_child_remove_unwanted_form_fields($fields) {
	unset( $fields ['company'] );
	unset( $fields ['address_2'] );
	unset( $fields ['billing_phone'] );

	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'storefront_child_remove_unwanted_form_fields' );*/
