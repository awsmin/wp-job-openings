<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Block {
	private static $instance = null;

    protected $unique_listing_id = 1;

    public function __construct() {
		add_action( 'awsm_block_filter_form', array( $this, 'display_block_filter_form' ) );
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
			'uid'        		=> $this->unique_listing_id,
			'layout'     		=> $blockatts['layout'],
			'listing_order' 	=> $blockatts['listing_order'],
			'filter_options'	=> $blockatts['filter_options'],
			'listings'   		=> get_option( 'awsm_jobs_list_per_page' ),
			'loadmore'   		=> 'yes',
			'pagination' 		=> get_option( 'awsm_jobs_pagination_type', 'modern' ),
		);

		$this->unique_listing_id++;

		ob_start();
        include get_awsm_jobs_template_path( 'block-job-openings-view', 'block-files' );
		$block_content = ob_get_clean();

		return $block_content;
	}

    public static function get_job_listing_view_class_block( $attributes = array() ) { 
		$view       = $attributes['layout']; 
		$view_class = 'awsm-lists';
		if ( $view === 'grid' ) {
			$number_columns = get_option( 'awsm_jobs_number_of_columns' );
			$view_class     = 'awsm-row';
			$column_class   = 'awsm-grid-col-' . $number_columns;
			if ( $number_columns == 1 ) {
				$column_class = 'awsm-grid-col';
			}
			$view_class .= ' ' . $column_class;
		}
		return sprintf( 'awsm-job-listings %s', $view_class );
	}
	
	public function display_block_filter_form( $shortcode_atts ) { 
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
		
		$taxonomies = get_object_taxonomies( 'awsm_job_openings', 'objects' );

		$display_filters = true;
		if ( $enable_job_filters !== 'enabled' || $filters_attr === 'no' ) {
			$display_filters = false;
		}
		// Hide filters if specs shortcode attribute is applied.
		if ( ! empty( $shortcode_atts['specs'] ) ) {
			$display_filters = false;
		}

		//$available_filters = get_option( 'awsm_jobs_listing_available_filters' );
		$available_filters = explode(',',$shortcode_atts['filter_options']); 
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
			$selected_filters = self::get_block_filters_query_args( $available_filters );
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

							$dropdown_content = sprintf( '<div class="awsm-filter-item" data-filter="%2$s"><label for="awsm-%1$s-filter-option%5$s" class="awsm-sr-only">%3$s</label><select name="awsm_job_spec[%1$s]" class="awsm-filter-option awsm-%1$s-filter-option" id="awsm-%1$s-filter-option%5$s" aria-label="%3$s"><option value="">%3$s</option>%4$s</select></div>', esc_attr( $taxonomy ), esc_attr( $filter_key . '_spec' ), esc_html( $filter_label ), $options_content, esc_attr( $uid ) );
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

	public static function get_block_filters_query_args( $filters = false ) {
		$query_args = array();
		if ( empty( $filters ) ) {
			$filters = get_option( 'awsm_jobs_listing_available_filters' );
		}
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
}

AWSM_Job_Openings_Block::init();
?>