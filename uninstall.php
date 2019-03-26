<?php

// if uninstall.php is not called by WordPress, then die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

if ( get_option( 'awsm_delete_data_on_uninstall' ) !== 'delete_data' ) {
	return;
}

require_once dirname( __FILE__ ) . '/inc/class-awsm-job-openings-uninstall.php';

if ( class_exists( 'AWSM_Job_Openings_Uninstall' ) ) {
	AWSM_Job_Openings_Uninstall::uninstall();
}
