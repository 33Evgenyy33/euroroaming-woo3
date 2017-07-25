<?php
/* Custom functions code goes here. */

/**
 * Load Enqueued Scripts in the Footer
 *
 * Automatically move JavaScript code to page footer, speeding up page loading time.
 */

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

/* Загрузка скрипта плавного скролла */
add_action('wp_enqueue_scripts', 'my_scripts_method');
function my_scripts_method()
{
    //wp_enqueue_script('smoothscroll', get_stylesheet_directory_uri() . '/js/smoothscroll.js', array('jquery'), '1.0', true);

    //wp_enqueue_style( 'fab',  get_stylesheet_directory_uri() . '/css/fab.css');
    //wp_enqueue_script( 'clipboard', get_stylesheet_directory_uri() . '/js/clipboard.min.js', array('jquery'), '1.0', true);
    wp_enqueue_script('fab', get_stylesheet_directory_uri() . '/js/fab.js', array('jquery'), '1.0', true);
}

add_filter('pre_get_posts', 'hide_posts_media_by_other');
function hide_posts_media_by_other($query)
{
    global $pagenow;
    if (('edit.php' != $pagenow && 'upload.php' != $pagenow) || !$query->is_admin) {
        return $query;
    }
    if (!current_user_can('manage_options')) {
        global $user_ID;
        $query->set('author', $user_ID);
    }
    return $query;
}

add_filter('posts_where', 'hide_attachments_wpquery_where');
function hide_attachments_wpquery_where($where)
{
    global $current_user;
    if (!current_user_can('manage_options')) {
        if (is_user_logged_in()) {
            if (isset($_POST['action'])) {
                // library query
                if ($_POST['action'] == 'query-attachments') {
                    $where .= ' AND post_author=' . $current_user->data->ID;
                }
            }
        }
    }
    return $where;
}

/* Загрузка скрипта VKontakte в шапку сайта  */
add_action('wp_head', 'my_custom_js');
function my_custom_js()
{
    echo '<script type="text/javascript">(window.Image ? (new Image()) : document.createElement(\'img\')).src = location.protocol + \'//vk.com/rtrg?r=HdsKraxE8HWoxhJ9OdOoqg5IsYvCGO0MUyAtZPZSWnZjFqyBwsZwGSimf9a01GccFD*fVs8cOL/y33Qs1uNfJPOURuay/bk2uZXD*BcncsKrfhtGgL5i4hvdMr8*07HDKD*1BUTHOS*rTVsYrS8oATYONJXDHctGbr8JeWB0ffw-&pixel_id=1000021590\';</script><!-- Facebook Pixel Code --><script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,\'script\',\'https://connect.facebook.net/en_US/fbevents.js\');fbq(\'init\', \'392030000921269\');fbq(\'track\', "PageView");</script><noscript><img height="1" width="1" style="display:none"src="https://www.facebook.com/tr?id=392030000921269&ev=PageView&noscript=1"/></noscript><!-- End Facebook Pixel Code -->';
}

/* Загрузка скрипта Scroll To ID */
add_action('wp_enqueue_scripts', 'my_scrolltoid_scripts');
function my_scrolltoid_scripts()
{
    if (is_single()) {
        wp_enqueue_script('scrolltoid', get_stylesheet_directory_uri() . '/js/scrolltoid.js');
    }
}

/* Загрузка скрипта для страницы CHECKOUT*/
add_action('wp_enqueue_scripts', 'my_checkout_scripts');
function my_checkout_scripts()
{
    if (is_page('checkout')) {
        wp_enqueue_script('mycheckout', get_stylesheet_directory_uri() . '/js/mycheckout.js', '', '', true);
    }
}

//Цена пластика на миниатюре товара на странице shop
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_price', 10);
function woocommerce_template_loop_product_price()
{
    global $post;
    $price = 0;

    switch ($post->ID) {
        case 18402:
            $price = 1340;
            break;
        case 18455:
            $price = 750;
            break;
        case 28328:
            $price = 750;
            break;
        case 18446:
            $price = 1005;
            break;
        case 41120:
            $price = 1000;
            break;
        case 18453:
            $price = 1140;
            break;
        case 18438:
            $price = 2345;
            break;
        case 48067:
            $price = 750;
            break;
        case 55050:
            $price = 0;
            break;
        case 18443:
            return;
    }
    //echo '<h3 id="shop-plastic-price">'.$post->ID.'</h3>';
    echo '<style>#shop-plastic-price {padding: 0;color: #838b08;font-weight: 500;}</style><h3 id="shop-plastic-price">' . $price . '₽</h3>';
}

/******* Разрешение загрузки разных тапов файлов *******/
/*function bodhi_svgs_disable_real_mime_check( $data, $file, $filename, $mimes ) {
    $wp_filetype = wp_check_filetype( $filename, $mimes );

    $ext = $wp_filetype['ext'];
    $type = $wp_filetype['type'];
    $proper_filename = $data['proper_filename'];

    return compact( 'ext', 'type', 'proper_filename' );
}
add_filter( 'wp_check_filetype_and_ext', 'bodhi_svgs_disable_real_mime_check', 10, 4 );*/

add_filter('upload_mimes', 'cc_mime_types');
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}

/*********************************************/

get_template_part('shortcodes');

add_filter('wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2);
function wc_make_processing_orders_editable($is_editable, $order)
{
    if ($order->get_status() == 'processing') {
        $is_editable = true;
    }

    return $is_editable;
}

add_action('admin_menu', 'my_remove_menu_pages', 999);
function my_remove_menu_pages()
{
    global $submenu;

    if (!current_user_can('add_users')) {

        remove_menu_page('users.php');
        remove_menu_page('vc-welcome');

        unset($submenu['wc_point_of_sale'][2]);
    }

}

/* Шоткод таблица стран */
add_shortcode('country_table', 'country_table_func');
function country_table_func()
{
    $output = '<script>jQuery(document).ready(function(n){n(document).ready(function(){"use strict";n(".menu > ul > li:has( > ul)").addClass("menu-dropdown-icon"),n(".menu > ul > li > ul:not(:has(ul))").addClass("normal-sub"),n(".menu > ul > li").hover(function(u){n(window).width()>943&&(n(this).children("ul").stop(!0,!1).fadeToggle(150),u.preventDefault())}),n(".menu > ul > li").click(function(){n(window).width()<=943&&n(this).children("ul").fadeToggle(150)})})});</script><div class="menu-container"> <div class="menu"> <ul> <li><a class="btn-pricing-tabl"><i class="material-icons">public</i> <span class="country_table_rep_text">Сравнение тарифов по странам</span></a> <ul> <li><span>А-Д</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-avstrii/" target="_blank">Австрия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-albanii/" target="_blank">Албания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-andorre/" target="_blank">Андорра</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-belgii/" target="_blank">Бельгия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-bolgarii/" target="_blank">Болгария</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-bosnii-i-gertsegovine/" target="_blank">Босния и Герцеговина</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-velikobritanii/" target="_blank">Великобритания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-vengrii/" target="_blank">Венгрия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gvadelupe/" target="_blank">Гваделупе</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-germanii/" target="_blank">Германия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gernsi/" target="_blank">Гернси</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-gibraltare/" target="_blank">Гибралтар</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gonkonge/" target="_blank">Гонконг</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-grenlandii/" target="_blank">Гренландия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-gretsii/" target="_blank">Греция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-danii/" target="_blank">Дания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-dzhersi/" target="_blank">Джерси</a></li> </ul> </li> <li><span>И-М</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-izraile/" target="_blank">Израиль</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-indii/" target="_blank">Индия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-irlandii/" target="_blank">Ирландия</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-islandii/" target="_blank">Исландия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-ispanii/" target="_blank">Испания</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-italii/" target="_blank">Италия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-kanarskih-ostrovah/" target="_blank">Канарские острова</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-kipre/" target="_blank">Кипр</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-kitae/" target="_blank">Китай</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-latvii/" target="_blank">Латвия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-litve/" target="_blank">Литва</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-lihtenshtejne/" target="_blank">Лихтенштейн</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-lyuksemburge/" target="_blank">Люксембург</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-makedonii/" target="_blank">Македония</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-malajzii/" target="_blank">Малайзия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-malte/" target="_blank">Мальта</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-marokko/" target="_blank">Марокко</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-martinike/" target="_blank">Мартиника</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-monako/" target="_blank">Монако</a></li> </ul> </li> <li><span>Н-Т</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-niderlandah/" target="_blank">Нидерланды</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-norvegii/" target="_blank">Норвегия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-ostrove-men/" target="_blank">Остров Мэн</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-polshe/" target="_blank">Польша</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-portugalii/" target="_blank">Португалия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-pribaltike/" target="_blank" rel="nofollow">Прибалтика</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-reyunone/" target="_blank">Реюньон</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-rumynii/" target="_blank">Румыния</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-san-marino/" target="_blank">Сан-Марино</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-sen-bartelemi/" target="_blank">Сен-Бартелеми</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-sen-martene/" target="_blank">Сен-Мартен</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-serbii/" target="_blank">Сербия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-singapure/" target="_blank">Сингапур</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-slovakii/" target="_blank">Словакия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-slovenii/" target="_blank">Словения</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-ssha/" target="_blank">США</a></li><li><a href="https://euroroaming.ru/mobilnyj-internet-v-tailande/" target="_blank">Таиланд</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-turtsii/" target="_blank">Турция</a></li></ul> </li> <li><span>Ф-Э</span> <ul> <li><a href="https://euroroaming.ru/mobilnyj-internet-na-farerskih-ostrovah/" target="_blank">Фарерские острова</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-finlyandii/" target="_blank">Финляндия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-vo-frantsii/" target="_blank">Франция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-vo-frantsuzskoj-gviane/" target="_blank">Французская Гвиана</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-horvatii/" target="_blank">Хорватия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-chehii/" target="_blank">Чехия</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-chili/" target="_blank">Чили</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-shvejtsarii/" target="_blank">Швейцария</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-shvetsii/" target="_blank">Швеция</a></li> <li><a href="https://euroroaming.ru/mobilnyj-internet-v-estonii/" target="_blank">Эстония</a></li> </ul> </li> </ul> </li> </ul> </div> </div>';
    return $output;
}

/* Шоткод таблица стран для главной */
add_shortcode('country_table_main', 'country_table_main_func');
function country_table_main_func()
{
    $output = '<ul class=g-cols><li class="vc_col-md-3 vc_col-sm-6"><span>А-Д</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-avstrii/ rel=nofollow target=_blank>Австрия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-albanii/ rel=nofollow target=_blank>Албания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-andorre/ rel=nofollow target=_blank>Андорра</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-belgii/ rel=nofollow target=_blank>Бельгия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-bolgarii/ rel=nofollow target=_blank>Болгария</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-bosnii-i-gertsegovine/ rel=nofollow target=_blank>Босния и Герцеговина</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-velikobritanii/ rel=nofollow target=_blank>Великобритания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-vengrii/ rel=nofollow target=_blank>Венгрия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gvadelupe/ rel=nofollow target=_blank>Гваделупе</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-germanii/ rel=nofollow target=_blank>Германия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gernsi/ rel=nofollow target=_blank>Гернси</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-gibraltare/ rel=nofollow target=_blank>Гибралтар</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gonkonge/ rel=nofollow target=_blank>Гонконг</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-grenlandii/ rel=nofollow target=_blank>Гренландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-gretsii/ rel=nofollow target=_blank>Греция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-danii/ rel=nofollow target=_blank>Дания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-dzhersi/ rel=nofollow target=_blank>Джерси</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>И-М</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-izraile/ rel=nofollow target=_blank>Израиль</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-indii/ rel=nofollow target=_blank>Индия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-irlandii/ rel=nofollow target=_blank>Ирландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-islandii/ rel=nofollow target=_blank>Исландия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-ispanii/ rel=nofollow target=_blank>Испания</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-italii/ rel=nofollow target=_blank>Италия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-kanarskih-ostrovah/ rel=nofollow target=_blank>Канарские острова</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-kipre/ rel=nofollow target=_blank>Кипр</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-kitae/ rel=nofollow target=_blank>Китай</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-latvii/ rel=nofollow target=_blank>Латвия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-litve/ rel=nofollow target=_blank>Литва</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-lihtenshtejne/ rel=nofollow target=_blank>Лихтенштейн</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-lyuksemburge/ rel=nofollow target=_blank>Люксембург</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-makedonii/ rel=nofollow target=_blank>Македония</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-malajzii/ rel=nofollow target=_blank>Малайзия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-malte/ rel=nofollow target=_blank>Мальта</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-marokko/ rel=nofollow target=_blank>Марокко</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-martinike/ rel=nofollow target=_blank>Мартиника</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-monako/ rel=nofollow target=_blank>Монако</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>Н-Т</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-v-niderlandah/ rel=nofollow target=_blank>Нидерланды</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-norvegii/ rel=nofollow target=_blank>Норвегия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-ostrove-men/ rel=nofollow target=_blank>Остров Мэн</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-polshe/ rel=nofollow target=_blank>Польша</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-portugalii/ rel=nofollow target=_blank>Португалия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-pribaltike/ rel=nofollow target=_blank>Прибалтика</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-reyunone/ rel=nofollow target=_blank>Реюньон</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-rumynii/ rel=nofollow target=_blank>Румыния</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-san-marino/ rel=nofollow target=_blank>Сан-Марино</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-sen-bartelemi/ rel=nofollow target=_blank>Сен-Бартелеми</a><li><a href=https://euroroaming.ru/mobilnyj-internet-na-sen-martene/ rel=nofollow target=_blank>Сен-Мартен</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-serbii/ rel=nofollow target=_blank>Сербия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-singapure/ rel=nofollow target=_blank>Сингапур</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-slovakii/ rel=nofollow target=_blank>Словакия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-slovenii/ rel=nofollow target=_blank>Словения</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-ssha/ rel=nofollow target=_blank>США</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-tailande/ rel=nofollow target=_blank>Таиланд</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-turtsii/ rel=nofollow target=_blank>Турция</a></ul><li class="vc_col-md-3 vc_col-sm-6"><span>Ф-Э</span><ul><li><a href=https://euroroaming.ru/mobilnyj-internet-na-farerskih-ostrovah/ rel=nofollow target=_blank>Фарерские острова</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-finlyandii/ rel=nofollow target=_blank>Финляндия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-vo-frantsii/ rel=nofollow target=_blank>Франция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-vo-frantsuzskoj-gviane/ rel=nofollow target=_blank>Французская Гвиана</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-horvatii/ rel=nofollow target=_blank>Хорватия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-chehii/ rel=nofollow target=_blank>Чехия</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-chili/ rel=nofollow target=_blank>Чили</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-shvejtsarii/ rel=nofollow target=_blank>Швейцария</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-shvetsii/ rel=nofollow target=_blank>Швеция</a><li><a href=https://euroroaming.ru/mobilnyj-internet-v-estonii/ rel=nofollow target=_blank>Эстония</a></ul></ul>';
    return $output;
}

/* Отправление почты */
add_action('phpmailer_init', 'tweak_mailer_ssl', 999);
function tweak_mailer_ssl($phpmailer)
{
    $phpmailer->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
}

add_filter('woocommerce_states', 'custom_woocommerce_states');
function custom_woocommerce_states($states)
{

    $states['RU'] = array(
        'Респ Адыгея' => 'Республика Адыгея',
        'Респ Алтай' => 'Республика Алтай',
        'Респ Башкортостан' => 'Республика Башкортостан',
        'Респ Бурятия' => 'Республика Бурятия',
        'Респ Дагестан' => 'Республика Дагестан',
        'Респ Ингушетия' => 'Республика Ингушетия',
        'Кабардино-Балкарская Респ' => 'Кабардино-Балкарская республика',
        'Респ Калмыкия' => 'Республика Калмыкия',
        'Карачаево-Черкесская Респ' => 'Карачаево-Черкесская республика',
        'Респ Карелия' => 'Республика Карелия',
        'Респ Коми' => 'Республика Коми',
        'Респ Крым' => 'Крым',
        'Респ Марий Эл' => 'Республика Марий Эл',
        'Респ Мордовия' => 'Республика Мордовия',
        'Респ Саха /Якутия/' => 'Республика Саха (Якутия)',
        'Респ Северная Осетия - Алания' => 'Респ Северная Осетия-Алания',
        'Респ Татарстан' => 'Республика Татарстан',
        'Респ Тува' => 'Республика Тыва',
        'Удмуртская Респ' => 'Удмуртская республика',
        'Респ Хакасия' => 'Республика Хакасия',
        'Чеченская Респ' => 'Чеченская республика',
        'Чувашская Республика - Чувашия' => 'Чувашская республика',
        'Алтайский край' => 'Алтайский край',
        'Забайкальский край' => 'Забайкальский край',
        'Камчатский край' => 'Камчатский край',
        'Краснодарский край' => 'Краснодарский край',
        'Красноярский край' => 'Красноярский край',
        'Пермский край' => 'Пермский край',
        'Приморский край' => 'Приморский край',
        'Ставропольский край' => 'Ставропольский край',
        'Хабаровский край' => 'Хабаровский край',
        'Амурская обл' => 'Амурская область',
        'Архангельская обл' => 'Архангельская область',
        'Астраханская обл' => 'Астраханская область',
        'Белгородская обл' => 'Белгородская область',
        'Брянская обл' => 'Брянская область',
        'Владимирская обл' => 'Владимирская область',
        'Волгоградская обл' => 'Волгоградская область',
        'Вологодская обл' => 'Вологодская область',
        'Воронежская обл' => 'Воронежская область',
        'Ивановская обл' => 'Ивановская область',
        'Иркутская обл' => 'Иркутская область',
        'Калининградская обл' => 'Калининградская область',
        'Калужская обл' => 'Калужская область',
        'Кемеровская обл' => 'Кемеровская область',
        'Кировская обл' => 'Кировская область',
        'Костромская обл' => 'Костромская область',
        'Курганская обл' => 'Курганская область',
        'Курская обл' => 'Курская область',
        'Ленинградская обл' => 'Ленинградская область',
        'Липецкая обл' => 'Липецкая область',
        'Магаданская обл' => 'Магаданская область',
        'Московская обл' => 'Московская область',
        'Мурманская обл' => 'Мурманская область',
        'Нижегородская обл' => 'Нижегородская область',
        'Новгородская обл' => 'Новгородская область',
        'Новосибирская обл' => 'Новосибирская область',
        'Омская обл' => 'Омская область',
        'Оренбургская обл' => 'Оренбургская область',
        'Орловская обл' => 'Орловская область',
        'Пензенская обл' => 'Пензенская область',
        'Псковская обл' => 'Псковская область',
        'Ростовская обл' => 'Ростовская область',
        'Рязанская обл' => 'Рязанская область',
        'Самарская обл' => 'Самарская область',
        'Саратовская обл' => 'Саратовская область',
        'Сахалинская обл' => 'Сахалинская область',
        'Свердловская обл' => 'Свердловская область',
        'Смоленская обл' => 'Смоленская область',
        'Тамбовская обл' => 'Тамбовская область',
        'Тверская обл' => 'Тверская область',
        'Томская обл' => 'Томская область',
        'Тульская обл' => 'Тульская область',
        'Тюменская обл' => 'Тюменская область',
        'Ульяновская обл' => 'Ульяновская область',
        'Челябинская обл' => 'Челябинская область',
        'Ярославская обл' => 'Ярославская область',
        'г Москва' => 'Москва',
        'г Санкт-Петербург' => 'Санкт-Петербург',
        'г Севастополь' => 'Севастополь',
        'Еврейская Аобл' => 'Еврейская автономная область',
        'Ненецкий АО' => 'Ненецкий автономный округ',
        'Ханты-Мансийский Автономный округ - Югра' => 'Ханты-Мансийский автономный округ - Югра',
        'Чукотский АО' => 'Чукотский автономный округ',
        'Ямало-Ненецкий АО' => 'Ямало-Ненецкий автономный округ'
    );
    return $states;
}

/*********************************Woocommerce промокод************************************************/
add_action('woocommerce_applied_coupon', 'apply_product_on_coupon');
function apply_product_on_coupon()
{
    global $woocommerce;

    $coupons = $woocommerce->cart->get_applied_coupons();

    $coupon_id = $coupons[0];

    $coupon_code_vodafone = $coupon_id . '-vodafone';
    $coupon_code_orange = $coupon_id . '-orange';
    $coupon_code_ortel = $coupon_id . '-ortel';
    $coupon_code_globalsim = $coupon_id . '-globalsim';
    $coupon_code_globalsim_usa = $coupon_id . '-globalsim-usa';
    $coupon_code_globalsim_internet = $coupon_id . '-globalsim-internet';
    $coupon_code_europasim = $coupon_id . '-europasim';
    $coupon_code_travelchat = $coupon_id . '-travelchat';


    $the_coupon_vodafone = new WC_Coupon($coupon_code_vodafone);
    $the_coupon_orange = new WC_Coupon($coupon_code_orange);
    $the_coupon_ortel = new WC_Coupon($coupon_code_ortel);
    $the_coupon_globalsim = new WC_Coupon($coupon_code_globalsim);
    $the_coupon_globalsim_usa = new WC_Coupon($coupon_code_globalsim_usa);
    $the_coupon_globalsim_internet = new WC_Coupon($coupon_code_globalsim_internet);
    $the_coupon_europasim = new WC_Coupon($coupon_code_europasim);
    $the_coupon_travelchat = new WC_Coupon($coupon_code_travelchat);


    if (in_array($coupon_id, $woocommerce->cart->applied_coupons)) {
        if ($the_coupon_vodafone->is_valid() && !in_array($coupon_code_vodafone, $woocommerce->cart->applied_coupons)) {
            $the_coupon_vodafone->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_vodafone);
        }
        if ($the_coupon_travelchat->is_valid() && !in_array($the_coupon_travelchat, $woocommerce->cart->applied_coupons)) {
            $the_coupon_travelchat->add_coupon_message('');

            $woocommerce->cart->add_discount($the_coupon_travelchat);
        }
        if ($the_coupon_orange->is_valid() && !in_array($coupon_code_orange, $woocommerce->cart->applied_coupons)) {
            $the_coupon_orange->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_orange);
        }
        if ($the_coupon_ortel->is_valid() && !in_array($coupon_code_ortel, $woocommerce->cart->applied_coupons)) {
            $the_coupon_ortel->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_ortel);
        }
        if ($the_coupon_globalsim->is_valid() && !in_array($coupon_code_globalsim, $woocommerce->cart->applied_coupons)) {
            $the_coupon_globalsim->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_globalsim);
        }
        if ($the_coupon_globalsim_usa->is_valid() && !in_array($coupon_code_globalsim_usa, $woocommerce->cart->applied_coupons)) {
            $the_coupon_globalsim_usa->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_globalsim_usa);
        }
        if ($the_coupon_globalsim_internet->is_valid() && !in_array($coupon_code_globalsim_internet, $woocommerce->cart->applied_coupons)) {
            $the_coupon_globalsim_internet->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_globalsim_internet);
        }
        if ($the_coupon_europasim->is_valid() && !in_array($coupon_code_europasim, $woocommerce->cart->applied_coupons)) {
            $the_coupon_europasim->add_coupon_message('');

            $woocommerce->cart->add_discount($coupon_code_europasim);
        }
    }



    //$coupons = $woocommerce->cart->get_coupons();
    /*if (in_array($coupon_id, $woocommerce->cart->applied_coupons) && count($coupons) == 1) {
        WC()->cart->remove_coupon($coupon_id);
        echo '<style>.woocommerce-message{display: none;}</style>';
        wc_add_notice(sprintf(__("Жаль, но этот промокод не может быть использован для товаров, которые находятся у вас в корзине.", "your-theme-language")), 'error');
    } else {
        echo '<style>.woocommerce div.woocommerce-message + div.woocommerce-message{display: none;}</style>';
    }*/

    $coupons = $woocommerce->cart->get_coupons();

    if (count($coupons) >= 1) {
        echo '<style>.woocommerce-info{display:none;}</style>';
    }
}

/********************************************************************************************************************/

/************Проверка на совместимость продуктов в корзине (баланс не может быть вместе с сим-картой в одном заказе)******************/
add_filter('woocommerce_add_to_cart_validation', 'filter_woocommerce_add_to_cart_validation', 10, 3);
function filter_woocommerce_add_to_cart_validation($true, $product_id, $quantity)
{
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $add_product_cat = get_the_terms($product_id, 'product_cat');

    foreach ($items as $item) {
        $product_in_cart_cat = get_the_terms($item['product_id'], 'product_cat');
        if ($add_product_cat[0]->slug !== $product_in_cart_cat[0]->slug) {
            wc_add_notice(__('Добавляемый продукт не совместим с тем, что Вы уже добавили в корзину. Данные продукты оформляются по отдельности', 'woocommerce'), 'error');
            return false;
        }
    }
    //file_put_contents("processing-3.txt", print_r($add_product_cat[0]->slug, true));
    return $true;
}

;
/***********************************************************************************************************************************/

add_action('pre_get_posts', 'shop_filter_cat');
function shop_filter_cat($query)
{
    if (!is_admin() && is_post_type_archive('product') && $query->is_main_query()) {
        $query->set('tax_query', array(
                array('taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'sim-karty'
                )
            )
        );
    }
}

/**********************************************/
/*function wc_ninja_remove_checkout_field($fields)
{
    $chosen_methods = WC()->session->get('chosen_shipping_methods');
    //var_dump($chosen_methods);
    $chosen_shipping = $chosen_methods[0];

    echo 'test1';

    if ($chosen_shipping == 'local_pickup_plus') {
        echo 'test3';
        unset($fields['billing']['billing_city']);
        return $fields;
    }
    return $fields;
}

add_action('woocommerce_cart_calculate_fees', 'woocommerce_custom_surcharge');
function woocommerce_custom_surcharge()
{

    add_filter('woocommerce_checkout_fields', 'wc_ninja_remove_checkout_field');
}*/
/**********************************************/

add_filter('woocommerce_available_payment_gateways', 'filter_gateways');
function filter_gateways($gateways)
{
    $payment_NAME = 'cheque';

    $chosen_methods = WC()->session->get('chosen_shipping_methods');
    //var_dump($chosen_methods);
    $chosen_shipping = $chosen_methods[0];

    if ($chosen_shipping == '18616' || $chosen_shipping == 'local_pickup_plus' || $chosen_shipping == 'flat_rate:1' || $chosen_shipping == 'flat_rate:2') unset($gateways[$payment_NAME]);

    if ($chosen_shipping == null) unset($gateways[$payment_NAME]);

    return $gateways;
}

/*
 * Доступные способы доставки в зависимости от товара в корзине
 *
 * */
add_filter('woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100);
function my_hide_shipping_when_free_is_available($rates)
{
    global $woocommerce;

    $free = array();

    $items = $woocommerce->cart->get_cart();

    foreach ($items as $item => $values) {

        $_product = $values['data']->post;
        //print_r($values['variation_id']);

        if ($_product->ID == 25841) {
            foreach ($rates as $rate_id => $rate) {
                if ('advanced_shipping' == $rate->method_id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }

        /*if ($_product->ID == 18446 || $_product->ID == 28328 || $_product->ID == 18453 || $_product->ID == 18443) {
            foreach ($rates as $rate_id => $rate) {
                if ('local_pickup_plus' == $rate->method_id || '18616' == $rate->id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }*/

        /************Three*************/
        /*if ($_product->ID == 55050) {
            //print_r($_product);
            foreach ($rates as $rate_id => $rate) {
                //echo $rate->method_id.'<br>';
                if ('advanced_shipping' == $rate->method_id || 'flat_rate' == $rate->method_id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }*/

        /************Vodafone только выдача (все форматы)*************/
        /*if ($_product->ID == 18438) {
            //print_r($_product);
            foreach ($rates as $rate_id => $rate) {
                if ('local_pickup_plus' == $rate->method_id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }*/

        /************EuropaSim только выдача (все форматы)*************/
        if ($_product->ID == 28328) {
            //print_r($_product);
            foreach ($rates as $rate_id => $rate) {
                if ('local_pickup_plus' == $rate->method_id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }

        /************Orange только выдача (все форматы)*************/
        /*if ($_product->ID == 18402) {
            //print_r($_product);
            foreach ($rates as $rate_id => $rate) {
                if ('local_pickup_plus' == $rate->method_id) {
                    $free[$rate_id] = $rate;
                }
            }
            break;
        }*/

        /************Orange только выдача (nano)*************/
        /*if ($_product->ID == 18402) {
            //print_r($_product);
            if ($values['variation_id'] == 24062 || $values['variation_id'] == 24059 || $values['variation_id'] == 24056
                || $values['variation_id'] == 31083 || $values['variation_id'] == 30954 || $values['variation_id'] == 30955) {
                foreach ($rates as $rate_id => $rate) {
                    if ('local_pickup_plus' == $rate->method_id) {
                        $free[$rate_id] = $rate;
                    }
                }
                break;
            }
        }*/
        /*
        24062
        24059
        24056
        31083
        30954
        30955
        local_pickup_plus
        */

    }
    return !empty($free) ? $free : $rates;
}

add_filter('affwp_currencies', 'affwp_custom_add_currency');
function affwp_custom_add_currency($currencies)
{
    $currencies['ye'] = 'YE';
    return $currencies;
}

/*add_filter('show_admin_bar', 'my_function_admin_bar');
function my_function_admin_bar()
{
    if (members_current_user_has_role('cashier') || members_current_user_has_role('editor') || members_current_user_has_role('administrator') || members_current_user_has_role('shop_manager')) {
        return true;
    } else {
        return false;
    }
}*/

add_action('wp_logout', create_function('', 'wp_redirect(home_url());exit();'));

//==================================================
// Пример редиректа
//==================================================
/*add_action('init', 'my_insert_post_hook');
function my_insert_post_hook($my_post)
{
    if ($_SERVER['REQUEST_URI'] == '/otzyvy/page/2/') {
        wp_redirect('https://euroroaming.ru/category/otzyvy/', 301);
        exit;
    }
}*/

add_action('woocommerce_before_checkout_form', 'action_woocommerce_before_checkout_form', 10, 2);
function action_woocommerce_before_checkout_form()
{
    global $woocommerce;

    $product_in_cart = false;

    foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
        $_product = $values['data'];
        $terms = get_the_terms($_product->id, 'product_cat');
        foreach ($terms as $term) {
            $_categoryid = $term->term_id;
            if ($_categoryid == 52) {
                //category is in cart!
                $product_in_cart = true;
            }
        }
    }

    if ($product_in_cart == true) {
        wp_enqueue_script('jquery-inputmask', get_stylesheet_directory_uri() . '/js/inputmask/jquery.inputmask.bundle.min.js');
        echo '<script>jQuery(document).ready(function(i){i("#billing_phone").inputmask({mask:"79999999999"}),i("#billing_email").inputmask({alias:"email"}), i("#date_activ_field label").html("<p style=\'margin-bottom: 0;line-height: 17px\'><span style=\'font-weight: 500;\'>Желаемая дата активации (для сим-карт Orange). <abbr class=\'required\' title=\'обязательно\'>*</abbr></span><br><span style=\'font-size: 14px;font-weight: 400;color: #000\'>Активация сим-карты производится в будние дни. В праздничные и выходные дни сим-карты не активируются.</span></p>")});</script>';
        echo '<p style="box-shadow: 0 1px 1px 0 rgba(0,0,0,0.05), 0 1px 3px 0 rgba(0,0,0,0.25);border-width: 1px 1px;background: #ffef74;padding: 20px;border-left: .618em solid rgba(0,0,0,.15);">Для выбора способа доставки или пункта самовывоза заполните все обязательные поля, отмеченные звездочкой <span style="color: #F60000;font-size: 18px;">*</span></p>';
    } else{
        wp_enqueue_script('jquery-inputmask', get_stylesheet_directory_uri() . '/js/inputmask/jquery.inputmask.bundle.min.js');
        echo '<script>jQuery(document).ready(function(i){i("#billing_phone").inputmask({mask:"79999999999"}),i("#billing_email").inputmask({alias:"email"}),i("#orange_replenishment").inputmask({mask:"699999999"}),i("#pin_code_recovery").inputmask({mask:"699999999"}),i("#vodafone_replenishment").inputmask({mask:"3499999999"})});</script>';
    }
}

add_action('woocommerce_review_order_before_payment', 'action_woocommerce_checkout_before_order_review', 10, 0);
function action_woocommerce_checkout_before_order_review()
{
    echo '<h3 style="background: #f8f8f8;padding: 7px 0 7px 0;text-align: center;">Выберите способ оплаты</h3>';
}

add_filter('comment_form_default_fields', 'crunchify_disable_comment_url');
function crunchify_disable_comment_url($fields)
{
    unset($fields['url']);
    return $fields;
}

/**
 * Add new section to WP profile and create custom fields
 */
function affwp_custom_extra_profile_fields($user)
{
    if (is_object($user)) {
        $actual_address = esc_attr(get_the_author_meta('actual_address', $user->ID));
        $billing_partner = esc_attr(get_the_author_meta('billing_partner', $user->ID));
        $promocod_partner = esc_attr(get_the_author_meta('promocod_partner', $user->ID));
    } else {
        $actual_address = null;
        $billing_partner = null;
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

add_action('show_user_profile', 'affwp_custom_extra_profile_fields');
add_action('edit_user_profile', 'affwp_custom_extra_profile_fields');
add_action("user_new_form", 'affwp_custom_extra_profile_fields');

/**
 * Save the fields when the values are changed on the profile page
 */
function affwp_custom_save_extra_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id))
        return false;
    update_user_meta($user_id, 'billing_partner', $_POST['billing_partner']);
    update_user_meta($user_id, 'actual_address', $_POST['actual_address']);
    update_user_meta($user_id, 'promocod_partner', $_POST['promocod_partner']);

}

add_action('user_register', 'affwp_custom_save_extra_profile_fields');
add_action('profile_update', 'affwp_custom_save_extra_profile_fields');
add_action('personal_options_update', 'affwp_custom_save_extra_profile_fields');
add_action('edit_user_profile_update', 'affwp_custom_save_extra_profile_fields');


/*************************Описание точки выдачи при оформлении заказа*****************************/
/*add_action('woocommerce_review_order_before_local_pickup_location', 'local_pickup_instructions');
function local_pickup_instructions()
{
    ?>
    <p style="font-weight: 500;padding: 7px;margin-bottom: 0;">Выберите пункт самовывоза из выпадающего списка</p>
    <?php
}*/

add_action('woocommerce_checkout_process', 'wdm_validate_custom_field', 10, 1);
function wdm_validate_custom_field($args)
{
    global $wpdb;
    //echo 'test11';
    if (isset($_POST['orange_replenishment'])) {
        $o_id = $_POST['orange_replenishment'];
        $track_o = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_orange_numbers WHERE numbers = %d", $o_id));

        if (empty($track_o->numbers))
            wc_add_notice('Данный номер Orange не принадлежит компании Евророуминг или номер введен некорректно', 'error');
    }



    if (isset($_POST['pin_code_recovery'])) {
        $o_id = $_POST['pin_code_recovery'];
        $track_o = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_orange_numbers WHERE numbers = %d", $o_id));

        if (empty($track_o->numbers))
            wc_add_notice('Данный номер Orange не принадлежит компании Евророуминг или номер введен некорректно', 'error');
    }


    if (isset($_POST['vodafone_replenishment'])) {
        $v_id = $_POST['vodafone_replenishment'];
        $track_v = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_vodafone_numbers WHERE numbers = $v_id"));

        if (empty($track_v->numbers))
            wc_add_notice('Номер ' . $v_id . ' Vodafone не принадлежит компании Евророуминг или номер введен некорректно', 'error');
    }
}

add_filter('woocommerce_default_address_fields', 'bbloomer_override_postcode_validation');
function bbloomer_override_postcode_validation($address_fields)
{
    $address_fields['postcode']['required'] = false;
    $address_fields['postcode']['label'] = 'Почтовый индекс (для почты РФ)';
    $address_fields['city']['label'] = 'Город (населенный пункт)';

    return $address_fields;
}


add_action('pre_user_query', 'tgm_order_users_by_date_registered');
function tgm_order_users_by_date_registered($query)
{
    global $pagenow;
    if (!is_admin() || 'users.php' !== $pagenow) {
        return;
    }
    $query->query_orderby = 'ORDER BY user_registered DESC';
}

/*add_filter( 'option_active_plugins', 'lg_disable_plugin' );
function lg_disable_plugin($plugins){

    $plugins_not_needed = array();

    if( $_SERVER['REQUEST_URI'] ==  '/checkout/') {
        $key = array_search( 'wp-store-locator/wp-store-locator.php' , $plugins );
        if ( false !== $key ) {
            unset( $plugins[$key] );
        }
    }

    return $plugins;
}*/

/****************************Store Locator Custom Templates and Custom Fields***************************/

add_filter('wpsl_templates', 'custom_templates');
function custom_templates($templates)
{

    /**
     * The 'id' is for internal use and must be unique ( since 2.0 ).
     * The 'name' is used in the template dropdown on the settings page.
     * The 'path' points to the location of the custom template,
     * in this case the folder of your active theme.
     */
    $templates[] = array(
        'id' => 'custom',
        'name' => 'Custom template',
        'path' => get_stylesheet_directory() . '/' . 'wpsl-templates/custom.php',
    );

    return $templates;
}

add_action('admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style');
function wpdocs_enqueue_custom_admin_style()
{
    $role = 'cashier';
    $user = wp_get_current_user();

    if (in_array($role, (array)$user->roles)) {

        wp_register_style('cashier_admin_style', get_stylesheet_directory_uri() . '/css/cashier-admin-style.css', false, '1.0.0');
        wp_enqueue_style('cashier_admin_style');

    }

}

add_action('admin_init', 'redirect_so_15396771');
function redirect_so_15396771()
{
    $role = 'cashier';
    $user = wp_get_current_user();

    if ($_SERVER['REQUEST_URI'] == '/wp-admin/' && in_array($role, (array)$user->roles)) {
        wp_redirect('/wp-admin/admin.php?page=wc_pos_registers', 301);
        exit;
    }

    if ($_SERVER['REQUEST_URI'] == '/wp-admin/index.php' && in_array($role, (array)$user->roles)) {
        wp_redirect('/wp-admin/admin.php?page=wc_pos_registers', 301);
        exit;
    }
}

add_filter('comment_post', 'comment_notification');

function comment_notification($comment_ID, $comment_approved)
{

    // Send email only when it's not approved
    if ($comment_approved == 0) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $comment = get_comment($comment_ID);

        $subject = "Проверьте комментарий к статье: " . get_the_title($comment->comment_post_ID);
        $message = 'Ссылка на статью: ' . get_permalink($comment->comment_post_ID);
        $message .= '<br>';
        $message .= 'Текст комментария:';
        $message .= '<br>';
        $message .= get_comment_text($comment_ID, array());

        wp_mail('o.koshkina@euroroaming.ru', $subject, $message, $headers);
        wp_mail('m.prohorova@euroroaming.ru', $subject, $message, $headers);
        wp_mail('e.simakova@euroroaming.ru', $subject, $message, $headers);
    }
}

/*function storefront_child_remove_phone($fields) {
    unset( $fields ['billing_phone'] );
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'storefront_child_remove_phone', 10 );*/

/*function storefront_child_remove_unwanted_form_fields($fields) {
    unset( $fields ['company'] );
    unset( $fields ['address_1'] );
    return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'storefront_child_remove_unwanted_form_fields', 10 );*/
