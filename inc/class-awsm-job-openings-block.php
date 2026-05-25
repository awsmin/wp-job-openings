<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Block {
	private static $instance = null;

	protected $unique_listing_id = 1;

	public function __construct() {
		add_action( 'awsm_block_filter_form', array( $this, 'display_block_filter_form' ) );
		add_action( 'wp_ajax_block_jobfilter', array( $this, 'awsm_block_posts_filters' ) );
		add_action( 'wp_ajax_nopriv_block_jobfilter', array( $this, 'awsm_block_posts_filters' ) );
		add_action( 'wp_ajax_block_loadmore', array( $this, 'awsm_block_posts_filters' ) );
		add_action( 'wp_ajax_nopriv_block_loadmore', array( $this, 'awsm_block_posts_filters' ) );
		add_action( 'awsm_block_filter_form_side', array( $this, 'display_block_filter_form_side' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function awsm_jobs_block_attributes( $blockatts ) {
		if ( ! function_exists( 'awsm_jobs_query' ) ) {
			return;
		}

		$block_atts_set = array(
			'uid'                            => $this->unique_listing_id,
			'search'                         => isset( $blockatts['search'] ) ? ( $blockatts['search'] === true ? 'enable' : $blockatts['search'] ) : '',
			'enable_job_filter'              => isset( $blockatts['enable_job_filter'] ) ? ( $blockatts['enable_job_filter'] === true ? 'enable' : $blockatts['enable_job_filter'] ) : '',
			'filter_options'                 => isset( $blockatts['filter_options'] ) ? $blockatts['filter_options'] : array(),
			'filter_types'                   => ( function () use ( $blockatts ) {
					$filter_types           = isset( $blockatts['filter_types'] ) && is_array( $blockatts['filter_types'] ) ? $blockatts['filter_types'] : array();
					$supported_filter_types = apply_filters( 'awsm_jobs_block_supported_filter_types', array( 'dropdown' ) );
				foreach ( $filter_types as $taxonomy => $type ) {
					if ( ! in_array( $type, $supported_filter_types, true ) ) {
						$filter_types[ $taxonomy ] = 'dropdown';
					}
				}
					return $filter_types;
			} )(),
			'layout'                         => ( function () use ( $blockatts ) {
					$layout            = isset( $blockatts['layout'] ) ? $blockatts['layout'] : 'list';
					$supported_layouts = apply_filters( 'awsm_jobs_block_supported_layouts', array( 'list', 'grid' ) );
					return in_array( $layout, $supported_layouts, true ) ? $layout : 'list';
			} )(),
			'hide_expired_jobs'              => isset( $blockatts['hide_expired_jobs'] ) ? $blockatts['hide_expired_jobs'] : '',
			'placement'                      => isset( $blockatts['placement'] ) ? $blockatts['placement'] : 'top',
			'search_placeholder'             => isset( $blockatts['search_placeholder'] ) ? sanitize_text_field( $blockatts['search_placeholder'] ) : '',
			'number_of_columns'              => isset( $blockatts['number_of_columns'] ) ? $blockatts['number_of_columns'] : 3,
			'other_options'                  => isset( $blockatts['other_options'] ) ? $blockatts['other_options'] : '',
			'show_spec_icon'                 => isset( $blockatts['show_spec_icon'] ) ? $blockatts['show_spec_icon'] : '',
			'list_type'                      => ( function () use ( $blockatts ) {
					$list_type             = isset( $blockatts['list_type'] ) ? $blockatts['list_type'] : 'all';
					$supported_list_types  = apply_filters( 'awsm_jobs_block_supported_list_types', array( 'all' ) );
					return in_array( $list_type, $supported_list_types, true ) ? $list_type : 'all';
			} )(),
			'selected_terms'                 => isset( $blockatts['selected_terms'] ) ? $blockatts['selected_terms'] : '',
			'order_by'                       => isset( $blockatts['order_by'] ) ? $blockatts['order_by'] : '',
			'listings'                       => isset( $blockatts['listing_per_page'] ) ? $blockatts['listing_per_page'] : '',
			'pagination'                     => isset( $blockatts['pagination'] ) ? $blockatts['pagination'] : '',
			'anchor'                         => isset( $blockatts['anchor'] ) ? $blockatts['anchor'] : '',
			'blockId'                        => isset( $blockatts['blockId'] ) ? $blockatts['blockId'] : '',
			'hz_sf_border_color'             => isset( $blockatts['hz_sf_border']['color'] ) ? $blockatts['hz_sf_border']['color'] : '',
			'hz_sf_border_width'             => isset( $blockatts['hz_sf_border']['width'] ) ? $blockatts['hz_sf_border']['width'] : '',
			'hz_sf_padding'                  => isset( $blockatts['hz_sf_padding'] ) ? $blockatts['hz_sf_padding'] : '',
			'hz_sf_border_radius'            => isset( $blockatts['hz_sf_border_radius'] ) ? $blockatts['hz_sf_border_radius'] : '',
			'hz_sidebar_width'               => isset( $blockatts['hz_sidebar_width'] ) ? $blockatts['hz_sidebar_width'] : '',
			'hz_ls_border_color'             => isset( $blockatts['hz_ls_border']['color'] ) ? $blockatts['hz_ls_border']['color'] : '',
			'hz_ls_border_width'             => isset( $blockatts['hz_ls_border']['width'] ) && $blockatts['hz_ls_border']['width'] !== '0px' ? $blockatts['hz_ls_border']['width'] : '1px',
			'hz_ls_border_radius'            => isset( $blockatts['hz_ls_border_radius'] ) ? $blockatts['hz_ls_border_radius'] : '',
			'hz_jl_border_color'             => isset( $blockatts['hz_jl_border']['color'] ) ? $blockatts['hz_jl_border']['color'] : '',
			'hz_jl_border_width'             => isset( $blockatts['hz_jl_border']['width'] ) ? $blockatts['hz_jl_border']['width'] : '',
			'hz_jl_border_radius'            => isset( $blockatts['hz_jl_border_radius'] ) ? $blockatts['hz_jl_border_radius'] : '',
			'hz_jl_padding'                  => isset( $blockatts['hz_jl_padding'] ) ? $blockatts['hz_jl_padding'] : '',
			'hz_bs_border_color'             => isset( $blockatts['hz_bs_border']['color'] ) ? $blockatts['hz_bs_border']['color'] : '',
			'hz_bs_border_width'             => isset( $blockatts['hz_bs_border']['width'] ) ? $blockatts['hz_bs_border']['width'] : '',
			'hz_bs_border_radius'            => isset( $blockatts['hz_bs_border_radius'] ) ? $blockatts['hz_bs_border_radius'] : '',
			'hz_bs_padding'                  => isset( $blockatts['hz_bs_padding'] ) ? $blockatts['hz_bs_padding'] : '',
			'hz_button_style'                => isset( $blockatts['hz_button_style'] ) ? $blockatts['hz_button_style'] : 'none',
			'hz_button_text'                 => ! empty( $blockatts['hz_button_text'] ) ? $blockatts['hz_button_text'] : '',
			'hz_button_background_color'     => isset( $blockatts['hz_button_background_color'] ) ? $blockatts['hz_button_background_color'] : '',
			'hz_button_text_color'           => isset( $blockatts['hz_button_text_color'] ) ? $blockatts['hz_button_text_color'] : '',
			'hz_pagination_background_color' => isset( $blockatts['hz_pagination_background_color'] ) ? $blockatts['hz_pagination_background_color'] : '',
			'hz_pagination_text_color'       => isset( $blockatts['hz_pagination_text_color'] ) ? $blockatts['hz_pagination_text_color'] : '',
			'hz_pagination_border_radius'    => isset( $blockatts['hz_pagination_border_radius'] ) ? $blockatts['hz_pagination_border_radius'] : array(),
			'hz_pagination_border_color'     => isset( $blockatts['hz_pagination_border']['color'] ) ? $blockatts['hz_pagination_border']['color'] : '',
			'hz_pagination_border_width'     => isset( $blockatts['hz_pagination_border']['width'] ) ? $blockatts['hz_pagination_border']['width'] : '',
			'hz_pagination_padding'          => isset( $blockatts['hz_pagination_padding'] ) ? $blockatts['hz_pagination_padding'] : array(),
			'hz_sf_background_color'         => isset( $blockatts['hz_sf_background_color'] ) ? $blockatts['hz_sf_background_color'] : '',
			'hz_sf_text_color'               => isset( $blockatts['hz_sf_text_color'] ) ? $blockatts['hz_sf_text_color'] : '',
			'hz_jl_background_color'         => isset( $blockatts['hz_jl_background_color'] ) ? $blockatts['hz_jl_background_color'] : '',
			'hz_jl_text_color'               => isset( $blockatts['hz_jl_text_color'] ) ? $blockatts['hz_jl_text_color'] : '',
			'hz_sidebar_bg_color'            => isset( $blockatts['hz_sidebar_bg_color'] ) ? $blockatts['hz_sidebar_bg_color'] : '',
			'hz_sidebar_tx_color'            => isset( $blockatts['hz_sidebar_tx_color'] ) ? $blockatts['hz_sidebar_tx_color'] : '',
		);

		/**
		 * Filter the attribute set for the Job Listing block.
		 *
		 * Allows modification of the attributes set for the job listings block before rendering.
		 *
		 * @since 3.5.0
		 *
		 * @param array $block_atts_set List of attributes used for rendering the block.
		 * @param array $blockatts      Original attributes passed to the block.
		 */
		$block_atts_set = apply_filters( 'awsm_jobs_block_attributes_set', $block_atts_set, $blockatts );

		++$this->unique_listing_id;

		ob_start();
		include get_awsm_jobs_template_path( 'block-job-openings-view', 'block-files' );
		$block_content = ob_get_clean();

		/**
		 * Filter the output content for the Job Listing block.
		 *
		 * Allows modification of the rendered block content before it is returned.
		 *
		 * @since 3.5.0
		 *
		 * @param string $block_content The rendered block content.
		 */
		return apply_filters( 'awsm_jobs_block_output_content', $block_content );
	}

	public static function get_job_listing_view_class_block( $attributes = array() ) {
		$view       = isset( $attributes['layout'] ) ? sanitize_text_field( $attributes['layout'] ) : 'list';
		$view_class = 'awsm-b-job-listing-items';

		switch ( $view ) {
			case 'grid':
				$number_columns = isset( $attributes['number_of_columns'] ) && intval( $attributes['number_of_columns'] ) > 0 ? intval( $attributes['number_of_columns'] ) : 3;
				$view_class     = 'awsm-b-row awsm-b-job-listing-items';
				$column_class   = $number_columns === 1 ? 'awsm-b-grid-col' : 'awsm-b-grid-col-' . $number_columns;

				$view_class .= ' ' . $column_class;
				break;

			default:
				$view_class .= ' awsm-b-lists';
				break;
		}

		return esc_attr( apply_filters( 'awsm_jobs_block_view_class', $view_class, $view, $attributes ) );
	}

	public static function is_edit_or_add_page( $type = '' ) {
		// Check if the request is a REST API request, which is used by the block editor
		if ( wp_is_json_request() ) {
			return true;
		}

		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( $screen ) {
				// Check for edit or add new pages
				if ( 'post' === $screen->base && ( 'post' === $screen->id || 'edit' === $screen->id ) ) {
					return true;
				}
				if ( 'page' === $screen->base && ( 'page' === $screen->id || 'edit-page' === $screen->id ) ) {
					return true;
				}
				if ( $type && ( $type === $screen->post_type ) && ( 'post' === $screen->base || 'edit' === $screen->base ) ) {
					return true;
				}
			}
		}
		return false;
	}

	public static function get_block_filters_query_args( $filters = false ) {
		$query_args = array();
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$current_filter_key = str_replace( '-', '__', $filter ) . '_spec';
				if ( isset( $_GET[ $current_filter_key ] ) ) {
					$query_args[ $filter ] = sanitize_title( $_GET[ $current_filter_key ] );
				}
			}
		}
		return $query_args;
	}

	public function awsm_block_posts_filters() {
		if ( ! isset( $_POST['awsm_block_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['awsm_block_nonce'] ) ), 'awsm_block_ajax' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'wp-job-openings' ) ), 403 );
		}

		$filters = $filters_list = $attributes = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

		$filter_action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';

		if ( ! empty( $_POST['awsm_job_spec'] ) ) {
			$job_specs = wp_unslash( $_POST['awsm_job_spec'] );

			if ( ! is_array( $job_specs ) ) {
				$job_specs = array();
			}

			foreach ( $job_specs as $taxonomy => $term_id ) {
				$taxonomy = sanitize_key( $taxonomy );

				if ( ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				if ( is_array( $term_id ) ) {
					foreach ( $term_id as $term ) {
						$filters_list[ $taxonomy ][] = absint( $term );
					}
				} else {
					$filters[ $taxonomy ] = absint( $term_id );
				}
			}
		}

		if ( isset( $_POST['awsm_job_specs_list'] ) ) {
			$filters_list = array();
			$raw_specs    = wp_unslash( $_POST['awsm_job_specs_list'] );

			if ( is_array( $raw_specs ) ) {
				foreach ( $raw_specs as $taxonomy => $terms ) {
					$taxonomy = sanitize_key( $taxonomy );
					if ( ! taxonomy_exists( $taxonomy ) ) {
						continue;
					}

					$terms = is_array( $terms ) ? $terms : array( $terms );
					$terms = array_values( array_filter( array_map( 'absint', $terms ) ) );

					if ( ! empty( $terms ) ) {
						$filters_list[ $taxonomy ] = $terms;
					}
				}
			}
		}

		if ( ! empty( $_POST['awsm-layout'] ) ) {
			$attributes['layout'] = sanitize_text_field( wp_unslash( $_POST['awsm-layout'] ) );
		}

		if ( isset( $_POST['listings_per_page'] ) ) {
			$attributes['listings'] = absint( wp_unslash( $_POST['listings_per_page'] ) );
		}

		if ( isset( $_POST['awsm-hide-expired-jobs'] ) ) {
			$hide_expired                    = sanitize_text_field( wp_unslash( $_POST['awsm-hide-expired-jobs'] ) );
			$hide_expired                    = strtolower( $hide_expired );
			$attributes['hide_expired_jobs'] = in_array( $hide_expired, array( 'expired', '1', 'true', 'yes', 'on' ), true ) ? 'expired' : '';
		}

		if ( isset( $_POST['awsm-other-options'] ) ) {
			$other_options_raw           = sanitize_text_field( wp_unslash( $_POST['awsm-other-options'] ) );
			$other_options               = array_filter(
				array_map(
					'sanitize_key',
					array_map( 'trim', explode( ',', $other_options_raw ) )
				)
			);
			$attributes['other_options'] = implode( ',', $other_options );
		}

		if ( isset( $_POST['awsm-spec-icons'] ) ) {
			$show_spec_icon               = sanitize_text_field( wp_unslash( $_POST['awsm-spec-icons'] ) );
			$show_spec_icon               = strtolower( $show_spec_icon );
			$attributes['show_spec_icon'] = in_array( $show_spec_icon, array( 'show_icon', '1', 'true', 'yes', 'on' ), true ) ? 'show_icon' : '';
		}

		if ( isset( $_POST['lang'] ) ) {
			$lang = sanitize_key( wp_unslash( $_POST['lang'] ) );

			if ( ! empty( $lang ) ) {
				AWSM_Job_Openings::set_current_language( $lang );
			}
		}

		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			// Set as classic pagination.
			$attributes['pagination'] = 'classic';
		} else {
			$attributes['pagination'] = 'modern';
		}

		if ( isset( $_POST['awsm-order-by'] ) ) {
			$order_by               = sanitize_key( wp_unslash( $_POST['awsm-order-by'] ) );
			$attributes['order_by'] = in_array( $order_by, array( 'new_to_old', 'old_to_new' ), true ) ? $order_by : 'new_to_old';
		}

		if ( isset( $_POST['awsm-button-style'] ) ) {
			$attributes['hz_button_style'] = sanitize_key( wp_unslash( $_POST['awsm-button-style'] ) );
		}

		if ( ! empty( $_POST['awsm-button-text'] ) ) {
			$attributes['hz_button_text'] = sanitize_text_field( wp_unslash( $_POST['awsm-button-text'] ) );
		}

		$attributes = apply_filters( 'awsm_jobs_block_restore_selected_terms', $attributes );

		$attributes = apply_filters( 'awsm_jobs_block_post_filters', $attributes, map_deep( wp_unslash( $_POST ), 'sanitize_text_field' ) );

		$args = self::awsm_block_job_query_args( $filters, $attributes, array(), $filters_list );

		if ( isset( $_POST['jq'] ) && ! empty( $_POST['jq'] ) ) {
			$args['s'] = sanitize_text_field( wp_unslash( $_POST['jq'] ) );
		}

		if ( isset( $_POST['paged'] ) ) {
			if ( isset( $_POST['awsm_pagination_base'] ) ) {
				$args['paged'] = absint( wp_unslash( $_POST['paged'] ) );
			} else {
				$args['paged'] = absint( wp_unslash( $_POST['paged'] ) ) + 1;
			}
		}

		$query = new WP_Query( $args );

		$ajax_button_style        = ! empty( $attributes['hz_button_style'] ) ? sanitize_key( $attributes['hz_button_style'] ) : 'none';
		$ajax_button_style_filter = function ( $class ) use ( $ajax_button_style ) {
			return $class . ' is-button-' . $ajax_button_style;
		};
		add_filter( 'awsm_b_job_more_button_class', $ajax_button_style_filter );

		$ajax_button_text        = ! empty( $attributes['hz_button_text'] ) ? $attributes['hz_button_text'] : '';
		$ajax_button_text_filter = null;
		if ( $ajax_button_text ) {
			$ajax_button_text_filter = function () use ( $ajax_button_text ) {
				return esc_html( $ajax_button_text );
			};
			add_filter( 'awsm_b_job_more_button_text', $ajax_button_text_filter );
		}

		ob_start();

		if ( $query->have_posts() ) {
			include AWSM_Job_Openings::get_template_path( 'block-main.php', 'block-files' );
		} else {
			$no_jobs_content = '';
			if ( $filter_action !== 'loadmore' ) {
				$no_jobs_content = sprintf( '<div class="awsm-jobs-none-container awsm-b-jobs-none-container"><p>%s</p></div>', esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' ) );
			} else {
				$no_jobs_content = sprintf( '<div class="awsm-b-jobs-pagination awsm-b-load-more-main awsm-no-more-jobs-container awsm-b-no-more-jobs-container"><p>%s</p></div>', esc_html__( 'Sorry! No more jobs to show.', 'wp-job-openings' ) );
			}
			/**
			 * Filters the HTML content for no jobs when filtered.
			 *
			 * @since 3.5.0
			 *
			 * @param string $no_jobs_content The HTML content.
			 */
			echo apply_filters( 'awsm_block_no_filtered_jobs_content', $no_jobs_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$html = ob_get_clean();

		remove_filter( 'awsm_b_job_more_button_class', $ajax_button_style_filter );
		if ( $ajax_button_text_filter ) {
			remove_filter( 'awsm_b_job_more_button_text', $ajax_button_text_filter );
		}

		ob_start();
		awsm_block_jobs_load_more( $query, $attributes );
		$pagination_html = ob_get_clean();

		wp_send_json_success(
			array(
				'html'            => $html,
				'pagination_html' => $pagination_html,
			)
		);
	}

	public static function awsm_block_job_query_args( $filters = array(), $attributes = array(), $is_term_or_slug = array(), $filters_list = array() ) {
		$args      = array();
		$tax_query = array(); // Initialize a single tax_query array.

		// Handle taxonomy filters for term archives.
		if ( is_tax() ) {
			$q_obj                        = get_queried_object();
			$taxonomy                     = $q_obj->taxonomy;
			$term_id                      = $q_obj->term_id;
			$filters                      = array( $taxonomy => $term_id );
			$is_term_or_slug[ $taxonomy ] = 'term_id';
		}

		$filters_list = apply_filters( 'awsm_jobs_block_selected_terms_query', $filters_list, $attributes, $filters );
		// Process taxonomy filters.
		if ( ! empty( $filters ) || ! empty( $filters_list ) ) {
			$filters      = is_array( $filters ) ? $filters : array();
			$filters_list = is_array( $filters_list ) ? $filters_list : array();
			$all_filters  = array_merge_recursive( $filters, $filters_list );

			foreach ( $all_filters as $taxonomy => $terms ) {
				if ( ! empty( $terms ) ) {
					// Ensure terms are always an array and cleaned.
					$terms = is_array( $terms ) ? array_values( array_filter( $terms ) ) : array( $terms );

					if ( ! empty( $terms ) ) {
						$field_type  = isset( $is_term_or_slug[ $taxonomy ] ) ? $is_term_or_slug[ $taxonomy ] : 'term_id';
						$tax_query[] = array(
							'taxonomy' => $taxonomy,
							'field'    => $field_type,
							'terms'    => $terms,
							'operator' => 'IN',
						);
					}
				}
			}
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		// General query setup.
		$list_per_page          = AWSM_Job_Openings::get_listings_per_page( $attributes );
		$args['post_type']      = 'awsm_job_openings';
		$args['posts_per_page'] = $list_per_page;

		if ( isset( $attributes['hide_expired_jobs'] ) && $attributes['hide_expired_jobs'] === 'expired' ) {
			$args['post_status'] = array( 'publish' );
		} else {
			$args['post_status'] = array( 'publish', 'expired' );
		}

		$sort = isset( $attributes['order_by'] ) ? sanitize_text_field( $attributes['order_by'] ) : 'new_to_old';

		switch ( $sort ) {
			case 'new_to_old':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;

			case 'old_to_new':
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
				break;

			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		// Pagination.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! AWSM_Job_Openings::is_default_pagination( $attributes ) && ! isset( $_POST['awsm_pagination_base'] ) ) {
			// Handle classic pagination on page load.
			if ( is_front_page() || is_home() ) {
				$paged = get_query_var( 'page' ) ? absint( get_query_var( 'page' ) ) : 1;
			} else {
				$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
			}
			$args['paged'] = $paged;
		}

		/**
		 * Filters the arguments for the jobs query.
		 *
		 * @since 3.5.0
		 *
		 * @param array $args arguments.
		 * @param array $filters Applicable filters.
		 * @param array $attributes Block attributes.
		 */
		return apply_filters( 'awsm_job_block_query_args', $args, $filters, $attributes );
	}

	public static function get_block_job_listing_data_attrs( $block_atts = array() ) {
		$attrs = array();
		// Basic attributes
		$attrs['listings']               = AWSM_Job_Openings::get_listings_per_page( $block_atts );
		$attrs['awsm-layout']            = isset( $block_atts['layout'] ) ? $block_atts['layout'] : '';
		$attrs['awsm-hide-expired-jobs'] = isset( $block_atts['hide_expired_jobs'] ) ? $block_atts['hide_expired_jobs'] : '';
		$attrs['awsm-other-options']     = isset( $block_atts['other_options'] ) && is_array( $block_atts['other_options'] )
			? implode( ',', $block_atts['other_options'] )
			: '';
		$attrs['awsm-spec-icons']        = isset( $block_atts['show_spec_icon'] ) ? $block_atts['show_spec_icon'] : '';

		$attrs['awsm-order-by']     = isset( $block_atts['order_by'] ) ? $block_atts['order_by'] : '';
		$attrs['awsm-button-style'] = isset( $block_atts['hz_button_style'] ) ? $block_atts['hz_button_style'] : 'none';
		$attrs['awsm-button-text']  = ! empty( $block_atts['hz_button_text'] ) ? $block_atts['hz_button_text'] : '';

		$current_lang = AWSM_Job_Openings::get_current_language();
		if ( ! empty( $current_lang ) ) {
			$attrs['lang'] = $current_lang;
		}

		if ( isset( $_GET['jq'] ) ) {
			$attrs['search'] = sanitize_text_field( $_GET['jq'] );
		}

		foreach ( $_GET as $key => $value ) {

			// 'jq' is already stored as data-search; adding it again as data-jq
			// causes getListingsData() in view.js to override the live form input
			// value with the stale PHP-rendered value on every filter AJAX call.
			if ( $key === 'jq' ) {
				continue;
			}

			$sanitized_key = sanitize_key( $key );

			if ( is_array( $value ) ) {
				$attrs[ $sanitized_key ] = wp_json_encode( array_map( 'sanitize_text_field', $value ) );
			} elseif ( is_string( $value ) && strpos( $value, ',' ) !== false ) {
				$attrs[ $sanitized_key ] = implode( ',', array_map( 'sanitize_text_field', explode( ',', $value ) ) );
			} else {
				$attrs[ $sanitized_key ] = sanitize_text_field( $value );
			}
		}

		if ( is_tax() ) {
			$q_obj             = get_queried_object();
			$attrs['taxonomy'] = $q_obj->taxonomy;
			$attrs['term-id']  = $q_obj->term_id;
		}

		return apply_filters( 'awsm_block_job_listing_data_attrs', $attrs, $block_atts );
	}

	public static function get_block_filter_specifications( $specs_keys = array() ) {
		$awsm_filters = get_option( 'awsm_jobs_filter' );
		$spec_keys    = wp_list_pluck( $awsm_filters, 'taxonomy' );
		if ( ! is_array( $specs_keys ) ) {
			$specs_keys = explode( ',', $specs_keys );
		}
		$specs = array();
		if ( ! empty( $specs_keys ) ) {
			foreach ( $specs_keys as $spec_key ) {
				$terms = self::get_block_spec_terms( $spec_key );
				if ( ! empty( $terms ) ) {
					$tax_obj = get_taxonomy( $spec_key );
					if ( ! empty( $tax_obj ) ) {
						$specs[] = array(
							'key'              => $spec_key,
							'label'            => $tax_obj->label,
							'terms'            => $terms,
							'expired_term_ids' => self::get_expired_only_term_ids( $spec_key ),
						);
					}
				}
			}
		} else {
			$taxonomy_objects = get_object_taxonomies( 'awsm_job_openings', 'objects' );
			foreach ( $taxonomy_objects as $spec => $spec_details ) {
				if ( ! in_array( $spec, $spec_keys, true ) ) {
					continue;
				}
				$terms = self::get_block_spec_terms( $spec );
				if ( ! empty( $terms ) ) {
					$specs[] = array(
						'key'              => $spec,
						'label'            => $spec_details->label,
						'terms'            => $terms,
						'expired_term_ids' => self::get_expired_only_term_ids( $spec ),
					);
				}
			}
		}
		return $specs;
	}

	public static function get_block_featured_image_size() {
		$image_size_choices = array();
		if ( get_option( 'awsm_jobs_enable_featured_image' ) === 'enable' ) {
			$image_sizes = get_intermediate_image_sizes();
			if ( ! in_array( 'full', $image_sizes, true ) ) {
				$image_sizes[] = 'full';
			}

			foreach ( $image_sizes as $image_size ) {
				$image_size_choices[] = array(
					'value' => $image_size,
					'text'  => $image_size,
				);
			}
		}
		return $image_size_choices;
	}

	public static function get_block_spec_terms( $spec ) {
		$terms_args = array(
			'taxonomy'   => $spec,
			'hide_empty' => false,
		);
		$terms      = get_terms( $terms_args );
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}
		return $terms;
	}

	/**
	 * Returns term IDs for the given taxonomy that have expired posts but no published posts.
	 * Uses WordPress's term count (which only counts published posts) to find zero-count terms,
	 * then checks which of those actually have expired posts.
	 *
	 * @param string $taxonomy Taxonomy key.
	 * @return int[] Array of term IDs.
	 */
	public static function get_expired_only_term_ids( $taxonomy ) {
		global $wpdb;
		$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT DISTINCT tt.term_id
				FROM {$wpdb->term_taxonomy} tt
				WHERE tt.taxonomy = %s
				AND tt.count = 0
				AND EXISTS (
					SELECT 1
					FROM {$wpdb->term_relationships} tr
					JOIN {$wpdb->posts} p ON p.ID = tr.object_id
					WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
					AND p.post_type = 'awsm_job_openings'
					AND p.post_status = 'expired'
				)",
				$taxonomy
			)
		);
		return array_map( 'intval', (array) $ids );
	}

	/**
	 * Get spec terms for the frontend filter dropdown.
	 *
	 * Always returns all terms regardless of job status (expired / excluded / filled).
	 * Hiding jobs in those states applies only to the job listing, not to filter dropdowns.
	 *
	 * @param string $taxonomy Taxonomy key.
	 * @return array Array of WP_Term objects.
	 */
	public static function get_block_filter_terms( $taxonomy ) {
		$terms = self::get_block_spec_terms( $taxonomy );
		return apply_filters( 'awsm_block_filter_terms', $terms, $taxonomy );
	}

	public function display_block_filter_form( $block_atts ) {
		$search_content        = '';
		$specs_filter_content  = '';
		$custom_action_content = '';
		$filters_attr          = isset( $block_atts['filter_options'] ) ? $block_atts['filter_options'] : array();
		$enable_job_filters    = isset( $block_atts['enable_job_filter'] ) ? $block_atts['enable_job_filter'] : '';
		$enable_search         = isset( $block_atts['search'] ) ? $block_atts['search'] : '';
		$placeholder_search    = isset( $block_atts['search_placeholder'] ) ? sanitize_text_field( $block_atts['search_placeholder'] ) : '';
		$default_text          = _x( 'Search Jobs', 'job filter', 'wp-job-openings' );

		/**
		 * Enable search in the job listing or not.
		 *
		 * @since 3.5.0
		 *
		 * @param mixed $enable_search Enable the search or not.
		 * @param array $block_atts The shortcode attributes.
		 */
		$uid = isset( $block_atts['uid'] ) ? '-' . $block_atts['uid'] : '';

		if ( $enable_search === 'enable' ) {
			$search_query = isset( $_GET['jq'] ) ? sanitize_text_field( $_GET['jq'] ) : '';
			/**
			 * Filters the search field placeholder text.
			 *
			 * @since 3.5.0
			 *
			 * @param string $text Placeholder text.
			 */
			$placeholder_text = apply_filters( 'awsm_jobs_block_search_field_placeholder', $placeholder_search ? $placeholder_search : $default_text );

			$search_icon = '<span class="awsm-job-search-btn awsm-b-job-search-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper"><i class="awsm-job-icon-search awsm-b-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-b-job-search-close-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper awsm-b-job-hide"><i class="awsm-job-icon-close-circle awsm-b-job-icon-close-circle"></i></span>';

			$search_content = sprintf( '<div class="awsm-b-filter-item-search"><div class="awsm-b-filter-item-search-in"><label for="awsm-jq%4$s" class="awsm-b-sr-only">%1$s</label><input type="text" id="awsm-jq%4$s" name="jq" value="%2$s" placeholder="%1$s" class="awsm-b-job-search awsm-b-job-form-control">%3$s</div></div>', esc_attr( $placeholder_text ), esc_attr( $search_query ), $search_icon, esc_attr( $uid ) );

			/**
			 * Filters the search field content.
			 *
			 * @since 3.5.0
			 *
			 * @param string $search_content Search field content.
			 */
			$search_content = apply_filters( 'awsm_jobs_block_search_field_content', $search_content );
		}

		$taxonomies = get_object_taxonomies( 'awsm_job_openings', 'objects' );
		// Respect separate toggles: search and filters can be enabled independently.
		$display_filters = ( $enable_job_filters === 'enable' );

		$available_filters = get_option( 'awsm_jobs_listing_available_filters' );

		if ( isset( $block_atts['filter_options'] ) && is_array( $block_atts['filter_options'] ) && ! empty( $block_atts['filter_options'] ) ) {
			$spec_keys = array();
			foreach ( $block_atts['filter_options'] as $option ) {
				// If it's the new format with specKey + value
				if ( is_array( $option ) && isset( $option['specKey'] ) ) {
					$spec_keys[] = $option['specKey'];
				} elseif ( is_string( $option ) ) {
					$spec_keys[] = $option;
				}
			}

			if ( ! empty( $spec_keys ) ) {
				$available_filters = $spec_keys;
			}
		} elseif ( isset( $block_atts['filter_options'] ) && is_array( $block_atts['filter_options'] ) ) {
			// filter_options explicitly set to empty array — no filters should show.
			$available_filters = array();
		}

		$available_filters = is_array( $available_filters ) ? $available_filters : array();
		// Do not force-enable filters when search is enabled.

		$available_filters_arr = array();
		if ( $display_filters && ! empty( $taxonomies ) ) {
			$selected_filters = self::get_block_filters_query_args( $available_filters );
			/**
			 * Modifies the available or active filters to be displayed in the job listing.
			 *
			 * @since 3.5.0
			 *
			 * @param array $available_filters The available filters.
			 * @param array $block_atts The block attributes.
			 */
			$available_filters = apply_filters( 'awsm_active_block_job_filters', $available_filters, $block_atts );
			foreach ( $taxonomies as $taxonomy => $tax_details ) {
				if ( in_array( $taxonomy, $available_filters ) ) {

					/**
					 * Filter arguments for the specification terms in the job filter.
					 *
					 * @since 3.5.0
					 *
					 * @param array $terms_args Array of arguments.
					 */
					$terms = self::get_block_filter_terms( $taxonomy );
					if ( ! empty( $terms ) ) {
						$available_filters_arr[ $taxonomy ] = $tax_details->label;

						$options_content = '';
						foreach ( $terms as $term ) {
							$selected = '';
							$get_key  = str_replace( '-', '__', $taxonomy ) . '_spec';
							if ( isset( $_GET[ $get_key ] ) && is_string( $_GET[ $get_key ] ) ) {
								// URL param takes priority over block preselection (user's explicit choice).
								$selected_specs = explode( ',', sanitize_text_field( wp_unslash( $_GET[ $get_key ] ) ) );
								$selected_specs = array_filter( array_map( 'sanitize_title', array_map( 'trim', $selected_specs ) ) );

								if ( in_array( $term->slug, $selected_specs, true ) ) {
									$selected = ' selected';
								}
							} elseif ( apply_filters( 'awsm_jobs_block_filter_option_is_selected', false, $term, $taxonomy, $block_atts ) ) {
								$selected = ' selected';
							}
							$option_content = sprintf( '<option value="%1$s" data-slug="%3$s"%4$s>%2$s</option>', esc_attr( $term->term_id ), esc_html( $term->name ), esc_attr( $term->slug ), esc_attr( $selected ) );
							/**
							 * Filter the job filter dropdown option content.
							 *
							 * @since 3.5.0
							 *
							 * @param string $option_content Filter dropdown option content.
							 * @param WP_Term $term Job spec term.
							 * @param string $taxonomy Job spec key.
							 */
							$option_content = apply_filters( 'awsm_job_filter_block_option_content', $option_content, $term, $taxonomy );

							$options_content .= $option_content;
						}

						$filter_key = str_replace( '-', '__', $taxonomy );
						$spec_name  = apply_filters( 'wpml_translate_single_string', $tax_details->label, 'WordPress', sprintf( 'taxonomy general name: %s', $tax_details->label ) );
						/**
						 * Filters the default label for the job filter.
						 *
						 * @since 3.5.0
						 *
						 * @param string $filter_label The label for the filter.
						 * @param string $taxonomy Taxonomy key.
						 * @param WP_Taxonomy $tax_details Taxonomy details.
						 */
						$filter_label = apply_filters( 'awsm_filter_block_label', esc_html_x( 'All', 'job filter', 'wp-job-openings' ) . ' ' . $spec_name, $taxonomy, $tax_details );

						$filter_class_admin_select_control = '';
						if ( ! self::is_edit_or_add_page() ) {
							$filter_class_admin_select_control = ' awsm-b-filter-option';
						}

						$spec_multiple_class = '';
						$multiple_for_spec   = '';
						if ( apply_filters( 'awsm_jobs_block_filter_is_multiple', false, $taxonomy, $block_atts ) ) {
							$spec_multiple_class = 'awsm-b-spec-multiple';
							$multiple_for_spec   = 'multiple';
						}

						$dropdown_content = sprintf( '<div class="awsm-b-filter-item" data-filter="%2$s"><label for="awsm-%1$s-filter-option%5$s" class="awsm-b-sr-only">%3$s</label><select name="awsm_job_spec[%1$s][]" class="awsm-b-filter-option ' . $spec_multiple_class . ' awsm-%1$s-filter-option ' . $filter_class_admin_select_control . '" id="awsm-%1$s-filter-option%5$s" aria-label="%3$s" ' . $multiple_for_spec . '><option value="">%3$s</option>%4$s</select></div>', esc_attr( $taxonomy ), esc_attr( $filter_key . '_spec' ), esc_html( $filter_label ), $options_content, esc_attr( $uid ) );
						/**
						 * Filter the job filter dropdown content.
						 *
						 * @since 3.5.0
						 *
						 * @param string $dropdown_content Filter dropdown content.
						 */
						$dropdown_content = apply_filters( 'awsm_job_filter_dropdown_content', $dropdown_content );

						$specs_filter_content .= $dropdown_content;
					}
				}
			}
		}

		$filter_content = '';

		/* Action for custom content for job listing */
		ob_start();
		do_action( 'awsm_block_form_inside', $block_atts );
		$custom_action_content = ob_get_clean();
		/* end */

		if ( ! empty( $search_content ) || ! empty( $specs_filter_content ) ) {
			$current_lang          = AWSM_Job_Openings::get_current_language();
			$hidden_fields_content = '';
			if ( ! empty( $current_lang ) ) {
				$hidden_fields_content .= sprintf( '<input type="hidden" name="lang" value="%s">', esc_attr( $current_lang ) );
			}
			if ( ! AWSM_Job_Openings::is_default_pagination( $block_atts ) ) {
				$paged                  = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				$hidden_fields_content .= sprintf( '<input type="hidden" name="awsm_pagination_base" value="%1$s"><input type="hidden" name="paged" value="%2$s">', esc_url( get_pagenum_link() ), absint( $paged ) );
			}
			$hidden_fields_content .= '<input type="hidden" name="action" value="block_jobfilter">';
			if ( ! empty( $specs_filter_content ) ) {
				$toggle_icon = '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" fill="rgb(9.803922%,9.803922%,9.803922%)" d="M 36.417969 19.9375 L 36.417969 17.265625 C 36.417969 16.160156 35.523438 15.265625 34.417969 15.265625 L 21.578125 15.265625 C 20.476562 15.265625 19.578125 16.160156 19.578125 17.265625 L 19.578125 19.9375 L 11 19.9375 L 11 26.9375 L 19.578125 26.9375 L 19.578125 30.105469 C 19.578125 31.210938 20.476562 32.105469 21.578125 32.105469 L 34.417969 32.105469 C 35.523438 32.105469 36.417969 31.210938 36.417969 30.105469 L 36.417969 26.9375 L 89 26.9375 L 89 19.9375 Z M 58.421875 43.578125 C 58.421875 42.476562 57.527344 41.578125 56.421875 41.578125 L 43.582031 41.578125 C 42.480469 41.578125 41.582031 42.476562 41.582031 43.578125 L 41.582031 46.5 L 11 46.5 L 11 53.5 L 41.582031 53.5 L 41.582031 56.421875 C 41.582031 57.527344 42.480469 58.421875 43.582031 58.421875 L 56.421875 58.421875 C 57.527344 58.421875 58.421875 57.527344 58.421875 56.421875 L 58.421875 53.5 L 89 53.5 L 89 46.5 L 58.421875 46.5 Z M 80.417969 70.140625 C 80.417969 69.035156 79.523438 68.140625 78.417969 68.140625 L 65.578125 68.140625 C 64.476562 68.140625 63.578125 69.035156 63.578125 70.140625 L 63.578125 73.0625 L 11 73.0625 L 11 80.0625 L 63.578125 80.0625 L 63.578125 82.984375 C 63.578125 84.085938 64.476562 84.984375 65.578125 84.984375 L 78.417969 84.984375 C 79.523438 84.984375 80.417969 84.085938 80.417969 82.984375 L 80.417969 80.0625 L 89 80.0625 L 89 73.0625 L 80.417969 73.0625 Z M 80.417969 70.140625"/></svg>';

				$toggle_text_wrapper_class = 'awsm-filter-toggle-text-wrapper';
				if ( $enable_search === 'enable' ) {
					$toggle_text_wrapper_class .= ' awsm-b-sr-only';
				}
				$toggle_control = sprintf( '<span class="%2$s">%1$s</span>%3$s', esc_html_x( 'Filter by', 'job filter', 'wp-job-openings' ), esc_attr( $toggle_text_wrapper_class ), $toggle_icon );
				/**
				 * Filters the HTML content for the specifications toggle button.
				 *
				 * @since 3.5.0
				 *
				 * @param string $toggle_control Toogle button HTML content.
			 */
				$toggle_control = apply_filters( 'awsm_job_filters_block_toggle_btn', $toggle_control );

				$filter_class_admin = '';
				if ( self::is_edit_or_add_page() ) {
					$filter_class_admin = ' awsm-b-filter-admin';
				}

				$custom_action_content_filter = '';
				if ( ! empty( $custom_action_content ) ) {
					$custom_action_content_filter = $custom_action_content;
				}

				$specs_filter_content = sprintf( '<a href="#" class="awsm-b-filter-toggle" role="button" aria-pressed="false">%2$s</a>' . $custom_action_content_filter . '<div class="awsm-b-filter-items' . $filter_class_admin . '">%1$s</div>', $specs_filter_content, $toggle_control );
			}

				$wrapper_class = 'awsm-b-filter-wrap';
				// Hide native selects until Selectric initializes to avoid a flash on page refresh.
			if ( ! self::is_edit_or_add_page() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
				$wrapper_class .= ' awsm-selectric-loading';
			}

			if ( ! $enable_search ) {
				$wrapper_class .= ' awsm-b-no-search-filter-wrap';
			}

			$alert_existing_class = '';
			if ( class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' ) && ! empty( $block_atts['enable_alert'] ) ) {
				$alert_existing_class = ' awsm-jobs-alerts-on';
			}

			$custom_action_content_main = '';
			if ( ! empty( $custom_action_content ) && empty( $specs_filter_content ) ) {
				$custom_action_content_main = $custom_action_content;
			}

			$filter_content = sprintf(
				'<div class="%3$s%5$s" data-placement="top"><form action="%2$s/wp-admin/admin-ajax.php" method="POST">%1$s %4$s</form></div>',
				$search_content . $custom_action_content_main . $specs_filter_content . $hidden_fields_content,
				esc_url( site_url() ),
				esc_attr( $wrapper_class ),
				'',
				$alert_existing_class
			);
		} elseif ( ! empty( $custom_action_content ) ) {
			$filter_content = $custom_action_content;
		}

		/**
		 * Filter the rendered content of the job listings block.
		 *
		 * Allows customization of the job listings filter block content, which includes
		 *
		 * @since 3.5.0
		 *
		 * @param string $filter_content          The generated HTML content for the filter block.
		 * @param array  $available_filters_arr   Array of filters available for the block.
		 */
		echo apply_filters( 'awsm_filter_block_content', $filter_content, $available_filters_arr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function display_block_filter_form_side( $block_atts ) {
		$uid                   = isset( $block_atts['uid'] ) ? '-' . $block_atts['uid'] : '';
		$enable_search         = isset( $block_atts['search'] ) ? $block_atts['search'] : '';
		$enable_job_filters    = isset( $block_atts['enable_job_filter'] ) ? $block_atts['enable_job_filter'] : '';
		$placeholder_search    = isset( $block_atts['search_placeholder'] ) ? sanitize_text_field( $block_atts['search_placeholder'] ) : '';
		$filter_options        = isset( $block_atts['filter_options'] ) ? $block_atts['filter_options'] : '';
		$default_text          = __( 'Search Jobs', 'wp-job-openings' );
		$search_content        = '';
		$available_filters     = array();
		$available_filters_arr = array();
		$specs_filter_content  = '';

		$hidden_fields_content = '<input type="hidden" name="action" value="block_jobfilter">';

		if ( ! AWSM_Job_Openings::is_default_pagination( $block_atts ) ) {
			$paged                  = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			$hidden_fields_content .= sprintf( '<input type="hidden" name="awsm_pagination_base" value="%1$s"><input type="hidden" name="paged" value="%2$s">', esc_url( get_pagenum_link() ), absint( $paged ) );
		}

		if ( $enable_search === 'enable' ) {
			$search_query     = isset( $_GET['jq'] ) ? sanitize_text_field( $_GET['jq'] ) : '';
			$placeholder_text = apply_filters( 'awsm_jobs_block_search_field_side_placeholder', $placeholder_search ? $placeholder_search : $default_text );

			$search_icon = '<span class="awsm-job-search-btn awsm-b-job-search-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper"><i class="awsm-job-icon-search awsm-b-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-b-job-search-close-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper awsm-b-job-hide"><i class="awsm-job-icon-close-circle awsm-b-job-icon-close-circle"></i></span>';

			$search_content = sprintf( '<div class="awsm-b-filter-item-search"><div class="awsm-b-filter-item-search-in"><label for="awsm-jq-1" class="awsm-b-sr-only">%1$s</label><input type="text" id="awsm-jq%4$s" name="jq" value="%2$s" placeholder="%1$s" class="awsm-b-job-search awsm-b-job-form-control">%3$s</div></div>', esc_attr( $placeholder_text ), esc_attr( $search_query ), $search_icon, esc_attr( $uid ) );

			$search_content = apply_filters( 'awsm_jobs_block_search_field_content_placement_side', $search_content );
		}

		$taxonomies       = get_object_taxonomies( 'awsm_job_openings', 'objects' );
		$selected_filters = self::get_block_filters_query_args( $available_filters );

		// Respect the legacy separate toggle: only build the filter dropdowns when enabled.
		if ( $enable_job_filters === 'enable' && ! empty( $taxonomies ) && is_array( $filter_options ) && ! empty( $filter_options ) ) {
			foreach ( $taxonomies as $taxonomy => $tax_details ) {
				foreach ( $filter_options as $spec ) {
					if ( ( is_string( $spec ) && $taxonomy === $spec ) || ( is_array( $spec ) && isset( $spec['specKey'] ) && $taxonomy === $spec['specKey'] ) ) {
						// Get terms for the taxonomy
						$terms = self::get_block_filter_terms( $taxonomy );

						if ( ! empty( $terms ) ) {
							$available_filters_arr[ $taxonomy ] = $tax_details->label;

							$options_content = '';
							foreach ( $terms as $term ) {
								$selected = '';
								$get_key  = str_replace( '-', '__', $taxonomy ) . '_spec';
								if ( isset( $_GET[ $get_key ] ) && is_string( $_GET[ $get_key ] ) ) {
									// URL param takes priority over block preselection (user's explicit choice).
									$selected_specs = explode( ',', sanitize_text_field( wp_unslash( $_GET[ $get_key ] ) ) );
									$selected_specs = array_filter( array_map( 'sanitize_title', array_map( 'trim', $selected_specs ) ) );
									if ( in_array( $term->slug, $selected_specs, true ) ) {
										$selected = ' selected';
									}
								} elseif ( apply_filters( 'awsm_jobs_block_filter_option_is_selected', false, $term, $taxonomy, $block_atts ) ) {
									$selected = ' selected';
								}

								$option_content = sprintf( '<option value="%1$s" data-slug="%3$s"%4$s>%2$s</option>', esc_attr( $term->term_id ), esc_html( $term->name ), esc_attr( $term->slug ), esc_attr( $selected ) );
								/**
								 * Filter the job filter dropdown option content.
								 *
								 * @since 3.5.0
								 *
								 * @param string $option_content Filter dropdown option content.
								 * @param WP_Term $term Job spec term.
								 * @param string $taxonomy Job spec key.
								 */
								$option_content = apply_filters( 'awsm_job_filter_block_option_content', $option_content, $term, $taxonomy );

								$options_content .= $option_content;
							}

							$filter_key = str_replace( '-', '__', $taxonomy );
							$spec_name  = apply_filters( 'wpml_translate_single_string', $tax_details->label, 'WordPress', sprintf( 'taxonomy general name: %s', $tax_details->label ) );

							/**
							 * Filters the default label for the job filter.
							 *
							 * @since 3.5.0
							 *
							 * @param string $main_spec_label The label for the filter.
							 * @param string $taxonomy Taxonomy key.
							 * @param WP_Taxonomy $tax_details Taxonomy details.
							 */
							$main_spec_label = apply_filters(
								'awsm_filter_block_label',
								esc_html( $spec_name ),
								$taxonomy,
								$tax_details
							);

							/**
							 * Filters the default label for the dropdown job filter.
							 *
							 * @since 3.5.0
							 *
							 * @param string $all_spec_label The label for the all filter.
							 * @param string $taxonomy Taxonomy key.
							 * @param WP_Taxonomy $tax_details Taxonomy details.
							 */
							$all_spec_label = apply_filters(
								'awsm_filter_block_dropdown_label',
								esc_html_x( 'All', 'job filter', 'wp-job-openings' ) . ' ' . esc_html( $spec_name ),
								$taxonomy,
								$tax_details
							);

							$filter_class_admin_select_control = '';
							if ( ! self::is_edit_or_add_page() ) {
								$filter_class_admin_select_control = ' awsm-b-filter-option';
							}

							$spec_multiple_class = '';
							$multiple_for_spec   = '';
							if ( apply_filters( 'awsm_jobs_block_filter_is_multiple', false, $taxonomy, $block_atts ) ) {
								$spec_multiple_class = 'awsm-b-spec-multiple';
								$multiple_for_spec   = 'multiple';
							}

							$label_class_name = '';
							if ( self::is_edit_or_add_page() ) {
								$label_class_name = 'awsm-b-sr-only';
							}

							$label_text = self::is_edit_or_add_page()
							? $all_spec_label   // Block editor preview
							: $main_spec_label; // Frontend

							$dropdown_content = sprintf(
								'<div class="awsm-b-filter-item" data-filter="%2$s">
									%11$s
									<label for="awsm-%1$s-filter-option%6$s" class="%7$s">%3$s</label>
									<select name="awsm_job_spec[%1$s][]" 
										class="awsm-b-filter-option %8$s awsm-%1$s-filter-option %9$s" 
										id="awsm-%1$s-filter-option%6$s" 
										aria-label="%3$s" %10$s>
										<option value="All">%4$s</option>
										%5$s
									</select>
								</div>',
								esc_attr( $taxonomy ),
								esc_attr( $filter_key . '_spec' ),
								esc_html( $label_text ),
								esc_html( $all_spec_label ),
								$options_content,
								esc_attr( $uid ),
								esc_attr( $label_class_name ),
								esc_attr( $spec_multiple_class ),
								esc_attr( $filter_class_admin_select_control ),
								esc_attr( $multiple_for_spec ),
								self::is_edit_or_add_page() ? '<div>' . esc_html( $main_spec_label ) . '</div>' : ''
							);

							/**
							 * Filter the job filter dropdown content.
							 *
							 * @since 3.5.0
							 *
							 * @param string $dropdown_content Filter dropdown content.
							 */
							$dropdown_content = apply_filters( 'awsm_job_filter_dropdown_content', $dropdown_content );

							$specs_filter_content .= $dropdown_content;
						}
					}
				}
			}
		}

		$filter_class_admin = '';
		if ( self::is_edit_or_add_page() ) {
			$filter_class_admin = ' awsm-b-filter-admin';
		}

		/* Action for custom content for job listing */
		ob_start();
		do_action( 'awsm_block_form_inside', $block_atts );
		$custom_action_content = ob_get_clean();
		/* end */

		if ( ! empty( $specs_filter_content ) ) {
			$toggle_icon = '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" fill="rgb(9.803922%,9.803922%,9.803922%)" d="M 36.417969 19.9375 L 36.417969 17.265625 C 36.417969 16.160156 35.523438 15.265625 34.417969 15.265625 L 21.578125 15.265625 C 20.476562 15.265625 19.578125 16.160156 19.578125 17.265625 L 19.578125 19.9375 L 11 19.9375 L 11 26.9375 L 19.578125 26.9375 L 19.578125 30.105469 C 19.578125 31.210938 20.476562 32.105469 21.578125 32.105469 L 34.417969 32.105469 C 35.523438 32.105469 36.417969 31.210938 36.417969 30.105469 L 36.417969 26.9375 L 89 26.9375 L 89 19.9375 Z M 58.421875 43.578125 C 58.421875 42.476562 57.527344 41.578125 56.421875 41.578125 L 43.582031 41.578125 C 42.480469 41.578125 41.582031 42.476562 41.582031 43.578125 L 41.582031 46.5 L 11 46.5 L 11 53.5 L 41.582031 53.5 L 41.582031 56.421875 C 41.582031 57.527344 42.480469 58.421875 43.582031 58.421875 L 56.421875 58.421875 C 57.527344 58.421875 58.421875 57.527344 58.421875 56.421875 L 58.421875 53.5 L 89 53.5 L 89 46.5 L 58.421875 46.5 Z M 80.417969 70.140625 C 80.417969 69.035156 79.523438 68.140625 78.417969 68.140625 L 65.578125 68.140625 C 64.476562 68.140625 63.578125 69.035156 63.578125 70.140625 L 63.578125 73.0625 L 11 73.0625 L 11 80.0625 L 63.578125 80.0625 L 63.578125 82.984375 C 63.578125 84.085938 64.476562 84.984375 65.578125 84.984375 L 78.417969 84.984375 C 79.523438 84.984375 80.417969 84.085938 80.417969 82.984375 L 80.417969 80.0625 L 89 80.0625 L 89 73.0625 L 80.417969 73.0625 Z M 80.417969 70.140625"/></svg>';

			$toggle_text_wrapper_class = 'awsm-filter-toggle-text-wrapper';
			if ( $enable_search === 'enable' ) {
				$toggle_text_wrapper_class .= ' awsm-b-sr-only';
			}

			$toggle_control = sprintf( '<span class="%2$s">%1$s</span>%3$s', esc_html_x( 'Filter by', 'job filter', 'wp-job-openings' ), esc_attr( $toggle_text_wrapper_class ), $toggle_icon );

			$toggle_control = apply_filters( 'awsm_job_filters_block_toggle_btn', $toggle_control );

			$filter_class_admin = '';
			if ( self::is_edit_or_add_page() ) {
				$filter_class_admin = ' awsm-b-filter-admin';
			}

			$specs_filter_content = sprintf(
				'<a href="#" class="awsm-b-filter-toggle" role="button" aria-pressed="false">%2$s</a><div class="awsm-b-filter-items%3$s">%1$s</div>',
				$specs_filter_content,
				$toggle_control,
				$filter_class_admin
			);
		}

		$filter_content = '';

		if ( ! empty( $search_content ) || ! empty( $specs_filter_content ) ) {
			$filter_content = sprintf(
				'<form action="%2$s/wp-admin/admin-ajax.php" method="POST">%1$s</form>',
				$search_content . $specs_filter_content . $hidden_fields_content,
				esc_url( site_url() )
			);
		}

		// Output the filter form content
		echo apply_filters( 'awsm_filter_block_content_placement_side', $filter_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Output the custom action content (e.g. alerts button) outside the form, below the sidebar box.
		if ( ! empty( $custom_action_content ) ) {
			echo $custom_action_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public static function get_specifications_content_block( $post_id, $display_label, $filter_data = array(), $listing_specs = array(), $has_term_link = true, $show_icon = '' ) {
		$spec_content = '';
		$filter_data  = ! empty( $filter_data ) ? $filter_data : get_option( 'awsm_jobs_filter' );
		// Normalize to the expected array-of-arrays shape to avoid PHP notices in REST responses.
		$filter_data = is_array( $filter_data ) ? $filter_data : array();
		$filter_data = array_values(
			array_filter(
				$filter_data,
				static function ( $f ) {
					return is_array( $f ) && ! empty( $f['taxonomy'] );
				}
			)
		);

		if ( ! empty( $filter_data ) ) {
			$spec_keys          = wp_list_pluck( $filter_data, 'taxonomy' );
			$taxonomies         = get_object_taxonomies( 'awsm_job_openings', 'objects' );
			$is_specs_clickable = get_option( 'awsm_jobs_make_specs_clickable' );

			foreach ( $taxonomies as $taxonomy => $options ) {
				if ( ! in_array( $taxonomy, $spec_keys, true ) ) {
					continue;
				}

				$display = true;
				if ( ! empty( $listing_specs ) ) {
					$display = false;
					if ( isset( $listing_specs['specs'] ) && is_array( $listing_specs['specs'] ) && in_array( $taxonomy, $listing_specs['specs'] ) ) {
						$display = true;
					}
				}

				if ( $display ) {
					$terms = get_the_terms( $post_id, $taxonomy );

					/** Filter the job specification terms. */
					$terms = apply_filters( 'awsm_block_job_spec_terms', $terms, $post_id, $taxonomy );

					if ( $terms !== false && ( ! is_wp_error( $terms ) ) ) {
						$spec_label = $spec_icon = $spec_terms = ''; // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

						if ( $display_label ) {
							$spec_name  = apply_filters( 'wpml_translate_single_string', $options->label, 'WordPress', sprintf( 'taxonomy general name: %s', $options->label ) );
							$spec_label = '<span class="awsm-job-specification-label"><strong>' . $spec_name . ': </strong></span>';
						}

						// Get icon and filter data
						$current_filter = null;
						foreach ( $filter_data as $filter ) {
							if ( $taxonomy === $filter['taxonomy'] ) {
								$current_filter = $filter;
								if ( ! empty( $filter['icon'] ) ) {
									$is_single_job = get_post_type( $post_id ) === 'awsm_job_openings';
									if ( ! $is_single_job || $show_icon === 'show_icon' ) {
										$spec_icon = sprintf( '<i class="awsm-job-icon-%1$s"></i>', esc_attr( $filter['icon'] ) );
									}
								}
								break;
							}
						}

						// Create ordered terms array based on filter tags
						$ordered_terms = array();
						if ( $current_filter && ! empty( $current_filter['tags'] ) ) {
							// Create a map of term names to term objects
							$term_map = array();
							foreach ( $terms as $term ) {
								$term_map[ $term->name ] = $term;
							}

							// Add terms in the order specified by tags
							foreach ( $current_filter['tags'] as $tag ) {
								if ( isset( $term_map[ $tag ] ) ) {
									$ordered_terms[] = $term_map[ $tag ];
									unset( $term_map[ $tag ] );
								}
							}

							// Add any remaining terms that weren't in the filter tags
							foreach ( $term_map as $term ) {
								$ordered_terms[] = $term;
							}
						} else {
							$ordered_terms = $terms;
						}

						// Generate terms HTML
						foreach ( $ordered_terms as $term ) {
							$term_link = get_term_link( $term );
							if ( ! is_singular( 'awsm_job_openings' ) || $is_specs_clickable !== 'make_clickable' || is_wp_error( $term_link ) || ! $has_term_link ) {
								$spec_terms .= '<span class="awsm-job-specification-term">' . esc_html( $term->name ) . '</span> ';
							} else {
								$spec_terms .= sprintf( '<a href="%2$s" class="awsm-job-specification-term">%1$s</a> ', esc_html( $term->name ), esc_url( $term_link ) );
							}
						}

						$spec_item_content = sprintf( '<div class="awsm-job-specification-item awsm-job-specification-%2$s">%1$s</div>', $spec_icon . $spec_label . $spec_terms, esc_attr( $taxonomy ) );

						/** Filters the job specification item content. */
						$spec_item_content = apply_filters( 'awsm_block_job_spec_item_content', $spec_item_content, $post_id, $taxonomy );
						$spec_content     .= $spec_item_content;
					}
				}
			}
		}

		if ( ! empty( $spec_content ) ) {
			$spec_content = sprintf( '<div class="awsm-job-specification-wrapper">%1$s</div>', $spec_content );
		}

		$spec_content = apply_filters_deprecated( 'awsm_block_specification_content', array( $spec_content, $post_id ), '2.3.0', 'awsm_job_specs_content' );

		/** Filters the job specifications content. */
		return apply_filters( 'awsm_block_job_specs_content', $spec_content, $post_id );
	}
}

AWSM_Job_Openings_Block::init();
