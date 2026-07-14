<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Elementor {
	private static $instance = null;

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'awsm-job-openings',
			array(
				'title' => esc_html__( 'HireZoot', 'wp-job-openings' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	public function register_widget( $widgets_manager ) {
		if ( ! class_exists( 'Awsm_Job_Guten_Blocks' ) ) {
			return;
		}

		require_once __DIR__ . '/class-awsm-job-openings-elementor-widget.php';
		$widgets_manager->register( new AWSM_Job_Openings_Elementor_Widget() );
	}
}

AWSM_Job_Openings_Elementor::init();
