<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Filters {
	private static $instance = null;

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

	public function display_filter_form( $shortcode_atts ) {
		$filter_content     = '';
		$filters_attr       = isset( $shortcode_atts['filters'] ) ? $shortcode_atts['filters'] : '';
		$enable_job_filters = get_option( 'awsm_enable_job_filter_listing' );
		$enable_search      = get_option( 'awsm_enable_job_search' );

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
			$search_content   = sprintf( '<div class="awsm-filter-item"><div class="awsm-filter-item-search"><label for="awsm-jq-%4$s" class="awsm-sr-only">%1$s</label><input type="text" id="awsm-jq-%4$s" name="jq" value="%2$s" placeholder="%1$s" class="awsm-job-search awsm-job-form-control">%3$s</div></div>', esc_attr( $placeholder_text ), esc_attr( $search_query ), $search_icon, esc_attr( $shortcode_atts['uid'] ) );
			/**
			 * Filters the search field content.
			 *
			 * @since 1.6.0
			 *
			 * @param string $search_content Search field content.
			 */
			$filter_content .= apply_filters( 'awsm_jobs_search_field_content', $search_content );
		}

		$filter_suffix = '_spec';
		$taxonomies    = get_object_taxonomies( 'awsm_job_openings', 'objects' );

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
			$selected_filters = array();
			foreach ( $available_filters as $available_filter ) {
				$current_filter_key = str_replace( '-', '__', $available_filter ) . $filter_suffix;
				if ( isset( $_GET[ $current_filter_key ] ) ) {
					$selected_filters[ $available_filter ] = sanitize_title( $_GET[ $current_filter_key ] );
				}
			}
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
							$options_content                    = '';
						foreach ( $terms as $term ) {
							$selected = '';
							if ( in_array( $taxonomy, array_keys( $selected_filters ) ) && $selected_filters[ $taxonomy ] === $term->slug ) {
								$selected = ' selected';
							}
							$options_content .= sprintf( '<option value="%1$s" data-slug="%3$s"%4$s>%2$s</option>', esc_attr( $term->term_id ), esc_html( $term->name ), esc_attr( $term->slug ), esc_attr( $selected ) );
						}
							$filter_key = str_replace( '-', '__', $taxonomy );
							$spec_name  = apply_filters( 'wpml_translate_single_string', $tax_details->label, 'WordPress', sprintf( 'taxonomy general name: %s', $tax_details->label ) );
							/**
							 * Filters the default label for the job filter.
							 *
							 * @since 1.6.0
							 *
							 * @param string $taxonomy Taxonomy key.
							 * @param array  $tax_details Taxonomy details.
							 */
							$filter_label    = apply_filters( 'awsm_filter_label', esc_html_x( 'All', 'job filter', 'wp-job-openings' ) . ' ' . $spec_name, $taxonomy, $tax_details );
							$filter_content .= sprintf( '<div class="awsm-filter-item" data-filter="%2$s"><label for="awsm-%1$s-filter-option-%5$s" class="awsm-sr-only">%3$s</label><select name="awsm_job_spec[%1$s]" class="awsm-filter-option awsm-%1$s-filter-option" id="awsm-%1$s-filter-option-%5$s"><option value="">%3$s</option>%4$s</select></div>', esc_attr( $taxonomy ), esc_attr( $filter_key . $filter_suffix ), $filter_label, $options_content, esc_attr( $shortcode_atts['uid'] ) );
					}
				}
			}
		}

		if ( ! empty( $filter_content ) ) {
			$current_lang = AWSM_Job_Openings::get_current_language();
			if ( ! empty( $current_lang ) ) {
				$filter_content .= sprintf( '<input type="hidden" name="language" value="%s">', esc_attr( $current_lang ) );
			}
			$filter_content = sprintf( '<div class="awsm-filter-wrap"><form action="%2$s/wp-admin/admin-ajax.php" method="POST">%1$s<input type="hidden" name="action" value="jobfilter"></form></div>', $filter_content, esc_url( site_url() ) );
		}

		echo apply_filters( 'awsm_filter_content', $filter_content, $available_filters_arr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function awsm_posts_filters() {
        // phpcs:disable WordPress.Security.NonceVerification.Missing
		$filters = $shortcode_atts = array(); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found

		$filter_action = isset( $_POST['action'] ) ? $_POST['action'] : '';

		if ( isset( $_POST['awsm_job_spec'] ) && ! empty( $_POST['awsm_job_spec'] ) ) {
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

		if ( isset( $_POST['language'] ) ) {
			AWSM_Job_Openings::set_current_language( $_POST['language'] );
		}

		$args = AWSM_Job_Openings::awsm_job_query_args( $filters, $shortcode_atts );

		if ( isset( $_POST['jq'] ) && ! empty( $_POST['jq'] ) ) {
			$args['s'] = sanitize_text_field( $_POST['jq'] );
		}

		if ( isset( $_POST['paged'] ) ) {
			$args['paged'] = intval( $_POST['paged'] ) + 1;
		}

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			include AWSM_Job_Openings::get_template_path( 'main.php', 'job-openings' );
		} else {
			$no_jobs_content = '';
			if ( $filter_action !== 'loadmore' ) {
				$no_jobs_content = sprintf( '<div class="awsm-jobs-none-container"><p>%s</p></div>', esc_html__( 'Sorry! No jobs to show.', 'wp-job-openings' ) );
			} else {
				$no_jobs_content = sprintf( '<div class="awsm-load-more-main awsm-no-more-jobs-container"><p>%s</p></div>', esc_html__( 'Sorry! No more jobs to show.', 'wp-job-openings' ) );
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
