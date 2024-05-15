<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Block {
	private static $instance = null;

    protected $unique_listing_id = 1;

    public function __construct() {
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
}

AWSM_Job_Openings_Block::init();
?>