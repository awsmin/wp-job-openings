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
		
		//$block_content = sprintf( '<div %2$s>%1$s</div>', AWSM_Job_Openings::awsm_jobs_shortcode( $atts ) );
		//return $block_content;
		//print_r($atts);
		
		if ( isset( $atts['filter_options'] ) && is_array( $atts['filter_options'] ) ) {
			$atts['specs'] = implode( ',', $atts['filter_options'] );
		}
		$awsm_job_openings = AWSM_Job_Openings::init();
		return $awsm_job_openings->awsm_jobs_shortcode( $atts);
		//print_r($overview_data);
	}

	public function example_callback( $atts ) {
		if ( isset( $atts['filter_options'] ) && is_array( $atts['filter_options'] ) ) {
			$atts['specs'] = implode( ',', $atts['filter_options'] );
		}

		print_r($atts);

		return $atts;
	 }

	public function block_assets() {
		wp_enqueue_script( 'awsm-job-admin' );
	}
}

Awsm_Job_Guten_Blocks::get_instance();