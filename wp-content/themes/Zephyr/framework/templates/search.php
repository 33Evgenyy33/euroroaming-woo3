<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying search results pages
 */

$us_layout = US_Layout::instance();

get_header();

// Creating .l-titlebar
us_load_template(
	'templates/titlebar', array(
	'title' => sprintf( us_translate( 'Search results for &#8220;%s&#8221;' ), esc_attr( get_search_query() ) ),
)
);

$template_vars = array(
	'layout' => us_get_option( 'search_layout', 'compact' ),
	'type' => us_get_option( 'search_type', 'grid' ),
	'columns' => us_get_option( 'search_cols', 1 ),
	'img_size' => us_get_option( 'search_img_size', 'default' ),
	'metas' => (array) us_get_option( 'search_meta', array() ),
	'content_type' => us_get_option( 'search_content_type', 'excerpt' ),
	'show_read_more' => in_array( 'read_more', (array) us_get_option( 'search_meta', array() ) ),
	'pagination' => us_get_option( 'search_pagination', 'regular' ),
);

$default_search_sidebar_id = us_get_option( 'search_sidebar_id', 'default_sidebar' );
?>
	<div class="l-main">
		<div class="l-main-h i-cf">

			<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
				<section class="l-section">
					<div class="l-section-h i-cf">

						<?php do_action( 'us_before_search' ) ?>

						<?php us_load_template( 'templates/blog/listing', $template_vars ) ?>

						<?php do_action( 'us_after_search' ) ?>

					</div>
				</section>
			</main>

			<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
				<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_search_sidebar_id ); ?>"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPSideBar"' : ''; ?>>
					<?php us_dynamic_sidebar( $default_search_sidebar_id ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>


<?php
get_footer();
