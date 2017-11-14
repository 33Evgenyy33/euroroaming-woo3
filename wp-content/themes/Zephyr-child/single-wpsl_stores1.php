<?php
/**
 * Example of a single WPSL store template for the Twenty Fifteen theme.
 *
 * @package Twenty_Fifteen
 */

$us_layout = US_Layout::instance();
get_header();

$post_type = get_post_type();
if ( $post_type == 'post' ) {
	$template_vars = array(
		'title' => us_get_option( 'titlebar_post_title', 'Blog' ),
	);
	us_load_template( 'templates/titlebar', $template_vars );
} elseif ( in_array( $post_type, us_get_option( 'custom_post_types_support', array() ) ) ) {
	us_load_template( 'templates/titlebar' );
	$default_post_sidebar_id = us_get_option( 'sidebar_' . $post_type . '_id', 'default_sidebar' );
}

$template_vars = array(
	'metas' => (array) us_get_option( 'post_meta', array() ),
	'show_tags' => in_array( 'tags', us_get_option( 'post_meta', array() ) ),
);

if ( ! isset( $default_post_sidebar_id ) ) {
	$default_post_sidebar_id = us_get_option( 'post_sidebar_id', 'default_sidebar' );
}

?>
	<div class="l-main">
		<div class="l-main-h i-cf">

			<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>

				<?php do_action( 'us_before_single' ) ?>

				<?php
				while ( have_posts() ) {
					the_post();

					us_load_template( 'templates/blog/single-post', $template_vars );
				}
				?>

				<?php do_action( 'us_after_single' ) ?>

			</main>

			<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
				<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_post_sidebar_id ); ?>"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPSideBar"' : ''; ?>>
					<?php us_dynamic_sidebar( $default_post_sidebar_id ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>

<?php get_footer(); ?>

//get_header(); ?>
<!---->
<!--	<div id="primary" class="content-area">-->
<!--		<main id="main" class="site-main" role="main">-->
<!--			<article id="post---><?php //the_ID(); ?><!--" --><?php //post_class(); ?><!-->-->
<!--				<header class="entry-header">-->
<!--					<h1 class="entry-title">--><?php //single_post_title(); ?><!--</h1>-->
<!--				</header>-->
<!--				<div class="entry-content">-->
<!--					--><?php
//					global $post;
//					$queried_object = get_queried_object();
//
//					// Add the map shortcode
//					echo do_shortcode( '[wpsl_map]' );
//					echo do_shortcode( '[wpsl_simcards]' );
//					// Add the content
//					$post = get_post( $queried_object->ID );
//					setup_postdata( $post );
//					the_content();
//					wp_reset_postdata( $post );
//
//					// Add the address shortcode
//					echo do_shortcode( '[wpsl_address]' );
//					?>
<!--				</div>-->
<!--			</article>-->
<!--		</main><!-- #main -->-->
<!--	</div><!-- #primary -->-->
<!---->
<?php //get_footer(); ?>