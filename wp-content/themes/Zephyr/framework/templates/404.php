<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying the 404 page
 */

$page_404 = get_page_by_path( 'error-404' );

$us_layout = US_Layout::instance();

if ( $page_404 ) {
	get_header();
	$titlebar_vars = array(
		'title' => apply_filters( 'the_title', $page_404->post_title ),
	);
	us_load_template( 'templates/titlebar', $titlebar_vars );
?>
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>

			<?php
			do_action( 'us_before_page' );

			$the_content = apply_filters( 'the_content', $page_404->post_content );

			// The page may be paginated itself via <!--nextpage--> tags
			$pagination = us_wp_link_pages(
				array(
					'before' => '<div class="w-blog-pagination"><nav class="navigation pagination">',
					'after' => '</nav></div>',
					'next_or_number' => 'next_and_number',
					'nextpagelink' => '>',
					'previouspagelink' => '<',
					'link_before' => '<span>',
					'link_after' => '</span>',
					'echo' => 0,
				)
			);

			// If content has no sections, we'll create them manually
			$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
			if ( ! $has_own_sections ) {
				$the_content = '<section class="l-section"><div class="l-section-h i-cf">' . $the_content . $pagination . '</div></section>';
			} elseif ( ! empty( $pagination ) ) {
				$the_content .= '<section class="l-section"><div class="l-section-h i-cf">' . $pagination . '</div></section>';
			}

			echo $the_content;

			do_action( 'us_after_page' );
			?>

		</main>

		<?php
		if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ) { ?>
			<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos; ?>"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPSideBar"' : ''; ?>>
				<?php
				if ( usof_meta( 'us_sidebar', array(), $page_404->ID ) == 'custom' ) {
					$sidebar_id = usof_meta( 'us_sidebar_id', array(), $page_404->ID );
					if ( ! empty( $sidebar_id ) ) {
						dynamic_sidebar( $sidebar_id );
					}
				} else {
					$sidebar_id = us_get_option( 'page_sidebar_id', 'default_sidebar' );
					dynamic_sidebar( $sidebar_id );
				}
				?>
			</aside>
		<?php } ?>

	</div>
</div>
<?php
} else {
	$us_layout->sidebar_pos = 'none';
	get_header();
?>
<div class="l-main">
	<div class="l-main-h i-cf">

		<div class="l-content">

			<section class="l-section">
				<div class="l-section-h i-cf">

					<?php do_action( 'us_before_404' ) ?>

					<div class="page-404">

						<?php

						$the_content = '<h1>' . us_translate( 'Page not found' ) . '</h1><p>' . __( 'The link you followed may be broken, or the page may have been removed.', 'us' ) . '</p>';
						echo apply_filters( 'us_404_content', $the_content );

						?>

					</div>

					<?php do_action( 'us_after_404' ) ?>

				</div>
			</section>

		</div>

	</div>
</div>
<?php
}
get_footer();
?>
