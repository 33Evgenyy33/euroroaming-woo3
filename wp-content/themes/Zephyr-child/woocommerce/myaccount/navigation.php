<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
    <ul>
		<?php if (members_current_user_has_role('blogger')) { ?>
            <li class="woocommerce-MyAccount-navigation-link--blogger">
                <a href="/affiliate-area">Статистика по промокодам</a>
            </li>
		<?php } ?>

		<?php if (members_current_user_has_role('cashier')) { ?>
            <li class="woocommerce-MyAccount-navigation-link--cashier">
                <a href="https://euroroaming.ru/wp-admin/">Кабинет продаж</a>
            </li>
		<?php } ?>

		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			if ($label == 'Загрузки') continue;
			?>
            <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
            </li>
		<?php endforeach; ?>

    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
