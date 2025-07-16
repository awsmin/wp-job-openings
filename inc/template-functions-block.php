<?php
/**
 * Template specific functions for block
 *
 * @package wp-job-openings
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'awsm_block_jobs_view_class' ) ) {
	function awsm_block_jobs_view_class( $class = '', $attributes = array() ) {
		$view_class = AWSM_Job_Openings_Block::get_job_listing_view_class_block( $attributes );

		// Merge custom class with view class if present
		if ( ! empty( $class ) ) {
			$view_class = trim( "$view_class $class" );
		}

		// Only return the class attribute if there's a class to output
		echo $view_class ? sprintf( 'class="%s"', esc_attr( $view_class ) ) : '';
	}
}


if ( ! function_exists( 'awsm_block_job_filters_explode' ) ) {
	function awsm_block_job_filters_explode( $filter_data ) {
		if ( is_array( $filter_data ) ) {
			return $filter_data;
		}

		if ( is_string( $filter_data ) && ! empty( $filter_data ) ) {
			return explode( ',', $filter_data );
		}

		return array();
	}
}


if ( ! function_exists( 'get_block_filtered_job_terms' ) ) {
	function get_block_filtered_job_terms( $attributes ) {
		/* $filter_suffix  = '_spec';
		$filters        = explode( ',', $attributes['filter_options'] );
		$filtered_terms = array();

		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$current_filter_key = str_replace( '-', '__', $filter ) . $filter_suffix;
				if ( isset( $_GET[ $current_filter_key ] ) ) {
					$term_slug = sanitize_title( $_GET[ $current_filter_key ] );
					$term      = get_term_by( 'slug', $term_slug, $filter );

					if ( $term && ! is_wp_error( $term ) ) {
						$filtered_terms[ $filter ] = $term;
					} else {
						$filtered_terms[ $filter ] = null;
					}
				}
			}
		}

		return $filtered_terms; */
		$filter_suffix  = '_spec';
		$filters        = $attributes['filter_options'];
		$filtered_terms = array();

		error_log( json_encode( 'enters get_block_filtered_job_terms', JSON_PRETTY_PRINT ) );

		if ( ! empty( $filters ) && is_array( $filters ) ) {
			foreach ( $filters as $filter ) {
				if ( ! empty( $filter['specKey'] ) ) {
					$taxonomy           = $filter['specKey'];
					$current_filter_key = str_replace( '-', '__', $taxonomy ) . $filter_suffix;

					if ( isset( $_GET[ $current_filter_key ] ) ) {
						$term_slug = sanitize_title( $_GET[ $current_filter_key ] );
						$term      = get_term_by( 'slug', $term_slug, $taxonomy );

						if ( $term && ! is_wp_error( $term ) ) {
							$filtered_terms[ $taxonomy ] = $term;
						} else {
							$filtered_terms[ $taxonomy ] = null;
						}
					}
				}
			}
		}

		return $filtered_terms;
	}
}

if ( ! function_exists( 'awsm_block_jobs_query' ) ) {
	function awsm_block_jobs_query( $attributes = array() ) {
		$query_args      = array();
		$is_term_or_slug = array();
		$filter_suffix   = '_spec';

		$filter_options_array = $attributes['filter_options'];

		//$filter_options_array = explode( ',', $attributes['filter_options'] );

		/* if ( is_string( $attributes['filter_options'] ) ) {
			$filter_options_array = explode( ',', $attributes['filter_options'] );
		} else {
			$filter_options_array = array(); // Or handle the case as per your requirement
		}
		*/
		/* if ( ! empty( $filter_options_array ) ) {
			foreach ( $filter_options_array as $filter ) {
				$current_filter_key = str_replace( '-', '__', $filter ) . $filter_suffix;
				if ( isset( $_GET[ $current_filter_key ] ) ) {
					$term_slug = sanitize_title( $_GET[ $current_filter_key ] );
					$term      = get_term_by( 'slug', $term_slug, $filter );
					if ( $term && ! is_wp_error( $term ) ) {
						$query_args[ $filter ]      = $term->term_id;
						$is_term_or_slug[ $filter ] = 'term_id';
					} else {
						$query_args[ $filter ]      = $term_slug;
						$is_term_or_slug[ $filter ] = 'slug';
					}
				}
			}
		} */

		if ( ! empty( $filter_options_array ) ) {
			foreach ( $filter_options_array as $filter ) {
				if ( ! empty( $filter['specKey'] ) ) {
					$taxonomy           = $filter['specKey'];
					$current_filter_key = str_replace( '-', '__', $taxonomy ) . $filter_suffix;

					if ( isset( $_GET[ $current_filter_key ] ) ) {
						$term_slug = sanitize_title( $_GET[ $current_filter_key ] );
						$term      = get_term_by( 'slug', $term_slug, $taxonomy );

						if ( $term && ! is_wp_error( $term ) ) {
							$query_args[ $taxonomy ]      = $term->term_id;
							$is_term_or_slug[ $taxonomy ] = 'term_id';
						} else {
							$query_args[ $taxonomy ]      = $term_slug;
							$is_term_or_slug[ $taxonomy ] = 'slug';
						}
					}
				}
			}
		}

		$args  = AWSM_Job_Openings_Block::awsm_block_job_query_args( $query_args, $attributes, $is_term_or_slug = array() );
		$query = new WP_Query( $args );
		return $query;
	}
}

if ( ! function_exists( 'awsm_block_jobs_data_attrs' ) ) {
	function awsm_block_jobs_data_attrs( $attrs = array(), $attributes = array() ) {
		$content = '';
		$attrs   = array_merge( AWSM_Job_Openings_Block::get_block_job_listing_data_attrs( $attributes ), $attrs );
		if ( ! empty( $attrs ) ) {
			foreach ( $attrs as $name => $value ) {
				if ( ! empty( $value ) ) {
					$content .= sprintf( ' data-%s="%s"', esc_attr( $name ), esc_attr( $value ) );
				}
			}
		}
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'awsm_block_jobs_load_more' ) ) {
	function awsm_block_jobs_load_more( $query, $shortcode_atts = array() ) {
		$loadmore      = isset( $shortcode_atts['block_loadmore'] ) && $shortcode_atts['block_loadmore'] === 'no' ? false : true;
		$max_num_pages = $query->max_num_pages;
		if ( $loadmore && $max_num_pages > 1 ) {
			if ( AWSM_Job_Openings::is_default_pagination( $shortcode_atts ) ) {
				$paged = ( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;
				if ( $paged < $max_num_pages ) {
					 $load_more_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-main"><a href="#" class="awsm-b-load-more awsm-b-load-more-btn" data-page="%2$s">%1$s</a></div>', esc_html__( 'Load more', 'wp-job-openings' ), esc_attr( $paged ) );
					/**
					 * Filters the load more content.
					 *
					 * @since 3.5.0
					 *
					 * @param string $load_more_content The HTML content.
					 * @param WP_Query $query The Query object.
					 * @param array $shortcode_atts Shortcode attributes.
					 */
					echo apply_filters( 'awsm_block_jobs_load_more_content', $load_more_content, $query, $shortcode_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {
				echo awsm_block_jobs_paginate_links( $query ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
}

if ( ! function_exists( 'awsm_block_jobs_paginate_links' ) ) {
	function awsm_block_jobs_paginate_links( $query, $shortcode_atts = array() ) {
		$current       = ( $query->query_vars['paged'] ) ? (int) $query->query_vars['paged'] : 1;
		$max_num_pages = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;

		$base_url = get_pagenum_link();
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			$base_url = $_POST['awsm_pagination_base'];
		}
		// phpcs:enable

		$args               = array(
			'base'    => esc_url_raw( add_query_arg( 'paged', '%#%', $base_url ) ),
			'format'  => '',
			'type'    => 'list',
			'current' => max( 1, $current ),
			'total'   => $max_num_pages,
		);
		$pagination_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-classic" data-effect-duration="slow">%s</div>', paginate_links( $args ) );
		/**
		 * Filters the paginate links content.
		 *
		 * @since 3.5.0
		 *
		 * @param string $pagination_content The HTML content.
		 * @param WP_Query $query The Query object.
		 * @param array $args Paginate links arguments.
		 * @param array $shortcode_atts Shortcode attributes.
		 */
		return apply_filters( 'awsm_block_jobs_paginate_links_content', $pagination_content, $query, $args, $shortcode_atts );
	}
}

if ( ! function_exists( 'awsm_jobs_block_featured_image' ) ) {
	function awsm_jobs_block_featured_image( $echo = true, $size = 'thumbnail', $attr = '', $block_atts = array() ) {
		$content                = '';
		$post_thumbnail_id      = get_post_thumbnail_id();
		$featured_image_support = get_option( 'awsm_jobs_enable_featured_image' );
		if ( $featured_image_support === 'enable' && $post_thumbnail_id ) {
			$content = wp_get_attachment_image( $post_thumbnail_id, $size, false, $attr );
		}
		/**
		 * Filters the featured image content.
		 *
		 * @since 3.5.0
		 *
		 * @param string $content The image content.
		 * @param int $post_thumbnail_id The post thumbnail ID.
		 */
		$content = apply_filters( 'awsm_jobs_block_featured_image_content', $content, $post_thumbnail_id, $block_atts );
		if ( ! empty( $content ) ) {
			$content = '<div class="awsm-job-featured-image">' . $content . '</div>';
		}
		if ( $echo ) {
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $content;
		}
	}
}

if ( ! function_exists( 'awsm_jobs_block_listing_item_class' ) ) {
	function awsm_jobs_block_listing_item_class( $class = array() ) {
		$job_id  = get_the_ID();
		$classes = array( 'awsm-b-job-listing-item' );
		if ( is_awsm_job_expired() ) {
			$classes[] = 'awsm-b-job-expired-item';
		}
		if ( ! empty( $class ) ) {
			$classes = array_merge( $classes, $class );
		}
		/**
		 * Filters the classes for each job listing item.
		 *
		 * @since 3.5.0
		 *
		 * @param array $classes Array of class names.
		 * @param int $job_id The Job ID.
		 */
		$classes = apply_filters( 'awsm_job_block_listing_item_class', $classes, $job_id );
		return sprintf( 'class="%s"', esc_attr( join( ' ', $classes ) ) );
	}
}

if ( ! function_exists( 'hz_get_sf_styles' ) ) {
	function hz_get_ui_styles( $attributes ) {
		$styles = array(
			'block_id'                         => ! empty( trim( $attributes['block_id'] ?? '' ) )
			? $attributes['block_id']
			: 'default-block-id',

			'border_width'                     => ! empty( $attributes['hz_sf_border_width'] ) && $attributes['hz_sf_border_width'] !== '0px'
			? $attributes['hz_sf_border_width']
			: '',

			'border_color'                     => ! empty( $attributes['hz_sf_border_color'] )
			? $attributes['hz_sf_border_color']
			: '#ccc',

			'sf_border_radius_topleft'         => ! empty( $attributes['hz_sf_border_radius']['topLeft'] )
			? $attributes['hz_sf_border_radius']['topLeft']
			: '5px',

			'sf_border_radius_topright'        => ! empty( $attributes['hz_sf_border_radius']['topRight'] )
			? $attributes['hz_sf_border_radius']['topRight']
			: '5px',

			'sf_border_radius_bottomright'     => ! empty( $attributes['hz_sf_border_radius']['bottomRight'] )
			? $attributes['hz_sf_border_radius']['bottomRight']
			: '5px',

			'sf_border_radius_bottomleft'      => ! empty( $attributes['hz_sf_border_radius']['bottomLeft'] )
			? $attributes['hz_sf_border_radius']['bottomLeft']
			: '5px',

			'padding_left'                     => ! empty( $attributes['hz_sf_padding']['left'] )
			? hz_append_unit_if_missing( $attributes['hz_sf_padding']['left'] )
			: '15px',

			'padding_right'                    => ! empty( $attributes['hz_sf_padding']['right'] )
			? hz_append_unit_if_missing( $attributes['hz_sf_padding']['right'] )
			: '15px',

			'padding_top'                      => ! empty( $attributes['hz_sf_padding']['top'] )
			? hz_append_unit_if_missing( $attributes['hz_sf_padding']['top'] )
			: '15px',

			'padding_bottom'                   => ! empty( $attributes['hz_sf_padding']['bottom'] )
			? hz_append_unit_if_missing( $attributes['hz_sf_padding']['bottom'] )
			: '15px',

			'sidebar_width'                    => ! empty( $attributes['hz_sidebar_width'] )
			? hz_append_unit_if_missing( $attributes['hz_sidebar_width'], '%' )
			: '33.333%',

			'border_width_field'               => ! empty( $attributes['hz_ls_border_width'] ) && $attributes['hz_ls_border_width'] !== '0px'
			? $attributes['hz_ls_border_width']
			: '1px',

			'border_color_field'               => ! empty( $attributes['hz_ls_border_color'] )
			? $attributes['hz_ls_border_color']
			: '#ccc',

			'ls_border_radius_topleft'         => ! empty( $attributes['hz_ls_border_radius']['topLeft'] )
			? $attributes['hz_ls_border_radius']['topLeft']
			: '5px',

			'ls_border_radius_topright'        => ! empty( $attributes['hz_ls_border_radius']['topRight'] )
			? $attributes['hz_ls_border_radius']['topRight']
			: '5px',

			'ls_border_radius_bottomright'     => ! empty( $attributes['hz_ls_border_radius']['bottomRight'] )
			? $attributes['hz_ls_border_radius']['bottomRight']
			: '5px',

			'ls_border_radius_bottomleft'      => ! empty( $attributes['hz_ls_border_radius']['bottomLeft'] )
			? $attributes['hz_ls_border_radius']['bottomLeft']
			: '5px',

			'border_width_jobs'                => ! empty( $attributes['hz_jl_border_width'] ) && $attributes['hz_jl_border_width'] !== '0px'
			? $attributes['hz_jl_border_width']
			: '',

			'border_color_jobs'                => ! empty( $attributes['hz_jl_border_color'] )
			? $attributes['hz_jl_border_color']
			: '#ccc',

			'jobs_border_radius_topleft'       => ! empty( $attributes['hz_jl_border_radius']['topLeft'] )
			? $attributes['hz_jl_border_radius']['topLeft']
			: '5px',

			'jobs_border_radius_topright'      => ! empty( $attributes['hz_jl_border_radius']['topRight'] )
			? $attributes['hz_jl_border_radius']['topRight']
			: '5px',

			'jobs_border_radius_bottomright'   => ! empty( $attributes['hz_jl_border_radius']['bottomRight'] )
			? $attributes['hz_jl_border_radius']['bottomRight']
			: '5px',

			'jobs_border_radius_bottomleft'    => ! empty( $attributes['hz_jl_border_radius']['bottomLeft'] )
			? $attributes['hz_jl_border_radius']['bottomLeft']
			: '5px',

			'padding_left_jobs'                => ! empty( $attributes['hz_jl_padding']['left'] )
			? hz_append_unit_if_missing( $attributes['hz_jl_padding']['left'] )
			: '15px',

			'padding_right_jobs'               => ! empty( $attributes['hz_jl_padding']['right'] )
			? hz_append_unit_if_missing( $attributes['hz_jl_padding']['right'] )
			: '15px',

			'padding_top_jobs'                 => ! empty( $attributes['hz_jl_padding']['top'] )
			? hz_append_unit_if_missing( $attributes['hz_jl_padding']['top'] )
			: '15px',

			'padding_bottom_jobs'              => ! empty( $attributes['hz_jl_padding']['bottom'] )
			? hz_append_unit_if_missing( $attributes['hz_jl_padding']['bottom'] )
			: '15px',

			'button_width_field'               => ! empty( $attributes['hz_bs_border_width'] ) && $attributes['hz_bs_border_width'] !== '0px'
			? $attributes['hz_bs_border_width']
			: '',

			'button_color_field'               => ! empty( $attributes['hz_bs_border_color'] )
			? $attributes['hz_bs_border_color']
			: '#ccc',

			'button_border_radius_topleft'     => ! empty( $attributes['hz_bs_border_radius']['topLeft'] )
			? $attributes['hz_bs_border_radius']['topLeft']
			: '5px',

			'button_border_radius_topright'    => ! empty( $attributes['hz_bs_border_radius']['topRight'] )
			? $attributes['hz_bs_border_radius']['topRight']
			: '5px',

			'button_border_radius_bottomright' => ! empty( $attributes['hz_bs_border_radius']['bottomRight'] )
			? $attributes['hz_bs_border_radius']['bottomRight']
			: '5px',

			'button_border_radius_bottomleft'  => ! empty( $attributes['hz_bs_border_radius']['bottomLeft'] )
			? $attributes['hz_bs_border_radius']['bottomLeft']
			: '5px',

			'button_background_color'          => ! empty( $attributes['hz_button_background_color'] )
			? $attributes['hz_button_background_color']
			: '',

			'button_text_color'                => ! empty( $attributes['hz_button_text_color'] )
			? $attributes['hz_button_text_color']
			: '',

			'padding_left_button'              => ! empty( $attributes['hz_bs_padding']['left'] )
			? hz_append_unit_if_missing( $attributes['hz_bs_padding']['left'] )
			: '13px',

			'padding_right_button'             => ! empty( $attributes['hz_bs_padding']['right'] )
			? hz_append_unit_if_missing( $attributes['hz_bs_padding']['right'] )
			: '13px',

			'padding_top_button'               => ! empty( $attributes['hz_bs_padding']['top'] )
			? hz_append_unit_if_missing( $attributes['hz_bs_padding']['top'] )
			: '13px',

			'padding_bottom_button'            => ! empty( $attributes['hz_bs_padding']['bottom'] )
			? hz_append_unit_if_missing( $attributes['hz_bs_padding']['bottom'] )
			: '13px',
		);

		return apply_filters( 'hz_ui_styles', $styles, $attributes );
	}
}

if ( ! function_exists( 'hz_append_unit_if_missing' ) ) {
	function hz_append_unit_if_missing( $value, $default_unit = 'px' ) {
		// List of common CSS units
		$units_pattern = '/(px|em|rem|%)$/';

		// If value is numeric or lacks a valid unit, append the default unit
		return ( is_numeric( $value ) || ! preg_match( $units_pattern, $value ) )
			? $value . $default_unit
			: $value;
	}
}

if ( ! function_exists( 'awsm_b_job_more_details' ) ) {
	function awsm_b_job_more_details( $link, $view ) {

		$button_class = 'awsm-b-job-more';

		$more_dtls_link = sprintf(
			'<div class="awsm-b-job-more-container"><%1$s class="%2$s"%3$s>%4$s <span></span></%1$s></div>',
			( $view === 'grid' ) ? 'span' : 'a',
			esc_attr( $button_class ),
			( $view === 'grid' ) ? '' : ' href="' . esc_url( $link ) . '"',
			esc_html__( 'More Details', 'wp-job-openings' )
		);

		echo apply_filters( 'awsm_b_jobs_listing_details_link', $more_dtls_link, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'do_dynamic_filter_form_action' ) ) {
	function do_dynamic_filter_form_action( $attributes ) {
		$placement = isset( $attributes['placement'] ) ? $attributes['placement'] : 'slide';

		$action_name = ( $placement === 'top' )
			? 'awsm_block_filter_form'
			: 'awsm_block_filter_form_slide';

		do_action( $action_name, $attributes );
	}
}

if ( ! function_exists( 'render_awsm_block_job_wrap' ) ) {
	function render_awsm_block_job_wrap( $attributes, $block_id = '', $placement_sidebar_class = '', $show_filter = true ) {
		$placement = isset( $attributes['placement'] ) ? $attributes['placement'] : 'top';

		if ( $placement === 'top' ) {
			?>
			<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?>" id="<?php echo esc_attr( $block_id ); ?>">
				<?php
					do_dynamic_filter_form_action( $attributes );
					do_action( 'awsm_block_form_outside', $attributes );
				?>
				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php awsm_block_jobs_view_class( '', $attributes ); ?>>
						<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="awsm-b-job-wrap<?php awsm_jobs_wrapper_class(); ?> awsm-job-form-plugin-style <?php echo esc_attr( $placement_sidebar_class ); ?>" id="<?php echo esc_attr( $block_id ); ?>">
				<?php if ( $show_filter ) : ?>
					<div class="awsm-b-filter-wrap awsm-jobs-alerts-on">
						<?php
							do_dynamic_filter_form_action( $attributes );
							do_action( 'awsm_block_form_outside', $attributes );
						?>
					</div>
				<?php endif; ?>

				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php echo awsm_block_jobs_view_class( 'custom-class', $attributes ); ?>>
						<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
