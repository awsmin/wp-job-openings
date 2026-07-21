<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Elementor {
	private static $instance = null;

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_widget_icon_style' ) );
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

	/**
	 * The widget icon is our own HireZoot mark (same SVG already used for the wp-admin
	 * menu icon), not an Elementor icon-font glyph, so it's applied as a CSS mask
	 * (background-color: currentColor) rather than a font ligature. This lets it inherit
	 * Elementor's own icon color in every state (panel list, hover, navigator) for free.
	 */
	public function enqueue_widget_icon_style() {
		$icon_url = esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/hirezoot-icon.svg' );

		wp_add_inline_style(
			'elementor-editor',
			"i.awsm-job-listings-widget-icon {
				display: inline-block;
				width: 1em;
				height: 1em;
				background-color: currentColor;
				-webkit-mask-image: url({$icon_url});
				mask-image: url({$icon_url});
				-webkit-mask-repeat: no-repeat;
				mask-repeat: no-repeat;
				-webkit-mask-position: center;
				mask-position: center;
				-webkit-mask-size: contain;
				mask-size: contain;
			}"
		);
	}
}

AWSM_Job_Openings_Elementor::init();
