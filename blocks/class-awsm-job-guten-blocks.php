<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Awsm_Job_Guten_Blocks {

	private static $instance = null;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_dynamic_block' ) );
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_dynamic_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$args = array(
			'render_callback' => array( $this, 'block_render_callback' ),
		);

		register_block_type( __DIR__ . '/build', $args );
	}

	public function block_render_callback( $atts, $content ) {  

		if ( empty( $atts['filter_options'] ) ) {
			$default_filters = array();
			$specs = AWSM_Job_Openings::get_filter_specifications();
			foreach($specs as $k => $v){
                $default_filters[$k]['specKey']  = $v['key'];
				$default_filters[$k]['value']    = 'dropdown';
			}
			$atts['filter_options'] = $test;
		}
		
		if ( isset( $atts['search'] ) && $atts['search'] === true ) {
			$atts['search'] = 'enable';
		}

		if ( isset( $atts['filter_options'] ) && is_array( $atts['filter_options'] ) ) {
			$atts['filter_options'] = $atts['filter_options'];
		}

		if ( isset( $atts['layout'] ) && is_array( $atts['layout'] ) ) {
			$atts['layout'] = $atts['layout'];
		}

		if ( isset( $atts['number_of_columns'] ) && is_array( $atts['number_of_columns'] ) ) {
			$atts['number_of_columns'] = $atts['number_of_columns'];
		}

		if ( isset( $atts['hide_expired_jobs'] ) && $atts['hide_expired_jobs'] === true ) {
			$atts['hide_expired_jobs'] = 'expired';
		}

		if ( isset( $atts['placement'] ) ) {
			$atts['placement'] = $atts['placement'];
		}

		if ( isset( $atts['search_placeholder'] ) && $atts['search_placeholder'] === true ) {
			$atts['search_placeholder'] = $atts['search_placeholder'];
		}

		if ( isset( $atts['listType'] ) && $atts['listType'] === true ) {
			$atts['listType'] = $atts['listType'];
		}

		if ( isset( $atts['selectedTerms'] ) && is_array( $atts['selectedTerms'] ) ) {
			$atts['selectedTerms'] = $atts['selectedTerms'];
		}

		if ( isset( $atts['orderBy'] ) && $atts['orderBy'] === true ) {
			$atts['orderBy'] = $atts['orderBy'];
		}

		if ( isset( $atts['jobsPerPage'] ) ) {
			$atts['listing_per_page'] = $atts['jobsPerPage'];
		}

		if ( isset( $atts['pagination'] ) ) {
			$atts['pagination'] = $atts['pagination'];
		}

		/** end */

		/** Style Tab */

		if ( isset( $atts['blockId'] ) ) {
			$atts['block_id'] = $atts['blockId'];
		} 

		/** End */

		 /**
		 * Filters the block attributes.
		 *
		 * Allows modification of block attributes prior to rendering.
		 *
		 * @since 3.5.0
		 *
		 * @param array $atts List of supported attributes.
		 */
		$atts = apply_filters( 'awsm_jobs_listings_block_attributes', $atts );

		$class_block_init = AWSM_Job_Openings_Block::init();
		$block_content    = $class_block_init->awsm_jobs_block_attributes( $atts );

		if ( empty( $block_content ) ) {
			return '<p>No job listings found.</p>'; // Fallback content
		}

		return $block_content;
	}

	public function block_assets() {
		wp_enqueue_script( 'awsm-job-admin' );
		if ( ! wp_style_is( 'awsm-jobs-style' ) || ! wp_script_is( 'awsm-job-scripts' ) ) {
			$awsm_job_openings = AWSM_Job_Openings::init();
			$awsm_job_openings->awsm_enqueue_scripts();
		}
	}
}
Awsm_Job_Guten_Blocks::get_instance();
