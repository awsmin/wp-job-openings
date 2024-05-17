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
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
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
		if ( isset( $atts['filter_options'] ) && is_array( $atts['filter_options'] ) ) {
			$atts['filter_options'] = implode( ',', $atts['filter_options'] );
		}

		if ( isset( $atts['search'] ) && $atts['search'] === true ) {
			$atts['search'] = 'enable';
		}

		if ( isset( $atts['enable_job_filter'] ) && $atts['enable_job_filter'] === true ) {
			$atts['enable_job_filter'] = 'enable';
		}


		/* 
		if ( isset( $atts['layout'] ) && is_array( $atts['layout'] ) ) { 
			$atts['layout'] = implode( ',', $atts['layout'] );
		} */

		$class_block_init = AWSM_Job_Openings_Block::init();
		$block_content =  $class_block_init->awsm_jobs_block_attributes($atts);
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