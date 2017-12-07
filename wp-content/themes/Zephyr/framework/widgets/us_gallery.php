<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Gallery
 *
 * Class US_Widget_Media_Gallery
 */

class US_Widget_Media_Gallery extends WP_Widget_Media_Gallery {

	public function get_instance_schema() {

		$schema = parent::get_instance_schema();

		$schema['indents'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'indents',
			'should_preview_update' => false,
		);

		$schema['masonry'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'masonry',
			'should_preview_update' => false,
		);

		$schema['meta'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'meta',
			'should_preview_update' => false,
		);

		$schema['size']['enum'] = array_merge( get_intermediate_image_sizes(), array( 'full', 'custom', 'default' ) );
		$schema['size']['default'] = 'default';

		return $schema;
	}

	public function render_media( $instance ){
		$instance = array_merge( wp_list_pluck( $this->get_instance_schema(), 'default' ), $instance );

		$shortcode_atts = array_merge(
			$instance,
			array(
				'link' => $instance['link_type'],
				'img_size' => $instance['size'],
			)
		);

		// @codeCoverageIgnoreStart
		if ( $instance['orderby_random'] ) {
			$shortcode_atts['orderby'] = 'rand';
		}

		if ( ! isset( $instance['size'] ) ) {
			$shortcode_atts['img_size'] = 'thumbnail';
		}

		if ( ! isset( $instance['link_type'] ) ) {
			$shortcode_atts['link_type'] = 'post';
		}

		if ( isset( $shortcode_atts['masonry'] ) AND $shortcode_atts['masonry'] ) {
			$shortcode_atts['masonry'] = 'true';
		}

		// @codeCoverageIgnoreEnd
		global $us_shortcodes;
		echo $us_shortcodes->us_gallery( $shortcode_atts );
	}

}
