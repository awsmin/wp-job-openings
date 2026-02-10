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
		if ( ! empty( $class ) ) {
			$view_class = trim( $view_class . ' ' . $class );
		}
		printf( 'class="%s"', esc_attr( $view_class ) );
	}
}

if ( ! function_exists( 'awsm_block_job_filters_explode' ) ) {
	function awsm_block_job_filters_explode( $filter_data ) {
		$available_filters = array();

		if ( ! empty( $filter_data ) ) {
			$available_filters = explode( ',', $filter_data );
		}
		return $available_filters;
	}
}


if ( ! function_exists( 'get_block_filtered_job_terms' ) ) {
	function get_block_filtered_job_terms( $attributes ) {
		$filter_suffix  = '_spec';
		$filters        = $attributes['filter_options'];
		$filtered_terms = array();

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

		$filters = get_option( 'awsm_jobs_listing_available_filters' );

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
	function awsm_block_jobs_load_more( $query, $shortcode_atts = array() ) {
		$loadmore      = isset( $shortcode_atts['block_loadmore'] ) && $shortcode_atts['block_loadmore'] === 'no' ? false : true;
		$max_num_pages = $query->max_num_pages;
		if ( $loadmore && $max_num_pages > 1 ) {
			if ( AWSM_Job_Openings::is_default_pagination( $shortcode_atts ) ) {
				$paged = ( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1;
				if ( $paged < $max_num_pages ) {
					 $load_more_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-main"><a href="#" class="awsm-b-load-more awsm-b-load-more-btn" data-page="%2$s">%1$s</a></div>', esc_html__( 'Load more...', 'wp-job-openings' ), esc_attr( $paged ) );
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
		$is_homepage = is_front_page() || is_home();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['paged'] ) ) {
			$current = absint( $_POST['paged'] );// phpcs:disable WordPress.Security.NonceVerification.Missing
		} else {
			if ( $is_homepage ) {
				$current = get_query_var( 'page' ) ? absint( get_query_var( 'page' ) ) : 1;
			} else {
				$current = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
			}
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
		$pagination_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-load-more-classic" data-effect-duration="slow">%s</div>', paginate_links( $args ) );
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

