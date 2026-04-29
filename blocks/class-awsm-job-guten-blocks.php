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
			'supports'        => array( 'anchor' => true ),
		);

		register_block_type( __DIR__ . '/build', $args );
	}

	public function block_render_callback( $atts, $content ) {

		// Ensure filter_options exists and is valid
		if ( ! isset( $atts['filter_options'] ) || ! is_array( $atts['filter_options'] ) ) {
			$default_filters = array();
			$specs           = AWSM_Job_Openings::get_filter_specifications();

			foreach ( $specs as $spec ) {
				$default_filters[] = array(
					'specKey' => $spec['key'],
					'value'   => 'dropdown',
				);
			}

			$atts['filter_options'] = $default_filters;
		}

		$search_enabled  = ! empty( $atts['search'] );
		$filters_enabled = ! empty( $atts['enable_job_filter'] );
		$hide_expired    = ! empty( $atts['hide_expired_jobs'] );
		$show_spec_icon  = ! empty( $atts['show_spec_icon'] );

		$processed_atts = $atts;

		if ( $search_enabled ) {
			$processed_atts['search'] = 'enable';
		}

		if ( $filters_enabled ) {
			$processed_atts['enable_job_filter'] = 'enable';
		}

		if ( $hide_expired ) {
			$processed_atts['hide_expired_jobs'] = 'expired';
		}

		if ( $show_spec_icon ) {
			$processed_atts['show_spec_icon'] = 'show_icon';
		}

		$processed_atts = apply_filters(
			'awsm_jobs_listings_block_attributes',
			$processed_atts
		);

		$class_block_init = AWSM_Job_Openings_Block::init();
		$block_content    = $class_block_init->awsm_jobs_block_attributes( $processed_atts );

		if ( empty( $block_content ) ) {
			return '<p>' . __( 'No job listings found.', 'wp-job-openings' ) . '</p>';
		}

		return $block_content;
	}

	public function block_assets() {
		if ( is_admin() ) {
			wp_enqueue_script( 'awsm-job-admin' );
			wp_enqueue_style( 'awsm-jobs-general' );
		}

		if ( ! wp_script_is( 'awsm-job-scripts' ) ) {
			wp_enqueue_script( 'awsm-job-scripts' );
		}

		if ( ! wp_style_is( 'awsm-jobs-style' ) ) {
			wp_enqueue_style( 'awsm-jobs-style' );
		}
	}
}
Awsm_Job_Guten_Blocks::get_instance();
