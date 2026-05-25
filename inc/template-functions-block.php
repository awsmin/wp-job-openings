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


if ( ! function_exists( 'awsm_block_jobs_query' ) ) {
	function awsm_block_jobs_query( $attributes = array() ) {
		$query_args      = array();
		$is_term_or_slug = array();
		$filter_suffix   = '_spec';
		$search_job      = '';

		$filters = get_option( 'awsm_jobs_listing_available_filters' );
		$filters = awsm_block_job_filters_explode( $filters );

		// Also include any taxonomies configured in this block's filter_options that aren't in the global list.
		// Handles both string format ['tax-key'] (current) and old object format [{specKey, value}].
		if ( ! empty( $attributes['filter_options'] ) && is_array( $attributes['filter_options'] ) ) {
			foreach ( $attributes['filter_options'] as $filter_option ) {
				if ( is_string( $filter_option ) && ! empty( $filter_option ) ) {
					$key = $filter_option;
				} elseif ( is_array( $filter_option ) && ! empty( $filter_option['specKey'] ) ) {
					$key = $filter_option['specKey'];
				} else {
					continue;
				}
				if ( ! in_array( $key, $filters, true ) ) {
					$filters[] = $key;
				}
			}
		}

		if ( isset( $_GET['jq'] ) && $_GET['jq'] !== '' ) {
			$search_job = sanitize_text_field( wp_unslash( $_GET['jq'] ) );
		}

		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$current_filter_key = str_replace( '-', '__', $filter ) . $filter_suffix;

				// Check if filter exists in URL ($_GET), else use stored option
				if ( isset( $_GET[ $current_filter_key ] ) && ! empty( $_GET[ $current_filter_key ] ) ) {
					$term_slugs = explode( ',', sanitize_text_field( $_GET[ $current_filter_key ] ) );
				} else {
					// Fallback to stored option if URL parameter is missing
					$saved_terms = get_option( 'awsm_jobs_default_' . $filter, '' ); // Modify key accordingly
					$term_slugs  = ! empty( $saved_terms ) ? explode( ',', $saved_terms ) : array();
				}

				if ( ! empty( $term_slugs ) ) {
					$query_args[ $filter ] = array();

					foreach ( $term_slugs as $term_slug ) {
						$term = get_term_by( 'slug', sanitize_title( $term_slug ), $filter );

						if ( $term && ! is_wp_error( $term ) ) {
							$query_args[ $filter ][]    = $term->term_id;
							$is_term_or_slug[ $filter ] = 'term_id';
						} else {
							$query_args[ $filter ][]    = $term_slug;
							$is_term_or_slug[ $filter ] = 'slug';
						}
					}
				}
			}
		}

		$args = AWSM_Job_Openings_Block::awsm_block_job_query_args( $query_args, $attributes, $is_term_or_slug );

		if ( ! empty( $search_job ) ) {
			$args['s'] = $search_job;
		}

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
	function awsm_block_jobs_load_more( $query, $attributes = array() ) {
		$loadmore      = isset( $attributes['block_loadmore'] ) && $attributes['block_loadmore'] === 'no' ? false : true;
		$max_num_pages = $query->max_num_pages;
		if ( $loadmore && $max_num_pages > 1 ) {
			$button_style = ! empty( $attributes['hz_button_style'] ) ? sanitize_key( $attributes['hz_button_style'] ) : 'none';
			if ( AWSM_Job_Openings::is_default_pagination( $attributes ) ) {
				$paged = ( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;
				if ( $paged < $max_num_pages ) {
					$load_more_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-main is-button-%3$s"><a href="#" class="awsm-b-load-more awsm-b-load-more-btn" data-page="%2$s">%1$s</a></div>', esc_html__( 'Load more', 'wp-job-openings' ), esc_attr( $paged ), esc_attr( $button_style ) );
					/**
					 * Filters the load more content.
					 *
					 * @since 3.5.0
					 *
					 * @param string $load_more_content The HTML content.
					 * @param WP_Query $query The Query object.
					 * @param array $attributes Block attributes.
					 */
					echo apply_filters( 'awsm_block_jobs_load_more_content', $load_more_content, $query, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {
				echo awsm_block_jobs_paginate_links( $query, array( 'hz_button_style' => $button_style ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
}

if ( ! function_exists( 'awsm_block_jobs_paginate_links' ) ) {
	function awsm_block_jobs_paginate_links( $query, $shortcode_atts = array() ) {
		$is_homepage = is_front_page() || is_home();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['paged'] ) ) {
			$current = absint( $_POST['paged'] );// phpcs:disable WordPress.Security.NonceVerification.Missing
		} elseif ( $is_homepage ) {
				$current = get_query_var( 'page' ) ? absint( get_query_var( 'page' ) ) : 1;
		} else {
			$current = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		}

		$page_var      = ( is_front_page() || is_home() ) ? 'page' : 'paged';
		$max_num_pages = isset( $query->max_num_pages ) ? $query->max_num_pages : 1;
		$base_url      = get_pagenum_link();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			$base_url = $_POST['awsm_pagination_base'];
		}
		// phpcs:enable

		$args               = array(
			'base'    => esc_url_raw( add_query_arg( $page_var, '%#%', $base_url ) ),
			'format'  => '',
			'type'    => 'list',
			'current' => max( 1, $current ),
			'total'   => $max_num_pages,
		);
		$button_style       = ! empty( $shortcode_atts['hz_button_style'] ) ? sanitize_key( $shortcode_atts['hz_button_style'] ) : 'none';
		$pagination_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-classic is-button-%2$s" data-effect-duration="slow">%1$s</div>', paginate_links( $args ), esc_attr( $button_style ) );
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

if ( ! function_exists( 'awsm_block_job_listing_spec_content' ) ) {
	function awsm_block_job_listing_spec_content( $job_id, $awsm_filters, $listing_specs, $has_term_link = true, $show_icon = false ) {
		echo AWSM_Job_Openings_Block::get_specifications_content_block( $job_id, false, $awsm_filters, array( 'specs' => $listing_specs ), $has_term_link, $show_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

if ( ! function_exists( 'hz_get_ui_styles' ) ) {
	function hz_get_ui_styles( $attributes ) {

		if ( ! empty( $attributes['anchor'] ) ) {
			$block_id = sanitize_title( $attributes['anchor'] );
		} elseif ( ! empty( $attributes['blockId'] ) ) {
			$block_id = $attributes['blockId'];
		} else {
			$block_id = 'awsm-block-' . wp_unique_id();
		}

		$styles = array(
			'block_id'                             => $block_id,

			// Search form / main block
			'border_width'                         => ! empty( $attributes['hz_sf_border_width'] ) && $attributes['hz_sf_border_width'] !== '0px'
				? hz_append_unit_if_missing( $attributes['hz_sf_border_width'] )
				: '',

			'border_color'                         => ! empty( $attributes['hz_sf_border_color'] )
				? sanitize_hex_color( $attributes['hz_sf_border_color'] )
				: '#ccc',

			'sf_border_radius_topleft'             => isset( $attributes['hz_sf_border_radius']['topLeft'] ) ? $attributes['hz_sf_border_radius']['topLeft'] : '5px',
			'sf_border_radius_topright'            => isset( $attributes['hz_sf_border_radius']['topRight'] ) ? $attributes['hz_sf_border_radius']['topRight'] : '5px',
			'sf_border_radius_bottomright'         => isset( $attributes['hz_sf_border_radius']['bottomRight'] ) ? $attributes['hz_sf_border_radius']['bottomRight'] : '5px',
			'sf_border_radius_bottomleft'          => isset( $attributes['hz_sf_border_radius']['bottomLeft'] ) ? $attributes['hz_sf_border_radius']['bottomLeft'] : '5px',

			'padding_left'                         => ! empty( $attributes['hz_sf_padding']['left'] )
				? hz_append_unit_if_missing( $attributes['hz_sf_padding']['left'] )
				: '15px',
			'padding_right'                        => ! empty( $attributes['hz_sf_padding']['right'] )
				? hz_append_unit_if_missing( $attributes['hz_sf_padding']['right'] )
				: '15px',
			'padding_top'                          => ! empty( $attributes['hz_sf_padding']['top'] )
				? hz_append_unit_if_missing( $attributes['hz_sf_padding']['top'] )
				: '15px',
			'padding_bottom'                       => ! empty( $attributes['hz_sf_padding']['bottom'] )
				? hz_append_unit_if_missing( $attributes['hz_sf_padding']['bottom'] )
				: '15px',

			'sidebar_width'                        => ! empty( $attributes['hz_sidebar_width'] )
				? hz_append_unit_if_missing( $attributes['hz_sidebar_width'], '%' )
				: '33.333%',

			// List style block
			'border_width_field'                   => ! empty( $attributes['hz_ls_border_width'] ) && $attributes['hz_ls_border_width'] !== '0px'
				? hz_append_unit_if_missing( $attributes['hz_ls_border_width'] )
				: '1px',
			'border_color_field'                   => ! empty( $attributes['hz_ls_border_color'] )
				? sanitize_hex_color( $attributes['hz_ls_border_color'] )
				: '#ccc',
			'ls_border_radius_topleft'             => isset( $attributes['hz_ls_border_radius']['topLeft'] ) ? $attributes['hz_ls_border_radius']['topLeft'] : '5px',
			'ls_border_radius_topright'            => isset( $attributes['hz_ls_border_radius']['topRight'] ) ? $attributes['hz_ls_border_radius']['topRight'] : '5px',
			'ls_border_radius_bottomright'         => isset( $attributes['hz_ls_border_radius']['bottomRight'] ) ? $attributes['hz_ls_border_radius']['bottomRight'] : '5px',
			'ls_border_radius_bottomleft'          => isset( $attributes['hz_ls_border_radius']['bottomLeft'] ) ? $attributes['hz_ls_border_radius']['bottomLeft'] : '5px',

			// Job listings block
			'border_width_jobs'                    => ! empty( $attributes['hz_jl_border_width'] ) && $attributes['hz_jl_border_width'] !== '0px'
				? hz_append_unit_if_missing( $attributes['hz_jl_border_width'] )
				: '',
			'border_color_jobs'                    => ! empty( $attributes['hz_jl_border_color'] )
				? sanitize_hex_color( $attributes['hz_jl_border_color'] )
				: '#CBCBCB',
			'jobs_border_radius_topleft'           => isset( $attributes['hz_jl_border_radius']['topLeft'] ) ? $attributes['hz_jl_border_radius']['topLeft'] : '5px',
			'jobs_border_radius_topright'          => isset( $attributes['hz_jl_border_radius']['topRight'] ) ? $attributes['hz_jl_border_radius']['topRight'] : '5px',
			'jobs_border_radius_bottomright'       => isset( $attributes['hz_jl_border_radius']['bottomRight'] ) ? $attributes['hz_jl_border_radius']['bottomRight'] : '5px',
			'jobs_border_radius_bottomleft'        => isset( $attributes['hz_jl_border_radius']['bottomLeft'] ) ? $attributes['hz_jl_border_radius']['bottomLeft'] : '5px',
			'padding_left_jobs'                    => ! empty( $attributes['hz_jl_padding']['left'] )
				? hz_append_unit_if_missing( $attributes['hz_jl_padding']['left'] )
				: '15px',
			'padding_right_jobs'                   => ! empty( $attributes['hz_jl_padding']['right'] )
				? hz_append_unit_if_missing( $attributes['hz_jl_padding']['right'] )
				: '15px',
			'padding_top_jobs'                     => ! empty( $attributes['hz_jl_padding']['top'] )
				? hz_append_unit_if_missing( $attributes['hz_jl_padding']['top'] )
				: '15px',
			'padding_bottom_jobs'                  => ! empty( $attributes['hz_jl_padding']['bottom'] )
				? hz_append_unit_if_missing( $attributes['hz_jl_padding']['bottom'] )
				: '15px',

			// Button block
			'button_width_field'                   => ! empty( $attributes['hz_bs_border_width'] ) && $attributes['hz_bs_border_width'] !== '0px'
				? hz_append_unit_if_missing( $attributes['hz_bs_border_width'] )
				: '',
			'button_color_field'                   => ! empty( $attributes['hz_bs_border_color'] )
				? sanitize_hex_color( $attributes['hz_bs_border_color'] )
				: '#4e35df',
			'button_border_radius_topleft'         => isset( $attributes['hz_bs_border_radius']['topLeft'] ) ? $attributes['hz_bs_border_radius']['topLeft'] : '5px',
			'button_border_radius_topright'        => isset( $attributes['hz_bs_border_radius']['topRight'] ) ? $attributes['hz_bs_border_radius']['topRight'] : '5px',
			'button_border_radius_bottomright'     => isset( $attributes['hz_bs_border_radius']['bottomRight'] ) ? $attributes['hz_bs_border_radius']['bottomRight'] : '5px',
			'button_border_radius_bottomleft'      => isset( $attributes['hz_bs_border_radius']['bottomLeft'] ) ? $attributes['hz_bs_border_radius']['bottomLeft'] : '5px',
			'button_style'                         => ! empty( $attributes['hz_button_style'] ) ? sanitize_key( $attributes['hz_button_style'] ) : 'none',
			'button_text'                          => ! empty( $attributes['hz_button_text'] ) ? sanitize_text_field( $attributes['hz_button_text'] ) : '',
			'button_background_color'              => ! empty( $attributes['hz_button_background_color'] )
				? hz_sanitize_color( $attributes['hz_button_background_color'] )
				: '',
			'button_text_color'                    => ! empty( $attributes['hz_button_text_color'] )
				? hz_sanitize_color( $attributes['hz_button_text_color'] )
				: '',
			'pagination_background_color'          => ! empty( $attributes['hz_pagination_background_color'] )
				? hz_sanitize_color( $attributes['hz_pagination_background_color'] )
				: '',
			'pagination_text_color'                => ! empty( $attributes['hz_pagination_text_color'] )
				? hz_sanitize_color( $attributes['hz_pagination_text_color'] )
				: '',
			'pagination_border_width'              => ! empty( $attributes['hz_pagination_border_width'] ) && $attributes['hz_pagination_border_width'] !== '0px'
				? hz_append_unit_if_missing( $attributes['hz_pagination_border_width'] )
				: '1px',
			'pagination_border_color'              => ! empty( $attributes['hz_pagination_border_color'] )
				? sanitize_hex_color( $attributes['hz_pagination_border_color'] )
				: '#cbcbcb',
			'pagination_border_radius_topleft'     => isset( $attributes['hz_pagination_border_radius']['topLeft'] ) ? $attributes['hz_pagination_border_radius']['topLeft'] : '5px',
			'pagination_border_radius_topright'    => isset( $attributes['hz_pagination_border_radius']['topRight'] ) ? $attributes['hz_pagination_border_radius']['topRight'] : '5px',
			'pagination_border_radius_bottomright' => isset( $attributes['hz_pagination_border_radius']['bottomRight'] ) ? $attributes['hz_pagination_border_radius']['bottomRight'] : '5px',
			'pagination_border_radius_bottomleft'  => isset( $attributes['hz_pagination_border_radius']['bottomLeft'] ) ? $attributes['hz_pagination_border_radius']['bottomLeft'] : '5px',
			'sf_background_color'                  => ! empty( $attributes['hz_sf_background_color'] )
				? hz_sanitize_color( $attributes['hz_sf_background_color'] )
				: '',
			'sf_text_color'                        => ! empty( $attributes['hz_sf_text_color'] )
				? hz_sanitize_color( $attributes['hz_sf_text_color'] )
				: '',
			'jl_background_color'                  => ! empty( $attributes['hz_jl_background_color'] )
				? hz_sanitize_color( $attributes['hz_jl_background_color'] )
				: '',
			'jl_text_color'                        => ! empty( $attributes['hz_jl_text_color'] )
				? hz_sanitize_color( $attributes['hz_jl_text_color'] )
				: '',
			'sidebar_bg_color'                     => ! empty( $attributes['hz_sidebar_bg_color'] )
				? hz_sanitize_color( $attributes['hz_sidebar_bg_color'] )
				: '',
			'sidebar_tx_color'                     => ! empty( $attributes['hz_sidebar_tx_color'] )
				? hz_sanitize_color( $attributes['hz_sidebar_tx_color'] )
				: '',
			'padding_left_button'                  => ! empty( $attributes['hz_bs_padding']['left'] )
				? hz_append_unit_if_missing( $attributes['hz_bs_padding']['left'] )
				: '13px',
			'padding_right_button'                 => ! empty( $attributes['hz_bs_padding']['right'] )
				? hz_append_unit_if_missing( $attributes['hz_bs_padding']['right'] )
				: '13px',
			'padding_top_button'                   => ! empty( $attributes['hz_bs_padding']['top'] )
				? hz_append_unit_if_missing( $attributes['hz_bs_padding']['top'] )
				: '13px',
			'padding_bottom_button'                => ! empty( $attributes['hz_bs_padding']['bottom'] )
				? hz_append_unit_if_missing( $attributes['hz_bs_padding']['bottom'] )
				: '13px',
			'padding_top_pagination'               => ! empty( $attributes['hz_pagination_padding']['top'] )
				? hz_append_unit_if_missing( $attributes['hz_pagination_padding']['top'] )
				: ( isset( $attributes['pagination'] ) && $attributes['pagination'] === 'classic' ? '5px' : '20px' ),
			'padding_right_pagination'             => ! empty( $attributes['hz_pagination_padding']['right'] )
				? hz_append_unit_if_missing( $attributes['hz_pagination_padding']['right'] )
				: ( isset( $attributes['pagination'] ) && $attributes['pagination'] === 'classic' ? '5px' : '20px' ),
			'padding_bottom_pagination'            => ! empty( $attributes['hz_pagination_padding']['bottom'] )
				? hz_append_unit_if_missing( $attributes['hz_pagination_padding']['bottom'] )
				: ( isset( $attributes['pagination'] ) && $attributes['pagination'] === 'classic' ? '5px' : '20px' ),
			'padding_left_pagination'              => ! empty( $attributes['hz_pagination_padding']['left'] )
				? hz_append_unit_if_missing( $attributes['hz_pagination_padding']['left'] )
				: ( isset( $attributes['pagination'] ) && $attributes['pagination'] === 'classic' ? '5px' : '20px' ),
		);

		return apply_filters( 'hz_ui_styles', $styles, $attributes );
	}
}

if ( ! function_exists( 'hz_sanitize_color' ) ) {
	function hz_sanitize_color( $color ) {
		if ( preg_match( '/^var\(--[a-zA-Z0-9_-]+(?:,\s*[^)]+)?\)$/', $color ) ) {
			return $color;
		}
		return sanitize_hex_color( $color );
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

		$button_class = apply_filters( 'awsm_b_job_more_button_class', 'awsm-b-job-more' );

		$more_dtls_link = sprintf(
			'<div class="awsm-b-job-more-container"><%1$s class="%2$s"%3$s>%4$s</%1$s></div>',
			( $view === 'grid' ) ? 'span' : 'a',
			esc_attr( $button_class ),
			( $view === 'grid' ) ? '' : ' href="' . esc_url( $link ) . '"',
			apply_filters( 'awsm_b_job_more_button_text', esc_html__( 'More Details →', 'wp-job-openings' ) )
		);

		echo apply_filters( 'awsm_b_jobs_listing_details_link', $more_dtls_link, $view ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'do_dynamic_filter_form_action' ) ) {
	function do_dynamic_filter_form_action( $attributes ) {
		$placement = isset( $attributes['placement'] ) ? $attributes['placement'] : 'top';

		$action_name = ( $placement === 'top' )
			? 'awsm_block_filter_form'
			: 'awsm_block_filter_form_side';

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
					<div class="awsm-b-filter-wrap<?php echo ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ? '' : ' awsm-selectric-loading'; ?><?php echo ( class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' ) && ! empty( $attributes['enable_alert'] ) ) ? ' awsm-jobs-alerts-on' : ''; ?>" data-placement="side">
						<?php
							do_dynamic_filter_form_action( $attributes );
							do_action( 'awsm_block_form_outside', $attributes );
						?>
					</div>
				<?php endif; ?>

				<div class="awsm-b-job-listings"<?php awsm_block_jobs_data_attrs( array(), $attributes ); ?>>
					<div <?php awsm_block_jobs_view_class( 'custom-class', $attributes ); ?>>
						<?php include get_awsm_jobs_template_path( 'block-main', 'block-files' ); ?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
