<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Socials
 *
 * Class US_Widget_Socials
 */
class US_Widget_Socials extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}
		$socials_inline_css = '';
		if ( ! empty( $instance['size'] ) ) {
			$socials_inline_css = ' style="font-size: ' . $instance['size'] . ';"';
		}

		$style_translate = array(
			'solid_square' => 'solid',
			'outlined_square' => 'outlined',
			'solid_circle' => 'solid circle',
			'outlined_circle' => 'outlined circle',
		);

		if ( ! empty( $instance['style'] ) AND array_key_exists( $instance['style'], $style_translate ) ) {
			$instance['style'] = $style_translate[$instance['style']];
		}

		$style_class = ( ! empty( $instance['style'] ) ) ? ' style_' . $instance['style'] : '';

		$output .= '<div class="w-socials align_left ' . $style_class . ' color_' . $instance['color'] . '"' . $socials_inline_css . '>';
		$output .= '<div class="w-socials-list">';

		if ( isset( $this->config['params'] ) AND is_array( $this->config['params'] ) ) {
			foreach ( $this->config['params'] as $param_name => $param ) {
				if ( in_array(
					$param_name, array(
					'title',
					'size',
					'style',
					'color',
					'custom_link',
					'custom_title',
					'custom_icon',
					'custom_color',
				)
				) ) {
					// Not all the params are social keys
					continue;
				}
				if ( empty( $instance[$param_name] ) ) {
					continue;
				}
				$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param_name;
				$value = $instance[$param_name];
				$link_target = ' target="_blank"';
				if ( $param_name == 'email' ) {
					$link_target = '';
					if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
						$value = 'mailto:' . $value;
					}
				} elseif ( $param_name == 'skype' ) {
					// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
					if ( strpos( $value, ':' ) === FALSE ) {
						$value = 'skype:' . esc_attr( $value );
					}
				} else {
					$value = esc_url( $value );
				}
				$output .= '<div class="w-socials-item ' . $param_name . '">';
				$output .= '<a class="w-socials-item-link"' . $link_target . ' href="' . $value . '" aria-label="' . $param['heading'] . '">';
				$output .= '<span class="w-socials-item-link-hover"></span>';
				$output .= '</a>';
				$output .= '<div class="w-socials-item-popup"><span>' . $param['heading'] . '</span></div>';
				$output .= '</div>';
			}
		}

		if ( ( ! empty( $instance['custom_link'] ) ) AND ( ! empty( $instance['custom_icon'] ) ) ) {
			$link_style = $hover_style = '';
			if ( ! empty( $instance['custom_color'] ) ) {
				if ( ! empty( $instance['color'] ) AND $instance['color'] == 'brand' ) {
					$link_style = ' style="color: ' . $instance['custom_color'] . ';"';
				}
				$hover_style = ' style="background-color: ' . $instance['custom_color'] . ';"';
			}

			$output .= '<div class="w-socials-item custom">';
			$output .= '<a class="w-socials-item-link" target="_blank" href="' . esc_attr( $instance['custom_link'] ) . '" aria-label="' . $instance['custom_title'] . '"' . $link_style . '>';
			$output .= '<span class="w-socials-item-link-hover"' . $hover_style . '></span>';
			$output .= us_prepare_icon_tag( $instance['custom_icon'] );
			$output .= '</a>';
			if ( ! empty( $instance['custom_title'] ) ) {
				$output .= '<div class="w-socials-item-popup"><span>' . $instance['custom_title'] . '</span></div>';
			}
			$output .= '</div>';
		}

		$output .= '</div></div>';

		$output .= $args['after_widget'];

		echo $output;
	}
}
