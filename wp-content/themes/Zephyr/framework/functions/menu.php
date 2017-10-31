<?php

class US_Walker_Nav_Menu extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		// depth dependent classes
		$level = ( $depth + 2 ); // because it counts the first submenu as 0

		// build html
		$output .= "\n" . '<ul class="w-nav-list level_' . $level . '">' . "\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= "</ul>\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$level = ( $depth + 1 ); // because it counts the first submenu as 0

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'w-nav-item';
		$classes[] = 'level_' . $level;
		$classes[] = 'menu-item-' . $item->ID;

		if ( ! empty( $item->mega_menu_cols ) ) {
			$classes[] = 'columns_' . $item->mega_menu_cols;
		}

		// Removing active classes for scroll links, so they could be handled by JavaScript instead
		if ( isset( $item->url ) AND strpos( $item->url, '#' ) !== FALSE ) {
			$classes = array_diff(
				$classes, array(
				'current-menu-item',
				'current-menu-ancestor',
				'current-menu-parent',
			)
			);
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= '<li' . $id . $class_names .'>';
		if ( $item->object == 'us_widget_area' ) {
			$item_post = get_post( $item->object_id );
			if ( $item_post AND is_active_sidebar( $item_post->post_name ) ) {
				ob_start();
				dynamic_sidebar( $item_post->post_name );
				$output .= ob_get_clean();
			}

		} else {
			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

			$item_output = $args->before;
			$item_output .= '<a class="w-nav-anchor level_' . $level . '" ' . $attributes . '>';
			$item_output .= $args->link_before . '<span class="w-nav-title">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span><span class="w-nav-arrow"></span>' . $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}


	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

class US_Walker_Simplenav_Menu extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '<div class="w-nav-list">';
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '</div>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		if ( ! empty( $id ) ) {
			$attributes .= ' id="' . esc_attr( $id ) . '"';
		}

		$item_output = $args->before;

		$classes_array = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes_array ), $item, $args ) );

		$item_output .= '<a class="w-menu-item ' . $classes . '" ' . $attributes . '><span>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</span></a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
	}
}

add_filter( 'wp_nav_menu_objects', 'us_mega_menu_wp_nav_menu_objects' );
function us_mega_menu_wp_nav_menu_objects( $sorted_menu_items ) {
	foreach ( $sorted_menu_items as $index => $item ) {
		// IF it is a first level item or if it is a fake last item
		if ( $item->menu_item_parent == 0 ) {
			$mega_menu_settings = get_post_meta( $item->ID, 'us_mega_menu_settings', TRUE );

			if ( is_array( $mega_menu_settings ) ) {
				if ( ! empty( $mega_menu_settings['columns'] ) AND intval( $mega_menu_settings['columns'] ) > 1 ) {
					$item->mega_menu_cols = intval( $mega_menu_settings['columns'] );
				}
			}

			$sorted_menu_items[$index] = $item;
		}
	}
	return $sorted_menu_items;
}

// Add fallback menu location, which can be used in some plugins
add_action( 'init', 'register_us_menu' );
function register_us_menu() {
	register_nav_menus(
		array(
			'us_main_menu' => __( 'Custom Menu', 'us' ),
		)
	);
}
