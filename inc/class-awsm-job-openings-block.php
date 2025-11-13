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
			'uid'                => $this->unique_listing_id,
			'layout'             => isset( $blockatts['layout'] ) ? $blockatts['layout'] : '',
			'filter_options'     => isset( $blockatts['filter_options'] ) ? $blockatts['filter_options'] : '',
			'other_options'      => isset( $blockatts['other_options'] ) ? $blockatts['other_options'] : '',
			'search'             => isset( $blockatts['search'] ) ? $blockatts['search'] : '',
			'listings'           => isset( $blockatts['listing_per_page'] ) ? $blockatts['listing_per_page'] : 10,
			'number_of_columns'  => isset( $blockatts['number_of_columns'] ) ? $blockatts['number_of_columns'] : 3,
			'block_loadmore'     => 'yes',
			'pagination'         => isset( $blockatts['pagination'] ) ? $blockatts['pagination'] : 'modern',
			'enable_job_filter'  => isset( $blockatts['enable_job_filter'] ) ? $blockatts['enable_job_filter'] : '',
			'search_placeholder' => isset( $blockatts['search_placeholder'] ) ? $blockatts['search_placeholder'] : '',
			'hide_expired_jobs'  => isset( $blockatts['hide_expired_jobs'] ) ? $blockatts['hide_expired_jobs'] : '',
			'select_filter_full' => isset( $blockatts['select_filter_full'] ) ? $blockatts['select_filter_full'] : '',
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

		$this->unique_listing_id++;

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
		$view       = $attributes['layout'];
		$view_class = 'awsm-b-lists';
		if ( $view === 'grid' ) {
			$number_columns = isset( $attributes['number_of_columns'] ) && ! empty( $attributes['number_of_columns'] ) ? $attributes['number_of_columns'] : 3;
			$view_class     = 'awsm-b-row';
			$column_class   = 'awsm-b-grid-col-' . $number_columns;
			if ( $number_columns == 1 ) {
				$column_class = 'awsm-b-grid-col';
			}
			$view_class .= ' ' . $column_class;
		}
		return sprintf( 'awsm-b-job-listings %s', $view_class );
	}

	public function display_block_filter_form( $block_atts ) {
		$search_content        = '';
		$specs_filter_content  = '';
		$custom_action_content = '';
		$filters_attr          = isset( $block_atts['filter_options'] ) ? $block_atts['filter_options'] : '';
		$enable_job_filters    = isset( $block_atts['enable_job_filter'] ) ? $block_atts['enable_job_filter'] : '';
		$enable_search         = isset( $block_atts['search'] ) ? $block_atts['search'] : '';
		$placeholder_search    = isset( $block_atts['search_placeholder'] ) ? $block_atts['search_placeholder'] : '';
		$select_filter_full    = isset( $block_atts['select_filter_full'] ) ? $block_atts['select_filter_full'] : '';

		$placeholder_search = isset( $block_atts['search_placeholder'] ) ? $block_atts['search_placeholder'] : '';
		$default_text       = _x( 'Search Jobs', 'job filter', 'wp-job-openings' );

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
			$search_query = isset( $_GET['jq'] ) ? $_GET['jq'] : '';
			/**
			 * Filters the search field placeholder text.
			 *
			 * @since 3.5.0
			 *
			 * @param string $text Placeholder text.
			 */
			$placeholder_text = apply_filters( 'awsm_jobs_block_search_field_placeholder', $placeholder_search ? $placeholder_search : $default_text );

			$search_icon = '<span class="awsm-job-search-btn awsm-b-job-search-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper"><i class="awsm-job-icon-search awsm-b-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-b-job-search-close-btn awsm-job-search-icon-wrapper awsm-b-job-search-icon-wrapper awsm-job-hide awsm-b-job-hide"><i class="awsm-job-icon-close-circle awsm-b-job-icon-close-circle"></i></span>';

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

		$taxonomies      = get_object_taxonomies( 'awsm_job_openings', 'objects' );
		$display_filters = true;
		if ( $enable_job_filters !== 'enable' || $filters_attr === '' ) {
			$display_filters = false;
		}

		$available_filters = get_option( 'awsm_jobs_listing_available_filters' );

		$available_filters = explode( ',', $block_atts['filter_options'] );
		$available_filters = is_array( $available_filters ) ? $available_filters : array();
		if ( empty( $available_filters ) ) {
			$display_filters = true;
		}

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
					$terms_args = apply_filters(
						'awsm_filter_block_spec_terms_args',
						array(
							'taxonomy'   => $taxonomy,
							'orderby'    => 'name',
							'hide_empty' => true,
						)
					);
						$terms  = get_terms( $terms_args );
					if ( ! empty( $terms ) ) {
						$available_filters_arr[ $taxonomy ] = $tax_details->label;

						$options_content = '';
						foreach ( $terms as $term ) {
							$selected = '';
							if ( in_array( $taxonomy, array_keys( $selected_filters ) ) && $selected_filters[ $taxonomy ] === $term->slug ) {
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
						$filter_label                      = apply_filters( 'awsm_filter_block_label', esc_html_x( 'All', 'job filter', 'wp-job-openings' ) . ' ' . $spec_name, $taxonomy, $tax_details );
						$filter_class_admin_select_control = '';
						if ( ! self::is_edit_or_add_page() ) {
							$filter_class_admin_select_control = ' awsm-job-select-control';
						}

						$dropdown_content = sprintf( '<div class="awsm-b-filter-item" data-filter="%2$s"><label for="awsm-%1$s-filter-option%5$s" class="awsm-b-sr-only">%3$s</label><select name="awsm_job_spec[%1$s]" class="awsm-b-filter-option awsm-%1$s-filter-option ' . $filter_class_admin_select_control . '" id="awsm-%1$s-filter-option%5$s" aria-label="%3$s"><option value="">%3$s</option>%4$s</select></div>', esc_attr( $taxonomy ), esc_attr( $filter_key . '_spec' ), esc_html( $filter_label ), $options_content, esc_attr( $uid ) );
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
			if ( ! $enable_search ) {
				$wrapper_class .= ' awsm-b-no-search-filter-wrap';
			}

			if ( self::is_edit_or_add_page() && $select_filter_full ) {
				$wrapper_class .= ' awsm-b-full-width-search-filter-wrap';
			}

			$alert_existing_class = '';
			if ( class_exists( 'AWSM_Job_Openings_Alert_Main_Blocks' ) ) {
				$alert_existing_class = ' awsm-jobs-alerts-on';
			}

			$custom_action_content_main = '';
			if ( ! empty( $custom_action_content ) && empty( $specs_filter_content ) ) {
				$custom_action_content_main = $custom_action_content;
			}

			$filter_content = sprintf(
				'<div class="%3$s%5$s"><form action="%2$s/wp-admin/admin-ajax.php" method="POST">%1$s %4$s</form></div>',
				$search_content . $custom_action_content_main . $specs_filter_content . $hidden_fields_content,
				esc_url( site_url() ),
				esc_attr( $wrapper_class ),
				'',
				$alert_existing_class
			);
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
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		$filters = $attributes = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

		$filter_action = isset( $_POST['action'] ) ? $_POST['action'] : '';

		if ( ! empty( $_POST['awsm_job_spec'] ) ) {
			$job_specs = $_POST['awsm_job_spec'];
			foreach ( $job_specs as $taxonomy => $term_id ) {
				$taxonomy             = sanitize_text_field( $taxonomy );
				$filters[ $taxonomy ] = intval( $term_id );
			}
		}

		if ( ! empty( $_POST['awsm-layout'] ) ) {
			$attributes['layout'] = sanitize_text_field( $_POST['awsm-layout'] );
		}

		if ( isset( $_POST['listings_per_page'] ) ) {
			$attributes['listings'] = intval( $_POST['listings_per_page'] );
		}

		if ( isset( $_POST['awsm-hide-expired-jobs'] ) ) {
			$attributes['hide_expired_jobs'] = $_POST['awsm-hide-expired-jobs'];
		}

		if ( isset( $_POST['awsm-other-options'] ) ) {
			$attributes['other_options'] = $_POST['awsm-other-options'];
		}

		if ( isset( $_POST['lang'] ) ) {
			AWSM_Job_Openings::set_current_language( $_POST['lang'] );
		}

		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			// Set as classic pagination.
			$attributes['pagination'] = 'classic';
		} else {
			$attributes['pagination'] = 'modern';
		}

		$attributes = apply_filters( 'awsm_jobs_block_post_filters', $attributes, $_POST );

		$args = self::awsm_block_job_query_args( $filters, $attributes );

		if ( isset( $_POST['jq'] ) && ! empty( $_POST['jq'] ) ) {
			$args['s'] = sanitize_text_field( $_POST['jq'] );
		}

		if ( isset( $_POST['paged'] ) ) {
			if ( isset( $_POST['awsm_pagination_base'] ) ) {
				$args['paged'] = absint( $_POST['paged'] );
			} else {
				$args['paged'] = absint( $_POST['paged'] ) + 1;
			}
		}

		$query = new WP_Query( $args );

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
		wp_die();
		// phpcs:enable
	}

	public static function awsm_block_job_query_args( $filters = array(), $attributes = array() ) {
		$args = array();
		if ( is_tax() ) {
			$q_obj    = get_queried_object();
			$taxonomy = $q_obj->taxonomy;
			$term_id  = $q_obj->term_id;
			$filters  = array( $taxonomy => $term_id );
		}

		if ( ! empty( $filters ) ) {
			foreach ( $filters as $taxonomy => $term_id ) {
				if ( ! empty( $term_id ) ) {
					$spec                = array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_id,
					);
					$args['tax_query'][] = $spec;
				}
			}
		}

		$list_per_page          = AWSM_Job_Openings::get_listings_per_page( $attributes );
		$args['post_type']      = 'awsm_job_openings';
		$args['posts_per_page'] = $list_per_page;
		if ( isset( $attributes['hide_expired_jobs'] ) && $attributes['hide_expired_jobs'] === 'expired' ) {
			if ( $list_per_page > 0 ) {
				$args['post_status'] = array( 'publish' );
			} else {
				$args['numberposts'] = -1;
			}
		} else {
			$args['post_status'] = array( 'publish', 'expired' );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! AWSM_Job_Openings::is_default_pagination( $attributes ) && ! isset( $_POST['awsm_pagination_base'] ) ) {
			// Handle classic pagination on page load.
			$paged         = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
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
		$attrs                           = array();
		$attrs['listings']               = AWSM_Job_Openings::get_listings_per_page( $block_atts );
		$attrs['awsm-layout']            = isset( $block_atts['layout'] ) ? $block_atts['layout'] : '';
		$attrs['awsm-hide-expired-jobs'] = isset( $block_atts['hide_expired_jobs'] ) ? $block_atts['hide_expired_jobs'] : '';
		$attrs['awsm-other-options']     = isset( $block_atts['other_options'] ) ? $block_atts['other_options'] : '';

		$current_lang = AWSM_Job_Openings::get_current_language();
		if ( ! empty( $current_lang ) ) {
			$attrs['lang'] = $current_lang;
		}

		if ( isset( $_GET['jq'] ) ) {
			$attrs['search'] = $_GET['jq'];
		}

		if ( is_tax() ) {
			$q_obj             = get_queried_object();
			$attrs['taxonomy'] = $q_obj->taxonomy;
			$attrs['term-id']  = $q_obj->term_id;
		}
		/**
		 * Filters the data attributes for the job listings div element.
		 *
		 * @since 3.5.0
		 *
		 * @param array $attrs The data attributes.
		 * @param array $block_atts The block attributes.
		 */
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
							'key'   => $spec_key,
							'label' => $tax_obj->label,
							'terms' => $terms,
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
						'key'   => $spec,
						'label' => html_entity_decode( $spec_details->label ),
						'terms' => $terms,
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
}

AWSM_Job_Openings_Block::init();

