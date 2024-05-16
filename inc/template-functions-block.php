<?php
/**
 * Template specific functions for block
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'awsm_jobs_view_class_block' ) ) {
	function awsm_jobs_view_class_block( $class = '', $attributes = array() ) {
		$view_class = AWSM_Job_Openings_Block::get_job_listing_view_class_block( $attributes );
		if ( ! empty( $class ) ) {
			$view_class = trim( $view_class . ' ' . $class );
		}
		printf( 'class="%s"', esc_attr( $view_class ) );
	}
}

if ( ! function_exists( 'awsm_block_job_filters_explode' ) ) { 
	function awsm_block_job_filters_explode( $filter_data ) {  
		$available_filters = array();
		
		if( !empty($filter_data) ){
			$available_filters = explode(',',$filter_data ); 
		}else{
			$available_filters = get_option( 'awsm_jobs_listing_specs' );
		}
		return $available_filters;
	}
}
