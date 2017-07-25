<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output one post from blog listing.
 *
 * (!) Should be called in WP_Query fetching loop only.
 * @link   https://codex.wordpress.org/Class_Reference/WP_Query#Standard_Loop
 *
 * @var $layout         string Blog layout: classic / flat / tiles / cards / smallcircle / smallsquare / compact / related / latest
 * @var $type        	string layout type: 'grid' / 'masonry' / 'carousel'
 * @var $metas          array Meta data that should be shown: array('date', 'author', 'categories', 'tags', 'comments')
 * @var $title_size     string Posts Title Size
 * @var $columns        int Number of columns 1 / 2 / 3 / 4
 * @var $content_type   string Content type: 'excerpt' / 'content' / 'none'
 * @var $show_read_more boolean
 *
 * @action Before the template: 'us_before_template:templates/blog/listing-post'
 * @action After the template: 'us_after_template:templates/blog/listing-post'
 * @filter Template variables: 'us_template_vars:templates/blog/listing-post'
 */

global $us_blog_img_ratio;

// Retreiving post format
$post_format = get_post_format() ? get_post_format() : 'standard';

// Determining default thumbnail size
$thumbnail_sizes = array(
	'classic' => 'tnail-1x1',
	'flat' => 'tnail-1x1',
	'cards' => 'tnail-1x1',
	'tiles' => 'tnail-1x1',
	'related' => 'tnail-1x1-small',
	'smallcircle' => 'tnail-1x1-small',
	'smallsquare' => 'tnail-1x1-small',
	'compact' => FALSE,
	'latest' => FALSE,
);
$has_preview = ( ! isset( $thumbnail_sizes[$layout] ) OR $thumbnail_sizes[$layout] !== FALSE );

$the_content = get_the_content();

global $blog_listing_slider_size;
$blog_listing_slider_size = 'tnail-1x1';

$featured_image = '';
$featured_html = '';
if ( $has_preview AND ! post_password_required() ) {
	$thumbnail_size = isset( $thumbnail_sizes[$layout] ) ? $thumbnail_sizes [$layout] : 'tnail-1x1';
	if ( $type == 'masonry' AND ! in_array( $layout, array( 'smallcircle', 'smallsquare' ) ) ) {
		$thumbnail_size = 'medium_large';
		$blog_listing_slider_size = 'medium_large';
	}
	if ( $columns == 1 AND $layout == 'classic' ) {
		$thumbnail_size = 'large';
		$blog_listing_slider_size = 'large';
	}
	if ( isset( $img_size ) AND $img_size != 'default' ) {
		$thumbnail_size = $img_size;
		$blog_listing_slider_size = $img_size;
	}

	global $_wp_additional_image_sizes;
	if ( isset( $_wp_additional_image_sizes[$thumbnail_size] )
		AND $_wp_additional_image_sizes[$thumbnail_size]['width'] != 0
		AND $_wp_additional_image_sizes[$thumbnail_size]['height'] != 0 ) {
		$us_blog_img_ratio = number_format( $_wp_additional_image_sizes[$thumbnail_size]['height'] / $_wp_additional_image_sizes[$thumbnail_size]['width'] * 100, 4 );
	}
	$featured_image = has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), $thumbnail_size ) : '';
	if ( $featured_image == '' ) {
		// We fetch previews for images at any layout and for any post formats at classic / flat layouts
		if ( $post_format == 'image' ) {
			$featured_image = us_get_post_preview( $the_content, TRUE );
		} elseif ( $post_format == 'gallery' ) {
			if ( preg_match( '~\[us_gallery.+?\]|\[us_image_slider.+?\]|\[gallery.+?\]~', $the_content, $matches ) ) {
				$gallery = preg_replace( '~(vc_gallery|us_gallery|gallery)~', 'us_image_slider', $matches[0] );
				preg_match( '~\[us_image_slider(.+?)\]~', $gallery, $matches2 );
				$shortcode_atts = shortcode_parse_atts( $matches2[1] );
				if ( ! empty( $shortcode_atts['ids'] ) ) {
					$ids = explode( ',', $shortcode_atts['ids'] );
					if ( count( $ids ) > 0 ) {
						$featured_image = wp_get_attachment_image( $ids[0], $thumbnail_size );
						$featured_html = '';
					}
				}
			}
		}
	}

	if ( in_array( $layout, array( 'classic', 'flat' ) ) || ( $layout == 'cards' AND $post_format == 'gallery' ) ) {
		$featured_html = us_get_post_preview( $the_content, TRUE );
	}

	if ( $type == 'carousel' AND $post_format == 'gallery' ) {
		$featured_html = '';
	}
}

// We need some special markup for quotes
$use_special_quote_markup = ( $post_format == 'quote' AND ! in_array( $layout, array( 'compact', 'related' ) ) );

if ( $use_special_quote_markup ) {
	// Always display content for normal quotes
	$content_type = 'content';
}

if ( $content_type == 'content' ) {
	$the_content = apply_filters( 'the_content', $the_content );
} elseif ( $content_type == 'none' ) {
	$the_content = '';
} else/*if ( $content_type == 'excerpt' )*/ {
	$the_content = apply_filters( 'the_excerpt', get_the_excerpt() );
}


// Meta => certain html in a proper order
$meta_html = array_fill_keys( $metas, '' );

// Preparing post metas separately because we might want to order them inside the .w-blog-post-meta in future
$meta_html['date'] = '<time class="w-blog-post-meta-date date updated';
if ( ! in_array( 'date', $metas ) ) {
	// Hiding from users but not from search engines
	$meta_html['date'] .= ' hidden';
}
$meta_html['date'] .= '" datetime="' . get_the_date( 'Y-m-d H:i:s' ) . '">';
if ( $layout == 'latest' ) {
	// Special date format for latest posts
	$meta_html['date'] .= '<span class="w-blog-post-meta-date-month">' . get_the_date( 'M' ) . '</span>';
	$meta_html['date'] .= '<span class="w-blog-post-meta-date-day">' . get_the_date( 'd' ) . '</span>';
	$meta_html['date'] .= '<span class="w-blog-post-meta-date-year">' . get_the_date( 'Y' ) . '</span>';
} else {
	$meta_html['date'] .= get_the_date();
}
$meta_html['date'] .= '</time>';

$meta_html['author'] = '<span class="w-blog-post-meta-author vcard author';
if ( ! in_array( 'author', $metas ) ) {
	$meta_html['author'] .= ' hidden';
}
$meta_html['author'] .= '">';
$meta_html['author'] .= '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ) . '" class="fn">' . get_the_author() . '</a>';
$meta_html['author'] .= '</span>';

if ( in_array( 'categories', $metas ) ) {
	$meta_html['categories'] = get_the_category_list( ', ' );
	if ( ! empty( $meta_html['categories'] ) ) {
		$meta_html['categories'] = '<span class="w-blog-post-meta-category">' . $meta_html['categories'] . '</span>';
	}
}

if ( in_array( 'tags', $metas ) ) {
	$meta_html['tags'] = get_the_tag_list( '', ', ', '' );
	if ( ! empty( $meta_html['tags'] ) ) {
		$meta_html['tags'] = '<span class="w-blog-post-meta-tags">' . $meta_html['tags'] . '</span>';
	}
}

$comments_number = get_comments_number();
if ( in_array( 'comments', $metas ) AND ! ( $comments_number == 0 AND ! comments_open() ) ) {
	$meta_html['comments'] = '<span class="w-blog-post-meta-comments">';
	// TODO Replace with get_comments_popup_link() when https://core.trac.wordpress.org/ticket/17763 is resolved
	ob_start();
	$comments_label = sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $comments_number ), $comments_number );
	comments_popup_link( us_translate( 'No Comments' ), $comments_label, $comments_label );
	$meta_html['comments'] .= ob_get_clean();
	$meta_html['comments'] .= '</span>';
}

$meta_html = apply_filters( 'us_listing_post_meta_html', $meta_html, get_the_ID() );

$post_classes = 'w-blog-post';
if ( in_array( $post_format, array( 'gallery', 'image' ) ) AND $featured_image != '' ) {
	$post_classes .= ' has-post-thumbnail';
}

$categories = get_the_terms( get_the_ID(), 'category' );
$categories_slugs = array();
if ( ! is_array( $categories ) ) {
	$categories = array();
}
foreach ( $categories as $category ) {
	$post_classes .= ' ' . $category->slug;
	$categories_slugs[] = $category->slug;
}
$anchor_atts = '';
// If portfolio page with custom link is shown on search results
if ( get_post_type() == 'us_portfolio' AND ( usof_meta( 'us_tile_link' ) != '' AND $link_arr = json_decode( usof_meta( 'us_tile_link' ), TRUE ) AND $link_arr['url'] != '' ) ) {
	$link = $link_arr['url'];
	if ( $link_arr['target'] == '_blank' ) {
		$anchor_atts = ' target="_blank"';
	}
} else {
	$link = esc_url( apply_filters( 'the_permalink', get_permalink() ) );
	if ( $post_format == 'link' ) {
		$anchor_atts = ' target="_blank"';
	}
}
$read_more_btn_size = '';
if ( us_get_option( 'read_more_btn_size' ) != '' ) {
	$read_more_btn_size = ' style="font-size:' . us_get_option( 'read_more_btn_size' ) . ';"';
}

if ( ! empty( $us_blog_img_ratio ) ) {
	$preview_icon_style = ' style="padding-bottom:' . $us_blog_img_ratio . '%;"';
} else {
	$preview_icon_style = '';
}

if ( ! $use_special_quote_markup ): ?>

	<article <?php post_class( $post_classes ) ?> data-id="<?php the_ID() ?>" data-categories="<?php echo implode( ',', $categories_slugs ) ?>">
		<div class="w-blog-post-h">
			<?php if ( $has_preview AND ! empty( $featured_html ) ): ?>
				<div class="w-blog-post-preview">
					<?php echo $featured_html ?>
					<span class="w-blog-post-preview-icon"<?php echo $preview_icon_style; ?>></span>
				</div>
			<?php endif/*( ! empty( $featured_html ) )*/
			; ?>
			<?php if ( $has_preview AND empty( $featured_html ) ): ?>
				<a href="<?php echo $link; ?>"<?php echo $anchor_atts ?>>
					<div class="w-blog-post-preview">
						<?php echo $featured_image; ?>
						<span class="w-blog-post-preview-icon"<?php echo $preview_icon_style; ?>></span>
					</div>
				</a>
			<?php endif/*( empty( $featured_html ) )*/
			; ?>
			<div class="w-blog-post-body">
				<h2 class="w-blog-post-title"<?php if ( $title_size != '' ) {
					echo 'style="font-size:' . $title_size . '"';
				} ?>>
					<a class="entry-title" rel="bookmark" href="<?php echo $link; ?>"<?php echo $anchor_atts ?>><?php the_title(); ?></a>
				</h2>

				<div class="w-blog-post-meta<?php echo empty( $metas ) ? ' hidden' : '' ?>">
					<?php echo implode( '', $meta_html ) ?>
				</div>
				<?php if ( ! empty( $the_content ) ): ?>
					<div class="w-blog-post-content">
						<?php echo $the_content ?>
					</div>
				<?php endif/*( ! empty( $the_content ) )*/
				; ?>
				<?php if ( $show_read_more ): ?>
					<a class="w-blog-post-more w-btn color_<?php echo us_get_option( 'read_more_btn_color' ) ?> style_<?php echo us_get_option( 'read_more_btn_style' ) ?>" href="<?php echo $link; ?>"<?php echo $anchor_atts . $read_more_btn_size ?>>
						<span class="w-btn-label"><?php _e( 'Read More', 'us' ) ?></span>
					</a>
				<?php endif/*( $show_read_more )*/
				; ?>
			</div>
		</div>
	</article>

<?php else/*if ( $use_special_quote_markup )*/
	: ?>

	<article <?php post_class( $post_classes ) ?> data-id="<?php the_ID() ?>" data-categories="<?php echo implode( ',', $categories_slugs ) ?>">
		<div class="w-blog-post-h">
			<div class="w-blog-post-preview">
				<?php echo $featured_image ?>
				<span class="w-blog-post-preview-icon"<?php echo $preview_icon_style; ?>></span>
			</div>
			<div class="w-blog-post-body">
				<blockquote>
					<?php echo $the_content ?>
					<cite class="entry-title"><?php the_title() ?></cite>
				</blockquote>
			</div>
		</div>
	</article>

<?php endif/*( $use_special_quote_markup )*/
; ?>
