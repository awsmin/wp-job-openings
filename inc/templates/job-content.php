<?php
/**
 * Template for displaying single job listing content
 *
 * Override this by copying it to currenttheme/wp-job-openings/job-content.php
 *
 * @package wp-job-openings
 * @version 1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div <?php awsm_job_content_class(); ?>>

	<?php
		/**
		 * before_awsm_job_details hook
		 *
		 * @hooked AWSM_Job_Openings_Form::insert_application()
		 *
		 * @since 1.0
		 */
		do_action( 'before_awsm_job_details' );
	?>

	<?php if ( ! is_awsm_job_expired( false ) ) : ?>

		<div class="awsm-job-content">
			<?php include get_awsm_jobs_template_path( 'main', 'single-job' ); ?>
		</div><!-- .awsm-job-content -->

		<div class="awsm-job-form">
			<?php
			if ( ! is_awsm_job_expired() ) {
				/**
				 * awsm_application_form_init hook
				 *
				 * Initialize job application form
				 *
				 * @hooked AWSM_Job_Openings_Form::application_form()
				 *
				 * @since 1.0
				 */
				do_action( 'awsm_application_form_init' );
			} else {
				awsm_jobs_expired_msg( '<div class="awsm-job-form-inner">', '</div>' );
			}
			?>
		</div><!-- .awsm-job-form -->

	<?php else : ?>
		<div class="awsm-expired-message">
			<?php awsm_jobs_expired_msg( '<p>', '</p>' ); ?>
		</div><!-- .awsm-expired-message -->
	<?php endif; ?>

	<?php
		/**
		 * after_awsm_job_details hook
		 *
		 * @since 1.0
		 */
		do_action( 'after_awsm_job_details' );
	?>

</div>
