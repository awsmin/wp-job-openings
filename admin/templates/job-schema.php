<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$settings_fields = apply_filters(
		'awsm_jobs_job_schema_settings_fields',
		array(
			'default' => array(
				array(
					'name'          => 'awsm_jobs_enable_job_schema',
					'label'         => __( 'Schema', 'wp-job-openings' ),
					'type'          => 'checkbox',
					'default_value' => 'enable',
					'choices'       => array(
						array(
							'value' => 'enable',
							'text'  => __( 'Enable JobPosting schema', 'wp-job-openings' ),
						),
					),
					'description'   => __( 'Add JobPosting structured data to job pages.', 'wp-job-openings' ),
				),
			),
		)
	);
	?>

<div id="settings-awsm-settings-job-schema" class="awsm-admin-settings">
	<?php do_action( 'awsm_settings_form_elem_start', 'job-schema' ); ?>
	<form method="POST" action="options.php" id="job_schema_settings_form">
		<?php
			settings_fields( 'awsm-jobs-job-schema-settings' );

			do_action( 'before_awsm_settings_main_content', 'job-schema' );
		?>

		<div class="awsm-form-section-main awsm-sub-options-container" id="awsm-job-schema-options-container">
			<table class="form-table">
				<tbody>
					<?php
						do_action( 'before_awsm_job_schema_settings' );

						$this->display_settings_fields( $settings_fields['default'] );

						do_action( 'after_awsm_job_schema_settings' );
					?>
				</tbody>
			</table>
		</div>

		<?php do_action( 'after_awsm_settings_main_content', 'job-schema' ); ?>

		<div class="awsm-form-footer">
		<?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'job-schema' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div><!-- .awsm-form-footer -->
	</form>
	<?php do_action( 'awsm_settings_form_elem_end', 'job-schema' ); ?>
</div>
