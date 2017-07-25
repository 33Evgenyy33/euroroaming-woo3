<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output menu element
 *
 * @var $hover_effect    string Hover Effect: 'simple' / 'underline'
 * @var $dropdown_effect string Dropdown Effect
 * @var $vstretch        boolean Stretch menu items vertically to fit the available height
 * @var $indents         int Items Indents
 * @var $mobile_width    int On which screen width menu becomes mobile
 * @var $mobile_behavior boolean Mobile behavior
 * @var $design_options  array
 * @var $id              string
 * @var $source          string WP Menu source
 */
if ( substr( $source, 0, 9 ) == 'location:' ) {
	$location = substr( $source, 9 );
	$theme_locations = get_nav_menu_locations();
	if ( isset( $theme_locations[$location] ) ) {
		$menu_obj = get_term( $theme_locations[$location], 'nav_menu' );
		if ( $menu_obj ) {
			$source = $menu_obj->slug;
		} else {
			return;
		}
	} else {
		return;
	}
} else {
	$location = NULL;
}

if ( empty( $source ) OR ! is_nav_menu( $source ) ) {
	return;
}

$classes = '';
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $design_options ) AND isset( $design_options['hide_for_not-sticky'] ) AND $design_options['hide_for_not-sticky'] ) {
	$classes .= ' hide-for-not-sticky';
}
$list_classes = ' level_1 hover_' . $hover_effect;
$classes .= ' type_desktop dropdown_' . $dropdown_effect;
if ( $vstretch ) {
	$classes .= ' height_full';
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}
$list_classes .= ' hide_for_mobiles';

echo '<nav class="w-nav' . $classes . '" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">';
echo '<a class="w-nav-control" href="javascript:void(0);">';
echo '<div class="w-nav-icon"><i></i></div>';
echo '<span>' . us_translate( 'Menu' ) . '</span>';
echo '</a>';
echo '<ul class="w-nav-list' . $list_classes . '">';
if ( $location ) {
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container' => 'ul',
			'container_class' => 'w-nav-list',
			'walker' => new US_Walker_Nav_Menu,
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
} else {
	wp_nav_menu(
		array(
			'menu' => $source,
			'container' => 'ul',
			'container_class' => 'w-nav-list',
			'walker' => new US_Walker_Nav_Menu,
			'items_wrap' => '%3$s',
			'fallback_cb' => FALSE,
		)
	);
}

echo '</ul>';
echo '<div class="w-nav-options hidden"';
echo us_pass_data_to_js(
	array(
		'mobileWidth' => intval( $mobile_width ),
		'mobileBehavior' => intval( $mobile_behavior ),
	)
);
echo '></div>';
echo '</nav>';
