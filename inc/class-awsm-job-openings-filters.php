<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Filters {
	private static $instance = null;

	protected static $filter_suffix = '_spec';

	public function __construct() {
		add_action( 'awsm_filter_form', array( $this, 'display_filter_form' ) );
		add_action( 'wp_ajax_jobfilter', array( $this, 'awsm_posts_filters' ) );
		add_action( 'wp_ajax_nopriv_jobfilter', array( $this, 'awsm_posts_filters' ) );
		add_action( 'wp_ajax_loadmore', array( $this, 'awsm_posts_filters' ) );
		add_action( 'wp_ajax_nopriv_loadmore', array( $this, 'awsm_posts_filters' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function get_filters_query_args( $filters = false ) {
		$query_args = array();
		if ( empty( $filters ) ) {
			$filters = get_option( 'awsm_jobs_listing_available_filters' );
		}
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$current_filter_key = str_replace( '-', '__', $filter ) . self::$filter_suffix;
				if ( isset( $_GET[ $current_filter_key ] ) ) {
					$query_args[ $filter ] = sanitize_title( $_GET[ $current_filter_key ] );
				}
			}
		}
		return $query_args;
	}

	public function display_filter_form( $shortcode_atts ) {
		$search_content       = '';
		$specs_filter_content = '';
		$filters_attr         = isset( $shortcode_atts['filters'] ) ? $shortcode_atts['filters'] : '';
		$enable_job_filters   = get_option( 'awsm_enable_job_filter_listing' );
		$enable_search        = get_option( 'awsm_enable_job_search' );

		/**
		 * Enable search in the job listing or not.
		 *
		 * @since 2.2.0
		 *
		 * @param mixed $enable_search Enable the search or not.
		 * @param array $shortcode_atts The shortcode attributes.
		 */
		$enable_search = apply_filters( 'awsm_job_filters_enable_search', $enable_search, $shortcode_atts );

		if ( $enable_job_filters !== 'enabled' && $filters_attr !== 'yes' && $enable_search !== 'enable' ) {
			return;
		}

		if ( is_archive() && ! is_post_type_archive( 'awsm_job_openings' ) ) {
			return;
		}

		$uid = isset( $shortcode_atts['uid'] ) ? '-' . $shortcode_atts['uid'] : '';

		if ( $enable_search === 'enable' ) {
			$search_query = isset( $_GET['jq'] ) ? $_GET['jq'] : '';
			/**
			 * Filters the search field placeholder text.
			 *
			 * @since 1.6.0
			 *
			 * @param string $text Placeholder text.
			 */
			$placeholder_text = apply_filters( 'awsm_jobs_search_field_placeholder', _x( 'Search', 'job filter', 'wp-job-openings' ) );
			$search_icon      = '<span class="awsm-job-search-btn awsm-job-search-icon-wrapper"><i class="awsm-job-icon-search"></i></span><span class="awsm-job-search-close-btn awsm-job-search-icon-wrapper awsm-job-hide"><i class="awsm-job-icon-close-circle"></i></span>';
			$search_content   = sprintf( '<div class="awsm-filter-item-search"><div class="awsm-filter-item-search-in"><label for="awsm-jq%4$s" class="awsm-sr-only">%1$s</label><input type="text" id="awsm-jq%4$s" name="jq" value="%2$s" placeholder="%1$s" class="awsm-job-search awsm-job-form-control">%3$s</div></div>', esc_attr( $placeholder_text ), esc_attr( $search_query ), $search_icon, esc_attr( $uid ) );
			/**
			 * Filters the search field content.
			 *
			 * @since 1.6.0
			 *
			 * @param string $search_content Search field content.
			 */
			$search_content = apply_filters( 'awsm_jobs_search_field_content', $search_content );
		}

		$taxonomies = get_object_taxonomies( 'awsm_job_openings', 'objects' );

		$display_filters = true;
		if ( $enable_job_filters !== 'enabled' || $filters_attr === 'no' ) {
			$display_filters = false;
		}
		// Hide filters if specs shortcode attribute is applied.
		if ( ! empty( $shortcode_atts['specs'] ) ) {
			$display_filters = false;
		}

		$available_filters = get_option( 'awsm_jobs_listing_available_filters' );
		$available_filters = is_array( $available_filters ) ? $available_filters : array();
		if ( empty( $available_filters ) ) {
			$display_filters = false;
		}

		/**
		 * Modifies the visibility for the filters in the job listing.
		 *
		 * @since 2.2.0
		 *
		 * @param bool $is_visible Whether the filters is visible or not.
		 * @param array $shortcode_atts The shortcode attributes.
		 */
		$display_filters = apply_filters( 'awsm_is_job_filters_visible', $display_filters, $shortcode_atts );

		$available_filters_arr = array();
		if ( $display_filters && ! empty( $taxonomies ) ) {
			$selected_filters = self::get_filters_query_args( $available_filters );
			/**
			 * Modifies the available or active filters to be displayed in the job listing.
			 *
			 * @since 2.2.0
			 *
			 * @param array $available_filters The available filters.
			 * @param array $shortcode_atts The shortcode attributes.
			 */
			$available_filters = apply_filters( 'awsm_active_job_filters', $available_filters, $shortcode_atts );
			foreach ( $taxonomies as $taxonomy => $tax_details ) {
				if ( in_array( $taxonomy, $available_filters ) ) {
					/**
					 * Filter arguments for the specification terms in the job filter.
					 *
					 * @since 2.0.0
					 *
					 * @param array $terms_args Array of arguments.
					 */
					$terms_args = apply_filters(
						'awsm_filter_spec_terms_args',
						array(
							'taxonomy'   => $taxonomy,
							'orderby'    => 'name',
							'hide_empty' => true,
						)
					);
					$terms      = get_terms( $terms_args );
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
							 * @since 3.3.0
							 *
							 * @param string $option_content Filter dropdown option content.
							 * @param WP_Term $term Job spec term.
							 * @param string $taxonomy Job spec key.
							 */
							$option_content = apply_filters( 'awsm_job_filter_option_content', $option_content, $term, $taxonomy );

							$options_content .= $option_content;
						}

							$filter_key = str_replace( '-', '__', $taxonomy );
							$spec_name  = apply_filters( 'wpml_translate_single_string', $tax_details->label, 'WordPress', sprintf( 'taxonomy general name: %s', $tax_details->label ) );
							/**
							 * Filters the default label for the job filter.
							 *
							 * @since 1.6.0
							 *
							 * @param string $filter_label The label for the filter.
							 * @param string $taxonomy Taxonomy key.
							 * @param WP_Taxonomy $tax_details Taxonomy details.
							 */
							$filter_label = apply_filters( 'awsm_filter_label', esc_html_x( 'All', 'job filter', 'wp-job-openings' ) . ' ' . $spec_name, $taxonomy, $tax_details );

							$dropdown_content = sprintf( '<div class="awsm-filter-item" data-filter="%2$s"><label for="awsm-%1$s-filter-option%5$s" class="awsm-sr-only">%3$s</label><select name="awsm_job_spec[%1$s]" class="awsm-filter-option awsm-%1$s-filter-option" id="awsm-%1$s-filter-option%5$s" aria-label="%3$s"><option value="">%3$s</option>%4$s</select></div>', esc_attr( $taxonomy ), esc_attr( $filter_key . self::$filter_suffix ), esc_html( $filter_label ), $options_content, esc_attr( $uid ) );
							/**
							 * Filter the job filter dropdown content.
							 *
							 * @since 3.3.0
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
		if ( ! empty( $search_content ) || ! empty( $specs_filter_content ) ) {
			$current_lang          = AWSM_Job_Openings::get_current_language();
			$hidden_fields_content = '';
			if ( ! empty( $current_lang ) ) {
				$hidden_fields_content .= sprintf( '<input type="hidden" name="lang" value="%s">', esc_attr( $current_lang ) );
			}
			if ( ! AWSM_Job_Openings::is_default_pagination( $shortcode_atts ) ) {
				$paged                  = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				$hidden_fields_content .= sprintf( '<input type="hidden" name="awsm_pagination_base" value="%1$s"><input type="hidden" name="paged" value="%2$s">', esc_url( get_pagenum_link() ), absint( $paged ) );
			}
			$hidden_fields_content .= '<input type="hidden" name="action" value="jobfilter">';
			if ( ! empty( $specs_filter_content ) ) {
				$toggle_icon = '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin"><path xmlns="http://www.w3.org/2000/svg" fill="rgb(9.803922%,9.803922%,9.803922%)" d="M 36.417969 19.9375 L 36.417969 17.265625 C 36.417969 16.160156 35.523438 15.265625 34.417969 15.265625 L 21.578125 15.265625 C 20.476562 15.265625 19.578125 16.160156 19.578125 17.265625 L 19.578125 19.9375 L 11 19.9375 L 11 26.9375 L 19.578125 26.9375 L 19.578125 30.105469 C 19.578125 31.210938 20.476562 32.105469 21.578125 32.105469 L 34.417969 32.105469 C 35.523438 32.105469 36.417969 31.210938 36.417969 30.105469 L 36.417969 26.9375 L 89 26.9375 L 89 19.9375 Z M 58.421875 43.578125 C 58.421875 42.476562 57.527344 41.578125 56.421875 41.578125 L 43.582031 41.578125 C 42.480469 41.578125 41.582031 42.476562 41.582031 43.578125 L 41.582031 46.5 L 11 46.5 L 11 53.5 L 41.582031 53.5 L 41.582031 56.421875 C 41.582031 57.527344 42.480469 58.421875 43.582031 58.421875 L 56.421875 58.421875 C 57.527344 58.421875 58.421875 57.527344 58.421875 56.421875 L 58.421875 53.5 L 89 53.5 L 89 46.5 L 58.421875 46.5 Z M 80.417969 70.140625 C 80.417969 69.035156 79.523438 68.140625 78.417969 68.140625 L 65.578125 68.140625 C 64.476562 68.140625 63.578125 69.035156 63.578125 70.140625 L 63.578125 73.0625 L 11 73.0625 L 11 80.0625 L 63.578125 80.0625 L 63.578125 82.984375 C 63.578125 84.085938 64.476562 84.984375 65.578125 84.984375 L 78.417969 84.984375 C 79.523438 84.984375 80.417969 84.085938 80.417969 82.984375 L 80.417969 80.0625 L 89 80.0625 L 89 73.0625 L 80.417969 73.0625 Z M 80.417969 70.140625"/></svg>';

				$toggle_text_wrapper_class = 'awsm-filter-toggle-text-wrapper';
				if ( $enable_search === 'enable' ) {
					$toggle_text_wrapper_class .= ' awsm-sr-only';
				}
				$toggle_control = sprintf( '<span class="%2$s">%1$s</span>%3$s', esc_html_x( 'Filter by', 'job filter', 'wp-job-openings' ), esc_attr( $toggle_text_wrapper_class ), $toggle_icon );
				/**
				 * Filters the HTML content for the specifications toggle button.
				 *
				 * @since 3.2.0
				 *
				 * @param string $toggle_control Toogle button HTML content.
				 */
				$toggle_control = apply_filters( 'awsm_job_filters_toggle_btn', $toggle_control );

				$specs_filter_content = sprintf( '<a href="#" class="awsm-filter-toggle" role="button" aria-pressed="false">%2$s</a><div class="awsm-filter-items">%1$s</div>', $specs_filter_content, $toggle_control );
			}

			$wrapper_class = 'awsm-filter-wrap';
			if ( $enable_search !== 'enable' ) {
				$wrapper_class .= ' awsm-no-search-filter-wrap';
			}
			$filter_content = sprintf( '<div class="%3$s"><form action="%2$s/wp-admin/admin-ajax.php" method="POST">%1$s</form></div>', $search_content . $specs_filter_content . $hidden_fields_content, esc_url( site_url() ), esc_attr( $wrapper_class ) );
		}

		echo apply_filters( 'awsm_filter_content', $filter_content, $available_filters_arr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function awsm_posts_filters() {
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		$filters = $shortcode_atts = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

		$filter_action = isset( $_POST['action'] ) ? $_POST['action'] : '';

		if ( ! empty( $_POST['awsm_job_spec'] ) ) {
			$job_specs = $_POST['awsm_job_spec'];
			foreach ( $job_specs as $taxonomy => $term_id ) {
				$taxonomy             = sanitize_text_field( $taxonomy );
				$filters[ $taxonomy ] = intval( $term_id );
			}
		}

		if ( ! empty( $_POST['shortcode_specs'] ) ) {
			$shortcode_atts['specs'] = sanitize_text_field( $_POST['shortcode_specs'] );
		}

		if ( isset( $_POST['listings_per_page'] ) ) {
			$shortcode_atts['listings'] = intval( $_POST['listings_per_page'] );
		}

		if ( isset( $_POST['lang'] ) ) {
			AWSM_Job_Openings::set_current_language( $_POST['lang'] );
		}

		if ( isset( $_POST['awsm_pagination_base'] ) ) {
			// Set as classic pagination.
			$shortcode_atts['pagination'] = 'classic';
		} else {
			$shortcode_atts['pagination'] = 'modern';
		}

		$args = AWSM_Job_Openings::awsm_job_query_args( $filters, $shortcode_atts );

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
			include AWSM_Job_Openings::get_template_path( 'main.php', 'job-openings' );
		} else {
			$no_jobs_content = '';
			if ( $filter_action !== 'loadmore' ) {
				$no_jobs_content = sprintf( '<div class="awsm-jobs-none-container"><p>%s</p></div>', esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' ) );
			} else {
				$no_jobs_content = sprintf( '<div class="awsm-jobs-pagination awsm-load-more-main awsm-no-more-jobs-container"><p>%s</p></div>', esc_html__( 'Sorry! No more jobs to show.', 'wp-job-openings' ) );
			}
			/**
			 * Filters the HTML content for no jobs when filtered.
			 *
			 * @since 2.3.0
			 *
			 * @param string $no_jobs_content The HTML content.
			 */
			echo apply_filters( 'awsm_no_filtered_jobs_content', $no_jobs_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		wp_die();
		// phpcs:enable
	}
}
