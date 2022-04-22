<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_WPML {
	private static $instance = null;

	protected $cpath = null;

	public function __construct() {
		$this->cpath = untrailingslashit( plugin_dir_path( __FILE__ ) );

		add_action( 'update_option_awsm_jobs_filter', array( $this, 'jobs_filter_update_handler' ), 10, 2 );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function jobs_filter_update_handler( $old_value, $specs ) {
		if ( ! empty( $specs ) ) {
			foreach ( $specs as $spec ) {
				if ( isset( $spec['taxonomy'], $spec['filter'] ) ) {
					$taxonomy   = $spec['taxonomy'];
					$tax_length = strlen( $taxonomy );
					if ( $tax_length > 0 && $tax_length <= 32 ) {
						do_action( 'wpml_register_single_string', 'WordPress', sprintf( 'taxonomy general name: %s', $spec['filter'] ), $spec['filter'] );
						do_action( 'wpml_register_single_string', 'WordPress', sprintf( 'taxonomy singular name: %s', $spec['filter'] ), $spec['filter'] );
					}
				}
			}
		}
	}
}

AWSM_Job_Openings_WPML::init();
