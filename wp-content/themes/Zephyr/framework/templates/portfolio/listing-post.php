<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output one post from portfolio listing.
 *
 * (!) Should be called in WP_Query fetching loop only.
 * @link   https://codex.wordpress.org/Class_Reference/WP_Query#Standard_Loop
 *
 * @var $type       string layout type: 'grid' / 'masonry' / 'carousel'
 * @var $metas      array Meta data that should be shown: array('title', 'date', 'categories', 'desc')
 * @var $ratio      string Items ratio: '3x2' / '4x3' / '1x1' / '2x3' / '3x4' / '16x9'
 * @var $columns    int Columns number: 2 / 3 / 4 / 5
 * @var $is_widget  bool if used in widget
 * @var $title_size string Title Font Size
 * @var $meta_size  string Meta Font Size
 * @var $text_color string
 * @var $bg_color   string
 * @var $img_size   string
 *
 * @action Before the template: 'us_before_template:templates/portfolio/listing-post'
 * @action After the template: 'us_after_template:templates/portfolio/listing-post'
 * @filter Template variables: 'us_template_vars:templates/portfolio/listing-post'
 */

// portfolio tile additional variables
$classes = $anchor_atts = $title_inner_css = $meta_inner_css = $anchor_inner_css = '';

$tile_size = '1x1';
if ( usof_meta( 'us_tile_size' ) != '' ) {
	$tile_size = usof_meta( 'us_tile_size' );
}

// In case of any image issue using placeholder so admin could understand it quickly
// TODO Move placeholder URL to some config
global $us_template_directory_uri;
$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.jpg';

$tnail_id = get_post_thumbnail_id();
if ( $tnail_id ) {
	$image = wp_get_attachment_image_src( $tnail_id, $img_size );
	if ( $type != 'carousel' AND $tile_size != '1x1' AND $img_size != 'full' ) {
		$image = wp_get_attachment_image_src( $tnail_id, 'large' );
	}
	if ( $is_widget ) {
		$image = wp_get_attachment_image_src( $tnail_id, 'thumbnail' );
	}
}

if ( ! $tnail_id OR ( ! $image ) ) {
	$image = array( $placeholder_url, 600, 600 );
}
$item_title = get_the_title();
$image_html = '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . esc_attr( $item_title ) . '" />';

$categories = get_the_terms( get_the_ID(), 'us_portfolio_category' );
$categories_slugs = array();
if ( ! is_array( $categories ) ) {
	$categories = array();
}
foreach ( $categories as $category ) {
	$classes .= ' ' . $category->slug;
	$categories_slugs[] = $category->slug;
}

$link_arr = json_decode( usof_meta( 'us_tile_link' ), TRUE );

if ( $items_action == 'lightbox_image' ) {
	$link = $tnail_id ? wp_get_attachment_image_src( $tnail_id, 'full' ) : $placeholder_url;
	if ( $link ) {
		$link = $link[0];
		$anchor_atts .= ' ref="magnificPopupPortfolio" title="' . esc_attr( $item_title ) . '"';
	}
} elseif ( usof_meta( 'us_tile_link' ) != '' AND $link_arr = json_decode( usof_meta( 'us_tile_link' ), TRUE ) AND $link_arr['url'] != '' ) {
	$link = $link_arr['url'];
	if ( $link_arr['target'] == '_blank' ) {
		$anchor_atts .= ' target="_blank"';
	} elseif ( preg_match( "/\.(bmp|gif|jpeg|jpg|png)$/i", $link ) ) {
		$anchor_atts .= ' ref="magnificPopup"';
	}
	$classes .= ' custom-link';
} else {
	$link = esc_url( apply_filters( 'the_permalink', get_permalink() ) );
}

if ( $title_size != '' ) {
	$title_inner_css = ' style="font-size:' . $title_size . '"';
}

if ( $meta_size != '' ) {
	$meta_inner_css = ' style="font-size:' . $meta_size . '"';
}

$available_metas = array( 'title', 'date', 'categories', 'desc' );
$metas = ( isset( $metas ) AND is_array( $metas ) ) ? array_intersect( $metas, $available_metas ) : array( 'title' );
$meta_html = array_fill_keys( $metas, '' );
if ( in_array( 'title', $metas ) ) {
	$meta_html['title'] = '<h2 class="w-portfolio-item-title"' . $title_inner_css . '>' . get_the_title() . '</h2>';
}
if ( in_array( 'date', $metas ) ) {
	$meta_html['date'] = '<span class="w-portfolio-item-text"' . $meta_inner_css . '>' . get_the_date() . '</span>';
}
if ( in_array( 'categories', $metas ) AND count( $categories ) > 0 ) {
	$meta_html['categories'] = '<span class="w-portfolio-item-text"' . $meta_inner_css . '>';
	foreach ( $categories as $index => $category ) {
		$meta_html['categories'] .= ( ( $index > 0 ) ? ' / ' : '' ) . $category->name;
	}
	$meta_html['categories'] .= '</span>';
}
if ( in_array( 'desc', $metas ) ) {
	$meta_html['desc'] = '<span class="w-portfolio-item-text"' . $meta_inner_css . '>' . usof_meta( 'us_tile_description' ) . '</span>';
}
if ( ( ! $is_widget ) AND $type != 'carousel' ) {
	$classes .= ' size_' . $tile_size;
}
if ( $bg_color != '' ) {
	$anchor_inner_css .= 'background-color: ' . $bg_color . ';';
}
if ( usof_meta( 'us_tile_bg_color' ) != '' ) {
	$anchor_inner_css .= 'background-color: ' . usof_meta( 'us_tile_bg_color' ) . ';';
}
if ( $text_color != '' ) {
	$anchor_inner_css .= 'color: ' . $text_color . ';';
}
if ( usof_meta( 'us_tile_text_color' ) != '' ) {
	$anchor_inner_css .= 'color: ' . usof_meta( 'us_tile_text_color' ) . ';';
}
if ( $anchor_inner_css != '' ) {
	$anchor_inner_css = ' style="' . $anchor_inner_css . '"';
}

$classes = apply_filters( 'us_portfolio_listing_item_classes', $classes );

?>
<div class="w-portfolio-item<?php echo $classes ?>" data-id="<?php the_ID() ?>" data-categories="<?php echo implode( ',', $categories_slugs ) ?>">
	<a class="w-portfolio-item-anchor" href="<?php echo $link ?>"<?php echo $anchor_atts . $anchor_inner_css ?>>
		<?php do_action( 'us_before_portfolio_item_anchor_html' ); ?>
		<div class="w-portfolio-item-image"<?php if ( $type != 'masonry' ) { ?> style="background-image: url(<?php echo $image[0] ?>)"<?php } ?>>
			<?php echo $image_html ?>
		</div>
		<?php
		$image2_id = intval( usof_meta( 'us_tile_additional_image' ) );
		if ( $image2_id ) {
			$image2 = wp_get_attachment_image_src( $image2_id, $img_size );
			if ( $tile_size != '1x1' AND $img_size != 'full' ) {
				$image2 = wp_get_attachment_image_src( $tnail_id, 'large' );
			}
		}
		if ( $image2_id != '' AND is_array( $image2 ) ) {
			echo '<div class="w-portfolio-item-image second"';
			if ( $type != 'masonry' ) {
				echo ' style="background-image: url(' . $image2[0] . ')"';
			}
			echo '>';
			echo '<img src="' . $image2[0] . '" width="' . $image2[1] . '" height="' . $image2[2] . '" alt="' . esc_attr( $item_title ) . '" />';
			echo '</div>';
		}
		?>
		<?php if ( ! empty( $meta_html ) ): ?>
			<div class="w-portfolio-item-meta">
				<div class="w-portfolio-item-meta-h">
					<?php echo implode( '', $meta_html ) ?>
					<span class="w-portfolio-item-arrow"></span>
				</div>
			</div>
		<?php endif; ?>
		<?php do_action( 'us_after_portfolio_item_anchor_html' ); ?>
	</a>
</div>
