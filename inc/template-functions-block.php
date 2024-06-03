<?php
/**
 * Template specific functions for block
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'awsm_block_jobs_view_class' ) ) {
	function awsm_block_jobs_view_class( $class = '', $attributes = array() ) {
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

		if ( ! empty( $filter_data ) ) {
			$available_filters = explode( ',', $filter_data );
		} else {
			$available_filters = get_option( 'awsm_jobs_listing_specs' );
		}
		return $available_filters;
	}
}

if ( ! function_exists( 'awsm_block_jobs_query' ) ) {
	function awsm_block_jobs_query( $attributes = array() ) { 
		$args  = AWSM_Job_Openings_Block::awsm_block_job_query_args( array(), $attributes );
		$query = new WP_Query( $args );
		return $query;
	}
}

if ( ! function_exists( 'awsm_block_jobs_data_attrs' ) ) {
	function awsm_block_jobs_data_attrs( $attrs = array(), $attributes = array() ) {
		$content = '';
		$attrs   = array_merge( AWSM_Job_Openings_Block::get_block_job_listing_data_attrs( $attributes ), $attrs );
		if ( ! empty( $attrs ) ) {
			foreach ( $attrs as $name => $value ) {
				if ( ! empty( $value ) ) {
					$content .= sprintf( ' data-%s="%s"', esc_attr( $name ), esc_attr( $value ) );
				}
			}
		}
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

