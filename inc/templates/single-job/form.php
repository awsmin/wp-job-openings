<?php
/**
 * For displaying job application form
 *
 * Override this by copying it to currenttheme/wp-job-openings/single-job/form.php
 *
 * @package wp-job-openings
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="awsm-job-form-inner">

	<?php
		/**
		 * before_awsm_application_form hook
		 *
		 * @since 1.0.0
		 * @since 2.2.0 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_attrs Attributes array for the form.
		 */
		do_action( 'before_awsm_application_form', $form_attrs );
	?>

	<h2>
		<?php
			/**
			 * Filters the application form title.
			 *
			 * @since 1.0.0
			 * @since 2.2.0 The `$form_attrs` parameter was added.
			 *
			 * @param array $form_attrs Attributes array for the form.
			 */
			$form_title = apply_filters( 'awsm_application_form_title', __( 'Apply for this position', 'wp-job-openings' ), $form_attrs );
			echo esc_html( $form_title );
		?>
	</h2>

	<?php
		/**
		 * awsm_application_form_description hook
		 *
		 * @since 1.3.0
		 * @since 2.2.0 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_attrs Attributes array for the form.
		 */
		do_action( 'awsm_application_form_description', $form_attrs );
	?>

	<form id="<?php echo $form_attrs['single_form'] ? 'awsm-application-form' : esc_attr( 'awsm-application-form-' . $form_attrs['job_id'] ); ?>" class="awsm-application-form" name="applicationform" method="post" enctype="multipart/form-data">

		<?php
			/**
			 * awsm_application_form_field_init hook
			 *
			 * Initialize job application form fields
			 *
			 * @hooked AWSM_Job_Openings_Form::form_field_init()
			 *
			 * @since 1.0.0
			 * @since 2.2.0 The `$form_attrs` parameter was added.
			 *
			 * @param array $form_attrs Attributes array for the form.
			 */
			do_action( 'awsm_application_form_field_init', $form_attrs );
		?>

		<input type="hidden" name="awsm_job_id" value="<?php echo esc_attr( $form_attrs['job_id'] ); ?>">
		<input type="hidden" name="action" value="awsm_applicant_form_submission">
		<div class="awsm-job-form-group">
			<?php awsm_job_form_submit_btn( $form_attrs ); ?>
		</div>

	</form>

	<div class="awsm-application-message" style="display: none;"></div>

	<?php
		/**
		 * after_awsm_application_form hook
		 *
		 * @since 1.0.0
		 * @since 2.2.0 The `$form_attrs` parameter was added.
		 *
		 * @param array $form_attrs Attributes array for the form.
		 */
		do_action( 'after_awsm_application_form', $form_attrs );
	?>

</div><!-- .awsm-job-form-inner -->
