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
