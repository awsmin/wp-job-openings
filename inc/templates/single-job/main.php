<?php
/**
 * For displaying job details
 *
 * Override this by copying it to currenttheme/wp-job-openings/single-job/main.php
 *
 * @package wp-job-openings
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * awsm_application_form_notices hook
 *
 * Display messages when job application is submitted.
 *
 * @hooked AWSM_Job_Openings_Form::awsm_form_submit_notices()
 *
 * @since 1.0
 */
do_action( 'awsm_application_form_notices' );

awsm_job_expiry_details( '<div class="awsm-job-head">', '</div>' );

awsm_job_spec_content( 'above_content' );

?>

<div class="awsm-job-entry-content entry-content">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div><!-- .awsm-job-entry-content -->

<?php

awsm_job_spec_content( 'below_content' );
