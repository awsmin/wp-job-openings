<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'awsm_jobs_sanitize_textarea' ) ) {
	function awsm_jobs_sanitize_textarea( $input ) {
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			$input = sanitize_textarea_field( $input );
		} else {
			$input = esc_textarea( $input );
		}
		return $input;
	}
}

if ( ! function_exists( 'get_awsm_jobs_date_format' ) ) {
	function get_awsm_jobs_date_format( $id = '', $format = '' ) {
		/**
		 * Filters the date format used in WP Job Openings.
		 *
		 * @since 2.1.0
		 *
		 * @param string $format The date format.
		 * @param string $id Unique ID to filter the date format.
		 */
		$format = apply_filters( 'awsm_jobs_date_format', $format, $id );
		if ( empty( $format ) ) {
			$format = get_option( 'date_format' );
		}
		return $format;
	}
}

if ( ! function_exists( 'get_awsm_jobs_time_format' ) ) {
	function get_awsm_jobs_time_format( $id = '', $format = '' ) {
		/**
		 * Filters the time format used in WP Job Openings.
		 *
		 * @since 2.1.0
		 *
		 * @param string $format The time format.
		 * @param string $id Unique ID to filter the time format.
		 */
		$format = apply_filters( 'awsm_jobs_time_format', $format, $id );
		if ( empty( $format ) ) {
			$format = get_option( 'time_format' );
		}
		return $format;
	}
}
